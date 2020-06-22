<?php
namespace AsasVirtuaisWP\PuC;

class UpdateManager {

	private function __construct() {

		add_action( 'acf/init', [ $this, 'maybe_add_pre_release_settings_page' ], 1, 1 );

	}

	private $pre_release_plugins = [];
	public function maybe_add_pre_release_settings_page() {

		if ( ! empty( $pre_release_plugins ) ) {

			// Create the Release Settings page
			asas_virtuais()->acf_manager()->settings_page( 'Release Settings' );

			// Generate the switch field
			$fields = array_map( function( $plugin_name ) {
				return \av_acf_boolean_field( $plugin_name . ' Pre-Release' );
			}, $pre_release_plugins );

			// Add the page fields
			acf_add_local_field_group( \av_acf_field_group( 
				'Plugin Release Options',
				\av_acf_location( 'options_page', 'release-settings' ),
				$fields
			) );
		}
	}

	private static $instance;
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function register_plugin( $plugin_name, $args ) {

		if ( $this->valid_args( $args ) ) {

			if ( isset( $args['pre_release_options'] ) ) {

				$this->pre_release_plugins[] = $plugin_name;

				if ( get_field( $plugin_name."_pre_release", 'option' ) ) {
					return $this->set_with_pre_releases( $args['pre_release_options'] );
				}
			}

			return $this->build_update_checker( $args );

		} else {
			asas_virtuais()->admin_manager()->admin_warning( 'Plugin updater initialized incorrectly for the plugin: ' . $plugin_name );
		}
	}

	private function set_with_pre_releases( $args ) {
		if ( $this->valid_args( $args ) ) {
			return $this->build_update_checker( $args );
		} else {
			asas_virtuais()->admin_manager()->admin_warning( 'Plugin updater initialized incorrectly, using Pre-Release for the plugin: ' . $plugin_name );
		}
	}
	private function build_update_checker( $args ) {
		require_once $args['puc_path'];
		$myUpdateChecker = \Puc_v4_Factory::buildUpdateChecker(
			$args['meta_url'],
			$args['plugin_file'],
			$args['plugin_name'],
		);
	}

	private function valid_args( $args ) {
		return isset(
			$args['plugin_file'],
			$args['puc_path'],
			$args['meta_url'],
		);
	}

}
