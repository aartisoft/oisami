<?php
add_shortcode('pw_woo_gift_rule_list', 'pw_woo_gift_rule_list_function');

function pw_woo_gift_rule_list_function($atts, $content = null)
{
    global $woocommerce;
    extract(shortcode_atts(array(
        'rules' => '',
        'pw_date' => '',
        'pw_cat_dep' => '',
        'nb_products' => '',
        'cart_amount' => '',
        'header_backcolor' => '',
        'header_text_color' => '',
        'pw_item_width' => '200',
        'pw_item_marrgin' => '10',
        'pw_slide_direction' => 'horizontal',
        'pw_show_pagination' => '',
        'pw_show_control' => '',
        'pw_item_per_view' => '3',
        'pw_item_per_slide' => '1',
        'pw_slide_speed' => '2000',
        'pw_auto_play' => '',
        'pw_countdown_style' => '',
    ), $atts));
    $ret = '';
    $rules = array_map('intval', explode(',', $rules));
    //	print_r($rules);
//	if($rules!="")
    //	$rules=array($rules);

    $setting = get_option("pw_gift_options");

    $query_meta_query = array('relation' => 'AND');
    $query_meta_query[] = array(
        'key' => 'status',
        'value' => "active",
        'compare' => '=',
    );
    $cart_contents = $woocommerce->cart->cart_contents;
    $matched_products = get_posts(
        array(
            'post_type' => 'pw_gift_rule',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'post__in' => $rules,
            'no_found_rows' => true,
            'orderby' => 'modified',
            'meta_query' => $query_meta_query,
        )
    );

    if (!is_array($matched_products) || count($matched_products) <= 0)
        return;


    foreach ($matched_products as $p) {
        //echo $p.'<br>';
        //Get Name Rule
        $pw_name = get_post_meta($p, 'pw_name', true);
        //End Get Name Rule

        //Get description Rule
        $pw_rule_description = get_post_meta($p, 'pw_rule_description', true);
        //End Get description Rule

        //Get From Rule
        $pw_from = get_post_meta($p, 'pw_from', true);
        //End Get From Rule
        //Get to Rule
        $pw_to = get_post_meta($p, 'pw_to', true);
        //End Get to Rule

        //Get Product Gift
        $pw_gifts = get_post_meta($p, 'pw_gifts', true);
        $product_item = '';
        if (is_array($pw_gifts) || is_object($pw_gifts)) {
            foreach ($pw_gifts as $r) {
                $product = get_product($r);
                $img_url = wp_get_attachment_image_src($product->get_image_id(), 'large');
                $img_url = $img_url[0];
                //echo '<img src="'.$img_url[0].'" />';
                $title = $product->get_title();
                $permalink = $product->get_permalink();


                $img_html = '<img src="' . $img_url . '" />';
                $title_html = '<div class="gift-product-title">' . $title . '</div>';
                $item_hover = 'hovering';
                $product_item .= '<div class="gift-product-item ' . $item_hover . '">
						<div class="gift-product-hover" ><div><a href="' . $permalink . '"></a></div></div>
						' . $img_html . '
						' . $title_html . '
					</div>
				';


//				$product_item.='
//					<li>
//						<div class="gift-product-item">
//							<a href="'.$permalink.'"><img src="'.$img_url.'" /></a>
//							<div class="gift-product-title"><a href="'.$permalink.'">'.$title.'</a>
//						</div>
//					</li>';
            }
            //End Get Product Gift
        }

        //Get Gategory
        $cat_depends = "";
        $category_depends = get_post_meta($p, 'category_depends', true);
        if ($category_depends == "yes") {
            $pw_category_depends = get_post_meta($p, 'pw_category_depends', true);
            foreach ($pw_category_depends as $r) {
                $term = get_term($r, 'product_cat');
                $term_link = get_term_link($term);
                $cat_depends .= '<li><a href="' . $term_link . '">' . $term->name . '</a><span>/</span></li>';

            }
        }
        //End Gategory

        //Number Cart
        //	$cri_num_products=$cri_cart_amunt="";
        //	$criteria_nb_products=get_post_meta($p,'criteria_nb_products',true);
        //	$cri_num_products .='You need to have:'.$criteria_nb_products.'product(s) in your cart';
        //End Number Cart

        //Amount Cart
        //$pw_cart_amount=get_post_meta($p,'pw_cart_amount',true);

        //$cri_cart_amunt.='Minimum cart amount:'.woocommerce_price($pw_cart_amount);
        //End Amount Cart
        $did = rand(0, 1000);
        $ret .= '<div class="gift-role-cnt role-' . $did . '">
				<div class="gift-role-header">
					<div class="gift-role-title-cnt">
						<div class="gift-role-title">
							<i class="fa fa-check"></i>
							<h3>' . $pw_name . '</h3>
						</div>
						<div class="gift-role-desc">
							<span>' . $pw_rule_description . '</span>
						</div>
					</div>';


        $ret .= '</div>  <!-- gift-role-header-->
				 <div class="gift-products-cnt">';
        if ($pw_date == '1' && ($pw_from != '' && $pw_to != '')) {
            $ret .= '
					<div class="gift-counter-cnt">
						<div class="fl-rule-coundown countdown-' . $did . '	">
							<ul class="fl-' . $pw_countdown_style . ' fl-medium fl-countdown countdown_' . $did . '">
							  <li><span class="days">--</span><p class="days_text">Days</p></li>
								<li class="seperator">:</li>
								<li><span class="hours">--</span><p class="hours_text">' . $setting['Hour'] . '</p></li>
								<li class="seperator">:</li>
								<li><span class="minutes">--</span><p class="minutes_text">' . $setting['Minutes'] . '</p></li>
								<li class="seperator">:</li>
								<li><span class="seconds">--</span><p class="seconds_text">' . $setting['Seconds'] . '</p></li>
							</ul>
						</div>		
					</div>';
        }
        $ret .= '<div class="owl-carousel wb-car-car  wb-carousel-layout wb-car-cnt " id="slider_' . $did . '" >
					' . $product_item . '
				</div>';
//        $ret .= '
//					<div class="gift-car-cnt">
//						<ul class="wb-bxslider wb-car-car  wb-carousel-layout wb-car-cnt " id="slider_' . $did . '" >
//							' . $product_item . '
//						</ul>
//					</div>
//				 </div><!--gift-product-cnt -->';
					
			$ret .= '<div class="gift-depends-cnt">';
        if ($pw_cat_dep == '1' && $cat_depends != '') {
            $ret .= '<div class="gift-depends-item">
								<div class="gift-depends-item-cnt">
									<div class="gift-depends-title">CATEGORY DEPENDENCY</div>
									<ul class="gift-depends-ul">
										' . $cat_depends . '
									</ul>
								</div>
							</div><!--gift-depends-item -->';
        }
        $criteria_nb_products = get_post_meta($p, 'criteria_nb_products', true);
        if ($nb_products == '1' && ($criteria_nb_products != '')) {
            $ret .= '<div class="gift-depends-item">
								<div class="gift-depends-item-cnt">
									<div class="gift-check-icon"></div>
									<div class="gift-depends-title">YOU NEED TO HAVE: ' . $criteria_nb_products . ' PRODUCT(S) IN YOUR CART</div>
								</div>
							</div><!--gift-depends-item -->';

        }
        $pw_cart_amount = get_post_meta($p, 'pw_cart_amount', true);
        if ($cart_amount == '1' && $pw_cart_amount != '' && $pw_cart_amount != 0) {
            $pw_cart_amount .= 'Minimum cart amount:' . woocommerce_price($pw_cart_amount);
            $ret .= '<div class="gift-depends-item">
							   <div class="gift-depends-item-cnt">
									<div class="gift-check-icon"></div>
									<div class="gift-depends-title">Minimum cart amount: ' . woocommerce_price($pw_cart_amount) . '</div>
								</div>
						   </div><!--gift-depends-item -->';
        }
        $ret .= '   
				 </div><!--gift-depends-cnt -->
					
				</div><!--gift-role-cnt -->';

        if ($pw_date == '1' && ($pw_from != '' && $pw_to != '')) {
            $ret .= '<script type="text/javascript">
						jQuery(".countdown_' . $did . '").countdown({
							date: "' . $pw_to . '",
							offset: -8,
							day: "Day",
							days: "Days"
						}, function () {
						});
					</script>';
        }
        $ret .= "
			<style>
				.role-" . $did . " .gift-role-header{ background:" . $header_backcolor . "; color:" . $header_text_color . " }
					.role-" . $did . " .gift-role-header:after{ border-color:" . $header_backcolor . " transparent transparent transparent }
			</style>
            <script type='text/javascript'>
						jQuery(document).ready(function() {
							  jQuery('#slider_" . $did . "').owlCarousel({
								  margin : " . $pw_item_marrgin . " , 
								  loop:true,
								  dots:" . ($pw_show_pagination == 'true' ? 'true' : 'false') . ",
								  nav:" . ($pw_show_control == 'true' ? 'true' : 'false') . ",
								  slideBy: " . $pw_item_per_slide . ",
								  autoplay:" . $pw_auto_play . ",
								  autoplayTimeout : " . $pw_slide_speed . ",
								  responsive:{
							        0:{
							            items:1
							        },
							        600:{
							            items:2
							        },
							        1000:{
							            items:" . $pw_item_per_view . "
							        }
							    },
							    autoplayHoverPause: true,
							    navText: [ '>', '<' ]
							 })          		
                         })
                 </script>";
//			<script type='text/javascript'>
//					jQuery(document).ready(function() {
//						slider" . $did . " =
//						 jQuery('#slider_" . $did . "').bxSlider({
//							  mode : 'horizontal' ,
//							  touchEnabled : true ,
//							  adaptiveHeight : true ,
//							  slideMargin : " . $pw_item_marrgin . " ,
//							  wrapperClass : 'wb-bx-wrapper wb-car-car ' ,
//							  infiniteLoop:true,
//							  pager:" . ($pw_show_pagination == 'true' ? 'true' : 'false') . ",
//							  controls:" . ($pw_show_control == 'true' ? 'true' : 'false') . ",
//							  slideWidth:" . $pw_item_width . ",
//							  minSlides: " . $pw_item_per_view . ",
//							  maxSlides: " . $pw_item_per_view . ",
//							  moveSlides: " . $pw_item_per_slide . ",
//							  auto: " . $pw_auto_play . ",
//							  pause : " . $pw_slide_speed . ",
//							  autoHover  : true ,
//							  autoStart: true,
//							  responsive:false,
//						 });
        if ($pw_auto_play == 'true') {
            $ret .= "
							 jQuery('.wb-bx-wrapper .wb-bx-controls-direction a').click(function(){
								  slider" . $did . ".startAuto();
							 });
							 jQuery('.wb-bx-pager a').click(function(){
								 var i = jQuery(this).data('slide-index');
								 slider" . $did . ".goToSlide(i);
								 slider" . $did . ".stopAuto();
								 restart=setTimeout(function(){
									slider" . $did . ".startAuto();
									},1000);
								 return false;
							 });";
        }
        $ret .= "
				</script>";
    }

    //	$rules
    return $ret;
}

?>