(function() {

    'use strict';

    // ***********************************************************************************************
    // * Página Unidades/Consumo
    // ***********************************************************************************************
    var bar;
    var d_start = moment().subtract(6, 'days');
    var d_end = moment();
    /*
        if ($('.page-header').data('central').toString().startsWith('53')) {
            d_start = moment().subtract(7, 'days');
            d_end = moment().subtract(1, 'days');
        }
    */
    var bar_mode = 'zoom-in';
    if ($('.page-header').data('start')) {
        d_start = moment.unix($('.page-header').data('start'));
        d_end = moment.unix($('.page-header').data('end'));
    }

    var bar_update = function() {
        $('.chart-bar-body').trigger('loading-overlay:show');
        $.ajax({
            method: 'POST',
            url: (bar_mode == 'zoom-out') ? '/ajax/get_bar_chart_detail_3' : '/ajax/get_bar_chart_3',
            data: { type: $('.page-header').data('monitoramento'), uid: $('.page-header').data('unidade'), start: d_start.format('YYYY-MM-DD'), end: d_end.format('YYYY-MM-DD') },
            dataType: 'json',
            success: function(json) {

                if (json.data.hasOwnProperty("extra")) {
                    if (bar_mode == 'zoom-out') {
                        json.options.plugins.tooltip.callbacks.title = function(tooltipItems, data) {
                            return json.data.extra[ tooltipItems[0].dataIndex ];
                        };
                    } else {
                        json.options.plugins.tooltip.callbacks.title = function(tooltipItems) {
                            return tooltipItems[0].label + ' - ' + json.data.extra[ tooltipItems[0].dataIndex ];
                        };
                    }
                    json.options.plugins.tooltip.callbacks.label = function(context) {
                        if (context.parsed.y === null)
                            return "";

                        let dec = (context.parsed.y > 0 && context.parsed.y < 1 ) ? 3 : json.data.decimals;
                        return context.dataset.label + ": " + context.parsed.y.toLocaleString("pt-BR", {minimumFractionDigits: dec, maximumFractionDigits: dec}) + " L";
                    };
                }

                /*json.options.onHover = function(event, elements) {
                    event.native.target.style.cursor = elements.length > 0 ? "pointer" : "default";
                };*/

                if (bar) {
                    bar.data = json.data;
                    bar.options = json.options
                    bar.update();
                } else {
                    bar = new Chart($('#bar-chart'), {
                        type: 'bar',
                        data: json.data,
                        options: json.options
                    });
                }
            },
            error: function(xhr, status, error) {
                notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                return false;
            },
            complete: function() {
                $('.chart-bar-body').trigger('loading-overlay:hide');
            }
        });
    };


    bar_update();

    document.getElementById("bar-chart").onclick = function(evt){
        var activePoints = bar.getElementsAtEventForMode(evt, 'index', { intersect: true }, false);
        var url;
        var d_l_start, d_l_end;

        if (activePoints.length > 0) {
            $('.chart-bar-body').trigger('loading-overlay:show');

            if (bar_mode == 'zoom-in') {
                bar_mode = 'zoom-out';
                d_l_start = d_start.clone().add(activePoints[0].index, 'days');
                d_l_end = d_l_start.clone();
                url = '/ajax/get_bar_chart_detail_3';
            } else {
                bar_mode = 'zoom-in';
                d_l_start = d_start.clone();
                d_l_end = d_end.clone();
                url = '/ajax/get_bar_chart_3';
            }

            $.ajax({
                method: 'POST',
                url: url,
                data: { type: 'agua', uid: $('.page-header').data('unidade'), start: d_l_start.format('YYYY-MM-DD'), end: d_l_end.format('YYYY-MM-DD') },
                dataType: 'json',
                success: function(json) {
                    // atualiza grafico
                    bar.data = json.data;
                    bar.options = json.options

                    if (json.data.hasOwnProperty("extra")) {
                        if (bar_mode == 'zoom-out') {
                            json.options.plugins.tooltip.callbacks.title = function(tooltipItems, data) {
                                return json.data.extra[ tooltipItems[0].dataIndex ];
                            };
                        } else {
                            json.options.plugins.tooltip.callbacks.title = function(tooltipItems) {
                                return tooltipItems[0].label + ' - ' + json.data.extra[ tooltipItems[0].dataIndex ];
                            };
                        }
                        json.options.plugins.tooltip.callbacks.label = function(context) {
                            if (context.parsed.y === null)
                                return "";

                            let dec = (context.parsed.y > 0 && context.parsed.y < 1 ) ? 3 : json.data.decimals;
                            return context.dataset.label + ": " + context.parsed.y.toLocaleString("pt-BR", {minimumFractionDigits: dec, maximumFractionDigits: dec}) + " L";
                        };
                    }

                    // mostra pointer nas barras
                    json.options.onHover = function(event, elements) {
                        event.native.target.style.cursor = elements.length > 0 ? "pointer" : "default";
                    };
                    // atualiza grafico
                    bar.update();
                    //atualiza range button
                    if (bar_mode == 'zoom-in')
                        $('#daterange span').html(d_l_start.format('DD/MM/YYYY') + ' - ' + d_l_end.format('DD/MM/YYYY'));
                    else
                        $('#daterange span').html(d_l_start.format('ddd, DD/MM/YYYY'));
                    $('#daterange').data('daterangepicker').setStartDate(d_l_start);
                    $('#daterange').data('daterangepicker').setEndDate(d_l_end);
                    // reseta filtro
                    $('.filter').removeClass('selected').children().addClass('fa-none').removeClass('fa-check');
                    $('.filter').filter('[data-filter="todos"]').addClass('selected').children().addClass('fa-check').removeClass('fa-none');
                },
                error: function(xhr, status, error) {
                    notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                    return false;
                },
                complete: function() {
                    $('.chart-bar-body').trigger('loading-overlay:hide');
                }
            });
        }
    };

    // **
    // * Handler Menu Seleção de Entradas
    // **
    $(document).on('click',".dropdown-menu-config .filter", function (e) {
        e.preventDefault();
        $('.filter').removeClass('selected').children().addClass('fa-none').removeClass('fa-check');
        $(this).addClass('selected').children().addClass('fa-check').removeClass('fa-none');
        if( $(this).data('filter') == 'todos' ) {
            bar.getDatasetMeta(0).hidden = null;
            bar.getDatasetMeta(1).hidden = null;
            bar.update();
        } else {
            for (var i = 0; i < bar.data.datasets.length; i++) {
                bar.getDatasetMeta(i).hidden = true;
            }
            bar.getDatasetMeta($('.filter.selected').data('filter')).hidden = null;
            bar.update();
        }
    });

    let range_list = {
        'Hoje': [moment(), moment()],
        'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
        'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
        'Este Mês': [moment().startOf('month'), moment().endOf('month')],
        'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    };
    /*
        if ($('.page-header').data('central').toString().startsWith('53')) {
            range_list = {
                'Último dia': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 dias': [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
                'Últimos 30 dias': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            };
        }
    */
    $('#daterange').daterangepicker({
            startDate: d_start,
            endDate: d_end,
            minDate: primeira_leitura,
//        maxDate: ($('.page-header').data('central').toString().startsWith('53')) ? moment().subtract(1, 'days').format('DD/MM/YYYY') : moment().format('DD/MM/YYYY'),
            maxDate: moment().format('DD/MM/YYYY'),
            maxSpan: { "days": 60 },
            opens: "left",
            ranges: range_list,
            locale: {
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "De",
                "toLabel": "até",
                "customRangeLabel": "Personalizado"
            }
        },
        function(start, end, label) {
            bar_mode = (label == 'Hoje' || label == 'Ontem') ? 'zoom-out' : 'zoom-in';

            // atualiza botão daterange
            if (bar_mode == 'zoom-in')
                $('#daterange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            else
                $('#daterange span').html(start.format('ddd, DD/MM/YYYY'));
            // atualiza datas e modo
            d_start = start;
            d_end = end;

            // reseta filtro
            $('.filter').removeClass('selected').children().addClass('fa-none').removeClass('fa-check');
            $('.filter').filter('[data-filter="todos"]').addClass('selected').children().addClass('fa-check').removeClass('fa-none');
            // atualiza grafico
            bar_update();
        });

    $('#daterange span').html(d_start.format('DD/MM/YYYY') + ' - ' + d_end.format('DD/MM/YYYY'));

    var dtFaturamentos = $('#dt-faturamentos').DataTable({
        dom: '<"table-responsive"t>rp',
        processing: true,
        columns: [ {data: "id", visible: false},
            { data: "competencia", className: 'dt-body-center' }, { data: "consumo", className: 'dt-body-center' },
            { data: "v_basico", className: 'dt-body-right' }, { data: "v_acomum", className: 'dt-body-right' },
            { data: "v_taxas", className: 'dt-body-right' }, { data: "v_gestao", className: 'dt-body-right' },
            { data: "v_total", className: 'dt-body-right font-weight-bold' }, { data: "action", orderable: false, className: "dt-body-center actions"} ],
        serverSide: true,
        sorting: [],
        pageLength: 7,
        pagingType: "numbers",
        searching: false,
        ajax: {
            url: $('#dt-faturamentos').data('url'),
            data: function ( d ) {
                return $.extend( {}, d, {
                    uid: $('.page-header').data('unidade'),
                    cid: $('.page-header').data('condo')
                } );
            },
            error: function () {
                notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-faturamentos').dataTable().fnProcessingIndicator(false);
                $('#dt-faturamentos_wrapper .table-responsive').removeClass('processing');
            }
        },
        fnPreDrawCallback: function() { $('#dt-faturamentos_wrapper .table-responsive').addClass('processing'); },
        fnDrawCallback: function() { $('#dt-faturamentos_wrapper .table-responsive').removeClass('processing'); $('[data-toggle="tooltip"]').tooltip(); },
        initComplete: (settings, json) => {
            $('#dt-faturamentos_paginate').appendTo('.card-faturamentos .card-footer');
        }
    });

    // **
    // * Handler Action Visualizar Gráfico
    // **
    $(document).on('click',".actions .action-grafico", function (e) {

        e.preventDefault();

        $('.chart-bar-body').trigger('loading-overlay:show');

        // trata periodo
        d_start = moment($(this).data('ini')  * 1000);
        d_end = moment($(this).data('fim') * 1000);

        $.ajax({
            method: 'POST',
            url: '/ajax/get_bar_chart_3',
            data: { type: 'agua', uid: $('.page-header').data('unidade'), start: d_start.format('YYYY-MM-DD'), end: d_end.format('YYYY-MM-DD') },
            dataType: 'json',
            success: function(json) {
                // seta mode
                bar_mode = 'zoom-in';
                // atualiza grafico
                bar.data = json.data;
                bar.options = json.options
                json.options.plugins.tooltip.callbacks.title = function(tooltipItems) {
                    return tooltipItems[0].label + ' - ' + json.data.extra[ tooltipItems[0].dataIndex ];
                };
                // mostra pointer nas barras
                json.options.onHover = function(event, elements) {
                    event.native.target.style.cursor = elements.length > 0 ? "pointer" : "default";
                };
                // atualiza grafico
                bar.update();
                //atualiza range button
                $('#daterange span').html(d_start.format('DD/MM/YY') + ' - ' + d_end.format('DD/MM/YY'));
                $('#daterange').data('daterangepicker').setStartDate(d_start);
                $('#daterange').data('daterangepicker').setEndDate(d_end);
                // reseta filtro
                $('.filter').removeClass('selected').children().addClass('fa-none').removeClass('fa-check');
                $('.filter').filter('[data-filter="todos"]').addClass('selected').children().addClass('fa-check').removeClass('fa-none');
                // vai até o grafico
                $([document.documentElement, document.body]).animate({
                    scrollTop: $(".card-chart-bar").offset().top
                }, 1000);
            },
            error: function(xhr, status, error) {
                notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                return false;
            },
            complete: function() {
                $('.chart-bar-body').trigger('loading-overlay:hide');
            }
        });
    });

    $(document).on('click',".actions .action-visualiza", function (e) {

        e.preventDefault();

        $.magnificPopup.open( {
            items: {src: '/ajax/md_fechamento_unidade'},
            type: 'ajax',
            focus: '#id-bloco',
            modal:true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {
                        fid: $(this).data('fid'),
                        uid: $(this).data('uid'),
                        origem: 'admin'
                    }
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

    let his;
    let his_gas;
    let chart_update_agua = function(period = 'last') {
        $('.chart-bar-body-comp').trigger('loading-overlay:show');
        $.ajax({
            method: 'POST',
            url: '/ajax/get_chart_fechamentos',
            data: { uid: $('.page-header').data('unidade'), p: period, m: 'agua' },
            dataType: 'json',
            success: function(json) {

                if (!json.nodata) {

                    if (json.data.hasOwnProperty("extra")) {

                        $(".h6.total").html(json.data.extra.total);
                        $(".h6.medio").html(json.data.extra.media);
                        $(".h6.maximo").html(json.data.extra.max);
                        $(".h6.minimo").html(json.data.extra.min);
                    }

                    json.options.plugins.tooltip.callbacks.label = function(tooltipItem) {
                        return ["Inicio: " + json.data.tooltip[tooltipItem.dataIndex].inicio,
                            "Fim: " + json.data.tooltip[tooltipItem.dataIndex].fim,
                            "Consumo: " + parseInt(json.data.tooltip[tooltipItem.dataIndex].consumo).toLocaleString("pt-BR") + " L",
                            "Valor: R$ " + parseFloat(json.data.tooltip[tooltipItem.dataIndex].valor).toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2})];
                    };

                    if (his) {
                        his.data = json.data;
                        his.options = json.options
                        his.update();
                    } else {
                        his = new Chart($('#chart-comp'), {
                            type: 'bar',
                            data: json.data,
                            options: json.options
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                return false;
            },
            complete: function() {
                $('.chart-bar-body-comp').trigger('loading-overlay:hide');
            }
        });
    }

    chart_update_agua();

    // **
    // * Handler Menu Seleção Ano Água
    // **
    $(document).on('click',".dropdown-menu-unit .filter", function (e) {
        e.preventDefault();
        $('.filter').removeClass('selected').children().addClass('fa-none').removeClass('fa-check');
        $(this).addClass('selected').children().addClass('fa-check').removeClass('fa-none');
        $(".btn-year").text($(this).text());

        chart_update_agua($(this).data("filter"));
    });

    if ($('#chart-comp-gas').length) {

        let chart_update_gas = function(period = 'last') {
            $('.chart-bar-body-comp-gas').trigger('loading-overlay:show');
            $.ajax({
                method: 'POST',
                url: '/ajax/get_chart_fechamentos',
                data: { uid: $('.page-header').data('unidade'), p: period, m: 'gas' },
                dataType: 'json',
                success: function(json) {

                    if (!json.nodata) {

                        if (json.data.hasOwnProperty("extra")) {

                            $(".h6.total-gas").html(json.data.extra.total + " M&sup3;");
                            $(".h6.medio-gas").html(json.data.extra.media + " M&sup3;");
                            $(".h6.maximo-gas").html(json.data.extra.max + " M&sup3;");
                            $(".h6.minimo-gas").html(json.data.extra.min + " M&sup3;");
                        }

                        if (his_gas) {
                            his_gas.data = json.data;
                            his_gas.options = json.options
                            his_gas.update();
                        } else {
                            his_gas = new Chart($('#chart-comp-gas'), {
                                type: 'bar',
                                data: json.data,
                                options: json.options
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                    return false;
                },
                complete: function() {
                    $('.chart-bar-body-comp-gas').trigger('loading-overlay:hide');
                }
            });
        }

        chart_update_gas();

        // **
        // * Handler Menu Seleção Ano Gás
        // **
        $(document).on('click',".dropdown-menu-gas .filter-gas", function (e) {
            e.preventDefault();
            $('.filter-gas').removeClass('selected').children().addClass('fa-none').removeClass('fa-check');
            $(this).addClass('selected').children().addClass('fa-check').removeClass('fa-none');
            $(".btn-year-gas").text($(this).text());

            chart_update_gas($(this).data("filter"));
        });
    }
    // **
    // * Configuração Datatable Proprietarios
    // **
    let dtProprietarios = $('#dt-proprietarios').DataTable({
        dom: '<"table-responsive"t>rp',
        pagingType: "numbers",
        sorting: [],
        processing: true,
        serverSide: true,
        searching: false,
        columns: [ { data: "nome" }, { data: "username" }, { data: "telefone", className: "dt-body-center" }, { data: "situacao", className: "dt-body-center" },
            { data: "cadastro", className: "dt-body-center" },{ data: "action", className: "actions dt-body-center", orderable: false }
        ],
        ajax: {
            url: $('#dt-proprietarios').data('url'),
            error: function () {
                notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-proprietarios').dataTable().fnProcessingIndicator(false);
                $('#dt-proprietarios_wrapper .table-responsive').removeClass('processing');
            }
        },
        fnPreDrawCallback: function() { $('#dt-proprietarios_wrapper .table-responsive').addClass('processing'); },
        fnDrawCallback: function() { $('#dt-proprietarios_wrapper .table-responsive').removeClass('processing'); }
    });

    // **
    // * Configuração Datatable Convites
    // **
    let dtConvites = $('#dt-convites').DataTable({
        dom: '<"table-responsive"t>rp',
        pagingType: "numbers",
        sorting: [[ 0, "desc" ]],
        processing: true,
        serverSide: true,
        searching: false,
        columns: [ { data: "nome" }, { data: "email" }, { data: "situacao", className: "dt-body-center" }, { data: "permissoes", className: "dt-body-center", orderable: false },
            { data: "enviado_por", className: "dt-body-center" }, { data: "cadastro", className: "dt-body-center" }, { data: "action", className: "actions dt-body-center", orderable: false }
        ],
        ajax: {
            url: $('#dt-convites').data('url'),
            error: function () {
                notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-convites').dataTable().fnProcessingIndicator(false);
                $('#dt-convites_wrapper .table-responsive').removeClass('processing');
            }
        },
        fnPreDrawCallback: function() { $('#dt-convites_wrapper .table-responsive').addClass('processing'); },
        fnDrawCallback: function() { $('#dt-convites_wrapper .table-responsive').removeClass('processing'); }
    });

    // **
    // * Handler Action ativar/desativar proprietário
    // **
    $(document).on('click', '.action-change', function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) return;

        let table = "#" + $(this).closest("table").attr("id");

        // mostra indicador
        var $btn = $(this);
        var html = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');
        // desabilita botões
        $(table + ' .actions a').addClass('disabled');
        // pega o valor do id
        var id = $btn.data('uid');
        // faz a requisição
        $.post("/ajax/usuario_active", {id: id}, function(json) {
            if (json.status == 'success') {
                // atualiza tabela
                if (table == "#dt-proprietarios")
                    dtProprietarios.ajax.reload( null, false );
                else
                    dtConvites.ajax.reload( null, false );

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
            })
            .always(function() {
                // oculta indicador e habilita botão
                $btn.html(html);
                // habilita botões
                $(table + ' .actions a').removeClass('disabled');
            });
    });

    // **
    // * Handler Action redefinir senha
    // **
    $(document).on('click', '#modalConfirm .modal-confirm', function (e) {
        e.preventDefault();

        // mostra indicador
        var $btn = $(this);
        $btn.trigger('loading-overlay:show');
        // desabilita botões
        var $btn_d = $('.btn:enabled').prop('disabled', true);
        // pega o valor do id
        var uid = $('#modalConfirm .id').val();
        // faz a requisição
        $.post("/ajax/reset_password", {id: uid}, function(json) {
            if (json.status == 'success') {
                // fecha modal
                $.magnificPopup.close();
                // atualiza tabela
                dtConvites.ajax.reload( null, false );
                dtProprietarios.ajax.reload( null, false );
                // mostra notificação
                notifySuccess(json.message);
            } else {
                // fecha modal
                $.magnificPopup.close();
                // mostra erro
                notifyWarning(json.message);
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
                $('#modalConfirm .id').val('');
            });
    });

    // **
    // * Handler Action redefinir senha
    // **
    $(document).on('click', '#dt-proprietarios .action-reset', function () {

        var uid = $(this).data('uid');

        // abre a modal
        $.magnificPopup.open( {
            items: {src: '#modalConfirm'}, type: 'inline',
            callbacks: {
                beforeOpen: function() {
                    $('#modalConfirm .id').val( uid );
                }
            }
        });
    });

    // **
    // * Handler Action excluir usuário. Abre confirmação
    // **
    $(document).on('click', '.action-delete', function () {
        var cid = $(this).data('cid');
        var uid = $(this).data('uid');
        // abre a modal
        $.magnificPopup.open( {
            items: {src: '#modalExclui'}, type: 'inline',
            callbacks: {
                beforeOpen: function() {
                    $('#modalExclui .id').val( cid );
                    $('#modalExclui .id').data('uid', uid);
                }
            }
        });
    });

    // **
    // * Handler Botão Excluir Modal Confirmação Exclusão
    // **
    $(document).on('click', '#modalExclui .modal-confirm', function () {
        // mostra indicador
        var $btn = $(this);
        $btn.trigger('loading-overlay:show');
        // desabilita botões
        var $btn_d = $('.btn:enabled').prop('disabled', true);
        // pega o valor do id
        var cid = $('#modalExclui .id').val();
        var uid = $('#modalExclui .id').data('uid');
        // faz a requisição
        $.post("/ajax/delete_convite", {cid: cid, uid: uid}, function(json) {
            if (json.status == 'success') {
                // fecha modal
                $.magnificPopup.close();
                // atualiza tabela
                dtConvites.ajax.reload( null, false );
                dtProprietarios.ajax.reload( null, false );
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
                $('#modalExclui .id').val('').data('uid', null);
            });
    });

    // **
    // * Handler Botão Cadastrar Usuário: abre modal
    // **
    $(document).on('click', '.btn-cadastrar', function (e) {
        e.preventDefault();

        $.magnificPopup.open( {
            items: {src: '/ajax/md_usuario_add'},
            type: 'ajax',
            modal:true,
            focus: '#con-nome',
            ajax: {
                settings: {
                    type: 'POST',
                    data: { uid: $(this).data('uid') }
                }
            },
            callbacks: {
                ajaxContentAdded: function() {
                    $('[data-loading-overlay]').loadingOverlay();
                }
            }
        });
    });

    // **
    // * Handler Confirma Modal Cadastrar Usuário
    // **
    $(document).on('click', '.form-add .modal-confirm', function (e) {
        // para propagação
        e.preventDefault();

        if ( $(".form-add").valid() &&
            ( $("#con-prop").is(':checked') ||
                ( !$("#con-prop").is(':checked') && ( $("#con-agua").is(':checked') || $("#con-gas").is(':checked') || $("#con-energia").is(':checked') ) )
            )
        ) {

            var $btn = $(this);
            $btn.trigger('loading-overlay:show');
            // desabilita botões
            var $btn_d = $('.btn:enabled').prop('disabled', true);
            // captura dados
            var data = $('.form-add').serializeArray();
            // envia os dados
            $.post('/ajax/add_usuario', data, function(json) {
                if (json.status == 'success') {
                    // fecha a modal
                    $.magnificPopup.close();
                    // atualiza tabela
                    dtConvites.ajax.reload( null, false );
                    // mostra notificação
                    notifySuccess(json.message);
                } else if (json.status == 'warning') {
                    // fecha a modal
                    $.magnificPopup.close();
                    // mostra notificação
                    notifyWarning(json.message);
                } else {
                    $('.notification').html(json.message).show();
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
                });

        } else if( !( $("#con-agua").is(':checked') || $("#con-gas").is(':checked') || $("#con-energia").is(':checked') ) ) {
            $('.acesso.error').html('Selecione pelo menos uma opção.').show();
        }
    });

    $.validator.addClassRules("vnome", { twostring : true });

    $(document).on('change', '#con-prop', function() {
        if(this.checked) {
            $('div.change-vis').addClass('d-none');
        } else {
            $('div.change-vis').removeClass('d-none')
        }
    });

    var dtLeituras = $('#dt-gas').DataTable({
        dom: '<"table-responsive"t>rp',
        processing: true,
        columns: [
            { data: "competencia", className: 'dt-body-center' },
            { data: "leitura_anterior", className: 'dt-body-center' },
            { data: "leitura_atual", className: 'dt-body-center' },
            { data: "consumo", className: 'dt-body-center' },
            { data: "inicio", className: 'dt-body-center' },
            { data: "fim", className: 'dt-body-center' },
            { data: "action", orderable: false, className: "dt-body-center actions"} ],
        serverSide: true,
        sorting: [],
        pageLength: 7,
        pagingType: "numbers",
        searching: false,
        ajax: {
            url: $('#dt-gas').data('url'),
            data: function ( d ) {
                return $.extend( {}, d, {
                    uid: $('.page-header').data('unidade'),
                    cid: $('.page-header').data('condo')
                } );
            },
            error: function () {
                notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-gas').dataTable().fnProcessingIndicator(false);
                $('#dt-gas_wrapper .table-responsive').removeClass('processing');
            }
        },
        fnPreDrawCallback: function() { $('#dt-gas_wrapper .table-responsive').addClass('processing'); },
        fnDrawCallback: function() { $('#dt-gas_wrapper .table-responsive').removeClass('processing'); $('[data-toggle="tooltip"]').tooltip(); },

        initComplete: (settings, json)=>{
            $('#dt-gas_paginate').appendTo('.card-leituras .card-footer');
        }
    });

    // **
    // * Handler Action Abrir modal imagem do medidor
    // **
    $(document).on('click', '#dt-gas .action-image', function () {
        var img = $(this).data("img");
        var un = $(this).data("un");
        var lt = $(this).data("lt");

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/ajax/md_gas_image'},
            type: 'ajax',
            modal:true,
            callbacks: {
                ajaxContentAdded: function() {
                    $('img')
                        .wrap('<span style="display:inline-block"></span>')
                        .parent()
                        .zoom();
                }
            },
            ajax: {
                settings: {
                    type: 'POST',
                    data: { img: img, un: un, lt: lt }
                }
            }
        });
    });

    // **
    // * Handler Fechar Modal modal-dismiss
    // **
    $(document).on('click', '.modal-dismiss', function (e) {
        // para propagação
        e.preventDefault();
        // fecha a modal
        $.magnificPopup.close();
    });

    // **
    // * Configuração Datatable Acessos
    // **
    let dtAcessos = $('#dt-access').DataTable({
        dom: '<"table-responsive"t>rp',
        pagingType: "numbers",
        sorting: [],
        pageLength: 6,
        processing: true,
        serverSide: true,
        searching: false,
        columns: [ { data: "nome" }, { data: "data" } ],
        ajax: {
            url: $('#dt-access').data('url'),
            error: function () {
                notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-access').dataTable().fnProcessingIndicator(false);
                $('#dt-access_wrapper .table-responsive').removeClass('processing');
            }
        },
        fnPreDrawCallback: function() { $('#dt-access_wrapper .table-responsive').addClass('processing'); },
        fnDrawCallback: function() { $('#dt-access_wrapper .table-responsive').removeClass('processing'); }
    });


}).apply(this, [jQuery]);