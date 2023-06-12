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
                        if (value === json.extra.custom.max * 0.05 || json.extra.custom.max === 0) {
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

}).apply(this, [jQuery]);