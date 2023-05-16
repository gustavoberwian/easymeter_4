(function () {

    "use strict";

    let $dtAlertas = $("#dt-alertas");
    let dtAlertas = $dtAlertas.DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "type", className: "dt-body-center", orderable: false},
            {data: "tipo", className: "dt-body-center", orderable: false},
            {data: "device", className: "dt-body-center filter"},
            {data: "nome", className: "filter" },
            {data: "titulo"},
            {data: "enviada", className: "dt-body-center"},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
        serverSide: true,
        sorting: [],
        pageLength: 25,
        pagingType: "numbers",
        searching: true,
        ajax: {
            url: $dtAlertas.data("url"),
            method: 'POST',
            data: {
                entidade: $(".content-body").data("entidade"),
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $dtAlertas.dataTable().fnProcessingIndicator(false);
                $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function() {
            $("#dt-alertas_wrapper .table-responsive").removeClass("processing");
            if ($('#dt-alertas tbody tr').hasClass('unread') && !$('.dataTables_paginate').children().hasClass('select-all'))
                $('#dt-alertas_paginate').prepend('<div class="select-all"><a class="mark-all cur-pointer">Marcar todos como lidos</a></div>')
        }
    });

    $(document).on('click', 'a.mark-all', function () {
        $.ajax({
            method : 'POST',
            url : "/consigaz/read_all_alert",
            data : { },
            dataType : 'json',
            success : function(json) {
                if (json.status === 'success') {
                    dtAlertas.datatable.draw();
                    // mostra notificação
                    notifySuccess(json.message);
                } else {
                    // mostra erro
                    notifyError(json.message);
                }
            },
            error : function(xhr, status, error) {
                // mostra erro
                notifyError(error, 'Ajax Error');
            }
        });
    });

    $(document).on('click', '.action-delete', function () {

        let $btn = $(this);
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
        $.post(
            "/consigaz/delete_alert",
            {
                id: $(this).data('id'),
            },
            function(json) {
                if (json.status == 'success') {
                    // remove linha
                    dtAlertas.datatable.draw();
                    // mostra notificação
                    notifySuccess(json.message);
                } else {
                    $btn.html('<i class="fas fa-trash"></i>');
                    // mostra erro
                    notifyError(json.message);
                }
            }, 'json').fail(function(xhr, status, error) {
                $btn.html('<i class="fas fa-trash"></i>');
                notifyError(error, 'Ajax Error');
            }
        );
    });

    $('#dt-alertas tbody').on('click', 'tr', function (event) {

        if (event.target.cellIndex === undefined || event.target.cellIndex === 6) return;

        let data = dtAlertas.datatable.row( this ).data();
        let $row = $(this);

        $.magnificPopup.open( {
            items: {src: '/consigaz/show_alert'},
            type: 'ajax',
            modal:true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: { id: data.DT_RowId }
                }
            },
            callbacks: {
                close: function() {
                    // mostra action
                    $('.action-delete').filter('[data-id="'+data.DT_RowId+'"]').removeClass('d-none')
                    // atualiza badge se necessário
                    if ($row.hasClass('unread')) {
                        let $badge = $('.badge-alerta');
                        let $count = $badge.attr('data-count') - 1;
                        $badge.attr('data-count', $count).html($count);
                    }
                    // remove destaque da linha
                    $row.removeClass('unread');
                }
            }
        });
    });

}.apply(this, [jQuery]));