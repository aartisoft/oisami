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
    $ver = '1.2d';
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
    Importacao
===============================================*/
add_shortcode('vm50-sami-importacao', 'vm50_sami_monta_impportacao');
function vm50_sami_monta_impportacao() {
    if( isset( $_POST['importar'] ) ) {
        vm50_sami_importar();
    } else {
        vm50_sami_instala_tabela();
        vm50_sami_tela_importacao();
    }
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
        $args['meta_query']     = array(
            'relation' => 'OR',
                array(
                    'key'     => 'first_name',
                    'value'   => $texto,
                    'compare' => 'LIKE'
                ),
                array(
                    'key'     => 'last_name',
                    'value'   => $texto,
                    'compare' => 'LIKE'
                ),
        );
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





/*==============================================
    Cria as tabelas
===============================================*/
function vm50_sami_instala_tabela() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $sql      = '';
    $tabela01 = $wpdb->prefix.'vm50_sami_clientes';
    if ( $wpdb->get_var("SHOW TABLES LIKE '".$tabela01."'") != $tabela01 ) {
        $sql .= "CREATE TABLE ".$tabela01;
        $sql .= " ( ";
        $sql .= "ID          int(11) NOT NULL AUTO_INCREMENT, ";
        $sql .= "razao       varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "cnpj        varchar(18)  COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "endereco    varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "numero      varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "complemento varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "bairro      varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "cep         varchar(9)   COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "cidade      varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "estado      varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "responsavel varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "telefone    varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "email       varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "num_func    varchar(5)   COLLATE utf8_unicode_ci DEFAULT NULL, ";
        $sql .= "PRIMARY KEY (ID) ";
        $sql .= " ); \n";
    }
    if ( $sql != '' ) {
        dbDelta( $sql );
    }
    return;
}





/*==============================================
    Cria tela de importacao
===============================================*/
function vm50_sami_tela_importacao( $erro='' ) {
    list( $clientes, $medicos ) = vm50_sami_lista_clientes('select');
    $saida  = '';
    $saida .= '<div class="vm50-sami-importacao">';
    $saida .= '<form action="#" method="post" name="vm50_sami_importa_upload" id="vm50_sami_importa_upload" enctype="multipart/form-data">';
    $saida .= '<div class="vm50-sami-importa-linha vm50-sami-importa-erro">';
    $saida .= $erro;
    $saida .= '</div>'; //.vm50-sami-importa-linha
    $saida .= '<div class="vm50-sami-importa-linha">';
    $saida .= '<label for="tipo_importacao">Tipo de Importação: </lable><br />';
    $saida .= '<input type="radio" name="vm50_sami_importa_tipo" id="vm50_sami_importa_tipo_C" value="C" onchange="vm50_sami_importa_muda_tipo(\'C\');"> Clientes<br />';
    $saida .= '<input type="radio" name="vm50_sami_importa_tipo" id="vm50_sami_importa_tipo_M" value="M" onchange="vm50_sami_importa_muda_tipo(\'M\');"> Médicos e auxiliares<br />';
    $saida .= '<input type="radio" name="vm50_sami_importa_tipo" id="vm50_sami_importa_tipo_U" value="U" onchange="vm50_sami_importa_muda_tipo(\'U\');"> Usuários';
    $saida .= '</div>'; //.vm50-sami-importa-linha
    $saida .= '<div class="vm50-sami-importa-linha" id="vm50_sami_importa_cliente_area" style="display:none;">';
    $saida .= '<label for="cliente">Cliente: </lable>';
    $saida .= '<select name="vm50_sami_importa_cliente" id="vm50_sami_importa_cliente" onchange="vm50_sami_importa_muda_cliente();">';
    $saida .= $clientes;
    $saida .= '</select>';
    $saida .= 'Nome do cupom: <input type="text" name="vm50_sami_importa_cupom" id="vm50_sami_importa_cupom">';
    $saida .= '</div>'; //.vm50-sami-importa-linha
    $saida .= '<div class="vm50-sami-importa-linha" id="vm50_sami_importa_medico_area" style="display:none;">';
    $saida .= '<label for="medico">Médico: </lable>';
    if ( $medicos ) {
        $medicos = vm50_sami_lista_medicos();
//        $saida .='passei';
    }
    $saida .= $medicos;
    $saida .= '</div>'; //.vm50-sami-importa-linha
    $saida .= '<div class="vm50-sami-importa-linha" >';
    $saida .= '<label for="arquivo">Arquivo de importação (csv): </lable>';
    $saida .= '<input type="file" name="vm50_sami_importa_arquivo" id="vm50_sami_importa_arquivo" />';
    $saida .= '</div>'; //.vm50-sami-importa-linha
    $saida .= '<div class="vm50-sami-importa-linha" >';
    $saida .= '<input type="hidden" id="importar" name="importar" value="1">';
    $saida .= '<input type="hidden" id="vm50_cliente" name="vm50_cliente" value="">';
    $saida .= '<input type="hidden" id="vm50_medico"  name="vm50_medico"  value="">';
    $saida .= '<input type="button" onclick="vm50_sami_importa();" value="Importar">';
    $saida .= '</div>'; //.vm50-sami-importa-linha
    $saida .= '</form>';
    $saida .= '</div>'; //.vm50-sami-importacao
    echo $saida;
    return $saida;
}





