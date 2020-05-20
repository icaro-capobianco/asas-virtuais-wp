<?php
namespace AsasVirtuaisWP\Traits;

trait CustomFieldsTrait {

	protected $acf = [];

    abstract function get_acf_id();

    public function get_field( $field_name, $fallback = 'fallback', $format = true, $updated = false ) {
		if ( ! isset( $this->acf[ $field_name ] ) && ! $updated ) {
			$field_value = get_field( $field_name, $this->get_acf_id(), $format );
			if ( ! $field_value && $fallback !== 'fallback' ) {
				$field_value = $fallback;
			}
			$this->acf[ $field_name ] = $field_value;
        }
		return $this->acf[ $field_name ];
    }

}
