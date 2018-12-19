<?php
/**
 * Template Name: Dashboard Novo
 *
 * @package Listingo
 * @since Listingo 1.0
 * @desc Template used for front end dashboard.
 */

/* Define Global Variables */
global $current_user, $wp_roles, $userdata, $post;

$user_identity = $current_user->ID;
$url_identity = $user_identity;

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
<div class="container">
	<div class="row">
		<div id = "tg-twocolumns" class = "tg-twocolumns">
			<?php 
				if (is_user_logged_in()) {
					if( apply_filters('listingo_is_social_user', $user_identity) === 'yes' ){
						do_action('listingo_complete_registration_form');
					} 
					else{
				?>
				<div class = "col-xs-12 col-sm-5 col-md-4 col-lg-3 pull-left">
					<aside id = "tg-sidebar" class = "tg-sidebar">
						<?php Listingo_Profile_Menu::listingo_profile_menu_left(); ?>
						<?php if (is_active_sidebar('user-dashboard-sidebar')) {?>
						  <div class="tg-advertisement">
							<?php dynamic_sidebar('user-dashboard-sidebar'); ?>
						  </div>
						<?php }?>
					</aside>
				</div>

				<div class="col-xs-12 col-sm-7 col-md-8 col-lg-9 pull-right dashboard-admin">
					<div class="tg-dashboardtitle">
						<h2><?php wp_title(''); ?></h2>
					</div>

					<?php
// TO SHOW THE PAGE CONTENTS
    while ( have_posts() ) : the_post(); ?>
        <div class="entry-content-page">
            <?php the_content(); ?>
        </div>

    <?php
endwhile; //resetting the page loop
wp_reset_query(); //resetting the page query
?>
				</div>
			<?php }} else { ?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<?php Listingo_Prepare_Notification::listingo_warning(esc_html__('Restricted Access', 'listingo'), esc_html__('You have not any privilege to view this page.', 'listingo')); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>