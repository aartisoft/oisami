<?php
/**
 *
 * User Packages
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */
get_header();
global $current_user,$woocommerce;
$current_date	= date('Y-m-d H:i:s');
$package_id		= listingo_get_subscription_meta('subscription_id',$current_user->ID);
$package_expiry	= listingo_get_subscription_meta('subscription_expiry',$current_user->ID);
$package_expiry		= !empty( $package_expiry ) ? date( 'Y-m-d', $package_expiry ) : '';
$package_counter	= !empty( $package_expiry ) ? date( 'M d, Y H:i:s', strtotime( $package_expiry ) ) : '';
$provider_category  = listingo_get_provider_category($current_user->ID);
$package_title		= !empty( $package_id ) ? get_the_title($package_id) : esc_html__('NILL','listingo');
$roles 				= $current_user->roles;
$package_type		= 'provider';

if (!empty($roles[0]) && $roles[0] === 'customer' ) {
	$package_type	= 'customer';
}
?>
<div class="tg-formtheme">
  <fieldset>
    <div class="tg-pkgexpireyandcounter">
	  <div class="tg-pkgexpirey"><span><?php esc_html_e('Current Package','listingo');?></span>
		<h3><?php echo esc_attr($package_title);?></h3>
	  </div>	
      <?php if( !empty( $package_expiry ) && strtotime( $package_expiry )  > strtotime( $current_date ) ) {?>
		  <div class="tg-timecounter tg-expireytimecounter">
			<div id="tg-countdown" class="tg-countdown"></div>
			<div id="tg-note" class="tg-note"></div>
		  </div>
		  <?php wp_add_inline_script('listingo_user_dashboard', 'listingo_package_counter("'.esc_attr($package_expiry).'");'); ?>
      <?php } else{?>
		<div class="tg-timecounter tg-expireytimecounter">
			<div id="tg-countdown" class="tg-countdown">
			  <ul>
				<li>
				  <h3>0</h3>
				  <h4><?php esc_html_e('Days', 'listingo'); ?></h4>
				</li>
				<li>
				  <h3>0</h3>
				  <h4><?php esc_html_e('Hours', 'listingo'); ?></h4>
				</li>
				<li>
				  <h3>0</h3>
				  <h4><?php esc_html_e('Minutes', 'listingo'); ?></h4>
				</li>
				<li>
				  <h3>0</h3>
				  <h4><?php esc_html_e('Seconds', 'listingo'); ?></h4>
				</li>
			  </ul>
			</div>
			<div id="tg-note" class="tg-note"><?php esc_html_e('Expired', 'listingo'); ?></div>
		</div>
   	  <?php }?>
    </div>
    <?php if (class_exists('WooCommerce')) {?>
    	<div class="tg-dashboardbox">
		  <div class="tg-dashboardtitle">
			<h2><?php esc_html_e('Update Your Package', 'listingo'); ?><?php do_action('listingo_get_tooltip','section','packages');?></h2>
		  </div>
		  <div class="tg-packagesbox">
			<div class="tg-pkgplans tg-pkgplansvtwo">
			  <div class="row">
				<?php
					$args = array(
						'post_type' => 'product',
						'posts_per_page' => -1,
						'order' => 'DESC',
						'orderby' => 'ID',
						'post_status' => 'publish',
						'ignore_sticky_posts' => 1
					);


					$meta_query_args[] = array(
						'key' => 'sp_package_type',
						'value' => $package_type,
						'compare' => '=',
					);
					$query_relation = array('relation' => 'AND',);
					$meta_query_args = array_merge($query_relation, $meta_query_args);
					$args['meta_query'] = $meta_query_args;

					$loop = new WP_Query( $args );
					if ( $loop->have_posts() ) {
						while ( $loop->have_posts() ) : $loop->the_post();
							global $product;
							$sp_duration = get_post_meta( $product->get_id(), 'sp_duration', true );
							$sp_jobs = get_post_meta( $product->get_id(), 'sp_jobs', true );
							$sp_articles = get_post_meta( $product->get_id(), 'sp_articles', true );
							$sp_favorites = get_post_meta( $product->get_id(), 'sp_favorites', true );

							$sp_articles = !empty( $sp_articles ) ? $sp_articles : 1;
							$sp_jobs 	 = !empty( $sp_jobs ) ? $sp_jobs : 1;
							
						
							if (!empty($package_type) && $package_type === 'provider' ) {
								$sp_featured= get_post_meta( $product->get_id(), 'sp_featured', true );
								$sp_appointments = get_post_meta( $product->get_id(), 'sp_appointments', true );
								$sp_banner = get_post_meta( $product->get_id(), 'sp_banner', true );
								$sp_insurance = get_post_meta( $product->get_id(), 'sp_insurance', true );
								$sp_teams = get_post_meta( $product->get_id(), 'sp_teams', true );
								$sp_hours = get_post_meta( $product->get_id(), 'sp_hours', true );
								$sp_page_design = get_post_meta( $product->get_id(), 'sp_page_design', true );
								
								$sp_gallery_photos 	= get_post_meta( $product->get_id(), 'sp_gallery_photos', true );
								$sp_videos 			= get_post_meta( $product->get_id(), 'sp_videos', true );
								
								$sp_photos_limit 	= get_post_meta( $product->get_id(), 'sp_photos_limit', true );
								$sp_banners_limit	= get_post_meta( $product->get_id(), 'sp_banners_limit', true );
								$sp_contact_information = get_post_meta( $product->get_id(), 'sp_contact_information', true );
								
								$sp_gallery_photos  = !empty($sp_gallery_photos) ? $sp_gallery_photos : 0;
								$sp_videos 			= !empty($sp_videos) ? $sp_videos : 0;
								$sp_photos_limit 	= !empty($sp_photos_limit) ? $sp_photos_limit : 0;
								$sp_banners_limit 	= !empty($sp_banners_limit) ? $sp_banners_limit : 0;

							}
						?>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 pull-left">
						  <div class="tg-pkgplan">
							<div class="tg-pkgplanhead">
							  <h3><?php the_title();?></h3>
							  <h4><?php echo force_balance_tags( $product->get_price_html() ); ?> <em><?php esc_html_e( 'for','listingo' );?>&nbsp;<?php echo intval($sp_duration);?>&nbsp;<?php esc_html_e( 'days','listingo' );?></em></h4>
							</div>
							<ul>
							  <?php if ( apply_filters('listingo_get_theme_settings', 'jobs') == 'yes') {?>
							 	 <li><span><?php esc_html_e( 'Number of job(s)','listingo' );?></span><span><?php echo intval($sp_jobs);?></span></li>
							  <?php }?>
							  
							  <?php if( apply_filters('listingo_is_favorite_allowed',$current_user->ID) === true ){?>
							  	<li><span><?php esc_html_e( 'Favorites Listings','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_favorites);?></span></li>
							  <?php }?>
							  
							  <?php if (!empty($package_type) && $package_type === 'provider' ) {?>
								  <li><span><?php esc_html_e( 'Featured listing for','listingo' );?></span><span><?php echo intval($sp_featured);?>&nbsp;<?php esc_html_e( 'days','listingo' );?></span></li>
								  
								  <?php if( apply_filters('listingo_is_feature_allowed', $provider_category, 'appointments') === true ){?>
								  	<li><span><?php esc_html_e( 'Appointments','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_appointments);?></span></li>
								  <?php }?>
								  
								  <li><span><?php esc_html_e( 'Profile Banner','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_banner);?></span></li>
								  
								  <?php if( apply_filters('listingo_is_feature_allowed', $provider_category, 'insurance') === true ){?>
								  	<li><span><?php esc_html_e( 'Insurance Fields','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_insurance);?></span></li>
								  <?php }?>
								  
								  <?php if( apply_filters('listingo_is_feature_allowed', $provider_category, 'teams') === true ){?>
								  	<li><span><?php esc_html_e( 'Team Options','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_teams);?></span></li>
								  <?php }?>
								  
								  <li><span><?php esc_html_e( 'Business Hours','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_hours);?></span></li>
								  <?php if ( function_exists('fw_get_db_settings_option') && fw_ext('articles')) {?>
									  <?php if( apply_filters('listingo_is_feature_allowed', $provider_category, 'articles') === true ){?>
										<li><span><?php esc_html_e( 'Number of articles','listingo' );?></span><span><?php echo esc_attr($sp_articles);?></span></li>
									  <?php }?>
								  <?php }?>
								  
								  <li><span><?php esc_html_e( 'Page Design','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_page_design);?></span></li>
								  
								  <?php if( apply_filters('listingo_is_feature_allowed', $provider_category, 'gallery') === true ){?>
								  	<li><span><?php esc_html_e( 'Number of gallery photos','listingo' );?></span><span><?php echo esc_attr($sp_gallery_photos);?></span></li>
								  <?php }?>
								  
								  <li><span><?php esc_html_e( 'Number of profile photos','listingo' );?></span><span><?php echo esc_attr($sp_photos_limit);?></span></li>
								  <li><span><?php esc_html_e( 'Number of banner photos','listingo' );?></span><span><?php echo esc_attr($sp_banners_limit);?></span></li>
								  
								  <?php if( apply_filters('listingo_is_feature_allowed', $provider_category, 'videos') === true ){?>
								  	<li><span><?php esc_html_e( 'Number of video links','listingo' );?></span><span><?php echo esc_attr($sp_videos);?></span></li>
								  <?php }?>
								  
								  <?php if ( apply_filters('listingo_is_contact_informations_enabled', 'yes','','') === 'yes') {?>
								  	<li><span><?php esc_html_e( 'Contact informations','listingo' );?></span><span><?php echo listingo_is_feature_support($sp_contact_information);?></span></li>
								  <?php }?>
								  
							  <?php }?>

							</ul>
							<button class="tg-btn renew-package" data-key="<?php echo intval($product->get_id());?>"><?php esc_html_e( 'Buy/Renew Now','listingo' );?></button>
						   </div>
						</div>
						<?php
						endwhile;
					} else {?>
						 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-left">
							<?php Listingo_Prepare_Notification::listingo_warning(esc_html__('Sorry!', 'listingo'), esc_html__('No package purchased yet or package expired.', 'listingo')); ?>
						 </div>
					<?php }
					wp_reset_postdata();
				?> 
			  </div>
			</div>
		  </div>
		</div>
    <?php } else {?>
		 <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-left">
			<div class="row">
				<?php Listingo_Prepare_Notification::listingo_warning(esc_html__('Sorry!', 'listingo'), esc_html__('WooCoomerce should be installed for payments. Please contact to administrator', 'listingo')); ?>
			</div>
		 </div>
	<?php }?>
  </fieldset>
</div>
<?php get_footer(); ?>