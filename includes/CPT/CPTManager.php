<?php
namespace AsasVirtuaisWP\CPT;

class CPTManager {

	private $custom_post_types = [];

	public function register_cpt( $slug, $args = [] ) {

		$args = array_replace( [
			'labels'              => $this->cpt_labels( $slug ),
			'description'         => '',
			'public'              => true,
			'hierarchical'        => false,
			'show_in_rest'        => false,
			'supports'            => [ 'title', 'editor', 'thumbnail' ],
			'rewrite' 	          => false
		], $args );

		return register_post_type( $slug, $args );
	}

	public function cpt_labels( $slug ) {
		$name     = str_replace( [ '-', '_' ], ' ', $slug );
		$ucname   = ucwords( $name );
		$plural   = $name . 's';
		$ucplural = $ucname . 's';
		return [
			'name'                       => $ucplural,
			'singular_name'              => $ucname,
			'search_items'               => "Search $ucplural",
			'add_new_item'               => "Add New $ucname",
			'new_item'                   => "New $ucname",
			'edit_item'                  => "Edit $ucname",
			'view_item'                  => "View $ucname",
			'view_items'                 => "View $ucplural",
			'update_item'                => "Update $ucname",
			'search_items'               => "Search $ucplural",
			'not_found'                  => "No $plural found",
			'not_found_in_trash'         => "No $plural found in trash",
			'parent_item_colon'          => "Parent $ucname",
			'all_items'                  => "All $ucplural",
			'archives'                   => "$ucname Archives",
			'attributes'                 => "$ucname Attributes",
			'insert_into_item'           => "Insert into $ucname",			
			'uploaded_to_this_item'      => "Upload to this $ucname",
			'filter_items_list'          => "Filter $ucplural",
			'items_list_navigation'      => "$ucplural list",
			'items_list'                 => "$ucplural list’",
			'item_published'             => "$ucname published",
			'item_published_privately'   => "$ucname published privately",
			'item_reverted_to_draft'     => "$ucname reverted to draft",
			'item_scheduled'             => "$ucname scheduled",
			'item_updated'               => "$ucname updated",
		];
	}

}
