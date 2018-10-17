<?php
/**
 *
 * Class used as base to create theme footer
 *
 * @package   Listingo
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */
if (!class_exists('Listingo_Prepare_Footers')) {
	class Listingo_Prepare_Footers {
		function __construct() {
			add_action('listingo_do_process_footers', array(&$this, 'listingo_do_process_footers'));
		}
		/**
		 * @Prepare Top Strip
		 * @return {}
		 * @author themographics
		 */
		public function listingo_do_process_footers() {
			$footer_copyright = 'Copyright &copy; ' . date('Y') . '&nbsp;' . esc_html__('Listingo. All rights reserved.', 'listingo') . get_bloginfo();
			$enable_widget_area = 'enable';
			$enable_footer_menu = '';
			$enable_featrues = '';
			
			if (function_exists('fw_get_db_settings_option')) {
				$enable_widget_area = fw_get_db_settings_option('enable_widget_area', $default_value = null);
				$enable_footer_menu = fw_get_db_settings_option('enable_footer_menu', $default_value = null);
				$footer_copyright = fw_get_db_settings_option('footer_copyright', $default_value = null);
				$enable_featrues = fw_get_db_settings_option('feature_section');
			}
			$footer_features = array();
			if (!empty($enable_featrues['enable']['features'])) {
				$footer_features = $enable_featrues['enable']['features'];
			}
			?>
			</main>
			<footer id="tg-footer" class="tg-footer tg-haslayout">
				<div class="container">
					<div class="row">
						<?php 
						if (isset($enable_featrues['gadget']) && $enable_featrues['gadget'] === 'enable') {
							if (!empty($footer_features)) { ?>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<ul class="tg-features">
									<?php
									do_action('enqueue_unyson_icon_css');
									foreach ($footer_features as $key => $features) {
										$feature_title = $features['feature_title'];
										$feature_desc = $features['feature_desc'];
										$feature_icon = $features['feature_icon'];
										$feature_color = $features['feature_color'];
										$icon_bg_color = 'style=background:#ec407a;';
										$icon_txt_color = 'style=color:#ec407a;';
										if (!empty($feature_color)) {
											$icon_bg_color = 'style=background:' . $feature_color . ';';
											$icon_txt_color = 'style=color:' . $feature_color . ';';
										}
										?>
										<?php
										if (!empty($feature_title) ||
												!empty($feature_desc) ||
												!empty($feature_icon)) {
											?>
											<li>
												<div class="tg-feature">
													<?php if (!empty($feature_icon)) { ?>
														<span class="tg-featureicon" <?php echo esc_attr($icon_bg_color); ?>>
															<i class="<?php echo esc_attr($feature_icon['icon-class']); ?>"></i>
														</span>
													<?php } ?>
													<?php if (!empty($feature_title) || !empty($feature_desc)) { ?>
														<div class="tg-featurecontent">
															<?php if (!empty($feature_title)) { ?>
																<h3 <?php echo esc_attr($icon_txt_color); ?>><?php echo esc_attr($feature_title); ?></h3>
															<?php } ?>
															<?php if (!empty($feature_desc)) { ?>
																<span><?php echo esc_attr($feature_desc); ?></span>
															<?php } ?>
														</div>
													<?php } ?>
												</div>
											</li>
										<?php } ?>
									<?php } ?>
								</ul>
							</div>
						<?php }} ?>
						<?php
						if (isset($enable_widget_area) && $enable_widget_area === 'enable') {
							if (is_active_sidebar('footer_sidebar_1') ||
									is_active_sidebar('footer_sidebar_2') ||
									is_active_sidebar('footer_sidebar_3') ||
									is_active_sidebar('footer_sidebar_4')
							) {
								?> 
								<div class="tg-fourcolumns sp-widgets-area">
									<?php if (is_active_sidebar('footer_sidebar_1')) : ?>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pull-left">
											<?php dynamic_sidebar('footer_sidebar_1'); ?>
										</div>
									<?php endif; ?>
									<?php if (is_active_sidebar('footer_sidebar_2')) : ?>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pull-left">
											<?php dynamic_sidebar('footer_sidebar_2'); ?>
										</div>
									<?php endif; ?>
									<?php if (is_active_sidebar('footer_sidebar_3')) : ?>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pull-left">
											<?php dynamic_sidebar('footer_sidebar_3'); ?>
										</div>
									<?php endif; ?>
									<?php if (is_active_sidebar('footer_sidebar_4')) : ?>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-3 pull-left">
											<?php dynamic_sidebar('footer_sidebar_4'); ?>
										</div>
									<?php endif; ?>
								</div>
								<?php
							} //Check if widget is registered.
						}//Check if widget section enable endif.
						?>
					</div>
				</div>
				<div class="tg-footerbar">
					<div class="container">
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<?php if (!empty($footer_copyright)) { ?>
									<span><?php echo do_shortcode($footer_copyright); ?></span>
								<?php } ?>
								<?php if (isset($enable_footer_menu) && $enable_footer_menu === 'enable') { ?>
									<nav class="tg-footernav">
										<?php Listingo_Prepare_Headers::listingo_prepare_navigation('footer-menu', '', '', '0'); ?>
									</nav>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</footer>
			</div>
			<?php
			
			//Forgot password 
			if (!is_user_logged_in()) {
				do_shortcode('[user_lostpassword]');
			}
			
			//Check if Auth Key is not empty then call the hook
			if (!empty($_GET['activation_key'])) {
				$verify_key = $_GET['activation_key'];
				do_action('listingo_welcome_page', $verify_key);
			}
			
			//Reset Model
			if (!empty($_GET['key']) &&
					( isset($_GET['action']) && $_GET['action'] == "reset_pwd" ) &&
					(!empty($_GET['login']) )
			) {
				do_action('listingo_reset_password_form');
			}
			
			//Account Verifications Model
			if ( !empty($_GET['key']) && !empty($_GET['verifyemail']) ) {
				do_action('listingo_verify_user_account');
			}
			
			//Compose Message Modal
			if ( function_exists('fw_get_db_settings_option') && fw_ext('private-messaging')) {
				do_action('render_message_compose_modal');
			}
		}
	}
	new Listingo_Prepare_Footers();
}