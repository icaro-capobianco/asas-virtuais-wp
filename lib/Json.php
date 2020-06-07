<?php

function av_to_json( $input, $options = 0, $depth = 512 ) {
	$json = json_encode( $input, $options, $depth );
	$ret  = [
		'result'              => $json,
		'success'             => json_last_error() === JSON_ERROR_NONE,
		'failure'             => json_last_error() !== JSON_ERROR_NONE,
		'json_last_error'     => json_last_error(),
		'json_last_error_msg' => '',
	];
	if ( $ret['failure'] ) {
		$ret['json_last_error_msg'] = json_last_error_msg();
	}
	return $ret;
}
function av_to_json_or_throw( $input, $options = 0, $depth = 512 ) {
	$info = av_to_json( $input, $options, $depth );
	if ( $info['success'] ) {
		return $info['result'];
	}
	throw new \Exception( $info['json_last_error_msg'] );
}
function av_parse_json( $input, $assoc = false, $depth = 512, $options = 0 ) {
	$val        = json_decode( $input, $assoc, $depth, $options );
	$last_error = json_last_error();
	$ret        = [
		'result'              => $val,
		'success'             => $last_error === JSON_ERROR_NONE,
		'failure'             => $last_error !== JSON_ERROR_NONE,
		'json_last_error'     => $last_error,
		'json_last_error_msg' => '',
	];
	if ( $ret['failure'] ) {
		$ret['json_last_error_msg'] = json_last_error_msg();
	}
	return $ret;
}
function av_parse_json_or_throw( $input, $assoc = false, $depth = 512, $options = 0 ) {
	$info = av_parse_json( $input, $assoc, $depth, $options );
	if ( $info['success'] ) {
		return $info['result'];
	}
	throw new \Exception( $info['json_last_error_msg'] );
}