/*==============================================
    Lista clientes
===============================================*/
function vm50_sami_lista_clientes( $formato = 'select' ) {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $tabela01 = $wpdb->prefix.'vm50_sami_clientes';
    $clientes = $wpdb->get_results( "SELECT ID, razao FROM ".$tabela01." ORDER BY razao" );
    $saida    = false;
    $said1    = false;
    if ( isset($clientes[0]->ID) ) {
        if ( $formato=='select' ) {
            $saida = '<option value=""></option>';
            for ($cl=0; $cl<count($clientes); $cl++) {
                $saida .= '<option value="'.$clientes[$cl]->ID.'">'.$clientes[$cl]->razao.'</option>';
                $said1 .= '<select id="escolhe_medico_'.$clientes[$cl]->ID.'" id="escolhe_medico_'.$clientes[$cl]->ID.'" style="display:none;" class="vm50_escolhe_medico">';
                $said1 .= vm50_sami_lista_medicos($clientes[$cl]->ID, 'select');
                $said1 .= '</select>';
            }
        }
        $said1 .= '<div id="vm50_escolhe_medico_checkbox" style="display:none;">'.vm50_sami_lista_medicos().'</div>';
    }
    return array($saida, $said1);
}





/*==============================================
    Lista medicos
===============================================*/
function vm50_sami_lista_medicos( $cliente = '', $formato = 'select' ) {
    $saida    = false;
    if ( $cliente!='' ) {
        $pesquisa = new WP_User_Query( array( 'meta_key' => 'VM50_SAMI_META_CLIENTES_ATENDIDOS', $cliente ) );
        $medicos  = $pesquisa->get_results();
        if ( !empty($medicos) ) {
            if ( $formato=='select' ) {
                $saida = '<option value=""></option>';
            }
            foreach ( $medicos as $medico ) {
                if ( $formato=='select' ) {
                    $saida .= '<option value="'.$medico->ID.'">'.$medico->first_name.' '.$medico->last_name.'</option>';
                }
            }
        }
    } else {
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
        $pesquisa = new WP_User_Query($query_args);
        $medicos  = $pesquisa->get_results();
        if ( !empty($medicos) ) {
            $saida = '';
            foreach ( $medicos as $medico ) {
                $saida .= '<input type="checkbox" name="vm50_sami_importa_medico[]" id="vm50_sami_importa_medico_'.$medico->ID.'" value="'.$medico->ID.'"/>';
                $saida .= $medico->display_name;
                $saida .= '</p>';
            }
        }
    }
    return $saida;
}




/*==============================================
    Checa se eh csv
===============================================*/
function vm50_importa_eh_csv( $arquivo ) {
echo $arquivo;
    $filetype = wp_check_filetype( basename( $arquivo ));
    $saida    = false;
    if ($filetype['ext'] == 'csv') {
        $saida = true;
    }
    return $saida;
}





/*==============================================
    Funcoes Basicas Detecta Delimitar de Arquivo
===============================================*/
function vm50_importa_detect_delimiter($file){
    $handle       = @fopen($file, "r");
    $sumComma     = 0;
    $sumSemiColon = 0;
    $sumBar       = 0;
    if($handle){
        while (($data = fgets($handle, 4096)) !== FALSE) {
            $sumComma     += substr_count($data, ",");
            $sumSemiColon += substr_count($data, ";");
            $sumBar       += substr_count($data, "|");
        }
    }
    fclose($handle);
    if(($sumComma > $sumSemiColon) && ($sumComma > $sumBar))
        return ",";
    else if(($sumSemiColon > $sumComma) && ($sumSemiColon > $sumBar))
        return ";";
    else
        return "|";
}





