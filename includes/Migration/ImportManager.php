<?php

namespace AsasVirtuaisWP\Migration;

class ImportManager {

	private $importable_objects = [];
	private $token = false;

	public function __construct( $framework_instance, $token ) {

		add_action( 'init', function() {
			$this->setup_tokens_page();
		} );
		$this->framework_instance = $framework_instance;

		if ( $token ) {
			$this->set_token( $token );
			$this->register_import_endpoint();
		}

	}

	public function register_import_endpoint( $token = false ) {

		if ( ! $token ) {
			$token = $this->token;
		}

		if ( ! $token ) {
			return;
		}

		if ( ! isset( $this->framework_instance->rest_manager ) ) {
			throw new \Exception('Must instantiate rest_manager before import_manager');
		}

		$this->framework_instance->rest_manager()->add_endpoint( 'import/(?P<object_type>[a-zA-Z-_]+)', [
			'methods' => [ 'POST', 'OPTIONS' ],
			'callback' => [ $this, 'route_callback' ],
		] );
	}

	private $plugins_with_tokens_list = [];
	public function register_plugin_token( $name = false ) {
		if ( ! $name ) {
			$name = $this->framework_instance->plugin_name;
			$slug = $this->framework_instance->plugin_slug;
		} else {
			$slug = sanitize_title( $name );
		}
		$this->plugins_with_tokens_list[$slug] = $name;
	}

	public function setup_tokens_page() {
		$token_fields = [];
		foreach ( $this->plugins_with_tokens_list as $slug => $name ) {
			$token_fields[] = av_acf_text_field( "$name Token" );
		}

		if ( ! did_action( 'asas/loaded_tokens_page' ) ) {
			asas_virtuais()->acf_manager()->settings_page( 'Plugins Token Settings' );
			asas_virtuais()->acf_manager()->add_field_group( av_acf_field_group( 
				'Plugin Tokens',
				[ [ av_acf_location( 'options_page', 'acf-options-plugins-token-settings' ) ] ],
				$token_fields,
			) );
			asas_virtuais()->hook_manager()->add_filter( 'acf/load_field/name=plugins_tokens', [ $this, 'register_plugin_token_fields' ] );
			do_action( 'asas/loaded_tokens_page' );
		}
	}

	public function get_token( $name = false ) {
		if ( ! $name ) {
			$name = $this->framework_instance->plugin_name;
		}
		$field_name = av_acf_field_name( "$name Token" );
		return av_acf_get_field( $field_name, 'options' );
	}

	public function set_token( $token ) {
		$this->token = $token;
	}

	public function route_callback( $request ) {
		$this->authenticate_request_or_die( $request );
		$object_type = $request['object_type'] ?? false;
		if ( $object_type ) {
			$json = $request->get_body();
			$result = $this->import_json( $json, $object_type );
			return [
				'notices' => array_merge( asas_virtuais()->import_manager()->notices, $this->notices ),
				'errors' => array_merge( asas_virtuais()->import_manager()->errors, $this->errors ),
				'result' => $result ?? false,
			];
		} else {
			return ['errors' => [ 'invalid-import' ] ];
		}
	}

	public function authenticate_request_or_die( $request ) {
		$authorization = $request->get_header('Authorization');
		if( $authorization ) {
			$token = explode( ' ', $authorization )[1] ?? false;
			if( $token ) {
				$saved_token = $this->token;
				if( $saved_token && $saved_token === $token ) {
					return true;
				}
			}
		}
		http_response_code(403);
		die(403);
	}

	public function register_importable( $class ) {
		$identifier = sanitize_title( ( new \ReflectionClass( $class ) )->getShortName() );
		$this->importable_objects[ $identifier ] = $class;
	}

	private function import_json( $json, $object_type ) {

		try {

			$parsed = av_parse_json_or_throw( $json, true );
			$class = $this->importable_objects[ $object_type ] ?? false;
			if ( $class ) {
				$imported_object = $class::import( $parsed );
			} else {
				throw new \Exception( "$object_type is not a valid object to import" );
			}

	
			if ( ! $imported_object ) {
				$this->import_error( 'No imported object returned' );
			}

			return [ 'input' => $parsed, 'result' => $imported_object, 'errors' => $this->errors, 'messages' => $this->messages ];

	
		} catch ( \Throwable $th ) {
			$this->import_exception( $th );
		}
	
	}

	public $errors = [];
	public function import_error( $error ) {
		asas_virtuais()->admin_manager()->admin_error( $error );
		$this->errors[] = $error;
	}
	public $notices = [];
	public function import_notice( $notice ) {
		asas_virtuais()->admin_manager()->admin_notice( $notice );
		$this->notices[] = $notice;
	}
	public function import_exception( \Throwable $th, $additional = false ) {
		$message = av_get_error_details( $th );
		$this->import_error( $message );
	}


}
