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
        'listingo_get_media_filter', listingo_get_user_avatar(array('width' => 170, 'height' => 170), $author_profile->ID), array('width' => 170, 'height' => 170) //size width,height
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

$profile_view = apply_filters('sp_get_profile_views', $author_profile->ID, 'set_profile_view');
$post_id = '';
$category_name = '';
if (!empty($author_profile->category)) {
    $post_id 		= $author_profile->category;
    $category_name  = get_the_title($post_id);
}

$subcats = get_user_meta($author_profile->ID, 'sub_category', true);	

if( !empty( $subcats ) ){
	$cat_titles	= '';
	$total = listingo_count_items($subcats);
	$count	= 0;
	foreach( $subcats as $key=>$value ){
		$count++;
		$sub_category_term = get_term_by('slug', $value, 'sub_category');
		if (!empty($sub_category_term)) {
			$cat_titles		.= $sub_category_term->name;
			if ($count < ($total - 1)) {
			  $cat_titles		.= ', ';
		   }
		}
	}
}
?>
<div class="tg-detailpagehead">
	<figure>
		<img src="<?php echo esc_url($user_banner); ?>" alt="<?php esc_html_e('Banner', 'listingo'); ?>">
		<figcaption>
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="tg-detailpageheadcontent">
							<?php listingo_result_tags($author_profile->ID, 'echo'); ?>
						</div>
					</div>
				</div>
			</div>
		</figcaption>
	</figure>
	<div class="tg-detailheadholder">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="tg-detailheadinfo">
						<figure><img src="<?php echo esc_url($user_avatar); ?>" alt="<?php esc_html_e('Author Avatar', 'listingo'); ?>"></figure>
						<div class="tg-compnayinfo">
							<div class="tg-leftarea">
								<div class="tg-companycontent">
									<ul class="tg-matadata">
										<?php if (!empty($category_name)) { ?>
											<li class="<?php esc_html_e('Categories', 'listingo'); ?>"><i class="lnr lnr-tag"></i><em><?php echo esc_attr($category_name); ?>&nbsp;&nbsp;</em><?php if( !empty( $cat_titles ) ) {?><em><i class="fa fa-angle-right"></i>&nbsp;<?php echo esc_attr($cat_titles);?></em><?php }?></li>
										<?php } ?>
										<li>
											<i class="fa fa-eye"></i>
											<em><?php echo intval($profile_view); ?></em>
										</li>
									</ul>
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
							</div>
							<div class="tg-rightarea">
								<?php if (is_user_logged_in()) {
									if( apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true
										&& apply_filters('listingo_is_setting_enabled', $author_profile->ID, 'subscription_appointments') === true
										&& $current_user->ID != $author_profile->ID
										&& ( isset( $db_privacy['profile_appointment'] ) && $db_privacy['profile_appointment'] === 'on' )
									){
									?>
										<a class="tg-btnappointment" data-toggle="modal" data-target=".tg-appointmentModal"><span><?php esc_html_e('Make Appointment', 'listingo'); ?></span></a>
									<?php }?>
								<?php } else if( apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true 
									&& apply_filters('listingo_is_setting_enabled', $author_profile->ID,'subscription_appointments') === true
									&& ( isset( $db_privacy['profile_appointment'] ) && $db_privacy['profile_appointment'] === 'on' )
									&& !empty($login_reg_link) ) {?>
									<a class="tg-btnappointment" href="<?php echo esc_url(get_permalink((int) $login_reg_link[0])); ?>?redirect=<?php echo esc_url(get_author_posts_url($author_profile->ID)); ?>"><span><?php esc_html_e('Login to make an appointment', 'listingo'); ?></span></a>
								<?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>