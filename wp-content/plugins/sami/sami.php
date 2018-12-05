<?php
/*
  Plugin Name: Configuracao Sami
  Plugin URI: http://www.vm50.com.br
  Description: Plugin para configurar o tema para Sami
  Version: 1.1a
  Author: Valnei Lorenzetti
 */


/*==============================================
    Para customizar
===============================================*/
define( 'VM50_SAMI_PAGINA_LOGIN',         'login' );          // Pagina de Login
define( 'VM50_SAMI_PAGINA_TROCA_SENHA',   'troca-senha' );    // Pagina que obriga a trocar a senha
define( 'VM50_SAMI_PAGINA_REVISAR_DADOS', 'revisar-dados' );  // Pagina que obriga a revisar dados
define( 'VM50_SAMI_PAGINA_ACEITE',        'aceite' );         // Pagina do Aceite
define( 'VM50_SAMI_PAGINA_PESQUISA',      'pesquisa' );       // Pagina de pesquisa
define( 'VM50_SAMI_PAGINA_SOBRESAMI',     'sobre-sami' );     // Pagina do Sobre a Sami
//define( 'VM50_SAMI_PAGINA_AGENDA_MEDICO', 'medico-familia' ); // Pagina de Agenda Medico de Familia



/*==============================================
    Configuracao inicial
===============================================*/
define( 'VM50_SAMI_VERSION',                  '1.1a' );
define( 'VM50_SAMI_PLUGIN',                   __FILE__ );
define( 'VM50_SAMI_PLUGIN_BASENAME',          plugin_basename( VM50_SAMI_PLUGIN ) );
define( 'VM50_SAMI_PLUGIN_DIR',               untrailingslashit( dirname( VM50_SAMI_PLUGIN ) ) );
define( 'VM50_SAMI_PLUGIN_URL',               untrailingslashit( plugins_url( '', VM50_SAMI_PLUGIN ) ) );
define( 'VM50_SAMI_META_MEDICO_FAMILIA',      'vm50_meu_medico_familia' );
define( 'VM50_SAMI_META_ENFERMEIRO',          'vm50_medico_familia_enfermeiro' );
define( 'VM50_SAMI_META_CLIENTE',             'vm50_cliente' );
define( 'VM50_SAMI_META_CLIENTES_ATENDIDOS',  'vm50_clientes_atendidos' );
define( 'VM50_SAMI_META_RECOMENDACAO_MEDICA', 'vm50_recomendacao_medica' );
define( 'VM50_SAMI_META_RECOMENDACAO_TEMPO',  'vm50_recomendacao_tempo' );
$clientes = array( 'Cliente 1', 'Cliente 2', 'Cliente 3', 'Cliente 4', 'Cliente 5');





/*==============================================
    Carga de css e js
===============================================*/
add_action( 'wp_enqueue_scripts', 'vm50_sami_enqueue' );
function vm50_sami_enqueue() {
    $ver = '1.2b';
    wp_register_script('vm50_sami_javascript', plugins_url('js/vm50_sami.js', __FILE__), 'jquery', $ver, true);
    $translation_array = array('samiAjaxUrl' => admin_url('admin-ajax.php'));
    wp_localize_script('vm50_sami_javascript', 'referenciaSami', $translation_array);
    wp_enqueue_script('vm50_sami_javascript');
    wp_enqueue_script( 'vm50_sami_format', plugins_url('js/jquery.mask.min.js', __FILE__), 'jquery', $ver, true );
}




/*==============================================
    Gera os perfis necessarios
===============================================*/
//remove_role( VM50_SAMI_ROLE_MEDICO_FAMILIA );




/*==============================================
    Le medico de Familia
===============================================*/
function vm50_le_medico_familia() {
    $usuario = wp_get_current_user();
    $medico  = get_user_meta( $usuario->ID, VM50_SAMI_META_MEDICO_FAMILIA, true );
    if ( $medico == '' ) {
        $medico = false;
    }
    return $medico;
}




/*==============================================
    Amarra medico de Familia com paciente
===============================================*/
function vm50_grava_medico_familia( $usuario_id=0 ) {
    $saida   = false;
    $medico  = wp_get_current_user();
    $usuario = get_userdata( $usuario_id );
    if ( $usuario ) {
        delete_user_meta( $usuario->ID, VM50_SAMI_META_MEDICO_FAMILIA );
        $saida = ( !add_user_meta( $usuario->ID, VM50_SAMI_META_MEDICO_FAMILIA, $medico->ID, true ) ) ? false : true;
    }
    return $saida;
}




