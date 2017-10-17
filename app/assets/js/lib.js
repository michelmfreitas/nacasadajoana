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

            //retirar as bordas dos boxes
            $("#quadro").css('border', 'none');
            $(".content").css('border', 'none');
            $(".box").css('border', 'none');

            exportar();
            //transforma a DIV #conteudo-quadro em uma imagem.
            html2canvas($('#quadro'), {
                onrendered: function(canvas) {
                    var imgData = canvas.toDataURL('image/png', 1.0);
                    /*var pdf = new jsPDF('p', 'mm');
                    pdf.addImage(imgData, 'PNG', 10, 10);
                    pdf.save('test.pdf');*/

                    img = new Image();
                    img.src = imgData;
                    img.onload = function() {
                        localStorage.setItem('imagem', imgData);
                        //console.log(img);
                        //$("#teste").html(img);
                        window.location.href = 'escolherMoldura.html';
                        //alert('pronto.');
                    }
                    img.onerror = function() { alert('there was an image load error :('); };

                    //console.log(imgData);


                    //
                },
                //width: 1122.519685,
                //height: 1587.401575,
                letterRendering: true,

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

    function exportar() {
        var canvasShiftImg = function(img, shiftAmt, scale, pageHeight, pageWidth) {
            var c = document.createElement('canvas'),
                ctx = c.getContext('2d'),
                shifter = Number(shiftAmt || 0),
                scaledImgHeight = img.height * scale,
                scaledImgWidth = img.width * scale;

            ctx.canvas.height = pageHeight;
            ctx.canvas.width = pageWidth;
            ctx.drawImage(img, 0, shifter, scaledImgWidth, scaledImgHeight)

            return c;
        };

        var canvasToImg = function(canvas, loaded, error) {
            var dataURL = canvas.toDataURL('image/png'),
                img = new Image();
            img.onload = loaded;
            img.onerror = error;
            img.src = dataURL;
        };

        var imageToPdf = function() {
            // can't pass any parameters or else "this" won't be the img element
            var img = this,
                pdf = new jsPDF('l', 'px'),
                pdfInternals = pdf.internal,
                pdfPageSize = pdfInternals.pageSize,
                pdfScaleFactor = pdfInternals.scaleFactor,
                pdfPageWidth = pdfPageSize.width,
                pdfPageHeight = pdfPageSize.height,
                pdfPageWidthPx = pdfPageWidth * pdfScaleFactor,
                pdfPageHeightPx = pdfPageHeight * pdfScaleFactor,

                imgScaleFactor = Math.min(pdfPageWidthPx / img.width, 1),
                imgScaledHeight = img.height * imgScaleFactor,

                shiftAmt = 0,
                done = false;

            while (!done) {
                var newCanvas = canvasShiftImg(img, shiftAmt, imgScaleFactor, pdfPageHeightPx, pdfPageWidthPx);
                pdf.addImage(newCanvas, 'png', 0, 0, pdfPageWidth, 0, null, 'SLOW');

                shiftAmt -= pdfPageHeightPx;

                if (-1 * shiftAmt < imgScaledHeight) {
                    pdf.addPage();
                } else {
                    done = true;
                }
            }

            pdf.save('test.pdf');
        };

        var imageLoadError = function() {
            alert('there was an image load error :(');
        };

        var gravarDB = function() {
            let imgData = canvasShiftImg()
            localStorage.setItem('imagem', img);
            window.location.href = 'escolherMoldura.html';
        }


        html2canvas($('main')[0], {
            onrendered: function(canvas) {
                // params: canvas, onload, onerror
                //canvasToImg(canvas, imageToPdf, imageLoadError);
                canvasToImg(canvas, gravarDB, imageLoadError);
            }
        });
    }


});