<?php

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

function av_map_repeater_and_filter( $repeater, $attributes ) {
	return array_filter( array_map( gsg_map_feature_repeater( $attributes ), $repeater ) );
}