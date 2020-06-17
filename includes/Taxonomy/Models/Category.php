<?php
namespace AsasVirtuaisWP\V2_0_0\Taxonomy\Models;

class Category extends AbstractTerm {

    final static function get_taxonomy() {
        return 'category';
    }

}