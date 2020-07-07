<?php

namespace AsasVirtuaisWP\WooCommerce;

class Order {

	public $wc_order;
	public function __construct( \WC_Order $wc_order  ) {
		$this->wc_order = $wc_order;
	}

	public $customer;
	public function get_customer( $throw = false ) {
		if ( ! $this->customer ) {
			$customer_id = $this->wc_order->get_customer_id();
			if ( $customer_id ) {
				$wc_customer = new \WC_Customer( $customer_id );
				if ( $wc_customer ) {
					$this->customer = new Customer( $wc_customer );
				}
			}
		}
		if ( $throw && ! $this->customer ) {
			throw new \Exception('No customer ID');
		}
		return $this->customer;
	}

	public function get_id() {
		return $this->wc_order->get_id();
	}
	public function get_email() {

		$email = false;

		$customer = $this->get_customer();

		if ( $customer ) {
			$email = $customer->get_email();
		} else {
			$email = $this->wc_order->get_billing_email();
		}

		if ( ! $email ) {
			throw new \Exception('No Customer or Billing email in Order: ' . $this->get_id() );
		}
		return $email;
	}

}
