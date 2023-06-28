(function () {

    "use strict";

    let $dtUnidades = $("#dt-unidades");
    let dtUnidades = $dtUnidades.DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "m_id", className: "d-none", orderable: false},
            {data: "u_id", className: "d-none", orderable: false},
            {data: "medidor", className: "dt-body-center", orderable: false},
            {data: "device", className: "dt-body-center", orderable: false},
            {data: "unidade", className: "dt-body-center", orderable: false},
            {data: "bloco", className: "dt-body-center table-one-line", orderable: false},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
        serverSide: true,
        sorting: [],
        pageLength: 25,
        pagingType: "numbers",
        searching: true,
        ajax: {
            url: $dtUnidades.data("url"),
            method: 'POST',
            data: {
                entidade: $(".content-body").data("entidade"),
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $dtUnidades.dataTable().fnProcessingIndicator(false);
                $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function() {
            $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
        }
    });

    $(document).on('click', '.generate-code', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_generate_code'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {}
                },
            }
        });
    });

    $(document).on('click', '#md-generate-code .modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();

        $(".generate-code").remove();
        $(".request-code-btn").show(300);
    });

    $(document).on('click', '.request-code', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_request_code'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {}
                },
            }
        });
    });

    $(document).on('click', '#md-request-code .modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

    let dtUsuarios = $("#dt-usuarios").DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "id", className: "d-none", orderable: false},
            {data: "avatar", className: "d-none", orderable: false},
            {data: "nome", className: "dt-body-center", orderable: false},
            {data: "email", className: "dt-body-center", orderable: false},
            {data: "bloco", className: "dt-body-center", orderable: false},
            {data: "apto", className: "dt-body-center table-one-line", orderable: false},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
        serverSide: true,
        sorting: [],
        pageLength: 25,
        pagingType: "numbers",
        searching: true,
        ajax: {
            url: $("#dt-usuarios").data("url"),
            method: 'POST',
            data: function(d) {
                d.entidade = $("#sel-entity").val();
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-usuarios").dataTable().fnProcessingIndicator(false);
                $("#dt-usuarios_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function() {
            $("#dt-usuarios_wrapper .table-responsive").removeClass("processing");
        }
    });

    $(document).on('click', '.btn-incluir-usuario', function (e) {
        e.preventDefault();

        $.magnificPopup.open( {
            items: {src: '/consigaz/md_add_user'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: { 
                        eid: $(this).data('eid'), 
                        name: $(this).data('name') 
                    }
                },
            }
        });
    })

    $(document).on('click', '#md-add-user .modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

    $.validator.addClassRules("vnome", { twostring: true });
	$.validator.addClassRules("vemail", { email: true, required: true })
	$.validator.addClassRules("vsenha", { minlength: 5, required: true });
	$.validator.addClassRules("vconfirma", {
		minlength: 5,
		equalTo: "#senha-user"
	});

    $(document).on("click", ".form-add .modal-confirm", function () {
        // verifica se campos do modal são válidos
        if ($(".form-add").valid()) {
            // mostra indicador
            var $btn = $(this);
            $btn.trigger('loading-overlay:show');
            // desabilita botões
            var $btn_d = $('.form-add .btn:enabled').prop('disabled', true);
            // envia os dados
            var data = $('.form-add').serialize();
            var unity = $('.select-unity:selected').data('val');
            var payload = {
                data: data,
                unity: unity
            };
            $.post('/consigaz/add_user', payload, function (json) {
                if (json.status == 'success') {
                    //TODO Pergunta se quer cadastrar os blocos
                    notifySuccess(json.message);
                    // refresh dt
                    dtUsuarios.ajax.reload();
                    // close modal
                    $.magnificPopup.close();
                } else {
                    // notifica erro
                    notifyError(json.message.message);
                }
            }, 'json')
            .fail(function (xhr, status, error) {
                // falha no ajax: notifica
                notifyError(error);
            })
            .always(function () {
                // oculta indicador
                $btn.trigger('loading-overlay:hide');
                // habilita botões
                $btn_d.prop('disabled', false);
            });
        }
    });

    $(document).on('click', '#md-add-user .modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

    $(document).on('click', '#dt-usuarios .action-delete', function () {
		var uid = $(this).data('id');
		// abre a modal
		$.magnificPopup.open({
			items: { src: '#modalExclui' }, type: 'inline',
			callbacks: {
				beforeOpen: function () {
					$('#modalExclui .id').data('uid', uid);
				}
			}
		});
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
		$.post("/consigaz/delete_user", { uid: uid }, function (json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
				dtUsuarios.ajax.reload();
				// mostra notificação
				notifySuccess(json.message);
			} else {
				// fecha modal
				$.magnificPopup.close();
				// mostra erro
				notifyError(json.message);
			}
		}, 'json')
			.fail(function (xhr, status, error) {
				// fecha modal
				$.magnificPopup.close();
				// mostra erro
				notifyError(error, 'Ajax Error');
			})
			.always(function () {
				// oculta indicador e habilita botão
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
				// limpa id
				$('#modalExclui .id').val('').data('uid', null);
			});
	});



}.apply(this, [jQuery]));