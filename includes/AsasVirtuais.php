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

	public $plugin_data;
	public $plugin_version;
	public $plugin_prefix;
	public $plugin_slug;
	public $plugin_name;
	public $plugin_file;
	public $plugin_url;
	public $plugin_dir;
    public function initialize( string $plugin_file, array $args = [] ) {

		$plugin_data = $args['plugin_data'] ?? av_get_plugin_data( $plugin_file, false, false );
		$this->plugin_data = $plugin_data;
		$this->plugin_prefix = $args['prefix'] ?? '';
		$this->plugin_version = $args['version'] ?? $plugin_data['Version'];
		$this->plugin_slug = wp_basename( $plugin_file, '.php' );
		$this->plugin_name = $plugin_data['Name'] ?? $this->plugin_slug;
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
			$this->acf_manager = new ACF\ACFManager( $this );
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
		$this->update_manager->register_plugin( $this, $args );
		return $this->update_manager;
	}
	public $rest_manager;
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
	public function import_manager( $token = false ) {
		if ( ! isset( $this->import_manager ) ) {
			$this->import_manager = new Migration\ImportManager( $this, $token );
		}
		return $this->import_manager;
	}
	private $taxonomy_manager;
	public function taxonomy_manager( $prefix = '' ) {
		if ( ! isset( $this->taxonomy_manager ) ) {
			$this->taxonomy_manager = new Taxonomy\TaxonomyManager( $prefix );
		}
		return $this->taxonomy_manager;
	}
	private $template_manager;
	public function template_manager( $args = [] ) {
		if ( ! isset( $this->template_manager ) ) {
			$this->template_manager = new Templates\TemplateManager( $args );
		}
		return $this->template_manager;
	}
	private $hook_manager;
	public function hook_manager() {
		if ( ! isset( $this->hook_manager ) ) {
			$this->hook_manager = new Hooks\HookManager();
		}
		return $this->hook_manager;
	}
	private $meta_manager;
	public function meta_manager() {
		if ( ! isset( $this->meta_manager ) ) {
			$this->meta_manager = new Meta\MetaManager();
		}
		return $this->meta_manager;
	}

	private $current_user;
	public function user() {
		if ( ! isset( $this->current_user ) ) {
			return new Models\User;
		}
		return $this->current_user;
	}

	/**
	 * @param mixed $plugins array of plugin index by plugin_dir/plugin_file strings and with the plugin name as value
	 * @return bool
	 */
	public function check_required_plugins( array $plugins ) {

		foreach ( $plugins as $plugin_dir_file => $plugin_name ) {

			if ( ! is_plugin_active( $plugin_dir_file ) ) {
				$this->admin_manager()->admin_error( "The plugin $this->plugin_name requires the plugin $plugin_name to be installed and active." );
				return false;
			}
		}

		return true;
	}

}
