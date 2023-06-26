(function($) {

    'use strict';

    let chart = {};
    let start_last;
    let end_last;
    let device = $(".page-header").data('medidor');

    function apexchart(start = moment().subtract(6, 'days'), end = moment()) {

        $(".chart-main").each(function() {

            let el = $(this);

            if (el.data('field') === 'sensor') {
                if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {
                    return;
                }
            } else {
                el.parent().parent().trigger('loading-overlay:show');
            }

            let dados = {
                device  : device,
                start   : start.format("YYYY-MM-DD"),
                end     : end.format("YYYY-MM-DD"),
                field   : el.data("field"),
                compare : $('#compare').find(':selected').val(),
                shopping_id : $('.content-body').data('group')
            };

            $.ajax({
                method  : 'POST',
                url     : "/gas/chart",
                data    : dados,
                dataType: 'json',
                success : function (json) {

                    json.yaxis.labels.formatter = function (value) {
                        return (value === null) ? "" : value.toLocaleString("pt-BR", {
                            minimumFractionDigits: json.extra.decimals,
                            maximumFractionDigits: json.extra.decimals
                        }) + " " + json.extra.unit;
                    };

                    json.tooltip.x.formatter = function (value, {series, seriesIndex, dataPointIndex, w}) {
                        return json.extra.tooltip.title[dataPointIndex];
                    };

                    json.tooltip.y.formatter = function (value) {
                        if (value === json.extra.custom.max * 0.005 || json.extra.custom.max === 0) {
                            return 0 + " " + json.extra.unit;
                        } else {
                            return (value === null) ? "" : value.toLocaleString("pt-BR", {
                                minimumFractionDigits: json.extra.tooltip.decimals,
                                maximumFractionDigits: json.extra.tooltip.decimals
                            }) + " " + json.extra.unit;
                        }
                    };

                    if (json.hasOwnProperty('extra')) {
                        if (json.extra.hasOwnProperty('footer')) {
                            el.parent().parent().parent().children().remove(".card-footer");
                            el.parent().parent().parent().append(json.extra.footer);
                        }
                    }

                    if (json.chart.hasOwnProperty('events')) {
                        if (json.chart.events.hasOwnProperty('click') && json.chart.events.click === true) {
                            json.chart.events.click = function (event, chartContext, config) {
                                if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {
                                    apexchart(start_last, end_last)
                                    daterange(start_last, end_last)
                                } else {
                                    let data = json.extra.dates[config.dataPointIndex]
                                    apexchart(moment(data), moment(data))
                                    daterange(moment(data), moment(data))
                                }
                            }
                        }
                    }

                    if (el.data("field") === 'consumption') {

                        $(".period").html(json.extra.custom.period);
                        $(".month").html(json.extra.custom.month);
                        $(".prevision").html(json.extra.custom.prevision);
                    }

                    if (chart[el.data("field")]) {
                        chart[el.data("field")].updateOptions(json);
                    } else {
                        chart[el.data("field")] = new ApexCharts(el[0], json);
                        chart[el.data("field")].render();
                    }

                    if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {
                        // Populando seletor de data e ícones
                        $('#daterange-main span').html(start.format('ddd, DD/MM/YYYY'));
                    } else {
                        // Populando seletor de data e ícones
                        $('#daterange-main span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                    }

                    if (start.format("YYYY-MM-DD") !== end.format("YYYY-MM-DD")) {
                        start_last = start;
                        end_last = end;
                    }
                },
                error: function (xhr, status, error) {
                    notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                    return false;
                },
                complete: function () {
                    el.parent().parent().trigger('loading-overlay:hide');

                    if ($('.card-footer')) {
                        $('.card-footer').trigger('loading-overlay:hide');
                    }
                }
            });
        });
    }

    function daterange(start = moment().subtract(6, 'days'), end = moment()) {

        $('#daterange-main span').html(Math.round((end - start) / 86400000) === 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
        apexchart(start, end);

        // Daterange picker
        $('#daterange-main').daterangepicker(
            {
                startDate: start,
                endDate: end,
                maxDate: moment().format('DD/MM/YYYY'),
                maxSpan: {"days": 60},
                opens: "right",
                ranges: {
                    'Hoje': [moment(), moment()],
                    'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
                    'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                    'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "De",
                    "toLabel": "até",
                    "customRangeLabel": "Personalizado"
                },
            },
            function (start, end, label) {

                apexchart(start, end);

                $('#daterange-main span').html(Math.round((end - start) / 86400000) === 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
            }
        );
    }

    daterange();

    $(document).on('click', '.btn-sheet-consumo', function () {
        let _self = this;
        $(_self).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            method   : 'POST',
            url      : '/gas/download_consumo',
            dataType : 'json',
            data     : {
                device : device,
                start  : start_last.format("YYYY-MM-DD"),
                end    : end_last.format("YYYY-MM-DD")
            },
            success  :  function (json) {
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
            error    : function (xhr, status, error) {
            },
            complete : function () {
                $(_self).html('<i class="fas fa-file-download"></i> Gerar Planilha');
            }
        });
    })

    if ($("#dt-fechamentos-unidade").length) {
        let dtGas = $("#dt-fechamentos-unidade").DataTable({
            dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>pr',
            processing : true,
            paging     : true,
            language   : {
                sSearch: ""
            },
            columns: [
                {data: "competencia", class: "dt-body-center"},
                {data: "inicio", class: "dt-body-center" },
                {data: "fim", class: "dt-body-center"},
                {data: "consumo", class: "dt-body-center"},
                {data: "emissao", class: "dt-body-center"},
                {data: "action", class: "dt-body-center"},
            ],
            serverSide: true,
            sorting: [],
            pageLength: 10,
            pagingType: "numbers",
            searching: true,
            ajax: {
                type: 'POST',
                url: $("#dt-fechamentos-unidade").data("url"),
                data: function(d){
                    d.unidade = $(".page-header").data("unidade");
                },
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-fechamentos-unidade").dataTable().fnProcessingIndicator(false);
                    $("#dt-fechamentos_wrapper .table-responsive").removeClass("processing");
                },
            },
        });
    }

    if ($("#dt-alertas-unidade").length) {
        let dtAlertas = $("#dt-alertas-unidade").DataTable({
            dom: '<"table-responsive"t>pr',
            processing: true,
            columns: [
                {data: "type", className: "dt-body-center", orderable: false},
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
                url: $("#dt-alertas-unidade").data("url"),
                method: 'POST',
                data: function (d) {
                    d.entidade = $("#dt-alertas-unidade").data("entidade");
                },
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-alertas-unidade").dataTable().fnProcessingIndicator(false);
                    $("#dt-alertas-unidade_wrapper .table-responsive").removeClass("processing");
                },
            },
            fnDrawCallback: function () {
                $("#dt-alertas_wrapper .table-responsive").removeClass("processing");
                if ($('#dt-alertas tbody tr').hasClass('unread') && !$('.dataTables_paginate').children().hasClass('select-all'))
                    $('#dt-alertas_paginate').prepend('<div class="select-all"><a class="mark-all cur-pointer">Marcar todos como lidos</a></div>')
            }
        });
    }

    $(document).on('click', '.action-ver-fechamento', function (e) {
        e.preventDefault();

        var data_start = $(this).data("start").split('-');
        var start = moment({ year: data_start[0].trim(), month: data_start[1].trim(), day: data_start[2].trim() }); // $(this).data("start")

        var data_end = $(this).data("end").split('-');
        var end = moment({ year: data_end[0].trim(), month: data_end[1].trim(), day: data_end[2].trim() }); // $(this).data("end")

        apexchart(start, end);
    })

    $(document).on('click', '.action-modal-fechamento', function (e) {
        e.preventDefault();
    })

}).apply(this, [jQuery]);