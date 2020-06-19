<?php
namespace AsasVirtuais\WP\Framework;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\AsasVirtuais\WP\Framework\TakeOff' ) ) {
	class TakeOff {
		private $version;
		private $file;
		private $plugins = [];
		private function __construct() {
		}
		public function fly( $autoload, $plugin_file, $args ) {
			/** Dir to the framework latests version required across all plugins */
			$includes_dir = dirname( $this->file ) . 'includes/';
			/** Set the autoload Psr4 path */
			$autoload->setPsr4( 'AsasVirtuaisWP\\', $includes_dir . 'includes/' );
			/** Require functions file with the func asas_virtuais */
			require_once( $includes_dir . 'functions.php' );
			/** Register every plugin that required this framework */
			$plugin_slug = wp_basename( $plugin_file, '.php' );
			$this->plugins[$plugin_slug] = $args;
			/** Trigger loaded action */
			do_action( 'asas/loaded' );
			/** Instance of the framework for the plugin */
			return asas_virtuais( $plugin_slug )->initialize( $plugin_file, $args );
		}
		public function register_version( $file, $version ) {
			if ( did_action( 'asas/loaded' ) ) {
				throw new \Exception('Loading framework version too late, framework already loaded');
			} else {
				if ( version_compare( $version, $this->version ) > 0 ) {
					$this->version = $version;
					$this->file    = $file;
				}
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

return TakeOff::instance()->register_version( __FILE__, '3.0.0' );
