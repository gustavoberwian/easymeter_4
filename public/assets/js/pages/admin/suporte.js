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
		dom: '<"row"<"col-lg-6"><"col-lg-6 text-right"f>><"table-responsive"t><"row"<"col-lg-6 pt-4"l><"col-lg-6 text-right"pr>>',
		processing: true,
		pageLength: 10,
		columns: [
			{ data: "id", visible: false },
			{ data: "nome", orderable: false, className: "dt-body-center" },
			{ data: "status", orderable: true, className: "dt-body-center monitor"},
			{ data: "mensagem", orderable: false },
			{ data: "email", orderable: false, className: "dt-body-center" },
            { data: "cadastro", orderable: true, className: "table-one-line" },
			{ data: "departamento", orderable: true, className: "table-one-line" },
		],
        order: [[0, 'desc']],
        language: {search: ''},
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

	$('#dt-suporte tbody').on('click', 'tr', function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;
        // pega dados da linha
        var data = $dtSuporte.row( this ).data();
        // redireciona para o fechamento
        window.location = "/admin/suporte/" + data.DT_RowId;
    });

}).apply(this, [jQuery]);