/*==============================================
    Inclui campo medico de Familia na edicao
===============================================*/
add_action( 'show_user_profile', 'vm50_user_profile_medico_familia' );
add_action( 'edit_user_profile', 'vm50_user_profile_medico_familia' );
function vm50_user_profile_medico_familia( $usuario ) {
    global $clientes;
    $pac_cliente    = get_user_meta( $usuario->ID, VM50_SAMI_META_CLIENTE, true );
    $pac_medico     = get_user_meta( $usuario->ID, VM50_SAMI_META_MEDICO_FAMILIA, true );
    $med_enfermeiro = get_user_meta( $usuario->ID, VM50_SAMI_META_ENFERMEIRO, true );
    $cli_atendido   = get_user_meta( $usuario->ID, VM50_SAMI_META_CLIENTES_ATENDIDOS, true );
    $saida = '';
    $saida .= '<h3>SAMI - PACIENTE</h3>';
    $saida .= '<table class="form-table">';
    $saida .= '<tr>';
    $saida .= '<th><label for="paciente_cliente">Cliente</label></th>';
    $saida .= '<td>';
    $saida .= '<input type="text" name="paciente_cliente" id="paciente_cliente" value="' . $pac_cliente . '" class="regular-text" /><br />';
    $saida .= '<span class="description">ID do Cliente (Número)</span>';
    $saida .= '</td>';
    $saida .= '</tr>';
    $saida .= '<tr>';
    $saida .= '<th><label for="medico_de_familia">Médico de Família do Paciente</label></th>';
    $saida .= '<td>';
    $saida .= '<input type="text" name="medico_de_familia" id="medico_de_familia" value="' . $pac_medico . '" class="regular-text" /><br />';
    $saida .= '<span class="description">ID do Médico de Família (Número)</span>';
    $saida .= '</td>';
    $saida .= '</tr>';
    $saida .= '</table>';

    $saida .= '<h3>SAMI - MÉDICO DE FAMÍLIA</h3>';
    $saida .= '<table class="form-table">';
    $saida .= '<tr>';
    $saida .= '<th><label for="medico_de_familia_enfermeiro">Enfermeiro ligado a este Médico de Família</label></th>';
    $saida .= '<td>';
    $saida .= '<input type="text" name="medico_de_familia_enfermeiro" id="medico_de_familia_enfermeiro" value="' . $med_enfermeiro . '" class="regular-text" /><br />';
    $saida .= '<span class="description">ID do Enfermeiro (Número)</span>';
    $saida .= '</td>';
    $saida .= '</tr>';
    $saida .= '<tr>';
    $saida .= '<th><label for="medico_de_familia_clientes">Clientes atendidos por este Médico</label></th>';
    $saida .= '<td>';
    for ($cl=0; $cl<count($clientes); $cl++) {
        $saida .= '<input type="checkbox" name="medico_de_familia_clientes_atendidos[]" id="medico_de_familia_clientes_atendidos_'.$cl.'" value="'.$cl.'" ';
        if ( ( is_array($cli_atendido) ) && ( in_array($cl, $cli_atendido) ) ) {
            $saida .= ' checked';
        }
        $saida .= '/>'.$clientes[$cl].'<br />';
    }
    $saida .= '</td>';
    $saida .= '</tr>';
    $saida .= '</table>';
    echo $saida;
}

add_action( 'personal_options_update', 'vm50_save_user_profile_medico_familia' );
add_action( 'edit_user_profile_update', 'vm50_save_user_profile_medico_familia' );
function vm50_save_user_profile_medico_familia( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    update_user_meta( $user_id, VM50_SAMI_META_MEDICO_FAMILIA,     $_POST['medico_de_familia'] );
    update_user_meta( $user_id, VM50_SAMI_META_CLIENTE,            $_POST['paciente_cliente'] );
    update_user_meta( $user_id, VM50_SAMI_META_ENFERMEIRO,         $_POST['medico_de_familia_enfermeiro'] );
    update_user_meta( $user_id, VM50_SAMI_META_CLIENTES_ATENDIDOS, $_POST['medico_de_familia_clientes_atendidos'] );
}






/*==============================================
    Tela admin clientes
===============================================*/
add_shortcode( 'admin-clientes', 'vm50_pagina_admin_cliente' );
function vm50_pagina_admin_cliente( ) {
    global $clientes;
    $saida  = '';
    $saida .= '<p>Escolha um cliente para administrar:</p>';
    $saida .= '<select name="vm50_admin_escolhe_cliente" id="vm50_admin_escolhe_cliente" onchange="vm50_admin_escolhe_cliente();" >';
    $saida .= '<option value=""></option>';
    for ($cl=0; $cl<count($clientes); $cl++) {
        $saida .= '<option value="'.$cl.'">'.$clientes[$cl].'</option>';
    }
    $saida .= '</select>';
    $saida .= '<div id="vm50_admin_cliente"></div>';
    return $saida;
}






