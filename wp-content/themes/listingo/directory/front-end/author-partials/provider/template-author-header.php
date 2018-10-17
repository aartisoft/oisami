<?php
/**
 *
 * Author Header Template.
 *
 * @package   Listingo
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */
/**
 * Get User Queried Object Data
 */
global $current_user;
$author_profile = $wp_query->get_queried_object();

/* ============Get The User Avatar Image======================= */
$user_avatar = apply_filters(
    'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 200, 'height' => 200), $author_profile->ID), array('width' => 200, 'height' => 200) //size width,height
);
/* ============Get The User Dashboard Banner==================== */

$user_banner = apply_filters(
    'listingo_get_media_filter', listingo_get_user_banner(array('width' => 0, 'height' => 0), $author_profile->ID), array('width' => 1920, 'height' => 380) //size width,height
);
/* ==================Get Company Name & Tag Line================== */
$company_name = listingo_get_username($author_profile->ID);
$provider_category	= listingo_get_provider_category($author_profile->ID);
$db_privacy = listingo_get_privacy_settings($author_profile->ID);

$tag_line = '';
if (!empty($author_profile->tag_line)) {
    $tag_line = $author_profile->tag_line;
}

//Authentication page
$login_register = '';
$login_reg_link = '#';
if (function_exists('fw_get_db_settings_option')) {
	$login_register = fw_get_db_settings_option('enable_login_register');
}

if (!empty($login_register['enable']['login_reg_page'])) {
	$login_reg_link = $login_register['enable']['login_reg_page'];
}
?>
<div class="tg-detailpagehead sp-provider-wrap">
	<figure class="parallax-window" data-appear-top-offset= "600" data-parallax="scroll" data-position="center 0" data-image-src="<?php echo esc_url($user_banner); ?>" style="background: url('<?php echo esc_url($user_banner); ?>');">
		<figcaption>
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="tg-detailpageheadcontent">
							<?php if (!empty($user_avatar)) { ?>
								<div class="tg-companylogo">
									<img src="<?php echo esc_url($user_avatar); ?>" alt="<?php esc_html_e('Author Avatar', 'listingo'); ?>">
								</div>
							<?php } ?>

							<div class="tg-companycontent">
								<?php listingo_result_tags($author_profile->ID, 'echo'); ?>
								<div class="tg-title">
									<?php if (!empty($company_name)) { ?>
										<h1><?php echo esc_attr($company_name); ?></h1>
									<?php } ?>
									<?php if (!empty($tag_line)) { ?>
										<span><?php echo esc_attr($tag_line); ?></span>
									<?php } ?>
								</div>
								<?php do_action('sp_get_rating_and_votes', $author_profile->ID, 'echo'); ?>
							</div>

							<?php if (is_user_logged_in()) {
								if( apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true
									&& apply_filters('listingo_is_setting_enabled', $author_profile->ID, 'subscription_appointments') === true
									&& $current_user->ID != $author_profile->ID
									&& ( isset( $db_privacy['profile_appointment'] ) && $db_privacy['profile_appointment'] === 'on' )
								){
							?>
								<button class="tg-btn" type="button" data-toggle="modal" data-target=".tg-appointmentModal"><?php esc_html_e('Make Appointment', 'listingo'); ?></button>
							<?php }?>


							<!--div id="company">
								<a class="sp-forgot-password tg-btn " href="javascript:;"><?php esc_html_e('Make Appointment', 'listingo'); ?></a>
							</div-->

							
							<?php } else if( apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true 
								&& apply_filters('listingo_is_setting_enabled', $author_profile->ID,'subscription_appointments') === true
								&& ( isset( $db_privacy['profile_appointment'] ) && $db_privacy['profile_appointment'] === 'on' )
								&& !empty($login_reg_link) ) {?>
								<a class="tg-btn" href="<?php echo esc_url(get_permalink((int) $login_reg_link[0])); ?>?redirect=<?php echo esc_url(get_author_posts_url($author_profile->ID)); ?>"><?php esc_html_e('Login to make an appointment', 'listingo'); ?></a>
							<?php }?>
						</div>
					</div>
				</div>
			</div>
		</figcaption>
	</figure>
</div>