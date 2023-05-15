(function ($) {

    "use strict";

    // Inicializa tabela faturamentos
    let $dtUnidades = $("#dt-unidades");
    let dtUnidades = $dtUnidades.DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "medidor", className: "dt-body-center align-middle"},
            {data: "bloco", className: "dt-body-center align-middle"},
            {data: "unidade", className: "dt-body-center align-middle"},
            {data: "ultimo_mes", className: "dt-body-center align-middle"},
            {data: "mes_atual", className: "dt-body-center align-middle"},
            {data: "previsao", className: "dt-body-center align-middle"},
            {data: "state", className: "dt-body-center align-middle"},
            {data: "actions", className: "dt-body-center align-middle"},
        ],
        serverSide: true,
        sorting: [],
        pageLength: 25,
        pagingType: "numbers",
        searching: true,
        ajax: {
            url: $dtUnidades.data("url"),
            method: 'POST',
            data: {
                entidade: $(".content-body").data("entidade"),
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $dtUnidades.dataTable().fnProcessingIndicator(false);
                $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function () {
            $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            $(".switch-input").themePluginIOS7Switch()
        },
    });

    setInterval(() => dtUnidades.ajax.reload(), 30000);

    $(document).on('click', '.reload-table-modal', function () {
        dtUnidades.ajax.reload();
    });

    $(document).on('click', '.ios-switch', function () {
        let _self = this;
        let formData = $(this).parent().parent().serialize();

        $.ajax({
            method: 'POST',
            url: '/consigaz/edit_valve_stats',
            data: formData,
            dataType: 'json',
            success: function (json) {
                if (json.status === "success") {
                    // altera status do botão
                    $(_self).parent().addClass('warning')
                    $(_self).parent().addClass('disabled')
                } else {
                    // notifica erro
                    notifyError(json.message);

                    // altera status do botão para valor anterior
                    if ($(_self).hasClass('on')) {
                        $(_self).removeClass('on');
                        $(_self).addClass('off');
                    } else {
                        $(_self).removeClass('off');
                        $(_self).addClass('on');
                    }
                }
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        });
    });

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

}.apply(this, [jQuery]));