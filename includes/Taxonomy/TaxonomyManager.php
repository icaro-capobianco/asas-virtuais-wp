<?php

namespace AsasVirtuaisWP\Taxonomy;

class TaxonomyManager {

	public $prefix;
	public $custom_taxonomies = [];

	public function __construct( $prefix = '' ) {

		$this->prefix = $prefix;
		add_action( 'init', [ $this, 'register_taxonomies' ] );

	}

	public function register_taxonomies() {
		foreach( $this->custom_taxonomies as $name => $custom_taxonomy ) {
			register_taxonomy( $name, $custom_taxonomy->post_types, $custom_taxonomy->args );
		}
	}

	public function register_taxonomy( $slug, $post_types, $args = [] ) {

		$name = $this->prefix . $slug;

		if ( ! isset( $args['labels'] ) ) {
			$args['labels'] = $this->taxonomy_labels( $slug );
		}

		$this->custom_taxonomies[$name] = (object) compact( 'post_types', 'args' );

	}

	public function taxonomy_labels( $slug ) {

		$name     = str_replace( [ '-', '_' ], ' ', $slug );
		$ucname   = ucwords( $name );

		$last_char = $slug[ strlen( $slug ) - 1 ];

		if ( $last_char === 'y' ) {

			$plural = substr_replace( $name, "ies", -1 );
			$ucplural = substr_replace( $ucname, "ies", -1 );

		} else {

			$plural   = $name . 's';
			$ucplural = $ucname . 's';

		}

		$taxonomy_labels = [
			'name'                       => $ucplural,
			'singular_name'              => $ucname,
			'search_items'               => "Search $ucplural",
			'popular_items'              => "Popular $ucplural",
			'all_items'                  => "All $ucplural",
			'parent_item'                => "Parent $ucname",
			'parent_item_colon'          => "Parent $ucname:",
			'edit_item'                  => "Edit $ucname",
			'view_item'                  => "View $ucname",
			'update_item'                => "Update $ucname",
			'add_new_item'               => "Add New $ucname",
			'new_item_name'              => "New $ucname Name",
			'separate_items_with_commas' => "Separate $plural with commas",
			'add_or_remove_items'        => "Add or remove $plural",
			'choose_from_most_used'      => "Choose from the most used $plural",
			'not_found'                  => "No $plural found",
			'no_terms'                   => "No $plural",
			'items_list_navigation'      => $ucplural,
			'items_list'                 => $ucplural,
			'most_used'                  => "Most Used $ucplural",
			'back_to_items'              => "$ucname Updated",
		];
		return apply_filters( 'asas/taxonomy/labels', $taxonomy_labels, $slug );
	}

}
