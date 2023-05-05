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
			{ data: "monitora", orderable: false, class: "dt-body-center" },
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
		fnDrawCallback: function () {
			$(".switch-input").themePluginIOS7Switch()
		}
    });

	// **
	// * Handler menu mostra inativos
	// **
	$(document).on('click',".dropdown-menu-config .inativos", function () {
		$(this).children().toggleClass('fa-check fa-none');
		$dtUsers.ajax.reload();
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

}).apply(this, [jQuery]);