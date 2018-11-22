<?php

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( isset( $_POST['wptwa_settings'] ) ) {
	
	$legit = true;
	
	/* Check if our nonce is set. */
	if ( ! isset( $_POST['wptwa_settings_form_nonce'] ) ) {
		$legit = false;
	}
	
	$nonce = $_POST['wptwa_settings_form_nonce'];
	
	/* Verify that the nonce is valid. */
	if ( ! wp_verify_nonce( $nonce, 'wptwa_settings_form' ) ) {
		$legit = false;
	}
	
	/* 	Something is wrong with the nonce. Redirect it to the 
		settings page without processing any data.
		*/
	if ( ! $legit ) {
		wp_redirect( add_query_arg() );
	}
	
	/* Display and load-in settings */
	$toggle_text = isset( $_POST['toggle_text'] ) ? sanitize_text_field( trim( $_POST['toggle_text'] ) ) : '';
	$description = isset( $_POST['description'] ) ? sanitize_textarea_field( trim( $_POST['description'] ) ) : '';
	$box_position = isset( $_POST['box_position'] ) ? sanitize_text_field( trim( $_POST['box_position'] ) ) : '';
	$hide_on_large_screen = isset( $_POST['hide_on_large_screen'] ) ? 'on' : 'off';
	$hide_on_small_screen = isset( $_POST['hide_on_small_screen'] ) ? 'on' : 'off';
	
	WPTWA_Utils::updateSetting( 'toggle_text', $toggle_text );
	WPTWA_Utils::updateSetting( 'description', $description );
	WPTWA_Utils::updateSetting( 'box_position', $box_position );
	
	WPTWA_Utils::updateSetting( 'hide_on_large_screen', $hide_on_large_screen );
	WPTWA_Utils::updateSetting( 'hide_on_small_screen', $hide_on_small_screen );
	
	/* WhatsApp accounts */
	if ( isset( $_POST['account'] ) ) {
		$acc = array();
		$i = 1;
		foreach ( $_POST['account'] as $k => $v ) {
			
			if ( '' === trim( $v['number'] ) || '' === trim( $v['name'] ) ) {
				continue;
			}
			
			$acc[ $i ][ 'number' ] = sanitize_text_field( $v['number'] ); /* <- this is a phone number which could have + or ( or ) symbols. */
			$acc[ $i ][ 'name' ] = sanitize_text_field( htmlentities( stripslashes( $v['name'] ) ) );
			$acc[ $i ][ 'title' ] = sanitize_text_field( htmlentities( stripslashes( $v['title'] ) ) );
			$i++;
		}
		WPTWA_Utils::updateSetting( 'accounts', json_encode( $acc ) );
	}
	else {
		WPTWA_Utils::updateSetting( 'accounts', json_encode( array() ) );
	}
	
	/* Recreate CSS file */
	WPTWA_Utils::generateCustomCSS();
	
	add_settings_error( 'wptwa-settings', 'wptwa-settings', __( 'Settings saved', 'wptwa' ), 'updated' );
	
}

WPTWA_Utils::setView( 'settings' );

?>