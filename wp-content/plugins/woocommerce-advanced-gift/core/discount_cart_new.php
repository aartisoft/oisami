<?php
class pw_class_woocommerce_gift_discunt_cart_new {
	
		public function __construct() 
		{					
			$this->rule_check=array();
			
			$this->apply_rule=array();
			
			$this->gift_item_key=array();
			
			$this->arr_cart=array();
			
			$this->item_cart=array();
			
			$this->cart=array();
			
			//add_action( 'wp_head', array( $this, 'adjust_cart_rule' ) );
			add_action( 'wp', array( $this, 'pw_insert_gift_cart' ) );
			
			/* Do not allow user to update quantity of gift items */
			add_filter( 'woocommerce_is_sold_individually', array($this, 'proword_disallow_qty_update'), 10, 2 );
			
		
			//add_action( 'woocommerce_cart_item_removed', array($this, 'pw_gift_item_removed'), 10, 2 );
			
			//For display in cart
			//add_action( 'woocommerce_cart_contents',array( $this, 'woocommerce_cart_contents_function'));
			//add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'review_order_after_cart_contents_function' ) );
		//	add_action( 'woocommerce_new_order', array( $this, 'add_gift_to_order' ), 10, 1 );
		}
		public function proword_disallow_qty_update( $return, $product )
		{
			if( property_exists($product, 'variation_id') && $product->variation_id ) {
				$is_variation = get_post_meta($product->variation_id, '_proword_gift_product', true);
				if( (bool) $is_variation ) {
					return 1;
				}
			}
		}
		//public function adjust_cart_rule( $cart ) {
		public function adjust_cart_rule() {
			global $woocommerce,$wpdb;
			$this->cart['amount']="";
			$this->cart['quantities']="";
			$query_meta_query=array('relation' => 'AND');
			$query_meta_query[] = array(
				'key' =>'status',
				'value' => "active",
				'compare' => '=',
			);
			//$woocommerce->cart->cart_contents
			$this->item_cart=WC()->cart->cart_contents;
			//print_r($this->item_cart);
			$matched_products = get_posts(
				array(
					'post_type' 	=> 'pw_gift_rule',
					'numberposts' 	=> -1,
					'post_status' 	=> 'publish',
					'fields' 		=> 'ids',
					'no_found_rows' => true,
					'orderby'	=>'modified',
					'meta_query' => $query_meta_query,
				)
			);
			//$products=array();
			$quantity=0;
			foreach($this->item_cart as $cart_item_key => $cart_item)		
			{
				//echo $cart_item['data']->id.',<br>';
				$quantity = (isset($cart_item['quantity']) && $cart_item['quantity']) ? $cart_item['quantity'] : 1;
				$this->cart['amount'] += $cart_item['data']->get_price() * $quantity;
			}
			
			$this->cart['quantities'] = $woocommerce->cart->cart_contents_count;
			
			foreach($matched_products as $pr)
			{
				$is_ok=true;$product_depends=$category_depend=$pw_cart_amount=$brand_depends=$pw_brand_depends=$gift_auto_to_cart=$pw_id="";
				$gift_auto_to_cart = get_post_meta($pr,'gift_auto_to_cart',true);
				$category_depends = get_post_meta($pr,'category_depends',true);
				if(defined('plugin_dir_url_pw_woo_brand'))
				{	
					$brand_depends = get_post_meta($pr,'brand_depends',true);
					$pw_brand_depends = get_post_meta($pr,'pw_brand_depends',true);
				}
				$pw_cart_amount = get_post_meta($pr,'pw_cart_amount',true);
				$pw_cart_amount=($pw_cart_amount!="" ? $pw_cart_amount : 0);
				$r="";
				$pw_to=strtotime(get_post_meta($pr,'pw_to',true));
				$pw_from=strtotime(get_post_meta($pr,'pw_from',true));
				
				$pw_gifts = get_post_meta($pr,'pw_gifts',true);
				
				$users_depends = get_post_meta($pr,'users_depends',true);
				$pw_users = get_post_meta($pr,'pw_users');

				$pw_roles = get_post_meta($pr,'pw_roles');
				$roles_depends = get_post_meta($pr,'roles_depends',true);

				$criteria_nb_products = get_post_meta($pr,'criteria_nb_products',true);	
				

				$pw_product_depends = get_post_meta($pr,'pw_product_depends',true);
				$product_depends = get_post_meta($pr,'product_depends',true);
				
				$pw_category_depends = get_post_meta($pr,'pw_category_depends',true);
				$category_depends = get_post_meta($pr,'category_depends',true);
				
				$gift_auto_to_cart = get_post_meta($pr,'gift_auto_to_cart',true);
				
				//$blogtime = strtotime(current_time( 'mysql' ));
				//$cart_count=$woocommerce->cart->cart_contents_count;
				//$quantities = $this->count_in_cart($rule);				

				$this->rule_check[$pr]=array(
					"pw_id"=>$pr,
					"pw_gifts"=>$pw_gifts,
					"pw_roles"=>$pw_roles,
					"roles_depends"=>$roles_depends,
					"users_depends"=>$users_depends,
					"pw_users"=>$pw_users,
					"pw_to"=>$pw_to,
					"pw_from"=>$pw_from,
					"criteria_nb_products"=>$criteria_nb_products,
					"product_depends"=>$product_depends,
					"pw_product_depends"=>$pw_product_depends,
					"category_depends"=>$category_depends,
					"pw_category_depends"=>$pw_category_depends,
					"brand_depends"=>$brand_depends,
					"pw_brand_depends"=>$pw_brand_depends,
					"gift_auto_to_cart"=>$gift_auto_to_cart,
				);
			}
			$rules = array();
			foreach($this->rule_check as $rules_item =>$rule)
			{			
				$can_applied = false;
				if($this->check_candition_rules($rule))
				$this->apply_rule[$rules_item]=$rule;
				foreach($this->item_cart as $cart_item_key => $cart_item)
				{
					if($this->check_candition_rules_category_product($rule,$cart_item))
					{
						//$this->gift_item_key[]=$rule["pw_gifts"];
						foreach($rule['pw_gifts'] as $gift)
						{
							$id="";
							//$id=md5(uniqid());
							$id=$rule['pw_id'].$gift;
							
							$this->gift_item_key[$id]=array(
								"item"=>$gift,
								"key"=>$id,
							);
						}
						$can_applied = true;
                        break;
					}
				}
                if (!$can_applied) {
                    unset($this->apply_rule[$rules_item]);
                }
			}
			add_action( 'woocommerce_after_cart_table',array($this,'pw_woocommerce_after_cart_table_function' ));

		}

