<?php
namespace AsasVirtuaisWP\WooCommerce\Taxonomy;

use AsasVirtuaisWP\Taxonomy\Models\AbstractTerm;

abstract class AbstractAttribute extends AbstractTerm {

	abstract public static function attribute_name();

	public static function get_taxonomy() {
		return 'pa_' . sanitize_title( static::attribute_name() );
	}

}
