<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>
		<p class="price"><?php echo $product->get_price_html(); ?></p>
	</div>

	<div class="product_meta_new">
		<table class="table-product">
			<tbody>
				<tr>
					<td width="30%" style="text-align: left;">
						<?php do_action( 'woocommerce_product_meta_start' ); ?>
						<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
						<?php do_action( 'woocommerce_product_meta_end' ); ?>
					</td>

					<td width="70%" class="transparent">
						<?php echo get_the_term_list( $post->ID, 'laboratorio', '<p><strong>Laboratório:</strong> ', ', ', '</p>');?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<table class="table-product">
		<thead>
			<tr>
				<th width="25%" style="text-align: left;">Especialidade (NORMATIZADO)</th>
				<th width="25%">Especialidade (LAB / BÁSICA)</th>
				<th width="20%">Código Laboratório</th>
				<th width="20%">Código Unidade</th>
				<th width="10%">Código TUSS</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td style="text-align: left;"><?php echo get_the_term_list( $post->ID, 'especialidade-normatizado', '<p>', ', ', '</p>');?></td>

				<td>
					<?php echo get_the_term_list( $post->ID, 'especialidade-normatizado', '<p>', ', ', '</p>');?>
					<!--?php echo get_the_term_list( $post->ID, 'especialidade-basica', '<p>', ', ', '</p>');?-->
				</td>

				<td><?php echo get_the_term_list( $post->ID, 'codigo-laboratorio', '<p>', ', ', '</p>');?></td>
				<td><?php echo get_the_term_list( $post->ID, 'codigo-unidade', '<p>', ', ', '</p>');?></td>
				<td><?php echo get_the_term_list( $post->ID, 'codigo-tuss', '<p>', ', ', '</p>');?></td>
			</tr>
		</tbody>
	</table>
</div>
