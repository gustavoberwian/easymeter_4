(function($) {

	'use strict';

	let mostra = 0;

    // **
    // * Inicializa datatable
    // **
	let $dtUsers = $('#dt-users').DataTable({
		dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>pr',
		processing: true,
        columns: [
			{ data: "avatar", orderable: false, class: "dt-body-center" },
			{ data: "nome", orderable: false },
			{ data: "email", orderable: false },
            { data: "groups", orderable: false },
			{ data: "monitora", orderable: false, className: "dt-body-center monitor"},
			{ data: "page", orderable: false },
			{ data: "status", orderable: false, class: "dt-body-center" },
            { data: "actions", orderable: false, class: "dt-body-center actions"}
		],
		order:[],
		serverSide: true,
		ajax: {
			url: $('#dt-users').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					mode: mostra
				} );
			}
		},
		fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); },
		fnDrawCallback: function () {
			$(".switch-input").themePluginIOS7Switch(),
			$('.table-responsive').removeClass('processing');
		}
    });


	// **
	// * Handler menu tipo monitoramento
	// **
	$(document).on('click',".dropdown-menu-config .monitor", function () {
		$('.monitor').children().addClass('fa-none').removeClass('fa-check');
        $(this).children().addClass('fa-check').removeClass('fa-none');
		mostra = $(this).data('mode');
		$dtUsers.ajax.reload();
    });	

	$(document).on('click', '#dt-users .action-delete-user', function () {
        var uid = $(this).data('id');
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
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

	$(document).on('click', '#modalExclui .modal-confirm', function () {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
        var uid = $('#modalExclui .id').data('uid');
		// faz a requisição
		$.post("/admin/delete_user", { uid: uid}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
                $dtUsers.ajax.reload( null, false );
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
    

}).apply(this, [jQuery]);