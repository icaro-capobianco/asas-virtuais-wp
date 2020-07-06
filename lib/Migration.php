<?php

if ( ! function_exists( 'av_import_admin_notice' ) ) {
	function av_import_admin_notice( $message ) {
		asas_virtuais()->import_manager()->import_notice( $message );
	}
}

if ( ! function_exists( 'av_import_admin_error' ) ) {
	function av_import_admin_error( $message ) {
		asas_virtuais()->import_manager()->import_error( $message );
	}
}

if ( ! function_exists( 'av_import_admin_exception' ) ) {
	function av_import_admin_exception( \Throwable $th, $additional = false ) {
		asas_virtuais()->import_manager()->import_exception( $th, $additional );
	}
}
