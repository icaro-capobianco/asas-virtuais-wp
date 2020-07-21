<?php

namespace AsasVirtuaisWP\Templates;

class TemplateManager {

	private $templating_dir = '';

	public function __construct( $args = [] ) {
		if ( isset( $args['templating_dir'] ) ) {
			$this->templating_dir = $args['templating_dir'];
		}
	}

	public function theme_has_templates( array $names, string $type = '' ) {
		return locate_template( $this->get_template_files( $names, $type ), false );
	}

	/**
	 * Receives an array of template names and a optional $type string
	 * Returns an array of possible file names to look for in the theme
	 * Prepends the $type variable to the file names, followed by a -
	 * @param array $names
	 * @param string $type 
	 * @return bool
	 */
	public function get_template_files( array $names, string $type = '' ) {

		// Map names to possible files
		$files = [];
		foreach ( $names as $name ) {

			// Prepend template type
			if ( $type ) {
				$name = "$type-$name";
			}
			// Under the theme root directory
			$under_theme_root = "$name.php";
			$files[] = $under_theme_root;

			// Under the theme subdirectory named after the templating_dir set for the manager.
			$under_plugin_subdir = $this->templating_dir ? "$this->templating_dir/$name.php" : false;
			if ( $under_plugin_subdir ) {
				$files[] = $under_plugin_subdir;
			}
		}
		return $files;
	}

}
