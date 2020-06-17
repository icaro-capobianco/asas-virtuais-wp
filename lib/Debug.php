<?php

if ( ! function_exists( 'av_show' ) ) {
	function av_show( $var, $dump = false, $exit = true ) {
		echo '<pre>';
		if ( $dump ) {
			var_dump( $var );
		} else {
			print_r( $var );
		}
		echo '</pre>';
		if ( $exit ) {
			exit();
		}
	}
}
