<?php

namespace AsasVirtuaisWP\Assets;

class AssetsManager {

	public $prefix;
	public $version;

	public $js_dir;
	public $css_dir;
	public $assets_dir;

	public $styles = [];
	public $scripts = [];
	public $localize = [];
	public $admin_styles = [];
	public $admin_scripts = [];

	// Deprecated
	public $plugin_assets_dir;

	public function __construct( $args = [] ) {
		$this->prefix  = $args['prefix'] ?? '';
		$this->version = $args['version'] ?? '';

		if ( isset( $args['js_dir'] ) ) {
			$this->js_dir = $args['js_dir'];
		}
		if ( isset( $args['css_dir'] ) ) {
			$this->css_dir = $args['css_dir'];
		}
		if ( isset( $args['assets_dir'] ) ) {
			$this->assets_dir = $args['assets_dir'];
		}

		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
	}

	// Deprecated
	public function assets_dir() {
		return $this->plugin_assets_dir;
	}

	// Hooks and Internal methods
	public function enqueue_styles() {
		foreach( $this->styles as $style ) {
			$this->enqueue_style( $style );
		}
	}
	public function enqueue_scripts() {
		foreach( $this->scripts as $script ) {
			$this->enqueue_script( $script );
		}
	}
	public function enqueue_admin_scripts() {
		foreach( $this->admin_scripts as $script ) {
			$this->enqueue_script( $script );
		}
	}
	public function enqueue_admin_styles() {
		foreach( $this->admin_styles as $style ) {
			$this->enqueue_style( $style );
		}
	}
	private function enqueue_style( $style ) {
		wp_enqueue_style( $style->name, $style->src, $style->deps, $this->version, $style->media );
	}
	private function enqueue_script( $script ) {
		wp_enqueue_script( $script->name, $script->src, $script->deps, $this->version, $script->footer );
		$localize_arr = $this->localize[ $script->name ] ?? false;
		if ( $localize_arr ) {
			foreach( $localize_arr as $localize )
			wp_localize_script( $script->name, $localize->name, $localize->data );
		}
	}
	// Public methods for local scripts and styles
	public function enqueue_local_script( $name, $dir, $footer = true, $deps = [] ) {
		$src = self::asset_file_url( $name, $dir, '.js' );
		$this->scripts[] = (object) compact( 'name', 'src', 'deps', 'footer' );
	}
	public function enqueue_local_admin_script( $name, $dir, $footer = true, $deps = [] ) {
		$src = self::asset_file_url( $name, $dir, '.js' );
		$this->admin_scripts[] = (object) compact( 'name', 'src', 'deps', 'footer' );
	}
	public function enqueue_local_style( $name, $dir, $deps = [], $media = 'all' ) {
		$src = self::asset_file_url( $name, $dir, '.css' );
		$this->styles[] = $this->compact_style( $name, $src, $deps, $media );
	}
	public function enqueue_local_admin_style( $name, $dir, $deps = [], $media = 'all' ) {
		$src = self::asset_file_url( $name, $dir, '.css' );
		$this->admin_styles[] = $this->compact_style( $name, $src, $deps, $media );
	}
	// Public methods for remote (cdn) scripts and styles
	public function enqueue_remote_script( $name, $src, $footer = true, $deps = [] ) {
		$this->scripts[] = (object) compact( 'name', 'src', 'deps', 'footer' );
	}
	public function enqueue_remote_admin_script( $name, $src, $footer = true, $deps = [] ) {
		$this->admin_scripts[] = (object) compact( 'name', 'src', 'deps', 'footer' );
	}
	public function enqueue_remote_style( $name, $src, $deps = [], $media = 'all' ) {
		$this->styles[] = $this->compact_style( $name, $src, $deps, $media );
	}
	public function enqueue_remote_admin_style( $name, $src, $deps = [], $media = 'all' ) {
		$this->admin_styles[] = $this->compact_style( $name, $src, $deps, $media );
	}

	public function compact_script( $name, $src, $footer = true, $deps = [] ) {
		$name = $this->prefix . $name;
		return (object) compact( 'name', 'src', 'footer', 'deps' );
	}
	public function compact_style( $name, $src, $deps = [], $media = 'all' ) {
		$name = $this->prefix . $name;
		return (object) compact( 'name', 'src', 'deps', 'media' );
	}

	public function register_style( $name, $src = false, $deps = [], $media = 'all' ) {
		$name = $this->prefix . $name;
		if ( ! $src && $this->css_dir ) {
			$src = static::asset_file_url( $name, $this->css_dir, '.css' );
		}
		if ( $src ) {
			wp_register_style( $name, $src, $deps, $this->version, $media );
		}
	}
	public function register_script( $name, $src = false, $footer = true, $deps = [] ) {
		$name = $this->prefix . $name;
		if ( ! $src && $this->js_dir ) {
			$src = static::asset_file_url( $name, $this->js_dir, '.js' );
		}
		if ( $src ) {
			wp_register_script( $name, $src, $deps, $footer, $this->version, $footer );
		}
	}

	public function localize_script( $handle, $name, $data ) {
		$this->localize[$handle][] = (object) compact( 'name', 'data' );
	}


	public static function asset_file_url( $name, $dir_path, $extension ) {
		$min = $name . '.min' . $extension;
		$src = $name . $extension;
		$dir_url = plugin_dir_url( $dir_path . $src );
		if( file_exists( $dir_path . $min ) ) {
			return $dir_url . $min;
		} elseif( file_exists( $dir_path . $src ) ) {
			return $dir_url . $src;
		} else {
			return $dir_url . $src;
		}
	}

}
