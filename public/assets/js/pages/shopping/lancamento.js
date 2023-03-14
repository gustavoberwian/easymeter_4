(function () {

    "use strict";

    if ($(".content-body").data("type") == "energy") {

        // **
        // * Handler Baixar Planilha Água
        // **
        $(document).on("click", ".btn-download", function () {
            
            var $btn = $(this);
            $btn.trigger("loading-overlay:show").prop("disabled", true);

            // faz a requisição
            $.post("/energia/download", {id: $(this).data("id")}, function (json) {

                    if (json.status == "error") {
                        
                        notifyError(json.message);

                    } else {
                        var $a = $("<a>");
                        $a.attr("href", json.file);
                        $("body").append($a);
                        $a.attr("download", json.name + ".xlsx");
                        $a[0].click();
                        $a.remove();
                    }
                },
                "json"
            )
                .fail(function (xhr, status, error) {
                    // mostra erro
                    notifyError(error, "Ajax Error");
                })
                .always(function () {
                    // oculta indicador e habilita botão
                    $btn.trigger("loading-overlay:hide").prop("disabled", false);
                });
        });

        // **
        // * Configuração Datatable Unidades
        // **
        var dtUnidades = $("#dt-fechamento_unidades").DataTable({
            dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
            processing: true,
            columns: [
                {data: "nome", className: "dt-body-left"},
                {data: "leitura_anterior", className: "dt-body-center"},
                {data: "leitura_atual", className: "dt-body-center"},
                {data: "consumo", className: "dt-body-center"},
                {data: "consumo_p", className: "dt-body-center"},
                {data: "consumo_f", className: "dt-body-center"},
                {data: "demanda", className: "dt-body-center"},
                {data: "demanda_p", className: "dt-body-center"},
                {data: "demanda_f", className: "dt-body-center"},
            ],
            serverSide: true,
            sorting: [[0, 'asc']],
            pagingType: "numbers",
            pageLength: 20,
            ajax: {
                type: "POST",
                url : $("#dt-fechamento_unidades").data("url"),
                data: function (d) {
                    d.fid = $(".btn-download").data("id");
                },
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-fechamento_unidades").dataTable().fnProcessingIndicator(false);
                    $("#dt-fechamento_unidades_wrapper .table-responsive").removeClass("processing");
                },
            },
        });

        // **
        // * Configuração Datatable Comum
        // **
        var dtComum = $("#dt-fechamento_comum").DataTable({
            dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
            processing: true,
            columns: [
                {data: "nome", className: "dt-body-left"},
                {data: "leitura_anterior", className: "dt-body-center"},
                {data: "leitura_atual", className: "dt-body-center"},
                {data: "consumo", className: "dt-body-center"},
                {data: "consumo_p", className: "dt-body-center"},
                {data: "consumo_f", className: "dt-body-center"},
                {data: "demanda", className: "dt-body-center"},
                {data: "demanda_p", className: "dt-body-center"},
                {data: "demanda_f", className: "dt-body-center"},
            ],
            serverSide: true,
            sorting: [[0, 'asc']],
            pagingType: "numbers",
            pageLength: 20,
            ajax: {
                type: "POST",
                url : $("#dt-fechamento_comum").data("url"),
                data: function (d) {
                    d.fid = $(".btn-download").data("id");
                },
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-fechamento_comum").dataTable().fnProcessingIndicator(false);
                    $("#dt-fechamento_comum_wrapper .table-responsive").removeClass("processing");
                },
            },
        });
        
        $("#dt-fechamento_unidades tbody").on("click", "tr", function (event) {
            // se o clique não foi em uma celula ou na última, retorna
            if (event.target.cellIndex == undefined) return;

            let data = dtUnidades.row(this).data();
            // redireciona para o fechamento
            window.location = "/shopping/relatorio/energia/" + $(".btn-download").data("group")  + "/" + $(".btn-download").data("id") + "/" + data.DT_RowId;
        });

        $("#dt-fechamento_comum tbody").on("click", "tr", function (event) {
            // se o clique não foi em uma celula ou na última, retorna
            if (event.target.cellIndex == undefined) return;

            let data = dtComum.row(this).data();
            // redireciona para o fechamento
            window.location = "/shopping/relatorio/energia/" + $(".btn-download").data("group")  + "/" + $(".btn-download").data("id") + "/" + data.DT_RowId;
        });

    } else if ($(".content-body").data("type") == "water") {

        // **
        // * Handler Baixar Planilha Água
        // **
        $(document).on("click", ".btn-download", function () {
            
            var $btn = $(this);
            $btn.trigger("loading-overlay:show").prop("disabled", true);

            // faz a requisição
            $.post("/water/download", {id: $(this).data("id")}, function (json) {

                    if (json.status == "error") {
                        
                        notifyError(json.message);

                    } else {
                        var $a = $("<a>");
                        $a.attr("href", json.file);
                        $("body").append($a);
                        $a.attr("download", json.name + ".xlsx");
                        $a[0].click();
                        $a.remove();
                    }
                },
                "json"
            )
                .fail(function (xhr, status, error) {
                    // mostra erro
                    notifyError(error, "Ajax Error");
                })
                .always(function () {
                    // oculta indicador e habilita botão
                    $btn.trigger("loading-overlay:hide").prop("disabled", false);
                });
        });

        var dtUnidades = $("#dt-unidades").DataTable({
            dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
            processing: true,
            columns: [
                {data: "nome", className: "dt-body-left"},
                {data: "leitura_anterior", className: "dt-body-center"},
                {data: "leitura_atual", className: "dt-body-center"},
                {data: "consumo", className: "dt-body-center"},
                {data: "consumo_o", className: "dt-body-center"},
                {data: "consumo_c", className: "dt-body-center"},
            ],
            serverSide: true,
            sorting: [[0, 'asc']],
            pagingType: "numbers",
            pageLength: 20,
            ajax: {
                type: "POST",
                url : $("#dt-unidades").data("url"),
                data: function (d) {
                    d.fid = $(".btn-download").data("id");
                },
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-unidades").dataTable().fnProcessingIndicator(false);
                    $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
                },
            },
        });

        // **
        // * Configuração Datatable Comum
        // **
        var dtComum = $("#dt-comum").DataTable({
            dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
            processing: true,
            columns: [
                {data: "nome", className: "dt-body-left"},
                {data: "leitura_anterior", className: "dt-body-center"},
                {data: "leitura_atual", className: "dt-body-center"},
                {data: "consumo", className: "dt-body-center"},
                {data: "consumo_o", className: "dt-body-center"},
                {data: "consumo_c", className: "dt-body-center"},
            ],
            serverSide: true,
            sorting: [[0, 'asc']],
            pagingType: "numbers",
            pageLength: 20,
            ajax: {
                type: "POST",
                url : $("#dt-comum").data("url"),
                data: function (d) {
                    d.fid = $(".btn-download").data("id");
                },
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-comum").dataTable().fnProcessingIndicator(false);
                    $("#dt-comum_wrapper .table-responsive").removeClass("processing");
                },
            },
        });
        
        $("#dt-unidades tbody").on("click", "tr", function (event) {
            // se o clique não foi em uma celula ou na última, retorna
            if (event.target.cellIndex == undefined) return;

            let data = dtUnidades.row(this).data();
            // redireciona para o fechamento
            window.location = "/shopping/relatorio/agua/" + $(".btn-download").data("group")  + "/" + $(".btn-download").data("id") + "/" + data.DT_RowId;
        });

        $("#dt-comum tbody").on("click", "tr", function (event) {
            // se o clique não foi em uma celula ou na última, retorna
            if (event.target.cellIndex == undefined) return;

            let data = dtComum.row(this).data();
            // redireciona para o fechamento
            window.location = "/shopping/relatorio/agua/" + $(".btn-download").data("group")  + "/" + $(".btn-download").data("id") + "/" + data.DT_RowId;
        });
/*
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var currentTab = $(e.target).text(); // get current tab
            switch (currentTab)   {
            case 'Área Comum' :   //do nothing
                var table = $('#dt-fechamento_comum').DataTable();
                //$('#dt-fechamento_comum_wrapper').css( 'display', 'block' );
                table.columns.adjust().draw();
                break ;
            case 'Unidades' :
                var table = $('#dt-fechamento_unidades').DataTable();
                //$('#dt-fechamento_unidades_wrapper').css( 'display', 'block' );
                table.columns.adjust().draw();
                break ;
            default: //do nothing 
            };
        }) ;     
*/
    }
}.apply(this, [jQuery]));
