var unidade_validator;
(function($) {

	'use strict';

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


    // ***********************************************************************************************
    // * Entities listagem
    // ***********************************************************************************************

	let mostra = 0;

    // **
    // * Inicializa datatable
    // **
	let $dtSuporte = $('#dt-suporte').DataTable({
		dom: '<"row"<"col-lg-6"l><"col-lg-6 text-right"f>><"table-responsive"t>pr',
		processing: true,
		pageLength: 10,
		columns: [
			{ data: "id", visible: false },
			{ data: "ticket", className: "dt-body-center" },
			{ data: "email", className: "dt-body-center" },
			{ data: "mensagem", orderable: false, className: "dt-body-center monitor"},
			{ data: "status", className: "dt-body-center"},
            { data: "cadastro", className: "table-one-line" },
			{ data: "departamento", className: "table-one-line" },
			{ data: "agrupamento", className: "table-one-line" },
            { data: "classificacao", className: "dt-body-center"},
		],
        serverSide: true,
        pagingType: "numbers",
		ajax: {
			url: $('#dt-suporte').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					inactives: $('.dropdown-menu-config .inativos i').hasClass('fa-check'),
					mode: mostra
				} );
			}
		},
		fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); },
		fnDrawCallback: function() { $('.table-responsive').removeClass('processing'); },
		createdRow: function ( row, data ) {
            if ( data.status == 'inativo' ) {
				$(row).addClass('inactive');
            }
		}
	});

    //Abre modal de chamado

    $(document).on('submit', 'form.form-chamado', function(e) {
        e.preventDefault();
        $.post("/admin/new_chamado", $(this).serialize(), function(json) {
            if (json.status == 'success') {
                $dtSuporte.ajax.reload();
                notifySuccess(json.message);
            } else if (json.status == 'error') {
                alert(json.message);
            }
        }, 'json')
        .fail(function(xhr, status, error) {
            // mostra erro
            notifyError(error, 'Ajax Error');
        })
        .always(function() {
            $.magnificPopup.close();
        });
    });    

    $(document).on('click', '.btn-incluir', function (e) {
		$.magnificPopup.open( {
			items: {src: "/admin/md_chamado"},
			type: 'ajax',
			modal: true
		});
    });

}).apply(this, [jQuery]);


