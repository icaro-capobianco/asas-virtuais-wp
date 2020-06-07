<?php

function av_sanitize_title_with_underscores( $title ) {
	return str_replace( '-', '_', sanitize_title( $title ) );
}
