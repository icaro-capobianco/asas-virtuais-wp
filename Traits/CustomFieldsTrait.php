<?php
namespace AsasVirtuaisWP\Traits;

trait CustomFieldsTrait {

	protected $acf = [];

    abstract function get_acf_id();

	public function get_field( string $field_name, $fallback = -999, $format = true ) {
		if ( ! isset( $this->acf[ $field_name ] ) ) {
			$field_value = get_field( $field_name, $this->get_acf_id(), $format );
			if ( ! $field_value && $fallback !== -999 ) {
				$field_value = $fallback;
			}
			$this->acf[ $field_name ] = $field_value;
		}
		return $this->acf[ $field_name ];
	}

}
