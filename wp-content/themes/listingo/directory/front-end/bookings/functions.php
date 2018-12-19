<?php
/**
 *
 * Booking/Appointments functions
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */
/**
 * @get list of default slots
 * @return {}
 */
if (!function_exists('listingo_get_default_slots')) {

    function listingo_get_default_slots($day = '', $return_type = "return") {
        global $current_user, $wp_roles, $userdata, $post;
        $user_identity = $current_user->ID;
        $default_slots = array();
        $default_slots = get_user_meta($user_identity, 'default_slots', true);
        $time_format = get_option('time_format');

        ob_start();
        if (!empty($default_slots[$day])) {
            foreach ($default_slots[$day] as $key => $value) {

                $time = explode('-', $key);
                ?>
                <span class="tg-radiotimeslot">
                    <div class="slot-detail">
                        <!--
                        <?php if (!empty($default_slots[$day . '-details'][$key]['slot_title'])) { ?>
                            <span class="tg-title"><?php echo esc_attr($default_slots[$day . '-details'][$key]['slot_title']); ?></span>
                        <?php } ?>
                        -->
                        <span class="slot-time">
                            <em><?php echo date_i18n($time_format, strtotime('2016-01-01 ' . $time[0])); ?>-<?php echo date_i18n($time_format, strtotime('2016-01-01 ' . $time[1])); ?></em>
                        </span>
                        <!--span class="spaces-available"><?php echo esc_html_e('Available Spaces', 'listingo'); ?>:&nbsp;<?php echo intval($value); ?></span-->
                    </div>
                    <a href="javascript:;" data-time="<?php echo esc_attr($key); ?>" data-day="<?php echo esc_attr($day); ?>" class="fa fa-close tg-btndelete delete-current-slot"></a>
                </span>
                <?php
            }
        }

        if ($return_type === 'return') {
            return ob_get_clean();
        } else {
            echo ob_get_clean();
        }
    }

}

/**
 * @get list of appointment slots
 * @return {}
 */
if (!function_exists('listingo_get_appointment_slots')) {

    function listingo_get_appointment_slots($author_id, $slot_date = '', $return_type = "return") {
        global $current_user, $wp_roles, $userdata, $post;
        $time_slots = array();
		$day_name 	   = strtolower(date('l',strtotime($slot_date)));
        $time_slots 	= get_user_meta($author_id, 'default_slots', true);
        $time_format 	= get_option('time_format');
		
        ob_start();

        if (!empty($time_slots[$day_name])) {
            foreach ($time_slots[$day_name] as $key => $value) {
                $time = explode('-', $key);
				$data	= listingo_check_booked_slot($key,$slot_date,$author_id);
				if( isset( $data['available'] ) && $data['available'] === 'yes' ){
					?>
					<span class="tg-radio">
						<input type="radio" id="tg-timeslot-<?php echo esc_attr($key); ?>" name="tg-timeslot" value="<?php echo esc_attr($key); ?>">
						<label for="tg-timeslot-<?php echo esc_attr($key); ?>">
							<span class="apt-slot-date"><?php echo date_i18n($time_format, strtotime('2016-01-01 ' . $time[0])); ?>-<?php echo date_i18n($time_format, strtotime('2016-01-01 ' . $time[1])); ?></span>
							<!--span class="apt-slot-remaining"><?php esc_html_e('Spaces left','listingo');?>:&nbsp;<?php echo esc_attr($data['remaining']); ?></span-->
						</label>
					</span>
					<?php
				}
            }
        }

        if ($return_type === 'return') {
            return ob_get_clean();
        } else {
            echo ob_get_clean();
        }
    }

}

/**
 * @Appointment Booking Step 1
 * @return {}
 */
if (!function_exists('listingo_get_appointment_step_one')) {

    function listingo_get_appointment_step_one($user_identity = '', $return_type = 'echo') {

        $appointment_title = get_user_meta($user_identity, 'appointment_inst_title', true);
        $appointment_desc = get_user_meta($user_identity, 'appointment_inst_desc', true);
        if (!empty($appointment_title) || !empty($appointment_desc)) {
            ?>
            <?php if (!empty($appointment_title)) { ?>
                <h3><?php echo esc_attr($appointment_title); ?></h3>
            <?php } ?>
            <?php if (!empty($appointment_desc)) { ?>
                <div class="tg-description">
                    <?php echo wp_kses_post(wpautop(do_shortcode($appointment_desc))); ?>
                </div>
            <?php } ?>
            <?php
        }

        if (isset($return_type) && $return_type == 'return') {
            return ob_get_clean();
        } else {
            echo ob_get_clean();
        }
    }

}

/**
 * @Appointment Booking Step 2
 * @return {}
 */
