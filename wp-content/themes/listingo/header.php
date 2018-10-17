<!doctype html>
<!--[if (gt IE 9)|!(IE)]><html lang="en"><![endif]-->
<html <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
       	<?php do_action('blink_systemloader');?>
        <?php do_action('listingo_systemloader'); ?>
        <?php do_action('listingo_app_available'); ?>
        <div id="tg-wrapper" class="tg-wrapper tg-haslayout">
            <?php do_action('listingo_do_process_headers'); ?>
            <?php do_action('listingo_prepare_titlebars'); ?>
            <main id="tg-main" class="tg-main tg-haslayout">