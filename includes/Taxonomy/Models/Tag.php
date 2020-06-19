<?php
namespace AsasVirtuaisWP\Taxonomy\Models;

class Tag extends AbstractTerm {

    final static function get_taxonomy() {
        return 'tag';
    }

}
