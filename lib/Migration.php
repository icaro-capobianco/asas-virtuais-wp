<?php

function av_import_admin_notice( $message ) {
	asas_virtuais()->import_manager->import_notice( $message );
}

function av_import_admin_exception( $th, $additional = false ) {
	$message = "\nFile:" . $th->getFile() . "\nLine" . $th->getLine() . "\nMessage:" . $th->getMessage();
	if ( $additional ) {
		$message .= "\nAdditional input: " . $additional;
	}
	av_import_admin_error( $message );
}

function av_import_admin_error( $message ) {
	asas_virtuais()->import_manager->import_error( $message );
}
