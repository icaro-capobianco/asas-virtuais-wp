<?php
namespace AsasVirtuaisWP\Traits;

trait CustomFieldsTrait {

	protected $acf = [];

    abstract function get_acf_id();

	public function get_field( string $field_name, $fallback = null, $formatted = true ) {
		return av_acf_get_field( $field_name, $this->get_acf_id(), $formatted, $fallback );
	}

}
