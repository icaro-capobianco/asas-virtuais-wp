<?php

if ( ! function_exists( 'av_array_keys_exist' ) ) {
	function av_array_keys_exist( $array, $keys ) {
		foreach ( $keys as $k ) {
			if( ! isset( $array[$k] ) ) {
				return false;
			}
		}
		return true;
	}
}
/**
 * [ insert_data => [ title => A, thumbnail => B ] ]
 * [ insert_data => title, thumbnail ]
 */
if ( ! function_exists( 'av_array_keys_exist_recursive' ) ) {
	function av_array_keys_exist_recursive( $array, $keys ) {
		foreach ( $keys as $k => $key ) {
			if ( is_string( $k ) ) {
				$val = $array[$k] ?? false;
				if ( ! $val ) {
					return false;
				}
				if ( is_scalar( $val ) ) {
					return false;
				}
				return av_array_keys_exist_recursive( $val, $key );
			} else {
				$val = $array[$key] ?? false;
				if ( ! $val ) {
					return false;
				}
			}
		}
		return true;
	}
}
