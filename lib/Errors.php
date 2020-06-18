<?php

if ( ! function_exists( 'av_wp_error_message' ) ) {
	function av_wp_error_message( $wp_error ) {
		$errors   = implode( "\n", $wp_error->get_error_codes() );
		$messages = implode( "\n", $wp_error->get_error_messages() );
		return "WP_Error \n code: \n $errors \n messages: \n $messages ";
	}
}
