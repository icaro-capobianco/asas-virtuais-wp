<?php

if ( ! function_exists( 'av_get_post_by_slug' ) ) {
	/**
	 * @param string $slug
	 * @param mixed $args get_post $args
	 * @return \WP_Post|false
	 */
	function av_get_post_by_slug( $slug, $args = [] ) {
		$args['name'] = $slug;
		$posts = get_posts( $args );
		return isset( $posts[0] ) ? $posts[0] : false;
	}
}