/*==============================================
    Importar
===============================================*/
function vm50_sami_importar() {
    $tipo    = isset($_POST['vm50_sami_importa_tipo'])    ? $_POST['vm50_sami_importa_tipo']    : false;
    $cliente = isset($_POST['vm50_sami_importa_cliente']) ? $_POST['vm50_sami_importa_cliente'] : false;
    $medico  = isset($_POST['vm50_sami_importa_medico'])  ? $_POST['vm50_sami_importa_medico']  : false;
    $cupom   = isset($_POST['vm50_sami_importa_cupom'])   ? $_POST['vm50_sami_importa_cupom']   : false;
    $arquivo = ($_FILES['vm50_sami_importa_arquivo']['size'] > 0) ? $_FILES['vm50_sami_importa_arquivo']['tmp_name'] : false;
    $erro    = '';
    if ( !$tipo ) {
        $erro .= '<li>Tipo de Importação</li>';
    } else {
        if ( ( $tipo=='U' ) && ( !$cliente ) ) {
            $erro .= '<li>Falta escolher o cliente</li>';
        }
        if ( ( $tipo=='C' ) && ( !$medico ) ) {
            $erro .= '<li>Falta escolher o(s) Médico(s)</li>';
        }
    }
    if ( !$arquivo ) {
        $erro .= '<li>Selecionar o arquivo</li>';
    }
    if ( $erro == '' ) {
        if ( $tipo == 'C' ) {
            $saida = vm50_sami_importar_cliente( $arquivo, $medico );
        } elseif ( $tipo == 'M' ) {
            $saida = vm50_sami_importar_medico( $arquivo );
        } elseif ( $tipo == 'U' ) {
            $saida = vm50_sami_importar_usuario( $arquivo, $cliente, $medico, $cupom );
        }
    } else {
        $saida = 'Encontramos algum problema:<ul>'.$erro.'</ul>';
    }
    vm50_sami_tela_importacao( $saida );
    return;
}





/*==============================================
    Importar Cliente
===============================================*/
function vm50_sami_importar_cliente( $arquivo, $medico ) {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $tabela01  = $wpdb->prefix.'vm50_sami_clientes';
    $campos    = 'razao,cnpj,endereco,numero,complemento,bairro,cep,cidade,estado,responsavel,telefone,email,num_func';
    $saida     = '';
    $handle    = fopen($arquivo, "r");
    $delimi    = vm50_importa_detect_delimiter( $arquivo );
    $cabeco    = fgetcsv( $handle, 0, $delimi);
    $razao     = $cabeco[0];
    $cnpj      = $cabeco[1];
    $endereco  = $cabeco[2];
    $numero    = $cabeco[3];
    $complemen = $cabeco[4];
    $bairro    = $cabeco[5];
    $cep       = $cabeco[6];
    $cidade    = $cabeco[7];
    $estado    = $cabeco[8];
    $respons   = $cabeco[9];
    $telefone  = $cabeco[10];
    $email     = $cabeco[11];
    $nfunc     = $cabeco[12];
    if ( ($razao=='Razao') && ($cnpj=='CNPJ') && ($endereco=='Endereco') && ($numero=='Numero') && ($complemen=='Complemento') && ($bairro=='Bairro') && ($cep=='CEP') && ($cidade=='Cidade') && ($estado=='Estado') && ($respons=='Responsavel') && ($telefone=='Telefone') && ($email=='Email') && ($nfunc=='Num.Func.') ) {
        while ( ($linha = fgetcsv( $handle, 0, $delimi)) !== FALSE) {
            if ( $linha[0]!='' ) {
                $campo['razao']       = $linha[0];
                $campo['cnpj']        = $linha[1];
                $campo['endereco']    = $linha[2];
                $campo['numero']      = $linha[3];
                $campo['complemento'] = $linha[4];
                $campo['bairro']      = $linha[5];
                $campo['cep']         = $linha[6];
                $campo['cidade']      = $linha[7];
                $campo['estado']      = $linha[8];
                $campo['responsavel'] = $linha[9];
                $campo['telefone']    = $linha[10];
                $campo['email']       = $linha[11];
                $campo['num_func']    = $linha[12];
                $existe    = $wpdb->get_results( "SELECT ID FROM ".$tabela01." WHERE razao = '".$campo['razao']."'" );
                if ( isset($existe[0]->ID) ) {
                    $wpdb->update($tabela01, $campo, array('razao' => $campo['razao']));
                    $clie_id = $existe[0]->ID;
                    $saida  .= 'Atualizado: '.$campo['razao'].'</br>';
                } else {
//                    $wpdb->show_errors();
                    $retorno = $wpdb->insert($tabela01, $campo);
                    $clie_id = $wpdb->insert_id;
                    $saida  .= 'Incluído: '.$campo['razao'].'</br>';
                }
                if ( !is_array($medico) ) {
                    $medico[0] = $medico;
                }
                for ($md=0; $md<count($medico); $md++) {
                    update_user_meta( $medico[$md], VM50_SAMI_META_CLIENTES_ATENDIDOS, $clie_id );
                }
            }
        }

    } else {
        $saida .= '<p>Layout do arquivo é inválido!</p>';
        $saida .= '<p>Layout permitido:</p>';
        $saida .= '<p><b>Razao,CNPJ,Endereco,Numero,Complemento,Bairro,CEP,Cidade,Estado,Responsavel,Telefone,Email,Num.Func.</b></p>';
    }
    fclose($handle);
    return $saida;
}





