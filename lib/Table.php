<?php

if ( ! function_exists( 'av_output_table' ) ) {
	function av_output_table( $data ) {
		if ( is_scalar( $data ) ) {
			echo "<table><tr><td><pre>$data</pre></td></tr></table>";
		} elseif( is_object( $data ) ) {
			echo "<table><tr><td>" . var_export( $data, true ) . "</td></tr></table>";
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
							<td><pre><?= $value ?></pre></td>
						<?php else: ?>
							<td><pre><?php print_r( $value ) ?></pre></td>
						<?php endif; ?>
					<?php endforeach; ?>
					</tr>
				</table>
			<?php
		}
	}
}
