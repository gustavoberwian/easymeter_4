(function () {
    "use strict";

    // Inicializa tabela faturamentos
    let dtUnidades = $("#dt-unidades").DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "unidade_nome", className: "dt-body-left"},
            {data: "monitora", className: "dt-body-center"},
            {data: "unidade_tipo", className: "dt-body-left"},
            {data: "unidade_localizacao", className: "dt-body-center"},
            {data: "actions", className: "dt-body-center"},
        ],
        serverSide: true,
        sorting: [],
        pageLength: 10,
        pagingType: "numbers",
        searching: true,
        ajax: {
            url: $("#dt-unidades").data("url"),
            method: 'POST',
            data: {
                group_id: $("#dt-unidades").data("group"),
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-unidades").dataTable().fnProcessingIndicator(false);
                $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnPreDrawCallback: function () {
            $("#dt-unidades_wrapper .table-responsive").addClass("processing");
        },
        fnDrawCallback: function () {
            $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            $('[data-toggle="tooltip"]').tooltip();
        },
        initComplete: (settings, json) => {
            $("#dt-unidades_paginate").appendTo(".card-faturamentos .card-footer");
        },
    });

    // **
    // * Handler Row click Unidades
    // * Abre página da Unidades
    // **
    $("#dt-unidades tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        let data = dtUnidades.row(this).data();
        // redireciona para o fechamento
        window.location = "/shopping/unidade/" + data.group_id + "/" + data.id;
    });

    // **
    // * Handler Row click Unidades
    // * Abre página da Unidades
    // **
    $(".btn-incluir-unidade").on("click", function (e) {
        // para propagação
        e.preventDefault();

        window.location = "/shopping/unidades/" + $("#dt-unidades").data("group") + "/incluir";
    });

}.apply(this, [jQuery]));