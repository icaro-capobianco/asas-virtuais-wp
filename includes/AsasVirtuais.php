<?php
namespace AsasVirtuaisWP\V2_0_3;

defined( 'ABSPATH' ) or exit;

/** Not a SINGLETON */
class AsasVirtuais {

	private static $instances = [];

	public $plugin_prefix;
	public $plugin_version;

	public $plugin_url;
	public $plugin_dir;
	public $framework_url;
	public $framework_dir;

	public $admin_manager;
	public $assets_manager;

	public $acf_manager;

	public static function instance( $plugin_slug ) {

		if ( ! isset( self::$instances[$plugin_slug] ) ) {
            self::$instances[$plugin_slug] = new self();
		}

		return self::$instances[$plugin_slug];
    }

    public function initialize( $plugin_file, $args = [] ) {

		$this->plugin_prefix = $args['prefix'] ?? '';
		$this->plugin_version = $args['version'] ?? false;

		$this->plugin_url = plugin_dir_url( $plugin_file );
		$this->plugin_dir = plugin_dir_path( $plugin_file );
		$this->framework_url = plugin_dir_url( __DIR__ );
		$this->framework_dir = plugin_dir_path( __DIR__ );

		$this->admin_manager = new Admin\AdminManager();

		if ( isset( $args['custom_fields_dir'] ) ) {
			$this->acf_manager = new ACF\ACFManager( $args['custom_fields_dir'] );
		}
		if ( isset( $args['assets_dir'] ) ) {
			$this->assets_manager = new Assets\AssetsManager( $args['assets_dir'], $this->plugin_version, $this->plugin_prefix );
		}

		foreach( glob( $this->framework_dir . "lib/*.php") as $lib_file ){
            require_once $lib_file;
		}
		
		return $this;
    }

}
