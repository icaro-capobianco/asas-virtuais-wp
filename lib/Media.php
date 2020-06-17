<?php
if ( ! function_exists( 'av_attachment_index' ) ) {
	function av_attachment_index( $filename ) {
		global $wpdb;
		$title   = sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) );
		$query   = "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND ( post_title LIKE '%$title%' OR guid LIKE '%$filename'  )";
		$results = $wpdb->get_results( $query );
		if ( ! empty( $results ) ) {
			return $results[0];
		}
		return false;
	}
}
