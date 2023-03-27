(function () {

    "use strict";

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

    $(".btn-alert-config").on("click", function (event) {
        window.location.href = "/shopping/configuracoes/" + $(".page-header").data("group") + "#alertas";
    });

}.apply(this, [jQuery]));