/*==============================================
    Lista todos os medicos de familia
===============================================*/
//add_shortcode( 'lista-medicos', 'vm50_lista_medicos_de_familia' );
function vm50_lista_medicos_de_familia( $attr ) {
    $cliente       = ( isset($attr['cliente']) ) ? intval($attr['cliente']) : '';
    $sorting_order = 'ID';
    $order         = 'DESC';
    $query_args    = array(
        'order'    => $order,
        'orderby'  => $sorting_order,
    );
    $meta_query_args   = array();
    $meta_query_args[] = array(
        'key'     => 'sub_category',
        'value'   => 'medico-de-familia',
        'compare' => 'LIKE'
    );
    if (!empty($meta_query_args)) {
        $query_relation  = array('relation' => 'AND',);
        $meta_query_args = array_merge($query_relation, $meta_query_args);
        $query_args['meta_query'] = $meta_query_args;
    }
//    $query_args	= apply_filters('listingo_apply_extra_search_filters',$query_args);
    $pesquisa = new WP_User_Query($query_args);
    $medicos  = $pesquisa->get_results();
    $saida    = '';
    $saida   .= '<h3>Lista dos médicos</h3>';
    foreach ( $pesquisa->get_results() as $medicof ) {
//        print_r( $medicof );
//        print_r(get_user_meta ( $medicof->ID ));
        $clientes = get_user_meta( $medicof->ID, VM50_SAMI_META_CLIENTES_ATENDIDOS, true );
        $saida   .= '<p>';
        $saida   .= '<input type="checkbox" name="vm50_escolhe_medicos_do_cliente[]" id="vm50_escolhe_medicos_do_cliente_'.$medicof->ID.'" value="'.$medicof->ID.'"';
        if ( in_array($cliente,$clientes) ) {
            $saida .= ' checked';
        }
        $saida   .= '/>';
        $saida   .= '<a href="'.site_url('/consultas/').$medicof->user_nicename.'" target="_blank">';
        $saida   .= $medicof->display_name;
        $saida   .= '</a>';
        $saida   .= '</p>';
    }
    $saida   .= '<input type="button" id="vm50_admin_escolhe_medicos_do_cliente_bt" value="Salvar" onclick="vm50_admin_salvar_medicos_do_cliente(\''.$cliente.'\');" />';
    return $saida;
}






/*==============================================
    Lista medicos do cliente
===============================================*/
add_action('wp_ajax_vm50_escolhemedicosdocliente', 'vm50_escolhe_medicos_do_cliente');
add_action('wp_ajax_nopriv_vm50_escolhemedicosdocliente', 'vm50_escolhe_medicos_do_cliente');
function vm50_escolhe_medicos_do_cliente() {
    if ( isset($_POST['cliente']) ) {
        $saida = vm50_lista_medicos_de_familia( array( 'cliente' => $_POST['cliente'] ) );
    } else {
        $saida = 'Erro: Escolha um paciente antes!';
    }
    echo $saida;
    die();
    return;
}






/*==============================================
    Salva medicos do cliente
===============================================*/
add_action('wp_ajax_vm50_salvarmedicosdocliente', 'vm50_salvar_medicos_do_cliente');
add_action('wp_ajax_nopriv_vm50_salvarmedicosdocliente', 'vm50_salvar_medicos_do_cliente');
function vm50_salvar_medicos_do_cliente() {
    if ( (isset($_POST['medicos'])) && (isset($_POST['cliente'])) && (isset($_POST['naovai'])) ) {
        $medicos = $_POST['medicos'];
        $cliente = $_POST['cliente'];
        $naovai  = $_POST['naovai'];
        for ($md=0; $md<count($medicos); $md++) {
            $clientes = get_user_meta( $medicos[$md], VM50_SAMI_META_CLIENTES_ATENDIDOS, true );
            if ( !in_array($cliente, $clientes) ) {
                $clientes[] = $cliente;
                update_user_meta( $medicos[$md], VM50_SAMI_META_CLIENTES_ATENDIDOS, $clientes );
            }
        }
        for ($nv=0; $nv<count($naovai); $nv++) {
            $clientes = get_user_meta( $naovai[$nv], VM50_SAMI_META_CLIENTES_ATENDIDOS, true );
            if ( in_array($cliente, $clientes) ) {
                $pos = array_search($cliente, $clientes);
                array_splice($clientes, $pos, 1);
                update_user_meta( $naovai[$nv], VM50_SAMI_META_CLIENTES_ATENDIDOS, $clientes );
            }
        }
        $saida = 'Atualizado!';
    } else {
        $saida = 'Problemas ao salvar os dados!';
    }
    echo $saida;
    die();
    return;
}






