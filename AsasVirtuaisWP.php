<?php

defined( 'ABSPATH' ) or exit;

class AsasVirtuaisWP {

	const MINIMUM_PHP_VERSION = '7.3';
	const MINIMUM_WP_VERSION = '5.0';

	/** @var AsasVirtuaisWP */
	private static $instance;

	/** @var array the admin notices to add */
	private $notices = [];

	private $plugin_file;

	protected function __construct( $plugin_file ) {

		$this->plugin_file = $plugin_file;

		register_activation_hook( $plugin_file, [ $this, 'activation_check' ] );

		add_action( 'admin_init', [ $this, 'check_environment' ] );
		add_action( 'admin_init', [ $this, 'add_plugin_notices' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ], 15 );

		if ( $this->is_environment_compatible() ) {
			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		}
	}
	public function init_plugin() {
		if ( ! $this->is_wp_compatible() ) {
			return;
		}
		$loader = require_once( plugin_dir_path( $this->plugin_file ) . 'vendor/autoload.php' );
		$loader->addPsr4( 'AsasVirtuaisWP\\', plugin_dir_path( __FILE__ ) . 'includes' );

		require_once( plugin_dir_path( __FILE__ ) . 'includes/AsasVirtuais.php' );
	}
	public static function instance( $plugin_file ) {

		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_file );
		}

		return self::$instance;
	}

	// Activation/Deactivation Checks
		public function activation_check() {

			if ( ! $this->is_environment_compatible() ) {

				$this->deactivate_plugin();

				wp_die( $this->plugin_file . ' could not be activated. ' . $this->get_environment_message() );
			}
		}
		protected function deactivate_plugin() {

			deactivate_plugins( plugin_basename( $this->plugin_file ) );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

	// Admin Notices
		public function add_plugin_notices() {

			if ( ! $this->is_wp_compatible() ) {
				$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
					'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
					'<strong>' . $this->plugin_file . '</strong>',
					self::MINIMUM_WP_VERSION,
					'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
				) );
			}

		}
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
		private function is_wp_compatible() {
			if ( ! self::MINIMUM_WP_VERSION ) {
				return true;
			}
			return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
		}
		private function is_environment_compatible() {
			return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
		}
		private function get_environment_message() {
			return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
		}
		public function check_environment() {

			if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( $this->plugin_file ) ) ) {
	
				$this->deactivate_plugin();
	
				$this->add_admin_notice( 'bad_environment', 'error', $this->plugin_file . ' has been deactivated. ' . $this->get_environment_message() );
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
