<?php
/**
 *
 * The template part for displaying the dashboard Invoices.
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */
get_header();
global $current_user;
$customer_orders = get_posts( array(
    'numberposts' => -1,
    'meta_key'    => '_customer_user',
    'meta_value'  => $current_user->ID,
    'post_type'   => wc_get_order_types(),
    'post_status' => array_keys( wc_get_order_statuses() ),
) );
?>
<?php echo 'Invoices'; ?>
<?php get_footer(); ?>