// **
// * Datatables
// **
(function($) {

	'use strict';

    // ***********************************************************************************************
    // * Inicializadores
    // ***********************************************************************************************

    // **
    // * Inicializa datatable
    // **
	$('#dt-users').DataTable({
		dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>pr',
		processing: true,
        columns: [ { data: "avatar", orderable: false, class: "dt-body-center" }, { data: "nome" }, { data: "email" }, 
            { data: "groups", orderable: false }, { data: "condo" }, { data: "unidade", class: "dt-body-center" }, { data: "active", class: "dt-body-center" }, 
            { data: "action", orderable: false, class: "dt-body-center actions"} ],
		order:[],
		serverSide: true,
		ajax: { url: $('#dt-users').data('url') }
    });
    
    $.fn.dataTable.ext.errMode = 'throw';
	$.fn.dataTableExt.oApi.fnProcessingIndicator = function ( oSettings, onoff ) {
		if ( typeof( onoff ) == 'undefined' ) {
			onoff = true;
		}
		this.oApi._fnProcessingDisplay( oSettings, onoff );
	};




    // ***********************************************************************************************
    // * Página Usuários
    // ***********************************************************************************************

    // **
    // * Configuração Datatable Convites
    // **
	var dtConvites = $('#dt-convites').DataTable({
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
    // * Configuração Datatable Proprietarios
    // **
	var dtProprietarios = $('#dt-proprietarios').DataTable({
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
	// * Handler Action ativar/desativar usuário
	// **
	$(document).on('click', '#dt-convites .action-change', function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) return;

        // mostra indicador
		var $btn = $(this);
        var html = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');
		// desabilita botões
		$('#dt-convites .actions a').addClass('disabled');
		// pega o valor do id
		var id = $btn.data('uid');
		// faz a requisição
		$.post("/ajax/usuario_active", {id: id}, function(json) {
			if (json.status == 'success') {
				// atualiza tabela
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
			$('#dt-convites .actions a').removeClass('disabled');
		});
    });

    // **
	// * Handler Action ativar/desativar proprietário
	// **
	$(document).on('click', '#dt-proprietarios .action-change', function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) return;

        // mostra indicador
		var $btn = $(this);
        var html = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');
		// desabilita botões
		$('#dt-proprietarios .actions a').addClass('disabled');
		// pega o valor do id
		var id = $btn.data('uid');
		// faz a requisição
		$.post("/ajax/usuario_active", {id: id}, function(json) {
			if (json.status == 'success') {
				// atualiza tabela
				dtProprietarios.ajax.reload( null, false );
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
			$('#dt-proprietarios .actions a').removeClass('disabled');
		});
    });

    // **
	// * Handler Action excluir usuário. Abre confirmação
	// **
	$(document).on('click', '#dt-convites .action-delete', function () {
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
	// * Handler Fechar Modal Confirmação Exclusão
	// **
	$(document).on('click', '.modal-dismiss', function (e) {
		// para propagação
        e.preventDefault();

        // limpa id e data
        $('#modalExclui .id').val('').data('user', null);

		// fecha a modal
		$.magnificPopup.close();
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

    // **
	// * Handler Action Editar usuário. Abre confirmação
	// **
	$(document).on('click', '#dt-convites .action-edit', function () {
        // abre a modal
		$.magnificPopup.open( {
			items: {src: '/ajax/md_usuario_edit'},
			type: 'ajax',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { cid: $(this).data('cid'), uid: $(this).data('uid') }
				}
			}
		});
	});

    // **
	// * Handler Confirma Modal Edita Usuário
	// **
	$(document).on('click', '.form-edit .modal-confirm', function (e) {
		// para propagação
        e.preventDefault();
        
        if ( $(".form-edit").valid() && ( $("#con-agua").is(':checked') || $("#con-gas").is(':checked') || $("#con-energia").is(':checked') ) ) {

            var $btn = $(this);
            $btn.trigger('loading-overlay:show');
            // desabilita botões
            var $btn_d = $('.btn:enabled').prop('disabled', true);
            // captura dados
			var data = $('.form-edit').serializeArray();
			// envia os dados
			$.post('/ajax/edit_usuario', data, function(json) {
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
    
    $(document).on('change', 'input.acesso:checked', function (e) {
        $('.acesso.error').html('').hide();
    });

    // **
    // * Adiciona validadores especificos
    // **
    $.validator.addClassRules("vnome", { twostring : true });

    $(document).on('change', '#con-prop', function() {
        if(this.checked) {
            $('div.change-vis').addClass('d-none');
        } else {
            $('div.change-vis').removeClass('d-none')
        }
    });    

}).apply(this, [jQuery]);