(function () {

    "use strict";

    let dtEntidades = $("#dt-entidades").DataTable({
        dom        : '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>pr',
        processing : true,
        paging     : true,
        language   : {
            sSearch: "",
            sSearchPlaceholder: "Pesquisar..."
        },
        columns    : [
            {data: "nome", className: "dt-body-center table-one-line", responsivePriority: 1, targets: 0 },
            {data: "ultima_competencia", className: "dt-body-center"},
            {data: "opened", className: "dt-body-center"},
            {data: "closed", className: "dt-body-center"},
            {data: "vermelho", className: "dt-body-center"},
            {data: "vazamentos", className: "dt-body-center"},
            {data: "amarelo", className: "dt-body-center"},
            {data: "ultimo_mes", className: "dt-body-center"},
            {data: "mes_atual", className: "dt-body-center"},
            {data: "previsao", className: "dt-body-center"},
            {data: "actions", className: "dt-body-center", responsivePriority: 2, targets: -1},
        ],
        serverSide : true,
        ordering   : false,
        pageLength : 25,
        pagingType : "numbers",
        searching  : true,
        responsive : false,
        ajax       : {
            type: 'POST',
            url: $("#dt-entidades").data("url"),
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-entidades").dataTable().fnProcessingIndicator(false);
                $("#dt-entidades_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function (settings) {
            $(".consumo-mes-atual").html(settings.json.distinctData.atual);
            $(".consumo-mes-anterior").html(settings.json.distinctData.anterior);
            $(".abertas").html(settings.json.distinctData.abertas);
            $(".fechadas").html(settings.json.distinctData.fechadas);
            $(".erros").html(settings.json.distinctData.erros);
            $(".vazamentos").html(settings.json.distinctData.vazamentos);
            $(".outros").html(settings.json.distinctData.alertas);
        }
    });

    $("#dt-entidades tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        if ($(event.target).hasClass("dtr-control")) return;

        let data = dtEntidades.row(this).data();

        let newWindow = window.open("/" + $(".content-body").data("url") + "/unidades/");
        newWindow.onload = function () {
            newWindow.$("#sel-entity option[value=" + data.id + "]").attr('selected', 'selected');
            newWindow.$('#sel-entity').trigger('change');
        };
    });

    $(document).on('click', '.btn-sheet-condos', function () {
        let _self = this;
        $(_self).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            method: 'POST',
            url: '/consigaz/download_clientes',
            dataType: 'json',
            success: function (json) {
                if (json.status !== "success") {
                    // notifica erro
                    notifyError(json.message);
                } else {
                    let $a = $("<a>");
                    $a.attr("href", json.file);
                    $("body").append($a);
                    $a.attr("download", json.name + ".xlsx");
                    $a[0].click();
                    $a.remove();
                }
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
                $(_self).html('<i class="fas fa-file-download"></i> <span class="d-none d-sm-inline">Baixar Planilha</span>');
            }
        });
    })

    $(document).on('click', '.btn-inclui-all-fechamentos, .action-inclui-fechamento', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_fechamento_inclui'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {
                        entidade: $(this).data("id")
                    }
                }
            },
            callbacks: {
                ajaxContentAdded: function () {
                    $('#tar-gas-competencia').mask('00/0000');
                    $('#tar-gas-data-ini').mask('00/00/0000');
                    $('#tar-gas-data-fim').mask('00/00/0000');
                }
            }
        });
    });

    $(document).on('click', '.modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

    $(document).on('click', '#md-fechamento-inclui .modal-confirm', function () {

        if (!$(".form-gas-fechamento").valid())
            return;

        let $btn = $(this);
        $btn.trigger("loading-overlay:show");
        //$("#md-fechamento-inclui .btn").prop("disabled", true);

        // valida formulário
        if ( $(".form-gas-fechamento").valid() ) {
            // captura dados
            let data = $(".form-gas-fechamento").serializeArray();

            $.ajax({
                method: 'POST',
                url: '/gas/add_fechamento',
                dataType: 'json',
                data: data,
                success: function (json) {
                    if (json.status === 'success') {
                        // mostra sucesso
                        notifySuccess(json.message);
                        // fecha a modal
                        $.magnificPopup.close();
                    } else {
                        $("#md-fechamento-inclui .alert").html(json.message).removeClass("d-none");
                    }
                },
                error: function (xhr, status, error) {
                    // mostra erro
                    notifyError(error, 'Ajax Error');
                },
                complete: function () {
                    $btn.trigger("loading-overlay:hide");
                    $("#md-fechamento-inclui .btn").removeAttr("disabled");
                }
            });
        }
    });

    $(document).on('click', '.action-edit', function (e) {
        // para propagação
        e.preventDefault();

        $.magnificPopup.open({
            items: {src: '/consigaz/md_check_code'},
            type: 'ajax',
            modal: true,
            focus: "#code",
            ajax: {
                settings: {
                    type: 'POST',
                    data: {
                        entidade: $(this).data("id")
                    }
                }
            }
        });
    });

    $('.form-check-code').on('keypress', function (e) {
        e.preventDefault();

        // força click quando botão é pressionado
        $('#md-pin-check .modal-confirm').trigger("click");

        // retorna falso para não atualizar a página
        return false;
    })

    $(document).on('click', '#md-pin-check .modal-confirm', function (e) {
        e.preventDefault();

        let formData = $('.form-check-code').serialize();

        $("#md-pin-check .alert").html("").addClass("d-none");

        $.ajax({
            method: 'POST',
            url: '/consigaz/verify_code',
            data: formData,
            dataType: 'json',
            success: function (json) {
                if (json.status === "success") {
                    // fecha a modal
                    $.magnificPopup.close();

                    $.magnificPopup.open( {
                        items: {src: '/consigaz/md_edit_cliente'},
                        type: 'ajax',
                        modal: true,
                        ajax: {
                            settings: {
                                type: 'POST',
                                data: {
                                    entidade: json.entidade
                                }
                            }
                        }
                    });
                } else {
                    // mostra erro
                    $("#md-pin-check .alert").html(json.message).removeClass("d-none");
                }
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    });

    $(document).on('click', '#md-edit-cliente .modal-confirm', function () {

        if (!$(".form-edit-cliente").valid())
            return;

        let $btn = $(this);
        $btn.trigger("loading-overlay:show");

        // valida formulário
        if ( $(".form-edit-cliente").valid() ) {
            // captura dados
            let data = $(".form-edit-cliente").serializeArray();

            $.ajax({
                method: 'POST',
                url: '/consigaz/edit_cliente',
                dataType: 'json',
                data: data,
                success: function (json) {
                    if (json.status === 'success') {
                        // mostra sucesso
                        notifySuccess(json.message);
                        // fecha a modal
                        $.magnificPopup.close();
                        // recarrega tabela
                        dtEntidades.ajax.reload();
                    } else {
                        $("#md-edit-cliente .alert").html(json.message).removeClass("d-none");
                    }
                },
                error: function (xhr, status, error) {
                    // mostra erro
                    notifyError(error, 'Ajax Error');
                },
                complete: function () {
                    $btn.trigger("loading-overlay:hide");
                    $("#md-edit-cliente .btn").removeAttr("disabled");
                }
            });
        }
    });

    $(document).on('click', '.action-view', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_view_cliente'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {
                        entidade: $(this).data("id")
                    }
                }
            }
        });
    });

    $.validator.addClassRules("vdate", { dateBR : true });
    $.validator.addClassRules("vcompetencia", { competencia : true });
    $.validator.addClassRules("vnumber", { number : true });

}.apply(this, [jQuery]));