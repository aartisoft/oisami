<?php
/*
  Parte do plugin Name: Configuracao Sami
 */





/*==============================================
    Redirecionamento inicial
===============================================*/
add_shortcode( 'importacao', 'vm50_sami_monta_impportacao' );
function vm50_sami_monta_impportacao() {
    if( isset( $_POST['importar'] ) ) {
        vm50_sami_importar();
    } else {
        vm50_sami_instala_tabela();
        vm50_sami_tela_importacao();
    }
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
    list( $clientes, $medicos ) = vm50_sami_lista_clientes('select');;
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
    }
    return array($saida, $said1);
}





/*==============================================
    Lista medicos
===============================================*/
function vm50_sami_lista_medicos( $cliente, $formato = 'select' ) {
    $saida    = false;
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
//        if ( ( $tipo=='U' ) && ( !$medico ) ) {
//            $erro .= '<li>Médico</li>';
//        }
    }
    if ( !$arquivo ) {
        $erro .= '<li>Selecionar o arquivo</li>';
//    } elseif ( !vm50_importa_eh_csv($arquivo) ) {
//        $erro .= '<li>Arquivo precisa ser csv</li>';
    }
    if ( $erro == '' ) {
        if ( $tipo == 'C' ) {
            $saida = vm50_sami_importar_cliente( $arquivo );
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
function vm50_sami_importar_cliente( $arquivo ) {
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
                    $saida .= 'Atualizado: '.$campo['razao'].'</br>';
                } else {
//                    $wpdb->show_errors();
                    $retorno = $wpdb->insert($tabela01, $campo);
                    $saida .= 'Incluído: '.$campo['razao'].'</br>';
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
    $privacy_settings = 'a:20:{s:13:"profile_photo";s:2:"on";s:14:"profile_banner";s:2:"on";s:19:"profile_appointment";s:2:"on";s:15:"profile_contact";s:2:"on";s:13:"profile_hours";s:2:"on";s:15:"profile_service";s:2:"on";s:12:"profile_team";s:2:"on";s:15:"profile_gallery";s:2:"on";s:14:"profile_videos";s:2:"on";s:20:"privacy_introduction";s:2:"on";s:17:"privacy_languages";s:2:"on";s:18:"privacy_experience";s:2:"on";s:14:"privacy_awards";s:2:"on";s:21:"privacy_qualification";s:2:"on";s:15:"privacy_amenity";s:2:"on";s:17:"privacy_insurance";s:2:"on";s:17:"privacy_brochures";s:2:"on";s:20:"privacy_job_openings";s:2:"on";s:16:"privacy_articles";s:2:"on";s:13:"privacy_share";s:2:"on";}';
    $privacy_array	= unserialize( $privacy_settings );
    update_user_meta( $usuario, 'show_admin_bar_front',  'false' );
    update_user_meta( $usuario, 'business_hours',        unserialize( $business_hours ) );
    update_user_meta( $usuario, 'business_hours_format', '24hour' );
    update_user_meta( $usuario, 'privacy',               unserialize( $privacy_settings ) );
    update_user_meta( $usuario, 'is_avatar_available',   1);
    update_user_meta( $usuario, 'set_profile_view',      0);
    update_user_meta( $usuario, 'category',              13);
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
