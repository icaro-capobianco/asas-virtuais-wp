<?php
namespace AsasVirtuaisWP\Taxonomy\Models;

abstract class AbstractTerm {
	protected $wp_term;

	public function __construct( \WP_Term $wp_term ) {
		$this->wp_term = $wp_term;
	}

	// Getters
		public function get_id() {
			return $this->wp_term->term_id;
		}
		public function get_acf_id() {
			return static::get_taxonomy() . '_' . $this->get_id();
		}
		public function get_name() {
			return $this->wp_term->name;
		}
		public function get_slug() {
			return $this->wp_term->slug;
		}
		public function get_parent() {
			return $this->wp_term->parent;
		}
		public function get_description() {
			return $this->wp_term->description;
		}
		public function get_parent_slug() {
			$parent = $this->get_parent();
			if ( ! $parent ) {
				return 0;
			}
			$term = get_term( $parent, static::get_taxonomy() );
			return $term->slug;
		}


	// Instance
		/** @return self|false */
		public static function instance_from_id( $id ) {
			return static::instance_by( 'term_id', $id );
		}
		/** @return self|false */
		public static function instance_from_slug( string $slug ) {
			return static::instance_by( 'slug', $slug );
		}
		/** @return self|false */
		public static function instance_by( string $field, $value ) {
			$taxonomy = static::get_taxonomy();
			$result = get_term_by( $field, $value, $taxonomy );
			if ( is_array( $result ) ) {
				$result = $result[0];
			}
			if ( ! $result ) {
				return false;
			}
			return new static ( $result );
		}
		/** @return array */
		public static function query( $args ) {
			return array_map( function( \WP_Term $term ) {
				return new static( $term );
			}, get_terms( wp_parse_args( $args, [
				'taxonomy' => static::get_taxonomy()
			] ) ) );
		}
		// Deprecated
		protected static function validate_get_term_result( $result ) {
			if ( is_array( $result ) ) {
				$result = $result[0];
			}
			if ( ! $result ) {
				return false;
			}
			return $result;
		}

	// Import
		protected static $essential_import_args = [ 'name' ];
		public static function import( array $data ) {
			// Validate existance of necessary data
			static::validate_import_data( $data );

			$slug = sanitize_title( $data['name'] );

			// Check for existing index
			$existing_index = get_term_by( 'slug', $slug, static::get_taxonomy() );

			if ( $existing_index ) {
				av_import_admin_notice( "Existing index found for Term $slug, you may delete it and try again." );
				$static = new static( $existing_index );
			} else {
				$static = static::insert_from_import_data( $data, false );
			}

			if ( isset( $data['children'] ) && is_array( $data['children'] ) ) {
				$static->import_children( $data['children'] );
			}

			if ( isset( $data['parent'] ) ) {
				$static->import_parent( $data['parent'] );
			}

			if ( isset( $data['metadata'] ) ) {
				$static->import_metadata( $data['metadata'] );
			}

			if ( isset( $data['acf'] ) ) {
				$static->import_metadata( $data['acf'], true );
			}

			return $static;
		}
		protected static function validate_import_data( array $data ) {
			foreach ( static::$essential_import_args as $arg ) {
				if ( ! isset( $data[ $arg ] ) ) {
					throw new \Exception( "Empty $arg in Term import" );
				}
				if ( empty( $data[ $arg ] ) ) {
					throw new \Exception( "Empty $arg in Term import" );
				}
			}
		}
		protected static function insert_from_import_data( array $data, bool $validate = true ) {

			if ( $validate ) {
				static::validate_import_data( $data );
			}

			$name = $data['name'];
			$slug = sanitize_title( $name );
			$taxonomy = static::get_taxonomy();

			// Insert object
			$term_insert = wp_insert_term(
				$name,
				$taxonomy,
				[
					// 'alias_of'
					'slug'        => $slug,
					'description' => $data['description'] ?? '',
				]
			);

			// Validate inserted object
			if ( is_wp_error( $term_insert ) ) {
				throw new \Exception( "Failed to insert term $slug. For taxonomy $taxonomy.\n" . av_wp_error_message( $term_insert ) );
			}

			// Get the ID
			$term_id = $term_insert['term_id'];

			av_import_admin_notice( "Term $slug added with ID: $term_id" );

			return new static( get_term( $term_id, static::get_taxonomy() ) );
		}
		protected function import_children( array $children_slugs ) {
			foreach ( $children_slugs as $slug ) {
				$term = get_term_by( 'slug', $slug, static::get_taxonomy() );
				if ( $term && is_object( $term ) && isset( $term->term_id ) ) {
					wp_update_term( $term->term_id, static::get_taxonomy(), [ 'parent' => $this->get_id() ] );
				}
			}
		}
		protected function import_parent( string $parent_slug ) {
			$parent = get_term_by( 'slug', $parent_slug, static::get_taxonomy() );
			if ( $parent && is_object( $parent ) && isset( $parent->term_id ) ) {
				wp_update_term( $this->get_id(), static::get_taxonomy(), [ 'parent' => $parent->term_id ] );
			} else {
				av_import_admin_error( "Failed to set the term $parent_slug as the parent of the term " . $this->get_slug() . ".\n Details: " . var_export( $parent, true ) );
			}
		}
		public function import_metadata( $data, $acf = false ) {
			foreach ( $data as $key => $value ) {
				try {
					if ( $acf ) {
						$result = av_acf_import_field_data( $key, $value, $this->get_acf_id() );
						$identifier = $this->get_acf_id();
					} else {
						$result = update_term_meta( $this->get_id(), $key, $value );
						$identifier = $this->get_id();
						if ( $result ) {
							av_import_admin_notice( "Successfully imported meta $key to: $identifier with:\n" . var_export( $value, true ) );
						} else {
							throw new \Exception( "Failed to import meta $key to: $identifier with \n" . var_export( $value, true ) );
						}
					}
				} catch (\Throwable $th) {
					// TODO handle exception
					av_import_admin_exception( $th );
				}
			}
		}

	// Export
		public function export_json( $pretty = true ) {
			if ( $pretty ) {
				return json_encode( $this->export_array(), JSON_PRETTY_PRINT );
			} else {
				return json_encode( $this->export_array() );
			}
		}
		public function export_array() {
			return [
				'name'        => $this->get_name(),
				'slug'        => $this->get_slug(),
				'parent'      => $this->get_parent_slug(),
				'description' => $this->get_description(),
			];
		}


	// Abstract
	abstract public static function get_taxonomy();

}
