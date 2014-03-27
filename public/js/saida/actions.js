$(document).ready(function() {
    $('#desconto').live('blur', function() {
        var produto = $('#prod').val();
        var quebra  = produto.split("-");
        var quantidade = $('#quantidade').val();
        quantidade = quantidade.replace(',', '.');
        var desconto = $('#desconto').val();
        desconto = desconto.replace(',', '.');
        if(desconto > 0) {
            var total = quebra[1] * quantidade * (1-(desconto/100));
        } else {
            var total = quebra[1] * quantidade;
        }
        $('input[id=total]').val(total);
    });
    
    var postDadosCarrinho = function() {
        var produto = $('#prod').val();
        var quebra  = produto.split("-");
        var quantidade = $('#quantidade').val();
        quantidade = quantidade.replace(',', '.');
        var desconto = $('#desconto').val();
        desconto = desconto.replace(',', '.');
        var total = $('#total').val();
        $.post('/admin/saidaproduto/carrinho',
            {idProduto: quebra[0], valorUnitario: quebra[1], quantidade: quantidade, desconto: desconto, total: total},
            function(data) {
                $('#retorno_produtos_carrinho').empty();
                $('#retorno_produtos_carrinho').append(data);
            }
        );
    };
    
    var reloadDadosCarrinho = function() {
        $.post('/admin/saidaproduto/carrinho',
            {},
            function(data) {
                $('#retorno_produtos_carrinho').empty();
                $('#retorno_produtos_carrinho').append(data);
            }
        );
    };
    
    var updateDadosCarrinho = function() {
        var coluna = $(this).attr("name");
        var valor =  $(this).val();
        var id = $(this).attr("id");
        var action = 2;
        $.post('/admin/saidaproduto/carrinho',
            {action: action, coluna: coluna, valor: valor, id: id},
            function(data) {
                $('#retorno_produtos_carrinho').empty();
                $('#retorno_produtos_carrinho').append(data);
            }
        );
    };
    
    var updateTotalDesconto = function() {
        var desconto =  $(this).val();
        var total = $('#valor_total').val();
        var novoTotal = total * (1-(desconto/100));
        $('#valor_total').val(novoTotal);
    };
    
    var updateTotalValor = function() {
        var valor =  $(this).val();
        var total = $('#valor_total').val();
        var novoTotal = total - valor;
        $('#valor_total').val(novoTotal);
    };

    var cadastrar = function() {
        var tipo = "";
        //Executa Loop entre todas as Radio buttons com o name de valor
        $('input:radio[name=TIPO]').each(function() {
            //Verifica qual está selecionado
            if ($(this).is(':checked'))
                tipo = $(this).val();
        })
        $.post('/admin/saidaproduto/cadastrasaida',
        {tipo: tipo},
        function(data) {
            window.location.href('http://www.google.com');
        }
        );
    };
    $('#adicionar').bind('click', postDadosCarrinho);
    $('#recarregar').bind('click', reloadDadosCarrinho);
    $('.saida-produto-carrinho').live('blur', updateDadosCarrinho);
    $('#taxa_desconto').live('blur', updateTotalDesconto);
    $('#valor_desconto').live('blur', updateTotalValor);
    $('#cadastrar').live('click', cadastrar);
});

