<?php

/**
 * @Woocommerce Customization
 * return {}
 */
if (!class_exists('listingo_woocommerace')) {

    class listingo_woocommerace {

        function __construct() {

            //add_filter('woocommerce_enqueue_styles', '__return_false');
            add_filter('woocommerce_register_post_type_product', array(&$this, 'listingo_label_woo'));
            add_action('woocommerce_product_options_general_product_data', array(&$this, 'listingo_package_meta'));
            add_action('woocommerce_process_product_meta', array(&$this, 'listingo_save_package_meta'));
			add_action( 'listingo_woocommerce_add_to_cart_button', array(&$this,'listingo_woocommerce_add_to_cart_button'), 10 );
			add_action( 'woocommerce_checkout_fields', array( &$this, 'listingo_custom_checkout_update_customer' ), 10);
        }
		
		
		/**
		 * @Checkout First and last name 
		 * @return {}
		 */

		public function listingo_custom_checkout_update_customer( $fields ){
			$user = wp_get_current_user();
			$first_name = $user ? $user->user_firstname : '';
			$last_name = $user ? $user->user_lastname : '';
			$fields['billing']['billing_first_name']['default'] = $first_name;
			$fields['billing']['billing_last_name']['default']  = $last_name;
			return $fields;
		}

		/**
		 * @Add to cart button
		 * @return {}
		 */
		public function listingo_woocommerce_add_to_cart_button(){
			global $product;
			echo apply_filters( 'woocommerce_loop_add_to_cart_link',
				sprintf( '<a href="%s" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="%s product_type_%s ajax_add_to_cart  tg-btnaddtocart"><i class="lnr lnr-cart"></i><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></a>',
					esc_url( $product->add_to_cart_url() ),
					esc_attr( $product->get_id() ),
					esc_attr( $product->get_sku() ),
					esc_attr( isset( $quantity ) ? $quantity : 1 ),
					$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
					esc_attr( $product->get_type() ),
					esc_html( $product->add_to_cart_text() )
				),
			$product );
		}
		
        /**
         * @Rename Product Menu
         * return {}
         */
        public function listingo_label_woo($args) {
            $labels = array(
                'name' => esc_html__('Packages/Products', 'listingo'),
                'singular_name' => esc_html__('Packages/Products', 'listingo'),
                'menu_name' => esc_html__('Packages/Products', 'listingo'),
                'add_new' => esc_html__('Add Package/Product', 'listingo'),
                'add_new_item' => esc_html__('Add New Package/Product', 'listingo'),
                'edit' => esc_html__('Edit Package/Product', 'listingo'),
                'edit_item' => esc_html__('Edit Package/Product', 'listingo'),
                'new_item' => esc_html__('New Package/Product', 'listingo'),
                'view' => esc_html__('View Package/Product', 'listingo'),
                'view_item' => esc_html__('View Package/Product', 'listingo'),
                'search_items' => esc_html__('Search Packages/Product', 'listingo'),
                'not_found' => esc_html__('No Packages/Products found', 'listingo'),
                'not_found_in_trash' => esc_html__('No Packages/Products found in trash', 'listingo'),
                'parent' => esc_html__('Parent Package/Product', 'listingo')
            );

            $args['labels'] = $labels;
            $args['description'] = esc_html__('This is where you can add new tours to your store.', 'listingo');
            return $args;
        }

        /**
         * @Package Meta save
         * return {}
         */
        public function listingo_save_package_meta($post_id) {
            update_post_meta($post_id, 'sp_package_type', esc_attr($_POST['sp_package_type']));
            update_post_meta($post_id, 'sp_duration', esc_attr($_POST['sp_duration']));
            update_post_meta($post_id, 'sp_featured', esc_attr($_POST['sp_featured']));
			
			if ( apply_filters('listingo_get_theme_settings', 'jobs') == 'yes') {
            	update_post_meta($post_id, 'sp_jobs', esc_attr($_POST['sp_jobs']));
			}
			
            update_post_meta($post_id, 'sp_appointments', esc_attr($_POST['sp_appointments']));
            update_post_meta($post_id, 'sp_banner', esc_attr($_POST['sp_banner']));
            update_post_meta($post_id, 'sp_insurance', esc_attr($_POST['sp_insurance']));
            update_post_meta($post_id, 'sp_favorites', esc_attr($_POST['sp_favorites']));
            update_post_meta($post_id, 'sp_teams', esc_attr($_POST['sp_teams']));
            update_post_meta($post_id, 'sp_hours', esc_attr($_POST['sp_hours']));
            update_post_meta($post_id, 'sp_articles', esc_attr($_POST['sp_articles']));
			update_post_meta($post_id, 'sp_page_design', esc_attr($_POST['sp_page_design']));
			update_post_meta($post_id, 'sp_gallery_photos', esc_attr($_POST['sp_gallery_photos']));
			update_post_meta($post_id, 'sp_videos', esc_attr($_POST['sp_videos']));
			update_post_meta($post_id, 'sp_photos_limit', esc_attr($_POST['sp_photos_limit']));
			update_post_meta($post_id, 'sp_banners_limit', esc_attr($_POST['sp_banners_limit']));
			
			if ( apply_filters('listingo_is_contact_informations_enabled', 'yes','','') === 'yes') {
				update_post_meta($post_id, 'sp_contact_information', esc_attr($_POST['sp_contact_information']));
			}
        }

        /**
         * @Package Meta
         * return {}
         */
        public function listingo_package_meta($args) {
            global $woocommerce, $post;

            woocommerce_wp_select(
                    array(
                        'id' => 'sp_package_type',
                        'class' => 'sp_package_type',
                        'label' => esc_html__('Package Type?', 'listingo'),
                        'desc_tip' => 'true',
                        'description' => esc_html__('If packages type will be customer then package will be display in customer dashboard, and if type will be providers then this will show in providers dashboard.', 'listingo'),
                        'options' => array(
                            '' => esc_html__('Package Type', 'listingo'),
                            'customer' => esc_html__('For Customers ( Visitors )', 'listingo'),
                            'provider' => esc_html__('For Providers ( Business/Professional )', 'listingo'),
                        )
                    )
            );
            woocommerce_wp_text_input(
                    array(
                        'id' => 'sp_duration',
                        'class' => 'sp_duration sp-woo-field',
                        'label' => esc_html__('Package Duration', 'listingo'),
                        'placeholder' => '10',
                        'desc_tip' => 'true',
                        'description' => esc_html__('Add duration(days) for this package. Please add only integer value. eg : 30', 'listingo'),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min' => '1'
                        )
                    )
            );
            woocommerce_wp_text_input(
                    array(
                        'id' => 'sp_featured',
                        'class' => 'sp_featured  sp_provider sp-woo-field',
                        'label' => esc_html__('Feature duration', 'listingo'),
                        'placeholder' => '',
                        'desc_tip' => 'true',
                        'description' => esc_html__('Add duration(days) for featured listing. Please add only integer value. eg : 30, leave it empty to exlude from featured listing', 'listingo'),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min' => '1'
                        )
                    )
            );
			
			if ( apply_filters('listingo_get_theme_settings', 'jobs') === 'yes') {
				woocommerce_wp_text_input(
						array(
							'id' => 'sp_jobs',
							'class' => 'sp_jobs sp-woo-field',
							'label' => esc_html__('Jobs included?', 'listingo'),
							'placeholder' => '',
							'desc_tip' => 'true',
							'description' => esc_html__('Add number of jobs if your want to enable jobs in this package. Leave it empty to exclude.', 'listingo'),
							'type' => 'number',
							'custom_attributes' => array(
								'step' => '1',
								'min' => '0'
							)
						)
				);
			}
			
            woocommerce_wp_text_input(
                    array(
                        'id' => 'sp_articles',
                        'class' => 'sp_articles sp_provider sp-woo-field',
                        'label' => esc_html__('Articles included?', 'listingo'),
                        'placeholder' => '',
                        'desc_tip' => 'true',
                        'description' => esc_html__('Add number of articles if your want to enable articles in this package. Leave it empty to exclude.', 'listingo'),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min' => '0'
                        )
                    )
            );

            woocommerce_wp_select(
                    array(
                        'id' => 'sp_appointments',
                        'class' => 'sp_appointments  sp_provider sp-woo-field',
                        'label' => esc_html__('Appointments included?', 'listingo'),
                        'options' => array(
                            'no' => esc_html__('No', 'listingo'),
                            'yes' => esc_html__('Yes', 'listingo'),
                        )
                    )
            );

            woocommerce_wp_select(
                    array(
                        'id' => 'sp_banner',
                        'class' => 'sp_banner sp_provider sp-woo-field',
                        'label' => esc_html__('Profile banner included?', 'listingo'),
                        'options' => array(
                            'no' => esc_html__('No', 'listingo'),
                            'yes' => esc_html__('Yes', 'listingo'),
                        )
                    )
            );

            woocommerce_wp_select(
                    array(
                        'id' => 'sp_insurance',
                        'class' => 'sp_insurance sp_provider sp-woo-field',
                        'label' => esc_html__('Insurance included?', 'listingo'),
                        'options' => array(
                            'no' => esc_html__('No', 'listingo'),
                            'yes' => esc_html__('Yes', 'listingo'),
                        )
                    )
            );

            woocommerce_wp_select(
                    array(
                        'id' => 'sp_favorites',
                        'class' => 'sp_favorites sp-woo-field',
                        'label' => esc_html__('favorites included?', 'listingo'),
                        'options' => array(
                            'no' => esc_html__('No', 'listingo'),
                            'yes' => esc_html__('Yes', 'listingo'),
                        )
                    )
            );

            woocommerce_wp_select(
                    array(
                        'id' => 'sp_teams',
                        'class' => 'sp_teams sp_provider sp-woo-field',
                        'label' => esc_html__('Teams included?', 'listingo'),
                        'options' => array(
                            'no' => esc_html__('No', 'listingo'),
                            'yes' => esc_html__('Yes', 'listingo'),
                        )
                    )
            );

            woocommerce_wp_select(
                    array(
                        'id' => 'sp_hours',
                        'class' => 'sp_hours sp_provider sp-woo-field',
                        'label' => esc_html__('Business hours included?', 'listingo'),
                        'options' => array(
                            'no' => esc_html__('No', 'listingo'),
                            'yes' => esc_html__('Yes', 'listingo'),
                        )
                    )
            );
			
			woocommerce_wp_select(
                    array(
                        'id' => 'sp_page_design',
                        'class' => 'sp_page_design sp_provider sp-woo-field',
                        'label' => esc_html__('Provider Page Design?', 'listingo'),
						'desc_tip' => 'true',
						'description' => esc_html__('By enabling this feature provider will be able to sort page section and also will be able to select page design.', 'listingo'),
                        'options' => array(
                            'no' => esc_html__('No', 'listingo'),
                            'yes' => esc_html__('Yes', 'listingo'),
                        )
                    )
            );
			
			if ( apply_filters('listingo_is_contact_informations_enabled', 'yes','','') === 'yes') {
				woocommerce_wp_select(
						array(
							'id' => 'sp_contact_information',
							'class' => 'sp_contact_information sp_provider sp-woo-field',
							'label' => esc_html__('Include contact informations?', 'listingo'),
							'desc_tip' => 'true',
							'description' => esc_html__('Include contact infomation( phone number and email address )', 'listingo'),
							'options' => array(
								'no' => esc_html__('No', 'listingo'),
								'yes' => esc_html__('Yes', 'listingo'),
							)
						)
				);
			}
			
			 woocommerce_wp_text_input(
                    array(
                        'id' => 'sp_gallery_photos',
                        'class' => 'sp_gallery_photos sp_provider sp-woo-field',
                        'label' => esc_html__('Number of gallery photos?', 'listingo'),
                        'placeholder' => '',
                        'desc_tip' => 'true',
                        'description' => esc_html__('Add number of gallery photos. Default will be from Theme Settings > Directory Settings', 'listingo'),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min' => '1'
                        )
                    )
            );
			
			 woocommerce_wp_text_input(
                    array(
                        'id' => 'sp_videos',
                        'class' => 'sp_videos sp_provider sp-woo-field',
                        'label' => esc_html__('Number of videos?', 'listingo'),
                        'placeholder' => '',
                        'desc_tip' => 'true',
                        'description' => esc_html__('Add number of video links. Default will be from Theme Settings > Directory Settings', 'listingo'),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min' => '1'
                        )
                    )
            );
			
			woocommerce_wp_text_input(
                    array(
                        'id' => 'sp_photos_limit',
                        'class' => 'sp_photos_limit sp_provider sp-woo-field',
                        'label' => esc_html__('Number of profile photos?', 'listingo'),
                        'placeholder' => '',
                        'desc_tip' => 'true',
                        'description' => esc_html__('Add number of profile photos. Default will be from Theme Settings > Directory Settings', 'listingo'),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min' => '1'
                        )
                    )
            );
			
			woocommerce_wp_text_input(
                    array(
                        'id' => 'sp_banners_limit',
                        'class' => 'sp_banners_limit sp_provider sp-woo-field',
                        'label' => esc_html__('Number of banner photos?', 'listingo'),
                        'placeholder' => '',
                        'desc_tip' => 'true',
                        'description' => esc_html__('Add number of banner photos. Default will be from Theme Settings > Directory Settings', 'listingo'),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min' => '1'
                        )
                    )
            );
        }

    }

    new listingo_woocommerace();
}