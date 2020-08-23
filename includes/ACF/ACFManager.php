<?php
namespace AsasVirtuaisWP\ACF;

class ACFManager {

	public $acf_loaded = false;
	public $framework = false;

	public function __construct( $framework = false ) {

		$this->framework = $framework;
		$this->acf_loaded = class_exists( 'ACF' );

		add_action( 'acf/init', function() {
			$this->acf_loaded = true;
		} );

		add_action( 'init', function() use( $framework ) {
			if ( $this->acf_loaded ) {
				$this->acf_initialized();
			} elseif ( $framework ) {
				$framework->admin_manager()->admin_error( 'ACF is required for the plugin ' . $framework->plugin_name . ' to work' );
			}
		}, 99 );

	}

	/** Actions to do once ACF has been initialized */
	public function acf_initialized() {
		foreach( $this->pages as $page_options ) {
			acf_add_options_page( $page_options );
		}
		foreach ( $this->field_groups as $group_args ) {
			acf_add_local_field_group( $group_args );
		}
	}

	// Meta boxes
		public function add_meta_box( $title, $page_slug, $callback, $context = 'normal', $priority = 'default' ) {
			$metabox_slug = sanitize_title( $title );

			add_action( 'acf/input/admin_head', function() use ( $page_slug, $metabox_slug, $title, $callback, $context, $priority ) {
				$screen = get_current_screen();
				if ( $screen->id === $page_slug ) {
					add_meta_box( $metabox_slug, $title, $callback, 'acf_options_page', $context, $priority );
				}
			}, 20 );
		}

	// Options and settings pages
		private $pages = [];
		/** Creates a settings page under settings menu */
		public function settings_page( $label, $args = [] ) {
			$args['parent_slug'] = 'options-general.php';
			$this->options_page( $label, $args );
		}
		/** Creates an admin page and menu */
		public function options_page( $label, $args = [] ) {
			$defaults = [
				'page_title'  => $label,
				'menu_title'  => $label,
			];

			$options = array_replace( $defaults, $args );
			$this->pages[] = $options;
		}

	// Field Groups Registering
		private $field_groups = [];
		public function add_field_group( array $group_args ) {
			$this->field_groups[] = $group_args;
		}

	// Methods to handle changes in field name
		public function update_field_name_via_databe( $old_name, $new_name, $table ) {
			global $wpdb;
			$valid_tables = [ 'postmeta', 'commentmeta', 'termmeta', 'usermeta', 'options' ];
			if ( isset( $wpdb->$table ) || ! in_array( $table, $valid_tables ) ) {
				$table_name = $wpdb->$table;
				$key = strpos( $table_name, 'meta' ) !== false ? 'meta_key' : 'option_name';
				return $wpdb->update( $table_name, [ $key => $new_name ], [ $key => $old_name ] );
			} else {
				throw new \Exception("Invalid table $table");
			}
		}
		public function update_field_name_via_duplicate( $old_name, $new_name, $acf_ids ) {
			$r = [];
			foreach( $acf_ids as $id ) {
				$value = av_acf_get_field( $old_name, $id, false );
				if ( $value ) {
					$new_field_value = av_acf_get_field( $new_name, $id, false );
					if ( ! $new_field_value ) {
						$result = update_field( $new_name, $value, $id );
						$r[ $id ] = compact( 'value', 'old_name', 'new_name' );
					}
				}
			}
			return $r;
		}
		public function update_field_names_dictionary_duplicate( $name_dictionary, $acf_ids ) {
			$r = [];
			foreach( $name_dictionary as $old_name => $new_name ) {
				$r[] = $this->update_field_name_via_duplicate( $old_name, $new_name, $acf_ids );
			}
			return $r;
		}

	// Deprecated - Soon to be removed
	public function require_fields_file( $dirpath, $filename ) {

		if ( ! $dirpath ) {
			$dirpath = $this->custom_fields_dir;
		}

		$filepath = $dirpath . $filename . '.php';

		if ( file_exists( $filepath ) ) {
			require_once $filepath;
		} else {
			asas_virtuais()->admin_manager()->admin_error( "Could not load custom fields from file: $filepath" );
		}

	}

	public function get_field( $name, $id, $formatted = true, $fallback = null ) {
		$value = null;

		if ( $this->acf_loaded ) {

			$value = get_field( $name, $id, $formatted );

		} else {

			if ( is_numeric( $id ) ) {
				$value = get_post_meta( $id, $name, true );
			} elseif( in_array( $id, [ 'option', 'options' ] ) ) {
				$value = get_option( $name );
			} else {
				$explode = explode( '_', $id );
				$identifier = $explode[0] ?? false;
				$true_id = $explode[1] ?? false;
				if ( $identifier && $true_id ) {
					switch ( $identifier ) {
						case 'user':
							$value = get_user_meta( $true_id, $name, true );
							break;
						case 'term':
							$value = get_term_meta( $true_id, $name, true );
							break;
					}
				}
			}

		}
		if ( $value === null && $fallback !== null ) {
			return $fallback;
		} else {
			return $value;
		}
	}

}
