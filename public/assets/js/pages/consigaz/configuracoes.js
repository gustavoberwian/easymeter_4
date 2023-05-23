(function () {

    "use strict";

    let $dtUnidades = $("#dt-unidades");
    let dtUnidades = $dtUnidades.DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "m_id", className: "d-none", orderable: false},
            {data: "u_id", className: "d-none", orderable: false},
            {data: "medidor", className: "dt-body-center", orderable: false},
            {data: "device", className: "dt-body-center", orderable: false},
            {data: "unidade", className: "dt-body-center", orderable: false},
            {data: "bloco", className: "dt-body-center table-one-line", orderable: false},
            {data: "actions", className: "dt-body-center", orderable: false},
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
        fnDrawCallback: function() {
            $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
        }
    });

    $(document).on('click', '.generate-code', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_generate_code'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {}
                },
            }
        });
    });

    $(document).on('click', '.modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();

        setTimeout(function () {
            location.reload();
        }, 300)
    });

}.apply(this, [jQuery]));