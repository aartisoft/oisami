<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class pw_class_woocommerce_gift_discunt_cart
{

    public function __construct()
    {
        $this->rule_check = array();

        $this->apply_rule = array();

        $this->gift_item_key = array();

        $this->arr_cart = array();

        $this->item_cart = array();

        $this->cart = array();

        //$this->check_rule();
        //add_action( 'woocommerce_loaded', array( $this, 'woocommerce_loaded_function' ) );
        add_action('wp_head', array($this, 'adjust_cart_rule'));
        add_action('wp_head', array($this, 'pw_insert_gift_cart'));

        //For display in cart
        add_action('woocommerce_cart_contents', array($this, 'woocommerce_cart_contents_function'));
        add_action('woocommerce_review_order_after_cart_contents', array($this, 'review_order_after_cart_contents_function'));
        add_action('woocommerce_new_order', array($this, 'add_gift_to_order'), 10, 1);

        add_action('woocommerce_cart_calculate_fees', array($this, 'wc_add_surcharge'));  //Add For Adon fee
        //  add_action('wp_head', array($this, 'wc_add_surcharge'));  //Add For Adon fee

    }

    public function woocommerce_loaded_function()
    {
        add_action('woocommerce_cart_loaded_from_session', array($this, 'on_cart_loaded_from_session'), 99, 1);
    }

    public function adjust_cart_rule()
    {
        $setting = get_option("pw_gift_options");
        echo "<script type='text/javascript'>
				jQuery(document).ready(function() {
					jQuery( document.body ).on( 'updated_cart_totals', function(){
						if(jQuery('html').find('.owl-carousel').length){
							jQuery('.owl-carousel').owlCarousel('destroy'); 
							jQuery('.owl-carousel').owlCarousel({
								  margin : " . $setting['pw_item_marrgin'] . " , 
								  loop:true,
								  dots:" . $setting['pw_show_pagination'] . ",
								  nav:" . $setting['pw_show_control'] . ",
								  slideBy: " . $setting['pw_item_per_slide'] . ",
								  autoplay:" . $setting['pw_auto_play'] . ",
								  autoplayTimeout : " . $setting['pw_slide_speed'] . ",
								  rtl: " . (isset($setting['pw_slide_rtl']) ? $setting['pw_slide_rtl'] : false) . ",
								  responsive:{
									0:{
										items:1
									},
									600:{
										items:2
									},
									1000:{
										items:" . $setting['pw_item_per_view'] . "
									}
								},
								autoplayHoverPause: true,
								navText: [ '>', '<' ]
							});
						}
					});	
                })
        </script>";
        if (!$this->check_rule()) {
            return;
        }

        if (is_array($this->gift_item_key) && count($this->gift_item_key) > 0) {
            add_action('woocommerce_after_cart_table', array($this, 'pw_woocommerce_after_cart_table_function'));
            //use for Theme Avada and ....
            //add_action( 'wp_footer',array($this,'add_jqury_gifts' ));
        }

    }

    function wc_add_surcharge()
    {
        if (!defined('plugin_dir_path_gift_fee')) {
            return;
        }
        global $woocommerce;
        if (is_admin() && !defined('DOING_AJAX'))
            return;

        if (!$this->check_rule()) {
            return;
        }
        $flag_check = false;
        foreach ($this->gift_item_key as $gift_item_key => $cart_item) {
            if (is_array($woocommerce->session->wc_free_select_gift) &&
                in_array($cart_item['key'], $woocommerce->session->wc_free_select_gift)
            ) {
                $flag_check = true;
                break;
            } else
                $flag_check = false;
        }
        if ($flag_check == false) {
            unset($woocommerce->session->wc_free_select_gift);
            $woocommerce->session->wc_free_select_gift = "";
            $woocommerce->session->wc_free_select_gift = array();

            return;
        }

        $retrived_group_input_value = WC()->session->get('group_order_data');
        //$retrived_group_input_value = $woocommerce->session->group_order_data;
//        print_r($retrived_group_input_value);
        if (is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift) > 0 && is_array($this->gift_item_key) && count($this->gift_item_key) > 0) {
            $sum_value = 0;
            foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) {
                $gift_index = $this->gift_item_key[$index];
//                print_r($gift_index);
                $count = 1;
                $count = $retrived_group_input_value[$gift_index['key']]['q'];
                if (has_filter('add_fee_for_addon')) {
                    $value = apply_filters('add_fee_for_addon', $gift_index['item']);
                    $sum_value += $count * $value;
                }
            }
            if ($sum_value > 0) {
                $woocommerce->cart->add_fee('Fee', $sum_value, true, 'standard');
            }
        }
    }

    public function check_rule()
    {
        global $woocommerce, $wpdb, $product;
        $this->cart['amount'] = 0;
        $this->cart['quantities'] = "";
        $query_meta_query = array('relation' => 'AND');
        $query_meta_query[] = array(
            'key' => 'status',
            'value' => "active",
            'compare' => '=',
        );
        //$woocommerce->cart->cart_contents
        $this->item_cart = WC()->cart->cart_contents;
        //print_r($this->item_cart);
        $matched_products = get_posts(
            array(
                'post_type' => 'pw_gift_rule',
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
                'no_found_rows' => true,
                'orderby' => 'modified',
                'meta_query' => $query_meta_query,
            )
        );

        //$products=array();
        $quantity = 0;
        foreach ($this->item_cart as $cart_item_key => $cart_item) {
            //echo $cart_item['data']->id.',<br>';
            $quantity = (isset($cart_item['quantity']) && $cart_item['quantity']) ? $cart_item['quantity'] : 1;
            // $this->cart['amount'] += $cart_item['data']->get_price() * $quantity;
        }
        $this->cart['amount'] = $this->calculate_cart_subtotal();
        $this->cart['quantities'] = $woocommerce->cart->cart_contents_count;


        foreach ($matched_products as $pr) {
            $is_ok = true;
            $product_depends = $category_depend = $pw_cart_amount = $criteria_nb_products_min = $criteria_nb_products_max = $pw_cart_amount_min = $pw_cart_amount_max = $brand_depends = $pw_brand_depends = $gift_auto_to_cart = $pw_id = $disable_if = "";
            $gift_auto_to_cart = get_post_meta($pr, 'gift_auto_to_cart', true);
            $brand_depends = $pw_brand_depends = $pw_brand_depends_method = '';
            if (defined('plugin_dir_url_pw_woo_brand')) {
                $brand_depends = get_post_meta($pr, 'brand_depends', true);
                $pw_brand_depends = get_post_meta($pr, 'pw_brand_depends', true);
                $pw_brand_depends_method = get_post_meta($pr, 'pw_brand_depends_method', true);
            }
            $pw_cart_amount = get_post_meta($pr, 'pw_cart_amount', true);
            $pw_cart_amount_min = get_post_meta($pr, 'pw_cart_amount_min', true);
            $pw_cart_amount_max = get_post_meta($pr, 'pw_cart_amount_max', true);
            $criteria_nb_products_min = get_post_meta($pr, 'criteria_nb_products_min', true);
            $criteria_nb_products_max = get_post_meta($pr, 'criteria_nb_products_max', true);
            $criteria_nb_products_op = get_post_meta($pr, 'criteria_nb_products_op', true);
			$criteria_nb_products = get_post_meta($pr, 'criteria_nb_products', true);
			$criteria_nb_products = ($criteria_nb_products != "" ? $criteria_nb_products : 0);
            $cart_amount_op = get_post_meta($pr, 'cart_amount_op', true);
            $disable_if = get_post_meta($pr, 'disable_if', true);
            $pw_number_gift_allowed = get_post_meta($pr, 'pw_number_gift_allowed', true);
            $pw_cart_amount = ($pw_cart_amount != "" ? $pw_cart_amount : 0);
            $r = "";
            $pw_to = strtotime(get_post_meta($pr, 'pw_to', true));
            $pw_from = strtotime(get_post_meta($pr, 'pw_from', true));

            $pw_gifts = get_post_meta($pr, 'pw_gifts', true);
            $pw_gifts_metod = get_post_meta($pr, 'pw_gifts_metod', true);
            $pw_gifts_category = get_post_meta($pr, 'pw_gifts_category', true);

            $users_depends = get_post_meta($pr, 'users_depends', true);
            $pw_users = get_post_meta($pr, 'pw_users', true);

            $pw_roles = get_post_meta($pr, 'pw_roles', true);
            $roles_depends = get_post_meta($pr, 'roles_depends', true);


            $is_coupons = get_post_meta($pr, 'is_coupons', true);
            $order_op_count = get_post_meta($pr, 'order_op_count', true);
            $order_count = get_post_meta($pr, 'order_count', true);


            $pw_product_depends = get_post_meta($pr, 'pw_product_depends', true);
            $product_depends = get_post_meta($pr, 'product_depends', true);
            $pw_product_depends_method = get_post_meta($pr, 'pw_product_depends_method', true);
            $pw_category_depends_method = get_post_meta($pr, 'pw_category_depends_method', true);
            $exclude_product_depends = get_post_meta($pr, 'exclude_product_depends', true);
            $pw_exclude_product_depends = get_post_meta($pr, 'pw_exclude_product_depends', true);

            $pw_category_depends = get_post_meta($pr, 'pw_category_depends', true);
            $exclude_pw_category_depends = get_post_meta($pr, 'exclude_pw_category_depends', true);
            $category_depends = get_post_meta($pr, 'category_depends', true);
            $exclude_category_depends = get_post_meta($pr, 'exclude_category_depends', true);

            $gift_auto_to_cart = get_post_meta($pr, 'gift_auto_to_cart', true);
            $pw_limit_per_rule = get_post_meta($pr, 'pw_limit_per_rule', true);
            $pw_limit_cunter = get_post_meta($pr, 'pw_limit_cunter', true);
            $pw_limit_per_user = get_post_meta($pr, 'pw_limit_per_user', true);
            $pw_register_user = get_post_meta($pr, 'pw_register_user', true);
            $schedule_type = get_post_meta($pr, 'schedule_type', true);
            $pw_weekly = get_post_meta($pr, 'pw_weekly', true);
            $pw_daily = get_post_meta($pr, 'pw_daily', true);
            $pw_monthly = get_post_meta($pr, 'pw_monthly', true);
            $can_several_gift = get_post_meta($pr, 'can_several_gift', true);

            //$blogtime = strtotime(current_time( 'mysql' ));
            //$cart_count=$woocommerce->cart->cart_contents_count;
            //$quantities = $this->count_in_cart($rule);

            $this->rule_check[$pr] = array(
                "pw_id" => $pr,
                "disable_if" => $disable_if,
                "pw_number_gift_allowed" => ($pw_number_gift_allowed <= 0 ? 1 : $pw_number_gift_allowed),
                "pw_gifts" => $pw_gifts,
                "pw_gifts_metod" => $pw_gifts_metod,
                "is_coupons" => $is_coupons,
                "order_count" => $order_count,
                "order_op_count" => $order_op_count,
                "pw_gifts_category" => $pw_gifts_category,
                "pw_roles" => $pw_roles,
                "roles_depends" => $roles_depends,
                "users_depends" => $users_depends,
                "pw_users" => $pw_users,
                "pw_to" => $pw_to,
                "pw_from" => $pw_from,
                "criteria_nb_products" => $criteria_nb_products,
                "pw_cart_amount" => $pw_cart_amount,
                "pw_cart_amount_min" => $pw_cart_amount_min,
                "pw_cart_amount_max" => $pw_cart_amount_max,
                "criteria_nb_products_min" => $criteria_nb_products_min,
                "criteria_nb_products_max" => $criteria_nb_products_max,
                "criteria_nb_products_op" => $criteria_nb_products_op,
                "cart_amount_op" => $cart_amount_op,
                "product_depends" => $product_depends,
                "pw_product_depends" => $pw_product_depends,
                "pw_product_depends_method" => $pw_product_depends_method,
                "exclude_product_depends" => $exclude_product_depends,
                "pw_exclude_product_depends" => $pw_exclude_product_depends,
                "category_depends" => $category_depends,
                "exclude_category_depends" => $exclude_category_depends,
                "pw_category_depends" => $pw_category_depends,
                "exclude_pw_category_depends" => $exclude_pw_category_depends,
                "pw_category_depends_method" => $pw_category_depends_method,
                "brand_depends" => $brand_depends,
                "pw_brand_depends" => $pw_brand_depends,
                "pw_brand_depends_method" => $pw_brand_depends_method,
                "gift_auto_to_cart" => $gift_auto_to_cart,
                "pw_limit_per_rule" => $pw_limit_per_rule,
                "pw_limit_per_user" => $pw_limit_per_user,
                "pw_limit_cunter" => $pw_limit_cunter,
                "pw_register_user" => $pw_register_user,
                "schedule_type" => $schedule_type,
                "pw_weekly" => $pw_weekly,
                "pw_daily" => $pw_daily,
                "pw_monthly" => $pw_monthly,
                "can_several_gift" => ($can_several_gift == 'yes' ? 'yes' : 'no'),
            );
        }

        $rules = array();
        foreach ($this->rule_check as $rules_item => $rule) {
            $can_applied = false;

            if ($this->check_candition_rules($rule)) {

                $this->apply_rule[$rules_item] = $rule;
                if ($this->check_candition_rules_product($rule)) {

                    if ($this->check_candition_rules_category_product($rule)) {
                        if ($this->check_candition_rules_brand_product($rule)) {
                            //for each (if you want don't apply later rule)
                            foreach ($this->item_cart as $cart_item_key => $cart_item) {
                                if ($rule['pw_gifts_metod'] == "product") {
                                    if ($rule['pw_gifts'] != "") {
                                        //$this->gift_item_key[]=$rule["pw_gifts"];
                                        $count_auto_add = 0;
                                        foreach ($rule['pw_gifts'] as $gift) {
                                            $product_get = "";
                                            $product_get = wc_get_product($gift);
                                            if ($product_get->is_in_stock()) {
                                                $id = "";
                                                //$id=md5(uniqid());
                                                $id = $rule['pw_id'] . '-' . $gift;

                                                $this->gift_item_key[$id] = array(
                                                    "item" => $gift,
                                                    "rule_id" => $rule['pw_id'],
                                                    "key" => $id,
                                                    "disable_if" => $rule['disable_if'],
                                                    "pw_number_gift_allowed" => $rule['pw_number_gift_allowed'],
                                                    "can_several_gift" => $rule['can_several_gift'],
                                                );
                                                if ($rule['gift_auto_to_cart'] == "yes" && $count_auto_add < $rule['pw_number_gift_allowed']) {
                                                    $count_auto_add++;
                                                    $this->pw_insert_gift_cart($id);
                                                }
                                            }
                                        }
                                        $can_applied = true;
                                        break;
                                    }
                                } else {

                                    if ($rule['pw_gifts_category'] != "") {
                                        $query_meta_query[] = array(
                                            array(
                                                'taxonomy' => 'product_cat',
                                                'field' => 'id',
                                                'terms' => $rule['pw_gifts_category']
                                            )
                                        );
                                        $matched_products = get_posts(
                                            array(
                                                'post_type' => 'product',
                                                'numberposts' => -1,
                                                'post_status' => 'publish',
                                                'fields' => 'ids',
                                                'no_found_rows' => true,
                                                'tax_query' => $query_meta_query,
                                            )
                                        );
                                        $count_auto_add = 0;
                                        foreach ($matched_products as $gift) {
                                            $id = "";

                                            //$id=md5(uniqid());
                                            $product_get = wc_get_product($gift);
                                            if ($product_get->is_in_stock()) {
                                                $id = $rule['pw_id'] . '-' . $gift;

                                                $this->gift_item_key[$id] = array(
                                                    "item" => $gift,
                                                    "key" => $id,
                                                    "rule_id" => $rule['pw_id'],
                                                    "disable_if" => $rule['disable_if'],
                                                    "pw_number_gift_allowed" => $rule['pw_number_gift_allowed'],
                                                    "can_several_gift" => $rule['can_several_gift'],
                                                );
                                                if ($rule['gift_auto_to_cart'] == "yes" && $count_auto_add < $rule['pw_number_gift_allowed']) {
                                                    $count_auto_add++;
                                                    $this->pw_insert_gift_cart($id);
                                                }
                                            }
                                        }
                                        $can_applied = true;
                                        break;
                                    }
                                }
                            }
                        }//End for each (if you want don't apply later rule)
                    }
                }
            }
            if (!$can_applied) {
                unset($this->apply_rule[$rules_item]);
            }
        }

        if (is_array($this->gift_item_key) && count($this->gift_item_key) > 0) {
            return true;
        }

        return false;
    }

    public function review_order_after_cart_contents_function()
    {
        global $woocommerce;

        if (!$this->check_rule()) {
            return;
        }
        $setting = get_option("pw_gift_options");
        /*         * ***************  if was issue , can use from this **************** */
        /* */
        $retrived_group_input_value = WC()->session->get('group_order_data');
        //$retrived_group_input_value = $woocommerce->session->group_order_data;
        $flag_check = false;
        foreach ($this->gift_item_key as $gift_item_key => $cart_item) {
            if (is_array($woocommerce->session->wc_free_select_gift) &&
                in_array($cart_item['key'], $woocommerce->session->wc_free_select_gift)
            ) {
                $flag_check = true;
                break;
            } else
                $flag_check = false;
        }
        if ($flag_check == false) {
            unset($woocommerce->session->wc_free_select_gift);
            $woocommerce->session->wc_free_select_gift = "";
            $woocommerce->session->wc_free_select_gift = array();
            WC()->session->set('group_order_data', '');
            //$woocommerce->session->group_order_data = '';
            return;
        }
        if (is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift) > 0 && is_array($this->gift_item_key) && count($this->gift_item_key) > 0) {
            foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) {
                $gift_index = "";
                $gift_index = $this->gift_item_key[$index];
                if (count($gift_index) <= 0) {
                    $arr = $woocommerce->session->wc_free_select_gift;
                    $index_r = array_search($index, $arr);
                    unset($arr[$index_r]);
                    $woocommerce->session->wc_free_select_gift = $arr;


                    unset($retrived_group_input_value[$index]);
                    WC()->session->set('group_order_data', $retrived_group_input_value);
                    //$woocommerce->session->group_order_data = $retrived_group_input_value;
                    continue;
                }
                $product = wc_get_product($gift_index['item']);
                $title = '';
                //    if ($product->product_type == 'simple') {
                $title = $product->get_title();
//                } else if ($product->product_type == 'variation') {
//                    $variation_names = "";
//                    $variant_abs = array_values($product->get_variation_attributes());
//                    foreach ($variant_abs as $var) {
//                        $variation_name = $var;
//                        if ($variation_name) {
//                            $variation_names .= ' (' . $variation_name . ')';
//                        }
//                    }
//                    $title = $product->get_title() . ' ' . $variation_names;
//                    $child = $variation_names = "";
//                    $variation_names = "";
//                }
                //$price_p = apply_filters('woocommerce_get_price_html', apply_filters('woocommerce_free_price_html', __('Free', 'pw_wc_advanced_gift')));
                $price_p = $setting['free'];
                $count = isset($retrived_group_input_value[$index]['q']) ? $retrived_group_input_value[$index]['q'] : 1;
                echo '<tr>
          <td class="product-name">' .
                    apply_filters('woocommerce_checkout_product_title', $title, $product) . ' ' .
                    '<strong class="product-quantity">' . $count . '</strong>' .
                    '</td>
          <td class="product-total" style="color: #00aa00;">' . $price_p . '</td>
          </tr>';
            }
        }

        /*         * *************** end ***************** */
        /*
         * changed for issue
         *
         *
          if (!isset($woocommerce->session->wc_free_select_gift) ||
          empty($woocommerce->session->wc_free_select_gift) ||
          $woocommerce->session->wc_free_select_gift == "" ||
          !is_array($woocommerce->session->wc_free_select_gift))
          return;
          if (is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift) > 0) {
          //$arr=$woocommerce->session->wc_free_select_gift;
          foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) {
          foreach ($this->gift_item_key as $gift_item_key => $cart_item) {
          if (in_array($cart_item['key'], $woocommerce->session->wc_free_select_gift)) {
          $id = "";
          $id = explode("-", $index);
          $Product = get_product($id[1]);

          //$gift_index="";
          //$gift_index=$this->gift_item_key[$index];
          //$Product = get_product($gift_index['item']);

          if (!$Product->is_in_stock())
          return;
          $price_p = apply_filters('woocommerce_get_price_html', apply_filters('woocommerce_free_price_html', __('Free', 'pw_wc_advanced_gift')));
          echo '<tr>
          <td class="product-name">' .
          apply_filters('woocommerce_checkout_product_title', $Product->get_title(), $Product) . ' ' .
          '<strong class="product-quantity">1</strong>' .
          '</td>
          <td class="product-total" style="color: #00aa00;">' . $price_p . '</td>
          </tr>';
          }
          }
          }
          }

         */
    }



    /* 	public function on_cart_loaded_from_session( $cart ) {

      global $woocommerce;

      $sorted_cart = array();
      if ( sizeof( $cart->cart_contents ) > 0 )
      foreach ( $cart->cart_contents as $cart_item_key => $values )
      $sorted_cart[$cart_item_key] = $values;

      $this->adjust_cart_rule( $sorted_cart );

      }
     */

    //public function adjust_cart_rule( $cart ) {


    public function check_candition_rules_product($rule)
    {
        if ($rule['product_depends'] == 'yes') {

            if ($rule['pw_product_depends_method'] == 'all') {
                $id_product = array();
                foreach ($this->item_cart as $cart_item_key => $cart_item) {
                    if ($cart_item['data']->post_type == 'product_variation') {
                        $id_product [] = $cart_item['variation_id'];
                    } else
                        $id_product [] = $cart_item['product_id'];
                }
//                print_r($this->item_cart);
                foreach ($rule['pw_product_depends'] as $pw_product_depends_id) {
                    if (!in_array($pw_product_depends_id, $id_product)) {
                        return false;
                    }
                }
            } else {
                foreach ($this->item_cart as $cart_item_key => $cart_item) {
                    $id_product = "";
                    $id_product = $cart_item['product_id'];
                    if ($cart_item['data']->post_type == 'product_variation') {
                        $id_product = $cart_item['variation_id'];
                    }
                    if (in_array($id_product, $rule['pw_product_depends'])) {
                        return true;
                    }
                }
                return false;
            }
        }

        if ($rule['exclude_product_depends'] == 'yes') {

            foreach ($this->item_cart as $cart_item_key => $cart_item) {
                $id_product = "";
                $id_product = $cart_item['product_id'];
                if ($cart_item['data']->post_type == 'product_variation') {
                    $id_product = $cart_item['variation_id'];
                }
                if (in_array($id_product, $rule['pw_exclude_product_depends'])) {
                    return false;
                }
            }
            return true;
        }

        return true;
    }

    public function check_candition_rules_brand_product($rule)
    {
        if ($rule['brand_depends'] == 'yes') {
            if ($rule['pw_brand_depends_method'] == 'all') {
                foreach ($rule['pw_brand_depends'] as $pw_brand_depends) {
                    $flag = false;
                    foreach ($this->item_cart as $cart_item_key => $cart_item) {
                        if (!in_array($pw_brand_depends, $this->get_cart_item_brands($cart_item))) {
                            $flag = false;
                            break;
                        } else
                            $flag = true;
                    }
                    if ($flag) {
                        return false;
                    }
                }
            } else {
                foreach ($this->item_cart as $cart_item_key => $cart_item) {
                    if (count(array_intersect($this->get_cart_item_brands($cart_item), $rule['pw_brand_depends'])) != 0)
                        return true;
                }
                return false;
            }
        }
        return true;
    }

    /*
      //for count of product in cart if depends is categories
      public function check_candition_rules_category_product($rule)
      {
      if ($rule['category_depends'] == 'yes')
      {
      $count_quesntity=0;
      $criteria_nb_products=$rule['criteria_nb_products'];
      foreach($this->item_cart as $cart_item_key => $cart_item)
      {
      if(count(array_intersect($this->get_cart_item_categories($cart_item), $rule['pw_category_depends'])) != 0)
      {
      $count_quesntity+=$cart_item['quantity'];
      if($count_quesntity >= $criteria_nb_products)
      {
      return true;
      }
      }
      }
      return false;
      }
      return true;
      }
     */

    public function check_candition_rules_category_product($rule)
    {

        if ($rule['category_depends'] == 'yes') {

            if ($rule['pw_category_depends_method'] == 'all') {
                foreach ($rule['pw_category_depends'] as $pw_category_depends) {
                    $flag = false;
                    foreach ($this->item_cart as $cart_item_key => $cart_item) {
                        if (!in_array($pw_category_depends, $this->get_cart_item_categories($cart_item))) {
                            $flag = false;
                            break;
                        } else
                            $flag = true;
                    }
                    if ($flag) {
                        return false;
                    }
                }

//                foreach ($this->item_cart as $cart_item_key => $cart_item) {
//
//                    if (count(array_intersect($this->get_cart_item_categories($cart_item), $rule['pw_category_depends'])) <= 0) {
//                        return false;
//                    }
//                }
            } else {

                foreach ($this->item_cart as $cart_item_key => $cart_item) {

                    if (count(array_intersect($this->get_cart_item_categories($cart_item), $rule['pw_category_depends'])) != 0) {
                        return true;
                    }
                }
                return false;
            }
        }

        if ($rule['exclude_category_depends'] == 'yes') {

            foreach ($this->item_cart as $cart_item_key => $cart_item) {
                if (count(array_intersect($this->get_cart_item_categories($cart_item), $rule['exclude_pw_category_depends'])) > 0) {
                    return false;
                }
            }
            return true;
        }

        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    public function check_candition_rules($rule)
    {
        global $woocommerce;
        $setting = get_option("pw_gift_options");
        $multiselect_cart_amount = $setting['multiselect_cart_amount'];
        $multiselect_gift_count = $setting['multiselect_gift_count'];

        if ($rule['pw_register_user'] == "yes" && !is_user_logged_in()) {
            return false;
        } elseif ($rule['pw_register_user'] == "yes" && is_user_logged_in() && is_array($rule['pw_limit_cunter']) && $rule['pw_limit_per_user'] != '') {
            $user_id = get_current_user_id();
            $number = 0;
            foreach ($rule['pw_limit_cunter']['user_info'] as $user_info) {
                if ($user_info['id'] == $user_id) {
                    $number = $user_info['number'];
                    break;
                }
            }

            if ($number >= $rule['pw_limit_per_user']) {
                return false;
            }
        }


        if ($rule['pw_limit_per_rule'] != 0 && $rule['pw_limit_per_rule'] != "" && is_array($rule['pw_limit_cunter']) && $rule['pw_limit_cunter']['count'] >= $rule['pw_limit_per_rule']) {
            return false;
        }


        if ($setting['multiselect'] == "yes" && count($this->gift_item_key) > 1) {

            if ($this->calculate_cart_subtotal() < $multiselect_cart_amount || count($woocommerce->session->wc_free_select_gift) >= $multiselect_gift_count) {
                return false;
            }
        }

        /* if($setting['multiselect']=="yes" && ($this->calculate_cart_subtotal()<$multiselect_cart_amount || (is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift)>=$multiselect_gift_count)))
          {
          echo 'z';
          return false;
          }
         */
        if ($rule['roles_depends'] == "yes") {
            if (count(array_intersect($this->pw_current_user_roles(), $rule['pw_roles'])) < 1) {
                return false;
            }
            //if (count(array_intersect($this->pw_current_user_roles(), $rule['roles_depends'])) < 1) {
            //	return false;
            //}
        }

        if ($rule['users_depends'] == 'yes') {
            $current_user = wp_get_current_user();
            //print_r($rule['pw_users']);
            if (isset($rule['pw_users']) && !in_array($current_user->ID, $rule['pw_users'])) {

                return false;
            }
        }
//        $week_start = strtotime('last Sunday', time());
//        $week_end = strtotime('next Sunday', time());

//        $t = date("m/d/Y", time());
//        echo date('m/d/Y', strtotime($t . ' second sunday'));
//
//        $month_start = strtotime('first day of this month', time());
//        $month_end = strtotime('last day of this month', time());
//
//        $year_start = strtotime('first day of January', time());
//        $year_end = strtotime('last day of December', time());
//
//        echo date('D, M jS Y', $week_start) . 'aa<br/>';
//        echo date('D, M jS Y', $week_end) . '<br/>';
//
//        echo date('D, M jS Y', $month_start) . '<br/>';
//        echo date('D, M jS Y', $month_end) . '<br/>';
//
//        echo date('D, M jS Y', $year_start) . '<br/>';
//        echo date('D, M jS Y', $year_end) . '<br/>';

        if (isset($rule['pw_to']) && !empty($rule['pw_to']) && ($rule['pw_to']) < time()) {
            return false;
        }
        if (isset($rule['pw_from']) && !empty($rule['pw_from']) && ($rule['pw_from'] > time())) {
            return false;
        }

        if (isset($rule['schedule_type']) && $rule['schedule_type'] != 'unlimited') {

            if ($rule['schedule_type'] == 'daily') {
                $ret = true;
                $t = date("d", time());
                $month_end = date('d', strtotime('last day of this month', time()));
                if (in_array('last', $rule['pw_daily']) && $month_end == $t) {
                    $ret = false;
                }

                if (in_array($t, $rule['pw_daily'])) {
                    $ret = false;
                }
                if ($ret) {
                    return false;
                }
            } elseif ($rule['schedule_type'] == 'weekly') {
                if (!is_array($rule['pw_weekly'])) {
                    return false;
                }
                $t = date("l", time());
                if (!in_array($t, $rule['pw_weekly'])) {
                    return false;
                }

            } elseif ($rule['schedule_type'] == 'monthly') {
                $each = $rule['pw_monthly']['each'];
                $day = $rule['pw_monthly']['day'];
                $time_for_gift = date('d', strtotime($each . ' ' . $day . ' of ' . date('M Y')));
                $t = date("d", time());
                if ($time_for_gift != $t) {
                    return false;
                }
            }
        }

        if (isset($rule['is_coupons']) && $rule['is_coupons'] == 'yes') {

            if (WC()->cart->get_coupons()) {
                return false;
            }
        }

        if (isset($rule['order_count']) && !empty($rule['order_count'])) {
            global $wpdb;
            if (!is_user_logged_in()) {
                return false;
            }
            $user_id = get_current_user_id();
            $count = $wpdb->get_var("SELECT COUNT(*)
				FROM $wpdb->posts as posts

				LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

				WHERE   meta.meta_key       = '_customer_user'
				AND     posts.post_type     IN ('" . implode("','", wc_get_order_types('order-count')) . "')
				AND     posts.post_status   = 'wc-completed'
				AND     meta_value          = $user_id
			");

            $count = absint($count);
            switch ($rule['order_op_count']) {
                case '>':
                    if ($count < $rule['order_count']) {
                        return false;
                    }
                    break;
                case '<':
                    if ($count > $rule['order_count']) {
                        return false;
                    }
                    break;
                case '==':
                    if ($count != $rule['order_count']) {
                        return false;
                    }
                    break;
            }
        }

        if (isset($rule['criteria_nb_products'])) {
			
            $count = 0;
            $quantity = $this->check_nb_product($rule);
//            print_r($quantity);
            if (count($quantity) <= 0) {
                return false;
            }
            foreach ($quantity as $cunter) {
                $count += $cunter;
            }

            switch ($rule['criteria_nb_products_op']) {
                case '>':
                    if ($count <= $rule['criteria_nb_products']) {

                        return false;
                    }
                    break;
                case '<':
                    if ($count >= $rule['criteria_nb_products']) {
                        return false;
                    }
                    break;
                case '==':
                    if ($count != $rule['criteria_nb_products']) {
                        return false;
                    }
                    break;                
				case 'min_max':
				
                    if ($count < $rule['criteria_nb_products_min'] || $count > $rule['criteria_nb_products_max']) {
                        return false;
                    }
                    break;
            }
        }/**/

        if (isset($rule['pw_cart_amount'])) {
			$quantity = $this->check_amount_product($rule);

            if ($quantity <= 0) {
                return false;
            }

            switch ($rule['cart_amount_op']) {
                case '>':
                    if (!empty($rule['pw_cart_amount']) && $this->cart['amount'] < $rule['pw_cart_amount'] || $quantity < $rule['pw_cart_amount'] ) {
                        return false;
                    }
                    break;
                case '<':
                    if (!empty($rule['pw_cart_amount']) && $this->cart['amount'] > $rule['pw_cart_amount'] || $quantity > $rule['pw_cart_amount']) {
                        return false;
                    }
                    break;
                case '==':
                    if (!empty($rule['pw_cart_amount']) && $this->cart['amount'] != $rule['pw_cart_amount'] || $quantity != $rule['pw_cart_amount']) {
                        return false;
                    }
                    break;
                case 'min_max':

                    // list($min_value, $max_value) = explode(':', $rule['pw_cart_amount']);

                    if ($this->cart['amount'] < $rule['pw_cart_amount_min'] || $this->cart['amount'] > $rule['pw_cart_amount_max'] ) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    public function check_nb_product($rule)
    {
        $cart_items = array();
        foreach ($this->item_cart as $cart_item_key => $cart_item) {
            //$cart_items[$cart_item_key] = $cart_item;
            $inter = false;
            if ($rule['category_depends'] == 'yes') {
                $categories = $this->cart_categories($cart_item);
                //print_r($rule['pw_product_category']);
                if (isset($rule['pw_category_depends']) && count(array_intersect($categories, $rule['pw_category_depends'])) > 0) {
                    $cart_items[$cart_item_key] = $cart_item;
                    $inter = true;
                }
            }
            if ($rule['brand_depends'] == 'yes' && $inter == false) {
                $categories = $this->get_cart_item_brands($cart_item);
                if (count(array_intersect($categories, $rule['pw_brand_depends'])) > 0) {
                    $cart_items[$cart_item_key] = $cart_item;
                    $inter = true;
                }
            }
            if ($rule['product_depends'] == 'yes' && $inter == false) {
                $id_product = "";
                $id_product = $cart_item['product_id'];
                if ($cart_item['data']->post_type == 'product_variation') {
                    $id_product = $cart_item['variation_id'];
                }
                if (isset($rule['pw_product_depends']) && in_array($id_product, $rule['pw_product_depends'])) {
                    $cart_items[$cart_item_key] = $cart_item;
                }
                $inter = true;
            }
            if ($inter == false && $rule['product_depends'] != 'yes' && $rule['category_depends'] != 'yes' && $rule['brand_depends'] != 'yes') {

                $cart_items[$cart_item_key] = $cart_item;
            }
        }

        $quantity = array();
//        if ($inter == false) {
//            return $quantity;
//        }

        foreach ($cart_items as $item_key => $item) {
            if (isset($quantity[$item['product_id']])) {
                $quantity[$item['product_id']] += $item['quantity'];
            } else {
                $quantity[$item['product_id']] = $item['quantity'];
            }
        }
        //if($quantity==array())
//        print_r($quantity);
        return $quantity;
        //die('a');
    }

    public function check_amount_product($rule)
    {
        $cart_items = array();
        foreach ($this->item_cart as $cart_item_key => $cart_item) {
            //$cart_items[$cart_item_key] = $cart_item;
            $inter = false;
            if ($rule['category_depends'] == 'yes') {
                $categories = $this->cart_categories($cart_item);
                //print_r($rule['pw_product_category']);
                if (isset($rule['pw_category_depends']) && count(array_intersect($categories, $rule['pw_category_depends'])) > 0) {
                    $cart_items[$cart_item_key] = $cart_item;
                    $inter = true;
                }
            }
            if ($rule['brand_depends'] == 'yes' && $inter == false) {
                $categories = $this->get_cart_item_brands($cart_item);
                if (count(array_intersect($categories, $rule['pw_brand_depends'])) > 0) {
                    $cart_items[$cart_item_key] = $cart_item;
                    $inter = true;
                }
            }
            if ($rule['product_depends'] == 'yes' && $inter == false) {
                $id_product = "";
                $id_product = $cart_item['product_id'];
                if ($cart_item['data']->post_type == 'product_variation') {
                    $id_product = $cart_item['variation_id'];
                }
                if (isset($rule['pw_product_depends']) && in_array($id_product, $rule['pw_product_depends'])) {
                    $cart_items[$cart_item_key] = $cart_item;
                }
                $inter = true;
            }
            if ($inter == false && $rule['product_depends'] != 'yes' && $rule['category_depends'] != 'yes' && $rule['brand_depends'] != 'yes') {

                $cart_items[$cart_item_key] = $cart_item;
            }
        }

        $cart_subtotal = 0;
        foreach ($cart_items as $item_key => $item) {
			$quantitye = (isset($item['quantity']) && $item['quantity']) ? $item['quantity'] : 1;
            $cart_subtotal += $item['data']->get_price() * $quantitye;
        }
        return $cart_subtotal;
    }
	
    public function cart_categories($cart_item)
    {
        $categories = array();
        $current = wp_get_post_terms($cart_item['product_id'], 'product_cat');
        foreach ($current as $category) {
            $categories[] = $category->term_id;
        }
        return $categories;
    }

    public function pw_current_user_roles()
    {
        global $current_user;
        wp_get_current_user();
        return $current_user->roles;
    }

    public function add_jqury_gifts()
    {
        global $woocommerce, $product;
        $print = "";
        $cart_page_id = wc_get_page_id('cart');
        $cart_page_id = get_permalink($cart_page_id);
        if (substr($cart_page_id, -1) == "/") {
            $cart_page_id = substr($cart_page_id, 0, -1);
        }
        $setting = get_option("pw_gift_options");
        $flag_check = false;
        foreach ($this->gift_item_key as $gift_item_key => $cart_item) {
            if (is_array($woocommerce->session->wc_free_select_gift) &&
                in_array($cart_item['key'], $woocommerce->session->wc_free_select_gift)
            ) {
                $flag_check = true;
                break;
            } else
                $flag_check = false;
            //print_r($cart_item);
        }
        if ($flag_check == false) {
            unset($woocommerce->session->wc_free_select_gift);
            $woocommerce->session->wc_free_select_gift = "";
            $woocommerce->session->wc_free_select_gift = array();
            WC()->session->set('group_order_data', '');
            //$woocommerce->session->group_order_data = '';
            return;
        }
        //$check_cart=$this->adjust_cart_rule_check();
        //echo $check_cart;
        if (is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift) > 0 && is_array($this->gift_item_key) && count($this->gift_item_key) > 0) {
            //$arr=$woocommerce->session->wc_free_select_gift;
            foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) {
                $gift_index = "";
                $gift_index = $this->gift_item_key[$index];
                //print_r($index);
                $product = wc_get_product($gift_index['item']);
                $title = '';
                if ($product->is_in_stock()) {

                    //        if ($product->product_type == 'simple') {
                    $title = $product->get_title();
//                    } else if ($product->product_type == 'variation') {
//                        $variation_names = "";
//                        $variant_abs = array_values($product->get_variation_attributes());
//                        foreach ($variant_abs as $var) {
//                            $variation_name = $var;
//                            if ($variation_name) {
//                                $variation_names .= ' (' . $variation_name . ')';
//                            }
//                        }
//                        $title = $product->get_title() . ' ' . $variation_names;
//
//                        $child = $variation_names = "";
//                        $variation_names = "";
//                    }
                    if (strpos($cart_page_id, '?') !== false)
                        $cart_page_id = $cart_page_id . '&';
                    else
                        $cart_page_id = $cart_page_id . '?';
                    $print .= '<tr class="cart_item"><td class="product-name"><span class="product-thumbnail"></span><div class="product-info"><a class="product-title" href="' . $product->get_permalink() . '">' . apply_filters('woocommerce_checkout_product_title', $title, $product) . '</a></div></td><td class="product-price"><span class="woocommerce-Price-amount amount">' . $setting['free'] . '</span></td><td class="product-quantity">1</td><td class="product-subtotal"><span class="woocommerce-Price-amount amount">' . $setting['free'] . '</span></td><td class="product-remove-link"><a href="' . $cart_page_id . 'pw_gift_remove=' . $index . '" class="remove" title="Poista tämä tuote">×</a></td></tr>';
                }
            }
            echo "<script type='text/javascript'>
                    jQuery(document).ready(function(){
                        jQuery('" . ($print) . "').insertBefore('.avada-cart-actions');
                    });
                </script>
                ";
        }
    }

//Dispaly under cart

    /**
     *
     */
    public function pw_woocommerce_after_cart_table_function()
    {
        global $woocommerce;

        //echo "<script language='javascript' type='text/javascript'>
        //				jQuery(document).ready(function() {
        //				    alert('kosegab')
        //                })
        //       </script>";
        //echo 'd';

        //wp_enqueue_script('test', plugins_url('/js/a.js', __FILE__), '2.0.3', true);

        $product_item = '';
        $setting = get_option("pw_gift_options");
        $multiselect_cart_amount = $setting['multiselect_cart_amount'];
        $multiselect_gift_count = $setting['multiselect_gift_count'];
        $add_gift = $setting['add_gift'];

        $cart_page_id = wc_get_page_id('cart');
        $cart_page_id = get_permalink($cart_page_id);
        if (substr($cart_page_id, -1) == "/") {
            $cart_page_id = substr($cart_page_id, 0, -1);
        }
        $i = 0;
        $page = 1;

        $retrived_group_input_value = WC()->session->get('group_order_data');
        //$retrived_group_input_value = $woocommerce->session->group_order_data;
        $count_gift = 0;
        $count_rule_gift = array();
        $gifts_set = array();
        if (is_array($retrived_group_input_value) && count($retrived_group_input_value) > 0) {
            foreach ($retrived_group_input_value as $index => $set) {
                $count_gift += $set['q'];
                $gifts_set[] = $set['id'];
                if (array_key_exists($set['rule_id'], $count_rule_gift)) {
                    $count_rule_gift[$set['rule_id']]['q'] += $set['q'];
                } else {
                    $count_rule_gift[$set['rule_id']]['q'] = $set['q'];
                }
            }
        }
        $t = 1;
        $innsert_div = false;
        foreach ($this->gift_item_key as $gift_item_key => $gift) {
            $product = wc_get_product($gift['item']);
            if (strpos($cart_page_id, '?') !== false)
                $permalink = $cart_page_id . '&pw_gift_add=' . $gift['key'];
            else
                $permalink = $cart_page_id . '?pw_gift_add=' . $gift['key'];
            //   if ($product->product_type == 'simple') {

            $title = $product->get_title();
            if ($product->post_type == 'product_variation') {
                //print_r($product);
                $title = $product->get_name();
            }
//            } else if ($product->product_type == 'variation') {
//                $variation_names = "";
//                $variant_abs = array_values($product->get_variation_attributes());
//                foreach ($variant_abs as $var) {
//                    $variation_name = $var;
//                    if ($variation_name) {
//                        $variation_names .= ' (' . $variation_name . ')';
//                    }
//                }
//                $title = $product->get_title() . ' ' . $variation_names;
//
//                $child = $variation_names = "";
//                $variation_names = "";
//            }
            $img_url = wp_get_attachment_image_src($product->get_image_id(), 'large');
            $img_url = $img_url[0];
            $img_html = $title_html = '';
            $item_hover = '';
            $disable = false;


            if (array_key_exists($gift['rule_id'], $count_rule_gift) && $count_rule_gift[$gift['rule_id']]['q'] >= $gift['pw_number_gift_allowed']) {
//                if ($setting['hide_gifts_after_select'] == "yes") {
//                    continue;
//                }
                if ($gift['disable_if'] == 'hide') {
                    continue;

                } else
                    $disable = true;

            } elseif (in_array($gift['key'], $gifts_set) && $gift['can_several_gift'] == 'no') {
                if ($gift['disable_if'] == 'hide') {
                    continue;
                } else
                    $disable = true;
            } elseif ($gift['disable_if'] != 'show' && count($woocommerce->session->wc_free_select_gift) >= 1) {
//                if ($setting['hide_gifts_after_select'] == "yes") {
//                    break;
//                }
                if ($gift['disable_if'] == 'hide') {
                    continue;
                } else
                    $disable = true;
            } else if ($setting['multiselect'] == "no" && is_array($woocommerce->session->wc_free_select_gift) && $count_gift > 0) {
                if ($gift['disable_if'] == 'hide') {
                    break;
                }

                $disable = true;
            } else if ($setting['multiselect'] == "yes" && count($woocommerce->session->wc_free_select_gift) >= 1) {
                if ($this->calculate_cart_subtotal() < $multiselect_cart_amount || $count_gift >= $multiselect_gift_count) {
//                    if ($setting['hide_gifts_after_select'] == "yes") {
//                        break;
//                    }
                    if ($gift['disable_if'] == 'hide') {
                        break;
                    }

                    $disable = true;
                }
            }
            if ($disable == true) {
                $img_html = '<img src="' . $img_url . '" />';
                $title_html = '<div class="gift-product-title">' . $title . '</div>';
                $item_hover = 'disable-hover';
            } else {
                $img_html = '<img src="' . $img_url . '" />';
                $title_html = '<div class="gift-product-title">' . $title . '</div>';
                $item_hover = 'hovering';
            }

            if ($i == 0 && $setting["view_cart_gift"] == "grid") {
                $active = '';
                if ($page == 1) {
                    $active = ' pw-gift-active ';
                }
                $product_item .= '<div class="page_' . $page . ' pw_gift_pagination_div ' . $active . '" style="display: none;">';
                $page++;
                $innsert_div = true;
            }

            $item = ($setting["view_cart_gift"] == "grid") ? '<div class="' . $setting["mobile_columns"] . ' ' . $setting["tablet_columns"] . ' ' . $setting["desktop_columns"] . ' ' . $i . '" >' : '';
            $end_item = ($setting["view_cart_gift"] == "grid") ? '</div>' : '';
            $i++;

            $product_item .= $item . '<div class="gift-product-item ' . $item_hover . '">
						<div class="gift-product-hover"  ><div><a href="' . $permalink . '">' . $add_gift . '</a></div></div>
						' . $img_html . '
						' . $title_html . '
					</div>
				' . $end_item;

            if (($i == $setting["number_per_page"] && $setting["view_cart_gift"] == "grid") || (count($this->gift_item_key) == $t && $innsert_div == true)) {
               
                $innsert_div = false;
                $product_item .= '</div>';
                $i = 0;
            }
            $t++;
        }
        $did = rand(0, 1000);
        $setting = get_option("pw_gift_options");
        if ($product_item == '') {
            return;
        }
        echo '<div class="gift-popup-title">' . $setting['cart_title'] . '</div>';

        if ($setting['view_cart_gift'] == 'carousel') {
            echo '<div class="owl-carousel wb-car-car  wb-carousel-layout wb-car-cnt " id="pw_slider_gift" >
					' . $product_item . '
				</div>';
            echo "<script type='text/javascript'>
						jQuery(document).ready(function() {
							//alert('kirekhar');
jQuery( document.body ).on( 'updated_cart_totals', function(){
	
    //re-do your jquery
   // confirm('KIE'+'#slider_" . $did . "');
    jQuery('.owl-carousel').owlCarousel('destroy'); 
    jQuery('.owl-carousel').owlCarousel({
								  margin : " . $setting['pw_item_marrgin'] . " , 
								  loop:true,
								  dots:" . $setting['pw_show_pagination'] . ",
								  nav:" . $setting['pw_show_control'] . ",
								  slideBy: " . $setting['pw_item_per_slide'] . ",
								  autoplay:" . $setting['pw_auto_play'] . ",
								  autoplayTimeout : " . $setting['pw_slide_speed'] . ",
								  rtl: " . (isset($setting['pw_slide_rtl']) ? $setting['pw_slide_rtl'] : false) . ",
								  responsive:{
							        0:{
							            items:1
							        },
							        600:{
							            items:2
							        },
							        1000:{
							            items:" . $setting['pw_item_per_view'] . "
							        }
							    },
							    autoplayHoverPause: true,
							    navText: [ '>', '<' ]
							 });
});						    
							  jQuery('#pw_slider_gift').owlCarousel({
								  margin : " . $setting['pw_item_marrgin'] . " , 
								  loop:true,
								  dots:" . $setting['pw_show_pagination'] . ",
								  nav:" . $setting['pw_show_control'] . ",
								  slideBy: " . $setting['pw_item_per_slide'] . ",
								  autoplay:" . $setting['pw_auto_play'] . ",
								  autoplayTimeout : " . $setting['pw_slide_speed'] . ",
								  rtl: " . (isset($setting['pw_slide_rtl']) ? $setting['pw_slide_rtl'] : false) . ",
								  responsive:{
							        0:{
							            items:1
							        },
							        600:{
							            items:2
							        },
							        1000:{
							            items:" . $setting['pw_item_per_view'] . "
							        }
							    },
							    autoplayHoverPause: true,
							    navText: [ '>', '<' ]
							 })          		
						    
                         })
                 </script>";


        } else if ($setting['view_cart_gift'] == 'grid') {
            $btn = '';
            if ($page > 2) {
                $btn = '<div class="gift-popup-title">';
                for ($i = 1; $i < $page; $i++) {
                    $btn .= '<a href="" class="pw_gift_pagination_num" data-page-id="page_' . $i . '" style="border: none;">' . $i . '</a>';
                    if ($i + 1 < $page) {
                        $btn .= ' | ';
                    }
                }
                $btn .= '</div>';
            }
            echo '<div class="wg-row wg-maincontainer">' . $product_item . '</div></div>' . $btn;

            echo '<script type="text/javascript">
						jQuery(document).ready(function($) {
							$(".pw_gift_pagination_num").click(function(e){
							e.preventDefault();
							var page=$(this).attr("data-page-id");
							$("."+page).siblings(".pw_gift_pagination_div").removeClass("pw-gift-active");
							  $("."+page).addClass("pw-gift-active");
						});
					   });	
					 </script>';
        }
    }

    public function pw_insert_gift_cart($gift = null)
    {
        global $woocommerce;
        //if set session
        if (isset($woocommerce->session->wc_free_select_gift)) {
            foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) {
                if (!array_key_exists($index, $this->gift_item_key)) {
                    $arr = $woocommerce->session->wc_free_select_gift;
                    $index_r = array_search($index, $arr);
                    unset($arr[$index_r]);
                    $woocommerce->session->wc_free_select_gift = $arr;
                    $retrived_group_input_value = WC()->session->get('group_order_data');
                    unset($retrived_group_input_value[$index]);
                    WC()->session->set('group_order_data', $retrived_group_input_value);
                    continue;
                }
            }
        }
        $setting = get_option("pw_gift_options");
        $multiselect_cart_amount = $setting['multiselect_cart_amount'];
        $multiselect_gift_count = $setting['multiselect_gift_count'];

        $retrived_group_input_value = WC()->session->get('group_order_data');
        //$retrived_group_input_value = $woocommerce->session->group_order_data;

        $count_gift = 0;
        $count_rule_gift = array();
        $gifts_set = array();
        if (is_array($retrived_group_input_value) && count($retrived_group_input_value) > 0) {

            foreach ($retrived_group_input_value as $index => $set) {
                $count_gift += $set['q'];
                $gifts_set[] = $set['id'];
                if (array_key_exists($set['rule_id'], $count_rule_gift)) {
                    $count_rule_gift[$set['rule_id']]['q'] += $set['q'];
                } else {
                    $count_rule_gift[$set['rule_id']]['q'] = $set['q'];
                }
            }
        }

        if (count($woocommerce->session->wc_free_select_gift) <= 0 || ($count_gift > 1 && $setting['multiselect'] == "no")) {
            //die;
            unset($woocommerce->session->wc_free_select_gift);
            $woocommerce->session->wc_free_select_gift = "";
            $woocommerce->session->wc_free_select_gift = array();
            WC()->session->set('group_order_data', '');
            //$woocommerce->session->group_order_data = '';
        }

        if (isset($_GET['pw_gift_add'])) {
            $gift = $_GET['pw_gift_add'];
        }
        if (!empty($gift) && array_key_exists($gift, $this->gift_item_key)) {
            foreach ($this->gift_item_key as $gift_item_key => $item_key) {
                $rule_id = $this->gift_item_key[$gift]['rule_id'];
                $pw_number_gift_allowed = $this->gift_item_key[$gift]['pw_number_gift_allowed'];
                if (array_key_exists($rule_id, $count_rule_gift) && $count_rule_gift[$rule_id]['q'] >= $pw_number_gift_allowed) {

                    return;
                } elseif (in_array($gift, $gifts_set) && $item_key['can_several_gift'] == 'no') {
                    return;
                } elseif ($this->gift_item_key[$gift]['disable_if'] != 'show' && count($woocommerce->session->wc_free_select_gift) >= 1) {
                    return;
                } else if ($setting['multiselect'] == "no" && is_array($woocommerce->session->wc_free_select_gift) && $count_gift > 0) {
                    return;
                } else if ($setting['multiselect'] == "yes" && count($woocommerce->session->wc_free_select_gift) >= 1) {
//                    return;
                }
            }


            if (is_array($woocommerce->session->wc_free_select_gift) &&
                count($woocommerce->session->wc_free_select_gift) > 0
            ) {
                if (!in_array($gift, $woocommerce->session->wc_free_select_gift) &&
                    ($setting['multiselect'] == "yes" && $this->cart['amount'] >= $multiselect_cart_amount && $count_gift < $multiselect_gift_count)
                ) {
                    $arr = $woocommerce->session->wc_free_select_gift;
                    $get = $gift;
                    array_push($arr, $get);
                    $woocommerce->session->wc_free_select_gift = $arr;
                    $retrived_group_input_value[$gift] = array('id' => $gift, 'q' => 1, 'rule_id' => $this->gift_item_key[$gift]['rule_id']);
                    WC()->session->set('group_order_data', $retrived_group_input_value);
                    //$woocommerce->session->group_order_data = $retrived_group_input_value;
                } elseif (in_array($gift, $woocommerce->session->wc_free_select_gift) &&
                    ((array_key_exists($this->gift_item_key[$gift]['rule_id'], $count_rule_gift) && $count_rule_gift[$this->gift_item_key[$gift]['rule_id']]['q'] < $this->gift_item_key[$gift]['pw_number_gift_allowed']) &&
                        $count_gift < $multiselect_gift_count)
                ) {

                    //foreach($woocommerce->session->wc_free_select_gift as $item_se)
                    //{
                    $retrived_group_input_value[$gift] = array('id' => $gift, 'q' => $retrived_group_input_value[$gift]['q'] + 1, 'rule_id' => $this->gift_item_key[$gift]['rule_id']);
                    WC()->session->set('group_order_data', $retrived_group_input_value);
                    //$woocommerce->session->group_order_data = $retrived_group_input_value;
                }
            } else {
                $retrived_group_input_value = array();
                $woocommerce->session->wc_free_select_gift = array($gift);
                $retrived_group_input_value[$gift] = array('id' => $gift, 'q' => 1, 'rule_id' => $this->gift_item_key[$gift]['rule_id']);
                WC()->session->set('group_order_data', $retrived_group_input_value);
                //$woocommerce->session->group_order_data = $retrived_group_input_value;
            }
        }
        if (
            isset($_GET['pw_gift_remove']) &&
            is_array($woocommerce->session->wc_free_select_gift) &&
            count($woocommerce->session->wc_free_select_gift) > 0 &&
            in_array($_GET['pw_gift_remove'], $woocommerce->session->wc_free_select_gift)
        ) {
            $arr = $woocommerce->session->wc_free_select_gift;
            $index = array_search($_GET['pw_gift_remove'], $arr);
            unset($arr[$index]);
            $woocommerce->session->wc_free_select_gift = $arr;

            unset($retrived_group_input_value[$_GET['pw_gift_remove']]);
            WC()->session->set('group_order_data', $retrived_group_input_value);
            //$woocommerce->session->group_order_data = $retrived_group_input_value;
        }
    }

//For display in cart
    public function woocommerce_cart_contents_function()
    {
        global $woocommerce, $product;

        $cart_page_id = wc_get_page_id('cart');
        $cart_page_id = get_permalink($cart_page_id);
        if (substr($cart_page_id, -1) == "/") {
            $cart_page_id = substr($cart_page_id, 0, -1);
        }

        $setting = get_option("pw_gift_options");
        $flag_check = false;
        foreach ($this->gift_item_key as $gift_item_key => $cart_item) {
            if (is_array($woocommerce->session->wc_free_select_gift) &&
                in_array($cart_item['key'], $woocommerce->session->wc_free_select_gift)
            ) {
                $flag_check = true;
                break;
            } else
                $flag_check = false;
        }

        if ($flag_check == false) {
            unset($woocommerce->session->wc_free_select_gift);
            $woocommerce->session->wc_free_select_gift = "";
            $woocommerce->session->wc_free_select_gift = array();
            WC()->session->set('group_order_data', '');
            //$woocommerce->session->group_order_data = '';
            return;
        }

        if (is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift) > 0 && is_array($this->gift_item_key) && count($this->gift_item_key) > 0) {
            //$arr=$woocommerce->session->wc_free_select_gift;
            foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) {
                $gift_index = "";
                if (!array_key_exists($index, $this->gift_item_key)) {
                    $arr = $woocommerce->session->wc_free_select_gift;
                    $index_r = array_search($index, $arr);
                    unset($arr[$index_r]);
                    $woocommerce->session->wc_free_select_gift = $arr;
                    $retrived_group_input_value = WC()->session->get('group_order_data');
                    //$retrived_group_input_value = $woocommerce->session->group_order_data;
                    unset($retrived_group_input_value[$index]);
                    WC()->session->set('group_order_data', $retrived_group_input_value);
                    //$woocommerce->session->group_order_data = $retrived_group_input_value;
                    continue;
                }
                $gift_index = $this->gift_item_key[$index];

                if (count($gift_index) <= 0) {
                    $arr = $woocommerce->session->wc_free_select_gift;
                    $index_r = array_search($index, $arr);
                    unset($arr[$index_r]);
                    $woocommerce->session->wc_free_select_gift = $arr;

                    //$retrived_group_input_value = WC()->session->get('group_order_data');
                    $retrived_group_input_value = $woocommerce->session->group_order_data;
                    unset($retrived_group_input_value[$index]);
                    WC()->session->set('group_order_data', $retrived_group_input_value);
                    //$woocommerce->session->group_order_data = $retrived_group_input_value;
                    continue;
                }
                $product = wc_get_product($gift_index['item']);
                $title = '';
                $title = $product->get_title();
                if ($product->post_type == 'product_variation') {
                    $title = $product->get_name();
                }

//                } else if ($product->product_type == 'variation') {
//                    $variation_names = "";
//                    $variant_abs = array_values($product->get_variation_attributes());
//                    foreach ($variant_abs as $var) {
//                        $variation_name = $var;
//                        if ($variation_name) {
//                            $variation_names .= ' (' . $variation_name . ')';
//                        }
//                    }
//                    $title = $product->get_title() . ' ' . $variation_names;
                //if ( $child instanceof WC_Product_Variation ) {
                //	$title=$child->post->post_title	.' '.$variation_names;
                //echo '<option '.$selected.' value="'.$child_id.'">'.$child->post->post_title	.' '.$variation_names.' (variation id: ' . $child->variation_id . ')</option>';
                //}
//                    $child = $variation_names = "";
//                    $variation_names = "";
//                }
                //For Dispaly Image
               
                  $img_url='';
                  $img_url = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );
                  $img_url=$img_url[0];
                  $img_html='';
                  $img_html = '<img src="'.$img_url.'" alt="no found"/>';
               /*    echo '
                  <tr class="cart_item">
                  <td class="product-remove">
                  <a class="gift-close-link" href="'.$cart_page_id.'?pw_gift_remove='.$index.'"><div class="gift-close"></div></a>
                  </td>
                  <td class="product-name"><a href="'.$product->get_permalink().'">'. $img_html .apply_filters( 'woocommerce_checkout_product_title', $title, $product ).'</a></td>
                  <td class="product-price">'.$setting['free'].'</td>
                  <td class="product-quantity">1</td>
                  <td class="product-subtotal">'.$setting['free'].'</td>
                  </tr>';
                 */
                if (strpos($cart_page_id, '?') !== false)
                    $cart_page_id = $cart_page_id . '&';
                else
                    $cart_page_id = $cart_page_id . '?';
                $retrived_group_input_value = WC()->session->get('group_order_data');
                //$retrived_group_input_value = $woocommerce->session->group_order_data;
                $count = isset($retrived_group_input_value[$index]['q']) ? $retrived_group_input_value[$index]['q'] : 1;
                echo '
							<tr class="woocommerce-cart-form__cart-item cart_item">
								<td class="product-remove">
									<a class="remove gift-close-link" href="' . $cart_page_id . 'pw_gift_remove=' . $index . '"><div class="gift-close"></div></a>
								</td>
								<td class="product-thumbnail">'.$img_html.'</td>
								<td class="product-name" data-title="'. $setting['cart_title'] .'"><a href="' . $product->get_permalink() . '">' . apply_filters('woocommerce_checkout_product_title', $title, $product) . '</a></td>
								<td class="product-price" data-title="'.__("Price","woocommerce") . '">' . $setting['free'] . '</td>
								<td class="product-quantity" data-title="'.__("Quantity","woocommerce") . '">' . $count . '</td>
								<td class="product-subtotal" data-title="'.__("Total","woocommerce") . '">' . $setting['free'] . '</td>
							</tr>';
                //}
            }
        }
    }

    public function add_gift_to_order($order_id)
    {
        global $woocommerce;
        if (!$this->check_rule()) {
            return;
        }
        $flag_check = false;
        foreach ($this->gift_item_key as $gift_item_key => $cart_item) {
            if (is_array($woocommerce->session->wc_free_select_gift) &&
                in_array($cart_item['key'], $woocommerce->session->wc_free_select_gift)
            ) {
                $flag_check = true;
                break;
            } else
                $flag_check = false;
        }
        if ($flag_check == false) {
            unset($woocommerce->session->wc_free_select_gift);
            $woocommerce->session->wc_free_select_gift = "";
            $woocommerce->session->wc_free_select_gift = array();

            return;
        }
        $retrived_group_input_value = WC()->session->get('group_order_data');
        //$retrived_group_input_value = $woocommerce->session->group_order_data;
        if (is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift) > 0 && is_array($this->gift_item_key) && count($this->gift_item_key) > 0) {
            $flag = false;
            foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) {
                $gift_index = "";
                $gift_index = $this->gift_item_key[$index];
                if (count($gift_index) <= 0) {
                    $arr = $woocommerce->session->wc_free_select_gift;
                    $index_r = array_search($index, $arr);
                    unset($arr[$index_r]);
                    $woocommerce->session->wc_free_select_gift = $arr;

                    $retrived_group_input_value = WC()->session->get('group_order_data');
                    //$retrived_group_input_value = $woocommerce->session->group_order_data;
                    unset($retrived_group_input_value[$index]);
                    WC()->session->set('group_order_data', $retrived_group_input_value);
                    //$woocommerce->session->group_order_data = $retrived_group_input_value;
                    continue;
                }
                $product = wc_get_product($gift_index['item']);
                $product_id = "";
                $product_id = $gift_index['item'];
                if ($product->post_type == 'product_variation') {
                    $product_id = wp_get_post_parent_id($gift_index['item']);
                    //$product_id=$product->parent_id;
                }

                $rule_id = $gift_index['rule_id'];
                $title = '';

                if ($product->is_in_stock()) {
                    $item_id = woocommerce_add_order_item($order_id, array(
                        'order_item_name' => $product->get_title(),
                        'order_item_type' => 'line_item'
                    ));
                    if ($item_id) {
                        woocommerce_add_order_item_meta($item_id, '_qty', $retrived_group_input_value[$index]['q']);
                        woocommerce_add_order_item_meta($item_id, '_tax_class', $product->get_tax_class());
                        woocommerce_add_order_item_meta($item_id, '_product_id', $product_id);
                        woocommerce_add_order_item_meta($item_id, '_variation_id', $product->variation_id ? $product->variation_id : '');
                        woocommerce_add_order_item_meta($item_id, '_line_subtotal', woocommerce_format_decimal(0, 4));
                        woocommerce_add_order_item_meta($item_id, '_line_total', woocommerce_format_decimal(0, 4));
                        woocommerce_add_order_item_meta($item_id, '_line_tax', woocommerce_format_decimal(0, 4));
                        woocommerce_add_order_item_meta($item_id, '_line_subtotal_tax', woocommerce_format_decimal(0, 4));
                        woocommerce_add_order_item_meta($item_id, '_free_gift', 'yes');
                        woocommerce_add_order_item_meta($item_id, '_rule_id_free_gift', $rule_id);

                        if (@$product->variation_data && is_array($product->variation_data)) {
                            foreach ($product->variation_data as $key => $value) {
                                woocommerce_add_order_item_meta($item_id, esc_attr(str_replace('attribute_', '', $key)), $value);
                            }
                        }
                        //For Limit
                        if (!$flag) {
                            $pw_limit_cunter = get_post_meta($gift_index['rule_id'], 'pw_limit_cunter', true);
                            if (is_array($pw_limit_cunter)) {
                                $pw_limit_cunter['count']++;

                                $order = new WC_Order($order_id);
                                $user_id = $order->get_customer_id('view');
//                        $billing_email = $order->get_billing_email('view');
                                if ($user_id > 0) {
//                            $user_id = $billing_email;
                                    $i = 0;
                                    foreach ($pw_limit_cunter['user_info'] as $user_info) {
                                        if ($user_info['id'] == $user_id) {
                                            $nubmer = $user_info['number'];
                                            $pw_limit_cunter['user_info'][$i]['number']++;
                                            $flag = true;
                                            break;
                                        }
                                        $i++;
                                    }
                                    if (!$flag) {
                                        $pw_limit_cunter['user_info'][] = array(
                                            'id' => $user_id,
                                            'number' => 1,
                                        );
                                        $flag = true;
                                    }
                                }
                                update_post_meta($gift_index['rule_id'], 'pw_limit_cunter', $pw_limit_cunter);
                            }
                        }
                    }
                    $woocommerce->session->wc_free_select_gift = "";
                    $woocommerce->session->wc_free_select_gift = array();
                }
            }
        }
    }

    public function get_cart_item_categories($cart_item)
    {
        $categories = array();
        $current = wp_get_post_terms($cart_item['product_id'], 'product_cat');
        foreach ($current as $category) {
            $categories[] = $category->term_id;
        }
        return $categories;
    }

    public function get_cart_item_brands($cart_item)
    {
        $brands = array();
        $current = wp_get_post_terms($cart_item['product_id'], 'product_brand');
        foreach ($current as $brand) {
            $brands[] = $brand->term_id;
        }
        return $brands;
    }

    /* 	public function get_adjact_item_categories($category)
      {
      $categories = array();
      $current = wp_get_post_terms($cart_item['data']->id, 'product_cat');
      foreach ($current as $category) {
      $categories[] = $category->term_id;
      }
      return $categories;
      }
     */

    public function get_cart_item_tags($cart_item)
    {
        $tags = array();
        $current = wp_get_post_terms($cart_item['product_id'], 'product_tag');
        foreach ($current as $tag) {
            $tags[] = $tag->term_id;
        }
        return $tags;
    }

    public function calculate_cart_subtotal()
    {
        global $woocommerce;
        $cart_subtotal = 0;
        // Iterate over all cart items and
        foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) {
            $quantity = (isset($cart_item['quantity']) && $cart_item['quantity']) ? $cart_item['quantity'] : 1;
            $cart_subtotal += $cart_item['data']->get_price() * $quantity;
        }
        //https://businessbloomer.com/woocommerce-slashed-cart-subtotal-coupon-cart/
        $price_new = WC()->cart->subtotal - $woocommerce->cart->get_cart_discount_tax_total() - $woocommerce->cart->get_cart_discount_total();
        return (float)$price_new;
        //echo (float)$cart_subtotal - (float)$woocommerce->cart->get_cart_discount_total();
        // return (float)$cart_subtotal + (float)$woocommerce->cart->get_cart_discount_total();
    }

}

new pw_class_woocommerce_gift_discunt_cart();
?>