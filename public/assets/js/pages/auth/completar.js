(function() {

    'use strict';
    
    var $w3finish = $('#w3').find('ul.pager li.finish');
	var $w3validator = $("#w3 form").validate({
        highlight: function(element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        success: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).remove();
        },
        errorPlacement: function( error, element ) {
            element.parent().append( error );
        }
    });

	$('#w3 ul.pager li.finish a').on('click', function( e ) {
        e.preventDefault();
        if ($(this).hasClass('disabled')) return false;
		var validated = $('#w3 form').valid();
		if ( validated ) {
            var $btn = $(this);
            $('.btn-link').addClass('disabled');
            $btn.html('<i class="fas fa-spinner fa-spin"></i>');
            $('.alert.alert-danger').hide().html('');
            //verifica codigo, antes de enviar
            $.ajax({
                url : '/ajax/code_verify',
                type : 'POST',
                dataType: 'json',
                data : { codigo: $('#codigo').val(), bloco: $('#bloco').val(), apto: $('#apto').val() },
                success: function(json){
                    if(json.status == 'success') {
                        $('input[name="cid"]').val(json.cid);
                        $('input[name="uid"]').val(json.uid);

                        $('form').submit();
                    } else if(json.status == 'invalid') {
                        $('#codigo').parent().addClass('has-error').append(json.message);
                    } else if(json.status == 'wrong') {
                        $('#codigo').parent().addClass('has-error').append(json.message);
                    } else if(json.status == 'already') {
                        $('.alert.alert-danger').append(json.message).show();
                    }
                    $('.btn-link').removeClass('disabled');
                    $btn.html('Finalizar');
                }
            })
            .fail(function() {
                $('.btn-link').removeClass('disabled');
                $btn.html('Finalizar');
            });
        }
	});

	$('#w3').bootstrapWizard({
		tabClass: 'wizard-steps',
		nextSelector: 'ul.pager li.next',
		previousSelector: 'ul.pager li.previous',
		firstSelector: null,
		lastSelector: null,
		onNext: function( tab, navigation, index, newindex ) {
            var validated = $('#w3 form').valid();
			if( !validated ) {
				$w3validator.focusInvalid();
				return false;
            }
            var $btn = $('ul.pager li.next a');
            if (tab.children()[0].hash == '#w3-acesso') {
                $('.btn-link').addClass('disabled');
                $btn.html('<i class="fas fa-spinner fa-spin"></i>');
                //verifica se email já não cadastrado
                $.ajax({
                    url : '/ajax/identity_verify',
                    type : 'POST',
                    dataType: 'json',
                    data : { email: $('#identity').val() },
                    success: function(json){
                        if(json.status) {
                            $('#w3').bootstrapWizard('show', 1);
                        } else {
                            $('#identity').parent().addClass('has-error').append('<label id="identity-error" class="error" for="identity">Email já cadastrado.</label>');
                        }
                    }
                })
                .always(function() {
                    $('.btn-link').removeClass('disabled');
                    $btn.html('Próxima <i class="fas fa-angle-right"></i>');
                });

                return false;
            }
/*
            if (tab.children()[0].hash == '#w3-unidade11') {
                $('.btn-link').attr('disabled', true);
                $btn.html('<i class="fas fa-spinner fa-spin"></i>');
                $('.alert.alert-danger').hide().html('');
                //verifica se email já não cadastrado
                $.ajax({
                    url : '/ajax/code_verify',
                    type : 'POST',
                    dataType: 'json',
                    data : { codigo: $('#codigo').val(), bloco: $('#bloco').val(), apto: $('#apto').val() },
                    success: function(json){
                        if(json.status == 'success') {
                            $('input[name="cid"]').val(json.cid);
                            $('input[name="uid"]').val(json.uid);
                            $('#w3').bootstrapWizard('show', index );
                        } else if(json.status == 'invalid') {
                            $('#codigo').parent().addClass('has-error').append(json.message);
                        } else if(json.status == 'wrong') {
                            $('#codigo').parent().addClass('has-error').append(json.message);
                        } else if(json.status == 'already') {
                            $('.alert.alert-danger').append(json.message).show();
                        }
                    }
                })
                .always(function() {
                    $('.btn-link').attr('disabled', false);
                    $btn.html('Próxima <i class="fas fa-angle-right"></i>');
                });

                return false;
            }
*/

		},
		onTabClick: function( tab, navigation, index, newindex ) {
            return false;
		},
		onTabChange: function( tab, navigation, index, newindex ) {
			var $total = navigation.find('li').length - 1;
			$w3finish[ newindex != $total ? 'addClass' : 'removeClass' ]( 'hidden' );
            $('#w3').find(this.nextSelector)[ newindex == $total ? 'addClass' : 'removeClass' ]( 'hidden' );
            if (newindex == 0) $('ul.pager li.back').show(); else $('ul.pager li.back').hide();
		},
		onTabShow: function( tab, navigation, index ) {
			navigation.find('li').removeClass('active');
			navigation.find('li').eq( index ).addClass('active');

			tab.prevAll().addClass('completed');
			tab.nextAll().removeClass('completed');
		}
    });
    
    $('.login').on('click', function() {
        if ($(this).hasClass('disabled')) return false;
    });

    // **
    // * Adiciona validadores especificos
    // **
    $.validator.addClassRules("vnome", { twostring : true });
    $.validator.addClassRules("vtelefone", { telefone : true });

    // **
    // * Inicializa Mascaras
    // **
	var SPMaskBehavior = function (val) { return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009'; };

    $('.vtelefone').mask(SPMaskBehavior, {
        onKeyPress: function(val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    });

}).apply(this, [jQuery]);