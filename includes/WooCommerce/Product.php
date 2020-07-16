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