/*==============================================
    Lista paciente
===============================================*/
add_action('wp_ajax_vm50_pesquisameupaciente', 'vm50_lista_usuarios');
add_action('wp_ajax_nopriv_vm50_pesquisameupaciente', 'vm50_lista_usuarios');
add_shortcode( 'lista-usuarios', 'vm50_lista_usuarios' );
function vm50_lista_usuarios( $attr ) {
    $medico       = wp_get_current_user();
    $cli_atendido = get_user_meta( $medico->ID, VM50_SAMI_META_CLIENTES_ATENDIDOS, true );
    if ( isset($_POST['texto']) ) {
        $attr['texto'] = $_POST['texto'];
    }
    $porpag = (isset($attr['por_pagina'])) ? ($attr['por_pagina']*1) : -1;
    $pagina = (isset($attr['pagina']))     ? ($attr['pagina']*1)     : 1;
    $texto  = (isset($attr['texto']))      ? ($attr['texto'])        : '';
    if ($pagina == 0) {
        $pagina = 1;
    }
    $args['role']    = 'customer';
    $args['orderby'] = 'display_name';
    $args['order']   = 'DESC';
    if ( $texto != '' ) {
        $args['search'] = '*' . esc_attr( $texto ) . '*';
        $pesquisa = new WP_User_Query( $args );
        $saida    = '';
        foreach ( $pesquisa->get_results() as $usuario ) {
            $usuario_info = get_userdata( $usuario->ID );
            $medico_id    = get_user_meta( $usuario->ID, VM50_SAMI_META_MEDICO_FAMILIA, true );
            $cliente_id   = get_user_meta( $usuario->ID, VM50_SAMI_META_CLIENTE, true );
            if ( ( is_array($cli_atendido) ) && ( in_array($cliente_id, $cli_atendido) ) ) {
                $saida .= '<input type="radio" name="vm50_meu_paciente" id="vm50_meu_paciente_'.$usuario->ID.'" value="'.$usuario->ID.'"';
                if ( $medico_id > 0 ) {
                    $saida .= ' disabled ';
                }
                $saida .= '/> <label for="vm50_meu_paciente_'.$usuario->ID.'">'.$usuario_info->first_name.' '.$usuario_info->last_name;
                if ( $medico_id > 0 ) {
                    $saida .= ' (já tem médico)';
                }
                $saida .= '</label><br />';
            }
        }
        $saida .= '<br /><input type="button" name="vm50_meu_paciente_escolher" id="vm50_meu_paciente_escolher" value="Escolher" onclick="vm50_meu_paciente_escolher()" />';
        echo $saida;
        die();
        return;
    } else {
        $saida  = '<div class="vm50_meu_paciente_pesquisa">';
        $saida .= 'Pesquisa por paciente: <input type="text" name="vm50_meu_paciente_pesquisar" id="vm50_meu_paciente_pesquisar" />';
        $saida .= '<input type="button" value="Pesquisar" id="vm50_meu_paciente_pesquisar_bt" onclick="vm50_meu_paciente_pesquisar()" />';
        $saida .= '<div class="vm50_meu_paciente_pesquisa_resultado"></div>';
        $saida .= '</div>'; //.vm50_meu_paciente_pesquisa
    }
    return $saida;
}






/*==============================================
    Escolhe paciente para marcar como medico de familia
===============================================*/
add_action('wp_ajax_vm50_escolhemeupaciente', 'vm50_escolhe_usuarios');
add_action('wp_ajax_nopriv_vm50_escolhemeupaciente', 'vm50_escolhe_usuarios');
function vm50_escolhe_usuarios( $attr ) {
    if ( isset($_POST['paciente']) ) {
        if ( vm50_grava_medico_familia( $_POST['paciente'] ) ) {
            $saida = 'Paciente escolhido com sucesso!';
        } else {
            $saida = 'Erro ao escolher o paciente!';
        }
    } else {
        $saida = 'Erro: Escolha um paciente antes!';
    }
    echo $saida;
    die();
    return;
}





/*==============================================
    Checa login
===============================================*/
add_action( 'init', 'vm50_checa_login' );
function vm50_checa_login() {
    $usuario = wp_get_current_user();
    $caminho = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
//    if (
//        in_array('business',     (array) $usuario->roles) ||
//        in_array('professional', (array) $usuario->roles) ||
//        in_array('customer',     (array) $usuario->roles)
//    ) {
    if ( in_array('customer', (array) $usuario->roles) ) {
        $usuario_id = $usuario->ID;
        $prim_login = get_user_meta( $usuario_id, 'vm50_primeiro_login', true );
        $revisado   = get_user_meta( $usuario_id, 'vm50_revisado', true );
        $aceite     = get_user_meta( $usuario_id, 'vm50_aceite', true );
        $pesquisa   = get_user_meta( $usuario_id, 'vm50_pesquisa', true );
        $sobre_sami = get_user_meta( $usuario_id, 'vm50_sobre_sami', true );
        if ( ( strpos( $caminho, VM50_SAMI_PAGINA_TROCA_SENHA ) === false ) && ( strpos( $caminho, VM50_SAMI_PAGINA_REVISAR_DADOS ) === false ) && ( strpos( $caminho, VM50_SAMI_PAGINA_ACEITE ) === false ) && ( strpos( $caminho, VM50_SAMI_PAGINA_PESQUISA ) === false ) && ( strpos( $caminho, VM50_SAMI_PAGINA_SOBRESAMI ) === false ) && ( strpos( $caminho, 'wp-admin' ) === false )  && ( strpos( $caminho, 'logout' ) === false ) ) {
            if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                if ( $prim_login != '1' ) {
                    echo '<script>window.location = "'.site_url('/'.VM50_SAMI_PAGINA_TROCA_SENHA.'/').'";</script>';
                    exit;
                } elseif ( $revisado != '1' ) {
                    echo '<script>window.location = "'.site_url('/'.VM50_SAMI_PAGINA_REVISAR_DADOS.'/').'";</script>';
                    exit;
                } elseif ( $aceite != '1' ) {
                    echo '<script>window.location = "'.site_url('/'.VM50_SAMI_PAGINA_ACEITE.'/').'";</script>';
                    exit;
                } elseif ( $pesquisa != '1' ) {
                    echo '<script>window.location = "'.site_url('/'.VM50_SAMI_PAGINA_PESQUISA.'/').'";</script>';
                    exit;
                } elseif ( $sobre_sami != '1' ) {
                    echo '<script>window.location = "'.site_url('/'.VM50_SAMI_PAGINA_SOBRESAMI.'/').'";</script>';
                    exit;
                }
            }
        }
    }
    return;
}




