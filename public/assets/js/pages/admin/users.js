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
			{ data: "monitora", orderable: false, className: "dt-body-center monitor"},
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
		fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); },
		fnDrawCallback: function () {
			$(".switch-input").themePluginIOS7Switch(),
			$('.table-responsive').removeClass('processing');
		}
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

	$(document).on('click', '#dt-users .action-delete-user', function () {
        var uid = $(this).data('id');
		// abre a modal
		$.magnificPopup.open( {
			items: {src: '#modalExclui'}, type: 'inline',
			callbacks: {
				beforeOpen: function() {
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
		$.post("/admin/delete_user", { uid: uid}, function(json) {
			if (json.status == 'success') {
				// fecha modal
				$.magnificPopup.close();
				// atualiza tabela
                $dtUsers.ajax.reload( null, false );
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
			$('#modalExclui .id').val('').data('uid', null);
		});
    });

	$(document).on('click', '.btn-incluir', function(e){
		e.preventDefault()
		console.log('test');
		window.location = "/admin/users/incluir";
	})

	// **
    // * Handler Switch
    // **

	$(document).on('click', '.ios-switch', function () {
        let _self = $(this)
		var id = _self.parent().children().last().data("id");

        $.ajax({
            method: 'POST',
            url: '/admin/edit_active_stats',
            data: {id: id},
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
		highlight: function( label ) { $(label).closest('.form-user').removeClass('has-success').addClass('has-error'); },
        success: function( label ) { $(label).closest('.form-user').removeClass('has-error'); label.remove(); },
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

	$.post("/admin/get_entity_for_select", function(result) {
		$('#entity-user').html(result);
	})
	$.post("/admin/get_groups_for_select", function(result) {
		$('#group-user').html(result);
	})
	

	$(document).change('#classificacao-user', function(){
		$('.relation-user-entity').removeAttr('hidden');
		if ($('#classificacao-user').val() === 'entidade')
		{
			$('.relation-user-unity').attr('hidden', true);
			$('.relation-user-unity').attr('disable', true);

			$('.relation-user-group').attr('hidden', true);
			$('.relation-user-group').attr('disable', true);

			$('.relation-user-entity').removeAttr('hidden');
			$('.relation-user-entity').removeAttr('disable');
			

		} else if ($('#classificacao-user').val() === 'agrupamento')
		{			
			$('.relation-user-entity').attr('hidden', true);
			$('.relation-user-entity').attr('disable', true);

			$('.relation-user-unity').attr('hidden', true);
			$('.relation-user-unity').attr('disabled', true);

			$('.relation-user-group').removeAttr('hidden');
			$('.relation-user-group').removeAttr('disabled');

			
		} else 
		{
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
	$.validator.addClassRules("vnome", { twostring : true });
	$.validator.addClassRules("vemail", {email: true, required: true})
	$.validator.addClassRules("vsenha", { minlength: 5, required: true});
	$.validator.addClassRules("vconfirma", { minlength: 5,
		equalTo: "#senha-user"});
		$.validator.addClassRules("vpage", { required: true});

	
		
    // **
    // * Handler para limpar o form principal
    // **
	$(document).on("click", ".form-entity .btn-reset-user", function()
	{
		// limpa campos
		$('.form-user').trigger("reset");
		valid.resetForm();
    });

	// **
    // * Handler para salvar novo condominio
    // **
	$(document).on("click", ".form-user .btn-salvar-user", function()
	{
		// verifica se campos do modal são válidos
		if ( $(".form-user").valid() ) {
			// mostra indicador
			
			var $btn = $(this);
			$btn.trigger('loading-overlay:show');
			// desabilita botões
			var $btn_d = $('.form-user .btn:enabled').prop('disabled', true);
			// envia os dados
			$.post('/admin/add_user', $('.form-user').serialize(), function(json) {
				if (json.status == 'success') {
					//TODO Pergunta se quer cadastrar os blocos
					notifySuccess(json.message)
					// redirect
					setTimeout(() => {'timeout'},3000);
					window.location = ( 'admin/users')

					
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
	
 	
    

}).apply(this, [jQuery]);