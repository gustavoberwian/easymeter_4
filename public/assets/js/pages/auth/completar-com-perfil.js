(function() {

    'use strict';
    
    var $w3finish = $('#w3').find('ul.pager li.finish'),
		$w3validator = $("#w3 form").validate({
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

	$w3finish.on('click', function( ev ) {
		ev.preventDefault();
		var validated = $('#w3 form').valid();
		if ( validated ) {
            $(this).find('a').html('<i class="fas fa-spinner fa-spin"></i>');
            $('form').submit();
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
                    $btn.html('Próxima <i class="fas fa-angle-right"></i>');
                });

                return false;
            }

            if (tab.children()[0].hash == '#w3-unidade') {
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
                    $btn.html('Próxima <i class="fas fa-angle-right"></i>');
                });

                return false;
            }


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

    $.validator.addMethod("twostring", function(value, element) {
        if (!element.required && value == '') return true;
    
           return (/\w+\s+\w+/.test(value));
    }, "Informe o nome completo");
    
    $.validator.addClassRules("vnome", { twostring : true });
    
    $('[data-plugin-spinner]').themePluginSpinner($('[data-plugin-spinner]').data('plugin-options'));

}).apply(this, [jQuery]);