/*==============================================
    Formulario de troca de senha
===============================================*/
add_shortcode( 'troca-senha', 'vm50_troca_senha' );
function vm50_troca_senha() {
    $saida = '';
    $saida .= '<div id="password-reset-form" class="widecolumn">';
    $saida .= '<div class="tg-bordertitle">';
    $saida .= '<h3>Nova Senha</h3>';
    $saida .= '</div>';
    $saida .= '<form name="resetpassform" id="resetpassform" action="#" method="post" autocomplete="off">';
    $saida .= '<p>';
    $saida .= '<label for="pass1">Nova Senha</label>';
    $saida .= '<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />';
    $saida .= '</p>';
    $saida .= '<p>';
    $saida .= '<label for="pass2">Repetir a nova senha</label>';
    $saida .= '<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />';
    $saida .= '</p>';
    $saida .= '<p class="description">' . wp_get_password_hint() . '</p>';
    $saida .= '<p class="resetpass-submit">';
    $saida .= '<input type="button" name="vm50_trocar_senha" id="vm50_trocar_senha" onclick="vm50_trocarsenha();" value="Trocar a senha" />';
    $saida .= '</p>';
    $saida .= '<p id="vm50_msg_troca_senha"></p>';
    $saida .= '</form>';
    $saida .= '</div>';
    return $saida;
}




/*==============================================
    Ajax de troca de senha
===============================================*/
add_action('wp_ajax_vm50_trocasenha', 'vm50_trocar_a_senha');
add_action('wp_ajax_nopriv_vm50_trocasenha', 'vm50_trocar_a_senha');
function vm50_trocar_a_senha() {
    $saida = 'Erro não identificado';
    if ( isset($_POST['senha']) ) {
        $senha   = $_POST['senha'];
        $usuario = wp_get_current_user();
        update_user_meta( $usuario->ID, 'vm50_primeiro_login', '1' );
        wp_set_password( $senha, $usuario->ID );
        $saida = '<span>Senha trocada com sucesso. Clique <a href="'.site_url('/'.VM50_SAMI_PAGINA_LOGIN.'/').'">aqui</a> para fazer seu login novamente.</span>';
    }
    echo $saida;
    die();
    return;
}




/*==============================================
    Lista estados
===============================================*/
function vm50_lista_estados( $estado ) {
    $estados = array("AC", "AL", "AP", "AM", "BA", "CE", "DF", "ES", "GO", "MA", "MS", "MT", "MG", "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RS", "RO", "RR", "SC", "SP", "SE", "TO");
    $saida = '';
    for ($es=0; $es<count($estados); $es++) {
        $saida .= '<option value="'.$estados[$es].'"';
        if ( $estado == $estados[$es] ) $saida .= ' selected';
        $saida .= '>'.$estados[$es].'</option>';
    }
    return $saida;
}




/*==============================================
    Lista genero
===============================================*/
function vm50_lista_genero( $genero ) {
    $generos = array("M", "F");
    $saida = '';
    for ($es=0; $es<count($generos); $es++) {
        $saida .= '<option value="'.$generos[$es].'"';
        if ( $genero == $generos[$es] ) $saida .= ' selected';
        $saida .= '>'.$generos[$es].'</option>';
    }
    return $saida;
}




