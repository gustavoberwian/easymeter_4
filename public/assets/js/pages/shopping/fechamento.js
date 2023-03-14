(function ($) {
    "use strict";

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
                $(".table-responsive").removeClass("processing");
            },
        },
    });

    // **
    // * Configuração Datatable Comum
    // **
    var dtUnidades = $("#dt-fechamento_comum").DataTable({
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
                $(".table-responsive").removeClass("processing");
            },
        },
    });
    
    $("#dt-fechamento_unidades tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        let data = dtUnidades.row(this).data();
        // redireciona para o fechamento
        window.location = "/shopping/relatorio/" + $(".btn-download").data("group")  + "/" + $(".btn-download").data("id") + "/" + data.DT_RowId;
    });

    $("#dt-fechamento_comum tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        let data = dtUnidades.row(this).data();
        // redireciona para o fechamento
        window.location = "/shopping/relatorio/" + $(".btn-download").data("group")  + "/" + $(".btn-download").data("id") + "/" + data.DT_RowId;
    });

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


    if ($("#chart").length) {
        // trata periodo
        let d_start = moment($(".d_start").data("inicio") * 1000);
        let d_end = moment($(".d_end").data("fim") * 1000).subtract(1, "days");
        let bar_mode = "zoom-in";
        let bar;

        let bar_update = function () {
            $(".card-chart").trigger("loading-overlay:show");

            $.ajax({
                method: "POST",
                url: "/ajax/get_bar_chart_3",
                data: {
                    type: "agua",
                    start: d_start.format("YYYY-MM-DD"),
                    end: d_end.format("YYYY-MM-DD"),
                    uid: $("#fechamento_id").data("uid"),
                    total: true,
                },
                dataType: "json",
                success: function (json) {
                    // seta mode
                    bar_mode = "zoom-in";
                    // atualiza grafico
                    json.options.plugins.tooltip.callbacks.title = function (
                        tooltipItems
                    ) {
                        return (
                            tooltipItems[0].label +
                            " - " +
                            json.data.extra[tooltipItems[0].dataIndex]
                        );
                    };
                    json.options.plugins.tooltip.callbacks.label = function (context) {
                        return (
                            context.dataset.label +
                            ": " +
                            context.parsed.y.toLocaleString("pt-BR", {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            }) +
                            "kW"
                        );
                    };
                    // mostra pointer nas barras
                    json.options.onHover = function (e, elements) {
                        e.native.target.style.cursor = elements[0] ? bar_mode : "default";
                    };

                    json.options.scales.y.ticks.maxTicksLimit = 6;
                    json.options.plugins.legend.display = true;

                    $(".consumo-unidades").html(
                        Math.round(json.data.total / 1000) + "M&sup3;"
                    );

                    bar = new Chart($("#chart"), {
                        type: "bar",
                        data: json.data,
                        options: json.options,
                    });
                },
                error: function (xhr, status, error) {
                    notifyError(error, "Ocorreu um erro ao processar a solicitação.");
                    return false;
                },
                complete: function () {
                    $(".card-chart").trigger("loading-overlay:hide");
                },
            });
        };

        bar_update();

        document.getElementById("chart").onclick = function (evt) {
            var activePoints = bar.getElementsAtEventForMode(
                evt,
                "index",
                {intersect: true},
                false
            );
            var url;
            var d_l_start, d_l_end;

            if (activePoints.length > 0) {
                $(".card-chart").trigger("loading-overlay:show");

                if (bar_mode == "zoom-in") {
                    bar_mode = "zoom-out";
                    d_l_start = d_start.clone().add(activePoints[0].index, "days");
                    d_l_end = d_l_start.clone();
                    url = "/ajax/get_bar_chart_detail_3";
                } else {
                    bar_mode = "zoom-in";
                    d_l_start = d_start.clone();
                    d_l_end = d_end.clone();
                    url = "/ajax/get_bar_chart_3";
                }

                $.ajax({
                    method: "POST",
                    url: url,
                    data: {
                        type: "agua",
                        uid: $("#fechamento_id").data("uid"),
                        start: d_l_start.format("YYYY-MM-DD"),
                        end: d_l_end.format("YYYY-MM-DD"),
                        total: true,
                    },
                    dataType: "json",
                    success: function (json) {
                        // atualiza grafico
                        bar.data = json.data;
                        bar.options = json.options;

                        if (json.data.hasOwnProperty("extra")) {
                            if (bar_mode == "zoom-out") {
                                json.options.plugins.tooltip.callbacks.title = function (
                                    tooltipItems,
                                    data
                                ) {
                                    return (
                                        moment(((json.condo[5] + json.condo[4]) / 2) * 1000).format(
                                            "DD/MM"
                                        ) +
                                        ": " +
                                        json.data.extra[tooltipItems[0].dataIndex]
                                    );
                                };
                            } else {
                                json.options.plugins.tooltip.callbacks.title = function (
                                    tooltipItems
                                ) {
                                    return (
                                        tooltipItems[0].label +
                                        " - " +
                                        json.data.extra[tooltipItems[0].dataIndex]
                                    );
                                };
                            }
                            json.options.plugins.tooltip.callbacks.label = function (
                                context
                            ) {
                                return (
                                    context.dataset.label +
                                    ": " +
                                    context.parsed.y.toLocaleString("pt-BR", {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0,
                                    }) +
                                    "kW"
                                );
                            };
                        }

                        // mostra pointer nas barras
                        json.options.onHover = function (e, elements) {
                            e.native.target.style.cursor = elements[0] ? bar_mode : "default";
                        };

                        json.options.plugins.legend.display = true;

                        // atualiza grafico
                        bar.update();
                    },
                    error: function (xhr, status, error) {
                        notifyError(error, "Ocorreu um erro ao processar a solicitação.");
                        return false;
                    },
                    complete: function () {
                        $(".card-chart").trigger("loading-overlay:hide");
                    },
                });
            }
        };
    }
}.apply(this, [jQuery]));
