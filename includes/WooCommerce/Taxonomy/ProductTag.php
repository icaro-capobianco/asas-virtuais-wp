<?php
namespace AsasVirtuaisWP\V2_0_0\WooCommerce\Taxonomy;

use AsasVirtuaisWP\V2_0_0\Taxonomy\AbstractTerm;

class ProductTag extends AbstractTerm {

    final static function get_taxonomy() {
        return 'product_tag';
    }

}