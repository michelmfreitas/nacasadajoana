$(document).ready(function() {

    $("#telefone").mask("(00) 0000-00009");
    $("#cep").mask('00000-000');

    var enderecoUsuario = localStorage.getItem('enderecoUsuario');
    dados = JSON.parse(enderecoUsuario);
    if (dados.cep) {
        $("#cep").val(dados.cep);
    }
    if (dados.logradouro) {
        $("#endereco").val(dados.logradouro);
    }
    if (dados.bairro) {
        $("#bairro").val(dados.bairro);
    }
    if (dados.localidade) {
        $("#cidade").val(dados.localidade);
    }
    if (dados.uf) {
        $("#uf").val(dados.uf);
    }




    $("#bt-cadastrar").click(function() {

        if (
            $("#nome").val() == "",
            $("#email").val() == "",
            $(".sexo").val() == undefined,
            $("#telefone").val() == "",
            $("#endereco").val() == "",
            $("#numero").val() == "",
            $("#destinatario").val() == "",
            $("#bairro").val() == "",
            $("#cidade").val() == "",
            $("#uf").val() == "",
            $("#pais").val() == ""
        ) {
            alert("Há campos não preenchidos no formulário.");
        }

        var cadastro = {
            "nome": $("#nome").val(),
            "email": $("#email").val(),
            "sexo": $(".sexo:checked").val(),
            "telefone": $("#telefone").val(),
            "destinatario": $("#destinatario").val(),
            "endereco": $("#endereco").val(),
            "numero": $("#numero").val(),
            "complemento": $("#complemento").val(),
            "cidade": $("#cidade").val(),
            "cep": $("#cep").val(),
            "uf": $("#uf").val(),
            "pais": $("#pais").val(),
            "bairro": $("#bairro").val(),
            "ficou_sabendo": $("#apresentacao").val(),
        }

        //console.log(cadastro);
        localStorage.setItem('dadosUsuario', JSON.stringify(cadastro));

        window.location.href = 'pagamento.html';
    });


});