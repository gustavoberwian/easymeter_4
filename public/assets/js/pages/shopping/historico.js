
(function($) {

	'use strict';
    // ***********************************************************************************************
    // * Inicializadores
    // ***********************************************************************************************

    // **
    // * Inicializa datatable
    // **
	var dtLog = $('#dt-log').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ {data: "DT_RowClass", visible: false},
                { data: "tipo", class: "dt-body-center" },
                { data: "enviado_por", class: "dt-body-center d-none d-lg-table-cell" },
                { data: "mensagem", className: 'table-ellipsis' }, 
                { data: "cadastro" }, 
                { data: "actions", className: "actions dt-body-center", orderable: false} ],
        ordering: false,
        pagingType: "numbers",
        autoWidth: false,
        lengthChange: false,
        pageLength: 30,
        serverSide: true,
		ajax: { 
            url: $('#dt-log').data('url'),
            data: function ( d ) {
                return $.extend( {}, d, {
                    tipo: $('.nav-link.active').data('tipo')
                } );
            },
            dataFilter: function(response){
                var temp = JSON.parse(response);
                $("span.badge-log").data('count', temp.extra.total).html(temp.extra.total);
                    return response;
            },            
			error: function () {
				notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-log').dataTable().fnProcessingIndicator(false);
                $("#dt-log .table-responsive").removeClass("processing");
            }           
        },
        fnDrawCallback: function(oSettings) {
            $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            if ($('#dt-log tbody tr').hasClass('unread')) {
                $('#dt-log_paginate').prepend('<div class="select-all"><a href="#" class="mark-all">Marcar todos como lido</a></div>');
//                $('span.badge-log').data('count', $("#dt-log tbody tr.unread").length).html($("#dt-log tbody tr.unread").length).removeClass("d-none");
            } else {
//                $('span.badge-log').data('count', 0).addClass("d-none");
            }
        }        
    });

    // **
	// * Handler Action marcar/desmarcar como lido/resolvido
	// **
	$(document).on('click', '#dt-log .action-readed', function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) return;

        // mostra indicador
		var $btn = $(this);
        var html = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');
		// desabilita botões
		$('#dt-log .actions a').addClass('disabled');
		// pega o valor do id
        var id = $btn.data('id');
		// faz a requisição
		$.post("/shopping/set_log_state", {id: id}, function(json) {
			if (json.status == 'success') {
				// atualiza tabela
				dtLog.ajax.reload( null, false );
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
			$('#dt-log .actions a').removeClass('disabled');
		});
    });

    // **
	// * Handler marcar todos como lido
	// **
	$(document).on('click', '#dt-log_paginate .mark-all', function (e) {
        e.preventDefault();

		// desabilita botões
		$('#dt-log .actions a').addClass('disabled');
		// faz a requisição
		$.post("/shopping/set_log_state", {id: 0}, function(json) {
			if (json.status == 'success') {
				// atualiza tabela
                dtLog.ajax.reload( null, false );
                // oculta badge no menu
                $('span.badge-log').addClass("d-none");
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
			// habilita botões
			$('#dt-log .actions a').removeClass('disabled');
		});
    });

    // **
	// * Handler Filter
	// **
	$(document).on('click', 'a.nav-link', function (e) {
        $('a.nav-link').removeClass('active');
        $(this).addClass('active');
        dtLog.ajax.reload();
    });

	/*var dtAccess = $('#dt-access').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [  { data: "nome" },
                    { data: "data", class: "dt-body-center" },
                    { data: "condo", className: 'table-ellipsis'}, 
                    { data: "unidade", orderable: false}, 
                    { data: "actions", className: "actions dt-body-center", orderable: false} ],
        pagingType: "numbers",
        ordering: false,
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        serverSide: true,
		ajax: { 
            url: $('#dt-access').data('url'),
			error: function () {
				notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-access').dataTable().fnProcessingIndicator(false);
            }           
        },
    });*/

}).apply(this, [jQuery]);
