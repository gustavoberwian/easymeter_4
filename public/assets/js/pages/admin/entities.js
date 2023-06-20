/*jshint -W100*/
// **
// * Entities
// **
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
	let $dtEntities = $('#dt-entities').DataTable({
		dom: '<"row"<"col-lg-6"><"col-lg-6 text-right"f>><"table-responsive"t><"row"<"col-lg-6 pt-4"l><"col-lg-6"pr>>',
		processing: true,
		pageLength: 10,
		columns: [
			{ data: "id", visible: false },
			{ data: "nome", className: "table-one-line" },
			{ data: "tipo", className: "dt-body-center" },
			{ data: "classificacao", className: "dt-body-center" },
			{ data: "monitoramento", orderable: false, className: "dt-body-center monitor"},
			{ data: "cidade", className: "table-one-line" },
			{ data: "nome_adm", className: "table-one-line" },
			{ data: "nome_gestor", className: "table-one-line" },
			{ data: "status", visible: false },
			{ data: "action", orderable: false, className: "actions dt-body-center"}
		],
		language: {search: ''},
        serverSide: true,
        pagingType: "numbers",
		ajax: {
			url: $('#dt-entities').data('url'),
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

    // **
    // * Handler menu mostra inativos
    // **
	$(document).on('click',".dropdown-menu-config .inativos", function () {
        $(this).children().toggleClass('fa-check fa-none');
		$dtEntities.ajax.reload();
    });	

    // **
    // * Handler menu tipo monitoramento
    // **
	$(document).on('click',".dropdown-menu-config .monitor", function () {
		$('.monitor').children().addClass('fa-none').removeClass('fa-check');
        $(this).children().addClass('fa-check').removeClass('fa-none');
		mostra = $(this).data('mode');
		$dtEntities.ajax.reload();
    });	

 	// **
	// * Handler redireciona para inclusão de entidade
	// **
	$(document).on('click', '.btn-incluir', function() {

		window.location = "/admin/entities/incluir";
	});
	
	// **
	// * Handler Action Excluir Entidade
	// **
	$(document).on('click', '#dt-entities .action-delete', function () {
		var id = $(this).data('id');
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
					$('#modalExclui .id').val( id );
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
		// fecha a modal
		$.magnificPopup.close();
	});
	
	// **
	// * Handler Botão Excluir Modal Confirmação Exclusão Condominio
	// **
	$(document).on('click', '#modalExclui .modal-confirm', function () {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
		var id = $('#modalExclui .id').val();
		// faz a requisição
		$.post("/admin/delete_entity", {id: id}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
				$dtEntities.ajax.reload( null, false );
				// mostra notificação
				notifySuccess('Entidade excluída com sucesso');
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
		});
	});
	
    // ***********************************************************************************************
    // * Inclusão de Condominio
    // ***********************************************************************************************
	
    // **
    // * Inicializa validação do form principal
    // **
    var chk_names = $.map($('input[type=checkbox].require-one'), function (el, i) {
        return $(el).prop("name");
    }).join(" ");

	var valid = $(".form-entity").validate({
		ignore: '.no-validate',
		highlight: function( label ) { $(label).closest('.form-entity').removeClass('has-success').addClass('has-error'); },
        success: function( label ) { $(label).closest('.form-entity').removeClass('has-error'); label.remove(); },
        groups: {
            checks: chk_names
        },        
		errorPlacement: function( error, element ) {
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

    // **
    // * Inicializa validação do form gestor (na modal)
    // **
	var valid_gestor = $(".form-gestor").validate({

	});

    // **
    // * Inicializa validação do form administradora (na modal)
    // **
	var valid_adm = $(".form-adm").validate({
		errorPlacement: function( error, element ) {
			var placement = element.closest('.input-group');
			if (!placement.get(0)) { placement = element; }
			if (placement.next('span').length) { placement = placement.next('span'); }
			if (error.text() !== '') { placement.after(error); }
		}
		
	});

	 // **
    // * Select2 Administradora valida a cada mudança
    // **
	
	$('#select-adm').select2({}).on("change", function () {
		if (!$.isEmptyObject($('#select-adm').val())) {
			valid.element( "#select-adm" );		
		} 
    });

	/*
	jQuery.validator.addMethod('greaterThan', function(value, element, param) {
		return ( parseInt(value) > parseInt(jQuery(param).val()) );
	}, 'Deve ser maior que o inicial' );

	jQuery.validator.addMethod('lesserThan', function(value, element, param) {
		return ( parseInt(value) < parseInt(jQuery(param).val()) );
	}, 'Deve ser menor que o final' );

    jQuery.validator.addMethod('require-one', function (value) {
        return ($('input[type=checkbox].require-one').filter(':checked').length > 0);
    }, 'Selecione pelo menos um tipo de monitoramento');
*/
    // **
    // * Adiciona validadores especificos
    // **
	$.validator.addClassRules("vnome", { twostring : true });
	$.validator.addClassRules("vcnpj", { cnpj : true });
	$.validator.addClassRules("vdate", { dateBR : true });
	$.validator.addClassRules("vcpf", { cpfBR : true });
	$.validator.addClassRules("vtelefone", { telefone : true });
	$.validator.addClassRules("vgreater", { greaterThan: "#tar-leitura-ini"});
	$.validator.addClassRules("vlesser", { lesserThan: "#tar-leitura-fim"});

    // **
    // * Inicializa Mascaras
    // **
	var SPMaskBehavior = function (val) { return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'; };
	// form principal
	$('#cnpj-entity').mask('00.000.000/0000-00');
	$("#cep-entity").mask("99999-999", {
		onComplete: function() { $('.btn-busca').prop('disabled', false); },
		onChange: function() { $('.btn-busca').prop('disabled', true); }
	} );
	$('#inicio-entity').mask('00/00/0000');
	$('#fim-entity').mask('00/00/0000');
	// form gestor
	$('#cpf-gestor').mask('000.000.000-00');
	$('#nasc-gestor').mask('00/00/0000');
	// form administradora
	$('#cnpj-adm').mask('00.000.000/0000-00');
	$("#cep-adm").mask("99999-999", {
		onComplete: function() { $('.btn-busca-adm').prop('disabled', false); },
		onChange: function() { $('.btn-busca-adm').prop('disabled', true); }
	} );
	// varios
	$('.celular').mask(SPMaskBehavior, {
		onKeyPress: function(val, e, field, options) {
			field.mask(SPMaskBehavior.apply({}, arguments), options);
		}
	});
	
	var $select_gestor = $('#select-gestor').select2( {
		ajax: {
			url: $('#select-gestor').data('url'),
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term,
					page: params.page
				};
			}
		},
		theme: 'bootstrap', language: 'pt-BR', placeholder: "Selecione o Gestor",
		width: '',
		escapeMarkup: function (markup) { return markup; },
		templateResult: function (data) { return data.nome || data.text; },
		templateSelection: function (data) { return data.nome || data.text; }
	})
	.on('select2:open', () => {
		$(".select2-results:not(:has(a))").append('<a href="#" class="select2-add-item add-gestor"><i class="fa fa-plus"></i> Incluir novo Gestor</a>');
		$(".select2-search--dropdown .select2-search__field").attr("placeholder", "Pesquise pelo Nome ou CPF");
	});

    // **
    // * Inicializa Select2 Administradora
    // **
	var $select_adm = $('#select-adm').select2( {
		ajax: {
			url: $('#select-adm').data('url'),
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term,
					page: params.page
				};
			}
		},
		theme: 'bootstrap', language: 'pt-BR', placeholder: "Selecione a Administradora",
		width: '',
		//escapeMarkup: function (markup) { return markup; },
		templateResult: function (data) { return data.nome || data.text; },
		templateSelection: function (data) { return data.nome || data.text; }
	})
	.on('select2:open', () => {
		$(".select2-results:not(:has(a))").append('<a href="#" class="select2-add-item add-adm"><i class="fa fa-plus"></i> Incluir nova Administradora</a>');
		$(".select2-search--dropdown .select2-search__field").attr("placeholder", "Pesquise pelo Nome da Adminstradora");
	});
	
    // **
    // * Select2 gestor valida a cada mudança
    // **
	$(document).on("change", "#select-gestor", function () {
		if (!$.isEmptyObject(valid.submitted)) {
			valid.element( "#select-gestor" );
		}
    });

   
	
		
    // **
    // * Handler para limpar o form principal
    // **
	$(document).on("click", ".form-entity .btn-reset", function()
	{
		// limpa campos
		$('.form-entity').trigger("reset");
		// oculta ramais
		$('.ramais-input').addClass('no-validate');
		$(".ramais").hide(200);
		// limpa tags
		//$('.ramais-input').tagsinput('removeAll');
		// limpa msg error
		valid.resetForm();
    });

	// **
    // * Handler para salvar novo condominio
    // **
	$(document).on("click", ".form-entity .btn-salvar", function()
	{
		// verifica se campos do modal são válidos
		if ( $(".form-entity").valid() ) {
			// mostra indicador
			
			var $btn = $(this);
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-entity .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/add_entity', $('.form-entity').serialize(), function(json) {
				if (json.status == 'success') {
					//TODO Pergunta se quer cadastrar os blocos
					notifySuccess(json.message)
					// redirect
					setTimeout(() => {'timeout'},3000);
					window.location = (json.id + '/editar')

					
				} else if(json.message.code == 1062) {
					// cnpj jé existe...avisa
					notifyError('Já existe um Condomínio cadastrado com o CNPJ informado!');
				} else {
					// notifica erro
					notifyError(json.message.message);
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				notifyError(error);
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
    });
	
	// **
	// * Handler editar condominio
	// ** 

	$(document).on("click", ".form-entity .btn-edit-cond", function()
	{
		// verifica se campos do modal são válidos
		if ( $(".form-entity").valid() ) {
			// mostra indicador
			
			var $btn = $(this);
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-entity .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/edit_entity', $('.form-entity').serialize(), function(json) {
				if (json.status == 'success') {
					//TODO Pergunta se quer cadastrar os blocos
					notifySuccess(json.message)	
				} else if(json.message.code == 1062) {
					// cnpj jé existe...avisa
					notifyError('Já existe um Condomínio cadastrado com o CNPJ informado!');
				} else {
					// notifica erro
					notifyError(json.message.message);
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				notifyError(error);
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
    });

	// **
    // * Handler Botão voltar em Edita condominio
    // **
	$(document).on("click", ".form-entity .btn-back", function()
	{
		window.location = "/admin/entities";
	});	
	
 	// **
	// * Handler Abrir Modal gestor
	// **
	$(document).on('click', '.add-gestor', function() {
		// fecha o select
		$select_gestor.select2('close');
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalGestor'}, type: 'inline',
			callbacks: {
				close: function() {
					// limpa campos do modal
					$('.form-gestor').trigger("reset");
					valid_gestor.resetForm();
					$('.form-gestor .notification').html('').hide();
				}
			}
		});
	});

	// **
	// * Handler Salva gestor
	// **
	$(document).on('click', '#modalGestor .modal-confirm', function () {

		// verifica se campos do modal são válidos
		if ( $("#modalGestor .form-gestor").valid() ) {
			// mostra indicador
			var $btn = $(this);
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-gestor .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/add_gestor', $('.form-gestor').serialize(), function(json) {
				if (json.status == 'success') {
					// seleciona o novo item
					var sel = $('#select-gestor');
					var option = new Option(json.message.nome, json.message.id, true, true);
					sel.append(option).trigger('change');
					sel.trigger({ type: 'select2:select', params: { data: json.message } });					
					// fecha a modal
					$.magnificPopup.close();
					// notifica do sucesso
					notifySuccess('Novo Síndico/Gestor inserido com sucesso!');
					
				} else if(json.message.code == 1062) {
					// cpf jé existe...avisa
					$('.form-gestor .notification').html('Já existe um Síndico/Gestor com o CPF informado!').show();
				} else {
					// notifica erro
					$('.form-gestor .notification').html(json.message.message).show();
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				$('.form-gestor .notification').html('<strong>Ajax Error:</strong> ' + error).show();
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
	});
	
 	// **
	// * Handler Abrir Modal Administradora
	// **
	$(document).on('click', '.add-adm', function() {
		// fecha o select
		$select_adm.select2('close');
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalAdm'}, type: 'inline',
			callbacks: {
				close: function() {
					// limpa campos do modal
					$('.form-adm').trigger("reset");
					valid_adm.resetForm();
					$('.form-adm .notification').html('').hide();
				}
			}
		});
	});

	// **
	// * Handler Salva Administradora
	// **
	$(document).on('click', '.modal-adm-confirm', function ()
	{
		// verifica se campos do modal são válidos
		if ( $(".form-adm").valid() ) {
			// mostra indicador
			var $btn = $(this);
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-adm .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/add_adm', $('.form-adm').serialize(), function(json) {
				if (json.status == 'success') {
					// seleciona o novo item
					var sel = $('#select-adm');
					var option = new Option(json.message.nome, json.message.id, true, true);
					sel.append(option).trigger('change');
					sel.trigger({ type: 'select2:select', params: { data: json.message } });					
					// fecha a modal
					$.magnificPopup.close();
					// notifica do sucesso
					notifySuccess('Nova Administradora inserida com sucesso!');
					
				} else if(json.message.code == 1062) {
					// cnpj jé existe...avisa
					$('.form-adm .notification').html('Já existe uma Administradora com o CNPJ informado!').show();
				} else {
					// notifica erro
					$('.form-adm .notification').html(json.message.message).show();
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				$('.form-adm .notification').html('<strong>Ajax Error:</strong> ' + error).show();
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
	});

	// **
	// * Handler Botão Completar Endereço
	// **
	$(document).on('click', '.btn-busca', function () {
		var $btn = $(this);
		// mostra indicador
		$btn.trigger('loading-overlay:show');
		// requisita info
		$.post( "/admin/busca_endereco", { cep: $('#cep-entity').val() }, function(json) {
			if (json.hasOwnProperty('erro')) {
				notifyAlert('CEP não encontrado');
			} else {
				// completa campos e destaca
				$('#logradouro-entity').val(json.logradouro).addClass('glow');
				$('#bairro-entity').val(json.bairro).addClass('glow');
				$('#cidade-entity').val(json.localidade).addClass('glow');
				$("#estado-entity").val(json.uf).addClass('glow');
				$('#numero-entity').focus();
				// limpa erros
				valid.element( "#logradouro-entity" );
				valid.element( "#bairro-entity" );
				valid.element( "#cidade-entity" );
				valid.element( "#estado-entity" );
				// retira destaque
				setTimeout( function() {
					$('input, select').removeClass('glow');
				}, 2000);
			}
		}, 'json')
  		.fail(function() {
			// falha no ajax: notifica
			notifyError('Não foi possível completar o endereço. Tente novamente em alguns minutos.');
  		})
  		.always(function() {
			// oculta indicador
    		$btn.trigger('loading-overlay:hide');
  		});
	});

	// **
	// * Handler Botão Completar Endereço Form Administradora
	// **
	$(document).on('click', '.btn-busca-adm', function () {
		var $btn = $(this);
		// mostra indicador
		$btn.trigger('loading-overlay:show');
		// requisita info
		$.post( "/admin/busca_endereco", { cep: $('#cep-adm').val() }, function(json) {
			if (json.hasOwnProperty('erro')) {
				notifyAlert('CEP não encontrado');
			} else {
				// completa campos e destaca
				$('#logradouro-adm').val(json.logradouro).addClass('glow');
				$('#bairro-adm').val(json.bairro);
				$('#cidade-adm').val(json.localidade).addClass('glow');
				$("#estado-adm").val(json.uf).addClass('glow');
				$('#numero-adm').focus();
				// limpa erros
				valid_adm.element( "#logradouro-adm" );
				valid_adm.element( "#cidade-adm" );
				valid_adm.element( "#estado-adm" );
				// retira destaque
				setTimeout( function() {
					$('input, select').removeClass('glow');
				}, 2000);
			}
		}, 'json')
  		.fail(function() {
			// falha no ajax: notifica
			notifyError('Não foi possível completar o endereço. Tente novamente em alguns minutos.');
  		})
  		.always(function() {
			// oculta indicador
    		$btn.trigger('loading-overlay:hide');
  		});
	});

//################2
	
	

	
	$(document).on("click", ".btn-cadastro", function(e)
	{
		e.preventDefault();
		$('.tab-form').addClass('d-none');
		$('.tab-form.cadastro').removeClass('d-none');
		$('.card-actions.buttons button').removeClass('active');
		$(this).addClass('active');
	});

	$(document).on("click", ".btn-entradas", function(e)
	{
		e.preventDefault();
		$('.tab-form').addClass('d-none');
		$('.tab-form.entradas').removeClass('d-none');
		$('.card-actions.buttons button').removeClass('active');
		$(this).addClass('active');
	});

    $(document).on("click", ".btn-unidades", function(e)
	{
		e.preventDefault();
		$('.tab-form').addClass('d-none');
		$('.tab-form.unidades').removeClass('d-none');
		$('.card-actions.buttons button').removeClass('active');
		$(this).addClass('active');
	});

	$(document).on("click", ".btn-leituras", function(e)
	{
		e.preventDefault();
		$('.tab-form').addClass('d-none');
		$('.tab-form.leituras').removeClass('d-none');
		$('.card-actions.buttons button').removeClass('active');
		$(this).addClass('active');
	});

    $(document).on("click", ".btn-configuracoes", function(e)
	{
		e.preventDefault();
		$('.tab-form').addClass('d-none');
		$('.tab-form.configuracoes').removeClass('d-none');
		$('.card-actions.buttons button').removeClass('active');
		$(this).addClass('active');
	});

    $(document).on("click", ".btn-consumo", function(e)
	{
		e.preventDefault();
		$('.tab-form').addClass('d-none');
		$('.tab-form.consumo').removeClass('d-none');
		$('.card-actions.buttons button').removeClass('active');
		$(this).addClass('active');
	});

    $(document).on("click", ".btn-geral", function(e)
	{
		e.preventDefault();
		$('.tab-form').addClass('d-none');
		$('.tab-form.geral').removeClass('d-none');
		$('.card-actions.buttons button').removeClass('active');
		$(this).addClass('active');
	});

    // **
    // * Handler para incluir novo campo de Prumada
    // **
	$(document).on("click", ".form-bloco .btn-add-prumada", function()
	{
		var hasSlot = false;
		$('.prumada-group input').each(function() {
			if ($(this).val() == '') {
				$(this).focus();
				hasSlot = true;
				return;
			}
		});
		if (!hasSlot) {
			var html = '<div class="input-group mt-3"><input name="id-prumada[' + $('.input-prumada').length + ']" class="form-control input-prumada" placeholder="Identificador da Prumada" required>' +
				'<span class="input-group-append"><button class="btn btn-danger btn-prumada-remove" type="button"><i class="fa fa-times"></i></button></span></div>';

			$(".prumada-group").append(html);
			$(".prumada-group input:last").focus();
		}
    });
	
	var prumadas_delete = [];
    // **
    // * Handler para excluir campo de Prumada
    // **
	$(document).on("click", ".form-bloco .btn-prumada-remove", function()
	{
		prumadas_delete.push($(this).data('id'));
        $(this).parents(".input-group").remove();
    });

    // **
    // * Handler para habilitar botão nova prumada
    // **
	$(document).on('keyup', '.form-bloco .prumada-group input:last', function () {
        if ($(this).val() == '') {
			$('.btn-add-prumada').prop('disabled', true);
        } else {
            $('.btn-add-prumada').prop('disabled', false);
        }
    });	
    	
    // **
    // * Handler Modal Inclui/Edita Novo Bloco
    // **
	$(document).on('click', '.form-bloco .modal-confirm', function (e) {
		e.preventDefault();

		if ( $(".form-bloco").valid() ) {
			// carrega e mostra indicador
			var $btn = $(this).loadingOverlay();
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-bloco .btn:enabled').prop('disabled', true);
			// captura dados	
			var data = $('.form-bloco').serializeArray();
			var arr = [];
			var result = [];
			jQuery.each(data, function(i, field) {
				if(field.name == 'sel-ramal'){
					result.push(field);	
				} else if (field.name == 'id-bloco'){
					arr.push(field);
				}
			});
			arr.push({'name': 'sel-ramal', 'value': JSON.stringify(result)});
			arr.push({'name': 'id-condo', 'value': $('.form-entity #id-entity').val()});
			// referencia para area de notificações
			var $msg = $('.form-bloco .notification').html('').hide();
			// envia os dados
			$.post('/admin/edit_agrupamento', arr, function(json) {
				if (json.status == 'success') {
                    // esconde alerta
                    $('.alert.alert-warning.unidades').hide();
                    // verifica se é inclusão
					if(json.hasOwnProperty('data')){
						// insere e seleciona o novo item na select
						$('.form-entity #sel-bloco').append($('<option>', {
							value: json.data.value,
							text : json.data.text,
							selected: true
                        })).trigger('change');
                        // habilita botões de edição do bloco
                        $('.btn-bloco-edit').prop('disabled', false);
                        $('.btn-bloco-delete').prop('disabled', false);
                        // habilita inserção de unidades
                        $('.btn-inclui-unidade').prop('disabled', false);
                        // retira blur
                        $('.tab-form.unidades').removeClass('inactive');
					} else {
                        // atualiza option
                        $(".form-entity #sel-bloco").find("option:selected").text(json.text);
                    }
					// fecha a modal
					$.magnificPopup.close();
					// notifica do sucesso
					notifySuccess(json.message);
				} else {
					// ERRO
					if(json.message.code == 1062) {
						if (json.message.message.indexOf('agrupamento_id') > -1) {
							$msg.html('As prumadas não podem ter nomes iguais!');
						} else {
							$msg.html('Já existe um bloco com este nome no condomínio!');
						}
					} else if(json.message.code == 1451) {
						$msg.html('A prumadas não podem ser removidas pois já possuem unidades e/ou medidores cadastrados!');
					} else {
						$msg.html(json.message.message);
					}
					$msg.show();
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				$msg.html('<strong>Ajax Error:</strong> ' + error).show();
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
    });
	
	$(document).on('change', '#sel-bloco', function()
	{
        // ajax to load colums define
        // set colums define
        dtUnidades.ajax.reload();
	});

	$(document).on('click', '.btn-bloco-delete', function(e)
	{
		// para propagação
		e.preventDefault();
		//
		var id = $('.form-entity #sel-bloco').val();
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalBlocoRemove'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
					$('#modalBlocoRemove input.id').val( id );
				}
			}
		});
/*		
		if (dtUnidades.data().length > 0) {
			// avisa q bloco possui unidades cadastradas
			notifyAlert('Não é possível excluir o bloco pois ele já possui unidades cadastradas.');
		} else {
			notifySuccess('Bloco excluído com sucesso.');
		}
*/		
	});

    $(document).on('click', '.btn-bloco-edit', function () {
		$.magnificPopup.open( {
			items: {src: '/admin/md_bloco'},
			type: 'ajax',
			focus: '#id-bloco',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { cid: $("#id-entity").val(), bid: $("#sel-bloco option:selected").val()}
				}
			},
			callbacks: {
				close: function() {
					// limpa campos do modal
					prumadas_delete = [];
				}
			}
		});
	});
	
    $(document).on('click', '.btn-bloco-add', function () {
		$.magnificPopup.open( {
			items: {src: '/admin/md_bloco'},
			type: 'ajax',
			focus: '#id-bloco',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { cid: $("#id-entity").val(), bid: 0 }
				}
			},
			callbacks: {
				ajaxContentAdded: function() {
					$(".form-bloco").validate({
						errorPlacement: function( error, element ) {
							var placement = element.closest('.input-group');
							if (!placement.get(0)) { placement = element; }
							if (placement.next('span').length) { placement = placement.next('span'); }
							if (error.text() !== '') { placement.after(error); }
						}
					});
				}
			}
		});
	});

    
    // Gráfico
    if ($("#chart").length) {

        // trata periodo
        let d_start = moment().subtract(6, 'days');
        let d_end = moment();

        if ($(".btn-geral").data('central').toString().startsWith('53')) {
            d_start = moment().subtract(7, 'days');
            d_end = moment().subtract(1, 'days');
        }

        let bar_mode = 'zoom-in';
        let bar;

        let bar_update = function() {

            $('.tab-form.geral').trigger('loading-overlay:show');

            $.ajax({
                method: 'POST',
                url: '/admin/get_bar_chart_3',
                data: { type: 'agua', start: d_start.format('YYYY-MM-DD'), end: d_end.format('YYYY-MM-DD'), uid: $(".btn-geral").data("unidade"), total: true },
                dataType: 'json',
                success: function(json) {
                    // seta mode
                    bar_mode = 'zoom-in';
                    // atualiza grafico
                    json.options.plugins.tooltip.callbacks.title = function(tooltipItems) {
                        return tooltipItems[0].label + ' - ' + json.data.extra[ tooltipItems[0].dataIndex ];
                    };
                    json.options.plugins.tooltip.callbacks.label = function(context) {
                        return context.dataset.label + ": " + context.parsed.y.toLocaleString("pt-BR", {minimumFractionDigits: 0, maximumFractionDigits: 0}) + " L";
                    };                        
                    // mostra pointer nas barras
                    json.options.onHover = function(e, elements) { 
                        e.native.target.style.cursor = elements[0] ? "pointer" : "default";
                    };

                    json.options.scales.y.ticks.maxTicksLimit = 6;
                    json.options.plugins.legend.display = true;

                    $(".consumo-unidades").html(Math.round(json.data.total / 1000) + "M&sup3;");

                    if (bar) {
                        bar.data = json.data;
                        bar.options = json.options
                        bar.update();
                    } else {
                        bar = new Chart($('#chart'), {
                            type: 'bar',
                            data: json.data,
                            options: json.options
                        });
                    }
                },
                error: function(xhr, status, error) {
                    notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                    return false;
                },
                complete: function() {
                    $('.tab-form.geral').trigger('loading-overlay:hide');
                }
            });
        };

        bar_update();

        let range_list = {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este Mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };

        if ($('.btn-geral').data('central').toString().startsWith('53')) {
            range_list = {
                'Último dia': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 dias': [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
                'Últimos 30 dias': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            };
        }

        $('#daterange').daterangepicker({
            startDate: d_start,
            endDate: d_end,
            minDate: primeira_leitura, 
            maxDate: ($('.btn-geral').data('central').toString().startsWith('53')) ? moment().subtract(1, 'days').format('DD/MM/YYYY') : moment().format('DD/MM/YYYY'),
            maxSpan: { "days": 60 },
            opens: "left",
            ranges: range_list,
            locale: {
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "De",
                "toLabel": "até",
                "customRangeLabel": "Personalizado"
            }        
        },
        function(start, end, label) {
            bar_mode = (label == 'Hoje' || label == 'Ontem') ? 'zoom-out' : 'zoom-in';
    
            // atualiza botão daterange
            if (bar_mode == 'zoom-in')
                $('#daterange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            else
                $('#daterange span').html(start.format('ddd, DD/MM/YYYY'));
            // atualiza datas e modo
            d_start = start;
            d_end = end;
            // atualiza grafico
            bar_update();
        });

        $('#daterange span').html(d_start.format('DD/MM/YYYY') + ' - ' + d_end.format('DD/MM/YYYY'));
    }

    // ***********************************************************************************************
    // * Inicializadores
    // ***********************************************************************************************
	var url = window.location.pathname.split( '/' );
	var dtUnidades = $('#dt-unidades').DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
		pagingType: "numbers",
        ordering: false,
		searching: false,
		lengthChange: false,
		serverSide: true,
		ajax: {
			url: "/admin/get_unidades_bloco",
			data: function ( d ) {
				return $.extend( {}, d, {
					bloco: $("#sel-bloco option:selected").val(), mode: url[4]
				} );
            },
            complete: function (json, type) {
                if (type == "success") {
                    $('.action-medidor').popover({container: 'body', placement: 'bottom', trigger: 'hover', html: true});
                }
            },
			error: function () {
				notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-alertas').dataTable().fnProcessingIndicator(false);
                $('.table-responsive').removeClass('processing');
            }
		},
        columns: [
			{ data: "apto", class: "dt-body-center" },
			{ data: "andar", class: "dt-body-center" },
			{ data: "codigo", class: "dt-body-center" },
            { data: "tipo", class: "dt-body-center" },
			{ data: "nome" },
			{ data: "medidores" },
			{ data: "action", class: "actions dt-body-center" }
		],
		fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); },
        fnDrawCallback: function(oSettings) { $('.table-responsive').removeClass('processing'); }        
    });
    
	$(document).on('click', '.btn-inclui-unidade', function () {
		$.magnificPopup.open( {
			items: {src: '/admin/md_unidade'},
			type: 'ajax',
			focus: '#nome-unidade',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { cid: $("#id-entity").val(), bid: $('.form-entity #sel-bloco').val() }
				}
            },
			callbacks: {
				ajaxContentAdded: function() {
					unidade_validator = $(".form-unidade-add").validate({
						ignore : '.ignore',
                        errorPlacement: function( error, element ) {
							var placement = element.closest('.input-group');
							if (!placement.get(0)) { placement = element; }
							if (placement.next('span').length) { placement = placement.next('span'); }
							if (error.text() !== '') { placement.after(error); }
						}
                    });

                    $('.vtelefone').mask(SPMaskBehavior, {
                        onKeyPress: function(val, e, field, options) {
                            field.mask(SPMaskBehavior.apply({}, arguments), options);
                        }
                    });

                    $(".fracao-unidade").select2({
                        theme: 'bootstrap',
                        language: 'pt-BR',
                        dropdownParent: $('#modalUnidade'),
                        placeholder: "Fração Ideal da Unidade",
                        tags: true,
                        createTag: function (params) {
                            var v = $(".select2-search__field").masked(params.term);
                            return {
                                id: v,
                                text: v,
                                newOption: true
                            }
                        },
                    })
                    .on("select2:open", function () {
                        $(".select2-search__field").mask("0.0000000000", {reverse: false});
                    });
                }
			}
		});
    });

	// **
	// * Handler Botão Excluir Modal Confirmação Exclusão
	// **
	$(document).on('click', '.form-fechamento .modal-confirm', function (e) {
		e.preventDefault();

		if ( $(".form-fechamento").valid() ) {
//@@
			// carrega e mostra indicador
			var $btn = $(this).loadingOverlay();
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-fechamento .btn:enabled').prop('disabled', true);

			// captura dados
			var data = $('.form-fechamento').serializeArray();
//			data.push({'name': 'bid', 'value': $('.form-entity #sel-bloco').val()});

			// referencia para area de notificações
			var $msg = $('.form-fechamento .notification').html('');
			// envia os dados
			$.post('/admin/edit_fechamento', data, function(json) {
				if (json.status == 'success') {
					// fecha a modal
					$.magnificPopup.close();
					// notifica do sucesso
					notifySuccess(json.message);
					// atualiza dt
//					dtUnidades.ajax.reload();
				} else {
					$msg.html(json.message);
					$msg.show();
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				$msg.html('<strong>Ajax Error:</strong> ' + error).show();
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
	});
	
	// **
	// * Handler Botão Excluir Modal Confirmação Exclusão
	// **
	$(document).on('click', '.form-unidade-add .modal-confirm', function (e) {
		e.preventDefault();

		if ( $(".form-unidade-add").valid() ) {
			// carrega e mostra indicador
			var $btn = $(this).loadingOverlay();
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-unidade-add .btn:enabled').prop('disabled', true);

			// captura dados
			var data = $('.form-unidade-add').serializeArray();
			data.push({'name': 'cid', 'value': $('.form-entity #id-entity').val()}, {'name': 'bid', 'value': $('.form-entity #sel-bloco').val()});

			// referencia para area de notificações
			var $msg = $('.form-unidade-add .notification').html('').hide();
			// envia os dados
			$.post('/admin/edit_unidade', data, function(json) {
				if (json.status == 'success') {
					// fecha a modal
					$.magnificPopup.close();
					// notifica do sucesso
					notifySuccess(json.message);
					// atualiza dt
					dtUnidades.ajax.reload();
				} else {
					$msg.html(json.message).show();
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				$msg.html('<strong>Ajax Error:</strong> ' + error).show();
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
	});

	// **
	// * Handler Action Editar Unidade
	// **
	$(document).on('click', '#dt-unidades .action-edit', function (e) {
		e.preventDefault();
		
		$.magnificPopup.open( {
			items: {src: '/admin/md_unidade'},
			type: 'ajax',
			focus: '#id-unidade',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { cid: $("#id-entity").val(), bid: $('.form-entity #sel-bloco').val(), uid: $(this).data('id') }
				}
			},
			callbacks: {
				ajaxContentAdded: function() {
					unidade_validator = $(".form-unidade-edit").validate({
						ignore : '.ignore',
                        errorPlacement: function( error, element ) {
							var placement = element.closest('.input-group');
							if (!placement.get(0)) { placement = element; }
							if (placement.next('span').length) { placement = placement.next('span'); }
							if (error.text() !== '') { placement.after(error); }
						}
                    });
                    $(".fracao-unidade").select2({
                        theme: 'bootstrap',
                        language: 'pt-BR',
                        dropdownParent: $('#modalUnidade'),
                        placeholder: "Fração Ideal da Unidade",
                        tags: true,
                        createTag: function (params) {
                            var v = $(".select2-search__field").masked(params.term);
                            return {
                                id: v,
                                text: v,
                                newOption: true
                            }
                        },
                    })
                    .on("select2:open", function () {
                        $(".select2-search__field").mask("0.0000000000", {reverse: false});
                    });

                    // atualiza select das portas
                    $('.centrais').each( function() {
                        $(this).trigger('change', 'edit');
                    });
				}
			}
		});
	});

   	// **
	// * Handler Action Visualizar Unidade
	// **
	$(document).on('click', '#dt-unidades .action-view', function (e) {
		e.preventDefault();
		
		$.magnificPopup.open( {
			items: { src: '/admin/md_unidade' },
			type: 'ajax',
			focus: '#id-unidade',
			modal: true,
			ajax: {
				settings: {
					type: 'POST',
					data: { cid: $("#id-entity").val(), bid: $('.form-entity #sel-bloco').val(), uid: $(this).data('id'), md: 1 }
				}
			},
			callbacks: {
				ajaxContentAdded: function() {
                    // atualiza select das portas
/*                    $('.centrais').each( function() {
                        $(this).trigger('change', 'view');
                    });
*/				}
			}
		});
	});

	// **
	// * Handler click Badge Medidor
	// **
	$(document).on('click', '#dt-unidades .action-medidor', function (e) {
		e.preventDefault();
		
		var id = $(this).data('id');
		$.magnificPopup.open( {
			items: {src: '/admin/md_medidor'},
			type: 'ajax',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: { id: id }
				}
			}
		});
	});

	// **
	// * Handler Botão Excluir Modal Confirmação Exclusão
	// **
	$(document).on('click', '#dt-unidades .action-delete', function (e) {
		// para propagação
		e.preventDefault();
		//
		var id = $(this).data('id');
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalUnidadeRemove'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
					$('#modalUnidadeRemove input.id').val( id );
				}
			}
		});
	});

	// **
	// * Handler Botão Excluir Modal Confirmação Exclusão
	// **
	$(document).on('click', '#modalBlocoRemove .modal-confirm', function () {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
		var id = $('#modalBlocoRemove input.id').val();
		// faz a requisição
		$.post("/admin/delete_bloco", {id: id}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// remove option
				$('.form-entity #sel-bloco option[value="'+id+'"]').remove();
				$('.form-entity #sel-bloco').trigger('change');
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
			$('#modalBlocoRemove input.id').val('');
		});
	});

	// **
	// * Handler Botão Excluir Modal Confirmação Exclusão
	// **
	$(document).on('click', '#modalUnidadeRemove .modal-confirm', function () {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
		var id = $('#modalUnidadeRemove input.id').val();
		// faz a requisição
		$.post("/admin/delete_unidade", {id: id}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// recarrega tabela
				dtUnidades.ajax.reload();
				// mostra notificação
				notifySuccess('Unidade excluído com sucesso');
			} else if(json.message.code == 1451) {
				// Bloco já tem unidades cadastradas
				$.magnificPopup.close();
				// Avisa do erro
				notifyAlert('Não é possível excluir a unidade pois ela já possui leituras armazenadas.');
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
			$('#modalUnidadeRemove input.id').val('');
		});
	});

	$("#agua-condo").click(function() {
		if($(this).is(":checked")) {
			$(".ramais").show(300);
			$('.ramais-input').removeClass('no-validate');
		} else {
			$(".ramais").hide(200);
			//$('.ramais-input').addClass('no-validate').tagsinput('removeAll');
		}
    });
    
    /*$('.form-entity #centrais-condo').tagsinput({
        maxChars: 8,
        tagClass: 'badge badge-info',
        allowDuplicates: false
    });*/

    $('.form-entity #centrais-condo').on('beforeItemAdd', function(event) {
        if (event.item !== event.item.toUpperCase()) {
            event.cancel = true;
            //$(this).tagsinput('add', event.item.toUpperCase());
        }
    });

    $(document).on('change', '.centrais', function (e, mode) {
        
        if($(this).val() == '') return;

        // limpa erros
        if(unidade_validator != undefined && unidade_validator.numberOfInvalids()) unidade_validator.resetForm();

        // id da central
        var central_id = $(this).val();
        var id = $(this).data('id');
        var porta = $(this).data('porta');
        var $fator = $('input[name="entrada['+id+']['+$(this).data('tipo')+'][fator]"]');

        //oculta portas
        $('.portas-'+id).html('');
        // se entrada não monitorada sai
        if(central_id == 'null') {
            $fator.val('-').attr('readonly', true).addClass('ignore');
            $('.portas-'+id).html('<option value="null" selected>-</option>').attr('disabled', true).show();
            return;
        }
        // trata campo fator

        $fator.removeClass('ignore');
        if(mode != 'view') $fator.attr('readonly', false)
        if($fator.val() == '-') $fator.val('');
        // mostra indicador
        $('.portas-'+id).html('<option disabled value="" selected>Buscando portas...</option>').attr('disabled', true).show();
        // busca portas
        $.post("/admin/get_portas", {id: central_id, porta: porta}, function(html) {
            // completa portas
            $('.portas-'+id).html(html)
            if(mode != 'view') $('.portas-'+id).attr('disabled', false);
        })
		.fail(function(xhr, status, error) {
			// mostra erro
			notifyError(error, 'Ajax Error');
  		})
		.always(function() {
		});
    });

	$(document).on('click', '.form-unidade-add .entradas-unidade', function() {
		if($(this).is(":checked")) {
			$('.entradas select.centrais').removeClass('ignore')
			$('.entradas').show();
		} else {
			$('.entradas select.centrais').addClass('ignore')
			$('.entradas').hide();
		}
    });

    function toTitleCase(str) {
        return str.replace(
            /\w\S*/g,
            function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            }
        );
    }    

    $(document).on('paste', '#proprietario-unidade', function () {
        var element = this;
        setTimeout(function () {
            var text = $(element).val();
            $(element).val(toTitleCase(text));
        }, 100);
    });

    // **
    // * Configuração Datatable Fechamentos
    // **
	let dtFechamentos = $('#dt-fechamentos').DataTable({
		dom: '<"table-responsive"t>r<"row"<"col-6"i><"col-6"p>>',
		processing: true,
        columns: [ { data: "ramal", className: 'dt-body-center' },
                   { data: "competencia", className: 'dt-body-center' }, 
                   { data: "data_inicio", className: 'dt-body-center' }, { data: "data_fim", className: 'dt-body-center' }, 
                   { data: "consumo", className: 'dt-body-center' }, 
                   { data: "v_concessionaria", className: 'dt-body-right' }, { data: "cadastro", className: 'dt-body-center' },
                   { data: "envios", className: 'dt-body-center' },
				   { data: "action", orderable: false, className: "actions"} ],
        serverSide: true,
        sorting: [],
        pagingType: "numbers",
        autoWidth: false,
		ajax: {
			url: $('#dt-fechamentos').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
                    condo: $("#id-entity").val()
				} );
			},
			error: function () {
				notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-fechamentos').dataTable().fnProcessingIndicator(false);
                $('.table-responsive').removeClass('processing');
			}		
		},
        fnDrawCallback: function(settings) { 
            $('.table-responsive').removeClass('processing');
            if(settings.json.recordsTotal - settings.json.recordsFiltered == 0) {
                $('.btn-filter').removeClass('active');
                $('.clear-filter').hide();
            } else {
                $('.btn-filter').addClass('active');
                $('.clear-filter').show();
            }
        },
        fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); },
        initComplete: function() { $('#dt-fechamentos_wrapper .dt-buttons').removeClass('btn-group'); },
    });
    
	// **
	// * Handler abrir modal para novo fechamento
	// **
	$(document).on('click', '.btn-tarifar', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
		$.magnificPopup.open( {
			items: {src: '/admin/md_fechamento'},
			type: 'ajax',
            ajax: {
				settings: {
					type: 'POST',
					data: { condo_id: $("#id-entity").val()}
                }
            },            
			modal:true,
			callbacks: {
				ajaxContentAdded: function() {
                    // configura mask após ajax carregar
                    $('#tar-competencia').mask('00/0000');
                    $('#tar-data-ini').mask('00/00/0000');
					$('#tar-data-fim').mask('00/00/0000');
					$('#tar-leitura-ini').mask('0#');
					$('#tar-leitura-fim').mask('0#');
                    $('#tar-valor').mask("#.##0,00", {reverse: true});
                    $('#tar-basico').mask("#.##0,00", {reverse: true});
                    $('#tar-taxas').mask("#.##0,00", {reverse: true});
                    $('#tar-gestao').mask("#.##0,00", {reverse: true});
                    // configura validação
                    $(".form-fechamento").validate();
				}
			}
		});
    });    

	// **
	// * Handler Action Abrir modal confirmação para excluir fechamento
	// **
	$(document).on('click', '#dt-fechamentos .action-delete', function () {

        var dis_timer, id = $(this).data('id');
        
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExcluiFechamento'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
                    $('#modalExcluiFechamento .id').val( id );
                },
                open: function() {
                    // desabilita botão
                    var btn = $('#modalExcluiFechamento .modal-confirm');
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
	$(document).on('click', '#modalExcluiFechamento .modal-confirm', function (e) {
		// mostra indicador
		var $btn = $(this);
		$btn.trigger('loading-overlay:show');
		// desabilita botões
		var $btn_d = $('.btn:enabled').prop('disabled', true);
		// pega o valor do id
		var id = $('#modalExcluiFechamento .id').val();
		// faz a requisição
		$.post("/admin/delete_faturamento", {id: id}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
				dtFechamentos.ajax.reload( null, false );
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
			$('#modalExcluiFechamento .id').val('');
		});
    });

    	// **
	// * Handler Baixar Planilha Água
	// **
    $(document).on('click', '#dt-fechamentos .action-download-agua', function () {        

        var $btn = $(this);
		$btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
		$.post("/admin/download_agua", { id: $(this).data('id') }, function(json) {

            var $a = $("<a>");
            $a.attr("href", json.file);
            $("body").append($a);
            $a.attr("download", json.name + '.xlsx');
            $a[0].click();
            $a.remove();
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

    // **
    // * Handler botão inclui novo fechamento na modal
    // * TODO: - verificar validação de competencia, se já existe no ramal...no php talvez
    // * - verificar range de datas...pra não sobrepor, no php talvez
    // * - verificar leituras...pra não sobrepor, no php talvez ou trocar pra consumo só...
	// **
	$(document).on('click', '.form-fechamento .modal-confirm', function (e) {
        // para propagação
		e.preventDefault();

        // valida formulário
		if ( $(".form-fechamento").valid() ) {
			// captura dados
			var data = $('.form-fechamento').serializeArray();
			// fecha a modal
			$.magnificPopup.close();
			// carrega e mostra indicador de calculos
			$.magnificPopup.open({items: {src: '#modalNoFooter'}, type: 'inline', showCloseBtn: true });
			// envia os dados
			$.post('/admin/add_fechamento', data, function(json) {
				if (json.status == 'success') {
                    // vai para a pagina do fechamento
                    window.location = "/admin/fechamento/" + json.id;
                } else {
                    notifyError(json.message);
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// mostra erro
				notifyError(error, 'Ajax Error');
			})
			.always(function() {
				// fecha modal
				$.magnificPopup.close();
			});
		}
    });
    
    // **
    // * Handler Row click fechamentos
    // * Abre página do fechamento
    // **
    $('#dt-fechamentos tbody').on('click', 'tr', function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined || event.target.cellIndex == 8) return;
        // pega dados da linha
        var data = dtFechamentos.row( this ).data();
        // redireciona para o fechamento
        window.location = "/admin/fechamento/" + data.DT_RowId;
    });

    // **
    // * Handler Row click condominios
    // * Abre página do condominio
    // **
    $('#dt-entities tbody').on('click', 'tr', function (event) {
		console.log('teste');
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined || event.target.cellIndex == 9) return;
        // pega dados da linha
        var data = $dtEntities.row( this ).data();
        // redireciona para o fechamento
        window.location = "/admin/entities/" + data.id;
    });

    // **
    // * Configuração Datatable Leituras Gas
    // **
	var dtLeituras = $('#dt-leituras').DataTable({
		//dom: '<"row"<"col-lg-6"l>><"table-responsive"t>r<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        dom: '<"table-responsive"t>r<"row"<"col-6"i><"col-6"p>>',
		processing: true,
        columns: [ { data: "competencia", className: 'dt-body-center' }, 
                   { data: "data_inicio", className: 'dt-body-center' }, { data: "data_fim", className: 'dt-body-center' }, 
                   { data: "consumo", className: 'dt-body-center' }, 
                   { data: "leitura", className: 'dt-body-center' } ],
        serverSide: true,
        sorting: [],
        pagingType: "numbers",
        searching: false,
        autoWidth: false,
		ajax: {
			url: $('#dt-leituras').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
                    condo: $("#id-entity").val()
				} );
			},
			error: function () {
				notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-leituras').dataTable().fnProcessingIndicator(false);
                $('.table-responsive').removeClass('processing');
			}		
		},
        fnDrawCallback: function(settings) { 
            $('.table-responsive').removeClass('processing');
            if(settings.json.recordsTotal - settings.json.recordsFiltered == 0) {
                $('.btn-filter').removeClass('active');
                $('.clear-filter').hide();
            } else {
                $('.btn-filter').addClass('active');
                $('.clear-filter').show();
            }
        },
        fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); }
    });

    // **
    // * Handler Row click leitura
    // * Abre página da leitura
    // **
    $('#dt-leituras tbody').on('click', 'tr', function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;
        // pega dados da linha
        var data = dtLeituras.row( this ).data();
        // redireciona para o fechamento
        window.location = "/admin/gas/" + data.DT_RowId;
    });

	$(document).on('click', '.ramal_modal', function() {
	$.magnificPopup.close();
	$.magnificPopup.open( {
		items: {src: '#modalRamal'}, 
		callbacks: {
			close: function() {
				// limpa campos do modal
				$('.form-ramal').trigger("reset");
				$('.form-ramal .notification').html('').hide();
			}
		}
	});
				
	});

	$(document).on('click', '.modal-ramal-confirm', function ()
	{
		// verifica se campos do modal são válidos
		if ( $(".form-ramal").valid() ) {
			// mostra indicador
			var $btn = $(this);
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-ramal .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/add_ramal', $('.form-ramal').serialize(), function(json) {
				if (json.status == 'success') {
					// seleciona o novo item
					var sel = $('#sel-ramal');
					var option = new Option(json.message.nome, json.message.id, true, true);
					sel.append(option).trigger('change');
					sel.trigger({ type: 'select2:select', params: { data: json.message } });					
					// fecha a modal
					$.magnificPopup.close();
					// notifica do sucesso
					notifySuccess('Novo ramal inserido com sucesso!');
					
				} else {
					// notifica erro
					$('.form-ramal .notification').html(json.message.message).show();
				}
			}, 'json')
			.fail(function(xhr, status, error) {
				// falha no ajax: notifica
				$('.form-ramal .notification').html('<strong>Ajax Error:</strong> ' + error).show();
			})
			.always(function() {
				// oculta indicador
				$btn.trigger('loading-overlay:hide');
				// habilita botões
				$btn_d.prop('disabled', false);
			});
		}
	});


}).apply(this, [jQuery]);
