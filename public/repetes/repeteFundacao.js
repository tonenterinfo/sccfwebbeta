$(function() {
    $("#addFundacao").click(function() {
        var input = '<div class="itenFundacao">';
        input += '<table width="100%">';
        input += '<tr>';
        input += '<td colspan="2"></td>';
        input += '<td colspan="3" bgColor=#f4f4f4 align="center"><b>Barras</b></td>';
        input += '<td colspan="3" align="center"><b>Quantitativo</b></td>';
        input += '</tr>';
        input += '<tr>';
        input += '</td>';
        input += '<td align="center">';
        input += 'Repetir <i>n</i> vezes<br>';
        input += '<input type="text" name="repetirPecaFundacao[]" class="input" >';
        input += '</td>';
        input += '<td align="center" >';
        input += 'Nome da peça<br>';
        input += '<input type="text" name="nomePecaFundacao[]" class="input" >';
        input += '<td align="center" bgColor=#f4f4f4>';
        input += 'Quantidade<br>';
        input += '<input type="text" name="qtdPecaFundacao[]" class="input" >';
        input += '</td>';
        input += '<td align="center" bgColor=#f4f4f4>';
        input += 'Comprimento<br>';
        input += '<input type="text" name="comprimentoPecaFundacao[]" class="input" >';
        input += '</td>';
        input += '<td align="center" bgColor=#f4f4f4>Ferro (mm)<br>';
        input += '<select class="input" tipoFerroPecaFundacao[]>';
        input += '<?php foreach($this->ferros as $valFerro): ?>';
        input += '<option value="<?php echo $valFerro->id; ?>"><?php echo $valFerro->tipo. " - ". $valFerro->diametro_polegada; ?></option>';
        input += '<?php endforeach; ?>';
        input += '</select>';
        input += '</td>';
        input += '<td align="center">';
        input += '&nbsp;&nbsp;&nbsp;Kg/m&nbsp;&nbsp;&nbsp;<br>';
        input += '<b>00,00</b>';
        input += '</td>';
        input += '<td align="center">';
        input += '&nbsp;&nbsp;Peso Total(Kg)&nbsp;&nbsp;<br>';
        input += '<b>00,00</b>';
        input += '</td>';
        input += '<td align="center">';
        input += '&nbsp;&nbsp;Comp. Total(m)&nbsp;&nbsp;<br>';
        input += '<b>00,00</b>';
        input += '</td>';
        input += '</tr>';
        input += '</table>';
                                
                                
        input += '</div>';

        $("#itensFundacao").append(input);
        return false;
    });
                            
});

