(function () {

    "use strict";

    function EditableTable() {
        return {

            initialize: function (
                table = null,
                columns = null,
                dom = null,
                url = null,
                save = null,
                cancel = null,
                edit = null,
                del = null,
                add = null,
                modalId = null,
                modalUrl = null,
                order = false,
            ) {
                this
                    .options(table, columns, dom, url, save, cancel, edit, del, add, modalId, modalUrl, order)
                    .setVars()
                    .build()
                    .events();
            },

            options: function (table, columns, dom, url, save, cancel, edit, del, add, modalId, modalUrl, order) {
                this.$options = {
                    table: table,
                    columns: columns,
                    dom: dom,
                    url: url,
                    save: save,
                    cancel: cancel,
                    edit: edit,
                    del: del,
                    add: add,
                    modalId: modalId,
                    modalUrl: modalUrl,
                    order: order
                }

                return this;
            },

            setVars: function () {
                this.$idtable = this.$options.table;
                this.$dom = this.$options.dom;
                this.$url = this.$options.url;
                this.$save = this.$options.save;
                this.$cancel = this.$options.cancel;
                this.$edit = this.$options.edit;
                this.$add = this.$options.add;
                this.$del = this.$options.del;
                this.$modalId = this.$options.modalId;
                this.$modalUrl = this.$options.modalUrl;
                this.$order = this.$options.order;
                this.$table = $(this.$options.table);
                this.$columns = $(this.$options.columns);
                return this;
            },

            build: function () {
                var _self = this;

                this.datatable = this.$table.DataTable({
                    dom: _self.$dom,
                    processing: true,
                    aoColumns: _self.$columns,
                    serverSide: true,
                    paging: true,
                    pageLength: 10,
                    order: _self.$order,
                    pagingType: "numbers",
                    ajax: {
                        type: 'POST',
                        url: _self.$table.data("url"),
                        data: {group: $(".page-header").data('group'), tipo: _self.$table.data("tipo")},
                        error: function () {
                            notifyError(
                                "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                            );
                            _self.$table.dataTable().fnProcessingIndicator(false);
                            $(_self.$idtable + "_wrapper .table-responsive").removeClass(
                                "processing"
                            );
                        },

                    },
                    createdRow: function (row, data, dataIndex) {
                        $(row).attr('data-item-id', data[0]);
                    },
                    fnPreDrawCallback: function () {
                        $(_self.$idtable + "_wrapper .table-responsive").addClass("processing");
                    },
                    fnDrawCallback: function () {
                        $(_self.$idtable + "_wrapper .table-responsive").removeClass("processing");
                        $('[data-toggle="tooltip"]').tooltip();
                        if (_self.$idtable === '#dt-alertas-conf-energia' || _self.$idtable === '#dt-alertas-conf-agua' || _self.$idtable === '#dt-agrupamentos-energia' || _self.$idtable === '#dt-agrupamentos-agua') {
                            if (_self.$idtable === '#dt-alertas-conf-energia' || _self.$idtable === '#dt-alertas-conf-agua') {
                                $(".switch-input").themePluginIOS7Switch()
                            }

                            var dados = {
                                buttonClass: 'multiselect dropdown-toggle form-select text-center form-control',
                                maxHeight: 250,
                                buttonWidth: '100%',
                                numberDisplayed: 1,
                                includeSelectAllOption: true
                            };
                            $(".select-medidores").multiselect(dados);
                        }
                    },
                });

                window.dt = this.datatable;

                return this;
            },

            events: function () {
                var _self = this;

                this.$table
                    .on('click', this.$save, function (e) {
                        e.preventDefault();

                        _self.rowSave($(this).closest('tr'));
                    })
                    .on('click', this.$cancel, function (e) {
                        e.preventDefault();

                        _self.rowCancel($(this).closest('tr'));
                    })
                    .on('click', this.$edit, function (e) {
                        e.preventDefault();

                        _self.rowEdit($(this).closest('tr'));
                    })
                    .on('click', this.$del, function (e) {
                        e.preventDefault();

                        var $row = $(this).closest( 'tr' ),
                            itemId = $row.attr('data-item-id');

                        $.magnificPopup.open({
                            items: { src: _self.$modalId }, type: 'inline',
                            callbacks: {
                                beforeOpen: function () {
                                    $(_self.$modalId + ' .id').val(itemId);
                                    $(_self.$modalId + ' .id').data('uid', itemId);
                                }
                            }
                        });
                    });

                $(_self.$modalId + ' .modal-confirm').on( 'click', function ( e ) {
                    e.preventDefault();

                    _self.rowDelete($(this).closest('tr'));
                });

                $('.modal-dismiss').on( 'click', function( e ) {
                    e.preventDefault();
                    $.magnificPopup.close();
                });

                $(_self.$add).on( 'click', function(e) {
                    e.preventDefault();

                    _self.rowAdd();
                });

                return this;
            },

            // ==========================================================================================
            // ROW FUNCTIONS
            // ==========================================================================================

            rowAdd: function () {
                $(this.$add).attr({ 'disabled': 'disabled' });
                var _self = this;

                var actions,
                    data,
                    $row;

                actions = [
                    '<a href="#" class="hidden on-editing save-row text-success"><i class="fas fa-save"></i></a>',
                    '<a href="#" class="hidden on-editing cancel-row text-danger"><i class="fas fa-times"></i></a>'
                ].join(' ');

                data = _self.datatable.row.add([ '', '', '', '', actions ]);
                $row = _self.datatable.row( data[0] ).nodes().to$();

                $row
                    .addClass( 'adding' )
                    .find( 'td:last' )
                    .addClass( 'actions' );

                _self.rowEdit( $row );

                _self.$table.append($row)
            },

            rowCancel: function ($row) {
                var _self = this,
                    $actions,
                    i,
                    data;

                data = this.datatable.row($row.get(0)).data();
                this.datatable.row($row.get(0)).data(data);

                $actions = $row.find('td.actions');
                if ($actions.get(0)) {
                    this.rowSetActionsDefault($row);
                }

                unsaved = false

                this.datatable.draw();
            },

            rowEdit: function ($row) {

                var _self = this;

                var data = this.datatable.row($row.get(0)).data();

                $row.children('td').each(function (i) {

                    var $this = $(this);

                    if (!data[i]) {
                        data[i] = "";
                    }

                    if ($this.hasClass('actions')) {

                        _self.rowSetActionsEditing($row);
                    } else if ($this.hasClass('static')) {

                        $this.html(data[i]);
                    } else if ($this.hasClass('switch-dt')) {

                        $this.children().removeClass('disabled');
                    } else if ($this.hasClass('select')) {

                        var html = '';
                        var options = {};
                        var _options = {};

                        if ($this.hasClass('medidores')) {

                            $this.children().children('select').multiselect('enable')
                        } else if ($this.hasClass('quando')) {

                            $this.children().prop('disabled', false);
                        } else if ($this.hasClass('subtipo')) {

                            html = '<select class="form-control" name="' + data[i] + '" id="' + data[i] + '">';

                            $this.text('');
                            $this.append('<div class="snippet" data-title="dot-flashing">\n' +
                                '          <div class="stage">\n' +
                                '            <div class="dot-flashing"></div>\n' +
                                '          </div>\n' +
                                '        </div>');
                            $.ajax({
                                method: 'POST',
                                url: '/shopping/get_subtipo_cliente_config',
                                data: {
                                    group: $(".page-header").data("group")
                                },
                                success: function (response) {
                                    options = [1, 2];
                                    _options = [response, 'Unidades'];
                                },
                                error: function (xhr, status, error) {
                                },
                                complete: function () {
                                    for (var j = 0; j < options.length; j++) {
                                        if (_options[j] === data[i]) {
                                            html += '<option value="' + options[j] + '" selected>' + _options[j] + '</option>';
                                        } else {
                                            html += '<option value="' + options[j] + '">' + _options[j] + '</option>';
                                        }
                                    }
                                    html += '</select>';

                                    $this.html(html);
                                }
                            });
                        } else if ($this.hasClass('unidades')) {

                            html = '<select class="form-control select-unidades" multiple="multiple" name="select_unidades" id="select-unidades">'

                            $this.text('');
                            $this.append('<div class="snippet" data-title="dot-flashing">\n' +
                                '          <div class="stage">\n' +
                                '            <div class="dot-flashing"></div>\n' +
                                '          </div>\n' +
                                '        </div>');
                            $.ajax({
                                method: 'POST',
                                url: '/shopping/get_unidades_select',
                                data: {
                                    group: $(".page-header").data("group")
                                },
                                dataType: 'json',
                                success: function (response) {
                                    options = response.options;
                                    _options = response._options;
                                },
                                error: function (xhr, status, error) {
                                },
                                complete: function () {
                                    for (var j = 0; j < options.length; j++) {
                                        if (_options[j] === data[i]) {
                                            html += '<option value="' + options[j] + '" selected>' + _options[j] + '</option>';
                                        } else {
                                            html += '<option value="' + options[j] + '">' + _options[j] + '</option>';
                                        }
                                    }
                                    html += '</select>';

                                    $this.html(html);

                                    var dados = {
                                        buttonClass: 'multiselect dropdown-toggle form-select text-center form-control',
                                        maxHeight: 250,
                                        buttonWidth: '100%',
                                        numberDisplayed: 1,
                                        includeSelectAllOption: true
                                    };
                                    $(".select-unidades").multiselect(dados);
                                }
                            });
                        } else {

                            html = '<select class="form-control" name="' + data[i] + '" id="' + data[i] + '">';

                            if ($this.hasClass('tipo')) {

                                options = ['iluminacao', 'ar_condicionado', 'loja', 'quiosque'];
                                _options = ['Iluminação', 'Ar Condicionado', 'Loja', 'Quiosque'];
                            } else if ($this.hasClass('faturamento')) {

                                options = ['incluir', 'nao_incluir'];
                                _options = ['Incluir', 'Não Incluir'];
                            }

                            for (var j = 0; j < options.length; j++) {
                                if (_options[j] === data[i]) {
                                    html += '<option value="' + options[j] + '" selected>' + _options[j] + '</option>';
                                } else {
                                    html += '<option value="' + options[j] + '">' + _options[j] + '</option>';
                                }
                            }
                            html += '</select>';

                            $this.html(html);
                        }
                    } else {
                        $this.html('<input type="text" class="form-control input-block" value="' + data[i] + '"/>');
                    }
                });
            },

            rowSave: function ($row) {
                var _self = this,
                    $actions,
                    values = [];

                if ($row.hasClass('adding')) {
                    $(this.$add).removeAttr('disabled');
                    $row.removeClass('adding');
                }

                var data = {};
                $row.children('td').each(function (i) {
                    if (i === 0) {
                        data['id'] = $(this).children('input').val();
                    } else if (i === 1) {
                        data['group_id'] = $(this).children('input').val();
                    } else {
                        if ($(this).hasClass('switch-dt')) {
                            if ($(this).children().children('input').prop('checked')) {
                                data[$(this).children().children('input').attr('name')] = 1;
                            } else {
                                data[$(this).children().children('input').attr('name')] = 0;
                            }
                        } else if ($(this).hasClass('select')) {
                            if ($(this).hasClass('medidores')) {
                                data[$(this).children().children('select').attr('name')] = $(this).children().children('select').val();
                            } else if ($(this).hasClass('quando')) {
                                data[$(this).children('select').attr('name')] = $(this).children('select').val();
                            } else {
                                data[$(this).find('select').attr('name')] = $(this).find('select').val();
                            }
                        } else {
                            if ($(this).children('input').length > 0) {
                                data[$(this).children('input').attr('name')] = $(this).children('input').val();
                            } else if ($(this).children('select').length > 0) {
                                data[$(this).children('select').attr('name')] = $(this).children('select').val();
                            }
                        }
                    }
                });

                data['entrada_id'] = _self.$table.data('tipo');

                $.ajax({
                    method: 'POST',
                    url: _self.$url,
                    data: data,
                    dataType: 'json',
                    success: function (json) {
                        if (json.status == "success") {
                            // notifica êxito
                            notifySuccess(json.message);
                        } else {
                            // notifica erro
                            notifyError(json.message);
                        }
                    },
                    error: function (xhr, status, error) {
                    },
                    complete: function () {
                        $actions = $row.find('td.actions');
                        if ($actions.get(0)) {
                            _self.rowSetActionsDefault($row);
                        }
                        _self.datatable.draw();
                    }
                });
            },

            // rowRemove -> disabled
            rowRemove: function ($row) {
                if ($row.hasClass('adding')) {
                    $(this.$add).removeAttr('disabled');
                }

                this.datatable.row($row.get(0)).remove().draw();
            },

            rowDelete: function ($row) {
                var _self = this;
                // pega o valor do id
                var id = $(_self.$modalId + ' .id').val();
                var itemId = $(_self.$modalId + ' .id').data('uid');
                // faz a requisição
                $.post(_self.$modalUrl, { id: id }, function (json) {
                    if (json.status == 'success') {
                        // fecha modal
                        $.magnificPopup.close();
                        // atualiza tabela
                        _self.rowRemove( $row );
                        // mostra notificação
                        notifySuccess(json.message);
                    } else {
                        // fecha modal
                        $.magnificPopup.close();
                        // mostra erro
                        notifyError(json.message);
                    }
                }, 'json')
                    .fail(function (xhr, status, error) {
                        // fecha modal
                        $.magnificPopup.close();
                        // mostra erro
                        notifyError(error, 'Ajax Error');
                    })
                    .always(function () {
                        // limpa id
                        $(_self.$modalId + ' .id').val('').data('uid', null);
                    });
            },

            rowSetActionsEditing: function ($row) {
                $row.find('.on-editing').removeClass('hidden');
                $row.find('.on-default').addClass('hidden');
            },

            rowSetActionsDefault: function ($row) {
                $row.find('.on-editing').addClass('hidden');
                $row.find('.on-default').removeClass('hidden');
            }

        };
    }

    var energyUnitsTable = new EditableTable();
    energyUnitsTable.initialize("#dt-unidades-energia",
        [
            {class: "d-none"},
            {class: "dt-body-left align-middle"},
            {class: "dt-body-center align-middle select subtipo"},
            {class: "dt-body-center align-middle select tipo"},
            {class: "dt-body-center align-middle"},
            {class: "dt-body-center align-middle"},
            {class: "dt-body-center align-middle"},
            {class: "dt-body-center align-middle select faturamento"},
            {"bSortable": false, class: "dt-body-center align-middle actions",}
        ],
        '<"row"<"col-md-6"><"col-md-6"f>><"table-responsive"t>pr',
        '/shopping/edit_unidade',
        '.save-row',
        '.cancel-row',
        '.edit-row',
        '',
        '',
        '',
        '',
        [[ 2, "asc" ], [ 1, "asc" ]]);

    var waterUnitsTable = new EditableTable();
    waterUnitsTable.initialize("#dt-unidades-agua",
        [
            {class: "d-none"},
            {class: "dt-body-left align-middle"},
            {class: "dt-body-center align-middle select subtipo"},
            {class: "dt-body-center align-middle select tipo"},
            {class: "dt-body-center align-middle"},
            {class: "dt-body-center align-middle"},
            {class: "d-none"},
            {class: "dt-body-center align-middle select faturamento"},
            {"bSortable": false, class: "dt-body-center align-middle actions",}
        ],
        '<"row"<"col-md-6"><"col-md-6"f>><"table-responsive"t>pr',
        '/shopping/edit_unidade',
        '.save-row',
        '.cancel-row',
        '.edit-row',
        '',
        '',
        '',
        '',
        [[ 2, "asc" ], [ 1, "asc" ]]);

    var energyGroupingsTable = new EditableTable();
    energyGroupingsTable.initialize("#dt-agrupamentos-energia",
        [
            {class: "d-none"},
            {class: "d-none"},
            {class: "dt-body-left align-middle"},
            {class: "dt-body-center align-middle select unidades"},
            {"bSortable": false, class: "dt-body-center align-middle actions",}
        ],
        '<"row"<"col-md-6"><"col-md-6"f>><"table-responsive"t>pr',
        '/shopping/edit_agrupamentos',
        '.save-row',
        '.cancel-row',
        '.edit-row',
        '.delete-row',
        '.btn-new-agrupamento-energia',
        '#modalExcluiAgrupamentoEnergia',
        '/shopping/delete_agrupamento');

    var waterGroupingsTable = new EditableTable();
    waterGroupingsTable.initialize("#dt-agrupamentos-agua",
        [
            {class: "d-none"},
            {class: "d-none"},
            {class: "dt-body-left align-middle"},
            {class: "dt-body-center align-middle select unidades"},
            {"bSortable": false, class: "dt-body-center align-middle actions",}
        ],
        '<"row"<"col-md-6"><"col-md-6"f>><"table-responsive"t>pr',
        '/shopping/edit_agrupamentos',
        '.save-row',
        '.cancel-row',
        '.edit-row',
        '.delete-row',
        '.btn-new-agrupamento-agua',
        '#modalExcluiAgrupamentoAgua',
        '/shopping/delete_agrupamento');

    var alertsConfigTableEnergy = new EditableTable();
    alertsConfigTableEnergy.initialize("#dt-alertas-conf-energia",
        [
            {"bSortable": false, class: "d-none"},
            {"bSortable": false, class: "d-none"},
            {"bSortable": false, class: "dt-body-center align-middle switch-dt"},
            {"bSortable": false, class: "dt-body-left align-middle static"},
            {"bSortable": false, class: "dt-body-center align-middle select medidores"},
            {"bSortable": false, class: "dt-body-center align-middle select quando"},
            {"bSortable": false, class: "dt-body-center align-middle switch-dt"},
            {"bSortable": false, class: "dt-body-center align-middle switch-dt"},
            {"bSortable": false, class: "dt-body-center align-middle actions",}
        ],
        '<"table-responsive"t>r',
        '/shopping/edit_alert_conf',
        '.save-row',
        '.cancel-row',
        '.edit-row');

    var alertsConfigTableWater = new EditableTable();
    alertsConfigTableWater.initialize("#dt-alertas-conf-agua",
        [
            {"bSortable": false, class: "d-none"},
            {"bSortable": false, class: "d-none"},
            {"bSortable": false, class: "dt-body-center align-middle switch-dt"},
            {"bSortable": false, class: "dt-body-left align-middle static"},
            {"bSortable": false, class: "dt-body-center align-middle select medidores"},
            {"bSortable": false, class: "dt-body-center align-middle select quando"},
            {"bSortable": false, class: "dt-body-center align-middle switch-dt"},
            {"bSortable": false, class: "dt-body-center align-middle switch-dt"},
            {"bSortable": false, class: "dt-body-center align-middle actions",}
        ],
        '<"table-responsive"t>r',
        '/shopping/edit_alert_conf',
        '.save-row',
        '.cancel-row',
        '.edit-row');


    // GERAL START

    $(document)
        .on('click', '.btn-save-geral', function () {
            if ($(".form-config-geral").valid()) {
                var formData = $('.form-config-geral').serialize();

                $.ajax({
                    method: 'POST',
                    url: '/shopping/edit_client_conf',
                    data: formData,
                    dataType: 'json',
                    success: function (json) {
                        if (json.status == "success") {
                            // notifica êxito
                            notifySuccess(json.message);
                        } else {
                            // notifica erro
                            notifyError(json.message);
                        }
                    },
                    error: function (xhr, status, error) {
                    },
                    complete: function () {
                        unsaved = false
                    }
                });
            }
        })
        .on('click', '.btn-save', function (e) {
            e.preventDefault();
            setTimeout(function () {
                unsaved = false
            }, 170);
        });

    // GERAL END

    // ALERTAS CONFIG START

    $('.period').on('change', function () {
        if (this.value == 3) {
            $("#" + $(this).data("msg")).show();
        } else {
            $("#" + $(this).data("msg")).hide();
        }
    })

    // ALERTAS CONFIG END


    // USUÁRIOS START

    let dtUsers = $("#dt-usuarios").DataTable({
        dom: '<"row"<"col-md-6"><"col-md-6"f>><"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "name", class: "dt-body-left align-middle"},
            {data: "email", class: "dt-body-left align-middle"},
            {data: "actions", class: "table-one-line dt-body-center align-middle", orderable: false},
        ],
        serverSide: true,
        paging: true,
        pageLength: 10,
        pagingType: "numbers",
        ajax: {
            type: 'POST',
            url: $("#dt-usuarios").data("url"),
            data: {group: $(".page-header").data('group')},
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-usuarios").dataTable().fnProcessingIndicator(false);
                $("#dt-usuarios_wrapper .table-responsive").removeClass(
                    "processing"
                );
            },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).attr('data-user', data.id);
        },
        fnPreDrawCallback: function () {
            $("#dt-usuarios_wrapper .table-responsive").addClass("processing");
        },
        fnDrawCallback: function () {
            $("#dt-usuarios_wrapper .table-responsive").removeClass("processing");
            $('[data-toggle="tooltip"]').tooltip();
        },
    });

    $(document).on('click', '.btn-new-user', function () {
        window.location.href = "/shopping/users/" + $(".page-header").data('group') + "/create";
    });

    $(document).on('click', '.action-delete-user', function () {
        var user = $(this).data('id')
        // abre a modal
        $.magnificPopup.open({
            items: {src: '#modalExcluiUser'}, type: 'inline',
            callbacks: {
                beforeOpen: function () {
                    $('#modalExcluiUser .id').val(user);
                    $('#modalExcluiUser .id').data('uid', user);
                }
            }
        });
    });

    // **
    // * Handler Fechar Modal Confirmação Exclusão
    // **
    $(document).on('click', '.modal-dismiss', function (e) {
        // para propagação
        e.preventDefault();

        // limpa id e data
        $('#modalExcluiUser .id').val('').data('user', null);

        // fecha a modal
        $.magnificPopup.close();
    });

    // **
    // * Handler Botão Excluir Modal Confirmação Exclusão
    // **
    $(document).on('click', '#modalExcluiUser .modal-confirm', function () {
        // mostra indicador
        var $btn = $(this);
        $btn.trigger('loading-overlay:show');
        // desabilita botões
        var $btn_d = $('.btn:enabled').prop('disabled', true);
        // pega o valor do id
        var cid = $('#modalExcluiUser .id').val();
        var uid = $('#modalExcluiUser .id').data('uid');
        // faz a requisição
        $.post("/shopping/delete_user", {user: uid}, function (json) {
            if (json.status == 'success') {
                // fecha modal
                $.magnificPopup.close();
                // atualiza tabela
                dtUsers.ajax.reload();
                // mostra notificação
                notifySuccess(json.message);
            } else {
                // fecha modal
                $.magnificPopup.close();
                // mostra erro
                notifyError(json.message);
            }
        }, 'json')
            .fail(function (xhr, status, error) {
                // fecha modal
                $.magnificPopup.close();
                // mostra erro
                notifyError(error, 'Ajax Error');
            })
            .always(function () {
                // oculta indicador e habilita botão
                $btn.trigger('loading-overlay:hide');
                // habilita botões
                $btn_d.prop('disabled', false);
                // limpa id
                $('#modalExcluiUser .id').val('').data('uid', null);
            });
    });

    $(document).on('click', '.btn-generate-token', function (e) {
        e.preventDefault();

        $.ajax({
            method: 'POST',
            data: { group_id: $(".form-config-api").data("grupo") },
            dataType: 'json',
            url: '/shopping/generateToken',
            success: function (json) {
                if (json.status == "success") {
                    // notifica êxito
                    notifySuccess(json.message);
                    $("#token").val(json.token);
                } else {
                    // notifica erro
                    notifyError(json.message);
                }
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
            }
        })
    });

    $('#token').keypress(function(event) {
        event.preventDefault();
        return false;
    });

}.apply(this, [jQuery]));