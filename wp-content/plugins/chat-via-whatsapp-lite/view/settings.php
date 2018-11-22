<?php

/**
 * Controller: settings.php
 */

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

$box_position = '' === WPTWA_Utils::getSetting( 'box_position' ) ? 'right' : WPTWA_Utils::getSetting( 'box_position' );

?>
<div class="wrap">
	<h1><?php esc_html_e( 'WhatsApp Click to Chat Settings', 'wptwa' ); ?></h1>
	
	<?php settings_errors(); ?>
	
	<form action="" method="post" novalidate="novalidate">
		
		<h2><?php esc_html_e( 'Display Settings', 'wptwa' ); ?></h2>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="toggle_text"><?php esc_html_e( 'Toggle Text', 'wptwa' ); ?></label></th>
					<td>
						<input name="toggle_text" type="text" id="toggle_text" class="regular-text" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'toggle_text' ) ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="description"><?php esc_html_e( 'Description', 'wptwa' ); ?></label></th>
					<td>
						<textarea name="description" id="description" cols="30" rows="3" class="regular-text"><?php echo stripslashes( WPTWA_Utils::getSetting( 'description' ) ); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="box_position"><?php esc_html_e( 'Box Position', 'wptwa' ); ?></label></th>
					<td>
						<p><input type="radio" name="box_position" value="left" id="box_position_left" <?php echo 'left' === $box_position ? 'checked' : ''; ?> /> <label for="box_position_left"><?php esc_html_e( 'Bottom Left', 'wptwa' ); ?></label></p>
						<p><input type="radio" name="box_position" value="right" id="box_position_right" <?php echo 'right' === $box_position ? 'checked' : ''; ?> /> <label for="box_position_right"><?php esc_html_e( 'Bottom Right', 'wptwa' ); ?></label></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for=""><?php esc_html_e( 'Display based on screen width', 'wptwa' ); ?></label></th>
					<td>
						<p><input type="checkbox" name="hide_on_large_screen" value="on" id="hide_on_large_screen" <?php echo 'on' === WPTWA_Utils::getSetting( 'hide_on_large_screen' ) ? 'checked' : ''; ?> /> <label for="hide_on_large_screen"><?php esc_html_e( 'Hide on large screen (wider than 782px)', 'wptwa' ); ?></label></p>
						<p><input type="checkbox" name="hide_on_small_screen" value="on" id="hide_on_small_screen" <?php echo 'on' === WPTWA_Utils::getSetting( 'hide_on_small_screen' ) ? 'checked' : ''; ?> /> <label for="hide_on_small_screen"><?php esc_html_e( 'Hide on small screen (narrower than 783px)', 'wptwa' ); ?></label></p>
					</td>
				</tr>
			</tbody>
		</table>
		
		<h2><?php esc_html_e( 'WhatsApp Accounts', 'wptwa' ); ?></h2>
		<div class="wptwa-account-items">
		
			<?php
			
			/* Loop through all accounts and display the HTML */
			
			$accounts = json_decode( WPTWA_Utils::getSetting( 'accounts' ), true );
			$accounts = is_array( $accounts ) && count( $accounts ) > 0 ? $accounts : array( array( 'number' => '', 'name' => '', 'title' => '' ) );
			foreach ( $accounts as $k => $v ) {
				
				$number = esc_attr( $v['number'] );
				$name = esc_attr( $v['name'] );
				$title = esc_attr( $v['title'] );
				
				WPTWA_Utils::displayAccounts( $k, $number, $name, $title );
			}
			
			?>
		
		</div>
		
		<?php wp_nonce_field( 'wptwa_settings_form', 'wptwa_settings_form_nonce' ); ?>
		<input type="hidden" name="wptwa_settings" value="submit" />
		<input type="hidden" name="submit" value="submit" />
		<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'wptwa' ); ?>"></p>
		
	</form>
</div>