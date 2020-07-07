<?php
if ( ! function_exists( 'av_sanitize_title_with_underscores' ) ) {
	function av_sanitize_title_with_underscores( $title ) {
		return str_replace( '-', '_', sanitize_title( $title ) );
	}
}

if ( ! function_exists( 'av_unslug' ) ) {
	function av_unslug( $slug ) {
		return ucwords( str_replace( [ '-', '_' ], ' ', $slug ) );
	}
}

if ( ! function_exists( 'av_depascal_case' ) ) {
	function av_depascal_case( $string ) {
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));	
	}
}
