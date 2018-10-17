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

  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="https://raw.githubusercontent.com/digitalBush/jquery.maskedinput/master/dist/jquery.maskedinput.min.js"></script>

  <script type="text/javascript">
    // <![CDATA[
      jQuery(function($) {
        $.mask.definitions['~']='[+-]';
        //Inicio Mascara Telefone
        $('input[type=tel]').focusout(function(){
          var phone, element;
          element = $(this);
          element.unmask();
          phone = element.val().replace(/\D/g, '');
          if(phone.length > 10) {
            element.mask("(99) 99999-999?9");
          } else {
            element.mask("(99) 9999-9999?9");
          }
        }).trigger('focusout');
        //Fim Mascara Telefone
        $("#cpf").mask("999.999.999-99");
        $("#rg").mask("99.999.999-*");
      });
    // ]]>
  </script>
  
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