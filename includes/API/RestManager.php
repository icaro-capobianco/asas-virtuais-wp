<?php

namespace AsasVirtuaisWP\API;

class RestManager {

	public $routes = [];
	public $route_namespace;

	public function __construct( string $route_namespace ) {
		$this->route_namespace = $route_namespace;
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		foreach( $this->routes as $endpoint => $args ) {
			register_rest_route( $this->route_namespace, "/$endpoint", $args );
		}
	}

	public function add_endpoint( string $endpoint, $args ) {
		$this->routes[ $endpoint ] = $args;
	}

}