		public function check_candition_rules_category_product($rule,$cart_item)
		{
			if ($rule['category_depends'] == 'yes') 
			{
				if(count(array_intersect($this->get_cart_item_categories($cart_item), $rule['pw_category_depends'])) == 0)
					return false;
			}
			
			if ($rule['product_depends'] == 'yes') 
			{		
				if(!in_array($cart_item['data']->id, $rule['pw_product_depends']))
				{
					return false;
				}
			}	
			
			if ($rule['brand_depends'] == 'yes') 
			{
				if(count(array_intersect($this->get_cart_item_brands($cart_item), $rule['pw_brand_depends'])) == 0)
					return false;
			}			
			
			return true;
		}
		public function check_candition_rules($rule)
		{
			if($rule['pw_roles']=="yes")
			{
				if (count(array_intersect($this->pw_current_user_roles(), $rule['roles_depends'])) < 1) {
					return false;
				}
			}
			
			if ($rule['users_depends'] == 'yes') {
				if (!in_array(get_current_user_id(), $rule['pw_users'])) {
					
					return false;
				}
			}
			
            if (isset($rule['pw_to']) && !empty($rule['pw_to']) && (strtotime($rule['pw_to']) > time())) {
                return false;
            }
			
            if (isset($rule['pw_from']) && !empty($rule['pw_from']) && (strtotime($rule['pw_from']) < time())) {
                return false;
            }
			
            if (isset($rule['pw_from']) && !empty($rule['pw_from']) && (strtotime($rule['pw_from']) < time())) {
                return false;
            }

			if (isset($rule['criteria_nb_products'])) {
				if($this->cart['quantities'] < $rule['criteria_nb_products'])					
					return false;
			}
			return true;
		}

