$(document).ready(function() {

    alvoAtivo = 1;

    function atualizarBoxes() {
        // Prepare extra handles
        var nw = $("<div>", {
            class: "ui-rotatable-handle"
        });
        var ne = nw.clone();
        var se = nw.clone();
        // Assign Rotatable
        $('.box').resizable().rotatable();
        // Assign coordinate classes to handles
        $('.box div.ui-rotatable-handle').addClass("ui-rotatable-handle-sw");
        nw.addClass("ui-rotatable-handle-nw");
        ne.addClass("ui-rotatable-handle-ne");
        se.addClass("ui-rotatable-handle-se");
        // Assign handles to box
        $(".box").append(nw, ne, se);
        // Assigning bindings for rotation event
        $(".box div[class*='ui-rotatable-handle-']").bind("mousedown", function(e) {
            $('.box').rotatable("instance").startRotate(e);
        });
    }

    $(".color-item").click(function() {
        let cor = $(this).attr("data-color");
        $("#quadro").removeAttr('class');
        $("#quadro").addClass(cor);
    });

    $(".bg-item").click(function() {
        let bg = $(this).attr("data-color");
        $("#quadro").removeAttr('class');
        $("#quadro").addClass(bg);
    });

    $(".text-item").click(function() {
        console.log(alvoAtivo);
        let text = $(this).attr("data-color");
        let cor = $("." + text).css('background-color');
        $(".box[data-target='" + alvoAtivo + "']").css("color", cor);
    });

    $(".align-left").click(function() {
        $(".box[data-target='" + alvoAtivo + "']").css('text-align', "left");
    });

    $(".align-center").click(function() {
        $(".box[data-target='" + alvoAtivo + "']").css('text-align', "center");
    });

    $(".align-right").click(function() {
        $(".box[data-target='" + alvoAtivo + "']").css('text-align', "right");
    });


    $("#bt-enviar").click(function() {
        if ($("input[type=checkbox]:checked").length != 1) {
            alert('Você deve aceitar os termos e regulamentos da compra para continuar.');
            return false;
        } else {
            let quadro = $("#conteudo-quadro").html();

            //transforma a DIV #conteudo-quadro em uma imagem.
            html2canvas($('#conteudo-quadro'), {
                onrendered: function(canvas) {
                    var imgData = canvas.toDataURL('image/png');
                    /*var pdf = new jsPDF('p', 'mm');
                    pdf.addImage(imgData, 'PNG', 10, 10);
                    pdf.save('test.pdf');*/

                    console.log(imgData);
                    $("#imagem-final").html("<img src='" + imgData + "'>");
                }
            });

        }
    });

    $(".btn-limpar").on("click", function() {
        $("#quadro").removeAttr('class');
        $(".content").removeAttr('style');
        $(".content").html('');
    });

    $("#fonte").on("change", function() {
        let fontes = [
            "font-nosifer",
            "font-anton",
            "font-lobster"
        ];
        fontes.forEach(function(element) {
            $(".box[data-target='" + alvoAtivo + "']").removeClass(element);
        }, this);
        $(".box[data-target='" + alvoAtivo + "']").addClass($(this).val());
    });

    $("#font-size").on("change", function() {
        let tamanho = [
            "14px", "18px", "24px", "32px", "42px", "56px"
        ];
        console.log($(this).val(), alvoAtivo);
        $(".box[data-target='" + alvoAtivo + "']").css("font-size", tamanho[$(this).val()]);
        $(".box[data-target='" + alvoAtivo + "']").css("line-height", tamanho[$(this).val()]);
    });

    $(".content").on("dblclick", ".box", function() {
        var obj = $(this).attr('data-target');
        alvoAtivo = alteraAlvoAtivo(obj);
        $("#novo-texto").val($(".box[data-target='" + obj + "'] span").html());
        $('#exampleModal').modal('show');
    });

    $('#bt-addTexto').on("click", function() {
        let novotexto = $("#novo-texto").val();
        novotexto = novotexto.replace(/\n\r?/g, '<br />');

        $(".box").css('border-color', "#FFF");
        $(this).css('border-color', "#999");

        if ($(".box[data-target=" + alvoAtivo + "]").length === 0) {
            let novoBox = "<div class='box' data-target='" + alvoAtivo + "'>" + novotexto + "</div>";
            $(".content").append(novoBox);
            $(".box[data-target='" + alvoAtivo + "']").draggable({
                cancel: ".ui-rotatable-handle",
                containment: "parent"
            });
        } else {
            $(".box[data-target=" + alvoAtivo + "]").html(novotexto);
        }
        $('#exampleModal').modal('hide');
        $("#novo-texto").val('');
    });

    $(".content").on("click", ".box", function() {
        var obj = $(this).attr('data-target');
        alvoAtivo = obj;
        $(".box").css('border-color', "#FFF");
        $(this).css('border-color', "#999");
        console.log("Alvo ativo: " + alvoAtivo);
        console.log("ObJ: " + obj);
        $(this).resizable().rotatable();
    });

    $("#addButton").on("click", function() {
        let novoBox = $(".box").length + 1;
        alvoAtivo++;
        $('#exampleModal').modal('show');
    });

    function alteraAlvoAtivo(alvoAtual) {
        return alvoAtivo = alvoAtual++;
    }

    function inserirNovoBox(texto) {
        var lastBox = $(".box").attr("data-target");
        console.log(lastBox);
    }

    $('[data-toggle="tooltip"]').tooltip('show');


    $('.box').draggable({
        cancel: ".ui-rotatable-handle",
        containment: "parent",
        stop: function() {
            console.log('evento após o draggable');
        }
    });


});