if (!function_exists('listingo_get_appointment_step_two')) {

    function listingo_get_appointment_step_two() {
        global $current_user, $wp_roles, $userdata, $post;

        $appointment_data = get_user_meta($current_user->ID, 'appointment_data', true);
        $apt_data = explode("|", $appointment_data);
        $author_id = '';
        
		if (!empty($apt_data[0])) {
            $author_id = intval($apt_data[0]);
        }
        
		$appointment_services = get_user_meta($author_id, 'profile_services', true);
        $apointment_types 	  = get_user_meta($author_id, 'appointment_types', true);
        $appointment_reasons  = get_user_meta($author_id, 'appointment_reasons', true);
        $currencies 		  = listingo_get_current_currency();
		$currency_symbol 	  = !empty( $currencies['symbol'] ) ? $currencies['symbol'] : '$';

        $username = listingo_get_username($current_user->ID);
        $phone = get_user_meta($current_user->ID, 'phone', true);
        $email = esc_attr($current_user->user_email);

        ob_start();
        ?>
        <form class="tg-formbookappointment tg-form-appointment-wizard">
            <div class="tg-appointmentinfo">
                <div class="form-group">
                    <div class="tg-heading">
                        <h3><?php esc_html_e('Appointment Information', 'listingo'); ?></h3>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                            <span class="tg-select">
                                <select name="appointment_form[apt_services]" class="sp_service">
                                    <option value=""><?php esc_html_e('Select Services*', 'listingo'); ?></option>
                                    <?php
                                    if (!empty($appointment_services)) {
                                        foreach ($appointment_services as $key => $services) {
                                            extract($services);
                                            if (!empty($title)) {
                                                ?>
                                                <option value="<?php echo esc_attr($key); ?>">
                                                    <?php echo esc_attr($title); ?> ( <?php echo listingo_format_price($price);?> )
                                                </option>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                            <span class="tg-select">
                                <select name="appointment_form[apt_types]" class="sp_appointment_types">
                                    <option value=""><?php esc_html_e('Choose Appointment Type*', 'listingo'); ?></option>

                                    <!--
                                    <option value="primeiraconsulta">Primeira Consulta</option>
                                    <option value="retorno">Retorno</option>
                                    <option value="outros">Outros</option>
                                    -->
                                    
                                    <?php if (!empty($apointment_types)) { foreach ($apointment_types as $key => $types) { ?>
                                      <option value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($types); ?></option>
                                    <?php }} ?>

                                </select>
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                            <span class="tg-select">
                                <select name="appointment_form[apt_reasons]" class="sp_appointment_reasons">
                                    <option value=""><?php esc_html_e('Reason For Visit*', 'listingo'); ?></option>

                                    <!--
                                    <option value="checkup">Checkup de Rotina</option>
                                    <option value="outros">Outros</option>
                                    -->
                                    
                                    <?php if (!empty($appointment_reasons)) { foreach ($appointment_reasons as $key => $reasons) { ?>
                                      <option value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($reasons); ?></option>
                                    <?php }} ?>

                                </select>
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-md-12 col-lg-12">
                        <textarea name="appointment_form[apt_description]" placeholder="<?php esc_html_e('Description', 'listingo'); ?>"></textarea>
                    </div>
                </div>
            </div>
            <div class="tg-userdetail">
                <div class="form-group">
                    <div class="tg-heading">
                        <h3><?php esc_html_e('Your Details', 'listingo'); ?></h3>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                            <input type="text" value="<?php echo esc_attr( $username ); ?>" name="appointment_form[apt_name]" class="form-control" placeholder="<?php esc_html_e('Your Name', 'listingo'); ?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                            <input type="text" value="<?php echo esc_attr( $phone ); ?>" name="appointment_form[apt_mobile]" class="form-control" placeholder="<?php esc_html_e('Phone', 'listingo'); ?>">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                            <input type="email" value="<?php echo esc_attr( $email ); ?>" name="appointment_form[apt_email]" class="form-control" placeholder="<?php esc_html_e('Email', 'listingo'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="appointment_form[apt_currency_symbol]" value="<?php echo esc_attr($currency_symbol); ?>">
            <?php wp_nonce_field('sp_appointment_wizard_form_nonce', 'appointment-form-settings-update'); ?>
        </form>
        <?php
        $json['data'] = ob_get_clean();
        $json['type'] = 'success';
        $json['message'] = esc_html__('Step 2', 'listingo');
        echo json_encode($json);
        die;
    }

    add_action('wp_ajax_listingo_get_appointment_step_two', 'listingo_get_appointment_step_two');
    add_action('wp_ajax_nopriv_listingo_get_appointment_step_two', 'listingo_get_appointment_step_two');
}
/**
 * @get service price
 * @return {}
 */
if (!function_exists('listingo_get_service_data')) {
	function listingo_get_service_data($key,$user_id,$field,$db_key){
		$appointment_data = get_user_meta($user_id, 'appointment_data', true);
        $apt_data = explode("|", $appointment_data);
        $author_id = '';
        
		if (!empty($apt_data[0])) {
            $author_id = intval($apt_data[0]);
        }
		
		if( $field === 'price' ){
			$default	= 0;
		} else {
			$default	= '';
		}
		
		
		if( !empty( $author_id ) ){
			$db_data = get_user_meta($author_id, $db_key, true);
			
			if( $field === 'price' ){
				if( !empty( $db_data[$key]['price'] ) ){
					return $db_data[$key]['price'];
				} else{
					return $default;
				}
			} else if( $field === 'title' ){
				if( !empty( $db_data[$key]['title'] ) ){
					return $db_data[$key]['title'];
				} else{
					return $default;
				}
			} else if( $field === 'type' ){
				if( !empty( $db_data[$key] ) ){
					return $db_data[$key];
				} else{
					return $default;
				}
			}else if( $field === 'reason' ){
				if( !empty( $db_data[$key] ) ){
					return $db_data[$key];
				} else{
					return $default;
				}
			}
			
		} else{
			return $default;
		}
		
	}
}

/**
 * @Appointment Booking Step 3
 * @return {}
 */
if (!function_exists('listingo_get_appointment_step_three')) {

    function listingo_get_appointment_step_three() {
        global $current_user, $wp_roles, $userdata, $post;
        $json = array();

        $do_check = check_ajax_referer('sp_appointment_wizard_form_nonce', 'appointment-form-settings-update', false);
        if ($do_check == false) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No kiddies please!', 'listingo');
            echo json_encode($json);
            die;
        }

        $appointment_form_data = array();
        if (!empty($_POST['appointment_form']) && is_array($_POST['appointment_form'])) {
            $appointment_form_data = $_POST['appointment_form'];
        }

		$service_price	= listingo_get_service_data($_POST['appointment_form']['apt_services'],$current_user->ID,'price','profile_services');
		$service_title	= listingo_get_service_data($_POST['appointment_form']['apt_services'],$current_user->ID,'title','profile_services');
		$service_reason	= listingo_get_service_data($_POST['appointment_form']['apt_reasons'],$current_user->ID,'reason','appointment_reasons');
		$service_type	= listingo_get_service_data($_POST['appointment_form']['apt_types'],$current_user->ID,'type','appointment_types');
		
		$appointment_form_data['price']		= $service_price;
		$appointment_form_data['title']		= $service_title;
		$appointment_form_data['reason']	= $service_reason;
		$appointment_form_data['type']		= $service_type;
		
        update_user_meta($current_user->ID, 'appointment_form', $appointment_form_data);

        //Generate the key which is send by email to user.
        $key_hash = rand(1000, 9999); // random 4 digit code
        update_user_meta($current_user->ID, 'appointment_form_hash', $key_hash);

        if (class_exists('ListingoProcessEmail')) {
            //Send Email
            $email_helper = new ListingoProcessEmail();
            $emailData = array();
            $emailData['user_id'] = $current_user->ID;
            $emailData['appt_hash'] = $key_hash;
			$emailData['apt_name']  = $appointment_form_data['apt_name'];
            $emailData['apt_email'] = $appointment_form_data['apt_email'];
            $email_helper->process_appt_authentication_email($emailData);
          
        }
        ob_start();
		
        ?>
        <form class="tg-formbookappointment tg-form-appointment-wizard-auth">
            <div class="form-group">
                <div class="tg-heading">
                    <h3><?php esc_html_e('Authentication Required', 'listingo'); ?></h3>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <p>Você receberá no seu e-mail um Código de Autenticação</p>
              </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <input type="text" name="appointment_authenticate" class="form-control" placeholder="<?php esc_html_e('Authentication Code', 'listingo'); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <input type="password" name="appointment_password" class="form-control" placeholder="<?php esc_html_e('Type Password', 'listingo'); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <input type="password" name="appointment_confirm_password" class="form-control" placeholder="<?php esc_html_e('Retype Password', 'listingo'); ?>">
                    </div>
                </div>
            </div>
            <?php wp_nonce_field('sp_appointment_wizard_auth_nonce', 'sp-appointment-auth-settings'); ?>
        </form>
        <?php
        $json['data'] = ob_get_clean();
        $json['type'] = 'success';
        $json['message'] = esc_html__('Step 3', 'listingo');
        echo json_encode($json);
        die;
    }

    add_action('wp_ajax_listingo_get_appointment_step_three', 'listingo_get_appointment_step_three');
    add_action('wp_ajax_nopriv_listingo_get_appointment_step_three', 'listingo_get_appointment_step_three');
}

/**
 * @get booking product ID
 * @return {}
 */
if (!function_exists('listingo_get_booking_product_id')) {

    function listingo_get_booking_product_id() {
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'order' => 'DESC',
			'orderby' => 'ID',
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1
		);


		$meta_query_args[] = array(
			'key' 			=> '_listingo_appointment',
			'value' 		=> 'yes',
			'compare' 		=> '=',
		);
		
		$query_relation 		= array('relation' => 'AND',);
		$meta_query_args 		= array_merge($query_relation, $meta_query_args);
		$args['meta_query'] 	= $meta_query_args;
		
		$booking_product = get_posts($args);
		
		if (!empty($booking_product)) {
            return (int) $booking_product[0]->ID;
        } else{
			 return 0;
		}
		
	}
}


/**
 * @update booking defautl product
 * @return {}
 */
if (!function_exists('listingo_update_booking_product')) {

    function listingo_update_booking_product() {
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'order' => 'DESC',
			'orderby' => 'ID',
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1
		);


		$meta_query_args[] = array(
			'key' 			=> '_listingo_appointment',
			'value' 		=> 'yes',
			'compare' 		=> '=',
		);
		
		$query_relation 		= array('relation' => 'AND',);
		$meta_query_args 		= array_merge($query_relation, $meta_query_args);
		$args['meta_query'] 	= $meta_query_args;
		
		$booking_product = get_posts($args);
		
		if (!empty($booking_product)) {
            $counter = 0;
            foreach ($booking_product as $key => $product) {
                update_post_meta($product->ID, '_listingo_appointment', 'no');
            }
        }
		
	}
}



/**
 * @Appointment Booking Step 4
 * @return {}
 */
if (!function_exists('listingo_get_appointment_step_four')) {

    function listingo_get_appointment_step_four() {
        global $current_user, $wp_roles, $userdata, $post,$woocommerce;
        $user_identity = $current_user->ID;
        $json = array();

		$apt_meta_array = array();
		/**
		 * Get appointment save meta from user meta table
		 */
		$appointment_form = get_user_meta($user_identity, 'appointment_form', true);
		$appointment_data = get_user_meta($user_identity, 'appointment_data', true);
		
		$apt_data 	= explode("|", $appointment_data);
		$author_id 	= !empty($apt_data[0]) ? intval($apt_data[0]) : '';
		$apt_time 	= !empty($apt_data[1]) ? esc_attr($apt_data[1]) : '';
		$apt_date 	= !empty($apt_data[2]) ? esc_attr($apt_data[2]) : '';
		$apt_prefix = substr($blogname, 0, 2);
		
        $do_check = check_ajax_referer('sp_appointment_wizard_auth_nonce', 'sp-appointment-auth-settings', false);
        if ($do_check == false) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No kiddies please!', 'listingo');
            echo json_encode($json);
            die;
        }

        if (!empty($_POST)) {

            $auth_code = esc_attr( $_POST['appointment_authenticate'] );
            $auth_pass = esc_attr( $_POST['appointment_password'] );
            $auth_confirm_pass = esc_attr( $_POST['appointment_confirm_password'] );
            
			//Get hash key from db
            $check_hash = get_user_meta($user_identity, 'appointment_form_hash', true);

            if (!empty($auth_code) && !empty($check_hash) && $check_hash !== $auth_code) {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Your authentication code does not match.', 'listingo');
                echo json_encode($json);
                die;
            }

            if (!wp_check_password($auth_confirm_pass, $current_user->user_pass, $user_identity)) {
                $json['type'] = 'error';
                $json['message'] = esc_html__('You have entered the wrong password.', 'listingo');
                echo json_encode($json);
                die;
            }
        }
		
		//Payments process type
		$product_id 	= listingo_get_booking_product_id();
		
		if( empty( $product_id ) ){
			$json['type'] = 'error';
			$json['message'] = esc_html__('Booking settings is missing. Please contact to site administrator for this issue. ', 'listingo');
			echo json_encode($json);
			die;
		}

		$bk_settings	= listingo_get_booking_settings();
		if( isset( $bk_settings['type'] ) && $bk_settings['type'] === 'woo' ){
			if (class_exists('WooCommerce')) {
				//Cart Update
				$cart_meta		= array();
				$product_id 	= listingo_get_booking_product_id();

				if (!empty($appointment_form)) {
					foreach ($appointment_form as $key => $apt_meta) {
						$cart_meta[$key] = $apt_meta;
					}
				}
				
				$price			= !empty( $appointment_form['price'] ) ? $appointment_form['price'] : 0;
				
				$cart_meta['apt_user_from'] 	= intval($user_identity);
				$cart_meta['apt_user_to'] 		= intval($author_id);
				$cart_meta['apt_time'] 			= esc_attr($apt_time);
				$cart_meta['apt_date'] 			= strtotime($apt_date);
				$cart_meta['apt_price']			= $price;
				
				$price_symbol	= get_woocommerce_currency_symbol();
				$bk_settings	= listingo_get_booking_settings();
				
				$admin_shares 		= 0.0;
				$provider_shares 	= 0.0;
				
				if( !empty( $price ) ){
					if( isset( $bk_settings['percentage'] ) && $bk_settings['percentage'] > 0 ){
						$admin_shares 		= $price/100*$bk_settings['percentage'];
						$provider_shares 	= $price - $admin_shares;
						$admin_shares 		= number_format($admin_shares, 2);
						$provider_shares 	= number_format($provider_shares , 2);
					}
				}
				
				if( isset( $bk_settings['percentage'] ) && $bk_settings['percentage'] > 0 ){
					$cart_meta['apt_admin_shares']		= $admin_shares;
					$cart_meta['apt_provider_shares']	= $provider_shares;

					$cart_data = array(
						'product_id' 		=> $product_id,
						'cart_data'     	=> $cart_meta,
						'payment_type'     	=> 'booking',
						'admin_shares'     	=> $price_symbol.$admin_shares,
						'provider_shares'   => $price_symbol.$provider_shares,
					);
				} else{
					$cart_data = array(
						'product_id' 		=> $product_id,
						'cart_data'     	=> $cart_meta,
						'payment_type'     	=> 'booking',
					);
				}
				
				$woocommerce->cart->empty_cart();
				$cart_item_data = $cart_data;
				WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data); 
				
				$json['type'] 		= 'success';
				$json['mode'] 		= 'woo';
				$json['appt_data']  = '<p class="apt-please-wait">'.esc_html__('Please wait....', 'listingo').'</p>';
				
				$json['checkout_url'] = esc_url($woocommerce->cart->get_checkout_url());
				$json['message'] 	= esc_html__('You are redirecting to checkout for payments.', 'listingo');
				echo json_encode($json);
				die;
				
			} else {
				$json = array();
				$json['type'] = 'error';
				$json['message'] = esc_html__('Please install WooCommerce plugin to process this order', 'listingo');
				die;
			}
			
		} else if( isset( $bk_settings['type'] ) && $bk_settings['type'] === 'adaptive' ){
			$cart_meta		= array();
			$product_id 	= listingo_get_booking_product_id();

			if (!empty($appointment_form)) {
				foreach ($appointment_form as $key => $apt_meta) {
					$cart_meta[$key] = $apt_meta;
				}
			}

			$price			= !empty( $appointment_form['price'] ) ? $appointment_form['price'] : 0;

			$cart_meta['apt_user_from'] 	= intval($user_identity);
			$cart_meta['apt_user_to'] 		= intval($author_id);
			$cart_meta['apt_time'] 			= esc_attr($apt_time);
			$cart_meta['apt_date'] 			= strtotime($apt_date);
			$cart_meta['apt_price']			= $price;

			$price_symbol	= get_woocommerce_currency_symbol();
			$bk_settings	= listingo_get_booking_settings();
			
			$admin_shares 		= 0.0;
			$provider_shares 	= 0.0;

			if( !empty( $price ) ){
				if( isset( $bk_settings['percentage'] ) && $bk_settings['percentage'] > 0 ){
					$admin_shares 		= $price/100*$bk_settings['percentage'];
					$provider_shares 	= $price - $admin_shares;
					
				}
			}
			
			$provider_shares 	= $price - $admin_shares;
			$admin_shares 		= $price - $provider_shares;
			
			$cart_meta['apt_admin_shares']		= number_format($admin_shares, 2);
			$cart_meta['apt_provider_shares']	= number_format($provider_shares , 2);
			
			update_user_meta($user_identity, 'sp_booking_cart', $cart_meta);

			$json['type'] 		= 'success';
			$json['mode'] 		= 'adaptive';
			$json['appt_data']  = get_listingo_booking_totals($cart_meta);
			$json['message'] 	= esc_html__('Please confirm you order.', 'listingo');
			echo json_encode($json);
			die;
			
		} else{
			
			$apt_prefix	= esc_html__( 'Order', 'listingo' );
			if (function_exists('fw_get_db_settings_option')) {
				$apt_prefix = fw_get_db_settings_option('appointment_no_prefix');
			}

			$appointment_no = esc_attr($apt_prefix) . '-' . sp_unique_increment(5);

			//Add Booking
			$appointment = array(
				'post_title' 	=> $appointment_no,
				'post_status' 	=> 'pending',
				'post_author' 	=> $user_identity,
				'post_type' 	=> 'sp_appointments',
				'post_date' 	=> current_time('Y-m-d h')
			);

			$post_id = wp_insert_post($appointment);
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			if (!empty($appointment_form)) {
				foreach ($appointment_form as $key => $apt_meta) {
					$apt_meta_array[$key] = $apt_meta;
				}
			}

			$apt_meta_array['apt_number'] 	 = esc_attr($appointment_no);
			$apt_meta_array['apt_user_from'] = intval($user_identity);
			$apt_meta_array['apt_user_to']   = intval($author_id);
			$apt_meta_array['apt_time'] 	 = esc_attr($apt_time);
			$apt_meta_array['apt_date'] 	 = strtotime($apt_date);

			//Update post meta
			foreach ($apt_meta_array as $key => $value) {
				update_post_meta($post_id, $key, $value);
			}
			
			//array data
			update_post_meta($post_id, 'cus_appointment_data', $apt_meta_array);

			//Get Appointment Data
			$appointment_json_data = listingo_get_appointment_data($post_id);

			//Send Confirmation Mail
			if (class_exists('ListingoProcessEmail')) {
				//Send Email
				$email_helper = new ListingoProcessEmail();
				$emailData = array();
				$emailData['post_id'] = $post_id;
				$email_helper->process_appt_confirmation_email($emailData);
				$email_helper->process_appt_confirmation_email_from_admin($emailData);

				update_user_meta($current_user->ID, 'appointment_form_hash', '');
				update_user_meta($current_user->ID, 'appointment_key', '');
				update_user_meta($current_user->ID, 'appointment_data', '');
			}
			
			$json['type'] 		= 'success';
			$json['mode'] 		= 'disabled';
			$json['appt_data']  = $appointment_json_data;
			$json['message'] 	= esc_html__('Step 4', 'listingo');
			echo json_encode($json);
			die;
		} 
    }

    add_action('wp_ajax_listingo_get_appointment_step_four', 'listingo_get_appointment_step_four');
    add_action('wp_ajax_nopriv_listingo_get_appointment_step_four', 'listingo_get_appointment_step_four');
}

