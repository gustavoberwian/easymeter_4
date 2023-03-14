(function () {

    "use strict";

    var dtAlerts = $("#dt-alerts").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-12"p>>',
        processing: true,
        columns: [
            {data: "type", className: "dt-body-center", orderable: false},
            {data: "tipo", className: "dt-body-center", orderable: false},
            {data: "device", className: "dt-body-center"},
            {data: "nome"},
            {data: "titulo"},
            {data: "enviada", className: "dt-body-center"},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
        serverSide: true,
        sorting: [],
        pagingType: "numbers",
        pageLength: 20,
        ajax: {
            type: "POST",
            url : $("#dt-alerts").data("url"),
            data: function (d) {
                d.fid = $(".btn-download").data("id");
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-alerts").dataTable().fnProcessingIndicator(false);
                $("#dt-alerts .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function(oSettings) { 
            $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            if ($('#dt-alerts tbody tr').hasClass('unread'))
                $('#dt-alerts_paginate').prepend('<div class="select-all"><a href="#" class="mark-all">Marcar todos como lidos</a></div>')
        }
    });

    $(".btn-alert-config").on("click", function (event) {
        window.location.href = "/shopping/configuracoes/" + $(".page-header").data("group") + "#alertas";
    });

    $('#dt-alerts tbody').on('click', 'tr', function (event) {

        if (event.target.cellIndex == undefined || event.target.cellIndex == 6) return;

        var data = dtAlerts.row( this ).data();
        var $row = $(this);

        $.magnificPopup.open( {
			items: {src: '/energia/ShowAlert'},
			type: 'ajax',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { id: data.DT_RowId }
				}
            },
            callbacks: {
				close: function() {
                    // mostra action
                    $('.action-delete').filter('[data-id="'+data.DT_RowId+'"]').removeClass('d-none')
                    // atualiza badge se necessário
                    if ($row.hasClass('unread')) {
                        var $count = $('.badge-alerta').attr('data-count') - 1;
                        $('.badge-alerta').attr('data-count', $count).html($count);
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
	$(document).on('click', '#dt-alerts .action-delete', function () {

        var $btn = $(this);
		$btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
		$.post("/energia/DeleteAlert", { id: $(this).data('id') }, function(json) {
			if (json.status == 'success') {
                // remove linha
                $('#dt-alerts tr#' + json.id).hide('slow', function(){ $(this).remove(); });
				// mostra notificação
				notifySuccess(json.message);
			} else {
                $btn.html('<i class="fas fa-trash"></i>');
				// mostra erro
				notifyError(json.message);
			}
		}, 'json')
		.fail(function(xhr, status, error) {
			$btn.html('<i class="fas fa-trash"></i>');
			notifyError(error, 'Ajax Error');
		});
	});

    $(document).on("click", 'a.mark-all', function() {

        // faz a requisição
		$.post("/energia/ReadAllAlert", function(json) {
			if (json.status == 'success') {
                // remove destaque da linha
                $('#dt-alerts tbody tr').removeClass('unread');
                // mostra actions
                $('#dt-alerts .action-delete').removeClass('d-none');
                // reset badge
                $('.badge-alerta').attr('data-count', 0)
                // esconde link
                $('.select-all').remove();
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
		});
    });

}.apply(this, [jQuery]));