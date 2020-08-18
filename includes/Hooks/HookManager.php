<?php

namespace AsasVirtuaisWP\Hooks;

class HookManager {

	public function __construct() {
	}

	public function add_action( $name, $callback, $priority = 10, $variables = 1 ) {
		add_action( $name, $this->make_callback( $callback, $variables ), $priority, $variables );
	}

	public function add_filter( $name, $callback, $priority = 10, $variables = 1 ) {
		add_filter( $name, $this->make_callback( $callback, $variables ), $priority, $variables );
	}

	public function make_callback( $callback, $variables ) {
		return function( $anything = false ) use( $callback, $variables ) {
			try {
				$args = func_get_args();
				return call_user_func_array( $callback, $args );
			} catch (\Throwable $th) {
				\av_admin_error_from_exception( $th );
			}
			return $anything;
		};
	}

}
