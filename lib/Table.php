<?php

if ( ! function_exists( 'av_output_table' ) ) {
	function av_output_table( $data ) {
		if ( is_scalar( $data ) ) {
			echo "<table><tr><td>$data</td></tr></table>";
		} else {
			?>
				<table>
					<tr>
					<?php foreach( array_keys( $data ) as $key ): ?>
						<th> <?= $key ?> </th>
					<?php endforeach; ?>
					</tr>
					<tr>
					<?php foreach( $data as $key => $value ): ?>
						<?php if( is_scalar( $value ) ): ?>
							<td><?= $value ?></td>
						<?php else: ?>
							<td><?php print_r( $value ) ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
					</tr>
				</table>
			<?php
		}
	}
}
