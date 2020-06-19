<?php

namespace AsasVirtuaisWP\Admin;

class AdminManager {

	public $notices = [];

	public function __construct() {

		add_action( 'admin_notices', [ $this, 'display_admin_notices' ] );

	}

	public function admin_notice( $message, $type = 'info', $dismissible = false ) {
		$class = $dismissible ? 'is-dismissible ' : '';
		$class .= 'notice-'.$type;
		$this->notices[] = (object) compact( 'message', 'class' );
	}

	public function admin_error( $message, $dismissible = false ) {
		$this->admin_notice( $message, 'error', $dismissible );
	}

	public function admin_warning( $message, $dismissible = false ) {
		$this->admin_notice( $message, 'warning', $dismissible );
	}

	public function admin_success( $message, $dismissible = false ) {
		$this->admin_notice( $message, 'success', $dismissible );
	}

	public function display_admin_notices() {
		foreach ( $this->notices as $notice ) {
			echo "<div class='notice $notice->class'><p>$notice->message</p></div>";
		}
	}

}
