<?php

function av_import_admin_notice( $message ) {
	add_action( 'admin_notices', function() use( $message ) {
		?>
		<div class="notice notice-info">
			<p><?= $message ?></p>
		</div>
		<?php
	} );
}
