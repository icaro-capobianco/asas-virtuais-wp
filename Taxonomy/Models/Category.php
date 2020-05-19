<?php
namespace AsasVirtuaisWP\Taxonomy\Models;

class Category extends AbstractTerm {

    final static function get_taxonomy() {
        return 'category';
    }

}