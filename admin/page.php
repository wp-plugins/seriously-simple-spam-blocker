<?php

function sssb_menu() {
	if( ! current_user_can( 'manage_options' ) ) { return; }
	add_submenu_page( 'tools.php', 'Seriously Simple Spam Blocker', 'Seriously Simple Spam Blocker', 'manage_options', 'sssb', 'sssb_admin_page' );
}
add_action( 'admin_menu', 'sssb_menu' );

function sssb_valid_option( $option ) {

	$option = trim( stripslashes( $option ) );
	if( is_numeric( $option ) && ! preg_match( '/([^0-9])+/', $option ) ) {
		if( $option >= 0 && $option <= 100 )
			return true;
	}
	return false;
}

function sssb_admin_page() {

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

		check_admin_referer( 'update_sssb_settings', 'sssb_nonce' );
		$sssb_ip = ( isset( $_POST['sssb_ip'] ) ? $_POST['sssb_ip'] : false );
		$sssb_username = ( isset( $_POST['sssb_username'] ) ? $_POST['sssb_username'] : false );
		$sssb_email = ( isset( $_POST['sssb_email'] ) ? $_POST['sssb_email'] : false );

		if( false !== $sssb_ip && sssb_valid_option( $sssb_ip ) ) {
			update_option( 'sssb_confidence_ip', esc_sql( trim( stripslashes( $sssb_ip ) ) ) );
		}
		if( false !== $sssb_username && sssb_valid_option( $sssb_username ) ) {
			update_option( 'sssb_confidence_username', esc_sql( trim( stripslashes( $sssb_username ) ) ) );
		}
		if( false !== $sssb_email && sssb_valid_option( $sssb_email ) ) {
			update_option( 'sssb_confidence_email', esc_sql( trim( stripslashes( $sssb_email ) ) ) );
		}
	}

?>
	<h2>Spam Report</h2>
	<table>
		<tr>
			<th>Spam Attempts Blocked: </th>
			<td><?php echo get_option( 'sssb_blocked', 0 ); ?></td>
		</tr>
	</table>
	<h2>Spam Blocking Options</h2>
	<form action="" method="POST">
	<div>
	<label for="sssb_ip">IP Confidence</label>
	<input type="text" name="sssb_ip" placeholder="<?php echo get_option( 'sssb_confidence_ip', 75 ); ?>" />
	</div>
	<div>
	<label for="sssb_username">Username Confidence</label>
	<input type="text" name="sssb_username" placeholder="<?php echo get_option( 'sssb_confidence_username', 95 ); ?>" />
	</div>
	<div>
	<label for="sssb_email">Email Confidence</label>
	<input type="text" name="sssb_email" placeholder="<?php echo get_option( 'sssb_confidence_email', 75 ); ?>" />
	</div>
	<div>
	<?php wp_nonce_field( 'update_sssb_settings', 'sssb_nonce' ); ?>
	<input type="submit" value="Save Options" />
	</div>
	</form>
	
	<p>Note: A confidence level is the probability that a given parameter falls within a specified set of values. In other words a confidence level of 95 means you are at least 95% sure the input is spam. Raising the number is less strict, lowering the number is more strict.</p>

	<h2>Support</h2>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	<p>Help keep this plugin free!</p>

<?php
}
