<?php
namespace AsasVirtuaisWP;

defined( 'ABSPATH' ) or exit;

class AsasVirtuais {

	private static $instance = null;

	public $plugin_dir;
	public $plugin_url;
	public $framework_dir;
	public $framework_url;

	public static function instance() {

		if ( null === self::$instance ) {
            self::$instance = new self();
		}

		return self::$instance;
    }

    public function initialize( $plugin_file, $framework_file ) {

        $this->plugin_dir = plugin_dir_path( $plugin_file );
		$this->plugin_url = plugin_dir_url( $plugin_file );
		$this->framework_dir = plugin_dir_path( $framework_file );
		$this->framework_url = plugin_dir_url( $framework_dir );

		foreach( glob( $this->framework_dir . "libs/*.php") as $lib_file ){
            require_once $lib_file;
		}
    }

}

function asas_virtuais() {
	return AsasVirtuais::instance();
}
