<?php

namespace AsasVirtuaisWP\Migration;

class ImportManager {

	private $importable_objects = [];

	public function __construct( $framework_instance ) {

		$framework_instance->rest_manager()->add_endpoint( 'import/(?P<object_type>[a-zA-Z-_]+)', [
			'methods' => [ 'POST', 'OPTIONS' ],
			'callback' => [ $this, 'route_callback' ],
			'args' => [
				'object_type'
			]
		] );
	}

	public function route_callback( $request ) {
		$this->authenticate_request_or_die( $request );
		$object_type = $request['object_type'] ?? false;
		if ( $object_type ) {
			$json = $request->get_body();
			$result = $this->import_json( $json, $object_type );
			return [
				'notices' => $this->notices,
				'errors' => $this->errors,
				'result' => $result ?? false,
			];
		} else {
			return ['errors' => [ 'invalid-import' ] ];
		}
	}

	private function authenticate_request_or_die( $request ) {
		$authorization = $request->get_header('Authorization');
		if( $authorization ) {
			$token = explode( ' ', $authorization )[1] ?? false;
			if( $token ) {
				$saved_token = get_field( 'av_import_api_token', 'options' );
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
				throw new \Exception( "$type is not a valid object to import" );
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
	public function import_exception( $th, $additional = false ) {
		$message = "\nFile:" . $th->getFile() . "\nLine" . $th->getLine() . "\nMessage:" . $th->getMessage();
		if ( $additional ) {
			$message .= "\nAdditional input: " . $additional;
		}
		$this->import_error( $message );
	}


}
