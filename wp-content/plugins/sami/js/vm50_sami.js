jQuery(document).ready(function() {
});



function vm50_meu_paciente_pesquisar() {
    var texto = jQuery('#vm50_meu_paciente_pesquisar').val();
    if ( texto != '' ) {
        jQuery('#vm50_meu_paciente_pesquisar_bt').prop('disabled', true);
        jQuery('.vm50_meu_paciente_pesquisa_resultado').html('Aguarde...');
        var data = {
            "action" : "vm50_pesquisameupaciente",
            "texto"  : texto
        };
        jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
            jQuery('.vm50_meu_paciente_pesquisa_resultado').html(response);
            jQuery('#vm50_meu_paciente_pesquisar_bt').prop('disabled', false);
        });
    }
    return;
}



function vm50_meu_paciente_escolher() {
    var paciente = jQuery('input[name=vm50_meu_paciente]:checked').val();
    if ( paciente != '' ) {
        jQuery('#vm50_meu_paciente_pesquisar_bt').prop('disabled', true);
        jQuery('.vm50_meu_paciente_pesquisa_resultado').html('Aguarde...');
        var data = {
            "action"   : "vm50_escolhemeupaciente",
            "paciente" : paciente
        };
        jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
            jQuery('.vm50_meu_paciente_pesquisa_resultado').html(response);
            jQuery('#vm50_meu_paciente_pesquisar_bt').prop('disabled', false);
        });
    }
    return;
}



function vm50_trocarsenha() {
    var pass1 = jQuery('#pass1').val();
    var pass2 = jQuery('#pass2').val();
    var erro  = '';
    pass1     = pass1.trim();
    pass2     = pass2.trim();
    if ( pass1.length == 0 ) {
        erro = 'A senha não pode estar em branco.';
    } else {
        if ( pass1 != pass2 ) {
            erro = 'Senhas não conferem.';
        } else {
            var data = {
                "action" : "vm50_trocasenha",
                "senha"  : pass1
            };
            jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
                jQuery('#vm50_msg_troca_senha').html( response );
                return;
            });
        }
    }
    jQuery('#vm50_msg_troca_senha').html( erro );
    return;
}



function vm50_confirmardados() {
    var userid      = jQuery('#vm50_userid').val();
    var nome        = jQuery('#vm50_nome').val();
    var sobrenome   = jQuery('#vm50_sobrenome').val();
    var email       = jQuery('#vm50_email').val();
    var cpf         = jQuery('#vm50_cpf').val();
    var telefone    = jQuery('#vm50_telefone').val();
    var endereco    = jQuery('#vm50_endereco').val();
    var numero      = jQuery('#vm50_numero').val();
    var complemento = jQuery('#vm50_complemento').val();
    var bairro      = jQuery('#vm50_bairro').val();
    var cidade      = jQuery('#vm50_cidade').val();
    var cep         = jQuery('#vm50_cep').val();
    var estado      = jQuery('#vm50_estado').val();
    var erro        = '';
    nome            = nome.trim();
    sobrenome       = sobrenome.trim();
    email           = email.trim();
    cpf             = cpf.trim();
    endereco        = endereco.trim();
    numero          = numero.trim();
    complemento     = complemento.trim();
    bairro          = bairro.trim();
    cidade          = cidade.trim();
    cep             = cep.trim();
    estado          = estado.trim();

    if ( nome.length == 0 ) {
        erro = 'Nome\n';
    }
    if ( sobrenome.length == 0 ) {
        erro += 'Sobrenome\n';
    }
    if ( email.length == 0 ) {
        erro += 'E-mail\n';
    }
    if ( cpf.length == 0 ) {
        erro += 'CPF\n';
    }
    if ( telefone.length == 0 ) {
        erro += 'Telefone\n';
    }
    if ( endereco.length == 0 ) {
        erro += 'Endereço\n';
    }
    if ( numero.length == 0 ) {
        erro += 'Número\n';
    }
    if ( bairro.length == 0 ) {
        erro += 'Bairro\n';
    }
    if ( cidade.length == 0 ) {
        erro += 'Cidade\n';
    }
    if ( cep.length == 0 ) {
        erro += 'CEP\n';
    }
    if ( estado.length == 0 ) {
        erro += 'Estado\n';
    }

    if ( erro != '' ) {
        alert( 'Dados inválidos:\n' + erro);
    } else {
        var data = {
            'action'      : 'vm50_confirmadados',
            'userid'      : userid,
            'nome'        : nome,
            'sobrenome'   : sobrenome,
            'email'       : email,
            'cpf'         : cpf,
            'telefone'    : telefone,
            'endereco'    : endereco,
            'numero'      : numero,
            'complemento' : complemento,
            'bairro'      : bairro,
            'cidade'      : cidade,
            'cep'         : cep,
            'estado'      : estado
        };
        jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
//            jQuery('#vm50_msg_confirma_dados').html( response );
            jQuery('.vm50-confirma-dados-submit').html( response );
            return;
        });
    }
    return;
}



function vm50_aceitar() {
    jQuery('#vm50_aceitar_bt').prop('disabled', true);
    var data = {
        "action" : "vm50_aceito"
    };
    jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
        jQuery('.vm50_aceitar').html( response );
        return;
    });
    return;
}



function vm50_pesquisa() {
    jQuery('#vm50_pesquisa_bt').prop('disabled', true);
    var data = {
        "action" : "vm50_pesquisa"
    };
    jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
        jQuery('.vm50_pesquisa').html( response );
        return;
    });
    return;
}



function vm50_entendi() {
    jQuery('#vm50_entendi_bt').prop('disabled', true);
    var data = {
        "action" : "vm50_entendi"
    };
    jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
        jQuery('.vm50_entendi').html( response );
        return;
    });
    return;
}



function vm50_recomendar( quem, recomenda ) {
    var data = {
        'action'       : 'vm50_recomendar',
        'profissional' : quem,
        'recomenda'    : recomenda
    };
    jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
        jQuery('.vm50_recomendacoes').html( response );
        return;
    });
    return;
}



function vm50_admin_escolhe_cliente() {
    var cliente = jQuery('#vm50_admin_escolhe_cliente').val();
    jQuery('#vm50_admin_cliente').html( '<h3>Aguarde...</h3>' );
    var data = {
        'action'  : 'vm50_escolhemedicosdocliente',
        'cliente' : cliente,
    };
    jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
        jQuery('#vm50_admin_cliente').html( response );
        return;
    });
    return;
}



function vm50_admin_salvar_medicos_do_cliente( cliente ) {
    var medicos = [];
    var naovai  = [];
//    jQuery("input[name='vm50_escolhe_medicos_do_cliente[]']:checked").each(function () {
//        medicos.push( jQuery(this).val() );
//    });
    jQuery("input[name='vm50_escolhe_medicos_do_cliente[]']").each(function () {
        if ( jQuery(this).prop('checked') ) {
            medicos.push( jQuery(this).val() );
        } else {
            naovai.push( jQuery(this).val() );
        }
    });
    var data = {
        'action'  : 'vm50_salvarmedicosdocliente',
        'cliente' : cliente,
        'medicos' : medicos,
        'naovai'  : naovai
    };
    jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
        jQuery('#vm50_admin_cliente').html( response );
        return;
    });
    return;
}
