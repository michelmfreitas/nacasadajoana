$(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

    $("#telefone").mask("(00) 0000-00009");
    $("#cep").mask('00000-000');
    $("#cpf").mask('000.000.000-00');

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
        //$("#uf").val(dados.uf);
        let opt = $("#uf option");
        for (var i = 0; i < opt.length; i++) {
            if (dados.uf == opt[i].value) {
                $(`#uf option[value=${dados.uf}]`).attr("selected", "selected");
            }
        }
    }


    $(".btn-voltar").click(function() {
        window.location.href = 'carrinho.html';
    });


    $("#bt-cadastrar").click(function() {

        if (
            $("#nome").val() == "",
            $("#email").val() == "",
            $("#telefone").val() == "",
            $("#endereco").val() == "",
            $("#numero").val() == "",
            $("#destinatario").val() == "",
            $("#bairro").val() == "",
            $("#cidade").val() == "",
            $("#uf").val() == "",
            $("#pais").val() == "",
            $("#cpf").val() == ""
        ) {
            alert("Há campos não preenchidos no formulário.");
            return false;
        }
        if (validarCPF($("#cpf").val()) == false) {
            alert("CPF Inválido. Confira os dados. =)");
            return;
        }
        var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
        if (!reg.test($("#email").val())) {
            alert("E-mail Inválido. Confira os dados. =)")
            return;
        }


        var cadastro = {
            "nome": $("#nome").val(),
            "email": $("#email").val(),
            //"sexo": $(".sexo:checked").val(),
            "telefone": $("#telefone").val(),
            "cpf": $("#cpf").val(),
            "nome_embalagem": $("#nome_embalagem").val(),
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
        window.location.href = 'pagamento.php';

    });




});

function validarCPF(cpf) {
    var soma = 0;
    var resto;
    var inputCPF = cpf.replace(/[^\d]+/g, '');

    if (inputCPF == '00000000000') return false;
    for (i = 1; i <= 9; i++) soma = soma + parseInt(inputCPF.substring(i - 1, i)) * (11 - i);
    resto = (soma * 10) % 11;

    if ((resto == 10) || (resto == 11)) resto = 0;
    if (resto != parseInt(inputCPF.substring(9, 10))) return false;

    soma = 0;
    for (i = 1; i <= 10; i++) soma = soma + parseInt(inputCPF.substring(i - 1, i)) * (12 - i);
    resto = (soma * 10) % 11;

    if ((resto == 10) || (resto == 11)) resto = 0;
    if (resto != parseInt(inputCPF.substring(10, 11))) return false;
    return true;
}

function validaEmail(email) {
    var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
    if (reg.test(email)) {
        return true;
    } else {
        return false;
    }
}