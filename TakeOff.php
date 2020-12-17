<?php
namespace AsasVirtuais\WP\Framework;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\AsasVirtuais\WP\Framework\TakeOff' ) ) {
	class TakeOff {
		private static $instance;
		private $version;
		private $file;
		private $plugins = [];
		private function __construct() {
		}
		public function fly( $autoload, $plugin_file, $args = [] ) {

			/** Register every plugin that required this framework */
			$plugin_slug = wp_basename( $plugin_file, '.php' );
			$this->plugins[$plugin_slug] = $args;

			// Set autoload
			if ( ! did_action( 'asas/loaded' ) ) {
				$this->load_framework( $autoload );
			}

			/** Instance of the framework for the plugin */
			$framework_instance = asas_virtuais( $plugin_slug )->initialize( $plugin_file, $args );
			/** Trigger loaded action */
			return $framework_instance;
		}
		public function load_framework( $autoload ) {
			/** Set autoload */
			$includes_dir = dirname( $this->file ) . '/includes/';
			$autoload->addPsr4( 'AsasVirtuaisWP\\', $includes_dir );

			/** Require asas_virtuais() */
			require_once( $includes_dir . 'functions.php' );

			/** Require other libraries */
			foreach( glob( dirname( $this->file ) . "/lib/*.php") as $lib_file ){
				require_once $lib_file;
			}

			/** Initialize framework default instance */
			asas_virtuais()->initialize( __FILE__, [
				'version' => $this->version,
				'prefix' => 'asas_'
			] );

			/** Trigger action asas/loaded */
			do_action( 'asas/loaded' );
		}
		public function register_version( $file, $version ) {
			if ( version_compare( $version, $this->version ) > 0 ) {
				$this->version = $version;
				$this->file    = $file;
			}
			return $this;
		}
		public static function instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}
	
			return self::$instance;
		}
		/** Cloning instances is forbidden due to singleton pattern. */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.0.0' );
		}
		/** Unserializing instances is forbidden due to singleton pattern. */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.0.0' );
		}
	}
}

return TakeOff::instance()->register_version( __FILE__, '6.8.3' );
