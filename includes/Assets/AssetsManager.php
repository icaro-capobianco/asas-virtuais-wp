<?php

namespace AsasVirtuaisWP\Assets;

class AssetsManager {

	public $prefix;
	public $version;
	public $styles = [];
	public $scripts = [];
	public $localize = [];
	public $admin_styles = [];
	public $admin_scripts = [];
	public $plugin_assets_dir;

	public function __construct( $assets_dir, $version, string $prefix = '' ) {
		$this->plugin_assets_dir = $assets_dir;
		$this->prefix  = $prefix;
		$this->version = $version;

		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
	}

	public function assets_dir() {
		return $this->plugin_assets_dir;
	}

	// Hooks and Internal methods
	public function enqueue_styles() {
		foreach( $this->styles as $style ) {
			$this->enque_style( $style );
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
	public function enqueue_local_style( $name, $dir, $deps = [], $media = 'all' ) {
		$src = self::asset_file_url( $name, $dir, '.css' );
		$name = $this->prefix . $name;
		$this->styles[] = (object) compact( 'name', 'src', 'deps', 'media' );
	}
	public function enqueue_local_script( $name, $dir, $footer = true, $deps = [] ) {
		$src = self::asset_file_url( $name, $dir, '.js' );
		$name = $this->prefix . $name;
		$this->scripts[] = (object) compact( 'name', 'src', 'footer', 'deps' );
	}
	public function enqueue_local_admin_script( $name, $dir, $footer = true, $deps = [] ) {
		$src = self::asset_file_url( $name, $dir, '.js' );
		$name = $this->prefix . $name;
		$this->admin_scripts[] = (object) compact( 'name', 'src', 'footer', 'deps' );
	}
	public function enqueue_local_admin_style( $name, $dir, $deps = [], $media = 'all' ) {
		$src = self::asset_file_url( $name, $dir, '.css' );
		$name = $this->prefix . $name;
		$this->admin_styles[] = (object) compact( 'name', 'src', 'deps', 'media' );
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