/*==============================================
    Formulario de confirmar dados
===============================================*/
add_shortcode( 'confirma-dados', 'vm50_confirma_dados' );
function vm50_confirma_dados() {
    $userid      = get_current_user_id();
    $dados_usr   = get_userdata( $userid );
    $genero      = get_user_meta( $userid, 'vm50_genero', true);
    $cpf         = get_user_meta( $userid, 'billing_cpf', true);
    $telefone    = get_user_meta( $userid, 'billing_phone', true);
    $endereco    = get_user_meta( $userid, 'billing_address_1', true);
    $numero      = get_user_meta( $userid, 'billing_number', true);
    $complemento = get_user_meta( $userid, 'billing_address_2', true);
    $bairro      = get_user_meta( $userid, 'billing_neighborhood', true);
    $cidade      = get_user_meta( $userid, 'billing_city', true);
    $cep         = get_user_meta( $userid, 'billing_postcode', true);
    $estado      = get_user_meta( $userid, 'billing_state', true);
    $saida = '';
    $saida .= '<div id="vm50-confirma-dados" class="widecolumn">';
    $saida .= '<div class="tg-bordertitle">';
    $saida .= '<h3>Meus Dados</h3>';
    $saida .= '</div>';
    $saida .= '<fieldset>';

    $saida .= '<form name="confirmadados" id="confirmadados" action="#" method="post" autocomplete="off">';
    $saida .= '<input type="hidden" name="vm50_userid" id="vm50_userid" value="'.$userid.'" />';

    $saida .= '<p id="alert">Seu Nome, E-mail ou CPF estão errados? Envie um E-mail para <a href="mailto:cadastro@oisami.com">cadastro@oisami.com</a> para corrigirmos</p>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Nome <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_nome" id="vm50_nome" placeholder="Nome" value="'.$dados_usr->first_name.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Sobrenome <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_sobrenome" id="vm50_sobrenome" placeholder="Sobrenome" value="'.$dados_usr->last_name.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Gênero <span>*</span></label>';
    $saida .= '<select name="vm50_genero" id="vm50_genero">';
    $saida .= vm50_lista_genero( $genero );
    $saida .= '</select>';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Email <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_email" id="vm50_email" placeholder="Email" value="'.$dados_usr->user_email.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">CPF <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_cpf" id="vm50_cpf" placeholder="CPF" value="'.$cpf.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Telefone <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_telefone" id="vm50_telefone" placeholder="Telefone" value="'.$telefone.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">CEP <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_cep" id="vm50_cep" placeholder="CEP" value="'.$cep.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Endereço <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_endereco" id="vm50_endereco" placeholder="Endereço" value="'.$endereco.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Número <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_numero" id="vm50_numero" placeholder="Número" value="'.$numero.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Complemento <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_complemento" id="vm50_complemento" placeholder="Complemento" value="'.$complemento.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Bairro <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_bairro" id="vm50_bairro" placeholder="Bairro" value="'.$bairro.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Cidade <span>*</span></label>';
    $saida .= '<input type="text" name="vm50_cidade" id="vm50_cidade" placeholder="Cidade" value="'.$cidade.'" />';
    $saida .= '</div>';

    $saida .= '<div class="form-group">';
    $saida .= '<label class="vm50_confirma_dados_titulo">Estado <span>*</span></label>';
    $saida .= '<select name="vm50_estado" id="vm50_estado" />';
    $saida .= '<option value="">Selecione</option>';
    $saida .= vm50_lista_estados( $estado );
    $saida .= '</select>';
    $saida .= '</div>';

    $saida .= '<p class="vm50-confirma-dados-submit">';
    $saida .= '<input type="button" name="vm50_confirmar_dados" id="vm50_confirmar_dados" onclick="vm50_confirmardados();" value="Confirmar dados" />';
    $saida .= '</p>';

    $saida .= '<p id="vm50_msg_confirma_dados"></p>';

    $saida .= '</form>';
    $saida .= '</fieldset>';
    $saida .= '</div>';
    return $saida;
}




