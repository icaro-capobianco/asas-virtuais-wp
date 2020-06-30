<?php
if ( ! function_exists( 'av_map_repeater' ) ) {
	function av_map_repeater( $attributes ) {
		return function( $repeater ) use ( $attributes ) {
			if ( is_string( $attributes ) ) {
				if ( isset( $repeater[$attributes] ) ) {
					return $repeater[$attributes];
				} else {
					return false;
				}
			} elseif ( is_array( $attributes ) ) {
				return array_reduce( $attributes, function( $acc, $attribute ) use ( $repeater ) {
					return $acc[$attribute] = $repeater[$attribute] ?: false;
				}, [] );
			} else {
				throw new \TypeError('Expecting $attributes to be either a string or an array.' );
			}
		};
	}
}
if ( ! function_exists( 'av_map_repeater_and_filter' ) ) {
	function av_map_repeater_and_filter( $repeater, $attributes ) {
		return array_filter( array_map( gsg_map_feature_repeater( $attributes ), $repeater ) );
	}
}

if ( ! function_exists( 'av_index_repeater_reduce' ) ) {
	function av_index_repeater_reduce( $index ) {
		return function( $acc, $repeater ) use ( $index ) {
			$i = $repeater[$index] ?? false;
			if ( $i && is_scalar( $i ) ) {
				if ( isset( $acc[$i] ) ) {
					$acc[$i] = isset( $acc[$i][0] ) ? $acc[$i] : [ $acc[$i] ] ;
					$acc[$i][] = $repeater;
				} else {
					$acc[$i] = [ $repeater ];
				}
			}
			return $acc;
		};
	}
}

if ( ! function_exists( 'av_index_repeater' ) ) {
	function av_index_repeater( $repeater, $index ) {
		return array_reduce( $repeater, av_index_repeater_reduce( $index ), [] );
	}
}