/**
 * @Get booking checkout page
 * @return {data}
 */
if (!function_exists('listingo_get_user_billing_address')) {
    function listingo_get_user_billing_address($user_id) {
		$billing	= array();
		$billing['first_name']  = get_user_meta($user_id, 'first_name', true);
		$billing['last_name']	= get_user_meta($user_id, 'last_name', true);
		$billing['company']		= get_user_meta($user_id, 'company', true);
		$billing['phone']		= get_user_meta($user_id, 'phone', true);
		$billing['address_1']	= get_user_meta($user_id, 'address', true);
		$billing['address_2']	= get_user_meta($user_id, 'address', true);
		$billing['city']		= get_user_meta($user_id, 'city', true);
		$billing['state']		= '';
		$billing['postcode']	= get_user_meta($user_id, 'zip', true);
		$billing['country']		= get_user_meta($user_id, 'country', true);
		
		return $billing;
	}
}


/**
 * @Get booking checkout page
 * @return {data}
 */
if (!function_exists('get_listingo_booking_totals')) {
    function get_listingo_booking_totals($data) {
		ob_start();
		
		$cart_items	= apply_filters('listingo_get_booking_meta', $data);		
		?>
		<div class="col-md-12">
			<div class="cart-data-wrap tg-haslayout adaptive-pay-wrap">
			  <h3><span class="cus-quantity"><?php esc_html_e('Your order','listingo');?></span></h3>
			  <div class="selection-wrap">
				<?php 
					$counter	= 0;
					foreach( $cart_items as $key => $value ){
						$counter++;
					?>
						<div class="cart-style"> 
							<span class="style-lable"><?php echo listingo_get_package_features($key);?></span> 
							<span class="style-name"><?php echo esc_attr( $value );?></span> 
						</div>
					<?php }?>
			  </div>
			  <div class="sp-payments-gateways tg-haslayout">
			  	<ul class="payment-withdrawal-options">
				  <li class="payment-withdrawal">
					<div class="withdrawal-wrap">
				  	  <form class="adaptive_payment_type" method="post">
				  	  	  <input class="service-paypal" checked="checked" id="withdrawal_paypal" type="radio" value="paypal" name="type">
						  <label for="withdrawal_paypal" data-key="withdrawal_paypal">
							<div class="withdrawal_title"><img src="<?php echo get_template_directory_uri();?>/images/withdrawal/paypal.png" /></div>
						  </label>
				  	  </form>
					</div>
				  </li>
				 </ul>
			  </div>
			  <a href="javascript:;" class="tg-btn tg-btnnext confirm-adaptive-booking"><?php esc_html_e('Confirm Order','listingo');?></a>
			</div>
		 </div>		
		<?php
		return ob_get_clean();
	}
}

