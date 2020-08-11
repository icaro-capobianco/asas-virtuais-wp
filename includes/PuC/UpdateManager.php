<?php
namespace AsasVirtuaisWP\PuC;

class UpdateManager {

	private function __construct() {

		add_action( 'plugins_loaded', [ $this, 'maybe_add_pre_release_settings_page' ], 20, 1 );

	}

	public $pre_release_plugins = [];
	public function maybe_add_pre_release_settings_page() {

		if ( ! empty( $this->pre_release_plugins ) ) {

			// Create the Release Settings page
			asas_virtuais()->acf_manager()->settings_page( 'Release Settings' );

			// Generate the switch field
			$fields = [];
			foreach( $this->pre_release_plugins as $plugin_slug => $plugin_name ) {
				$fields[] = av_acf_boolean_field( $plugin_slug . ' Pre-Release', [
					'label' => $plugin_name . ' Pre-Release'
				] );
			}

			// Add the page fields
			asas_virtuais()->acf_manager()->add_field_group( av_acf_field_group( 
				'Plugin Release Options',
				[ [ av_acf_location( 'options_page', 'acf-options-release-settings' ) ] ],
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

	public function register_plugin( $framework_instance, $args ) {
		try {
			$plugin_file = $framework_instance->plugin_file;
			$plugin_name = $framework_instance->plugin_name;
			$plugin_slug = $framework_instance->plugin_slug;

			$puc_path = $args['puc_path'] ?? false;
			$meta_url = $args['meta_url'] ?? false;
			$pre_release_url = $args['pre_release_url'] ?? false;
	
			if ( ! class_exists( 'Puc_v4_Factory' ) && ! include_once( $puc_path ) ) {
				return asas_virtuais()->admin_manager()->admin_warning( 'Plugin updater could not be loaded for the plugin: ' . $plugin_name );
			}
			if ( $meta_url && $plugin_slug ) {
				if ( $pre_release_url ) {
					$this->pre_release_plugins[$plugin_slug] = $plugin_name;
					if ( av_acf_get_field( av_sanitize_title_with_underscores($plugin_slug)."_pre_release", 'option' ) ) {
						return $this->build_update_checker( $pre_release_url, $plugin_file, $plugin_name );
					}
				}
				return $this->build_update_checker( $meta_url, $plugin_file, $plugin_name );
			} else {
				asas_virtuais()->admin_manager()->admin_warning( 'Plugin updater initialized incorrectly for the plugin: ' . $plugin_name );
			}	
		} catch (\Throwable $th) {
			$framework_instance->admin_manager()->admin_error_from_exception( $th );
		}
	}

	private function build_update_checker( $meta_url, $plugin_file, $plugin_slug ) {
		if ( class_exists( 'Puc_v4_Factory' ) ) {
			$myUpdateChecker = \Puc_v4_Factory::buildUpdateChecker(
				$meta_url,
				$plugin_file,
				$plugin_slug,
			);
		} else {
			asas_virtuais()->admin_manager()->admin_warning( 'Plugin updater class not found for the plugin: ' . $plugin_slug );
		}
	}

}
