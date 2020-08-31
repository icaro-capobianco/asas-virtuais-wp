<?php

namespace AsasVirtuaisWP\WooCommerce;

class Product extends \AsasVirtuaisWP\Models\AbstractPost {

	use \AsasVirtuaisWP\Traits\ImportTrait;

	public $wc_product;

	public function __construct( $data ) {
		if ( $data instanceof \WC_Product ) {
			$this->wc_product = $data;
			$post = get_post( $this->wc_product->get_id() );
			parent::__construct( $post );
		} else {
			parent::__construct( $data );
			$this->wc_product = wc_get_product( $data );
		}
	}

	public static function post_type() {
		return 'product';
	}

	public function add_attributes( $attribute_name, $attribute_terms ) {
		if ( is_string( $attribute_terms ) ) {
			$attribute_terms = [ $attribute_terms ];
		}
		$taxonomy = "pa_" . sanitize_title( $attribute_name );
		$result = wp_set_object_terms( $this->get_id(), $attribute_terms, $taxonomy, true );

		$product_attributes = get_post_meta( $this->get_id(), '_product_attributes', true );
		if ( ! $product_attributes ) {
			$product_attributes = [];
		}

		foreach ( $attribute_terms as $term_name ) {
			$product_attributes[$taxonomy] = [
				'name'         => $taxonomy, 
				'value'        => $term_name,
				'is_visible'   => '1',
				'is_taxonomy'  => '1'
			];
		}

		update_post_meta( $this->get_id(),'_product_attributes', $product_attributes );
	}

	public function export_array() {
		$array = parent::export_array();

		$array['metadata'] = [
			'_sku'    => $this->wc_product->get_sku( false ),
			'_price'  => $this->wc_product->get_price( false ),
			'_length' => $this->wc_product->get_length( false ),
			'_width'  => $this->wc_product->get_width( false ),
			'_height' => $this->wc_product->get_height( false ),
			'_weight' => $this->wc_product->get_weight( false ),
		];

		return $array;

	}

}
