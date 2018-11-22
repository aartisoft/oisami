<?php
/**
 *
 * The template part for displaying the dashboard menu
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */

global $current_user, $wp_roles, $userdata, $post;

$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
$user_identity 	 = $current_user->ID;
$bk_settings	 = listingo_get_booking_settings();

$url_identity = $user_identity;
if (isset($_GET['identity']) && !empty($_GET['identity'])) {
	$url_identity = $_GET['identity'];
}

$dir_profile_page = '';
$insight_page = '';
if (function_exists('fw_get_db_settings_option')) {
	$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
	$insight_page = fw_get_db_settings_option('insight_page', $default_value = null);
}

$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
$provider_category = listingo_get_provider_category($user_identity);

if( isset( $insight_page ) && $insight_page === 'enable' ){?>
	<li class="meu-time <?php echo ( $reference === 'dashboard' ? 'tg-active' : ''); ?>">
		<a href="<?php Listingo_Profile_Menu::listingo_profile_menu_link($profile_page, 'dashboard', $user_identity); ?>">
			<i class="lnr lnr-layers"></i>
			<span><?php esc_html_e('Insights', 'listingo'); ?></span>
		</a>
	</li>

  <li class="agendar">
		<a href="http://www.oisami.com/consultas/82288915048/">
			<i class="lnr lnr-calendar-full"></i>
			<span>Agendar Consulta</span>
		</a>
	</li>

	<li class="pedidos">
		<a href="/pacotes">
			<i class="lnr lnr-plus-circle"></i>
			<span>Vouchers Adicionais</span>
			<!--?php do_action('listingo_get_tooltip','menu','menu_favorites');?-->
		</a>
	</li>

	<li class="voucher">
		<a href="/minha-conta/orders/">
			<i class="lnr lnr-tag"></i>
			<span>Saldo de Vouchers</span>
			<!--?php do_action('listingo_get_tooltip','menu','menu_favorites');?-->
		</a>
	</li>
<?php }