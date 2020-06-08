<?php

namespace AsasVirtuaisWP\WooCommerce;

class Product {

	use \AsasVirtuais\Traits\ImportTrait;

	public $wc_product;

	public function __construct( \WC_Product $wc_product ) {
		$this->wc_product = $wc_product;
	}

	public function get_id() {
		return $this->wc_product->get_id();
	}
	public function get_acf_id() {
		return $this->get_id();
	}
	public static function get_essential_import_args() {
		return ['post_title', 'slug'];
	}
	public static function find_existing_index( $data ) {
		$slug = $data['slug'];
		$existing_index = get_page_by_path( $slug, OBJECT, 'product' );
		if ( $existing_index ) {
			$post_id = $existing_index->ID;
			return new static( wc_get_product( $post_id ) );
		}
		return false;
	}
	public static function insert_args() {
		return [
			'post_status'  => 'publish',
			'post_content' => '',
			'post_title'   => '',
			'post_type'    => 'product',
			'post_excerpt' => ''
		];
	}
	public static function insert( $args ) {
		$post_id = wp_insert_post( $args, true );

		if ( is_wp_error( $post_id ) ) {
			throw new \Exception( "Failed to insert:\n" . av_wp_error_message( $post_id ) );
		} else {
			av_import_admin_notice( "Product added with ID: $post_id" );
		}

		wp_set_object_terms( $post_id, 'simple', 'product_type' );

		return new static( wc_get_product( $post_id ) );
	}

	public function export_json( $pretty = true ) {
		if ( $pretty ) {
			return json_encode( $this->export_array(), JSON_PRETTY_PRINT );
		} else {
			return json_encode( $this->export_array() );
		}
	}
	public function export_array() {
		$array   = [
			'slug'         => $this->wc_product->get_slug(),
			'post_title'   => $this->wc_product->get_name(),
			'post_content' => $this->wc_product->get_description(),
			'post_excerpt' => $this->wc_product->get_short_description(),
		];

		$array['categories'] = array_map(
			function( $cat ) {
				return $cat->slug;
			},
			get_the_terms( $this->get_id(), 'product_cat' )
		);

		$array['metadata'] = [
			'_price'  => $this->wc_product->get_price( false ),
			'_length' => $this->wc_product->get_length( false ),
			'_width'  => $this->wc_product->get_width( false ),
			'_height' => $this->wc_product->get_height( false ),
			'_weight' => $this->wc_product->get_weight( false ),
		];

		return $array;

	}

}
