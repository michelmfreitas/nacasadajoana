<?php
$url = "https://ws.pagseguro.uol.com.br/v2/sessions"; //produção
//$url = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";
$data = "email=crieseuposter@gmail.com&token=843ACE66F983489FA42D993221B78D27"; //produção
//$data = "email=crieseuposter@gmail.com&token=854191901CF44E568067826327063C41";
$curl = curl_init($url);

curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

$xml = curl_exec($curl);
//echo $xml;

if ($xml == 'Unauthorized') {
    //Insira seu código de prevenção a erros
    //echo $xml;
    echo "Erro ao integrar com o PagSeguro.";
    //header('Location: erro.php?tipo=autenticacao');
    exit; //Mantenha essa linha
}

$xml= simplexml_load_string($xml);
//echo $xml; exit;

/*if (count($xml -> error) > 0) {
    //Insira seu código de tratamento de erro, talvez seja útil enviar os códigos de erros.
    echo "Dados inválidos do PagSeguro.";
    //header('Location: erro.php?tipo=dadosInvalidos');
    exit;
}*/
?>

<!DOCTYPE html>
<html lang="en">

<head>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-109849546-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-109849546-1');
</script>



    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" type="image/png" href="assets/img/favicon.png" />
    <title>Crie seu pôster - Na Casa Da Joana</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">

    <script src="assets/js/font-awesome.js"></script>
    <link rel="stylesheet" href="assets/css/font-awesome.css" type="text/css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/estilo.css" type="text/css">

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

    <script src="assets/js/jquery.mask.min.js"></script>
    
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>
    <script src="assets/js/pagamento.js"></script>

    <!--<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script> -->
    <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

    <script type="text/javascript">
        //console.log('<?php echo $xml->id; ?>');
        

        $(document).ready(function(){

            PagSeguroDirectPayment.setSessionId('<?php echo $xml->id; ?>');
            
            var total = parseInt(localStorage.getItem('total')).toFixed(2);
            $(".total-a-pagar span").text("R$ "+total);

            $(".valor-boleto").text(total);

            function carregaDadosPagSeguro(){
                
                $("#loading").show();

                //verificar metodos de pagamentos disponiveis
                PagSeguroDirectPayment.getPaymentMethods({
                    amount: total,
                    success: function(response) {

                    },
                    error: function(response) {
                        alert("Erro na aplicação");
                    },
                    complete: function(response) {
                        $("#loading").hide();
                    }
                });
            
            }

            carregaDadosPagSeguro();
            

            $("#ncartao").change(function(){
                
                if( $("#ncartao").val().length > 6 ){

                    let digitosCartao = $("input#ncartao").val().replace(/[^\d]+/g,'').substring(0,6);
                    
                    //pega bandeira do cartao digitado
                    PagSeguroDirectPayment.getBrand({
                        cardBin: digitosCartao,
                        success: function(response) {
                            $("#bandeira").html(`<img src='https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/42x20/${response.brand.name}.png'>`);
                            $(".bandeira").val(`${response.brand.name}`);
                            verificaParcelamento(total, response.brand.name);
                        },
                        error: function(response) {
                            alert("Bandeira de cartão inválido.");
                            $("input#ncartao").val("");
                            $("input#ncartao").focus();
                        },
                        complete: function(response){
                            
                        }
                    });
                }
            });
            
            $("#parcelas").change(function(){
                var total_compra = $(this).find(":selected").attr('data-item');
                var parcelamento = {
                    n_parcelas: $(this).find(":selected").attr('data-parcelas'),
                    valor: $(this).find(":selected").attr('data-valor')
                }
                localStorage.setItem('parcelamento', JSON.stringify(parcelamento));
                $(".total-a-pagar span").text("R$ "+total_compra);
            });

            //finalizar pedido
            $("#bt-concluir").click(function(){

                var tokenCC;
                $("#loading").show();
                
                var hashComprador = PagSeguroDirectPayment.getSenderHash();                

                if(
                    $("input#ncartao").val() == "" ||
                    $("input#titular_cartao").val() == "" ||
                    $("input#cpf_cartao").val() == "" ||
                    $("#validade_mes").val() == "" ||
                    $("#validade_ano").val() == "" ||
                    $("input#codigo_seguranca").val() == "" ||
                    $(".fatura").val() == "" ||
                    $("#parcelas").val() == "" ||
                    $("#telefone").val() == "" ||
                    $("#data_nascimento").val() == "" ||
                    $(".bandeira").val() == ""
                ){
                    alert("Preencha todos os campos corretamente.");
                    $("#loading").hide();
                    return false;
                }
                var espaco = $("input#titular_cartao").val().split(" ");
                //console.log(espaco, espaco.length);
                /*if(espaco.length <= 1){
                    alert("Insira o seu nome completo.");
                    return false;
                }*/
                
                var param = {
                    cardNumber: $("input#ncartao").val().replace(/[^\d]+/g,''),
                    cvv: $("input#codigo_seguranca").val(),
                    expirationMonth: $("#validade_mes").val(),
                    expirationYear: $("#validade_ano").val(),
                    //brand: 'visa',
                    success: function(response) {
                        tokenCC = response.card.token;
                        
                        var carrinho = JSON.parse(localStorage.getItem('carrinho'));
                        var total = localStorage.getItem('total');
                        total = parseInt(total).toFixed(2);
                        var comprador = JSON.parse(localStorage.getItem('dadosUsuario'));
                        var parcelamento = JSON.parse(localStorage.getItem('parcelamento'));
                        
                        comprador.ddd = $("#telefone").val().replace(/[^\d]+/g,'').slice(0,2);
                        comprador.phone = $("#telefone").val().replace(/[^\d]+/g,'').slice(2,12);
                        comprador.cpf = $("input#cpf_cartao").val().replace(/[^\d]+/g,'');
                        comprador.cep = comprador.cep.replace(/[^\d]+/g,'');
                        comprador.data_nascimento = $("#data_nascimento").val();
                        comprador.bandeira = $(".bandeira").val();

                        var dados_url = {
                            email: 'crieseuposter@gmail.com',
                            token: '843ACE66F983489FA42D993221B78D27', //produção
                            paymentMode: 'default',
                            paymentMethod: 'creditCard',
                            currency: 'BRL',
                            extraAmount: '0.00',
                            itemId1: '0001',
                            itemDescription1: 'Quadros personalizados Na Casa Da Joana',
                            itemAmount1: total,
                            itemQuantity1: 1,
                            reference: 'NCDJ-'+((new Date().getTime() / 1000) * Math.random()/10000),
                            senderName: `${comprador.nome}`,
                            senderCPF: `${comprador.cpf}`,
                            senderAreaCode: `${comprador.ddd}`,
                            senderPhone:`${comprador.phone}`,
                            senderEmail:`${comprador.email}`,
                            senderHash:`${hashComprador}`,
                            shippingAddressStreet:`${comprador.endereco}`,
                            shippingAddressNumber:`${comprador.numero}`,
                            shippingAddressComplement:`${comprador.complemento}`,
                            shippingAddressDistrict:`${comprador.bairro}`,
                            shippingAddressPostalCode:`${comprador.cep}`,
                            shippingAddressCity:`${comprador.cidade}`,
                            shippingAddressState:`${comprador.uf}`,
                            shippingAddressCountry:'BRA',
                            creditCardToken:`${tokenCC}`,
                            installmentQuantity:`${parcelamento.n_parcelas}`,
                            installmentValue:`${parcelamento.valor}`,
                            noInterestInstallmentQuantity:5,
                            creditCardHolderName:`${bandeira}`,
                            creditCardHolderCPF:`${comprador.cpf}`,
                            creditCardHolderBirthDate:`${comprador.data_nascimento}`,
                            creditCardHolderAreaCode:`${comprador.ddd}`,
                            creditCardHolderPhone:`${comprador.phone}`,
                            billingAddressStreet:`${comprador.endereco}`,
                            billingAddressNumber:`${comprador.numero}`,
                            billingAddressComplement:`${comprador.endereco}`,
                            billingAddressDistrict:`${comprador.bairro}`,
                            billingAddressPostalCode:`${comprador.cep}`,
                            billingAddressCity:`${comprador.cidade}`,
                            billingAddressState:`${comprador.uf}`,
                            billingAddressCountry:'BRA'
                        }

                        //console.log(dados_url);

                        $.ajax({
                            url: "api.php",
                            type: 'POST',
                            data: { dados: dados_url },
                            success: function (data) {
                                //console.log("success", data);
                                var retorno = JSON.parse(data);
                                //console.log(retorno, retorno.error);
                                if(retorno.error){
                                    var erro = retorno.error['code'];
                                    var msgErro = retorno.error['message'];
                                    alert(`Erro PagSeguro (${erro}): ${msgErro}`);
                                    return false;
                                }else{
                                    processaCompra(data); 
                                }
                            },
                            error: function(erro){
                                alert("Cannot get data");
                                //console.log(erro);
                            },
                            complete: function(data){
                                //console.log("complete", data);  
                            }
                        });

                        //callPHP(url);

                        //$.post("api.php", url);

                                
                    },
                    error: function(response) {
                        //console.log("TOKEN error", response);
                        alert("Dados do cartão de crédito inválidos.");
                        $("#loading").hide();
                        return false;

                        
                    },
                    complete: function(response) {
                        //$("#loading").hide();
                    }
                }
                

                //parâmetro opcional para qualquer chamada
                if($(".bandeira").val() != '') {
                    param.brand = $(".bandeira").val();
                }

                //console.log($(".bandeira").val(), param);

                PagSeguroDirectPayment.createCardToken(param);
            });

            $(".btn-pagar-boleto").click(function(){
                
                $("#loading").show();
                
                var hashComprador = PagSeguroDirectPayment.getSenderHash();

                var carrinho = JSON.parse(localStorage.getItem('carrinho'));
                var total = localStorage.getItem('total');
                total = parseInt(total).toFixed(2);
                var comprador = JSON.parse(localStorage.getItem('dadosUsuario'));
                        
                comprador.ddd = comprador.telefone.replace(/[^\d]+/g,'').slice(0,2);
                comprador.phone = comprador.telefone.replace(/[^\d]+/g,'').slice(2,12);
                comprador.cpf = comprador.cpf.replace(/[^\d]+/g,'');
                comprador.cep = comprador.cep.replace(/[^\d]+/g,'');

                //console.log(comprador);

                var dados_url = {
                    email: 'crieseuposter@gmail.com',
                    token: '843ACE66F983489FA42D993221B78D27', //prod
                    //token: '854191901CF44E568067826327063C41',
                    paymentMode: 'default',
                    paymentMethod: 'boleto',
                    currency: 'BRL',
                    extraAmount: '0.00',
                    itemId1: '0001',
                    itemDescription1: 'Quadros personalizados Na Casa Da Joana',
                    itemAmount1: total,
                    itemQuantity1: 1,
                    reference: 'NCDJ-'+((new Date().getTime() / 1000) * Math.random()/10000),
                    senderName: `${comprador.nome}`,
                    senderCPF: `${comprador.cpf}`,
                    senderAreaCode: `${comprador.ddd}`,
                    senderPhone:`${comprador.phone}`,
                    senderEmail:`${comprador.email}`,
                    //senderEmail:`c47323087046186728590@sandbox.pagseguro.com.br`,
                    senderHash:`${hashComprador}`,
                    shippingAddressStreet:`${comprador.endereco}`,
                    shippingAddressNumber:`${comprador.numero}`,
                    shippingAddressComplement:`${comprador.complemento}`,
                    shippingAddressDistrict:`${comprador.bairro}`,
                    shippingAddressPostalCode:`${comprador.cep}`,
                    shippingAddressCity:`${comprador.cidade}`,
                    shippingAddressState:`${comprador.uf}`,
                    shippingAddressCountry:'BRA'
                }

                //console.log(dados_url);

                $.ajax({
                    url: "api.php",
                    type: 'POST',
                    data: { dados: dados_url },
                    success: function (data) {
                        dados = JSON.parse(data);
                        //console.log(dados.paymentLink);
                        window.open(dados.paymentLink, "_blank");                        
                        processaCompra(data);
                    },
                    error: function(erro){
                        alert("Cannot get data");
                        //console.log(erro);
                    },
                    complete: function(data) {
                        //$("#loading").hide();
                    }
                });                   

            });


            $(".btn-pagar-deposito-em-conta").click(function(){
                
                $("#loading").show();
                
                var hashComprador = PagSeguroDirectPayment.getSenderHash();

                var carrinho = JSON.parse(localStorage.getItem('carrinho'));
                var total = localStorage.getItem('total');
                total = parseInt(total).toFixed(2);
                var comprador = JSON.parse(localStorage.getItem('dadosUsuario'));
                        
                comprador.ddd = comprador.telefone.replace(/[^\d]+/g,'').slice(0,2);
                comprador.phone = comprador.telefone.replace(/[^\d]+/g,'').slice(2,12);
                comprador.cpf = comprador.cpf.replace(/[^\d]+/g,'');
                comprador.cep = comprador.cep.replace(/[^\d]+/g,'');

                //console.log(comprador);

                var dados_url = {
                    email: 'crieseuposter@gmail.com',
                    token: '843ACE66F983489FA42D993221B78D27',
                    //token: '854191901CF44E568067826327063C41',
                    paymentMode: 'default',
                    paymentMethod: 'online_debit',
                    bankName: 'itau',
                    currency: 'BRL',
                    extraAmount: '0.00',
                    itemId1: '0001',
                    itemDescription1: 'Quadros personalizados Na Casa Da Joana',
                    itemAmount1: total,
                    itemQuantity1: 1,
                    reference: 'NCDJ-'+((new Date().getTime() / 1000) * Math.random()/10000),
                    senderName: `${comprador.nome}`,
                    senderCPF: `${comprador.cpf}`,
                    senderAreaCode: `${comprador.ddd}`,
                    senderPhone:`${comprador.phone}`,
                    senderEmail:`${comprador.email}`,
                    //senderEmail:`c47323087046186728590@sandbox.pagseguro.com.br`,
                    senderHash:`${hashComprador}`,
                    shippingAddressStreet:`${comprador.endereco}`,
                    shippingAddressNumber:`${comprador.numero}`,
                    shippingAddressComplement:`${comprador.complemento}`,
                    shippingAddressDistrict:`${comprador.bairro}`,
                    shippingAddressPostalCode:`${comprador.cep}`,
                    shippingAddressCity:`${comprador.cidade}`,
                    shippingAddressState:`${comprador.uf}`,
                    shippingAddressCountry:'BRA'
                }

                //console.log(dados_url);

                $.ajax({
                    url: "api.php",
                    type: 'POST',
                    data: { dados: dados_url },
                    success: function (data) {
                        //console.log(data);
                        dados = JSON.parse(data);
                        //console.log(dados);
                        window.open(dados.paymentLink, "_blank");
                        //geraPDF(dados_url.reference);
                        processaCompra(data);
                        
                    },
                    error: function(erro){
                        alert("Cannot get data");
                        //console.log(erro);
                    },
                    complete: function(data) {
                        //$("#loading").hide();
                        /*dados = JSON.parse(data);
                        console.log('complete...');
                        processaCompra(data);
                        window.open(dados.paymentLink);*/
                    }
                });
            });


            function geraPDF(pedido){
                var img = localStorage.getItem('imagem');
                //var cache_width = $('#quadro').width(); //Criado um cache do CSS
                //var a3 = [ 297, 420]; // Widht e Height de uma folha a3

                var doc = new jsPDF({
                    orientation: 'p',
                    unit: 'mm',
                    format: 'a5'
                });
                doc.addImage(img, 'PNG', 0, 0, 297, 420);
                doc.save(`${pedido}.pdf`);
                //var pdf = doc.output('datauristring');
                //console.log(pdf);
                //Retorna ao CSS normal
                //$('#quadro').width(cache_width);

            }



            function verificaParcelamento(valor, bandeira){
                PagSeguroDirectPayment.getInstallments({
                    amount: valor,
                    brand: bandeira,
                    maxInstallmentNoInterest: 5,
                    success: function(response) {
                        let parcelas = response.installments[bandeira];
                        let options = "<option>Selecione -></option>";
                        for(var i=0; i < parcelas.length; i++){
                            let prestacao = (parcelas[i].installmentAmount).toFixed(2);
                            let total = (parcelas[i].totalAmount).toFixed(2);
                            let juros = parcelas[i].interestFree == true ? "sem" : "com";
                            
                            if( parcelas[i].quantity == 1 ){
                                var selected = "selected";
                                var parcelamento = {
                                    n_parcelas: 1,
                                    valor: total
                                };            
                                localStorage.setItem('parcelamento', JSON.stringify(parcelamento));
                            }else{
                                var selected = '';
                            }

                            options += `<option value='${parcelas[i].quantity}x de R$ ${prestacao} = R$ ${total} ${juros} juros' data-item='${total}' data-parcelas="${parcelas[i].quantity}" data-valor="${prestacao}" ${selected}>${parcelas[i].quantity}x de R$ ${prestacao} = R$ ${total} ${juros} juros</option>`;
                        }
                        $("#parcelas").html(options);
                    },
                    error: function(response) {
                        //console.log(response);
                    },
                    complete: function(response) {
                        $("#parcelas").removeAttr("disabled");
                        $("#parcelas").css('color', 'orangered');
                        $("#parcelas").css('background-color', '#FFF');
                        $("#parcelas option:first").attr('selected', 'selected');
                    }
                });
            }



            function processaCompra(dados){
                let dadosCompra = JSON.parse(dados);
                $.ajax({
                    url: "enviaEmail.php",
                    type: 'POST',
                    data: { dadosUsuario: localStorage.getItem('dadosUsuario'), dados: dadosCompra, carrinho: localStorage.getItem('carrinho'), imagem: localStorage.getItem('imagem') },
                    success: function (data) {
                        let res = JSON.parse(data);
                        if(res.error == 0 && res.code == 1){
                            window.location.href='obrigado.html';
                        }else{
                            alert('Erro ao processar o pedido pelo PagSeguro.');
                        }
                        //$("#loading").hide();
                    },
                    error: function(erro){
                        alert("Cannot get data");
                        //console.log(erro);
                    }
                });
            }

        });
        
    </script>

