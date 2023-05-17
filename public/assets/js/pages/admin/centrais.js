var dtCentrais;
var dtEnvios;
(function($) {

	'use strict';

    // ***********************************************************************************************
    // * Inicializadores
    // ***********************************************************************************************

    // **
    // * Inicializa datatable
    // **
	dtCentrais = $('#dt-centrais').DataTable({
		dom: '<"table-responsive"t><"row"<"col-lg-6"l><"col-lg-6"p>>r',
		processing: true,
        columns: [  
                    { data: "status", orderable: false, class: "dt-body-center" },
                    { data: "nome", class: "dt-body-center" },
                    { data: "modo", class: "dt-body-center" },
                    { data: "versao", orderable: false, class: "dt-body-center" },
                    { data: "condo" },
                    { data: "ultima", orderable: false, class: "dt-body-center" },
                    { data: "alimentacao", orderable: false, class: "dt-body-center" },
                    { data: "fraude", orderable: false, class: "dt-body-center" },
                    { data: "actions", orderable: false, class: "dt-body-center actions"} ],
		order:[],
		serverSide: true,
		ajax: { url: $('#dt-centrais').data('url') }
	});

	var dtPostagens = $('#dt-postagens').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ { data: "central", class: "dt-body-center" },
                   { data: "data", class: "dt-body-center" },
                   { data: "id", class: "dt-body-center" },
                   { data: "tamanho", class: "dt-body-center" } ],
		order:[],
        serverSide: true,
        pageLength: 16,
        pagingType: "input",
		ajax: { url: $('#dt-postagens').data('url') },
    });

    var dtPortas2, dtPortas3, dtPortas4;
	var dtPortas1 = $('#dt-portas1').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ { data: "central", class: "dt-body-center" },
                   { data: "consumo_total", class: "dt-body-center" } ],
		order:[],
        serverSide: true,
        pageLength: 8,
        pagingType: "input",
        ajax: { url: $('#dt-portas1').data('url') },
        drawCallback: function() {
            dtPortas2 = $('#dt-portas2').DataTable({
                dom: '<"table-responsive"t>pr',
                processing: true,
                columns: [ { data: "central", class: "dt-body-center" },
                           { data: "consumo_total", class: "dt-body-center" } ],
                order:[],
                serverSide: true,
                pageLength: 8,
                pagingType: "input",
                ajax: { url: $('#dt-portas2').data('url') },
                drawCallback: function() {
                    dtPortas3 = $('#dt-portas3').DataTable({
                        dom: '<"table-responsive"t>pr',
                        processing: true,
                        columns: [ { data: "central", class: "dt-body-center" },
                                   { data: "consumo_total", class: "dt-body-center" } ],
                        order:[],
                        serverSide: true,
                        pageLength: 8,
                        pagingType: "input",
                        ajax: { url: $('#dt-portas3').data('url') },
                        drawCallback: function() {
                            dtPortas4 = $('#dt-portas4').DataTable({
                                dom: '<"table-responsive"t>pr',
                                processing: true,
                                columns: [ { data: "central", class: "dt-body-center" },
                                           { data: "consumo_total", class: "dt-body-center" } ],
                                order:[],
                                serverSide: true,
                                pageLength: 8,
                                pagingType: "input",
                                ajax: { url: $('#dt-portas4').data('url') }
                            });
                        }
                    });
                }
            });
        }
    });

    $(document).on('click', '.btn-centrais-reload', function (e) {
        // recarrega ajax do datatable, sem repaginar
        dtCentrais.ajax.reload(null, false);
    });    

    $(document).on('click', '.btn-envios-reload', function (e) {
        // recarrega ajax do datatable, sem repaginar
        dtPostagens.ajax.reload(null, false);
    });    

    $(document).on('click', 'button.search', function (e) {
        // redireciona para a página da central
        if ($("input[name='q']").val() != "" && $("button.search").data('searching') == 0) {
            $("input[name='q'], button.search").attr("disabled", true);
            $("button.search").html('<i class="fas fa-spinner fa-spin"></i>').data('searching', 1);
            $.post( '/admin_model/get_medidor', { mid: $("input[name='q']").val() }, function(json) {
                if (json.status == 'success') {
                    $('.preloader').fadeIn();
                    window.location = "/admin/centrais/" + json.central;
                } else {
                    // mostra erro
                    notifyWarning(json.message);
                }
            }, 'json').fail(function(xhr, status, error) {
                notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
            }).always(function() {
                $("input[name='q'], button.search").attr("disabled", false);
                $('button.search').html('<i class="fas fa-search"></i>').data('searching', 0);
            });
        }
    });    


    // ***********************************************************************************************
    // * Página Central
    // ***********************************************************************************************

    // **
    // * Inicializa datatable conexões
    // **
	var dtPortas = $('#dt-portas').DataTable({
        dom: '<"table-responsive"t><"row"<"col-lg-6"l><"col-lg-6"p>>r',
		processing: true,
        columns: [  { data: "version", class: "d-none"},
                    { data: "posicao", class: "dt-body-center" }, 
                    { data: "id", class: "dt-body-center" },
                    { data: "sensor", class: "dt-body-center" },
                    { data: "tipo", class: "dt-body-center" },
                    { data: "fator", class: "dt-body-center" },
                    { data: "entrada", class: "dt-body-center" }, 
                    { data: "unidade", class: "dt-body-center" }, 
                    { data: "unidade_tipo", class: "dt-body-center" }, 
                    { data: "leitura", class: "dt-body-center" },
                    { data: "consumo", class: "dt-body-center" },
                    { data: "fraude", class: "dt-body-center" },
                    { data: "ultimo_post", class: "dt-body-center" },
                    { data: "actions", class: "actions dt-body-center" } ],
        ordering: false,
        pageLength: 16,
        lengthMenu: [ 16, 20, 32, 64, 100 ],
        pagingType: "numbers",
		serverSide: true,
		ajax: { url: $('#dt-portas').data('url') },
        drawCallback: function() {$('.inlinebar').sparkline('html', {type: 'line', fillColor: false, width: '80px'}); }
	});

    // **
    // * Inicializa datatable Envios
    // **
	dtEnvios = $('#dt-envios').DataTable({
		dom: '<"table-responsive-md table-sm"t>pr',
		processing: true,
        columns: [ { data: "id", class: "dt-body-center" }, 
                   { data: "tamanho", class: "dt-body-center" },
                   { data: "data", class: "dt-body-center" } ],
        ordering: false,
        pageLength: 24,
        pagingType: "input",
		serverSide: true,
		ajax: { url: $('#dt-envios').data('url') },
        createdRow: function ( row, data ) {
            if (data.returned != null)
                row.title = data.returned;
		}        
    });
    
	$(document).on('click', '.modal-dismiss', function (e) {
		e.preventDefault();
		$.magnificPopup.close();
    });

	$(document).on('click', '#dt-envios tr', function (e) {
		$.magnificPopup.open( {
			items: {src: '/ajax/md_envio'},
			type: 'ajax',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { id: this.id, central: $("span#central").html()}
                }
            }
		});
    });

    $('#auto').change(function() {
        if (this.checked) {
            $.post( '/ajax/auto/on', { id: $("span#central").html() }, function(json) {
                if (json.status == 'success') {
                    $('.card-central').removeClass('disabled');
                    $('.btn-central-conf').hide();
                    // mostra notificação
                    notifySuccess(json.message);
                } else {
                    // mostra erro
                    notifyError(json.message);
                }
            }, 'json').fail(function(xhr, status, error) {
                notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
            });
        } else {
            $.post( '/ajax/auto/off', { id: $("span#central").html() }, function(json) {
                if (json.status == 'success') {
                    $('.card-central').addClass('disabled');
                    $('.btn-central-conf').show();
                    // mostra notificação
                    notifySuccess(json.message);
                } else {
                    // mostra erro
                    notifyError(json.message);
                }
            }, 'json').fail(function(xhr, status, error) {
                notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
            });
        }
    });

    $(document).on('click', '.btn-central-conf', function (e) {
		$.magnificPopup.open( {
			items: {src: '/ajax/md_central_conf'},
			type: 'ajax',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { id: this.id, central: $("span#central").html()}
                }
            }
		});
    });    

    $(document).on('click', '.form-central-conf .modal-reset', function (e) {
        e.preventDefault();
        $("#radio200").prop("checked", true);
        $('input[name="ctl-timestamp"').val('');
    });    

	$(document).on('click', '.form-central-conf .modal-confirm', function () {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
        // pega o valores
        var central =  $("input[name='central']").val();
        var codigo =  $("input[name='optionsRadios']:checked").val();
        var timestamp =  $("input[name='ctl-timestamp']").val();
		// faz a requisição
		$.post("/ajax/set_central_conf", {central: central, codigo: codigo, timestamp: timestamp}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// mostra notificação
				notifySuccess('Retorno da central configurado!');
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
		});
	});

}).apply(this, [jQuery]);