(function () {

    "use strict";

    // Inicializa tabela faturamentos
    let dtShoppings = $("#dt-shoppings").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-6"l><"col-md-6"p>>',
        processing: true,
        paging: false,
        columns: [
            { data: "nome", className: "table-one-line dt-body-left" },
            { data: "endereco", className: "table-one-line dt-body-left" },
            { data: "status", className: "table-one-line dt-body-center" },
        ],
        serverSide: true,
        sorting: [],
        pagingType: "numbers",
        searching: true,
        ajax: {
            type: 'POST',
            data: { entity: $(".content-body").data('entity') },
            url: $("#dt-shoppings").data("url"),
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-shoppings").dataTable().fnProcessingIndicator(false);
                $("#dt-shoppings_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnPreDrawCallback: function () {
            $("#dt-shoppings_wrapper .table-responsive").addClass("processing");
        },
        fnDrawCallback: function () {
            $("#dt-shoppings_wrapper .table-responsive").removeClass("processing");
            $('[data-toggle="tooltip"]').tooltip();
        },
        initComplete: (settings, json) => {
            $("#dt-shoppings_paginate").appendTo(".card-faturamentos .card-footer");
        },
    });

    // **
    // * Handler Row click linha
    // * Abre página do shopping
    // **
    $("#dt-shoppings tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        let data = dtShoppings.row(this).data();
        // redireciona para o fechamento
        window.location = "/shopping/energy/" + data.id;
    });

    $(document).on('click', '.card', function () {
        window.location = "/shopping/energy/" + $(this).data('group');
    });

}.apply(this, [jQuery]));