		public function pw_current_user_roles()
		{
			global $current_user;
            get_currentuserinfo();
            return $current_user->roles;
		}
		
		//Dispaly under cart
		public function pw_woocommerce_after_cart_table_function()
		{
			global $woocommerce;	
			
			$product_item='';
			$setting=get_option("pw_gift_options");
			$multiselect_cart_amount=$setting['multiselect_cart_amount'];
			$multiselect_gift_count=$setting['multiselect_gift_count'];
			
			$cart_page_id = wc_get_page_id('cart');
			$cart_page_id=get_permalink( $cart_page_id );
			if(substr($cart_page_id, -1)=="/")
				$cart_page_id=substr($cart_page_id, 0, -1);
			
			foreach ($this->gift_item_key as $gift_item_key => $gift) {
				$found = false;
				$pr_variation = get_posts( array('post_parent' => $gift['item'],'post_title' => 'proword_gift_product','post_type' => 'product_variation','posts_per_page' => 1));
				if( !empty($pr_variation) ) {
					update_post_meta( $pr_variation[0]->ID, '_price', 0);
					update_post_meta( $pr_variation[0]->ID, '_regular_price', 0);
					update_post_meta( $pr_variation[0]->ID, '_proword_gift_product', 1);
					
					if ( count( WC()->cart->get_cart() ) > 0 ) {
						foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
							$_product = $values['data'];
							if($_product->variation_id)
							{
								if ( $_product->variation_id == $pr_variation[0]->ID ) {
									$found = true;
									break;
								}
							}
						}
					}						
				}
				$product = get_product( $gift['item']);
				$permalink = $cart_page_id.'?pw_gift_add='.$gift['key'];
				$title=$product->get_title();
				$img_url = wp_get_attachment_image_src( $product->get_image_id(), 'large' );
				$img_url=$img_url[0];		
				$img_html = $title_html='';
				$item_hover='';
				//
				
				if($found==true){
					$img_html = '<img src="'.$img_url.'" />'; 
					$title_html = '<div class="gift-product-title">'.$title.'</div>'; 
					$item_hover = 'disable-hover';
					
				}else {

					$img_html = '<a href="'.$permalink.'"><img src="'.$img_url.'" /></a>'; 
					$title_html = '<div class="gift-product-title"><a href="'.$permalink.'">'.$title.'</a></div>';
					$item_hover = 'hovering';
					if(($setting['multiselect']=="no" && is_array($woocommerce->session->wc_free_select_gift)) 
						|| 
						($setting['multiselect']=="yes" && ($this->calculate_cart_subtotal()<$multiselect_cart_amount || count($woocommerce->session->wc_free_select_gift)>=$multiselect_gift_count))
						)
					{
						$img_html = '<img src="'.$img_url.'" />'; 
						$title_html = '<div class="gift-product-title">'.$title.'</div>'; 
						$item_hover = 'disable-hover';
					}										
				}
				$product_item.='
				<li>
					<div class="gift-product-item '.$item_hover.'">
						<div class="gift-product-hover" ><div><a href="'.$permalink.'">'.__('ADD GIFT','pw_wc_advanced_gift').'</a></div></div>
						'.$img_html.'
						'.$title_html.'
					</div>
				</li>';	
			}
			$did=rand(0,1000);
			$setting=get_option("pw_gift_options");
			echo '<div class="gift-popup-title">'.$setting['cart_title'].'</div>';
			echo '<ul class="wb-bxslider wb-car-car  wb-carousel-layout wb-car-cnt " id="slider_'.$did.'" >
					'.$product_item.'
				</ul>';
			echo "<script type='text/javascript'>
                jQuery(document).ready(function() {
                    slider" . $did ." =
					 jQuery('#slider_" . $did ."').bxSlider({ 
						  mode : 'horizontal' ,
						  touchEnabled : true ,
						  adaptiveHeight : true ,
						  slideMargin : ".$setting['pw_item_marrgin']." , 
						  wrapperClass : 'wb-bx-wrapper wb-car-car ' ,
						  infiniteLoop:true,
						  pager:".$setting['pw_show_pagination'].",
						  controls:".$setting['pw_show_control'].",
						  slideWidth:".$setting['pw_item_width'].",
						  minSlides: ".$setting['pw_item_per_view'].",
						  maxSlides: ".$setting['pw_item_per_view'].",
						  moveSlides: ".$setting['pw_item_per_slide'].",
						  auto:".$setting['pw_auto_play'].",
						  pause : ".$setting['pw_slide_speed'].",
						  autoHover  : true , 
 						  autoStart: true,
						  responsive:true,
					 });";
					 
					 echo "
						 jQuery('.wb-bx-wrapper .wb-bx-controls-direction a').click(function(){
							  slider" . $did .".startAuto();
						 });
						 jQuery('.wb-bx-pager a').click(function(){
							 var i = jQuery(this).data('slide-index');
							 slider" . $did .".goToSlide(i);
							 slider" . $did .".stopAuto();
							 restart=setTimeout(function(){
								slider" . $did .".startAuto();
								},1000);
							 return false;
						 });";
					 
               echo " });	
            </script>";
			
		}
		public function pw_gift_item_removed( $cart_item_key, $cart )
		{
			//die(print_r($cart_item_key));
			//print_r($cart_item_key);
			//return;
			if( empty($cart->cart_contents) ) {
				return;
			}			

			$removed_item = $cart->removed_cart_contents[ $cart_item_key ];
			if( !empty($removed_item['variation_id']) ) {
				return;
			}
			if( 'global' == $this->_wfg_type && WFG_Product_Helper::get_main_product_count() == 0 ) {
				foreach( $cart->cart_contents as $key => $content ) {
					WC()->cart->remove_cart_item( $key );
				}
			}			
		
		}
		public function pw_insert_gift_cart()
		{
			global $woocommerce,$product;
			$this->adjust_cart_rule();
			if( !is_cart() ) {
				return;
			}			
			
			$setting=get_option("pw_gift_options");
			$multiselect_cart_amount=$setting['multiselect_cart_amount'];
			$multiselect_gift_count=$setting['multiselect_gift_count'];

			if (isset($_GET['pw_gift_add']) && array_key_exists($_GET['pw_gift_add'],$this->gift_item_key))
			{				
					$gift_index="";
					$gift_index=$this->gift_item_key[$_GET['pw_gift_add']];					
					$free_product_gift = $this->create_gift_in_var( $gift_index['item'] );
					echo $free_product_gift;
					$found = false;

					if ( count( WC()->cart->get_cart() ) > 0 ) {
						foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
							$_product = $values['data'];
							if ( $_product->id == $free_product_gift ) {
								$found = true;
							}
						}

						if ( !$found ) {
							WC()->cart->add_to_cart(
									$free_product_gift,
									1,
									$gift_index['item'],
									array( 'Type' => 'Free Item')
							);
						}
					}			
			}	
		}

		
		public function create_gift_in_var($id)
		{
			$pr_variation = get_posts( array('post_parent' => $id,'post_title' => 'proword_gift_product','post_type' => 'product_variation','posts_per_page' => 1));

			if( !empty($pr_variation) ) {
				update_post_meta( $pr_variation[0]->ID, '_price', 0);
				update_post_meta( $pr_variation[0]->ID, '_regular_price', 0);
				update_post_meta( $pr_variation[0]->ID, '_proword_gift_product', 1);
				return $pr_variation[0]->ID;
			}

			$admin = get_users( 'orderby=nicename&role=administrator&number=1' );
			$variation = array(
				'post_author' => $admin[0]->ID,
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'post_name' => 'product-' . $id . '-variation',
				'post_parent' => $id,
				'post_title' => 'proword_gift_product',
				'ping_status' => 'closed',
				'post_type' => 'product_variation',
			);

			$post_id = wp_insert_post( $variation );
			update_post_meta( $post_id, '_price', 0);
			update_post_meta( $post_id, '_regular_price', 0);
			update_post_meta( $post_id, '_proword_gift_product', 1);

			return $post_id;
		}