/*==============================================
    Ajax de confirmar dados
===============================================*/
add_action('wp_ajax_vm50_confirmadados', 'vm50_confirmar_dados');
add_action('wp_ajax_nopriv_vm50_confirmadados', 'vm50_confirmar_dados');
function vm50_confirmar_dados() {
    $saida       = 'Erro não identificado';
    $user_id     = ( isset($_POST['userid']) )      ? $_POST['userid']      : '';
    $nome        = ( isset($_POST['nome']) )        ? $_POST['nome']        : '';
    $sobrenome   = ( isset($_POST['sobrenome']) )   ? $_POST['sobrenome']   : '';
    $email       = ( isset($_POST['email']) )       ? $_POST['email']       : '';
    $telefone    = ( isset($_POST['telefone']) )    ? $_POST['telefone']    : '';
    $cpf         = ( isset($_POST['cpf']) )         ? $_POST['cpf']         : '';
    $endereco    = ( isset($_POST['endereco']) )    ? $_POST['endereco']    : '';
    $numero      = ( isset($_POST['numero']) )      ? $_POST['numero']      : '';
    $complemento = ( isset($_POST['complemento']) ) ? $_POST['complemento'] : '';
    $bairro      = ( isset($_POST['bairro']) )      ? $_POST['bairro']      : '';
    $cidade      = ( isset($_POST['cidade']) )      ? $_POST['cidade']      : '';
    $cep         = ( isset($_POST['cep']) )         ? $_POST['cep']         : '';
    $estado      = ( isset($_POST['estado']) )      ? $_POST['estado']      : '';
    $genero      = ( isset($_POST['genero']) )      ? $_POST['genero']      : '';
    $usr_data    = array();
    if ( $user_id != '' ) {
        $usr_data['ID'] = $user_id;
        if ( $nome != '' ) {
            $usr_data['first_name'] = $nome;
            update_user_meta( $user_id, 'billing_first_name', $nome );
            update_user_meta( $user_id, 'shipping_first_name', $nome );
        }
        if ( $sobrenome != '' ) {
            $usr_data['last_name'] = $sobrenome;
            update_user_meta( $user_id, 'billing_last_name', $sobrenome );
            update_user_meta( $user_id, 'shipping_last_name', $sobrenome );
        }
        if ( $email != '' ) {
            $usr_data['user_email'] = $email;
            update_user_meta( $user_id, 'billing_email', $email );
        }
        if ( $telefone != '' ) {
            update_user_meta( $user_id, 'billing_phone', $telefone );
            update_user_meta( $user_id, 'billing_cellphone', $telefone );
            update_user_meta( $user_id, 'phone', $telefone );
        }
        if ( $cpf != '' ) {
            update_user_meta( $user_id, 'billing_cpf', $cpf );
        }
        if ( $endereco != '' ) {
            update_user_meta( $user_id, 'billing_address_1', $endereco );
            update_user_meta( $user_id, 'shipping_address_1', $endereco );
        }
        if ( $numero != '' ) {
            update_user_meta( $user_id, 'billing_number', $numero );
            update_user_meta( $user_id, 'shipping_number', $numero );
        }
        if ( $complemento != '' ) {
            update_user_meta( $user_id, 'billing_address_2', $complemento );
            update_user_meta( $user_id, 'shipping_address_2', $complemento );
        }
        if ( $bairro != '' ) {
            update_user_meta( $user_id, 'billing_neighborhood', $bairro );
            update_user_meta( $user_id, 'shipping_neighborhood', $bairro );
        }
        if ( $cidade != '' ) {
            update_user_meta( $user_id, 'billing_city', $cidade );
            update_user_meta( $user_id, 'shipping_city', $cidade );
        }
        if ( $cep != '' ) {
            update_user_meta( $user_id, 'zip', $cep );
            update_user_meta( $user_id, 'billing_postcode', $cep );
            update_user_meta( $user_id, 'shipping_postcode', $cep );
        }
        if ( $estado != '' ) {
            update_user_meta( $user_id, 'billing_state', $estado );
            update_user_meta( $user_id, 'shipping_state', $estado );
        }
        if ( $genero != '' ) {
            update_user_meta( $user_id, 'vm50_genero', $genero );
        }
        $user_id = wp_update_user( $usr_data );
        update_user_meta( $user_id, 'vm50_revisado', '1' );
        $saida = '<a href="'.site_url('/'.VM50_SAMI_PAGINA_ACEITE.'/').'"><input type="button" value="Continuar" /></a>';
        $saida = 'Clique <a href="'.site_url('/'.VM50_SAMI_PAGINA_ACEITE.'/').'">aqui</a> para continuar.';
        $saida = site_url('/'.VM50_SAMI_PAGINA_ACEITE.'/');
    }
    echo $saida;
    die();
    return;
}




/*==============================================
    Botao aceite
===============================================*/
add_shortcode( 'botao-aceite', 'vm50_aceite' );
function vm50_aceite() {
    $saida = '';
    $saida .= '<span class="vm50_aceitar"><input type="button" name="vm50_aceitar_bt" id="vm50_aceitar_bt" onclick="vm50_aceitar();" value="Aceito" /></span>';
    return $saida;
}




/*==============================================
    Ajax de Aceitar
===============================================*/
add_action('wp_ajax_vm50_aceito', 'vm50_aceitar');
add_action('wp_ajax_nopriv_vm50_aceito', 'vm50_aceitar');
function vm50_aceitar() {
    $usuario = wp_get_current_user();
    update_user_meta( $usuario->ID, 'vm50_aceite', '1' );
    $saida = '<a href="'.site_url('/'.VM50_SAMI_PAGINA_PESQUISA.'/').'"><input type="button" value="Continuar" /></a> ';
    $saida = 'Clique <a href="'.site_url('/'.VM50_SAMI_PAGINA_PESQUISA.'/').'">aqui</a> para continuar.';
    $saida = site_url('/'.VM50_SAMI_PAGINA_PESQUISA.'/');
    echo $saida;
    die();
    return;
}




/*==============================================
    Botao Pesquisa
===============================================*/
add_shortcode( 'botao-pesquisa', 'vm50_pesquisa' );
function vm50_pesquisa() {
    $saida = '';
    $saida .= '<span class="vm50_pesquisa"><input type="button" name="vm50_pesquisa_bt" id="vm50_pesquisa_bt" onclick="vm50_pesquisa();" value="Enviar Pesquisa" /></span>';
    return $saida;
}




/*==============================================
    Ajax de Entendi
===============================================*/
add_action('wp_ajax_vm50_pesquisa', 'vm50_pesquisar');
add_action('wp_ajax_nopriv_vm50_pesquisa', 'vm50_pesquisar');
function vm50_pesquisar() {
    $usuario = wp_get_current_user();
    update_user_meta( $usuario->ID, 'vm50_pesquisa', '1' );
    $saida = '<a href="'.site_url('/'.VM50_SAMI_PAGINA_SOBRESAMI.'/').'"><input type="button" value="Continuar" /></a>';
    $saida = 'Clique <a href="'.site_url('/'.VM50_SAMI_PAGINA_SOBRESAMI.'/').'">aqui</a> para continuar.';
    $saida = site_url('/'.VM50_SAMI_PAGINA_SOBRESAMI.'/');
    echo $saida;
    die();
    return;
}