/*==============================================
    Importar Medico
===============================================*/
function vm50_sami_importar_medico( $arquivo ) {
    $saida     = '';
    $handle    = fopen($arquivo, "r");
    $delimi    = vm50_importa_detect_delimiter( $arquivo );
    $cabeco    = fgetcsv( $handle, 0, $delimi);
    $m_nome      = $cabeco[0];
    $m_cpf       = $cabeco[1];
    $m_email     = $cabeco[2];
    $m_telefone  = $cabeco[3];
    $m_celular   = $cabeco[4];
    $m_crm       = $cabeco[5];
    $m_dtnasc    = $cabeco[6];
    $m_endereco  = $cabeco[7];
    $m_numero    = $cabeco[8];
    $m_complemen = $cabeco[9];
    $m_bairro    = $cabeco[10];
    $m_cep       = $cabeco[11];
    $m_cidade    = $cabeco[12];
    $m_estado    = $cabeco[13];
    $e_nome      = $cabeco[14];
    $e_cpf       = $cabeco[15];
    $e_email     = $cabeco[16];
    $e_telefone  = $cabeco[17];
    $e_celular   = $cabeco[18];
    $e_coren     = $cabeco[19];
    $e_dtnasc    = $cabeco[20];
    $e_endereco  = $cabeco[21];
    $e_numero    = $cabeco[22];
    $e_complemen = $cabeco[23];
    $e_bairro    = $cabeco[24];
    $e_cep       = $cabeco[25];
    $e_cidade    = $cabeco[26];
    $e_estado    = $cabeco[27];
    if (
        ($m_nome      == 'medico_nome') &&
        ($m_cpf       == 'medico_cpf') &&
        ($m_email     == 'medico_email') &&
        ($m_telefone  == 'medico_telefone') &&
        ($m_celular   == 'medico_celular') &&
        ($m_crm       == 'medico_crm') &&
        ($m_dtnasc    == 'medico_data_nasc') &&
        ($m_endereco  == 'medico_endereco') &&
        ($m_numero    == 'medico_numero') &&
        ($m_complemen == 'medico_complemento') &&
        ($m_bairro    == 'medico_bairro') &&
        ($m_cep       == 'medico_cep') &&
        ($m_cidade    == 'medico_cidade') &&
        ($m_estado    == 'medico_estado') &&
        ($e_nome      == 'enfermeiro_nome') &&
        ($e_cpf       == 'enfermeiro_cpf') &&
        ($e_email     == 'enfermeiro_email') &&
        ($e_telefone  == 'enfermeiro_telefone') &&
        ($e_celular   == 'enfermeiro_celular') &&
        ($e_coren     == 'enfermeiro_coren') &&
        ($e_dtnasc    == 'enfermeiro_dt_nasc') &&
        ($e_endereco  == 'enfermeiro_endereco') &&
        ($e_numero    == 'enfermeiro_numero') &&
        ($e_complemen == 'enfermeiro_complemento') &&
        ($e_bairro    == 'enfermeiro_bairro') &&
        ($e_cep       == 'enfermeiro_cep') &&
        ($e_cidade    == 'enfermeiro_cidade') &&
        ($e_estado    == 'enfermeiro_estado') ) {
        while ( ($linha = fgetcsv( $handle, 0, $delimi)) !== FALSE) {
            if ( ( $linha[0] != '' ) && ( $linha != '' ) ) {
                $medico['nome']          = $linha[0];
                $medico['cpf']           = $linha[1];
                $medico['email']         = $linha[2];
                $medico['telefone']      = $linha[3];
                $medico['celular']       = $linha[4];
                $medico['crm']           = $linha[5];
                $medico['dtnasc']        = $linha[6];
                $medico['endereco']      = $linha[7];
                $medico['numero']        = $linha[8];
                $medico['complemento']     = $linha[9];
                $medico['bairro']        = $linha[10];
                $medico['cep']           = $linha[11];
                $medico['cidade']        = $linha[12];
                $medico['estado']        = $linha[13];
                $enfermeiro['nome']      = $linha[14];
                $enfermeiro['cpf']       = $linha[15];
                $enfermeiro['email']     = $linha[16];
                $enfermeiro['telefone']  = $linha[17];
                $enfermeiro['celular']   = $linha[18];
                $enfermeiro['coren']     = $linha[19];
                $enfermeiro['dtnasc']    = $linha[20];
                $enfermeiro['endereco']  = $linha[21];
                $enfermeiro['numero']    = $linha[22];
                $enfermeiro['complemento'] = $linha[23];
                $enfermeiro['bairro']    = $linha[24];
                $enfermeiro['cep']       = $linha[25];
                $enfermeiro['cidade']    = $linha[26];
                $enfermeiro['estado']    = $linha[27];
                //Enfermeiro
                $e_existe = new WP_User_Query( array( 'user_login' => $enfermeiro['cpf'] ) );
                $e_id     = false;
                if ( ! empty( $e_existe->get_results() ) ) {
                    foreach ( $e_existe->get_results() as $enfermeiros ) {
                        $e_id = $enfermeiros->ID;
                    }
                    $e_dados = array('ID' => $e_id);
                    if ( $enfermeiro['nome'] != '' ) {
                        $e_dados['user_firstname'] = $enfermeiro['nome'];
                    }
                    if ( $enfermeiro['email'] != '' ) {
                        $e_dados['user_email'] = $enfermeiro['email'];
                    }
//                    $wpdb->show_errors();
                    $e_id   = wp_update_user( $e_dados );
                    $saida .= 'Alterado: '.$enfermeiro['nome'].'</br>';
                } else {
                    $e_dados = array (
                        'user_login'   => $enfermeiro['cpf'],
                        'user_email'   => $enfermeiro['email'],
                        'display_name' => $enfermeiro['nome'],
                        'first_name'   => $enfermeiro['nome'],
                        'user_pass'    => $enfermeiro['cpf'],
                        'role'         => 'professional'
                    );
//                    $wpdb->show_errors();
                    $e_id   = wp_insert_user($e_dados) ;
                    $saida .= 'Incluído: '.$enfermeiro['nome'].'</br>';
                }
                if ( $e_id ) {
                    vm50_sami_atualiza_dados_basicos_medico( $e_id, false, $enfermeiro );
                }
                //Medico
                $m_existe = new WP_User_Query( array( 'user_login' => $medico['cpf'] ) );
                $m_id     = false;
                if ( ! empty( $m_existe->get_results() ) ) {
                    foreach ( $m_existe->get_results() as $medicos ) {
                        $m_id = $medicos->ID;
                    }
                    $m_dados = array('ID' => $m_id);
                    if ( $medico['nome'] != '' ) {
                        $m_dados['user_firstname'] = $medico['nome'];
                    }
                    if ( $medico['email'] != '' ) {
                        $m_dados['user_email'] = $medico['email'];
                    }
//                    $wpdb->show_errors();
                    $m_id   = wp_update_user( $m_dados );
                    $saida .= 'Alterado: '.$medico['nome'].'</br>';
                } else {
                    $m_dados = array (
                        'user_login'   => $medico['cpf'],
                        'user_email'   => $medico['email'],
                        'display_name' => $medico['nome'],
                        'first_name'   => $medico['nome'],
                        'user_pass'    => $medico['cpf'],
                        'role'         => 'professional'
                    );
//                    $wpdb->show_errors();
                    $m_id   = wp_insert_user($m_dados) ;
                    $saida .= 'Incluído: '.$medico['nome'].'</br>';
                }
                if ( $m_id ) {
                    vm50_sami_atualiza_dados_basicos_medico( $m_id, true, $medico );
                    if ( $e_id ) {
                        update_user_meta( $m_id, VM50_SAMI_META_ENFERMEIRO, $e_id );
                    }
                }
            }
        }
    } else {
        $saida  = '<p>Layout do arquivo é inválido!</p>';
        $saida .= '<p>Layout permitido:</p>';
        $saida .= '<p><b>medico_nome,medico_cpf,medico_email,medico_telefone,medico_celular,medico_crm,medico_data_nasc,medico_endereco,medico_numero,medico_complemento,medico_bairro,medico_cep,medico_cidade,medico_estado,enfermeiro_nome,enfermeiro_cpf,enfermeiro_email,enfermeiro_telefone,enfermeiro_celular,enfermeiro_coren,enfermeiro_dt_nasc,enfermeiro_endereco,enfermeiro_numero,enfermeiro_complemento,enfermeiro_bairro,enfermeiro_cep,enfermeiro_cidade,enfermeiro_estado
        </b></p>';
    }
    fclose($handle);
    return $saida;
}





