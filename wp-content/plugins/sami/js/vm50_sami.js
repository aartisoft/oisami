jQuery(document).ready(function() {
    jQuery('#vm50_cpf').mask('000.000.000-00', {placeholder: 'CPF ___.___.___-__'});
    jQuery('#vm50_cep').mask('00000-000', {placeholder: 'CEP _____-__'});
    jQuery('#vm50_telefone').mask('(00) 00000-0000', {placeholder: 'Telefone (__) _____-____'});

    jQuery('#vm50_cep').blur(function() {
        var cep = jQuery(this).val().replace(/\D/g, '');
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                jQuery('#vm50_endereco').val('...');
                jQuery('#vm50_bairro').val('...');
                jQuery('#vm50_cidade').val('...');
                jQuery.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        jQuery('#vm50_endereco').val(dados.logradouro);
                        jQuery('#vm50_bairro').val(dados.bairro);
                        jQuery('#vm50_cidade').val(dados.localidade);
                        jQuery('#vm50_estado').val(dados.uf);
                    } else {
                        vm50_limpa_formulario_cep();
                        alert("CEP não encontrado.");
                    }
                });
            } else {
                vm50_limpa_formulario_cep();
                alert("Formato de CEP inválido.");
            }
        } else {
            vm50_limpa_formulario_cep();
        }
    });

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
    var genero      = jQuery('#vm50_genero').val();
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
    genero          = genero.trim();

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
    if ( genero.length == 0 ) {
        erro += 'Gênero\n';
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
            'estado'      : estado,
            'genero'      : genero
        };
        jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
            if (response.substr(0,4)=='Erro') {
                jQuery('.vm50-confirma-dados-submit').html( response );
            } else {
                window.location.href = response;
            }
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
        if (response.substr(0,4)=='Erro') {
            jQuery('.vm50_aceitar').html( response );
        } else {
            window.location.href = response;
        }
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
        if (response.substr(0,4)=='Erro') {
            jQuery('.vm50_pesquisa').html( response );
        } else {
            window.location.href = response;
        }
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
        if (response.substr(0,4)=='Erro') {
            jQuery('.vm50_entendi').html( response );
        } else {
            window.location.href = response;
        }
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
    jQuery('#vm50_admin_cliente').html( '<h3>Aguarde...</h3>' );
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



function vm50_admin_deleta_cliente_bt( cliente ) {
    var prossegue = confirm('Deseja deletar este cliente? \n Esta operação não pode ser revertida!');
    if (prossegue == true) {
        var data = {
            'action'  : 'vm50_deletarcliente',
            'cliente' : cliente
        };
        jQuery.post(referenciaSami.samiAjaxUrl, data, function(response) {
            jQuery('#vm50_admin_cliente').html( response );
            return;
        });
    }
    return;
}



function vm50_sami_importa( ) {
    var tipo = jQuery('input[name=vm50_sami_importa_tipo]:checked').val();
//    var clie = jQuery('#vm50_sami_importa_cliente').val();
    if ( ( tipo == 'C' ) || ( tipo == 'M' ) || ( tipo == 'U' ) ) {
        if ( tipo == 'U' )  {
            var cliente = jQuery('#vm50_sami_importa_cliente').val();
            var medico  = jQuery('#escolhe_medico_'+cliente).val();
            if ( (cliente=='') || (isNaN(cliente)) ) {
                alert( 'Selecione um cliente!' );
//            } else if ( (medico=='') || (medico==null) || (isNaN(medico)) ) {
//                alert( 'Selecione um médico!' );
            } else {
                jQuery('#vm50_cliente').val( cliente );
                jQuery('#vm50_medico').val( medico );
                jQuery('#vm50_sami_importa_upload').submit();
            }
        } else {
            jQuery('#vm50_sami_importa_upload').submit();
        }
    } else {
        alert( 'Selecione um tipo de arquivo!' );
    }
    return;
}



function vm50_sami_importa_muda_tipo( tipo ) {
    if ( tipo == 'C' ) {
        jQuery('#vm50_sami_importa_cliente_area').hide();
        jQuery('#vm50_sami_importa_medico_area').hide();
    } else if ( tipo == 'M' ) {
        jQuery('#vm50_sami_importa_cliente_area').hide();
        jQuery('#vm50_sami_importa_medico_area').hide();
    } else if ( tipo == 'U' ) {
        jQuery('#vm50_sami_importa_cliente_area').show();
        jQuery('#vm50_sami_importa_medico_area').show();
    }
    return;
}



function vm50_sami_importa_muda_cliente() {
    var tipo = jQuery('input[name=vm50_sami_importa_tipo]:checked').val();
    if ( tipo == 'U' ) {
        var cliente = jQuery('#vm50_sami_importa_cliente').val();
        jQuery('.vm50_escolhe_medico').hide();
        jQuery('#escolhe_medico_'+cliente).show();
    }
    return;
}



function vm50_limpa_formulario_cep() {
    jQuery('#vm50_endereco').val('');
    jQuery('#vm50_bairro').val('');
    jQuery('#vm50_cidade').val('');
    jQuery('#vm50_estado').val('');
}
