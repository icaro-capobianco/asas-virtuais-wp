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

}
