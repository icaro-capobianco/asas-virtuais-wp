<?php
namespace AsasVirtuaisWP;

defined( 'ABSPATH' ) or exit;

class AsasVirtuais {

	private static $instance = null;

	public $plugin_prefix;
	public $plugin_version;

	public $plugin_url;
	public $plugin_dir;
	public $framework_url;
	public $framework_dir;

	public $admin_manager;
	public $assets_manager;

	public $acf_manager;

	public static function instance() {

		if ( null === self::$instance ) {
            self::$instance = new self();
		}

		return self::$instance;
    }

    public function initialize( $plugin_file, $framework_file, $args = [] ) {

		$this->plugin_prefix = $args['prefix'] ?? '';
		$this->plugin_version = $args['version'] ?? false;

		$this->plugin_url = plugin_dir_url( $plugin_file );
		$this->plugin_dir = plugin_dir_path( $plugin_file );
		$this->framework_url = plugin_dir_url( $framework_file );
		$this->framework_dir = plugin_dir_path( $framework_file );

		$this->admin_manager = new \AsasVirtuaisWP\Admin\AdminManager();

		if ( isset( $args['custom_fields_dir'] ) ) {
			$this->acf_manager = new \AsasVirtuaisWP\ACF\ACFManager( $args['custom_fields_dir'] );
		}
		if ( isset( $args['assets_dir'] ) ) {
			$this->assets_manager = new \AsasVirtuaisWP\Assets\AssetsManager( $args['assets_dir'], $this->plugin_version, $this->plugin_prefix );
		}

		foreach( glob( $this->framework_dir . "lib/*.php") as $lib_file ){
            require_once $lib_file;
		}
    }

}
