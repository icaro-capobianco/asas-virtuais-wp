<?php

if ( ! function_exists( 'av_get_error_details' ) ) {
	function av_get_error_details( $e, $pre_msg = "", $separator = "\n" ) {
		$msg = $pre_msg . "\n";
		$class = get_class($e);
		$e_msg = $e->getMessage();
		$msg .= "File: {$e->getFile()}$separator\n";
		$msg .= "Line: {$e->getLine()}$separator\n";
		$msg .= "Type: {$class}$separator\n\n";
		$msg .= "Msg: $e_msg\n\n";
		if( $e instanceof \Swagger\Client\ApiException ) {
			$msg.= "Response Body: {$e->getResponseBody()}\n";
		}
		if( $e instanceof \Gsg\WooCommerce\Evosus\RequestException ) {
			$msg.= "Passed params: \n" . gsg_pretty_print_r( $e->getRequestParams() );
		}
		return $msg;
	}
}
