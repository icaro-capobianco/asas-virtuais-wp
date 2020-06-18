<?php
if ( ! function_exists( 'av_sanitize_title_with_underscores' ) ) {
	function av_sanitize_title_with_underscores( $title ) {
		return str_replace( '-', '_', sanitize_title( $title ) );
	}
}
