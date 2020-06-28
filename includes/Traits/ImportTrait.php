<?php
namespace AsasVirtuaisWP\Traits;

trait ImportTrait {

	# Abstract Methods
		/**
		 * Undocumented function
		 * @return string Object ID
		 */
		abstract function get_id();
		/**
		 * Undocumented function
		 * @return string ACF ID
		 */
		abstract function get_acf_id();
		/**
		 * Undocumented function
		 * @see import
		 * @return array Array of essential args on self::import data param
		 */
		abstract static function essential_import_args();
		/**
		 * Undocumented function
		 * @param mixed $data import data
		 * @return void
		 */
		abstract static function find_existing_index( $data );
		/**
		 * Inserts the object in the DV
		 * @param mixed $args to insert the Object in the database
		 * @return void
		 */
		abstract static function insert( $args );
		/**
		 * Returns key and value pairs, key being the property to look for in the import data to send to 
		 * the insert method, and the value being the default value for that property
		 * @see insert
		 * @return mixed
		 */
		abstract static function insert_args();
	# Import Logic
		/**
		 * Runs the import logic
		 * 1 - Check for existing index
		 * 2 - Import Metadata
		 * 3 - Import ACF
		 * 4 - Import Taxonomies
		 * 5 - Return static instance
		 * @param mixed $data
		 * @return StaticInstance
		 */
		public static function import( $data ) {
			static::validate_import_data( $data );
			$existing_index = static::find_existing_index( $data );
			if ( $existing_index ) {
				$static = $existing_index;
			} else {
				$static = static::insert_from_import( $data );
			}

			if( isset( $data['metadata'] ) ) {
				$static->import_metadata( $data['metadata'] );
			}
			if( isset( $data['acf'] ) ) {
				$static->import_metadata( $data['acf'], true );
			}
			if( isset( $data['terms'] ) && is_array( $data['terms'] ) ) {
				$static->import_taxonomies( $data['terms'] );
			}

			return $static;
		}
		/**
		 * 1 - Finds the args that are going to be used in the insert method
		 * 2 - Inserts the object in the DB or Throws an exception
		 * 3 - Returns a static instance of the object
		 * @uses self::insert_args
		 * @uses self::insert
		 * @uses self::validate_import_data
		 * @throws \Exception
		 * @param mixed $data
		 * @param boolean $validate To validate the $data or not
		 * @return StaticInstance
		 */
		public static function insert_from_import( $data, $validate = false ) {

			if ( $validate !== false ) {
				static::validate_import_data( $data );
			}

			$args = [];
			foreach ( static::insert_args() as $arg_key => $default_value ) {
				$args[ $arg_key ] = $data[ $arg_key ] ?? $default_value;
			}
			$result = static::insert( $args );

			if ( ! $result ) {
				throw new \Exception( "Failed to insert: \n" . av_wp_error_message( $result ) );
			} else {
				av_import_admin_notice( "Imported:\n" . var_export( $args, true ) );
				if ( get_class( $result ) !== static::class ) {
					throw new Exception('Doing it wrong: static method insert from the Import Trait must return an instance of static class');
				}
			}

			return $result;
		}
		/**
		 * Imports Metadata
		 * @param mixed $data
		 * @param boolean $acf to use ACF or not
		 * @return void
		 */
		public function import_metadata( $data, bool $acf = false ) {
			foreach ( $data as $key => $value ) {
				try {
					if ( $acf ) {
						$result = av_acf_import_field_data( $key, $value, $this->get_acf_id() );
						$identifier = $this->get_acf_id();
					} else {
						$result = $this->update_meta( $key, $value );
						$identifier = $this->get_id();
					}
					if ( $result ) {
						av_import_admin_notice( "Successfully imported meta $key to: $identifier with:\n" . var_export( $value, true ) );
					} else {
						throw new \Exception( "Failed to import meta $key to: $identifier with \n" . var_export( $value, true ) );
					}
				} catch (\Throwable $th) {
					// TODO handle exception
					av_import_admin_exception( $th );
				}
			}
		}
		/**
		 * Update meta
		 * @uses \update_post_meta
		 * @param string $key
		 * @param mixed $value
		 * @return boolean
		 */
		public function update_meta( $key, $value ) {
			return update_post_meta( $this->get_id(), $key, $value );
		}
		/**
		 * Sets object Taxonomies
		 * @param mixed $data key being the taxonomy and value being an array of terms
		 * @return void
		 */
		public function import_taxonomies( $data ) {
			foreach( $data as $taxonomy => $terms ) {
				$result = wp_set_object_terms( $this->get_id(), $terms, $taxonomy, true );
				if ( is_wp_error( $result ) ) {
					av_import_admin_error( "Could not insert taxonomy($taxonomy) terms " . var_export( $terms, true ) . "\nError:\n" . av_wp_error_message( $result ) );
				} else {
					av_import_admin_notice( "Taxonomy($taxonomy) terms " . var_export( $terms, true ) . " successfully added to " . $this->get_id() );
				}
			}
		}
		/**
		 * Validates the data used in import methods
		 * @param array $data import data
		 * @uses self::essential_import_args
		 * @throws \Exception
		 * @return void
		 */
		protected static function validate_import_data( array $data ) {
			foreach ( static::essential_import_args() as $arg ) {
				if ( ( ! isset( $data[ $arg ] ) ) || ( empty( $data[ $arg ] ) ) ) {
					throw new \Exception( "Missing or empty essential import $arg in import: \n" . var_export( $data, true ) );
				}
			}
		}

}
