(function($) {

    'use strict';

    var start = moment().subtract(6, 'days');
    var end = moment();
    var chart = {};
    var start_last;
    var end_last;
    var device = $(".page-header").data("device");

    function apexchart(start = moment().subtract(6, 'days'), end = moment()) {

        $(".chart-main").each(function() {
            $(this).parent().parent().trigger('loading-overlay:show');

            var el = $(this);

            var dados = {
                device  : device,
                start   : start.format("YYYY-MM-DD"),
                end     : end.format("YYYY-MM-DD"),
                field   : el.data("field")
            };

            $.ajax({
                method  : 'POST',
                url     : "/energia/chart_engineering",
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
                        if (el.data("field") === 'mainFactor' || el.data("field") === 'factor') {
                            if (value === null)
                                return null;
                            if (value > 0) {
                                return (1 - value).toLocaleString("pt-BR", {minimumFractionDigits: 3, maximumFractionDigits: 3}) + " ind";
                            } else if (value < 0) {
                                return (1 - (value * -1)).toLocaleString("pt-BR", {minimumFractionDigits: 3, maximumFractionDigits: 3}) + " cap";
                            }
                            return 1;

                        } else {

                            return (value === null) ? "" : value.toLocaleString("pt-BR", {minimumFractionDigits: json.extra.tooltip.decimals, maximumFractionDigits: json.extra.tooltip.decimals}) + " " + json.extra.unit;
                        }
                    };

                    if (json.hasOwnProperty('extra')) {
                        if (json.extra.hasOwnProperty('footer')) {
                            el.parent().parent().parent().children().remove(".card-footer");
                            el.parent().parent().parent().append(json.extra.footer);
                        }
                    }

                    if (start.format("YYYY-MM-DD") !== end.format("YYYY-MM-DD")) {
                        start_last = start;
                        end_last = end;
                    }

                    if (json.chart.hasOwnProperty('events')) {
                        if (json.chart.events.hasOwnProperty('click')) {
                            json.chart.events.click = function (event, chartContext, config) {
                                if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {
                                    apexchart(start_last, end_last)
                                } else {
                                    var data = json.extra.dates[config.dataPointIndex]
                                    apexchart(moment(data), moment(data))
                                }
                            }
                        }
                    }

                    if (el.data("field") === 'mainActivePositive') {

                        $(".main").html(json.extra.custom.main);
                        $(".period").html(json.extra.custom.period);
                        $(".period-f").html(json.extra.custom.period_f);
                        $(".period-p").html(json.extra.custom.period_p);
                        $(".month").html(json.extra.custom.month);
                        $(".month-f").html(json.extra.custom.month_f);
                        $(".month-p").html(json.extra.custom.month_p);
                        $(".prevision").html(json.extra.custom.prevision);
                        $(".prevision-p").html(json.extra.custom.prevision_p);
                        $(".prevision-f").html(json.extra.custom.prevision_f);
                        $(".day").html(json.extra.custom.day);
                        $(".day-p").html(json.extra.custom.day_p);
                        $(".day-f").html(json.extra.custom.day_f);

                    } else if (el.data("field") === 'mainFactor' || el.data("field") === 'factor') {

                        json.yaxis.labels.formatter = function (value, index) {
                            if (value === null)
                                return null;
                            if (value > 0) {
                                return (1 - value).toLocaleString("pt-BR", {minimumFractionDigits: json.extra.decimals, maximumFractionDigits: json.extra.decimals}) + " ind";
                            } else if (value < 0) {
                                return (1 - (value * -1)).toLocaleString("pt-BR", {minimumFractionDigits: json.extra.decimals, maximumFractionDigits: json.extra.decimals}) + " cap";
                            }
                            return 1;
                        };
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

    // Daterange picker
    $('#daterange-main').daterangepicker(
        {
            startDate: start,
            endDate: end,
            maxDate: moment().format('DD/MM/YYYY'),
            maxSpan: { "days": 60 },
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

            $('#dt-data').DataTable().ajax.reload();

            $('#daterange-main span').html(Math.round((end - start) / 86400000) == 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
        }
    );

    apexchart(start, end);

    /**
     * Handler on change select value
     */
    $('#sel-device').on('change', function () {
        device = this.value;
        if (device === 'href') {
            window.location = $(this).find(':selected').data('url');
            return;
        }
        apexchart(start_last, end_last);
        setTimeout(function() {
            $('#dt-data').DataTable().ajax.reload();
        }, 100);
    })

    let dtAbnormal = $("#dt-abnormal").DataTable({
        //dom: '<"table-responsive"t>Bpr',
        dom: '<"table-responsive"t>r<"row"<"col-md-6"B><"col-md-6"p>>',
        processing: true,
        paging: true,
        columns: [
            {data: "date", className: "dt-body-center"},
            {data: "voltageA", className: "dt-body-center"},
            {data: "voltageB", className: "dt-body-center"},
            {data: "voltageC", className: "dt-body-center"},
            {data: "currentA", className: "dt-body-center"},
            {data: "currentB", className: "dt-body-center"},
            {data: "currentC", className: "dt-body-center"},
            {data: "activeA", className: "dt-body-center"},
            {data: "activeB", className: "dt-body-center"},
            {data: "activeC", className: "dt-body-center"},
            {data: "reactiveA", className: "dt-body-center"},
            {data: "reactiveB", className: "dt-body-center"},
            {data: "reactiveC", className: "dt-body-center"},
            {data: "activePositiveConsumption", className: "dt-body-center"},
        ],
        serverSide: true,
        sorting: [],
        pagingType: "numbers",
        searching: true,
        deferLoading: 1,
        buttons: [
            {
                extend: 'excel',
                //messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.',
                //title: 'Data export'
            },
            'pdf',
            'print'
        ],
        ajax: {
            type: 'POST',
            data: function (d) {
                d.device = device;
                d.init   = start.format("YYYY-MM-DD");
                d.finish = end.format("YYYY-MM-DD");
                d.type   = $(".type").val();
                d.min    = $("#min").val();
                d.max    = $("#max").val();
            },
            url: "/energia/data",
            error: function () {
                notifyError("Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.");
                $("#dt-abnormal").dataTable().fnProcessingIndicator(false);
                $("#dt-abnormal_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    if ($("#dt-data")) {
        let dtData = $("#dt-data").DataTable({
            dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
            processing: true,
            paging: true,
            columns: [
                {data: "date", className: "dt-body-center"},
                {data: "activePositive", className: "dt-body-center"},
                {data: "voltageA", className: "dt-body-center"},
                {data: "voltageB", className: "dt-body-center"},
                {data: "voltageC", className: "dt-body-center"},
                {data: "currentA", className: "dt-body-center"},
                {data: "currentB", className: "dt-body-center"},
                {data: "currentC", className: "dt-body-center"},
                {data: "activeA", className: "dt-body-center"},
                {data: "activeB", className: "dt-body-center"},
                {data: "activeC", className: "dt-body-center"},
                {data: "reactiveA", className: "dt-body-center"},
                {data: "reactiveB", className: "dt-body-center"},
                {data: "reactiveC", className: "dt-body-center"},
                {data: "activePositiveConsumption", className: "dt-body-center"},
            ],
            serverSide: true,
            sorting: [],
            order: [[0, 'desc']],
            pagingType: "numbers",
            pageLength: 36,
            searching: true,
            buttons: [
                {
                    extend: 'excel',
                    //messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.',
                    //title: 'Data export'
                },
                'pdf',
                'print'
            ],
            ajax: {
                type: 'POST',
                data: function (d) {
                    d.device = device;
                    d.init = start.format("YYYY-MM-DD");
                    d.finish = end.format("YYYY-MM-DD");
                },
                url: "/energia/data",
                error: function () {
                    notifyError("Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.");
                    $("#dt-data").dataTable().fnProcessingIndicator(false);
                    $("#dt-data_wrapper .table-responsive").removeClass("processing");
                },
            },
        });
    }

    let dtResume = $("#dt-resume").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-6"B><"col-md-6"p>>',
        processing: true,
        paging: true,
        columns: [
            {data: "device", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "value_read", className: "dt-body-center"},
            {data: "value_month", className: "dt-body-center"},
            {data: "value_month_open", className: "dt-body-center"},
            {data: "value_month_closed", className: "dt-body-center"},
            {data: "value_ponta", className: "dt-body-center"},
            {data: "value_fora", className: "dt-body-center"},
            {data: "value_last", className: "dt-body-center"},
            {data: "value_future", className: "dt-body-center"},
        ],
        serverSide: true,
        sorting: [],
        order: [[ 1, 'asc' ]],
        pagingType: "numbers",
        pageLength: 36,
        searching: true,
        buttons: [
            {
                extend: 'excel',
                //messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.',
                //title: 'Data export'
            },
            'pdf',
            'print'
        ],
        ajax: {
            type: 'POST',
            url: "/energia/resume",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-resume").dataTable().fnProcessingIndicator(false);
                $("#dt-resume_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    $(".btn-view").on("click", function (event) {
        setTimeout(function() {
            $('#dt-abnormal').DataTable().ajax.reload();
        }, 100);
    });

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

    // **
    // * Handler Row click linha
    // * Abre página do shopping
    // **
    $("#dt-resume tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        let data = dtResume.row(this).data();
        $("#sel-device option[value=" + data.device + "]").attr('selected', 'selected');
        $('#sel-device').trigger('change');
        $('.nav-pills button[data-bs-target="#charts"]').tab('show');
    });

    $('#sel-device').trigger('change');

    (function () {

        "use strict";

        var dtAlerts = $("#dt-alerts").DataTable({
            dom: '<"table-responsive"t>r<"row"<"col-md-12"p>>',
            processing: true,
            columns: [
                {data: "tipo", className: "dt-body-center", orderable: false},
                {data: "titulo"},
                {data: "enviada", className: "dt-body-center"},
                {data: "actions", className: "dt-body-center", orderable: false},
            ],
            serverSide: true,
            sorting: [],
            pagingType: "numbers",
            pageLength: 20,
            ajax: {
                type: "POST",
                url : $("#dt-alerts").data("url"),
                data: function (d) {
                    d.fid = $(".btn-download").data("id");
                },
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-alerts").dataTable().fnProcessingIndicator(false);
                    $("#dt-alerts .table-responsive").removeClass("processing");
                },
            },
            fnDrawCallback: function(oSettings) {
                $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
                if ($('#dt-alerts tbody tr').hasClass('unread'))
                    $('#dt-alerts_paginate').prepend('<div class="select-all"><a href="#" class="mark-all">Marcar todos como lidos</a></div>')
            }
        });

        $(".btn-alert-config").on("click", function (event) {
            window.location.href = "/shopping/configuracoes/" + $(".page-header").data("group") + "#alertas";
        });

        $('#dt-alerts tbody').on('click', 'tr', function (event) {

            if (event.target.cellIndex == undefined || event.target.cellIndex == 4) return;

            var data = dtAlerts.row( this ).data();
            var $row = $(this);

            $.magnificPopup.open( {
                items: {src: '/energia/ShowAlert'},
                type: 'ajax',
                modal:true,
                ajax: {
                    settings: {
                        type: 'POST',
                        data: { id: data.DT_RowId }
                    }
                },
                callbacks: {
                    close: function() {
                        // mostra action
                        $('.action-delete').filter('[data-id="'+data.DT_RowId+'"]').removeClass('d-none')
                        // atualiza badge se necessário
                        if ($row.hasClass('unread')) {
                            var $count = $('.badge-alerta').attr('data-count') - 1;
                            $('.badge-alerta').attr('data-count', $count).html($count);
                        }
                        // remove destaque da linha
                        $row.removeClass('unread');
                    }
                }
            });
        });

        // **
        // * Handler Fechar Modal
        // **
        $(document).on('click', '.modal-dismiss', function (e) {
            // para propagação
            e.preventDefault();
            // fecha a modal
            $.magnificPopup.close();
        });

        // **
        // * Handler Action Excluir Alerta
        // **
        $(document).on('click', '#dt-alerts .action-delete', function () {

            var $btn = $(this);
            $btn.html('<i class="fas fa-spinner fa-spin"></i>');

            // faz a requisição
            $.post("/energia/DeleteAlert", { id: $(this).data('id') }, function(json) {
                if (json.status == 'success') {
                    // remove linha
                    $('#dt-alerts tr#' + json.id).hide('slow', function(){ $(this).remove(); });
                    // mostra notificação
                    notifySuccess(json.message);
                } else {
                    $btn.html('<i class="fas fa-trash"></i>');
                    // mostra erro
                    notifyError(json.message);
                }
            }, 'json')
                .fail(function(xhr, status, error) {
                    $btn.html('<i class="fas fa-trash"></i>');
                    notifyError(error, 'Ajax Error');
                });
        });

        $(document).on("click", 'a.mark-all', function() {

            // faz a requisição
            $.post("/energia/ReadAllAlert", function(json) {
                if (json.status == 'success') {
                    // remove destaque da linha
                    $('#dt-alerts tbody tr').removeClass('unread');
                    // mostra actions
                    $('#dt-alerts .action-delete').removeClass('d-none');
                    // reset badge
                    $('.badge-alerta').attr('data-count', 0)
                    // esconde link
                    $('.select-all').remove();
                    // mostra notificação
                    notifySuccess(json.message);
                } else {
                    // mostra erro
                    notifyError(json.message);
                }
            }, 'json')
                .fail(function(xhr, status, error) {
                    // mostra erro
                    notifyError(error, 'Ajax Error');
                });
        });

    }.apply(this, [jQuery]));

    let dtFaturamentos = $("#dt-faturamentos").DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "competencia", class: "dt-body-center"},
            {data: "inicio", class: "dt-body-center" },
            {data: "fim", class: "dt-body-center"},
            {data: "consumo", class: "dt-body-center"},
            {data: "consumo_p", class: "dt-body-center"},
            {data: "consumo_f", class: "dt-body-center"},
            {data: "demanda_p", class: "dt-body-center"},
            {data: "demanda_f", class: "dt-body-center"},
            {data: "fracao_consumo", class: "dt-body-center"},
            {data: "emissao", class: "dt-body-center"},
            {data: "action", class: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        sorting: [],
        pageLength: 10,
        pagingType: "numbers",
        searching: false,
        ajax: {
            type: 'POST',
            url: $("#dt-faturamentos").data("url"),
            data: function (d) {
                d.gid = $("#dt-faturamentos").data("group");
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-faturamentos").dataTable().fnProcessingIndicator(false);
                $("#dt-faturamentos_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

}).apply(this, [jQuery]);