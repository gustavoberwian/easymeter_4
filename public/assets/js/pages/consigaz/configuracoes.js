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
            {data: "unidade", className: "dt-body-center", orderable: false},
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

}.apply(this, [jQuery]));