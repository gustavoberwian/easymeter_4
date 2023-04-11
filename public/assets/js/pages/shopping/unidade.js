(function($) {

    'use strict';

    //Fazer download em excel energia

    $(document).on("click", ".action-download", function () {

        var $btn = $(this);
		$btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
		$.post("/energia/download", { id: $(this).data('id') }, function(json) {

            if (json.status == "error") {
                    
                notifyError(json.message);

            } else {

                var $a = $("<a>");
                $a.attr("href", json.file);
                $("body").append($a);
                $a.attr("download", json.name + '.xlsx');
                $a[0].click();
                $a.remove();
            }
		}, 'json')
		.fail(function(xhr, status, error) {
			// mostra erro
            notifyError(error, 'Ajax Error');
        })
		.always(function() {
			// oculta indicador e habilita botão
			$btn.html('<i class="fas fa-file-download"></i>');
        });
    });

    //Fazer download excel agua

    $(document).on("click", ".action-water-download", function () {

        var $btn = $(this);
		$btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
		$.post("/water/download", { id: $(this).data('id') }, function(json) {

            if (json.status == "error") {
                    
                notifyError(json.message);

            } else {

                var $a = $("<a>");
                $a.attr("href", json.file);
                $("body").append($a);
                $a.attr("download", json.name + '.xlsx');
                $a[0].click();
                $a.remove();
            }
		}, 'json')
		.fail(function(xhr, status, error) {
			// mostra erro
            notifyError(error, 'Ajax Error');
        })
		.always(function() {
			// oculta indicador e habilita botão
			$btn.html('<i class="fas fa-file-download"></i>');
        });
    });

    //Fazer download lancamentos energia

    $(document).on("click", ".btn-download", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/energia/DownloadLancamentos", {id: $(this).data("group")}, function (json) {

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

    //Fazer download lancamentos agua

    $(document).on("click", ".btn-water-download", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/water/DownloadLancamentos", {id: $(this).data("group")}, function (json) {

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

    function CallAlerts() {
        return {
            initialize: function (table, columns, url){
                this
                    .options(table, columns, url)
                    .setVars()
                    .build()
                    .events();
            },

            options: function (table, columns, url) {
                this.$options = {
                    table: table,
                    columns: columns,
                    url: url
                }

                return this;
            },

            setVars: function () {
                this.table = this.$options.table;
                this.columns = this.$options.columns;
                this.url = this.$options.url;
                this.$table = $(this.$options.table);
                this.$columns = $(this.$options.columns);

                return this;
            },

            build: function () {
                let _self = this;

                this.datatable = this.$table.DataTable({
                    dom: '<"table-responsive"t>r<"row"<"col-md-12"p>>',
                    processing: true,
                    columns: _self.$columns,
                    serverSide: true,
                    sorting: [],
                    pagingType: "numbers",
                    pageLength: 20,
                    ajax: {
                        type: "POST",
                        url: _self.$table.data("url"),
                        data: {
                            fid: $(".btn-download").data("id"),
                            monitoramento: _self.$table.data("tipo"),
                            group: $(".page-header").data("group")
                        },
                        error: function () {
                            notifyError(
                                "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                            );
                            _self.$table.dataTable().fnProcessingIndicator(false);
                            $(_self.table + " .table-responsive").removeClass("processing");
                        },
                    },
                    fnDrawCallback: function() {
                        $(_self.table + "_wrapper .table-responsive").removeClass("processing");
                        if ($(_self.table + ' tbody tr').hasClass('unread') && !$('.dataTables_paginate').children().hasClass('select-all'))
                            $(_self.table + '_paginate').prepend('<div class="select-all"><a class="mark-all' + _self.$table.data("tipo") + ' cur-pointer">Marcar todos como lidos</a></div>')
                    }
                });

                window.dt = this.datatable;

                return this;
            },

            events: function () {
                let _self = this;

                // duplica thead
                $(_self.table + ' thead tr').clone(true).appendTo( _self.table + ' thead' ).addClass('filter');

                // adiciona campos de filtro
                $(_self.table + ' thead tr:eq(1) th.filter').each( function (i) {

                    $(this).html( '<input type="text" class="form-control input-block" value="">' );

                    $( 'input', this ).on( 'keyup change', function () {
                        if ( _self.datatable.column(i).search() !== this.value ) {
                            _self.datatable
                                .column(i+1)
                                .search( this.value )
                                .draw();
                        }
                    } );
                } );

                // limpa campos que não são filtros
                $(_self.table + ' thead tr:eq(1) th:not(.filter)').each( function () {
                    $(this).text('')
                });

                // inclui botão limpar filtros
                $(_self.table + ' thead tr:eq(1) th:eq(6)').html('<a href="" class="clear-filter" title="Limpa filtros"><i class="fas fa-times"></i></a>').addClass('actions text-center');

                // handler botão limpar filtros
                $('.clear-filter').on('click', function () {
                    $(_self.table + ' thead tr:eq(1) th.filter input').each( function () {
                        this.value = '';
                    });
                    _self.datatable.columns().search('').draw();
                });

                $(_self.table + ' tbody').on('click', 'tr', function (event) {

                    if (event.target.cellIndex === undefined || event.target.cellIndex === 6) return;

                    let data = _self.datatable.row( this ).data();
                    let $row = $(this);

                    $.magnificPopup.open( {
                        items: {src: '/shopping/ShowAlert'},
                        type: 'ajax',
                        modal:true,
                        ajax: {
                            settings: {
                                type: 'POST',
                                data: { id: data.DT_RowId, monitoramento: _self.$table.data("tipo") }
                            }
                        },
                        callbacks: {
                            close: function() {
                                // mostra action
                                $('.action-delete').filter('[data-id="'+data.DT_RowId+'"]').removeClass('d-none')
                                // atualiza badge se necessário
                                if ($row.hasClass('unread')) {
                                    let $badge = $('.badge-alerta');
                                    let $count = $badge.attr('data-count') - 1;
                                    $badge.attr('data-count', $count).html($count);
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
                $(document).on('click', _self.table + ' .action-delete', function () {

                    let $btn = $(this);
                    $btn.html('<i class="fas fa-spinner fa-spin"></i>');

                    // faz a requisição
                    $.post(
                        "/shopping/DeleteAlert",
                        {
                            id: $(this).data('id'),
                            monitoramento: _self.$table.data('tipo'),
                        },
                        function(json) {
                            if (json.status == 'success') {
                                // remove linha
                                _self.datatable.draw();
                                // mostra notificação
                                notifySuccess(json.message);
                            } else {
                                $btn.html('<i class="fas fa-trash"></i>');
                                // mostra erro
                                notifyError(json.message);
                            }
                        }, 'json').fail(function(xhr, status, error) {
                            $btn.html('<i class="fas fa-trash"></i>');
                            notifyError(error, 'Ajax Error');
                        }
                    );
                });

                $(document).on("click", 'a.mark-all' + _self.$table.data("tipo"), function() {

                    // faz a requisição
                    $.ajax({
                        method : 'POST',
                        url : "/shopping/ReadAllAlert",
                        data : { monitoramento : _self.$table.data("tipo") },
                        dataType : 'json',
                        success : function(json) {
                            if (json.status == 'success') {
                                _self.datatable.draw();
                                // mostra notificação
                                notifySuccess(json.message);
                            } else {
                                // mostra erro
                                notifyError(json.message);
                            }
                        },
                        error : function(xhr, status, error) {
                            // mostra erro
                            notifyError(error, 'Ajax Error');
                        }
                    });
                })

                _self.$table
                    .on('click', '', function (e) {

                    })
            }
        }
    }

    let energyAlertsTable = new CallAlerts();
    energyAlertsTable.initialize("#dt-alerts-energia",
        [
            {data: "type", className: "dt-body-center", orderable: false},
            {data: "tipo", className: "dt-body-center", orderable: false},
            {data: "device", className: "dt-body-center filter"},
            {data: "nome", className: "filter" },
            {data: "titulo"},
            {data: "enviada", className: "dt-body-center"},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
    );

    let waterAlertsTable = new CallAlerts();
    waterAlertsTable.initialize("#dt-alerts-water",
        [
            {data: "type", className: "dt-body-center", orderable: false},
            {data: "tipo", className: "dt-body-center", orderable: false},
            {data: "device", className: "dt-body-center filter"},
            {data: "nome", className: "filter" },
            {data: "titulo"},
            {data: "enviada", className: "dt-body-center"},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
    );


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
                field   : el.data("field"),
                group   : $('.page-header').data("group")
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
                d.group  = $(".page-header").data("group");
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
                    d.group  = $(".page-header").data("group");
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

        // **
	// * Handler Action Abrir modal confirmação para excluir fechamento
	// **
	$(document).on('click', '.action-delete', function () {

        var dis_timer, id = $(this).data('id');
        
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
                    $('#modalExclui .id').val( id );
                    $('#modalExclui .type').val('energia');
                },
                open: function() {
                    // desabilita botão
                    var btn = $('#modalExclui .modal-confirm');
                    btn.prop("disabled", true);
                    // inicializa timer
                    var sec = btn.data('timer');
                    // declaração do timer regressimo
                    function countDown() {
                        // mostra valor
                        btn.html(sec);
                        if (sec <= 0) {
                            // terminou. Habilita botão e atualiza texto
                            btn.prop("disabled", false);
                            btn.html('Excluir');
                            return;
                        }
                        // continua contando
                        sec -= 1;
                        dis_timer = setTimeout(countDown, 1000);
                    }
                    countDown();
                },
                close: function() {
                    clearTimeout(dis_timer);
                }
			}
		});
	});

    // **
	// * Handler Action Abrir modal confirmação para excluir fechamento
	// **
	$(document).on('click', '.action-water-delete', function () {

        var dis_timer, id = $(this).data('id');
        
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
                    $('#modalExclui .id').val( id );
                    $('#modalExclui .type').val('water');
                },
                open: function() {
                    // desabilita botão
                    var btn = $('#modalExclui .modal-confirm');
                    btn.prop("disabled", true);
                    // inicializa timer
                    var sec = btn.data('timer');
                    // declaração do timer regressimo
                    function countDown() {
                        // mostra valor
                        btn.html(sec);
                        if (sec <= 0) {
                            // terminou. Habilita botão e atualiza texto
                            btn.prop("disabled", false);
                            btn.html('Excluir');
                            return;
                        }
                        // continua contando
                        sec -= 1;
                        dis_timer = setTimeout(countDown, 1000);
                    }
                    countDown();
                },
                close: function() {
                    clearTimeout(dis_timer);
                }
			}
		});
	});

    // **
	// * Handler Button excluir fechamento
	// **
	$(document).on('click', '#modalExclui .modal-confirm', function (e) {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
		var id = $('#modalExclui .id').val();
        var type = $('#modalExclui .type').val();
		// faz a requisição
		$.post("/"+type+"/DeleteLancamento", {id: id}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
				dtFaturamentos.ajax.reload( null, false );
                dtWater.ajax.reload( null, false );
				// mostra notificação
				notifySuccess(json.message);
			} else {
				// fecha modal
				$.magnificPopup.close();
				// mostra erro
				notifyError(json.message);
			}
		}, 'json')
		.fail(function(xhr, status, error) {
			// fecha modal
			$.magnificPopup.close();
			// mostra erro
			notifyError(error, 'Ajax Error');
  		})
		.always(function() {
			// oculta indicador e habilita botão
			$btn.trigger('loading-overlay:hide');
			// habilita botões
			$btn_d.prop('disabled', false);
			// limpa id
			$('#modalExclui .id').val('');
            $('#modalExclui .type').val('');
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
            {data: "demanda", class: "dt-body-center"},
            {data: "consumo_u", class: "dt-body-center"},
            {data: "consumo_u_p", class: "dt-body-center"},
            {data: "consumo_u_f", class: "dt-body-center"},
            {data: "demanda_u", class: "dt-body-center"},
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

    let dtWater = $("#dt-water").DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "competencia", class: "dt-body-center"},
            {data: "inicio", class: "dt-body-center" },
            {data: "fim", class: "dt-body-center"},
            {data: "consumo", class: "dt-body-center"},
            {data: "consumo_o", class: "dt-body-center"},
            {data: "consumo_c", class: "dt-body-center"},
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
            url: $("#dt-water").data("url"),
            data: function (d) {
                d.gid = $("#dt-water").data("group");
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-water").dataTable().fnProcessingIndicator(false);
                $("#dt-water_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

}).apply(this, [jQuery]);