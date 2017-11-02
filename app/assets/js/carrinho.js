$(document).ready(function() {

    jQuery.support.cors = true;

    $('#cep').mask('00000-000');

    let items_carrinho = JSON.parse(localStorage.getItem('carrinho'));
    //console.log(items_carrinho);
    var linhas;
    var total_carrinho = 0;

    for (var i = 0; i < items_carrinho.length; i++) {
        let valor = (items_carrinho[i].item == "Sem Moldura") ? 62 : 150;
        total = items_carrinho[i].quantidade * valor;
        linhas += `<tr class="produto" data-linha="${i}">
                            <td>
                                <img class="imgData" />
                                <div class="col-produto">
                                    <span class="titulo-produto">Meu Pôster Personalizado</span>
                                    <span class="descricao-produto">Tamanho A3 (30x42cm) / Moldura: ${items_carrinho[i].item}</span>
                                    <a href="javascript:" class="remover-produto" data-item="${i}">Remover</a>
                                </div>
                            </td>
    
                            <td>
                                <input type="number" name="quantidade" class="quantidade" value="${items_carrinho[i].quantidade}" data-description="${i}" />
                            </td>
    
                            <td>
                                <span class="valor-unitario">R$ ${valor},00</span>
                            </td>
    
                            <td>
                                <span class="valor-total">R$ ${total},00</span>
                            </td>
                        </tr>`
        total_carrinho += total;
    }


    $("#tbl-carrinho").append(linhas);
    $("#tbl-carrinho").append(`<tr class='total-carrinho'><td colspan='4'><strong style='color:orangered;'>Total:</strong> R$ <span>${total_carrinho}</span>,00</td></tr>`);

    $(":input[type='number']").bind('keyup mouseup', function() {

        //limpa cep e calculos
        $("#cep").val('');
        $(".preco_pac").text("0.00");
        $(".preco_sedex").text("0.00");
        $(".valor-frete").text("0.00");
        $(".total-pedido").text("0.00");
        $("input[type=radio]").prop('checked', false);

        //pega quantidade e linha de referencia
        let quantidade = parseInt($(this).val());
        let item = parseInt($(this).attr('data-description'));

        //pega valor unitario e multiplica pelo valor
        let valor = $(".valor-unitario").eq(item).text();
        valor = parseInt(valor.replace(/[^\d]+/g, ''));

        //insere valor total
        let total = parseInt(quantidade * (valor / 100));
        $(".valor-total").eq(item).html(`R$ ${total},00`);

        //soma total de todo carrinho
        let totais = $(".valor-total");
        //console.log(totais);

        var valor_total = 0;
        for (let i = 0; i < totais.length; i++) {
            //array_total.push(totais.eq(i).text().replace(/[^\d]+/g, '') / 100);
            valor_total += totais.eq(i).text().replace(/[^\d]+/g, '') / 100;
        }
        console.log(valor_total);
        $(".total-carrinho td span").html(valor_total);
    });

    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            //Atualiza os campos com os valores.
            document.getElementById('rua').value = (conteudo.logradouro);
            document.getElementById('bairro').value = (conteudo.bairro);
            document.getElementById('cidade').value = (conteudo.localidade);
            document.getElementById('uf').value = (conteudo.uf);
            document.getElementById('ibge').value = (conteudo.ibge);
        } //end if.
        else {
            //CEP não Encontrado.
            limpa_formulário_cep();
            alert("CEP não encontrado.");
        }
    }

    function stringToNumber(currency) {
        return Number(currency.replace(/[^0-9\.-]+/g, ""));
    }

    function NumberConvertToDecimal(number) {
        if (number == 0) {
            return '0.00';
        }
        number = parseFloat(number);
        number = number.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1");
        number = number.split('.').join('*').split('*').join('.');
        return number;
    }

    function pesquisacep(valor, quantidade) {

        $("#loading").show();

        var dados = {};

        //Nova variável "cep" somente com dígitos.
        var cep = valor.replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if (validacep.test(cep)) {

                var consulta = $.ajax({
                        url: `http://viacep.com.br/ws/${cep}/json/`,
                        method: "GET"
                    })
                    .then((res) => {
                        localStorage.setItem('enderecoUsuario', JSON.stringify(res));

                    }).catch((erro) => {
                        console.log("Erro", erro);
                    });

                var sedex = "04014";
                var pac = "04510";

                $.ajax({
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    url: `http://correios-server.herokuapp.com/frete/prazo?nCdServico=${sedex},${pac}&sCepOrigem=29050224&sCepDestino=${cep}&nVlPeso=1&nCdFormato=1&nVlComprimento=35&nVlAltura=6&nVlLargura=25&nVlDiametro=25&nVlValorDeclarado=0`,
                    dataType: "text",
                    success: function(res) {

                        let result = JSON.parse(res).response;
                        //console.log(result, result[0].Erro);

                        if (result[0].Erro == 0) {
                            dados.sedex = stringToNumber(result[0].Valor) / 100;
                            dados.prazo_sedex = parseInt(result[0].PrazoEntrega);
                            dados.pac = stringToNumber(result[1].Valor) / 100;
                            dados.prazo_pac = parseInt(result[1].PrazoEntrega);

                            //console.log((dados.pac).toFixed(2) + " - " + dados.prazo_pac + " dias");

                            $(".preco_pac").text((dados.pac).toFixed(2));
                            $(".preco_sedex").text((dados.sedex).toFixed(2));

                            localStorage.setItem('frete', JSON.stringify(dados));

                            $(".escolha-frete").show();

                            $("#loading").hide();

                        } else {
                            alert(result[0].MsgErro);
                            $("#cep").val('');
                        }
                    },

                });




            } //end if.
            else {
                //cep é inválido.
                $("#cep").val("");
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            $("#cep").val("");
        }
    };

    function correios(resultado) {
        console.log("to aqui");
    }

    $("#calcula_frete").click(function() {
        pesquisacep($("#cep").val());
    });

    $("input[type='radio']").click(function() {
        let frete = JSON.parse(localStorage.getItem('frete'));
        //console.log($(this).val(), frete);
        frete.selecionado = $(this).val();
        localStorage.setItem('frete', JSON.stringify(frete));

        let total_carrinho = parseFloat($(".total-carrinho td span").text());
        //console.log(frete);
        if ($(this).val() == 'pac') {
            let soma = frete.pac + total_carrinho;
            $(".total-pedido").text(soma.toFixed(2));
            $(".valor-frete").text((frete.pac).toFixed(2));
            localStorage.setItem('total', soma);
        } else {
            let soma = frete.sedex + total_carrinho;
            $(".total-pedido").text(soma.toFixed(2));
            $(".valor-frete").text((frete.sedex).toFixed(2));
            localStorage.setItem('total', soma);
        }
        $(".total-frete").show();
        $(".carrinho #bt-finalizar").show();


    });

    let imagem = localStorage.getItem('imagem');
    $(".imgData").attr("src", imagem);

    $("#bt-finalizar").click(function() {
        let itens_carrinho = localStorage.getItem('carrinho');
        if (!itens_carrinho) {
            alert("Não há itens no carrinho.");
            return false;
        }

        let frete = localStorage.getItem('frete');
        if (!frete) {
            alert("Insira o CEP e faça o cálculo do frete.");
            return false;
        }
        let imagem = localStorage.getItem('imagem');
        if (!imagem) {
            alert("Erro ao gerar a imagem. Comece novamente.");
            return false;
        }

        let moldura = localStorage.getItem('moldura');
        if (moldura < 1) {
            alert("Não há moldura selecionada.");
            return false;
        }

        let total = localStorage.getItem('total');
        if (total.length < 1) {
            alert("Faça o cálculo do frete.");
            return false;
        }
        window.location.href = "cadastro.html";
    });

});