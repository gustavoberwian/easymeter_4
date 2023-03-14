(function($) {

	'use strict';

    var charts  = {};
    var d_start = moment().subtract(6, 'days');
    var d_end   = moment();
    var l_start, l_end;
    let open = false;

    let __h = {
        'Hoje': [moment(), moment()],
        'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
        'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
        'Este Mês': [moment().startOf('month'), moment().endOf('month')],
        'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }

    function calendar(start = moment().subtract(6, 'days'), end = moment()) {
        $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                minDate: moment().subtract(4, 'months').format('DD/MM/YYYY'),
                maxDate: moment().format('DD/MM/YYYY'),
                maxSpan: {"days": 60},
                opens: "right",
                ranges: __h,
                locale: {
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "De",
                    "toLabel": "até",
                    "customRangeLabel": "Personalizado"
                }
            }
        );

        if (start.format('DD/MM/YYYY') == end.format('DD/MM/YYYY'))
            $('#daterange span').html(start.format('ddd, DD/MM/YYYY'));
        else
            $('#daterange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    };

    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        if (picker.element.hasClass('daterange')) {
            // atualiza botão daterange
            if (picker.startDate.format('DD/MM/YYYY') == picker.endDate.format('DD/MM/YYYY'))
                $('#daterange span').html(picker.startDate.format('ddd, DD/MM/YYYY'));
            else
                $('#daterange span').html(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

            d_start = picker.startDate;
            d_end   = picker.endDate;

            $('.chart-container.agua').each(function() { 
                chart_update($(this).data("mid"), picker.startDate, picker.endDate);
            });

            cards(picker.startDate, picker.endDate);
        }
    });

    let cards = function(start, end) {
        $.ajax({
            method: 'POST',
            url: '/ajax/get_consumo_total_periodo',
            data: {
                id: 26,
                start: start.format('YYYY-MM-DD'),
                end: end.format('YYYY-MM-DD'),
            },
            dataType: 'json',
            success: function(json) {
                $("#total").html(json.value);
                $("#average").html(json.average);
                $("#label").html(json.label);
            }
        });
    }

    let chart_update = function(mid, start, end) {
        var dados = {
            id: mid,
            monitoramento: $('#chart-' + mid).data('monitoramento'),
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD'),
        };

        $('.chart-body').trigger('loading-overlay:show');

        $.ajax({
            method: 'POST',
            url: '/ajax/get_chart',
            data: dados,
            dataType: 'json',
            success: function(json) {

                json.yaxis.labels.formatter = function (value) {
                    return (value === null) ? "" : value.toLocaleString("pt-BR", {minimumFractionDigits: json.extra.decimals, maximumFractionDigits: json.extra.decimals}) + " " + json.extra.unit;
                };

                json.tooltip.x.formatter = function (value, {series, seriesIndex, dataPointIndex, w}) {
                    return json.extra.tooltip.title[dataPointIndex];
                };

                json.tooltip.y.formatter = function (value) {
                    return (value === null) ? "" : value.toLocaleString("pt-BR", {minimumFractionDigits: json.extra.tooltip.decimals, maximumFractionDigits: json.extra.tooltip.decimals}) + " " + json.extra.unit;
                };
                
                if (json.hasOwnProperty('extra')) {
                    if (json.extra.hasOwnProperty('footer')) {
                        $('#card-' + mid).children().remove(".card-footer");
                        $('#card-' + mid).append(json.extra.footer);
                    }
                }

                json.chart.events.click = function (event, chartContext, config) {

                    if (open || $(chartContext.el).data('monitoramento') == 'nivel') return;

                    if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {

                        $('.chart-container.agua').each(function() { 
                            chart_update($(this).data("mid"), d_start, d_end);
                        });

                        //calendar(d_start, d_end);
                        $('#daterange').data('daterangepicker').setStartDate(d_start.format("DD/MM/YYYY"));
                        $('#daterange').data('daterangepicker').setEndDate(d_end.format("DD/MM/YYYY"));
                        $('#daterange span').html(d_start.format('DD/MM/YYYY') + ' - ' + d_end.format('DD/MM/YYYY'));
            

                        cards(d_start, d_end);

                    } else {

                        var data = json.extra.dates[config.dataPointIndex];
                        $('.chart-container.agua').each(function() { 
                            chart_update($(this).data("mid"), moment(data), moment(data));
                        });

                        //calendar(moment(data), moment(data));
                        $('#daterange').data('daterangepicker').setStartDate(moment(data).format("DD/MM/YYYY"));
                        $('#daterange').data('daterangepicker').setEndDate(moment(data).format("DD/MM/YYYY"));
                        $('#daterange span').html(moment(data).format('ddd, DD/MM/YYYY'));

                        cards(moment(data), moment(data));
                    }
                };

                if (charts[mid]) {
                    charts[mid].updateOptions(json);
                } else {
                    charts[mid] = new ApexCharts($('#chart-' + mid)[0], json);
                    charts[mid].render();
                }
            },
            error: function(xhr, status, error) {
                new PNotify({
                    title: 'Erro',
                    text: "Ocorreu um erro ao processar a solicitação.",
                    type: 'error',
                    buttons: {sticker: false}
                });	            
            },
            complete: function() {
                $('.chart-body').trigger('loading-overlay:hide');
            }
        });
    }

    let poco = function(el) {
        el.waterTank({
            width: 100,
            height: 350,
            color: "skyblue",
            level: el.data('volume'),
        });
    }

    $('.chart-container').each(function() { 
        chart_update($(this).data("mid"), d_start, d_end);
    });

    $('.reservatorio').each(function() { 
        poco($(this));
    });

    calendar(d_start, d_end);

    cards(d_start, d_end);

    $('.daterange-nivel').each(function() { 
        $('.daterange-nivel').find('span').html(moment().format('ddd, DD/MM/YYYY'));

        $('#'+$(this).prop('id')).daterangepicker({
            startDate: moment().format('DD/MM/YYYY'),
            endDate: moment().format('DD/MM/YYYY'),
            minDate: moment().subtract(4, 'months').format('DD/MM/YYYY'),
            maxDate: moment().format('DD/MM/YYYY'),
            singleDatePicker: true,
            autoApply: true,
        });

        $('#'+$(this).prop('id')).on('apply.daterangepicker', function(ev, picker) {

            $('.daterange-nivel').find('span').html(picker.startDate.format('ddd, DD/MM/YYYY'));

            $('.chart-container.nivel').each(function() { 
                chart_update($(this).data("mid"), picker.startDate, picker.startDate);
            });
        });

        $('#'+$(this).prop('id')).on('show.daterangepicker', function(ev, picker) {
            open = true;
        });
    
        $('#'+$(this).prop('id')).on('hide.daterangepicker', function(ev, picker) {
            setTimeout(function() {
                open = false;
            }, 300);
        });
    
    });

}).apply(this, [jQuery]);
