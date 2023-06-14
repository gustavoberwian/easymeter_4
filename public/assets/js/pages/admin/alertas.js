(function($) {

	'use strict';

    // ***********************************************************************************************
    // * Inicializadores
    // ***********************************************************************************************

    // **
    // * Inicializa datatable
    // **
	$.fn.dataTable.ext.errMode = 'throw';
	$.fn.dataTableExt.oApi.fnProcessingIndicator = function ( oSettings, onoff ) {
		if ( typeof( onoff ) == 'undefined' ) {
			onoff = true;
		}
		this.oApi._fnProcessingDisplay( oSettings, onoff );
	};

    var box = 'in';
    var monitoramento = 'todos'; //$('.monitoramento.active input').data('filter');
	var dtAlertas = $('#dt-alertas').DataTable({
		dom: '<"row"<"col-lg-6"l><"col-lg-6">><"table-responsive"t>pr',
		processing: true,
        columns: [ 
                { data: "tipo", visible: false, orderable: false}, 
                { data: "DT_RowClass", visible: false, orderable: false},
                { data: "active", visible: false, orderable: false},
                { data: "icon", class: "dt-body-center", orderable: false }, 
                { data: "titulo", className: 'table-ellipsis' }, 
                { data: "texto", className: 'table-ellipsis d-none d-lg-table-cell', orderable: false },
                { data: "enviada" }, 
                { data: "enviado_por", class: "dt-body-center d-none d-lg-table-cell", orderable: false }, 
                { data: "actions", className: "actions dt-body-center d-none d-lg-table-cell", orderable: false} ],
        ordering: true,
        pagingType: "numbers",
        sorting: [],
        lengthChange: false,
        pageLength: 10,
        serverSide: true,
		ajax: { 
            url: $('#dt-alertas').data('url'),
            data: function ( d ) {
                return $.extend( {}, d, {
                    box: box,
                    monitoramento: monitoramento
                } );
            },
			error: function () {
				notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-alertas').dataTable().fnProcessingIndicator(false);
                $('.table-responsive').removeClass('processing');
            },
            dataSrc: function ( json ) {
                //$('.badge-alerta').attr('data-count', json.extra.count).html(json.extra.count);

                return json.data;
            }           
        },
		fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); },
        fnDrawCallback: function(oSettings) { 
            $('.table-responsive').removeClass('processing'); 
            if (box == 'in' && $('#dt-alertas tbody tr').hasClass('unread')) //oSettings.aoData.length > 0
                $('#dt-alertas_paginate').prepend('<div class="select-all"><a href="#" class="mark-all">Marcar todas como lida</a></div>')
        }        
    });

    $('.read').on('click', function (event) {
        dtAlertas.column(2).search( $(this).data('filter'), true, false ).draw();
    });

    $('.inbox-group label.btn').on("click", function() {
        box = this.id;
        if (box == 'in') {
            $('.vis-group').fadeIn(); 
            $('a.mark-all').show();
        } else { 
            dtAlertas.column(0).search( '', true, false )
            dtAlertas.column(1).search( '', true, false );
            $('.vis-group').fadeOut();
            $('a.mark-all').hide();
            $('.tipo-group label').removeClass('active').first().addClass('active');
            $('.box-group label').removeClass('active').first().addClass('active');
        }
        dtAlertas.ajax.reload( );
    });

    $(".monitoramento-group :input").change(function() {
        monitoramento = $(this).data('filter');
        dtAlertas.ajax.reload( );
    });

    $('#refresh label.btn').on("click", function() {
        dtAlertas.ajax.reload( );
    });

    $(document).on("click", 'a.mark-all', function() {

        // faz a requisição
		$.post("/ajax/mark_all_read", { id: $('.userbox').data('uid') }, function(json) {
			if (json.status == 'success') {
                // remove destaque da linha
                $('#dt-alertas tbody tr').removeClass('unread');
                // mostra actions
                $('#dt-alertas .action-delete').removeClass('d-none');
                // reset badge
                $('.badge-alerta').attr('data-count', 0)
                // esconde link
                $('.select-all').remove();
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
			// mostra erro
			notifyError(error, 'Ajax Error');
		});
    });

	$(document).on('click', '.modal-dismiss', function (e) {
		// para propagação
		e.preventDefault();
		// fecha a modal
		$.magnificPopup.close();
    });


	// **
	// * Handler Action Excluir Alerta
	// **
	$(document).on('click', '#dt-alertas .action-delete', function () {

        var id = $(this).data('id');
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
					$('#modalExclui .id').val( id );
				}
			}
		});
	});
	
	// **
	// * Handler Action Excluir Alerta
	// **
	$(document).on('click', '.btn-send', function () {

		// abre a modal
//		$.magnificPopup.open( {
//			items: {src: '#modalEnvia'}, type: 'inline'
//        });
        $.magnificPopup.open( {
            items: {src: '/admin/md_confirma_aviso'},
            type: 'ajax',
            modal:true,
        });


	});

    // **
	// * Handler Botão Excluir Modal Confirmação Exclusão Alerta
	// **
	$(document).on('click', '#modalExclui .modal-confirm', function () {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
		var id = $('#modalExclui .id').val();
		// faz a requisição
		$.post("/ajax/delete_alerta", { id: id, box: box, adm: 1 }, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
                // remove linha
                $('#dt-alertas tr#' + id).hide('slow', function(){ $(this).remove(); });
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
		});
	});

    $(document).on("click", '.btn-export', function() {

        var $btn = $(this);
		$btn.trigger('loading-overlay:show').prop('disabled', true);

        // faz a requisição
		$.post("/painel/download_alertas", function(json) {
            var $a = $("<a>");
            $a.attr("href", json.file);
            $("body").append($a);
            $a.attr("download", json.name + '.xlsx');
            $a[0].click();
            $a.remove();
		}, 'json')
		.fail(function(xhr, status, error) {
			// mostra erro
            notifyError(error, 'Ajax Error');
        })
		.always(function() {
			// oculta indicador e habilita botão
			$btn.trigger('loading-overlay:hide').prop('disabled', false);
        });
    });

    $(document).on('click', '.filt', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $('.filt.active').removeClass('active');
        $btn.addClass('active');

    })
    

}).apply(this, [jQuery]);