		public function add_gift_to_order( $order_id )
		{
			global $woocommerce;
			$setting=get_option("pw_gift_options");

			if(!isset($woocommerce->session->wc_free_select_gift) || 
				empty($woocommerce->session->wc_free_select_gift) || 
				$woocommerce->session->wc_free_select_gift=="" ||
				!is_array($woocommerce->session->wc_free_select_gift))
				return;	
				
			if(is_array($woocommerce->session->wc_free_select_gift) && count($woocommerce->session->wc_free_select_gift)>0)
			{
				foreach ($woocommerce->session->wc_free_select_gift as $session_gift => $index) 
				{				
					$product_id = $index;			
					$product = get_product( $product_id );
					if ( $product->exists() && $product->is_in_stock())
					{
						$item_id = woocommerce_add_order_item( $order_id, array(
							'order_item_name' => $product->get_title(),
							'order_item_type' => 'line_item'
						) );
						if ( $item_id ) {
							woocommerce_add_order_item_meta( $item_id, '_qty', 1 );
							woocommerce_add_order_item_meta( $item_id, '_tax_class', $product->get_tax_class() );
							woocommerce_add_order_item_meta( $item_id, '_product_id', $product_id );
							woocommerce_add_order_item_meta( $item_id, '_variation_id', $product->variation_id ? $product->variation_id : '' );
							woocommerce_add_order_item_meta( $item_id, '_line_subtotal', woocommerce_format_decimal( 0, 4 ) );
							woocommerce_add_order_item_meta( $item_id, '_line_total', woocommerce_format_decimal( 0, 4 ) );
							woocommerce_add_order_item_meta( $item_id, '_line_tax', woocommerce_format_decimal( 0, 4 ) );
							woocommerce_add_order_item_meta( $item_id, '_line_subtotal_tax', woocommerce_format_decimal( 0, 4 ) );
							woocommerce_add_order_item_meta( $item_id, '_free_gift' , 'yes');
							woocommerce_add_order_item_meta( $item_id, get_option( 'pw_gift_free', 'gift_free' ) , get_option( 'pw_gift_yes', 'yes' ) );

							if ( @$product->variation_data && is_array( $product->variation_data ) )
								foreach ( $product->variation_data as $key => $value )
									woocommerce_add_order_item_meta( $item_id, esc_attr( str_replace( 'attribute_', '', $key ) ), $value );
						}
						$woocommerce->session->wc_free_select_gift = "";
					}
				}
			}
		}
		public function get_cart_item_categories($cart_item)
		{
			$categories = array();
			$current = wp_get_post_terms($cart_item['data']->id, 'product_cat');
			foreach ($current as $category) {
				$categories[] = $category->term_id;
			}
			return $categories;
		}
		public function get_cart_item_brands($cart_item)
		{
			$brands = array();
			$current = wp_get_post_terms($cart_item['data']->id, 'product_brand');
			foreach ($current as $brand) {
				$brands[] = $brand->term_id;
			}
			return $brands;
		}		

		public function get_cart_item_tags($cart_item)
		{
			$tags = array();
			$current = wp_get_post_terms($cart_item['data']->id, 'product_tag');
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

            return (float)$cart_subtotal;
        }		
}
new pw_class_woocommerce_gift_discunt_cart_new();
?>