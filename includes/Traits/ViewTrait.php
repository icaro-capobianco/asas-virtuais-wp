<?php

namespace AsasVirtuaisWP\Traits;

Trait ViewTrait {

	abstract public function views_dir_path();

	public function load_view( $view, $data = [] ) {
		$dir = $this->views_dir_path();
		return av_load_view( $dir, $view, $data, false );
	}

	public function render_view( $view, $data = [] ) {
		$dir = $this->views_dir_path();
		return av_render_view( $dir, $view, $data, true );
	}

}
