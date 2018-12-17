<?php

add_action('wp_head', 'pw_woo_gift_rule_popup_function');
function pw_woo_gift_rule_popup_function()
{

    $query_meta_query = array('relation' => 'AND');
    $query_meta_query[] = array(
        'key' => 'status',
        'value' => "active",
        'compare' => '=',
    );
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
    if (!is_array($matched_products) || count($matched_products) <= 0)
        return;
    $setting = get_option("pw_gift_options");

    echo '
			
			<div class="gift-popup" id="wg-popup"  >
				
				<div class="gift-popup-title">' . $setting['popup_title'] . '</div>';
    $product_item = "";

    foreach ($matched_products as $p) {
        $pw_name = get_post_meta($p, 'pw_name', true);
        $cat_depends = "";
        $category_depends = get_post_meta($p, 'category_depends', true);
        $category_role = '';
        if ($category_depends == "yes") {
            $pw_category_depends = get_post_meta($p, 'pw_category_depends', true);
            if (get_post_meta($p, 'pw_category_depends', true) != "") {
                foreach ($pw_category_depends as $r) {
                    $term = get_term($r, 'product_cat');
                    $term_link = get_term_link($term);
                    $category_role .= '<a href="' . $term_link . '">' . $term->name . '</a><span>/</span>';

                }
            }
        }

        $pw_cart_amount = get_post_meta($p, 'pw_cart_amount', true);
        $pw_cart_amount_role = '';
        if ($pw_cart_amount != '' && $pw_cart_amount != 0) {
            $pw_cart_amount_role .= 'MINIMUM CART AMOUNT <span class="gift-popup-val">' . wc_price($pw_cart_amount) . '</span>';
        }

        $criteria_nb_products = get_post_meta($p, 'criteria_nb_products', true);
        $criteria_nb_products_role = '';
        if ($criteria_nb_products != '') {
            $criteria_nb_products_role = 'NEED AT LEAST <span class="gift-popup-val">' . $criteria_nb_products . '</span> PRODUCT(S) IN YOUR CART';
        }

        $pw_from = get_post_meta($p, 'pw_from', true);
        //End Get From Rule
        //Get to Rule
        $pw_to = get_post_meta($p, 'pw_to', true);
        //End Get to Rule

        //Get Product Gift
        $pw_gifts_metod = get_post_meta($p, 'pw_gifts_metod', true);
        if ($pw_gifts_metod == "product") {
            $pw_gifts = get_post_meta($p, 'pw_gifts', true);
        }
        else {
            $pw_gifts_category = get_post_meta($p, 'pw_gifts_category', true);
            $query_meta_query[] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => $pw_gifts_category
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
            $pw_gifts = $matched_products;
        }

        //	if(($pw_to!="" && $blogtime>$pw_to) || ($pw_from!="" && $blogtime<$pw_from))
        //	{

        //	}

        foreach ((array)$pw_gifts as $r) {

            $product = wc_get_product($r);
            $img_url = wp_get_attachment_image_src($product->get_image_id(), 'large');
            $img_url = $img_url[0];
            $title = $product->get_title();
            $permalink = $product->get_permalink();
            $product_item .= '
					
						<div class="gift-product-item" >
							<a href="' . $permalink . '"><img src="' . $img_url . '" class="wg-img" /></a>
							<div class="gift-product-title"><a href="' . $permalink . '">' . $title . '</a>
							<div class="gift-popup-depends">';

                            $product_item .= ($category_role != '') ? '<div class="gift-popup-depends-item">CATEGORY DEPENDS: ' . $category_role . '</div>' : '';

                            $product_item .= ($criteria_nb_products_role != '') ? '
								<div class="gift-popup-depends-item">' . $criteria_nb_products_role . '</div>' : '';

                            $product_item .= ($pw_cart_amount_role != '') ? '
								<div class="gift-popup-depends-item">' . $pw_cart_amount_role . '</div>' : '';

            $product_item .= '
							</div>
						</div>
					</div>
					';
        }

    }
    $did = rand(0, 1000);
    echo '
			<div class="gift-popup-car">
				<div class="owl-carousel wb-car-car  wb-carousel-layout wb-car-cnt slider_' . $did . '" id="" >
						' . $product_item . '
				</div>
			</div>';
    echo '</div>
		';


    echo '
		<script language="javascript">
			
			jQuery(document).ready(function() {
					lightcase.start({
				        href: "#wg-popup",
				        closeOnOverlayClick : false
					});
					jQuery("#lightcase-content").css("visibility","hidden");
					//jQuery(".wg-cc").lightcase({showSequenceInfo: false});
				});
		</script>
		';




	echo "<script type='text/javascript'>
                jQuery(document).ready(function() {
					var owl;
                    setTimeout(function(){
						jQuery.when(
						    
						 jQuery(document).find('.slider_" . $did . "').owlCarousel({
							  margin : " . $setting['popup_pw_item_marrgin'] . " , 
							  loop:true,
							  dots:" . $setting['popup_pw_show_pagination'] . ",
							  nav:" . $setting['popup_pw_show_control'] . ",
							  slideBy: " . $setting['popup_pw_item_per_slide'] . ",
							  autoplay:" . $setting['popup_pw_auto_play'] . ",
							  autoplayTimeout : " . $setting['popup_pw_slide_speed'] . ",
							  responsive:{
						        0:{
						            items:1
						        },
						        600:{
						            items:2
						        },
						        1000:{
						            items:" . $setting['popup_pw_item_per_view'] . "
						        }
						    },
						    autoplayHoverPause: true,
						    navText: [ '>', '<' ]
						 })   ,
						lightcase.resize()
					    	 

						";

				echo "
					
					).done(function( x ) {
						jQuery('.gift-popup').css('visibility','visible');
						jQuery('#lightcase-content').css('visibility','visible');
						
					});
					},500);
					";

	echo " });
   </script>";

}

?>