/*==============================================
    Atualiza Dados Basicos da Profile Professional
===============================================*/
function vm50_sami_atualiza_dados_basicos_medico( $usuario, $medicodefamilia=false, $dados=array() ) {
    $business_hours	= 'a:7:{s:6:"monday";a:2:{s:9:"starttime";a:3:{i:0;s:5:"09:00";i:1;s:5:"17:00";i:2;s:5:"00:00";}s:7:"endtime";a:3:{i:0;s:5:"17:00";i:1;s:5:"23:00";i:2;s:5:"09:00";}}s:7:"tuesday";a:2:{s:9:"starttime";a:2:{i:0;s:5:"09:00";i:1;s:5:"17:00";}s:7:"endtime";a:2:{i:0;s:5:"17:00";i:1;s:5:"23:00";}}s:9:"wednesday";a:2:{s:9:"starttime";a:1:{i:0;s:5:"09:00";}s:7:"endtime";a:1:{i:0;s:5:"17:00";}}s:8:"thursday";a:2:{s:9:"starttime";a:1:{i:0;s:5:"09:00";}s:7:"endtime";a:1:{i:0;s:5:"17:00";}}s:6:"friday";a:2:{s:9:"starttime";a:1:{i:0;s:5:"09:00";}s:7:"endtime";a:1:{i:0;s:5:"17:00";}}s:8:"saturday";a:3:{s:7:"off_day";s:2:"on";s:9:"starttime";a:1:{i:0;s:0:"";}s:7:"endtime";a:1:{i:0;s:0:"";}}s:6:"sunday";a:3:{s:7:"off_day";s:2:"on";s:9:"starttime";a:1:{i:0;s:0:"";}s:7:"endtime";a:1:{i:0;s:0:"";}}}';
    $privacy_settings = 'a:20:{s:1996:"profile_photo";s:2:"on";s:14:"profile_banner";s:2:"on";s:19:"profile_appointment";s:2:"on";s:15:"profile_contact";s:2:"on";s:1996:"profile_hours";s:2:"on";s:15:"profile_service";s:2:"on";s:12:"profile_team";s:2:"on";s:15:"profile_gallery";s:2:"on";s:14:"profile_videos";s:2:"on";s:20:"privacy_introduction";s:2:"on";s:17:"privacy_languages";s:2:"on";s:18:"privacy_experience";s:2:"on";s:14:"privacy_awards";s:2:"on";s:21:"privacy_qualification";s:2:"on";s:15:"privacy_amenity";s:2:"on";s:17:"privacy_insurance";s:2:"on";s:17:"privacy_brochures";s:2:"on";s:20:"privacy_job_openings";s:2:"on";s:16:"privacy_articles";s:2:"on";s:1996:"privacy_share";s:2:"on";}';
    $privacy_array	= unserialize( $privacy_settings );
    update_user_meta( $usuario, 'show_admin_bar_front',  'false' );
    update_user_meta( $usuario, 'business_hours',        unserialize( $business_hours ) );
    update_user_meta( $usuario, 'business_hours_format', '24hour' );
    update_user_meta( $usuario, 'privacy',               unserialize( $privacy_settings ) );
    update_user_meta( $usuario, 'is_avatar_available',   1);
    update_user_meta( $usuario, 'set_profile_view',      0);
    update_user_meta( $usuario, 'category',              1996);
    if ( $medicodefamilia ) {
        update_user_meta( $usuario, 'sub_category',         'a:1:{i:0;s:17:"medico-de-familia";}');
        update_user_meta( $usuario, 'spcategory_search',    'Consultas');
        update_user_meta( $usuario, 'spsubcategory_search', 'a:1:{i:0;s:18:"Médico de Familia";}');
    }
    update_user_meta( $usuario, 'audio_video_urls',      'a:1:{i:0;s:0:"";}');
    update_user_meta( $usuario, 'awards',                'a:0:{}');
    update_user_meta( $usuario, 'experience',            'a:0:{}');
    update_user_meta( $usuario, 'qualification',         'a:0:{}');
    update_user_meta( $usuario, 'profile_services',      'a:0:{}');
    if ( ( !empty($privacy_array) ) && ( is_array($privacy_array) ) ) {
        foreach ($privacy_array as $key => $privacy) {
            update_user_meta($usuario, esc_attr($key), esc_attr($privacy));
        }
    }
    update_user_meta( $usuario, 'appointment_currency', 'R$' );
    update_user_meta( $usuario, 'rich_editing',         'true' );

    if ( ( isset($dados['nome']) ) && ( $dados['nome'] != '' ) ) {
        update_user_meta( $usuario, 'billing_first_name', $dados['nome'] );
        update_user_meta( $usuario, 'shipping_first_name', $dados['nome'] );
    }
    if ( ( isset($dados['email']) ) && ( $dados['email'] != '' ) ) {
        update_user_meta( $usuario, 'billing_email', $dados['email'] );
    }
    if ( ( isset($dados['telefone']) ) && ( $dados['telefone'] != '' ) ) {
        update_user_meta( $usuario, 'billing_phone', $dados['telefone'] );
        update_user_meta( $usuario, 'billing_cellphone', $dados['telefone']);
        update_user_meta( $usuario, 'phone', $dados['telefone'] );
    }
    if ( ( isset($dados['cpf']) ) && ( $dados['cpf'] != '' ) ) {
        update_user_meta( $usuario, 'billing_cpf', $dados['cpf'] );
    }
    if ( ( isset($dados['endereco']) ) && ( $dados['endereco'] != '' ) ) {
        update_user_meta( $usuario, 'billing_address_1', $dados['endereco'] );
        update_user_meta( $usuario, 'shipping_address_1', $dados['endereco'] );
    }
    if ( ( isset($dados['numero']) ) && ( $dados['numero'] != '' ) ) {
        update_user_meta( $usuario, 'billing_number', $dados['numero'] );
        update_user_meta( $usuario, 'shipping_number', $dados['numero'] );
    }
    if ( ( isset($dados['complemen']) ) && ( $dados['complemen'] != '' ) ) {
        update_user_meta( $usuario, 'billing_address_2', $dados['complemen'] );
        update_user_meta( $usuario, 'shipping_address_2', $dados['complemen'] );
    }
    if ( ( isset($dados['bairro']) ) && ( $dados['bairro'] != '' ) ) {
        update_user_meta( $usuario, 'billing_neighborhood', $dados['bairro'] );
        update_user_meta( $usuario, 'shipping_neighborhood', $dados['bairro'] );
    }
    if ( ( isset($dados['cidade']) ) && (  $dados['cidade'] != '' ) ) {
        update_user_meta( $usuario, 'billing_city',  $dados['cidade'] );
        update_user_meta( $usuario, 'shipping_city',  $dados['cidade'] );
    }
    if ( ( isset($dados['cep']) ) && ( $dados['cep'] != '' ) ) {
        update_user_meta( $usuario, 'zip', $dados['cep'] );
        update_user_meta( $usuario, 'billing_postcode', $dados['cep'] );
        update_user_meta( $usuario, 'shipping_postcode', $dados['cep'] );
    }
    if ( ( isset($dados['estado']) ) && ( $dados['estado'] != '' ) ) {
        update_user_meta( $usuario, 'billing_state', $dados['estado'] );
        update_user_meta( $usuario, 'shipping_state', $dados['estado'] );
    }
    if ( ( isset($dados['coren']) ) && ( $dados['coren'] != '' ) ) {
        update_user_meta( $usuario, 'vm50_sami_coren', $dados['coren'] );
    }
    if ( ( isset($dados['crm']) ) && ( $dados['crm'] != '' ) ) {
        update_user_meta( $usuario, 'vm50_sami_coren', $dados['crm'] );
    }
    if ( ( isset($dados['dtnasc']) ) && ( $dados['dtnasc'] != '' ) ) {
        update_user_meta( $usuario, 'vm50_sami_dtnasc', $dados['dtnasc'] );
    }
    return;
}





