<?php

namespace AsasVirtuaisWP\Models;

abstract class AbstractPost {

	use \AsasVirtuaisWP\Traits\CustomFieldsTrait;
	use \AsasVirtuaisWP\Traits\ImportTrait;

	protected $post_object;

	public function __construct( $data ) {
		if ( is_numeric( $data ) ) {
			$this->post_object = get_post( $data );
		} elseif ( $data instanceof \WP_Post ) {
			$this->post_object = $data;
		} else {
			throw new \Exception( 'WP_Post or Post ID expected, received: ' . var_export( $data, true ) );
		}
	}

	// Implementing abstract methods from traits
		/**
		 * @param string $key
		 * @param mixed $value
		 * @return bool
		 */
		public function update_meta( $key, $value ) {
			$result = update_post_meta( $this->get_id(), $key, $value );
			if ( $result !== false ) {
				return true;
			}
			$meta = get_post_meta( $this->get_id(), $key, true );
			if ( $value === $meta || json_encode( $value ) === json_encode( $meta ) ) {
				return true;
			}
			return false;
		}
		public static function essential_import_args() {
			return [ 'insert_data' => [ 'post_title' ] ];
		}
		public static function find_existing_index( $data ) {
			$post_title = $data['insert_data']['post_title'];
			$slug = sanitize_title( $post_title );
			$existing_index = av_get_post_by_slug( $slug, [
				'post_type' => static::post_type(),
			] );
			if ( $existing_index ) {
				return new static( $existing_index );
			}
			return false;
		}
		public static function insert_args() {
			return [
				'post_status'  => 'draft',
				'post_type'    => static::post_type(),
			];
		}
		public static function insert( $args ) {
			$post_id = wp_insert_post( $args, true );

			if ( is_wp_error( $post_id ) ) {
				throw new \Exception( "Failed to insert:\n" . av_wp_error_message( $post_id ) );
			} else {
				asas_virtuais('gsg-wc-evosus')->import_manager()->import_notice( 'Post of type ' . static::post_type() . " added with ID: $post_id" );
			}
	
			return new static( get_post( $post_id ) );
		}

	// Getters
		public function get_id() {
			return $this->post_object->ID;
		}
		public function get_acf_id() {
			return $this->get_id();
		}
		public function get_type() {
			return $this->post_object->post_type;
		}
		public function get_author() {
			return $this->post_object->post_author;
		}
		public function get_content() {
			return $this->post_object->post_content;
		}
		public function get_title() {
			return $this->post_object->post_title;
		}
		public function get_excerpt() {
			return $this->post_object->post_excerpt;
		}
		public function get_status() {
			return $this->post_object->post_status;
		}
		public function get_comment_status() {
			return $this->post_object->comment_status;
		}
		public function get_ping_status() {
			return $this->post_object->ping_status;
		}
		public function get_password() {
			return $this->post_object->post_password;
		}
		public function get_name() {
			return $this->post_object->post_name;
		}
		public function get_to_ping() {
			return $this->post_object->to_ping;
		}
		public function get_pinged() {
			return $this->post_object->pinged;
		}
		public function get_parent() {
			return $this->post_object->post_parent;
		}
		public function get_guid() {
			return $this->post_object->guid;
		}
		public function get_menu_order() {
			return $this->post_object->menu_order;
		}
		public function get_meta( $key, $fallback = null ) {
			$result = get_post_meta( $this->get_id(), $key, true );
			if ( $result === null && $fallback !== null ) {
				return $fallback;
			}
			return $result;
		}

	// Abstract methods
		abstract static function post_type();
	// Exandable methods
		public function export_array() {
			$array = [
				'insert_data' => [
					'slug' => $this->get_name(),
					'post_title' => $this->get_title(),
					'post_content' => $this->get_content(),
					'post_excerpt' => $this->get_excerpt(),
				],
				'taxonomies' => get_taxonomies( [ 'object_type' => static::post_type() ] )	
			];

			return $array;
		}
		public function export_json( $pretty = true ) {
			if ( $pretty ) {
				return json_encode( $this->export_array(), JSON_PRETTY_PRINT );
			} else {
				return json_encode( $this->export_array() );
			}
		}
		/**
		 * Query DB for posts of this type and retrieves array of static instances
		 * @param array $args get_posts args
		 * @return static instance
		 */
		public static function query( $args = [] ) {
			$defualts = [
				'post_type' => static::post_type(),
				'posts_per_page' => -1
			];

			return array_map( function( $post ) {
				return new static ( $post );
			}, get_posts( array_replace( $defualts, $args ) ) );
		}


}
