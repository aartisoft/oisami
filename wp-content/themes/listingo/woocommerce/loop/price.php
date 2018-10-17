<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
?>

<?php do_action( 'woocommerce_product_meta_start' ); ?>
<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<p>' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</p>' ); ?>
<?php do_action( 'woocommerce_product_meta_end' ); ?>

<?php 
	echo '<div class="dados-product"><span>' . get_the_term_list( $post->ID, 'laboratorio', '<p><strong>Laboratorio:</strong> ', ', ', '</p>') . '';
	//echo '' . get_the_term_list( $post->ID, 'unidade', '<p><strong>Unidade:</strong> ', ', ', '</p>') . '</span>';
	echo '' . get_the_term_list( $post->ID, 'codigo-tuss', '<p><strong>Código TUSS:</strong> ', ', ', '</p>') . '</span></div>';
?>

<?php if ( $price_html = $product->get_price_html() ) : ?>
	<span class="price"><strong>Preço:</strong><?php echo $price_html; ?></span>
<?php endif; ?>