/**
 * @Get appointment post meta
 * @return {data}
 */
if (!function_exists('listingo_get_appointment_data')) {

    function listingo_get_appointment_data($post_id) {
        global $post;

        //Get all appointent post meta
        $apt_status = get_post_status($post_id);
        $apt_services = get_post_meta($post_id, 'apt_services', true);
        $apt_types = get_post_meta($post_id, 'apt_types', true);
        $apt_reasons = get_post_meta($post_id, 'apt_reasons', true);
        $apt_description = get_post_meta($post_id, 'apt_description', true);
        $apt_name = get_post_meta($post_id, 'apt_name', true);
        $apt_mobile = get_post_meta($post_id, 'apt_mobile', true);
        $apt_email = get_post_meta($post_id, 'apt_email', true);
        $apt_currency_symbol = get_post_meta($post_id, 'apt_currency_symbol', true);
        $apt_number = get_post_meta($post_id, 'apt_number', true);
        $apt_user_from = get_post_meta($post_id, 'apt_user_from', true);
        $apt_user_to = get_post_meta($post_id, 'apt_user_to', true);

        $booking_services = get_user_meta($apt_user_to, 'profile_services', true);
        $booking_types = get_user_meta($apt_user_to, 'appointment_types', true);
        $booking_reasons = get_user_meta($apt_user_to, 'appointment_reasons', true);

        $apt_time = get_post_meta($post_id, 'apt_time', true);
        $apt_date = get_post_meta($post_id, 'apt_date', true);
        $apt_user_from = get_user_by('id', intval($apt_user_from));
        $apt_user_to = get_user_by('id', intval($apt_user_to));

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $time = explode('-', $apt_time);

        ob_start();
        ?>
        <div class="form-group">
            <div class="tg-heading">
                <h3><?php esc_html_e('Appointment Summary', 'listingo'); ?></h3>
            </div>
            <div class="tg-reminderemail">
                <span><?php esc_html_e('Appointment ID : ', 'listingo'); ?>&nbsp;<?php echo esc_attr($apt_number); ?></span>
            </div>
        </div>
        <ul class="tg-appointmentsummry">
            <li>
                <strong><?php esc_html_e('Date : ', 'listingo'); ?></strong>
                <span><?php echo date_i18n($date_format, $apt_date); ?></span>
            </li>
            <?php if (!empty($time[0]) && !empty($time[1])) { ?>
                <li>
                    <strong><?php esc_html_e('Meeting Time : ', 'listingo'); ?></strong>
                    <span><?php echo date_i18n($time_format, strtotime('2016-01-01 ' . $time[0])); ?>&nbsp;-&nbsp;<?php echo date_i18n($time_format, strtotime('2016-01-01 ' . $time[1])); ?></span>
                </li>
            <?php } ?>
            <?php if (!empty($booking_services[$apt_services])) { ?>
                <li>
                    <strong><?php esc_html_e('Service : ', 'listingo'); ?></strong>
                    <span><?php echo esc_attr($booking_services[$apt_services]['title']); ?></span>
                </li>
            <?php } ?>
            <?php if (!empty($booking_services[$apt_services])) { ?>
                <li>
                    <strong><?php esc_html_e('Appointment Fee : ', 'listingo'); ?></strong>
                    <span><?php echo listingo_format_price($booking_services[$apt_services]['price']);?></span>
                </li>
            <?php } ?>
            <?php if (!empty($booking_types[$apt_types])) { ?>
                <li>
                    <strong><?php esc_html_e('Appointment Type : ', 'listingo'); ?></strong>
                    <span><?php echo esc_attr($booking_types[$apt_types]); ?></span>
                </li>
            <?php } ?>
            <?php if (!empty($booking_reasons[$apt_reasons])) { ?>
                <li>
                    <strong><?php esc_html_e('Reason For Visit : ', 'listingo'); ?></strong>
                    <span><?php echo esc_attr($booking_reasons[$apt_reasons]); ?></span>
                </li>
            <?php } ?>

            <?php if (!empty($apt_status)) { ?>
                <li>
                    <strong><?php esc_html_e('Booking Status : ', 'listingo'); ?></strong>
                    <span><?php echo esc_attr(ucwords($apt_status)); ?></span>
                </li>
            <?php } ?>
            <li>
                <strong><?php esc_html_e('Description : ', 'listingo'); ?></strong>
                <span>
                    <div class="tg-description">
                        <?php echo wp_kses_post(wpautop(do_shortcode($apt_description))); ?>
                    </div>
                </span>
            </li>
        </ul>
        <?php
        return ob_get_clean();
    }

}
/**
 * @check if slot is booked
 * @return {data}
 */
