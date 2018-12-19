<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$current_user = wp_get_current_user();

global $current_user, $wp_roles, $userdata, $post;

$user_identity = $current_user->ID;
$url_identity = $user_identity;
$profile_status = get_user_meta($user_identity, 'profile_status', true);
$profile_status	=  !empty($profile_status) ? $profile_status : 'sphide';

$dir_profile_page = '';
if (function_exists('fw_get_db_settings_option')) {
	$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
}

$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';

$avatar = apply_filters(
	'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 100, 'height' => 100), $user_identity), array('width' => 100, 'height' => 100)
);

$statuses	= listingo_get_status_list();

if (isset($_GET['identity']) && !empty($_GET['identity'])) {
    $url_identity = $_GET['identity'];
}

$provider_category = listingo_get_provider_category($user_identity);
$bk_settings	= listingo_get_booking_settings();

$insight_page = '';
if (function_exists('fw_get_db_settings_option')) {
	$insight_page = fw_get_db_settings_option('insight_page', $default_value = null);
}

get_header();

do_action('listingo_is_user_active',$url_identity);
do_action('listingo_is_user_verified',$url_identity);

?>


<div class="col-xs-12 col-sm-5 col-md-4 col-lg-3 pull-left">
	<aside id="tg-sidebar" class="tg-sidebar">
		<?php Listingo_Profile_Menu::listingo_profile_menu_left(); ?>

		<!--div class="tg-widgetdashboard">

			<nav id="tg-dashboardnav" class="tg-dashboardnav">
				<ul class="dashboard-menu-left">
					<li class="">
						<a href="/dashboard/?ref=dashboard&amp;identity=<?php echo $current_user->ID; ?>">
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
						</a>
					</li>

					<li class="voucher">
						<a href="/minha-conta/orders/">
							<i class="lnr lnr-tag"></i>
							<span>Saldo de Vouchers</span>
						</a>
					</li>

					<li class="">
						<a href="/dashboard/?ref=settings&amp;identity=<?php echo $current_user->ID; ?>">
							<i class="lnr lnr-cog"></i>
							<span><?php esc_html_e('Profile Settings', 'listingo'); ?></span>
						</a>
					</li>

					<li class="">
						<a href="/dashboard/?ref=security_settings&amp;identity=<?php echo $current_user->ID; ?>">
							<i class="lnr lnr-construction"></i>
							<span><?php esc_html_e('Security Settings', 'listingo'); ?></span>
						</a>
					</li>

					<li>
						<a href="/wp-login.php?action=logout&amp;redirect_to=http%3A%2F%2Flocalhost%2F&amp;_wpnonce=346301435f">
							<i class="lnr lnr-exit"></i>
							<span>Sair</span>
						</a>
					</li>

					<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
						<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

		</div-->
	</aside>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
