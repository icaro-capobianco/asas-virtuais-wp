<?php
namespace AsasVirtuaisWP\ACF;

class ACFManager {

	public function __construct() {
		if ( ! did_action( 'acf/init' ) ) {
			add_action( 'acf/init', [ $this, 'acf_initialized' ], 30, 1 );
		} else {
			add_action( 'init', [ $this, 'acf_initialized' ], 30, 1 );
		}
	}

	public function add_meta_box( $title, $page_slug, $callback, $context = 'normal', $priority = 'default' ) {
		$metabox_slug = sanitize_title( $title );

		add_action( 'acf/input/admin_head', function() use ( $page_slug, $metabox_slug, $title, $callback, $context, $priority ) {
			$screen = get_current_screen();
			if ( $screen->id === $page_slug ) {
				add_meta_box( $metabox_slug, $title, $callback, 'acf_options_page', $context, $priority );
			}
		}, 20 );
  	}

	public function acf_initialized() {
		foreach( $this->pages as $page_options ) {
			acf_add_options_page( $page_options );
		}
		foreach ( $this->field_groups as $group_args ) {
			acf_add_local_field_group( $group_args );
		}
	}

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

	private $pages = [];
	public function settings_page( $label, $args = [] ) {
		$args['parent_slug'] = 'options-general.php';
		$this->options_page( $label, $args );
	}

	public function options_page( $label, $args = [] ) {
		$defaults = [
            'page_title'  => $label,
            'menu_title'  => $label,
		];

		$options = array_replace( $defaults, $args );
		$this->pages[] = $options;
	}

	private $field_groups = [];
	/** Use helper functions in ACF (av_acf_field_group) to build the group args */
	public function add_field_group( array $group_args ) {
		$this->field_groups[] = $group_args;
	}

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
			$value = get_field( $old_name, $id, false );
			if ( $value ) {
				$new_field_value = get_field( $new_name, $id, false );
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

}