if (!function_exists('listingo_check_booked_slot')) {
    function listingo_check_booked_slot($slot,$slot_date,$user_id) {

		$time_slots = array();
        $time_slots = get_user_meta($user_id, 'default_slots', true);
        $time_format = get_option('time_format');
		$day_name 	   = strtolower(date('l',strtotime($slot_date)));

		//Get booked Appointments
		$year  	  = date_i18n('Y',strtotime($slot_date));
		$month 	  = date_i18n('m',strtotime($slot_date));
		$day_no   = date_i18n('d',strtotime($slot_date));

		$start_timestamp = strtotime($year.'-'.$month.'-'.$day_no.' 00:00:00');
		$end_timestamp   = strtotime($year.'-'.$month.'-'.$day_no.' 23:59:59');
		

		$args 		= array('posts_per_page' => -1, 
							 'post_type' => 'sp_appointments', 
							 'post_status' => array('publish', 'pending'), 
							 'ignore_sticky_posts' => 1,
							 'meta_query' => array(
									array(
										'key'     => 'apt_date',
										'value'   => array( $start_timestamp, $end_timestamp ),
										'compare' => 'BETWEEN'
									),
									array(
										'key'     => 'apt_user_to',
										'value'   => $user_id,
										'compare' => '='
									),
									array(
										'key'     => 'apt_time',
										'value'   => $slot,
										'compare' => '='
									),
								)
							);
			

		$query 		= new WP_Query($args);
    
		$appointments_array	= array();
		if( $query->have_posts() ) {
			while($query->have_posts()) : $query->the_post();
				global $post;
				$apt_time       = get_post_meta($post->ID, 'apt_time',true);
				$appointments_array[$slot]['apt_time'][] = $apt_time;
			endwhile; wp_reset_postdata(); 
		}
		
		$json	= array();
		$json['available']	= 'no';
		$json['remaining']	= 0;
		
		if( !empty( $time_slots[$day_name][$slot] ) ) {
			$total_available	= !empty( $time_slots[$day_name][$slot] ) ?  $time_slots[$day_name][$slot] : 0;
			
			$booked	= 0;
			if( isset( $appointments_array[$slot]['apt_time'] ) && !empty( $appointments_array[$slot]['apt_time'] ) ){
				$booked	= listingo_count_items( $appointments_array[$slot]['apt_time'] );
				
			}
			
			if( empty( $booked ) ){
				$json['available']	= 'yes';
				$json['remaining']	= $total_available;
			} else if( !empty( $booked ) && $booked < $total_available ){
				$json['available']	= 'yes';
				$json['remaining']	= $total_available - $booked;
			}else{
				$json['available']	= 'no';
				$json['remaining']	= 0;
			}
		}
		
		return $json;
	}
}

