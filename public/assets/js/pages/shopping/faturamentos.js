(function () {

    "use strict";

    let dtFaturamentos = $("#dt-faturamentos").DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "competencia", class: "dt-body-center"},
            {data: "inicio", class: "dt-body-center" },
            {data: "fim", class: "dt-body-center"},
            {data: "consumo", class: "dt-body-center"},
            {data: "consumo_p", class: "dt-body-center"},
            {data: "consumo_f", class: "dt-body-center"},
            {data: "demanda", class: "dt-body-center"},
//            {data: "demanda_f", class: "dt-body-center"},
            {data: "consumo_u", class: "dt-body-center"},
            {data: "consumo_u_p", class: "dt-body-center"},
            {data: "consumo_u_f", class: "dt-body-center"},
            {data: "demanda_u", class: "dt-body-center"},
            {data: "emissao", class: "dt-body-center"},
            {data: "action", class: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        sorting: [],
        pageLength: 10,
        pagingType: "numbers",
        searching: false,
        ajax: {
            type: 'POST',
            url: $("#dt-faturamentos").data("url"),
            data: function (d) {
                d.gid = $("#dt-faturamentos").data("group");
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-faturamentos").dataTable().fnProcessingIndicator(false);
                $("#dt-faturamentos_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    let dtWater = $("#dt-water").DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "competencia", class: "dt-body-center"},
            {data: "inicio", class: "dt-body-center" },
            {data: "fim", class: "dt-body-center"},
            {data: "consumo", class: "dt-body-center"},
            {data: "consumo_o", class: "dt-body-center"},
            {data: "consumo_c", class: "dt-body-center"},
            {data: "emissao", class: "dt-body-center"},
            {data: "action", class: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        sorting: [],
        pageLength: 10,
        pagingType: "numbers",
        searching: false,
        ajax: {
            type: 'POST',
            url: $("#dt-water").data("url"),
            data: function (d) {
                d.gid = $("#dt-water").data("group");
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-water").dataTable().fnProcessingIndicator(false);
                $("#dt-water_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    // **
	// * Handler abrir modal para novo fechamento
	// **
	$(document).on('click', '.btn-incluir', function (e) {
        // para propagação
        e.preventDefault();

        $("#md-include .alert").html("").addClass("d-none");
        
        // abre modal
		$.magnificPopup.open( {
			items: {src: '#md-include'},
			type: 'inline',
			modal:true,
		});
    });

    // **
	// * Handler abrir modal para novo fechamento
	// **
	$(document).on('click', '.btn-water-incluir', function (e) {
        // para propagação
        e.preventDefault();

        $("#md-water-include .alert").html("").addClass("d-none");
        
        // abre modal
		$.magnificPopup.open( {
			items: {src: '#md-water-include'},
			type: 'inline',
			modal:true,
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
    // * Handler botão inclui novo fechamento na modal
    // * TODO: - verificar validação de competencia, se já existe no ramal...no php talvez
    // * - verificar range de datas...pra não sobrepor, no php talvez
    // * - verificar leituras...pra não sobrepor, no php talvez ou trocar pra consumo só...
	// **
	$(document).on('click', '#md-include .modal-confirm', function (e) {
        // para propagação
		e.preventDefault();

        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);
        $("#md-include .btn").prop("disabled", true);

        $("#md-include .alert").html("").addClass("d-none");        

        // valida formulário
		if ( $(".form-fechamento").valid() ) {
			// captura dados
			var data = $('.form-fechamento').serializeArray();
			// envia os dados
			$.post('/energia/faturamento', data, function(json) {
				if (json.status == 'success') {
                    // vai para a pagina do fechamento
                    window.location = "/shopping/lancamento/energia/" + $("#dt-faturamentos").data("group") + "/" + json.id;
                    
                    // fecha a modal
                    $.magnificPopup.close();

                } else {

                    $("#md-include .alert").html(json.message).removeClass("d-none");
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// mostra erro
				notifyError(error, 'Ajax Error');
			})
			.always(function() {
                $btn.trigger("loading-overlay:hide");
                $("#md-include .btn").removeAttr("disabled");
			});
		}
    });

	$(document).on('click', '.modal-water-confirm', function (e) {
        // para propagação
		e.preventDefault();
        
        if (!$(".form-water-fechamento").valid())
            return;

        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);
        $("#md-water-include .btn").prop("disabled", true);

        $("#md-water-include .alert").html("").addClass("d-none");        

        // valida formulário
		if ( $(".form-water-fechamento").valid() ) {
			// captura dados
			var data = $('.form-water-fechamento').serializeArray();
			// envia os dados
			$.post('/water/lancamento', data, function(json) {
				if (json.status == 'success') {
                    // vai para a pagina do fechamento
                    window.location = "/shopping/lancamento/agua/" + $("#dt-faturamentos").data("group") + "/" + json.id;
                    
                    // fecha a modal
                    $.magnificPopup.close();

                } else {

                    $("#md-water-include .alert").html(json.message).removeClass("d-none");
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// mostra erro
				notifyError(error, 'Ajax Error');
			})
			.always(function() {
                $btn.trigger("loading-overlay:hide");
                $("#md-water-include .btn").removeAttr("disabled");
			});
		}
    });

    // **
	// * Handler Action Abrir modal confirmação para excluir fechamento
	// **
	$(document).on('click', '.action-delete', function () {

        var dis_timer, id = $(this).data('id');
        
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
                    $('#modalExclui .id').val( id );
                    $('#modalExclui .type').val('energia');
                },
                open: function() {
                    // desabilita botão
                    var btn = $('#modalExclui .modal-confirm');
                    btn.prop("disabled", true);
                    // inicializa timer
                    var sec = btn.data('timer');
                    // declaração do timer regressimo
                    function countDown() {
                        // mostra valor
                        btn.html(sec);
                        if (sec <= 0) {
                            // terminou. Habilita botão e atualiza texto
                            btn.prop("disabled", false);
                            btn.html('Excluir');
                            return;
                        }
                        // continua contando
                        sec -= 1;
                        dis_timer = setTimeout(countDown, 1000);
                    }
                    countDown();
                },
                close: function() {
                    clearTimeout(dis_timer);
                }
			}
		});
	});

    // **
	// * Handler Action Abrir modal confirmação para excluir fechamento
	// **
	$(document).on('click', '.action-water-delete', function () {

        var dis_timer, id = $(this).data('id');
        
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
                    $('#modalExclui .id').val( id );
                    $('#modalExclui .type').val('water');
                },
                open: function() {
                    // desabilita botão
                    var btn = $('#modalExclui .modal-confirm');
                    btn.prop("disabled", true);
                    // inicializa timer
                    var sec = btn.data('timer');
                    // declaração do timer regressimo
                    function countDown() {
                        // mostra valor
                        btn.html(sec);
                        if (sec <= 0) {
                            // terminou. Habilita botão e atualiza texto
                            btn.prop("disabled", false);
                            btn.html('Excluir');
                            return;
                        }
                        // continua contando
                        sec -= 1;
                        dis_timer = setTimeout(countDown, 1000);
                    }
                    countDown();
                },
                close: function() {
                    clearTimeout(dis_timer);
                }
			}
		});
	});

    // **
	// * Handler Button excluir fechamento
	// **
	$(document).on('click', '#modalExclui .modal-confirm', function (e) {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
		var id = $('#modalExclui .id').val();
        var type = $('#modalExclui .type').val();
		// faz a requisição
		$.post("/"+type+"/DeleteLancamento", {id: id}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
				dtFaturamentos.ajax.reload( null, false );
                dtWater.ajax.reload( null, false );
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
            $('#modalExclui .type').val('');
		});
    });

    $(document).on("click", ".btn-download", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/energia/DownloadLancamentos", {id: $(this).data("group")}, function (json) {

                if (json.status == "error") {
                    
                    notifyError(json.message);

                } else {
                    var $a = $("<a>");
                    $a.attr("href", json.file);
                    $("body").append($a);
                    $a.attr("download", json.name + ".xlsx");
                    $a[0].click();
                    $a.remove();
                }
            },
            "json"
        )
            .fail(function (xhr, status, error) {
                // mostra erro
                notifyError(error, "Ajax Error");
            })
            .always(function () {
                // oculta indicador e habilita botão
                $btn.trigger("loading-overlay:hide").prop("disabled", false);
            });
    });

    $(document).on("click", ".btn-water-download", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/water/DownloadLancamentos", {id: $(this).data("group")}, function (json) {

                if (json.status == "error") {
                    
                    notifyError(json.message);

                } else {
                    var $a = $("<a>");
                    $a.attr("href", json.file);
                    $("body").append($a);
                    $a.attr("download", json.name + ".xlsx");
                    $a[0].click();
                    $a.remove();
                }
            },
            "json"
        )
            .fail(function (xhr, status, error) {
                // mostra erro
                notifyError(error, "Ajax Error");
            })
            .always(function () {
                // oculta indicador e habilita botão
                $btn.trigger("loading-overlay:hide").prop("disabled", false);
            });
    });

    $(document).on("click", ".action-download", function () {

        var $btn = $(this);
		$btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
		$.post("/energia/download", { id: $(this).data('id') }, function(json) {

            if (json.status == "error") {
                    
                notifyError(json.message);

            } else {

                var $a = $("<a>");
                $a.attr("href", json.file);
                $("body").append($a);
                $a.attr("download", json.name + '.xlsx');
                $a[0].click();
                $a.remove();
            }
		}, 'json')
		.fail(function(xhr, status, error) {
			// mostra erro
            notifyError(error, 'Ajax Error');
        })
		.always(function() {
			// oculta indicador e habilita botão
			$btn.html('<i class="fas fa-file-download"></i>');
        });
    });

    $(document).on("click", ".action-water-download", function () {

        var $btn = $(this);
		$btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
		$.post("/water/download", { id: $(this).data('id') }, function(json) {

            if (json.status == "error") {
                    
                notifyError(json.message);

            } else {

                var $a = $("<a>");
                $a.attr("href", json.file);
                $("body").append($a);
                $a.attr("download", json.name + '.xlsx');
                $a[0].click();
                $a.remove();
            }
		}, 'json')
		.fail(function(xhr, status, error) {
			// mostra erro
            notifyError(error, 'Ajax Error');
        })
		.always(function() {
			// oculta indicador e habilita botão
			$btn.html('<i class="fas fa-file-download"></i>');
        });
    });

    $(document).on("click", ".btn-cfg", function (event) {
        window.location = "/shopping/configuracoes/" + $("#dt-faturamentos").data("group") + "#unidades";
    });

    $("#dt-faturamentos tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined || event.target.cellIndex == 12) return;

        let data = dtFaturamentos.row(this).data();
        // redireciona para o fechamento
        window.location = "/shopping/lancamento/energia/" + $("#dt-faturamentos").data("group") + "/" + data.id;
    });

    $("#dt-water tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined || event.target.cellIndex == 7) return;

        let data = dtWater.row(this).data();
        // redireciona para o fechamento
        window.location = "/shopping/lancamento/agua/" + $("#dt-water").data("group") + "/" + data.id;
    });

    $.validator.addClassRules("vdate", { dateBR : true });
    $.validator.addClassRules("vcompetencia", { competencia : true });
    $.validator.addClassRules("vnumber", { number : true });

    $('#tar-competencia').mask('00/0000');
    $('#tar-data-ini').mask('00/00/0000');
    $('#tar-data-fim').mask('00/00/0000');
    $('#tar-water-competencia').mask('00/0000');
    $('#tar-water-data-ini').mask('00/00/0000');
    $('#tar-water-data-fim').mask('00/00/0000');
    // configura validação
    $(".form-fechamento").validate();
    $(".form-water-fechamento").validate();

    //DEV
/*    
    $('#tar-competencia').val('01/2023');
    $('#tar-data-ini').val('01/01/2023');
    $('#tar-data-fim').val('09/01/2023');
    $('#tar-tusd-p').val('0,10092');
    $('#tar-tusd-f').val('0,10092');
    $('#tar-demanda-p').val('37,32242');
    $('#tar-demanda-f').val('14,75010');
*/
}.apply(this, [jQuery]));