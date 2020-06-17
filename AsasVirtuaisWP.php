<?php

namespace AsasVirtuais\WP\PluginFramework\V2_0_0;

defined( 'ABSPATH' ) or exit;

class AsasVirtuaisWP {

	const MINIMUM_PHP_VERSION = '7.3';
	const MINIMUM_WP_VERSION = '5.0';

	/** @var AsasVirtuaisWP */
	private static $instance;

	/** @var array the admin notices to add */
	private $notices = [];

	private $plugin_file;

	private $init_args;

	/** Registered plugins using the Framework */
	private $plugins = [];

	protected function __construct() {

		add_action( 'admin_init', [ $this, 'check_environment' ] );
		add_action( 'admin_init', [ $this, 'check_wp_version' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ], 15 );

		if ( $this->is_environment_compatible() ) {

			require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php' );

			foreach( glob( plugin_dir_path( __FILE__ ) . "lib/*.php") as $lib_file ){
				require_once $lib_file;
			}

			$static_framework_instance = $this->add_plugin( 'asas-virtuais-wp' );
		}

	}

	public function add_plugin( $plugin_file, $args = [] ) {

		if ( function_exists( 'asas_virtuais' ) ) {
			$plugin_slug = wp_basename( $plugin_file, '.php' );
			$this->plugins[$plugin_slug] = $args;
	
			register_activation_hook( $plugin_file, [ $this, 'activation_check' ] );
	
			return asas_virtuais( $plugin_slug )->initialize( $plugin_file, $args );
		}

	}

	// Instance
		public static function instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

	// Activation/Deactivation Checks
		public function activation_check() {

			if ( ! $this->is_environment_compatible() ) {

				$this->deactivate_plugin();

				wp_die( $this->plugin_file . ' could not be activated. ' . $this->php_version_message() );
			}
		}
		protected function deactivate_plugin() {

			deactivate_plugins( plugin_basename( $this->plugin_file ) );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

	// Admin Notices
		private function add_admin_notice( $slug, $class = 'info', $message ) {
			$this->notices[ $slug ] = compact( 'class', 'message' );
		}
		public function admin_notices() {
			foreach ( (array) $this->notices as $notice_key => $notice ) {
				?>
				<div class="<?php echo esc_attr( $notice['class'] ); ?>">
					<p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
				</div>
				<?php
			}
		}

	// Compatibility
		private function php_version_message() {
			return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
		}
		private function is_environment_compatible() {
			return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
		}
		private function is_wp_compatible() {
			if ( ! self::MINIMUM_WP_VERSION ) {
				return true;
			}
			return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
		}
		public function check_environment() {

			if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( $this->plugin_file ) ) ) {
	
				$this->deactivate_plugin();
	
				$this->add_admin_notice( 'bad_environment', 'error', $this->plugin_file . ' has been deactivated. ' . $this->php_version_message() );
			}
		}
		public function check_wp_version() {

			if ( ! $this->is_wp_compatible() ) {
				$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
					'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
					'<strong>' . $this->plugin_file . '</strong>',
					self::MINIMUM_WP_VERSION,
					'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
				) );
			}

		}
	// Donts
		/** Cloning instances is forbidden due to singleton pattern. */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.0.0' );
		}
		/** Unserializing instances is forbidden due to singleton pattern. */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.0.0' );
		}

}
