//formata campos
$("input[name='FONE']").mask("(99)9999-9999");
$("input[name='FAX']").mask("(99)9999-9999");
$("input[name='CEP']").mask("99.999-999");
$("input[name='DATA_NASCIMENTO']").mask("99/99/9999");
$("input[name='DESDE']").mask("99/99/9999");
$("input[name='DESDE_FORNECEDOR']").mask("99/99/9999");

$(document).ready(function() {
    // Pega os dados informados na inserção de um novo endereço-pessoa
    var requisitaPost = function() {
        $('#myModalNovoEndereco').modal('hide');
        var idEnderecoEnviado = $('input[name=ID_ENDERECO]').val();
        var idPessoaEnviado = $('input[name=ID_PESSOA]').val();
        var classe = $('input[name=classe]').val();
        var logradouroEnviado = $('input[name=LOGRADOURO]').val();
        var numeroEnviado = $('input[name=NUMERO]').val();
        var complementoEnviado = $('input[name=COMPLEMENTO]').val();
        var cepEnviado = $('input[name=CEP]').val();
        var bairroEnviado = $('input[name=BAIRRO]').val();
        var ufEnviado = $('select[name=UF]').val();
        var idMunicipioEnviado = $('select[name=ID_MUNICIPIO]').val();
        var foneEnviado = $('input[name=FONE]').val();
        var faxEnviado = $('input[name=FAX]').val();
        if ($('#inlineCheckbox1').is(":checked")) {
            var principalEnviado = $('#inlineCheckbox1').val();
        } else {
            var principalEnviado = 0;
        }
        if ($('#inlineCheckbox2').is(":checked")) {
            var entregaEnviado = $('#inlineCheckbox2').val();
        } else {
            var entregaEnviado = 0;
        }
        if ($('#inlineCheckbox3').is(":checked")) {
            var cobrancaEnviado = $('#inlineCheckbox3').val();
        } else {
            var cobrancaEnviado = 0;
        }
        if ($('#inlineCheckbox4').is(":checked")) {
            var correspondenciaEnviado = $('#inlineCheckbox4').val();
        } else {
            var correspondenciaEnviado = 0;
        }
        $.post('/admin/pessoas/endereco',
                {idendereco: idEnderecoEnviado, idpessoa: idPessoaEnviado, classe: classe, logradouro: logradouroEnviado, numero: numeroEnviado, complemento: complementoEnviado,
                    cep: cepEnviado, bairro: bairroEnviado, uf: ufEnviado, idmunicipio: idMunicipioEnviado, fone: foneEnviado,
                    fax: faxEnviado, principal: principalEnviado, entrega: entregaEnviado, cobranca: cobrancaEnviado, correspondencia: correspondenciaEnviado},
        function(data) {
            $('#retorno_endereco').empty();
            $('#retorno_endereco').append(data);
        });

        return false;

    };

    // Pega os dados informados na edição de um novo endereço-pessoa
    var requisitaEdit = function() {
        $('#editarEndereco').modal('hide');
        var idEnderecoEnviado = $('input[name=ID_ENDERECO_EDIT]').val();
        var idPessoaEnviado = $('input[name=ID_PESSOA]').val();
        var logradouroEnviado = $('input[name=LOGRADOURO_EDIT]').val();
        var numeroEnviado = $('input[name=NUMERO_EDIT]').val();
        var complementoEnviado = $('input[name=COMPLEMENTO_EDIT]').val();
        var cepEnviado = $('input[name=CEP_EDIT]').val();
        var bairroEnviado = $('input[name=BAIRRO_EDIT]').val();
        var ufEnviado = $('select[name=UF_EDIT]').val();
        var idMunicipioEnviado = $('select[name=ID_MUNICIPIO_EDIT]').val();
        var foneEnviado = $('input[name=FONE_EDIT]').val();
        var faxEnviado = $('input[name=FAX_EDIT]').val();
        if ($('#inlineCheckbox1_edit').is(":checked")) {
            var principalEnviado = $('#inlineCheckbox1_edit').val();
        } else {
            var principalEnviado = 0;
        }
        if ($('#inlineCheckbox2_edit').is(":checked")) {
            var entregaEnviado = $('#inlineCheckbox2_edit').val();
        } else {
            var entregaEnviado = 0;
        }
        if ($('#inlineCheckbox3_edit').is(":checked")) {
            var cobrancaEnviado = $('#inlineCheckbox3_edit').val();
        } else {
            var cobrancaEnviado = 0;
        }
        if ($('#inlineCheckbox4_edit').is(":checked")) {
            var correspondenciaEnviado = $('#inlineCheckbox4_edit').val();
        } else {
            var correspondenciaEnviado = 0;
        }
        $.post('/admin/pessoas/endereco',
                {idendereco: idEnderecoEnviado, idpessoa: idPessoaEnviado, logradouro: logradouroEnviado, numero: numeroEnviado, complemento: complementoEnviado,
                    cep: cepEnviado, bairro: bairroEnviado, uf: ufEnviado, idmunicipio: idMunicipioEnviado, fone: foneEnviado,
                    fax: faxEnviado, principal: principalEnviado, entrega: entregaEnviado, cobranca: cobrancaEnviado, correspondencia: correspondenciaEnviado},
        function(data) {
            $('#retorno_endereco').empty();
            $('#retorno_endereco').append(data);
        });

        return false;

    };

    $('#salvar_endereco').bind('click', requisitaPost);
    $('#salvar_endereco_edit').bind('click', requisitaEdit);
    //////////////
    //

    //Peenche combo cidades de acordo com o estado
    var requisitaCidade = function() {
        var ufEnviado = $('select[id=uf]').val();
        $('.comboCidadesOpt').remove();
        $.post('/admin/pessoas/combocidade',
                {uf: ufEnviado},
        function(data) {
            $('#combo_cidade').append(data);
        });

        return false;

    };

    $('#uf').bind('change', requisitaCidade);
    $('#uf_edit').live('change', function() {
        var ufEnviado = $('select[id=uf_edit]').val();
        $('.comboCidadesOpt').remove();
        $.post('/admin/pessoas/combocidade',
                {uf: ufEnviado},
        function(data) {
            $('#combo_cidade_edit').append(data);
        });

        return false;

    });


//Pega id para preencher dados da janela edit pessoa
    $('a').live('click', function() {
        var id = $(this).attr("id");
        id = parseInt(id);
        function isNumeric(str) {
            var er = /^[0-9]+$/;
            return (er.test(str));
        }
        if (isNumeric(id)) {

            $.ajax({
                url: '/admin/pessoas/editaendereco/',
                dataType: 'html',
                type: 'POST',
                data: {id: id},
                beforeSend: function() {
                },
                complete: function() {
                },
                success: function(resultado) {
                    $('#modalEditaEndereco').empty();
                    $('#modalEditaEndereco').html(resultado);
                },
                error: function(xhr) {
                    $('#mensagem_erro').html(xhr.status);
                }
            });
        }
    })
/////////////////

/// Controla a apresentação da estrutura 
    var opt1 = $("input[id=optionsRadios1]:checked").val();
    var opt2 = $("input[id=optionsRadios2]:checked").val();
    if (opt1 == "F") {
        $('.fisica').show();
        $('.juridica').hide();
    }
    if (opt2 == "J") {
        $('.juridica').show();
        $('.fisica').hide();
    }
    $("#optionsRadios1").click(function() {
        $('.fisica').show();
        $('.juridica').hide();
    });
    $("#optionsRadios2").click(function() {
        $('.juridica').show();
        $('.fisica').hide();
    });
});

