<?php
if ( ! function_exists( 'av_attachment_index' ) ) {
	/** Finds index of attachment by filename
	 * @param string $filename
	 * @return \WP_Post|false
	 */
	function av_attachment_index( $filename ) {
		global $wpdb;
		$filename = sanitize_file_name ( $filename );
		$query   = "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND guid LIKE '%/$filename'";
		$results = $wpdb->get_results( $query );
		if ( ! empty( $results ) ) {
			return $results[0];
		}
		return false;
	}
}
if ( ! function_exists( 'av_insert_attachment_from_url' ) ) {
	/**	Downloads file from url and inserts attachment based on the path, returns attachment ID, checks for existing index
	 * @param string $url
	 * @param array $args
	 * @return integer
	 */
	function av_insert_attachment_from_url( $url, $args = [] ) {

		$existing_index = av_attachment_index( basename( $url ) );

		if ( $existing_index ) {

			$attach_id = $existing_index->ID;

			av_import_admin_notice( "Found existing index for image $attach_id from URL " . $url );

		} else {

			$upload = av_download_attachment_from_url( $url );

			$file_path = $upload['file'] ?? false;

			if( $file_path ) {
				$attach_id = av_insert_attachment_from_filepath( $file_path, $args );

				$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
		
				wp_update_attachment_metadata( $attach_id, $attach_data );
			} else {
				av_import_admin_error( "Failed to import attachment, no file attribute on av_download_attachment_from_url result" );
			}

		}

		return $attach_id;

	}
}
if ( ! function_exists( 'av_insert_attachment_from_filepath' ) ) {
	function av_insert_attachment_from_filepath( $file_path, $args = [] ) {
		$parent_post_id = $args['parent_post_id'] ?? null;
		$alt_text = $args['alt_text'] ?? false;

		if ( $alt_text ) {
			unset( $args['alt_text'] );
		}

		$file_name        = basename( $file_path );
		$file_type        = wp_check_filetype( $file_name, null );
		$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
		$wp_upload_dir    = wp_upload_dir();

		$post_args = [
			'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
			'post_mime_type' => $file_type['type'],
			'post_title'     => $attachment_title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		];

		array_replace( $post_args, $args );
		$post_info = $post_args;

		// Create the attachment
		$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );
		av_import_admin_notice( "Inserted attachment $attach_id for the file $file_path" );

		if ( $alt_text ) {
			update_post_meta( $attach_id, '_wp_attachment_image_alt', $alt_text );
		}

		// Include image.php
		require_once ABSPATH . 'wp-admin/includes/image.php';

		return $attach_id;

	}
}
if ( ! function_exists( 'av_insert_image_from_array' ) ) {
	function av_insert_image_from_array( $array, $parent_post_id = null ) {
		// Validate existance of necessary data
		if ( ! isset( $array[ 'url' ] ) ) {
			throw new \Exception( 'Missing url in media array ' . var_export( $array, true ) );
		}

		$overwrite = [];

		if ( isset( $array['title'] ) && ! empty( $array['title'] ) ) {
			$overwrite['post_title'] = $array['title'];
		}

		if ( isset( $array['caption'] ) && ! empty( $array['caption'] ) ) {
			$overwrite['post_excerpt'] = $array['caption'];
		}

		if ( isset( $array['alt_text'] ) && ! empty( $array['alt_text'] ) ) {
			$overwrite['alt_text'] = $array['alt_text'];
		}

		$overwrite['parent_post_id'] = $parent_post_id;

		return av_insert_attachment_from_url( $array['url'], $overwrite );

	}
}
if ( ! function_exists( 'av_download_attachment_from_url' ) ) {
	function av_download_attachment_from_url( $url ) {
		if ( ! class_exists( 'WP_Http' ) ) {
			require_once ABSPATH . WPINC . '/class-http.php';
		}

		$http     = new WP_Http();
		$response = $http->request( $url );
		if ( $response instanceof \WP_Error ) {
			throw new \Exception( 'Image request WP Error:' . var_export( $response, true ) );
		} else {
			if ( isset( $response['response'] ) && isset( $response['response']['code'] ) ) {
				if ( $response['response']['code'] != 200 ) {
					throw new \Exception( 'Image request error code: ' . $response['response']['code'] );
				}
			}
		}

		$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
		if ( ! empty( $upload['error'] ) ) {
			throw new \Exception( 'Image request upload error: ' . var_export( $upload, true ) );
		}

		av_import_admin_notice( 'Image imported from URL ' . $url );

		return $upload;
	}
}
if ( ! function_exists( 'av_import_media_from_array' ) ) {
	function av_import_media_from_array( $array ) {

		try {

			$attach_id = av_insert_image_from_array( $array );
			if ( ! $attach_id ) {
				throw new \Exception( 'Failed to import image:' . var_export( $array, true ) );
			}
			return $attach_id;

		} catch ( \Throwable $th ) {
			av_import_admin_exception( $th );
		}

	}
}
