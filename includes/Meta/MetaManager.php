<?php

namespace AsasVirtuaisWP\Meta;

class MetaManager {

	public $meta_boxes = [];

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
	}

	public function add_meta_boxes() {
		foreach ( $this->meta_boxes as $box_id => $meta_box ) {
			add_meta_box( $box_id, $meta_box->title, $meta_box->callback, $meta_box->screen, $meta_box->context, $meta_box->priority, $meta_box->cb_args );
		}
	}

	public function add_meta_box( $title, $screen, $callback, $args = [] ) {
		$box_id = sanitize_title( $title );
		$true_args = [
			'title'    => $title,
			'callback' => $callback,
			'screen'   => $screen,
			'context'  => $args['context'] ?? 'normal',
			'priority' => $args['priority'] ?? 'low',
			'cb_args'  => $args['cb_args'] ?? []
		];
		$this->meta_boxes[ $box_id ] = (object) $true_args;
	}
}
