$(document).ready(function() {

    function mantemProporcaoQuadro() {
        var larguraQuadro = $("#quadro").width();
        console.log(larguraQuadro);
        var alturaQuadro = (larguraQuadro * 494) / 350;
        $("#quadro").css("height", alturaQuadro.toFixed(2) + "px");




    }

    //mantemProporcaoQuadro();

    $(window).resize(function() {
        //mantemProporcaoQuadro();
    });

    alvoAtivo = 1;

    $("#bt-voltar").on("click", function() {
        console.log('voltando...');
        window.location.href = history.back(-1);
    });

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
        //$("#quadro").addClass(bg);
        $("#quadro").css("background-image", `url('assets/img/fundos-tela/${bg}.jpg')`);
    });

    $(".text-item").click(function() {
        //console.log(alvoAtivo);
        console.log('clicou');
        $(".texto").removeAttr('style');
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

            //retirar as bordas dos boxes, trocar imagem por alta resolução do fundo
            $("#quadro").css('box-shadow', 'none');
            var bgQuadro = $("#quadro").css('background-image');
            //bgQuadro = bgQuadro.replace("/fundos-tela/", "/fundos-exportacao/");
            $("#quadro").css('background-image', bgQuadro);

            $(".content").css('border', 'none');
            $(".box").css('border', 'none');



            html2canvas($('#quadro'), {
                onrendered: function(canvas) {
                    var imgData = canvas.toDataURL('image/png', 1.0);
                    img = new Image();
                    img.src = imgData;
                    img.width = 2482;
                    img.height = 3508;
                    img.onload = function() {
                        localStorage.setItem('imagem', imgData);
                        window.location.href = 'escolherMoldura.html';
                    }
                    img.onerror = function() { alert('there was an image load error :('); };


                },
                scale: 2,
                dpi: 300,
                letterRendering: true,

            });



        }
    });

    $(".btn-limpar").on("click", function() {
        window.location.href = 'index.html';
    });

    $("#fonte").on("change", function() {
        $(".box[data-target='" + alvoAtivo + "']").removeAttr("id");
        $(".box[data-target='" + alvoAtivo + "']").attr("id", $(this).val());

    });

    $("#font-size").on("change", function() {
        let tamanho = [
            "1rem", "1.4rem", "1.8rem", "2rem", "2.5rem", "3rem", "4rem", "5rem"
        ];
        //console.log($(this).val(), alvoAtivo);
        $(".box[data-target='" + alvoAtivo + "']").css("font-size", tamanho[$(this).val()]);
        $(".box[data-target='" + alvoAtivo + "']").css("line-height", tamanho[$(this).val()]);
    });

    $(".content").on("dblclick", ".box", function() {
        var obj = $(this).attr('data-target');
        alvoAtivo = alteraAlvoAtivo(obj);
        console.log(obj, alvoAtivo, $(".box[data-target='" + obj + "'] span").html());
        $("#novo-texto").val($(".box[data-target='" + obj + "'] span").html());
        $('#exampleModal').modal('show');
    });

    $('#bt-addTexto').on("click", function() {
        let novotexto = $("#novo-texto").val();
        novotexto = novotexto.replace(/\n\r?/g, '<br />');

        $(".box").css('border-color', "#FFF");
        $(this).css('border-color', "#999");

        if ($(".box[data-target=" + alvoAtivo + "]").length === 0) {
            let novoBox = "<div class='box' data-target='" + alvoAtivo + "'><span class='texto'>" + novotexto + "</span></div>";
            $(".content").append(novoBox);
            $(".box[data-target='" + alvoAtivo + "']").draggable({
                cancel: ".ui-rotatable-handle",
                containment: "parent"
            });
        } else {
            $(".box[data-target=" + alvoAtivo + "]").html(`<span class='texto'>${novotexto}</span>`);
        }
        $('#exampleModal').modal('hide');
        $("#novo-texto").val('');
    });

    $(".content").on("click", ".box", function() {
        //$(this).trigger("dblclick");
        var obj = $(this).attr('data-target');
        alvoAtivo = obj;
        $(".box").css('border-color', "#FFF");
        $(this).css('border-color', "#999");
        console.log("Alvo ativo: " + alvoAtivo);
        console.log("ObJ: " + obj);
        $(this).resizable().rotatable();
    });



    $("#addButton").on("click", function() {
        //reseta opções de fontes
        $("#fonte").val($("#fonte option:first").val());
        $('#font-size option:eq(3)').attr('selected', 'selected');
        let novoBox = $(".box").length + 1;
        alvoAtivo++;
        $('#exampleModal').modal('show');
    });

    $(".btn-apagar-box").on("click", function() {
        console.log("apagar elemento: " + alvoAtivo);
        $(`.box[data-target=${alvoAtivo}]`).remove();
    });

    function alteraAlvoAtivo(alvoAtual) {
        return alvoAtivo = alvoAtual++;
    }

    function inserirNovoBox(texto) {
        var lastBox = $(".box").attr("data-target");
        console.log(lastBox);
    }

    $('[data-toggle="tooltip"]').tooltip('show');
    /*$('[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
        $(this).tooltip('dispose');
    });*/
    

    $('.box').draggable({
        cancel: ".ui-rotatable-handle",
        containment: "parent",
        stop: function() {
            console.log('evento após o draggable');
        }
    });



});