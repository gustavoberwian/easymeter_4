(function($) {

    'use strict';

    var start = moment().subtract(6, 'days');
    var end = moment();
    var chart = {};
    var start_last;
    var end_last;
    var device = 0;

    var notifyError = function (msg, title = "Ocorreu um erro", visibility = true) {
        new PNotify({
            title: title,
            text: msg,
            type: "error",
            buttons: { sticker: false },
        });
    };

    function apexchart(start = moment().subtract(6, 'days'), end = moment()) {

        $(".chart-main").each(function() {

            $(this).parent().parent().trigger('loading-overlay:show');

            var el = $(this);

            var dados = {
                device  : device,
                start   : start.format("YYYY-MM-DD"),
                end     : end.format("YYYY-MM-DD"),
                field   : el.data("field"),
                compare : $('#compare').find(':selected').val()
            };

            $.ajax({
                method  : 'POST',
                url     : "/water/chart",
                data    : dados,
                dataType: 'json',
                success : function (json) {

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
                            el.parent().parent().parent().children().remove(".card-footer");
                            el.parent().parent().parent().append(json.extra.footer);
                        }
                    }

                    if (json.chart.hasOwnProperty('events')) {
                        if (json.chart.events.hasOwnProperty('click')) {
                            json.chart.events.click = function (event, chartContext, config) {
                                if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {
                                    apexchart(start_last, end_last)
                                    daterange(start_last, end_last)
                                } else {
                                    var data = json.extra.dates[config.dataPointIndex]
                                    apexchart(moment(data), moment(data))
                                    daterange(moment(data), moment(data))
                                }
                            }
                        }
                    }

                    if (el.data("field") === 'consumption') {

                        $(".main").html(json.extra.custom.main);
                        $(".period").html(json.extra.custom.period);
                        $(".period-o").html(json.extra.custom.period_o);
                        $(".period-c").html(json.extra.custom.period_c);
                        $(".month").html(json.extra.custom.month);
                        $(".month-o").html(json.extra.custom.month_o);
                        $(".month-c").html(json.extra.custom.month_c);
                        $(".prevision").html(json.extra.custom.prevision);
                        $(".prevision-o").html(json.extra.custom.prevision_o);
                        $(".prevision-c").html(json.extra.custom.prevision_c);
                        $(".day").html(json.extra.custom.day);
                        $(".day-o").html(json.extra.custom.day_o);
                        $(".day-c").html(json.extra.custom.day_c);
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

                $('#daterange-main span').html(Math.round((end - start) / 86400000) == 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
            }
        );
    }

    /**
     * Handler on change select value
     */
    $('#sel-device').on('change', function () {
        device = this.value;
        if (device === 'href') {
            window.location = $(this).find(':selected').data('url');
            return;
        }

        if ($('#compare').hasClass("select2-hidden-accessible")) {
            $('#compare').val(null).trigger('change');
            $("#compare option").prop('disabled', false);
            $("#compare option[value='" + device + "']").prop('disabled', true);
            $('#compare').select2('destroy').select2({"theme": "bootstrap", "placeholder": "Comparar", "allowClear": true});
        }

        apexchart(start_last, end_last);
        daterange(start_last, end_last);
        $('#daterange-main span').html(Math.round((end - start) / 86400000) == 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
        setTimeout(function() {
//            $('#dt-data').DataTable().ajax.reload();
        }, 100);
    })

    

    let dtResume = $("#dt-resume").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
        processing: true,
        paging: true,
        columns: [
            {data: "device", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "type", className: "dt-body-center"},
            {data: "value_read", className: "dt-body-center"},
            {data: "value_month", className: "dt-body-center"},
            {data: "value_month_open", className: "dt-body-center"},
            {data: "value_month_closed", className: "dt-body-center"},
            {data: "value_last", className: "dt-body-center"},
            {data: "value_future", className: "dt-body-center"},
        ],
        serverSide: true,
        sorting: [],
        order: [[ 2, "asc" ], [ 1, "asc" ]],
        pagingType: "numbers",
        pageLength: 36,
        searching: true,
        ajax: {
            type: 'POST',
            url: "/water/resume",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-resume").dataTable().fnProcessingIndicator(false);
                $("#dt-resume_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    $("#dt-resume tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        let data = dtResume.row(this).data();
        $("#sel-device option[value=" + data.device + "]").attr('selected', 'selected');
        $('#sel-device').trigger('change');
        $('.nav-pills button[data-bs-target="#charts"]').tab('show');
    });

    $(document).on("click", ".btn-download", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/water/downloadResume", {id: $(this).data("group")}, function (json) {

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

    $(".selector").hide();
    $(".consumption").hide();
    $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        var el = $(e.target).data("bs-target");
        $(".selector").show();
        if (el === "#charts" || el === "#engineering") {
            $(".consumption").show();
        } else {
            $(".consumption").hide();
            if (el === "#resume")
                $(".selector").hide();
        }
    });

    /**
     * Handler on change select value
     */
    $('#compare').on('change', function () {
        apexchart(start_last, end_last);
    })

    $('#compare').on('select2:unselecting', function(e) {
        $(this).data('unselecting1', true);
        $(this).data('unselecting2', true);
    });

    $('#compare').on('select2:open', function(e) {
        var unselecting1 = $(this).data('unselecting1');
        var unselecting2 = $(this).data('unselecting2');
    
        if(unselecting1 || unselecting2) {
            $(this).select2('close');
    
            if(unselecting1) {
                $(this).data('unselecting1', false);
            } else {
                $(this).data('unselecting2', false);
            }
        }
    });    

    $('#sel-device').trigger('change');

}).apply(this, [jQuery]);