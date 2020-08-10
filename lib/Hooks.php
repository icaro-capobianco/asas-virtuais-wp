<?php

if ( ! function_exists( 'av_add_action' ) ) {
	function av_add_action( $name, $callback, $priority = 10, $variables = 1 ) {
		asas_virtuais()->hook_manager()->add_action( $name, $callback, $priority, $variables );
	}
}

if ( ! function_exists( 'av_add_filter' ) ) {
	function av_add_filter( $name, $callback, $priority = 10, $variables = 1 ) {
		asas_virtuais()->hook_manager()->add_filter( $name, $callback, $priority, $variables );
	}
}
