<?php
namespace AsasVirtuaisWP\WooCommerce\Taxonomy;

use AsasVirtuaisWP\Taxonomy\Models\AbstractTerm;

class ProductCat extends AbstractTerm {

    final static function get_taxonomy() {
        return 'product_cat';
    }

}