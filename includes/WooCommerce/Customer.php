<?php

namespace AsasVirtuaisWP\WooCommerce;

class Customer {

	public $wc_customer;
	public function __construct( \WC_Customer $wc_customer  ) {
		$this->wc_customer = $wc_customer;
	}
	public function get_email() {
		return $this->wc_customer->get_email();
	}

}
