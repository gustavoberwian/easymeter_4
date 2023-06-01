(function () {

    "use strict";

    if ($("#dt-entidades").length) {
        let dtEntidades = $("#dt-entidades").DataTable({
            dom        : '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>pr',
            processing : true,
            paging     : true,
            language   : {
                sSearch: ""
            },
            columns    : [
                {data: "nome", className: "dt-body-center table-one-line"},
                {data: "ultima_competencia", className: "dt-body-center"},
                {data: "opened", className: "dt-body-center"},
                {data: "closed", className: "dt-body-center"},
                {data: "vermelho", className: "dt-body-center"},
                {data: "amarelo", className: "dt-body-center"},
                {data: "verde", className: "dt-body-center"},
                {data: "ultimo_mes", className: "dt-body-center"},
                {data: "mes_atual", className: "dt-body-center"},
                {data: "previsao", className: "dt-body-center"},
                {data: "actions", className: "dt-body-center"},
            ],
            serverSide : true,
            ordering   : false,
            pagingType : "numbers",
            searching  : true,
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
        });

        $("#dt-entidades tbody").on("click", "tr", function (event) {
            // se o clique não foi em uma celula ou na última, retorna
            if (event.target.cellIndex == undefined) return;

            let data = dtEntidades.row(this).data();

            window.location = "/" + $(".content-body").data("url") + "/unidades/" + data.id;
        });
    } else {
        $(document).on('click', '.card-group', function () {
            window.location = $(".content-body").data("url") + "/" + $(".content-body").data("monitoria") + "/" + $(this).data('group');
        });
    }

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
                $(_self).html('<i class="fas fa-file-download"></i> Baixar Planilha');
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
                        if (json.id) {
                            // vai para a pagina do fechamento
                            window.location = "/" + $(".content-body").data("url") + "/fechamentos/" + json.entidade + "/" + json.id;
                        } else {
                            // mostra sucesso
                            notifySuccess(json.message);
                        }

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

    $.validator.addClassRules("vdate", { dateBR : true });
    $.validator.addClassRules("vcompetencia", { competencia : true });
    $.validator.addClassRules("vnumber", { number : true });

}.apply(this, [jQuery]));