</head>

<body>

    <div id="loading"><img src="assets/img/Spinner.gif" style="width:64px; height: auto;"></div>
    
    <div class="container">
        <div class="row">
            <div class="col-sm-2 logo">
                <img src="assets/img/logo.jpg">
            </div>
        </div>
    </div>

    <div class="container pagamento">

        <div class="row">

            <div class="col-sm-9">
                <h3>Pagamento</h3>
                <h6>Nossos pagamentos são processados com segurança pelo PagSeguro.</h6>
            </div>

            <div class="col-sm-3 pagseguro">
                <img src="assets/img/bt-pagseguro.jpg" alt="PagSeguro">
            </div>

        </div>

        <!-- Bootstrap CSS -->
        <!-- jQuery first, then Bootstrap JS. -->
        <!-- Nav tabs -->

        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#cartao" role="tab" data-toggle="tab">Cartão de Crédito</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#boleto" role="tab" data-toggle="tab">Boleto Bancário</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#debito" role="tab" data-toggle="tab">Débito Online</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">

            <div role="tabpanel" class="tab-pane fade-in active" id="cartao">
                
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="ncartao">Número do cartão:</label>
                                <input type="text" class="form-control" id="ncartao" value=""><span id='bandeira'></span>
                                <input type="hidden" class="form-control bandeira" value="">
                            </div>
                            <div class="form-group">
                                <label for="titular_cartao">Nome do titular do cartão:</label>
                                <input type="text" class="form-control" id="titular_cartao" value="">
                            </div>
                            <div class="form-group">
                                <label for="cpf_cartao">CPF do titular do cartão:</label>
                                <input type="text" class="form-control" id="cpf_cartao" value="">
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="data_nascimento">Nascimento do titular:</label>
                                        <input type="text" class="form-control" id="data_nascimento" value=''>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="telefone">Telefone do titular:</label>
                                        <input type="text" class="form-control" id="telefone" value=''>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 ml-auto">
                            <div class='row'>
                                <div class="form-group col-sm-7 validade">
                                    <label>Validade</label>
                                    <span>Mês:</span>
                                    <select name="validade_mes" id="validade_mes">
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                    <span>Ano:</span>
                                    <select name="validade_ano" id="validade_ano">
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-5">
                                    <label for="codigo_seguranca">Código de segurança:</label>
                                    <input type="text" class="form-control" id="codigo_seguranca" value="">
                                </div>
                            </div>
                            
                            
                            <!--<div class="form-group radio-group">
                                <label for="fatura">Fatura do cartão</label><br>
                                <input type="radio" name="fatura" class="fatura" checked value="Está no mesmo endereço" /> Está no mesmo endereço de entrega do pedido.<br>
                                <input type="radio" name="fatura" class="fatura" value="Está em outro endereço" /> Está em outro endereço.
                            </div>-->

                            <div class="form-group">
                                <label for="parcelas">Parcelas</label>
                                <select name="parcelas" id="parcelas" disabled>
                                    <option>Digite o número do cartão</option>
                                </select>
                            </div>

                            <br>

                            <div class="total-a-pagar">
                                Total a pagar: <span>R$ valor</span>
                            </div>

                            <br>

                            <div class="row">
                                <div class="col-sm-6">
                                    <button class="btn btn-default btn-limpar">Limpar</button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-success" id="bt-concluir">ENVIAR PEDIDO</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                
            </div>

            <div role="tabpanel" class="tab-pane fade" id="boleto">
                <p>O Boleto Bancário poderá ser pago em qualquer agência ou internet banking.</p>
                
                <p>Após a confirmação automática do pagamento, nós daremos início à produção do seu pôster.</p>
                <br>
                <div class='quadro-boleto'>
                    Valor: R$ <span class='valor-boleto'></span>
                    <button class='btn btn-success btn-pagar-boleto'>Gerar boleto</button>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane fade" id="debito">
                <p>As instruções para pagamento serão apresentadas na tela seguinte, após o envio do pedido.</p>
                <br>
                <div class='quadro-boleto'>
                    Valor: R$ <span class='valor-boleto'></span>
                    <button class='btn btn-success btn-pagar-deposito-em-conta'>CONTINUAR</button>
                </div>
                <br>
            </div>

        </div>



    </div>

    <br><br><br>

    <div id="teste">
        <img src="assets/img/logo.jpg" alt="Crie o seu poster" />
        <br><br>
        <p>Oi! Por enquanto você só pode criar o seu pôster personalizado usando um computador.</p>
        <p>Acesse de um computador e crie quadros incríveis:<br>crieseuposter.nacasadajoana.com.br</p>
        <br><br><a href='http://www.nacasadajoana.com.br' class='btn btn-danger'>Vá para o nosso site.</a>
    </div>

    
</body>

</html>
