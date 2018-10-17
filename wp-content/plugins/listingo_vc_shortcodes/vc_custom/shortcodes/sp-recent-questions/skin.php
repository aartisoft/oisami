<?php
/**
 * @ Visual Composer Shortcode
 * @ Class Blog Posts
 * @ return {HTML}
 * @ Autor: Themographics
 */
if (!class_exists('SC_VC_Skin_Listingo_Recent_Questions')) {

    class SC_VC_Skin_Listingo_Recent_Questions extends SC_VC_Core {

        public function __construct() {
            add_shortcode("listingo_vc_recent_questions", array(
                &$this,
                "shortCodeCallBack"));
        }

        /**
         * @ Front end Init
         * @ return {HTML}
         */
        public function shortCodeCallBack($args, $content = '') {
            extract(shortcode_atts(array(                
                "section_heading" => '',
                "posts_by" => 'by_catgories',
                "categories" => '',
                "specific_posts" => '',
                "no_of_posts" => '',
                "show_pagination" => 'on',
                "order" => '',
                "orderby" => '',
                "custom_columns" => '',
                "column_lg" => '',
                "column_md" => '',
                "column_sm" => '',
                "column_xs" => '',
                "custom_id" => '',
                "custom_classes" => '',
                "css" => '',
                            ), $args));

            $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), 'listingo_vc_recent_questions', $args);

            $classes[] = $custom_classes;
            $classes[] = $css_class;

            if (isset($custom_columns) && $custom_columns === 'yes') {
                $item_classes = $column_xs . ' ' . $column_sm . ' ' . $column_md . ' ' . $column_lg;
            } else {
                $item_classes = 'col-xs-6 col-sm-6 col-md-4 col-lg-4';
            }
            
            global $paged;

            $pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
            $pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
            //paged works on single pages, page - works on homepage
            $paged = max($pg_page, $pg_paged);

            if (isset($posts_by) && $posts_by === 'by_posts' && !empty($specific_posts)
            ) {
                $posts_in['post__in'] = explode(',', $specific_posts);
            } else {
                $cat_sepration = array();
                if (isset($categories) && !empty($categories)) {
                    $cat_sepration = explode(',', $categories);
                }

                if (isset($cat_sepration) && !empty($cat_sepration)) {
                    $slugs = array();
                    foreach ($cat_sepration as $key => $value) {
                        $term = get_term($value, 'category');
                        $slugs[] = $term->slug;
                    }

                    $filterable = $slugs;
                    $tax_query['tax_query'] = array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'category',
                            'terms' => $filterable,
                            'field' => 'slug',
                    ));
                }
            }

            $show_posts = !empty($no_of_posts) ? $no_of_posts : '-1';

            //total posts Query 
            $query_args = array(
                'posts_per_page' => -1,
                'post_type' => 'sp_questions',
                'order' => $order,
                'orderby' => $orderby,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1);

            //By Categories
            if (!empty($meta_args)) {
                $query_args = array_merge($query_args, $meta_args);
            }
            //By Posts 
            if (!empty($posts_in)) {
                $query_args = array_merge($query_args, $posts_in);
            }
            $query = new WP_Query($query_args);
            $count_post = $query->post_count;

            //Main Query 
            $query_args = array(
                'posts_per_page' => $show_posts,
                'post_type' => 'sp_questions',
                'paged' => $paged,
                'order' => $order,
                'orderby' => $orderby,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1);

            //By Categories
            if (!empty($meta_args)) {
                $query_args = array_merge($query_args, $meta_args);
            }
            //By Posts 
            if (!empty($posts_in)) {
                $query_args = array_merge($query_args, $posts_in);
            }

            $query = new WP_Query($query_args);
            ob_start();
            ?>
            
            <div class="sp-sc-questions tg-haslayout">
                <?php if (!empty($section_heading) || !empty($content)) { ?>
                    <div class="col-xs-12 col-sm-12 col-md-10 col-md-push-1 col-lg-8 col-lg-push-2">
                        <div class="tg-sectionhead">
                            <?php if (!empty($section_heading)) { ?>
                                <div class="tg-sectiontitle">
                                    <h2><?php echo esc_attr($section_heading); ?></h2>
                                </div>
                            <?php } ?>
                            <?php if (!empty($content)) { ?>
                                <div class="tg-description">
                                    <?php echo wp_kses_post(wpautop(do_shortcode($content))); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="tg-content tg-companyfeaturebox">
                    <?php
                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            global $post;
                            $question_by = get_post_meta($post->ID, 'question_by', true);
                            $question_to = get_post_meta($post->ID, 'question_to', true);
                            $category = get_post_meta($post->ID, 'question_cat', true);
                
                            $category_icon = '';
                            $category_color = '';
                            $bg_color = '';
                            
                            if (function_exists('fw_get_db_post_option') && !empty( $category )) {
                                $categoy_bg_img = fw_get_db_post_option($category, 'category_image', true);
                                $category_icon  = fw_get_db_post_option($category, 'category_icon', true);
                                $category_color = fw_get_db_post_option($category, 'category_color', true);
                                
                                $bg_color = fw_get_db_post_option($category, 'category_color', true);
                                if (!empty($bg_color)) {
                                    $bg_color = 'style=background:' . $bg_color;
                                }
                            }
                            ?>
                            <div class="col-xs-12 col-sm-12 col-md-6  col-lg-6 tg-verticaltop">
                                <div class="tg-question">
                                    <div class="tg-questioncontent">
                                        <div class="tg-answerholder spq-v2">
                                            <?php
                                                if (isset($category_icon['type']) && $category_icon['type'] === 'icon-font') {
                                                    do_action('enqueue_unyson_icon_css');
                                                    if (!empty($category_icon['icon-class'])) {
                                                        ?>
                                                        <figure class="tg-docimg"><span class="<?php echo esc_attr($category_icon['icon-class']); ?> tg-categoryicon" style="background: <?php echo esc_attr($category_color); ?>;"></span></figure>
                                                        <?php
                                                    }
                                                } else if (isset($category_icon['type']) && $category_icon['type'] === 'custom-upload') {
                                                    if (!empty($category_icon['url'])) {
                                                        ?>
                                                        <figure class="tg-docimg"><em><img src="<?php echo esc_url($category_icon['url']); ?>" alt="<?php esc_html_e('category', 'listingo_vc_shortcodes'); ?>"></em></figure>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                            <div class="tg-questionbottom">
                                                <div class="sp-title-holder">
                                                    <h4><a href="<?php echo esc_url(get_permalink()); ?>"> <?php echo esc_attr(get_the_title()); ?> </a></h4>
                                                    <?php if (!empty($category)) { ?>
                                                        <a class="tg-themetag tg-categorytag" <?php echo esc_attr($bg_color); ?> href="<?php echo esc_url(get_permalink($category)); ?>">
                                                            <?php echo esc_attr(get_the_title($category)); ?>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                                <?php 
													if (!function_exists('fw_ext_get_total_votes_and_answers_html')) {
														fw_ext_get_total_votes_and_answers_html($post->ID);
													}
												?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tg-matadatahelpfull">
                                        <?php fw_ext_get_views_and_time_html($post->ID);?>
                                        <?php fw_ext_get_votes_html($post->ID,esc_html__('Is this helpful?', 'listingo_vc_shortcodes'));?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } wp_reset_postdata();
                    }
                    ?>
                    <?php if (isset($atts['show_pagination']) && $atts['show_pagination'] == 'yes') : ?>
                        <div class="col-md-12">
                            <?php listingo_prepare_pagination($count_post, $show_posts); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>        
            <?php
            echo ob_get_clean();
            }
        }

    new SC_VC_Skin_Listingo_Recent_Questions();
}
        