// **
// * Datatables
// **
(function($) {

	'use strict';
	var mostra = 0;

    // ***********************************************************************************************
    // * Inicializadores
    // ***********************************************************************************************

    // **
    // * Inicializa datatable
    // **
	var table = $('#dt-contatos').DataTable({
		dom: '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>pr',
		processing: true,
		columns: [ { data: "nome"}, { data: "email" }, { data: "telefone" }, { data: "condominio" }, { data: "cidade" }, { data: "data", class: "dt-body-center" }, { data: "status", class: "monitor" }, { data: "action", orderable: false, class: "dt-body-center actions"} ],
        order:[],
        pagingType: "numbers",
		serverSide: true,
        ajax: { 
			url: $('#dt-contatos').data('url'),
			data: function ( d ) {
			return $.extend( {}, d, {
				mode: mostra
			} );
			}
		},
	});
	
    // **
    // * Handler Menu Monitoramento
    
		$(document).on('click',".dropdown-menu-config .monitor", function () {
		$('.monitor').children().addClass('fa-square').removeClass('fa-check-square');
        $(this).children().addClass('fa-check-square').removeClass('fa-square');
		mostra = $(this).data('mode');
		table.ajax.reload();
		
		table.columns(6).search( $(this).data('filter') ).draw();

    });
    
    // **
	// * Handler Action marcar/desmarcar como lido/resolvido
	// **
	$(document).on('click', '#dt-contatos .action-readed', function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) return;

        // mostra indicador
		var $btn = $(this);
        var html = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');
		// desabilita botões
		$('#dt-contatos .actions a').addClass('disabled');
		// pega o valor do id
        var id = $btn.data('id');
		// faz a requisição
		$.post("/admin/set_contact_state", {id: id}, function(json) {
			if (json.status == 'success') {
				// atualiza tabela
				table.ajax.reload( null, false );
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
			$('#dt-contatos .actions a').removeClass('disabled');
		});
    });


}).apply(this, [jQuery]);