/*==============================================
    Captura depois do formulario de pesquisa
===============================================*/
add_shortcode( 'pesquisa-respondida', 'vm50_pesquisa_redireciona' );
function vm50_pesquisa_redireciona() {
    $usuario = wp_get_current_user();
    update_user_meta( $usuario->ID, 'vm50_pesquisa', '1' );
    echo '<script>'."\n";
    echo 'window.location.href="'.site_url('/'.VM50_SAMI_PAGINA_SOBRESAMI.'/').'"';
    echo '</script>'."\n";
    return;
}




/*==============================================
    Botao Entendi
===============================================*/
add_shortcode( 'botao-entendi', 'vm50_entendi' );
function vm50_entendi() {
    $saida = '';
    $saida .= '<span class="vm50_entendi"><input type="button" name="vm50_entendi_bt" id="vm50_entendi_bt" onclick="vm50_entendi();" value="Entendi" /></span>';
    return $saida;
}




/*==============================================
    Ajax de Entendi
===============================================*/
add_action('wp_ajax_vm50_entendi', 'vm50_entendimento');
add_action('wp_ajax_nopriv_vm50_entendi', 'vm50_entendimento');
function vm50_entendimento() {
    $usuario = wp_get_current_user();
    update_user_meta( $usuario->ID, 'vm50_sobre_sami', '1' );
    $cliente = get_user_meta( $usuario->ID, VM50_SAMI_META_CLIENTE, true );
    $medico_familia = site_url('/dashboard/');
    $medico_familia .= '?keyword=&geo=&geo_distance=50&lat=&long=&category=consulta&sortby=&orderby=&showposts=&sub_categories[]=medico-de-familia&zip=&country=&city=&gender=&lang=&view=&cliente_atendido='.$cliente;
    $saida = '<a href="'.$medico_familia.'"><input type="button" value="Continuar" /></a>';
    $saida = $medico_familia;
    echo $saida;
    die();
    return;
}




/*==============================================
    Ajax de Recomendar
===============================================*/
add_action('wp_ajax_vm50_recomendar', 'vm50_recomendar');
add_action('wp_ajax_nopriv_vm50_recomendar', 'vm50_recomendar');
function vm50_recomendar() {
    $profissional = ( isset($_POST['profissional']) ) ? $_POST['profissional'] : '';
    $recomendacao = ( isset($_POST['recomenda']) )    ? $_POST['recomenda']    : '';
    if ( ( is_array( $recomendacao ) ) && ( $profissional != '' ) ) {
        $profissional = intval( $profissional );
        for ( $rc=0; $rc<count($recomendacao); $rc++ ) {
            if ( $recomendacao[$rc] != '' ) {
                $recomenda = intval( $recomendacao[$rc] );
                delete_user_meta( $profissional, VM50_SAMI_META_RECOMENDACAO_MEDICA, $recomenda );
                add_user_meta(    $profissional, VM50_SAMI_META_RECOMENDACAO_MEDICA, $recomenda );
                add_user_meta(    $profissional, VM50_SAMI_META_RECOMENDACAO_TEMPO,  $recomenda.'-'.date('Ymd') );
            }
        }
    }
    echo 'Recomendado';
    die();
    return;
}




/*==============================================
    Lista Recomendadoes
===============================================*/
add_shortcode( 'lista-recomendacoes', 'vm50_lista_recomendacoes' );
function vm50_lista_recomendacoes() {
    $usuario     = wp_get_current_user();
    $recomenda_t = get_user_meta( $usuario->ID, VM50_SAMI_META_RECOMENDACAO_TEMPO );
    $corte       = date('Ymd', strtotime('-30 days'));
    for ($rt=0; $rt<count($recomenda_t); $rt++) {
        list($rec_id, $rec_tempo) = explode('-',$recomenda_t[$rt]);
        if ( $corte > $rec_tempo ) {
            delete_user_meta( $usuario->ID, VM50_SAMI_META_RECOMENDACAO_TEMPO,  $recomenda_t[$rt] );
            delete_user_meta( $usuario->ID, VM50_SAMI_META_RECOMENDACAO_MEDICA, $rec_id );
        }
    }
    $recomendacoes = get_user_meta( $usuario->ID, VM50_SAMI_META_RECOMENDACAO_MEDICA );
    $recomenda     = '';
    for ($rc=0; $rc<count($recomendacoes); $rc++) {
        if ( $recomenda !='' ) {
            $recomenda .= ',';
        }
        $recomenda .= $recomendacoes[$rc];
    }
    $reco_url = site_url('/find/');
    $reco_url .= '?keyword=&geo=&geo_distance=50&lat=&long=&category=consulta&sortby=&orderby=&showposts=&sub_categories[]=medico-de-familia&zip=&country=&city=&gender=&lang=&view=&recomenda='.$recomenda;
    echo '<script>'."\n";
    echo 'window.location.href="'.$reco_url.'"';
    echo '</script>'."\n";
//    header('Location: '.$reco_url);
    return;
}