/**
 * @check if slot is booked
 * @return {data}
 */
if (!function_exists('listingo_get_booking_settings')) {
    function listingo_get_booking_settings() {
		$settings	= array();
		$bookings	= array();
		
		if (function_exists('fw_get_db_settings_option')) {
            $bookings = fw_get_db_settings_option('booking_settings');
        }

		if( isset( $bookings['gadget'] ) && $bookings['gadget'] === 'enable' ){
			$settings['type']			= !empty( $bookings['enable']['pay']['type'] )  ? $bookings['enable']['pay']['type'] : '';
			$settings['minamount']		= !empty( $bookings['enable']['pay']['woo']['min_amount'] )  ? $bookings['enable']['pay']['woo']['min_amount'] : '';
			$settings['hide_wallet']	= !empty( $bookings['enable']['pay']['woo']['hide_wallet'] )  ? $bookings['enable']['pay']['woo']['hide_wallet'] : '';
			$settings['percentage']		= !empty( $bookings['enable']['percentage'] )  ? $bookings['enable']['percentage'] : '';
			$settings['is_enable']		= 'yes';
			$settings['paypal_app']		= !empty( $bookings['enable']['pay']['adaptive']['paypal_app'] )  ? $bookings['enable']['pay']['adaptive']['paypal_app'] : '';
			$settings['paypal_email']		= !empty( $bookings['enable']['pay']['adaptive']['paypal_email'] )  ? $bookings['enable']['pay']['adaptive']['paypal_email'] : '';
			$settings['paypal_fee']			= !empty( $bookings['enable']['pay']['adaptive']['paypal_fee'] )  ? $bookings['enable']['pay']['adaptive']['paypal_fee'] : '';
			$settings['paypal_mode']		= !empty( $bookings['enable']['pay']['adaptive']['paypal_mode'] )  ? $bookings['enable']['pay']['adaptive']['paypal_mode'] : '';
			$settings['paypal_username']	= !empty( $bookings['enable']['pay']['adaptive']['paypal_username'] )  ? $bookings['enable']['pay']['adaptive']['paypal_username'] : '';
			$settings['paypal_password']	= !empty( $bookings['enable']['pay']['adaptive']['paypal_password'] )  ? $bookings['enable']['pay']['adaptive']['paypal_password'] : '';
			
			$settings['paypal_signature']	= !empty( $bookings['enable']['pay']['adaptive']['paypal_signature'] )  ? $bookings['enable']['pay']['adaptive']['paypal_signature'] : '';
			$settings['paypal_payment_name']= !empty( $bookings['enable']['pay']['adaptive']['paypal_payment_name'] )  ? $bookings['enable']['pay']['adaptive']['paypal_payment_name'] : '';
		}
		
		return $settings;
		
	}
}