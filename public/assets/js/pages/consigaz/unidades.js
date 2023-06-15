(function () {

    "use strict";

    // Inicializa tabela faturamentos
    let dtUnidades = $("#dt-unidades").DataTable({
        dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>pr',
        processing : true,
        paging     : true,
        language   : {
            sSearch: "",
            sSearchPlaceholder: "Pesquisar..."
        },
        columns: [
            {data: "medidor", className: "dt-body-center align-middle"},
            {data: "device", className: "dt-body-center align-middle"},
            {data: "bloco", className: "dt-body-center align-middle"},
            {data: "unidade", className: "dt-body-center align-middle"},
            {data: "ultimo_mes", className: "dt-body-center align-middle"},
            {data: "mes_atual", className: "dt-body-center align-middle"},
            {data: "previsao", className: "dt-body-center align-middle"},
            {data: "state", className: "dt-body-center align-middle"},
            {data: "actions", className: "dt-body-center align-middle"},
        ],
        serverSide: true,
        ordering  : true,
        order: [[ 3, 'asc' ]],
        pagingType: "numbers",
        searching : true,
        ajax: {
            method: 'POST',
            url: $("#dt-unidades").data("url"),
            data: function(d){
                d.entidade = $("#sel-entity").val();
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-unidades").dataTable().fnProcessingIndicator(false);
                $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function (settings) {
            $(".switch-input").themePluginIOS7Switch();
            $(".consumo-mes-atual").html(settings.json.distinctData.atual);
            $(".consumo-mes-anterior").html(settings.json.distinctData.anterior);
            $(".abertas").html(settings.json.distinctData.abertas);
            $(".fechadas").html(settings.json.distinctData.fechadas);
            $(".erros").html(settings.json.distinctData.erros);
        },
        initComplete: function (settings, json) {
            let api = this.api();
            setInterval(function () {
                api.ajax.reload(null, false);
            }, 30000);
        },
    });

    $(document).on('change', '#sel-entity', function () {
        dtUnidades.ajax.reload();
    });

    $(document).on('click', '.reload-table-modal', function () {
        dtUnidades.ajax.reload(null, false);
    });

    $(document).on('click', '.ios-switch', function (e) {
        // para propagação
        e.preventDefault();

        if ($(this).hasClass('on')) {
            $(this).removeClass('on');
            $(this).addClass('off');
        } else {
            $(this).removeClass('off');
            $(this).addClass('on');
        }

        let formData = $(this).parent().parent().serialize();

        $("#md-pin-check .alert").html("").addClass("d-none");

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_check_code'},
            type: 'ajax',
            modal: true,
            focus: "#code",
            ajax: {
                settings: {
                    type: 'POST',
                    data: formData
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
            url: '/consigaz/edit_valve_stats',
            data: formData,
            dataType: 'json',
            success: function (json) {
                if (json.status === "success") {
                    // recarrega tabela
                    dtUnidades.ajax.reload(null, false);
                    // fecha a modal
                    $.magnificPopup.close();
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
    })

    $(document).on('click', '.sync-leitura-modal', function (e) {
        e.preventDefault();

        let mid = $(this).data('mid');
        // abre a modal
        $.magnificPopup.open( {
            items: {src: '#modalSync'}, type: 'inline',
            callbacks: {
                beforeOpen: function() {
                    $('#modalSync .mid').val( mid );
                }
            }
        });
    });

    $(document).on('click', '.modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

    $(document).on('click', '.btn-sync-leitura', function (e) {
        e.preventDefault();

        let formData = $('.form-sync-leitura').serialize();

        $.ajax({
            method: 'POST',
            url: '/consigaz/edit_valve_leitura',
            data: formData,
            dataType: 'json',
            success: function (json) {
                if (json.status === "success") {
                    // fecha modal
                    $.magnificPopup.close();
                    // notifica êxito
                    notifySuccess(json.message);
                } else {
                    // notifica erro
                    notifyError(json.message);
                }
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    })

    $(document).on('click', '.btn-sheet-unidades', function () {
        let _self = this;
        $(_self).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            method: 'POST',
            url: '/consigaz/download_unidades',
            dataType: 'json',
            data: {
                entidade: $("#sel-entity").val()
            },
            success: function (json) {
                if (json.status !== "success") {
                    // notifica erro
                    notifyError(json.message);
                } else {
                    let $a = $("<a>");
                    $a.attr("href", json.file);
                    $("body").append($a);
                    $a.attr("download", json.name + '.xlsx');
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

    $(document).on('click', '.action-edit', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_edit_medidor'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {
                        medidor: $(this).data("mid")
                    }
                }
            }
        });
    });

    $(document).on('click', '#md-edit-medidor .modal-confirm', function () {

        if (!$(".form-edit-medidor").valid())
            return;

        let $btn = $(this);
        $btn.trigger("loading-overlay:show");

        // valida formulário
        if ( $(".form-edit-medidor").valid() ) {
            // captura dados
            let data = $(".form-edit-medidor").serializeArray();

            $.ajax({
                method: 'POST',
                url: '/consigaz/edit_medidor',
                dataType: 'json',
                data: data,
                success: function (json) {
                    if (json.status === 'success') {
                        // mostra sucesso
                        notifySuccess(json.message);
                        // fecha a modal
                        $.magnificPopup.close();
                        // recarrega tabela
                        dtUnidades.ajax.reload();
                    } else {
                        $("#md-edit-medidor .alert").html(json.message).removeClass("d-none");
                    }
                },
                error: function (xhr, status, error) {
                    // mostra erro
                    notifyError(error, 'Ajax Error');
                },
                complete: function () {
                    $btn.trigger("loading-overlay:hide");
                    $("#md-edit-medidor .btn").removeAttr("disabled");
                }
            });
        }
    });

}.apply(this, [jQuery]));