/*==============================================
    Importar Usuario
===============================================*/
function vm50_sami_importar_usuario( $arquivo, $cliente, $medico, $cupom ) {
    $saida     = '';
    $handle    = fopen($arquivo, "r");
    $delimi    = vm50_importa_detect_delimiter( $arquivo );
    $cabeco    = fgetcsv( $handle, 0, $delimi);
    $nome      = $cabeco[0];
    $cpf       = $cabeco[1];
    $email     = $cabeco[2];
    $celular   = $cabeco[3];
    $endereco  = $cabeco[4];
    $numero    = $cabeco[5];
    $complemen = $cabeco[6];
    $bairro    = $cabeco[7];
    $cep       = $cabeco[8];
    $cidade    = $cabeco[9];
    $estado    = $cabeco[10];
    if (
        ($nome      == 'nome') &&
        ($cpf       == 'cpf') &&
        ($email     == 'email') &&
        ($celular   == 'celular') &&
        ($endereco  == 'endereco') &&
        ($numero    == 'numero') &&
        ($complemen == 'complemento') &&
        ($bairro    == 'bairro') &&
        ($cep       == 'cep') &&
        ($cidade    == 'cidade') &&
        ($estado    == 'estado')
    ) {
        while ( ($linha = fgetcsv( $handle, 0, $delimi)) !== FALSE) {
            if ( ( $linha[0] != '' ) && ( $linha != '' ) ) {
                $usuario['nome']          = $linha[0];
                $usuario['cpf']           = $linha[1];
                $usuario['email']         = $linha[2];
                $usuario['celular']       = $linha[3];
                $usuario['endereco']      = $linha[4];
                $usuario['numero']        = $linha[5];
                $usuario['complemento']   = $linha[6];
                $usuario['bairro']        = $linha[7];
                $usuario['cep']           = $linha[8];
                $usuario['cidade']        = $linha[9];
                $usuario['estado']        = $linha[10];

                $existe = new WP_User_Query( array( 'user_login' => $usuario['cpf'] ) );
                if ( ! empty( $existe->get_results() ) ) {
                    foreach ( $existe->get_results() as $usuarios ) {
                        $user_id = $usuarios->ID;
                    }
                    $dados = array('ID' => $user_id);
                    if ( $usuario['nome'] != '' ) {
                        $dados['user_firstname'] = $usuario['nome'];
                    }
                    if ( $usuario['email'] != '' ) {
                        $dados['user_email'] = $usuario['email'];
                    }
//                    $wpdb->show_errors();
                    $user_id   = wp_update_user( $dados );
                    $saida .= 'Alterado: '.$usuario['nome'].'</br>';
                } else {
                    $dados = array (
                        'user_login'   => $usuario['cpf'],
                        'user_email'   => $usuario['email'],
                        'display_name' => $usuario['nome'],
                        'first_name'   => $usuario['nome'],
                        'user_pass'    => $usuario['cpf'],
                        'role'         => 'customer'
                    );
//                    $wpdb->show_errors();
                    $user_id = wp_insert_user($dados) ;
                    vm50_sami_importar_manda_email( $usuario['email'], $cupom );
                    $saida  .= 'Incluído: '.$usuario['nome'].'</br>';
                }
                if ( $user_id ) {
                    update_user_meta( $user_id, $medico, $cliente );
                    update_user_meta( $user_id, VM50_SAMI_META_MEDICO_FAMILIA, $medico );
                }
            }
        }
    } else {
        $saida  = '<p>Layout do arquivo é inválido!</p>';
        $saida .= '<p>Layout permitido:</p>';
        $saida .= '<p><b>nome,cpf,email,celular,endereco,numero,complemento,bairro,cep,cidade,estado</b></p>';
    }
    fclose($handle);
    return $saida;
}


function vm50_sami_importar_manda_email( $email, $cupom ) {
    $subject  = 'Bem vindo ao Sami';
    $message  = 'Bem vindo ao Sami'."<br />";
    $message .= 'Use este cupom nas primeiras consultas.'."<br />";
    $message .=  '<span style="font-weigth:bold">'.$cupom.'</span>'."<br />";
    $message .= 'Atenciosamente,'."<br />";
    $message .= 'Equipe Sami'."<br />";
    $headers  = "Content-Type: text/html\r\n";
    $attachments="";
    wp_mail( $email, $subject, $message, $headers, $attachments );

}
