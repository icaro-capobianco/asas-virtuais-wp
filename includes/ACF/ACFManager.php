<?php
namespace AsasVirtuaisWP\V2_0_5\ACF;

class ACFManager {

	private $custom_fields_dir;

	public function __construct( $custom_fields_dir ) {
		$this->custom_fields_dir = $custom_fields_dir;
	}

	public function require_fields_file( $filename, $dirpath = false ) {

		if ( ! $dirpath ) {
			$dirpath = $this->custom_fields_dir;
		}

		$filepath = $dirpath . $filename . '.php';

		if ( file_exists( $filepath ) ) {
			require_once $filepath;
		} else {
			asas_virtuais()->admin->admin_error( "Could not load custom fields from file: $filepath" );
		}

	}

}
