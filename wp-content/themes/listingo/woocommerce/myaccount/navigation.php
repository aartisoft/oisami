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

?>


<div class="col-xs-12 col-sm-5 col-md-4 col-lg-3 pull-left">
	<aside id="tg-sidebar" class="tg-sidebar">
		<div class="tg-widgetprofile">
			<figure class="tg-profilebannerimg sp-profile-banner-img">
				<img src="http://localhost/wp-content/uploads/2018/09/slider-03-270x120.jpg" alt="Profile Banner">
				<a target="_blank" class="sp-view-profile" href="/author/<?php echo $current_user->user_login; ?>/"><span class="lnr lnr-eye"></span></a>
			</figure>

			<div class="tg-widgetcontent">
				<figure class="sp-user-profile-img">
					<img src="<?php echo esc_url($avatar); ?>" alt="<?php esc_html_e('Profile Avatar', 'listingo'); ?>">
					<a class="tg-btnedite sp-profile-edit" href="/dashboard/?ref=settings&amp;identity=<?php echo $current_user->ID; ?>">
						<i class="lnr lnr-pencil"></i>
					</a>

					<div class="tg-themedropdown tg-userdropdown spprofile-statuswrap sphide"> 
						<a href="javascript:;" class="spactive-status">
							<span class="sphide"></span>
					  </a>
					  <div class="tg-dropdownmenu tg-statusmenu" aria-labelledby="tg-usermenu">
					  	<nav class="tg-dashboardnav">
					  		<ul class="dashboard-status">
					  			<li class="status-current current-offline " data-key="offline"><a href="javascript:;">Offline</a></li>
					  			<li class="status-current current-online " data-key="online"><a href="javascript:;">Online</a></li>
					  			<li class="status-current current-busy " data-key="busy"><a href="javascript:;">Busy</a></li>
					  			<li class="status-current current-away " data-key="away"><a href="javascript:;">Away</a></li>
					  			<li class="status-current current-sphide status-selected" data-key="sphide"><a href="javascript:;">Hide status</a></li>
					  		</ul>
					  	</nav>
					  </div>
					</div>
				</figure>

				<div class="tg-admininfo">
					<h3><?php echo $current_user->username; ?></h3>
				</div>

				<a target="_blank" class="sp-view-profile-btn tg-btn" href="/author/<?php echo $current_user->user_login; ?>/">Ver Perfil</a>
				<a target="_blank" class="sp-view-profile-btn sp-switchaccount" href="/dashboard/?ref=switch_account&amp;identity=<?php echo $current_user->ID; ?>">Mudar de Conta</a>
			</div>
		</div>

		<div class="tg-widgetdashboard">

			<nav id="tg-dashboardnav" class="tg-dashboardnav">
				<ul class="dashboard-menu-left">
					<li class="">
						<a href="/dashboard/?ref=dashboard&amp;identity=<?php echo $current_user->ID; ?>">
							<i class="lnr lnr-layers"></i>
							<span>Meu Time</span>
						</a>
					</li>

					<li class="">
						<a href="/dashboard/?ref=settings&amp;identity=<?php echo $current_user->ID; ?>">
							<i class="lnr lnr-cog"></i>
							<span>Configurações de Perfil</span>
						</a>
					</li>

					<li class="">
						<a href="/dashboard/?ref=favourite&amp;identity=<?php echo $current_user->ID; ?>">
							<i class="lnr lnr-heart"></i>
							<span>Lista de Favoritos</span>
						</a>
					</li>

					<!--li class="tg-hasdropdown">
						<a id="tg-btntoggle" class="tg-btntoggle" href="javascript:">
							<i class="lnr lnr-apartment"></i>
							<span><?php esc_html_e('Rede Credenciada', 'listingo'); ?></span>
							<?php do_action('listingo_get_tooltip','menu','menu_appointments');?>
						</a>
						<ul class="tg-emailmenu">
							<li class="">
								<a href="/minha-conta/pedidos">
									<span>Pedidos</span>
								</a>
							</li>

							<li class="">
								<a href="/minha-conta/vouchers">
									<span>Vouchers</span>
								</a>
							</li>
						</ul>
					</li-->

					<li class="">
						<a href="/dashboard/?ref=security_settings&amp;identity=<?php echo $current_user->ID; ?>">
							<i class="lnr lnr-construction"></i>
							<span>Configurações de Segurança</span>
						</a>
					</li>

					<li>
						<a href="/wp-login.php?action=logout&amp;redirect_to=http%3A%2F%2Flocalhost%2F&amp;_wpnonce=346301435f">
							<i class="lnr lnr-exit"></i>
							<span>Sair</span>
						</a>
					</li>

					<!--
					<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
						<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
						</li>
					<?php endforeach; ?>
					-->
				</ul>
			</nav>

		</div>
	</aside>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
