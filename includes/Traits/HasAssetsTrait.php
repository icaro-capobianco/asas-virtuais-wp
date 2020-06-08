<?php
namespace Gsg_Onboard\Traits;

trait HasAssetsTrait {

	abstract public static function assets_dir_path();

	public function enqueue_script( $name, $footer = true, $deps = [] ) {
		asas_virtuais()->assets_manager->enqueue_local_script (
			$name,
			$this->assets_dir_path(),
			$footer,
			$deps,
		);
	}

	public function enqueue_admin_script( $name, $footer = true, $deps = [] ) {
		asas_virtuais()->assets_manager->enqueue_local_admin_script (
			$name,
			$this->assets_dir_path(),
			$footer,
			$deps,
		);
	}

	public function enqueue_style( $name, $deps = [], $media = 'all' ) {
		asas_virtuais()->assets_manager->enqueue_local_style (
			$name,
			$this->assets_dir_path(),
			$deps,
			$media,
		);
	}

}
