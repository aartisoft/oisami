<?php
/**
 *
 * Theme Files
 *
 * @package   Listingo
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */

require_once ( get_template_directory() . '/theme-config/theme-setup/class-theme-setup.php'); //Theme setup
require_once ( get_template_directory() . '/includes/class-notifications.php'); //Theme notifications
require_once ( get_template_directory() . '/includes/scripts.php'); //Theme styles and scripts
require_once ( get_template_directory() . '/includes/sidebars.php'); //Theme sidebars
require_once ( get_template_directory() . '/includes/functions.php'); //Theme functionalty
require_once ( get_template_directory() . '/includes/class-headers.php'); //headers
require_once ( get_template_directory() . '/includes/class-footers.php'); //footers
require_once ( get_template_directory() . '/includes/class-titlebars.php'); //Sub headers
require_once ( get_template_directory() . '/includes/google_fonts.php'); // goolge fonts
require_once ( get_template_directory() . '/includes/hooks.php'); //Hooks
require_once ( get_template_directory() . '/includes/template-tags.php'); //Tags
require_once ( get_template_directory() . '/includes/jetpack.php'); //jetpack
require_once ( get_template_directory() . '/theme-config/tgmp/init.php'); //TGM init
require_once ( get_template_directory() . '/framework-customizations/includes/option-types.php'); //Custom options
require_once ( get_template_directory() . '/includes/redius-search/location_check.php');
require_once ( get_template_directory() . '/includes/constants.php'); //Constants
require_once ( get_template_directory() . '/includes/class-woocommerce.php'); //Woocommerce
require_once ( get_template_directory() . '/includes/currencies.php');
require_once ( get_template_directory() . '/directory/front-end/class-dashboard-menu.php');
require_once ( get_template_directory() . '/directory/front-end/hooks.php');
require_once ( get_template_directory() . '/directory/front-end/functions.php');
require_once ( get_template_directory() . '/directory/front-end/woo-hooks.php');
require_once ( get_template_directory() . '/directory/front-end/bookings/hooks.php');
require_once ( get_template_directory() . '/directory/front-end/bookings/functions.php');
require_once ( get_template_directory() . '/directory/front-end/jobs/hooks.php');
require_once ( get_template_directory() . '/directory/front-end/jobs/functions.php');
require_once ( get_template_directory() . '/demo-content/data-importer/importer.php'); //Users dummy data
require_once ( get_template_directory() . '/includes/typo.php');
require_once ( get_template_directory() . '/directory/back-end/dashboard.php');
require_once ( get_template_directory() . '/directory/back-end/hooks.php');
require_once ( get_template_directory() . '/directory/back-end/functions.php');
require_once ( get_template_directory() . '/includes/vc_custom/config.php'); //Visual Composer init in theme 

//Page Slug Body Class
function add_slug_body_class( $classes ) {
	global $post;
	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );


add_filter('body_class','my_class_names');
function my_class_names($classes) {
	if (! ( is_user_logged_in() ) ) {
		$classes[] = 'logged-out';
	}
	return $classes;
}


/* Add User Role Class to Body */
function print_user_classes() {
	if ( is_user_logged_in() ) {
		add_filter('body_class','class_to_body');
		add_filter('admin_body_class', 'class_to_body_admin');
	}
}
add_action('init', 'print_user_classes');
 
/* Add user role class to front-end body tag */
function class_to_body($classes) {
	global $current_user;
	$user_role = array_shift($current_user->roles);
	$classes[] = $user_role.' ';
	return $classes;
}
 
/// Add user role class and user id to front-end body tag
 
/* add 'class-name' to the $classes array */
function class_to_body_admin($classes) {
	global $current_user;
	$user_role = array_shift($current_user->roles);
	/* Adds the user id to the admin body class array */
	$user_ID = $current_user->ID;
	$classes = $user_role.' '.'user-id-'.$user_ID ;
	return $classes;
	return 'user-id-'.$user_ID;
}
