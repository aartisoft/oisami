<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/wrapper-start.php.
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
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = wc_get_theme_slug_for_templates();

switch ( $template ) {
	case 'twentyten' :
		echo '<div id="container"><div id="content" role="main">';
		break;
	case 'twentyeleven' :
		echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
		break;
	case 'twentytwelve' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
		break;
	case 'twentythirteen' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
		break;
	case 'twentyfourteen' :
		echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwc">';
		break;
	case 'twentyfifteen' :
		echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
		break;
	case 'twentysixteen' :
		echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
		break;
	case 'consulta-do-bem' :
		echo '<div class="consulta-rede"><h1 style="margin: 0px auto; margin-bottom: 15px; text-align: center; color: #fff; text-transform: uppercase; width: 70%; line-height: 54px;">Buscando por:</h1><div class="img-network"><div id="bt-exame" class="box-network active"><div class="img-card-network"><img src="/wp-content/uploads/2018/06/exames.png"/></div>Exames</div><div id="bt-vacina" class="box-network"><div class="img-card-network"><img src="/wp-content/uploads/2018/06/vacina.png"/></div>Vacinas</div><div id="bt-cirurgia" class="box-network"><div class="img-card-network"><img src="/wp-content/uploads/2018/06/cirurgia.png"/></div>Cirurgias</div><div id="bt-pronto-socorro" class="box-network"><div class="img-card-network"><img src="/wp-content/uploads/2018/06/pronto-socorro.png"/></div>Pronto Socorro</div></div></div><div class="container"><div id="tg-twocolumns" class="tg-twocolumns"><div class="row">';
		break;
	default :
		echo '<div id="primary" class="content-area"><main id="main" class="site-main" role="main">';
		break;
}
