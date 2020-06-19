<?php

namespace AsasVirtuaisWP\Models;

class Post {

	protected $wp_post;

	public function __construct( \WP_Post $wp_post ) {
		$this->wp_post = $wp_post;
	}

	public function get_id() {
		return $this->wp_post->ID;
	}
	public function get_type() {
		return $this->wp_post->post_type;
	}
	public function get_author() {
		return $this->wp_post->post_author;
	}
	public function get_content() {
		return $this->wp_post->post_content;
	}
	public function get_title() {
		return $this->wp_post->post_title;
	}
	public function get_excerpt() {
		return $this->wp_post->post_excerpt;
	}
	public function get_status() {
		return $this->wp_post->post_status;
	}
	public function get_comment_status() {
		return $this->wp_post->comment_status;
	}
	public function get_ping_status() {
		return $this->wp_post->ping_status;
	}
	public function get_password() {
		return $this->wp_post->post_password;
	}
	public function get_name() {
		return $this->wp_post->post_name;
	}
	public function get_to_ping() {
		return $this->wp_post->to_ping;
	}
	public function get_pinged() {
		return $this->wp_post->pinged;
	}
	public function get_parent() {
		return $this->wp_post->post_parent;
	}
	public function get_guid() {
		return $this->wp_post->guid;
	}
	public function get_menu_order() {
		return $this->wp_post->menu_order;
	}

	public function export_array() {
		$array   = [
			'slug'              => $this->get_name(),
			'title'             => $this->get_title(),
			'long_description'  => $this->get_content(),
			'short_description' => $this->get_excerpt(),
		];

		$array['categories'] = array_map(
			function( $cat ) {
				return $cat->slug;
			},
			get_the_terms( $post->ID, 'category' )
		);

		return $array;
	}

}
