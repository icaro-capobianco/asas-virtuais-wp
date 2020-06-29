<?php
namespace AsasVirtuaisWP;

defined( 'ABSPATH' ) or exit;

/** Not a SINGLETON */
class AsasVirtuais {

	private static $instances = [];

	public static function instance( $plugin_slug ) {

		if ( ! isset( self::$instances[$plugin_slug] ) ) {
            self::$instances[$plugin_slug] = new self();
		}

		return self::$instances[$plugin_slug];
    }

	public $plugin_version;
	public $plugin_prefix;
	public $plugin_name;
	public $plugin_file;
	public $plugin_url;
	public $plugin_dir;
    public function initialize( $plugin_file, $args = [] ) {

		$this->plugin_name = wp_basename( $plugin_file, '.php' );
		$this->plugin_prefix = $args['prefix'] ?? '';
		$this->plugin_version = $args['version'] ?? false;
		$this->plugin_file = $plugin_file;
		$this->plugin_url = plugin_dir_url( $plugin_file );
		$this->plugin_dir = plugin_dir_path( $plugin_file );

		return $this;
	}
	
	private $admin_manager;
	public function admin_manager() {
		if ( ! isset( $this->admin_manager ) ) {
			$this->admin_manager = new Admin\AdminManager();
		}
		return $this->admin_manager;
	}
	private $cpt_manager;
	public function cpt_manager() {
		if ( ! isset( $this->cpt_manager ) ) {
			$this->cpt_manager = new CPT\CPTManager();
		}
		return $this->cpt_manager;
	}
	private $acf_manager;
	public function acf_manager() {
		if ( ! isset( $this->acf_manager ) ) {
			$this->acf_manager = new ACF\ACFManager();
		}
		return $this->acf_manager;
	}
	private $assets_manager;
	public function assets_manager( $args = [] ) {
		if ( ! isset( $this->assets_manager ) ) {
			$this->assets_manager = new Assets\AssetsManager( $args );
		}
		return $this->assets_manager;
	}
	private $update_manager;
	public function update_manager( $args = [] ) {
		if ( ! isset( $this->update_manager ) ) {
			$this->update_manager = PuC\UpdateManager::instance();
		}
		$this->update_manager->register_plugin( $this->plugin_file, $args );
		return $this->update_manager;
	}
	private $rest_manager;
	public function rest_manager( $route_namespace = false ) {
		if ( ! isset( $this->rest_manager ) ) {
			if ( ! $route_namespace ) {
				$prefix = empty( $this->plugin_prefix ) ? $this->plugin_name : $this->plugin_prefix;
				$route_namespace = "$prefix/v1";
			}
			$this->rest_manager = new API\RestManager( $route_namespace );
		}
		return $this->rest_manager;
	}
	private $import_manager;
	public function import_manager() {
		if ( ! isset( $this->import_manager ) ) {
			if ( ! isset( $this->rest_manager ) ) {
				throw new \Exception('Must instantiate rest_manager before import_manager');
			}
			$this->import_manager = new Migration\ImportManager( $this );
		}
		return $this->import_manager;
	}

}
