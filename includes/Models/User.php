<?php

namespace AsasVirtuaisWP\Models;

class User {

	use \AsasVirtuaisWP\Traits\CustomFieldsTrait;
	use \AsasVirtuaisWP\Traits\ImportTrait;

	protected $wp_user;

	public function __construct( $wp_user = false ) {
		if ( ! $wp_user ) {
			$this->wp_user = wp_get_current_user();
		} else {
			$this->wp_user = $wp_user;
		}
	}

	public function get_id() {
		return $this->wp_user->ID;
	}
	public function get_acf_id() {
		return 'user_' . $this->get_id();
	}

	private $is_admin;
	public function is_admin() {
		if ( ! isset( $this->is_admin ) ) {
			$this->is_admin = user_can( $this->get_id(), 'administrator' );
		}
		return $this->is_admin;
	}

	public static function essential_import_args() {
		return [ 'insert_data' ];
	}
	public static function find_existing_index( $data ) {
		$email = $data['insert_data']['user_email'] ?? false;
		if ( $email ) {
			$user = get_user_by( 'email', $email );
			if ( $user ) {
				return new static( $user );
			}
		}
		return false;
	}
	public static function insert( $args ) {
		remove_action( 'register_new_user', 'wp_send_new_user_notifications' );

		$login = $args['user_login'] ?? $args['user_email'];
		$args['user_login'] = $login;

		$user_id = wp_insert_user( $args );

		if ( is_wp_error( $user_id ) ) {
			throw new \Exception( "Failed to insert user:\n" . av_wp_error_message( $user_id ) );
		} else {
			av_import_admin_notice( "User added with ID: $user_id" );
		}

		return new static( get_user_by( 'ID', $user_id ) );
	}
	public static function insert_args() {
		return [
			'user_pass'            => wp_generate_password(),
			'show_admin_bar_front' => 'false', // Yes this is a string literal...
			'role'                 => 'subscriber',
		];
	}

	public function update_meta( $key, $value ) {
		return update_user_meta( $this->get_id(), $key, $value );
	}

}
