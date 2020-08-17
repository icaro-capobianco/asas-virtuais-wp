<?php

if ( ! function_exists( 'av_output_table' ) ) {
	function av_output_table( $data, $id = '' ) {
		if ( is_scalar( $data ) ) {
			echo "<table id='$id' ><tr><td><pre>$data</pre></td></tr></table>";
		} elseif( is_object( $data ) ) {
			echo "<table id='$id' ><tr><td>" . var_export( $data, true ) . "</td></tr></table>";
		} else {
			?>
			<table id="<?= $id ?>" >
				<thead>
					<?php
					foreach( $data as $key => $value ) {
						if ( ! is_integer( $key ) ) {
							?>
							<th><?= $key ?></th>
							<?php
						} elseif ( ! is_scalar( $value ) ) {
							foreach( $value as $k => $val ) {
								?>
								<th><?= $k ?></th>
								<?php
							}
							break;
						}
					}
					?>
				</thead>
				<tbody>
					<?php
					foreach( $data as $key => $value ) {
						if ( is_integer( $key ) ) {
							av_output_table_row( $value );
						}
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}
}

if ( ! function_exists( 'av_output_table_row' ) ) {
	function av_output_table_row( $data ) {
		?>
		<tr>
			<?php foreach( $data as $key => $value ): ?>
				<?php av_output_table_data( $value ); ?>
			<?php endforeach; ?>
		</tr>
		<?php
	}
}

if ( ! function_exists( 'av_output_table_data' ) ) {
	function av_output_table_data( $data ) {
		$value = is_scalar( $data ) ? $data : print_r( $data, true );
		?>
		<td><?= $value ?></td>
		<?php
	}
}
