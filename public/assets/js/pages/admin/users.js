(function ($) {

	'use strict';

	let mostra = 0;

	// **
	// * Inicializa datatable
	// **
	let $dtUsers = $('#dt-users').DataTable({
		dom: '<"row"<"col-lg-6"><"col-lg-6"f>><"table-responsive"t><"row"<"col-lg-6 pt-4"l><"col-lg-6"pr>>',
		processing: true,
		columns: [
			{ data: "avatar", orderable: false, class: "dt-body-center" },
			{ data: "nome", orderable: false },
			{ data: "email", orderable: false },
			{ data: "groups", orderable: false },
			{ data: "monitora", orderable: false, className: "dt-body-center monitor" },
			{ data: "page", orderable: false },
			{ data: "status", orderable: false, class: "dt-body-center" },
			{ data: "actions", orderable: false, class: "dt-body-center actions" }
		],
		language: { search: ''},
		order: [],
		serverSide: true,
		ajax: {
			url: $('#dt-users').data('url'),
			data: function (d) {
				return $.extend({}, d, {
					mode: mostra
				});
			}
		},
		fnPreDrawCallback: function () { $('.table-responsive').addClass('processing'); },
		fnDrawCallback: function () {
			$(".switch-input").themePluginIOS7Switch(),
				$('.table-responsive').removeClass('processing');
		}
	});

	$('#dt-users tbody').on('click', 'tr', function (event) {
		// se o clique não foi em uma celula ou na última, retorna
		if (event.target.cellIndex == undefined || event.target.cellIndex == 9) return;
		// pega dados da linha
		var data = $dtUsers.row(this).data();
		// redireciona para o fechamento
		window.location = "/admin/users/" + data.id;
	});


	// **
	// * Handler menu tipo monitoramento
	// **
	$(document).on('click', ".dropdown-menu-config .monitor", function () {
		$('.monitor').children().addClass('fa-none').removeClass('fa-check');
		$(this).children().addClass('fa-check').removeClass('fa-none');
		mostra = $(this).data('mode');
		$dtUsers.ajax.reload();
	});

	$(document).on('click', '#dt-users .action-delete-user', function () {
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
		$.post("/admin/delete_user", { uid: uid }, function (json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
				$dtUsers.ajax.reload(null, false);
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

	$(document).on('click', '.btn-incluir', function (e) {
		e.preventDefault()
		window.location = "/admin/users/incluir";
	})

	// **
	// * Handler Switch
	// **

	$(document).on('click', '.ios-switch', function () {
		let _self = $(this);
		var id = _self.parent().children().last().data("id");
		let cont = $('.ios-switch');
		if (!cont.is(':disabled')) {
			console.log('test2')
			$.ajax({
				method: 'POST',
				url: '/admin/edit_active_stats',
				data: { id: id },
				dataType: 'json',
				success: function (json) {
					if (json.status === "success") {
						// altera status do botão
					} else {
						// notifica erro
						notifyError(json.message);

						// altera status do botão para valor anterior
						if ($(_self).hasClass('on')) {
							$(_self).removeClass('on');
							$(_self).addClass('off');
						} else {
							$(_self).removeClass('off');
							$(_self).addClass('on');
						}
					}
				},
				error: function (xhr, status, error) {
				},
				complete: function () {
				}
			});
		} else {
			return;
		}


	});


	// **
	// * Inclusão de Usuário
	// **

	// **
	// * Inicializa validação do form principal
	// **
	var chk_names = $.map($('input[type=checkbox].require-one'), function (el, i) {
		return $(el).prop("name");
	}).join(" ");

	var valid = $(".form-user").validate({
		ignore: '.no-validate hidden',
		highlight: function (label) { $(label).closest('.form-user').removeClass('has-success').addClass('has-error'); },
		success: function (label) { $(label).closest('.form-user').removeClass('has-error'); label.remove(); },
		groups: {
			checks: chk_names
		},
		errorPlacement: function (error, element) {
			if (element.attr('id') == 'agua-condo') {
				var placement = element.closest('.row');
			} else {
				var placement = element.closest('.input-group');
				if (!placement.get(0)) { placement = element; }
				if (placement.next('span').length) { placement = placement.next('span'); }
			}
			if (error.text() !== '') { placement.after(error); }
		}
	});

	$.post("/admin/get_entity_for_select", function (result) {
		$('#entity-user').html(result);
	})

	$.post("/admin/get_groups_for_select", function (result) {
		$('#group-user').html(result);
	})


	$(document).change('#classificacao-user', function () {
		$('.relation-user-entity').removeAttr('hidden');
		if ($('#classificacao-user').val() === 'entidades') {
			$('.relation-user-unity').attr('hidden', true);
			$('.relation-user-unity').attr('disable', true);

			$('.relation-user-group').attr('hidden', true);
			$('.relation-user-group').attr('disable', true);

			$('.relation-user-entity').removeAttr('hidden');
			$('.relation-user-entity').removeAttr('disable');




		} else if ($('#classificacao-user').val() === 'agrupamentos') {
			$('.relation-user-entity').attr('hidden', true);
			$('.relation-user-entity').attr('disable', true);

			$('.relation-user-unity').attr('hidden', true);
			$('.relation-user-unity').attr('disabled', true);

			$('.relation-user-group').removeAttr('hidden');
			$('.relation-user-group').removeAttr('disabled');


		} else {
			$('.relation-user-entity').attr('hidden', true);
			$('.relation-user-entity').attr('disabled', true);

			$('.relation-user-group').attr('hidden', true);
			$('.relation-user-group').attr('disabled', true);

			$('.relation-user-unity').removeAttr('hidden');
			$('.relation-user-unity').removeAttr('disabled');


		}

	})



	// **
	// * Adiciona validadores especificos
	// **
	$.validator.addClassRules("vnome", { twostring: true });
	$.validator.addClassRules("vemail", { email: true, required: true })
	$.validator.addClassRules("vsenha", { minlength: 5, required: true });
	$.validator.addClassRules("vconfirma", {
		minlength: 5,
		equalTo: "#senha-user"
	});
	$.validator.addClassRules("vpage", { required: true });



	// **
	// * Handler para limpar o form principal
	// **
	$(document).on("click", ".form-entity .btn-reset-user", function () {
		// limpa campos
		$('.form-user').trigger("reset");
		valid.resetForm();
	});

	// **
	// * Handler para salvar novo usuário
	// **
	$(document).on("click", ".form-user .btn-salvar-user", function () {
		// verifica se campos do modal são válidos
		if ($(".form-user").valid()) {
			// mostra indicador

			var $btn = $(this);
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-user .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/add_user', $('.form-user').serialize(), function (json) {
				if (json.status == 'success') {
					//TODO Pergunta se quer cadastrar os blocos
					notifySuccess(json.message)
					// redirect
					setTimeout(() => { 'timeout' }, 3000);
					window.location = ('admin/users')


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



	// **
	// * Edição de Usuário
	// **



	if ($('#classificacao-user-edit').val()) {
		$('.relation-user-entity-edit').removeAttr('hidden');
		if ($('#classificacao-user-edit').val() === 'entidade') {
			$('.relation-user-unity-edit').attr('hidden', true);
			$('.relation-user-unity-edit').attr('disable', true);

			$('.relation-user-group-edit').attr('hidden', true);
			$('.relation-user-group-edit').attr('disable', true);

			$('.relation-user-entity-edit').removeAttr('hidden');
			$('.relation-user-entity-edit').removeAttr('disable');



		} else if ($('#classificacao-user-edit').val() === 'agrupamento') {
			$('.relation-user-entity-edit').attr('hidden', true);
			$('.relation-user-entity-edit').attr('disable', true);

			$('.relation-user-unity-edit').attr('hidden', true);
			$('.relation-user-unity-edit').attr('disabled', true);

			$('.relation-user-group-edit').removeAttr('hidden');
			$('.relation-user-group-edit').removeAttr('disabled');



		} else {
			$('.relation-user-entity-edit').attr('hidden', true);
			$('.relation-user-entity-edit').attr('disabled', true);

			$('.relation-user-group-edit').attr('hidden', true);
			$('.relation-user-group-edit').attr('disabled', true);

			$('.relation-user-unity-edit').removeAttr('hidden');
			$('.relation-user-unity-edit').removeAttr('disabled');


		}
	}

	if (window.location.href != 'admin/users/inserir') {
		$('.btn-alt-senha').on('click', function (e) {
			e.preventDefault();
			$('.vsenha').removeAttr('hidden');
			$('.vsenha').removeAttr('disabled');
			$('.btn-alt-senha').attr('hidden', true);
			$('.senha').removeAttr('hidden');
		})

		$('.cancel-alt').on('click', function (e) {
			e.preventDefault();
			$('.vsenha').attr('hidden', true);
			$('.vsenha').attr('disabled', true);
			$('.btn-alt-senha').removeAttr('hidden');
			$('.senha').attr('hidden', true);
		})
	}


	$(document).on("click", ".btn-back-user", function (e) {
		e.preventDefault();
		document.location.href = '/admin/users';
	});

	// **
	// * Handler para salvar novo usuário
	// **
	$(document).on("click", ".form-user-edit .btn-editar-user", function () {
		// verifica se campos do modal são válidos
		if ($(".form-user-edit").valid()) {
			// mostra indicador

			var $btn = $(this);

			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-user-edit .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/edit_user', $('.form-user-edit').serialize(), function (json) {
				if (json.status == 'success') {
					//TODO Pergunta se quer cadastrar os blocos
					notifySuccess(json.message)
					// redirect
					setTimeout(() => { 'timeout' }, 3000);
					document.location.href = '/admin/users'


				} else {
					// notifica erro
					notifyError(json.message);
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


}).apply(this, [jQuery]);