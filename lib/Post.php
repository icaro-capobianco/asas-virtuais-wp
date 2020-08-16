<?php

if ( ! function_exists( 'av_get_post_by_slug' ) ) {
	function av_get_post_by_slug( $slug, $args = [] ) {
		$args['name'] = $slug;
		$posts = get_posts( $args );
		return $posts[0] ?? false;
	}
}
