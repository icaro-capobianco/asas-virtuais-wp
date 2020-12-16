<?php

defined( 'ABSPATH' ) or exit;

if ( ! function_exists( 'av_get_plugin_data' ) ) {
	function av_get_plugin_data( $plugin_file ) {
		if( ! function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		return get_plugin_data( $plugin_file, false );
	}
}
