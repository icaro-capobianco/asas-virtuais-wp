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
	public function get_name() {
		return $this->wp_term->name;
	}
	public function get_slug() {
		return $this->wp_term->slug;
	}
	public function get_acf_id() {
		return static::get_taxonomy() . '_' . $this->get_id();
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
	public static function instance_from_id( $id ) {
		$taxonomy = static::get_taxonomy();
		$wp_term  = get_term( $id, $taxonomy );
		$wp_term  = static::validate_get_term_result( $wp_term, $id );
		return new static( $wp_term );
	}
	public static function instance_from_slug( string $slug ) {
		$taxonomy = static::get_taxonomy();
		$wp_term  = get_term_by( 'slug', $slug, $taxonomy );
		$wp_term  = static::validate_get_term_result( $wp_term, $slug );
		return new static( $wp_term );
	}
	protected static function validate_get_term_result( $result, $identifier ) {
		if ( is_array( $result ) ) {
			$result = $result[0];
		}
		if ( ! $result ) {
			throw new \Exception( "Term $identifier of taxonomy $taxonomy not found" );
		}
		return $result;
	}

	// Import
	protected static $essential_import_args = [ 'name', 'slug' ];
	public static function import( array $data ) {
		// Validate existance of necessary data
		static::validate_import_data( $data );

		$slug = $data['slug'];

		// Check for existing index
		$existing_index = get_term_by( 'slug', $slug, static::get_taxonomy() );

		if ( $existing_index ) {
			av_import_admin_notice( "Existing index found for Term $slug, you may delete it and try again." );
			$term = $existing_index;
		} else {
			$term = static::insert_from_import_data( $data, false );
		}

		if ( isset( $data['children'] ) && is_array( $data['children'] ) ) {
			static::import_children( $term->term_id, $data['children'] );
		}

		if ( isset( $data['parent'] ) ) {
			static::import_parent( $term->term_id, $data['parent'] );
		}

		return new static( $term );
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
		$slug = $data['slug'];

		// Insert object
		$term_insert = wp_insert_term(
			$name,
			static::get_taxonomy(),
			[
				// 'alias_of'
				'slug'        => $slug,
				'description' => $data['description'] ?? '',
			]
		);

		// Validate inserted object
		if ( is_wp_error( $term_insert ) ) {
			throw new \Exception( "Failed to insert term $slug.\n" . av_wp_error_message( $term_insert ) );
		}

		// Get the ID
		$term_id = $term_insert['term_id'];

		av_import_admin_notice( "Term $slug added with ID: $term_id" );

		return get_term( $term_id, static::get_taxonomy() );
	}
	protected static function import_children( $term_id, array $children_slugs ) {
		foreach ( $children_slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, static::get_taxonomy() );
			if( $term ) {
				wp_update_term( $term->term_id, static::get_taxonomy(), [ 'parent' => $term_id ] );
			}
		}
	}
	protected static function import_parent( $term_id, string $parent_slug ) {
		$term = get_term_by( 'slug', $parent_slug, static::get_taxonomy() );
		if ( $term ) {
			wp_update_term( $term_id, static::get_taxonomy(), [ 'parent' => $term->term_id ] );
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
