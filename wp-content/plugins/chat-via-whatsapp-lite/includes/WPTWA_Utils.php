<?php

/**
 * This class is meant to bundle miscellaneous functionalities
 */

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class WPTWA_Utils {
	
	private static $stateOptionName = WPTWA_SETTINGS_NAME;
	private static $states = array();
	private static $view;
	private static $impressions = array();
	private static $itIsMobileDevice = null;
	
	/**
	 * Setting a vew file to use. This method is used in 
	 * controller files.
	 */
	public static function setView ( $view ) {
		self::$view = $view;
	}
	
	/**
	 * Getting the view file. Used in WPTWA_Menu_Link().
	 */
	public static function getView () {
		
		$view = self::$view;
		
		$path_to_view = WPTWA_PLUGIN_DIR . 'view/' . $view . '.php';
		
		if ( file_exists( $path_to_view ) ) {
			include_once( $path_to_view );
		}
		else {
			if ( ! self::$view ) {
				echo '<p style="color: red;">' . esc_html__( 'Something is wrong: The view is not set yet. Please contact the developer.', 'wptwa' ) . '</p>';
			}
			else {
				echo '<p style="color: red;">' . esc_html__( 'Something is wrong: The view not found. Please contact the developer.', 'wptwa' ) . '</p>';
			}
		}
		
	}
	
	/**
	 * Used only once during plugin activation. Making sure that 
	 * we have the option.
	 */
	public static function prepeareSettings () {
		add_option( self::$stateOptionName );
	}
	
	public static function updateSetting ( $key, $value ) {
		$option = get_option( self::$stateOptionName );
		$data = array();
		
		if ( $option ) {
			$data = json_decode( $option, true );
		}
		$data[ $key ] = $value;
		
		update_option( self::$stateOptionName, json_encode( $data ), true );
	}
	
	public static function getSetting ( $key, $default = '' ) {
		$option = get_option( self::$stateOptionName );
		$data = json_decode( $option, true );
		if ( $data && isset( $data[ $key ] ) ) {
			return stripslashes( $data[ $key ] );
		}
		return $default;
	}
	
	public static function generateCustomCSS () {
		$css = '';
	
		if ( 'on' === WPTWA_Utils::getSetting( 'hide_on_large_screen' ) ) {
			$css.= '@media screen and (min-width : 783px) {
				.wptwa-container {
					display: none;
				}
			}';
		}
		
		if ( 'on' === WPTWA_Utils::getSetting( 'hide_on_small_screen' ) ) {
			$css.= '@media screen and (max-width : 782px) {
				.wptwa-container {
					display: none;
				}
			}';
		}
		
		$css_file = WPTWA_PLUGIN_DIR . 'assets/css/auto-generated-wptwa.css';
		file_put_contents( $css_file, trim( $css ) );
	}
	
	public static function displayAccounts ( $id = '', $number = '', $name = '', $title = '' ) {
		$id = '' !== $id ? filter_var( $id, FILTER_SANITIZE_NUMBER_INT ) : '#id#';
		$number = '' !== $id ? sanitize_text_field( $number ) : '';
		$name = '' !== $id ? sanitize_text_field( $name ) : '';
		$title = '' !== $id ? sanitize_text_field( $title ) : '';
		
		?>
		<table class="form-table wptwa-account-item">
			<tbody>
				<tr>
					<th scope="row"><label for="account[<?php echo $id; ?>][number]"><?php esc_html_e( 'WhatsApp Number or Group Chat URL', 'wptwa' ); ?></label></th>
					<td>
						<input type="text" id="account[<?php echo $id; ?>][number]" name="account[<?php echo $id; ?>][number]" value="<?php echo $number; ?>" class="regular-text" />
						<p class="description"><?php printf( esc_html__( 'This field is required. Either a WhatsApp number or a Group Chat URL. If it is a WhatsApp number, it should be in international format. Refer to %s for a detailed explanation.', 'wptwa' ), '<br/><a href="https://faq.whatsapp.com/en/general/21016748" target="_blank">https://faq.whatsapp.com/en/general/21016748</a>' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="account[<?php echo $id; ?>][name]"><?php esc_html_e( 'Name', 'wptwa' ); ?></label></th>
					<td>
						<input type="text" id="account[<?php echo $id; ?>][name]" name="account[<?php echo $id; ?>][name]" value="<?php echo $name; ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Name is also required. If left blank, this account will not be saved.', 'wptwa' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="account[<?php echo $id; ?>][title]"><?php esc_html_e( 'Title', 'wptwa' ); ?></label></th>
					<td>
						<input type="text" id="account[<?php echo $id; ?>][title]" name="account[<?php echo $id; ?>][title]" value="<?php echo $title; ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Title will be displayed above Name.', 'wptwa' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	
}

?>