(function() {

	'use strict';

    if (navigator.userAgent.indexOf('Easymeter') > 0 && identity && password) {
        localStorage.setItem('identity', identity);
        localStorage.setItem('password', password);
//        webkit.messageHandlers.cordova_iab.postMessage(JSON.stringify({cmd: 'login', uid: $('.userbox').data('id'), condo: $('.userbox').data('condo')}));
    }

    setInterval(function() {
        var d = new Date();

        if(d.getMinutes() == 1 && d.getSeconds() == 0) {
            $.getJSON( "/ajax/get_leitura", function( json ) {
                if (json.status == 'success' && json.data != false) {
                    $('.leitura-agua').prop('counter', parseInt( $('.leitura-agua').text() ) ).animate({
                        counter: json.data.leitura_agua
                    }, {
                        duration: 3000,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text( String( Math.ceil(now) ).padStart(7, '0') );
                        }
                    });
                }
            });
        }
    }, 1000);

	// **
	// * Handler abrir modal para novo chamado
	// **
	$(document).on('click', '.btn-chamado', function (e) {
        // para propagação
        e.preventDefault();
        
        // abre modal
		$.magnificPopup.open( {
			items: {src: '/condominio/md_chamado'},
			type: 'ajax',
			modal:true,
			callbacks: {
				ajaxContentAdded: function() {

                    $('[data-loading-overlay]').loadingOverlay();
                    $(".form-chamado").validate();

                    $('select[name="assunto"]').on('change', function (e) {
                        if ($(this).val() == 'v') {
                            $('textarea[name="message"]').attr("placeholder", 
                            "Explique aqui o motivo da solicitação da visita.\nFaremos uma revisão do consumo da unidade\ne entraremos em contato para agendar a visita");
                            $('.alert-warning.notification').show();
                        } else {
                            if ($(this).val() == 's')
                                $('textarea[name="message"]').attr("placeholder", "Escreva aqui sua sugestão");
                            else if ($(this).val() == 'd')
                                $('textarea[name="message"]').attr("placeholder", "Descreva aqui sua dúvida");
                            else if ($(this).val() == 'r')
                                $('textarea[name="message"]').attr("placeholder", "Explique aqui o motivo da solicitação de revisão");

                                $('.alert-warning.notification').hide();
                        }
                    });

                    $('.form-chamado .modal-confirm').on('click', function (e) {
                        e.preventDefault();
                        $('#modalChamado .alert-danger.notification').html('').hide();
                        if ($(".form-chamado").valid()) {
                            var $btn = $(this);
                            $btn.trigger('loading-overlay:show');
                            // desabilita botões
                            var $btn_d = $('#modalChamado .btn:enabled').prop('disabled', true);
                                        
                            // Busca alternativas 
                            $.post('/ajax/new_chamado', {a: $('select[name="assunto"]').val(), m: $('textarea[name="message"]').val()}, function(json) {
                                $btn_d.prop('disabled', false);
                                if (json.status == 'success') {
                                    $('#modalChamado .alert-success.notification').html(json.message).show();
                                    $btn.prop('disabled', true);
                                } else {
                                    $('#modalChamado .alert-danger.notification').html(json.message).show();
                                }
                            }, 'json')
                            .fail(function(xhr, status, error) {
                                // mostra erro
                                $('#modalChamado .alert-danger.notification').html('<b>Erro no servidor</b>: '+error).show();
                                $btn_d.prop('disabled', false);
                            })
                            .always(function() {
                                $btn.trigger('loading-overlay:hide');
                            });
                        
                        }
                    });
				}
			}
		});
    });

	// **
	// * Handler Fechar Modal modal-dismiss
	// **
	$(document).on('click', '.modal-dismiss', function (e) {
		// para propagação
		e.preventDefault();
		// fecha a modal
		$.magnificPopup.close();
    });
    






    /********************************************************************
     * 
     * Página da administradora
     * 
     *******************************************************************/


    // **
    // * Handler Row click condominios
    // * Abre página do condominio
    // **
    $('#dt-condos tbody').on('click', 'tr', function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;
        // redireciona para o fechamento
        window.location = "/painel/administra/" + $(this).data('id');
    });


 	/*
	Liquid Meter
	*/
	if( $('#meter').get(0) ) {
		$('#meter').liquidMeter({
			shape: 'circle',
			color: '#0088CC',
			background: '#F9F9F9',
			fontSize: '24px',
			fontWeight: '600',
			stroke: '#F2F2F2',
			textColor: '#abb4be',
			liquidOpacity: 0.9,
			liquidPalette: ['#333'],
			speed: 3000,
			animate: !$.browser.mobile
		});
	}

	if( $('#meter1').get(0) ) {
		$('#meter1').liquidMeter({
			shape: 'circle',
			color: '#0088CC',
			background: '#F9F9F9',
			fontSize: '24px',
			fontWeight: '600',
			stroke: '#F2F2F2',
			textColor: '#abb4be',
			liquidOpacity: 0.9,
			liquidPalette: ['#333'],
			speed: 3000,
			animate: !$.browser.mobile
		});
	}

    if( $('#meter2').get(0) ) {
		$('#meter2').liquidMeter({
			shape: 'circle',
			color: '#0088CC',
			background: '#F9F9F9',
			fontSize: '24px',
			fontWeight: '600',
			stroke: '#F2F2F2',
			textColor: '#abb4be',
			liquidOpacity: 0.9,
			liquidPalette: ['#333'],
			speed: 3000,
			animate: !$.browser.mobile
		});
	}    

}).apply(this, [jQuery]);