<?php


function av_taxonomy_labels( $name ) {
	$name     = str_replace( [ '-', '_' ], ' ', $name );
	$ucname   = ucwords( $name );
	$plural   = $name . 's';
	$ucplural = $ucname . 's';
	return [
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
}
