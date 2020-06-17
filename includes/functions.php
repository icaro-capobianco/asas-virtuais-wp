<?php

if ( ! function_exists( 'asas_virtuais' ) ) {
	function asas_virtuais( $plugin_slug = 'asas-virtuais-wp' ) {
		return \AsasVirtuaisWP\v2_0_0\AsasVirtuais::instance( $plugin_slug );
	}
}
