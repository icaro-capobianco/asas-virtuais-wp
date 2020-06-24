<?php
namespace AsasVirtuaisWP\ACF;

class ACFManager {

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'acf_initialized' ], 30, 1 );
	}

	public function acf_initialized() {
		foreach( $this->pages as $page_options ) {
			acf_add_options_page( $page_options );
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

		$defaults = [
            'page_title'  => $label,
            'menu_title'  => $label,
            'parent_slug' => 'options-general.php',
		];
		$options = array_replace( $defaults, $args );
		$this->pages[] = $options;
	}

}
