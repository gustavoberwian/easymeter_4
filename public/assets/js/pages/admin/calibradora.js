var ws;
//var processo = 0;
var fase;
var desabilitado = false;
(function() {

	'use strict';

    var ws_connect = function() {

        $('.terminal').append('.');
        // Let us open a web socket
        ws = new WebSocket("ws://192.168.1.15:81");
        console.log("Connectando...");
                    
        ws.onopen = function() {
            console.log("Connected!");
            $('.terminal').append('<br/>'+(new Date()).toLocaleString()+' - <span class="text-success">Conectado!</span><br/>').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
            $('.btn-iniciar').prop('disabled', false);
        };
    
        ws.onmessage = function (evt) { 
            console.log("Message: " + evt.data);

            if(evt.data instanceof Blob) {
                if (evt.data.size == 256) {
                    var reader = new FileReader();
                    reader.addEventListener("loadend", function() {
                       $.post("/admin/put_calibradora", { processo: processo, fase: fase, data: new Uint8Array(reader.result) }, function(json) {
                            $dtCondos.ajax.reload();
                            $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-success">Fase ' + fase + ' terminada.</span></br>').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
                            if($("#auto").is(':checked')) {
                                $('.btn-continuar').trigger('click');
                            } else {
                                $('.btn-continuar').show();
                            }
                        }, 'json')
                        .fail(function(xhr, status, error) {
                            // mostra erro
                            notifyError(error, 'Ajax Error');
                        })
                        .always(function() {
                        });
                    });
                    reader.readAsArrayBuffer(evt.data);
                }
            } else {
                if (evt.data.startsWith("[MSG]")) {
                    $('.terminal').append((new Date()).toLocaleString()+' - '+evt.data.substr(5) + '<br/>');
                    $(".terminal").scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
                } else if (evt.data.startsWith("[SUC]")) {
                    $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-success">' + evt.data.substr(5) + '</span><br/>');
                    $(".terminal").scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
                } else if (evt.data.startsWith("[ERR]")) {
                    $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-danger">' + evt.data.substr(5) + '</span><br/>');
                    $(".terminal").scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
                }
            }
        };
    
        ws.onclose = function() { 
        
            // websocket is closed.
            console.log("Connection is closed."); 
            ws_connect();

            $('.btn-conectar').addClass('btn-success').removeClass('connected btn-danger').html('Conectar');
            $('.btn-iniciar').show().prop('disabled', true);
            $('.btn-continuar').hide();
            $('.btn-salvar').hide();
            $('.btn-play').hide();
        };

        ws.onerror = function(err) {
            console.error('Socket error: ', err.message, 'Closing socket');
            ws.close();
        };
    }

    $('.terminal').append('Conectando..').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());

    ws_connect();

    if (processo > 0) {
        $('.terminal').append('<br/>Visualizando processo ' + processo + ' (' + data_processo + ')').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
    }


    // **
    // * Handler Botão Iniciar
    // **
    $('.btn-iniciar').on('click', function() {
        $.post("/admin/start_calibradora", function(json) {
            processo = json.processo;
            $dtCondos.ajax.reload();
            fase = '1a';
            $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-success">Processo ' + processo + ' iniciado.</span><br/>Fase 1A</br>').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
            ws.send("#sA");
            $('.btn-iniciar').addClass('collapse');
            if(!$("#auto").is(':checked')) {
                $('.btn-continuar').show();
            }
            $('.btn-play').show();
        }, 'json')
        .fail(function(xhr, status, error) {
            // mostra erro
            notifyError(error, 'Ajax Error');
        })
        .always(function() {
        });
    });    

    // **
    // * Handler Botão Iniciar
    // **
    $('.btn-salvar').on('click', function() {
        $.post("/ajax/add_sensores", {processo : processo}, function(json) {
            if (json.status == 'success') {
                $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-success">Sensores incluidos</br>').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
            }
        }, 'json')
        .fail(function(xhr, status, error) {
            // mostra erro
            notifyError(error, 'Ajax Error');
        })
        .always(function() {
        });
    });    

    $(document).on('change', "#dt-sensores input:checkbox", function() {
        if (this.checked) {
            $(this).closest('tr').css('background-color', '#fff');
        } else {
            $(this).closest('tr').css('background-color', '#f8d7da');
        }
    });

    // **
    // * Handler Botão Continuar
    // **
    $('.btn-continuar').on('click', function() {
        $.post("/admin/next_calibradora", { processo: processo }, function(json) {
            if (json.status == 'finished') {
                desabilitado = true;
                $dtCondos.ajax.reload();
                $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-success">Processo ' + processo + ' Finalizado.</span></br>').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
                // abre a modal
                $.magnificPopup.open( {
                    items: {src: '#modalCalcular'}, type: 'inline',
                    callbacks: {
                        close: function() {
                            $('.btn-continuar').hide();
                            $('.btn-salvar').show();     
                        }
                    }        
                });

            } else if (json.status == 'success') {
                fase = json.fase;
                $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-success">Fase '+fase+' iniciada.</br>').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
                ws.send("#s" + fase.slice(-1).toUpperCase());
            } else if (json.status == 'error') {
            }
        }, 'json')
        .fail(function(xhr, status, error) {
            // mostra erro
            notifyError(error, 'Ajax Error');
        })
        .always(function() {
        });
    });    

    // **
    // * Handler Botão Confirm na Modal
    // * Salva sensores marcados
    // **
    $(document).on('click', '.modal-confirm', function (e) {
        var ativos = $("#dt-sensores input:checkbox:checked").map(function(){
            return $(this).val();
        }).get();
        $.post("/admin/calcula_calibradora", { processo: processo,  sensores: ativos }, function(json) {
            if (json.status == 'success') {
                desabilitado = false;
                $('.terminal').append((new Date()).toLocaleString()+' - <span class="text-success">Processo ' + processo + ' Finalizado.</span></br>').scrollTop($(".terminal")[0].scrollHeight - $(".terminal").height());
                processo = 0;
                $dtCondos.ajax.reload();
                $('.btn-iniciar').show().prop('disabled', true);
                $('.btn-continuar').hide();
                $('.btn-salvar').hide();
                $('.btn-play').hide();
            } else if (json.status == 'error') {
                alert(json.message);
            }
            $.magnificPopup.close();
        }, 'json')
        .fail(function(xhr, status, error) {
            // mostra erro
            notifyError(error, 'Ajax Error');
        })
        .always(function() {
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
	// * Handler Fechar Modal Confirmação Exclusão
	// **
	$(document).on('click', '.btn-play', function (e) {
		// para propagação
		e.preventDefault();
        if ($(this).hasClass('paused')) {
            $(this).removeClass('paused').html('Pausar');
            ws.send('#1');
        } else {
            $(this).addClass('paused').html('Reiniciar');
            ws.send('#0');
        }
    });

    var $dtCondos = $('#dt-sensores').DataTable({
        dom: '<"table-responsive"tr>',
        processing: true,
        ordering: false,
		searching: false,
		lengthChange: false,
        columns: [ { data: "porta", className: "back-gray"}, { data: "serial" }, { data: "l1a" }, { data: "l1b" }, { data: "l1c" }, 
            { data: "l2a" }, { data: "l2b" }, { data: "l2c" }, { data: "l3a" }, { data: "l3b" }, { data: "l3c" },
            { data: "l4a" }, { data: "l4b" }, { data: "l4c" }, { data: "l5a" }, { data: "l5b" }, { data: "l5c" },
            { data: "fator", orderable: false } ],
        serverSide: true,
        ajax: {
            url: '/admin/get_sensores',
			data: function ( d ) {
				return $.extend( {}, d, {
					processo: processo
				} );
			},
            complete: function (json, type) {
                if (type == "success") {
                    $("#dt-sensores input:checkbox:unchecked").closest('tr').css('background-color', '#f8d7da');
                    $("#dt-sensores input:checkbox").attr('disabled', desabilitado);
                }
            }
        },
    });


}).apply(this, [jQuery]);