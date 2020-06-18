<?php
namespace AsasVirtuaisWP\V2_0_3\WooCommerce\Taxonomy;

use AsasVirtuaisWP\V2_0_3\Taxonomy\AbstractTerm;

class ProductTag extends AbstractTerm {

    final static function get_taxonomy() {
        return 'product_tag';
    }

}