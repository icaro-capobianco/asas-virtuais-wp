<?php

function av_load_view( $dir, $view, $data = [] ) {
	return $this->require_view( $dir, $view, $data, false );
}

function av_render_view( $dir, $view, $data = [] ) {
	return $this->require_view( $dir, $view, $data, true );
}

function av_require_view( $dir, $view, $data = [], $echo = false ) {

	if ( $data ) {
		extract( $data, EXTR_SKIP );
	}

	if ( $echo === true ) {

		return include( $dir . '/views/' . $view . '.php' );
	} else {

		ob_start();

		$return = include( $dir . '/views/' . $view . '.php' );
		if ( $return ) {
			$return = ob_get_contents();
		}

		ob_end_clean();

		return $return;
	}

}
