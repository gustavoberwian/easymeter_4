// **
// * Datatables
// **
(function($) {

	'use strict';

    // ***********************************************************************************************
    // * Inicializadores
    // ***********************************************************************************************

    // **
    // * Inicializa datatable
    // **

    //
    //Relatório Bavieira
    //

	var $dtAguaGas = $('#dt-agua-gas').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
            { data: "gas", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-gas').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });
    
	var $dtGasAgua = $('#dt-gas-agua').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
            { data: "gas", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-gas-agua').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtAgua100 = $('#dt-agua-inf').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-inf').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtAguaZero = $('#dt-agua-z').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-z').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtAguaVazamento = $('#dt-agua-v').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "horas", class: "dt-body-center" }, 
            { data: "consumo", class: "dt-body-center" }, 
            { data: "entrada", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-v').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtGasVazamento = $('#dt-gas-v').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "consumo", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-gas-v').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

    $('select').on('change', function() {
        $dtAguaGas.ajax.reload();
        $dtGasAgua.ajax.reload();
        $dtAgua100.ajax.reload();
        $dtAguaZero.ajax.reload();
        $dtAguaVazamento.ajax.reload();
        $dtGasVazamento.ajax.reload();
    });

    // **
	// * Handler Baixar Planilha Água
	// **
    $(".btn-download").on('click', function () {        

        var $btn = $(this);
		$btn.trigger('loading-overlay:show').prop('disabled', true);

        // faz a requisição
		$.post("/admin/download_relatorio", { c: $("select").val() }, function(json) {

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
			$btn.trigger('loading-overlay:hide').prop('disabled', false);
        });
    });

    // **
    // * Handler Row click fechamentos
    // **
    $('table.table-clickable').on('click', 'tbody tr', function (event) {
        // se o clique não foi em uma celula, retorna
        if (event.target.cellIndex == undefined) return;
        // pega dados da linha
        var data = $('#' + event.delegateTarget.id).DataTable().row(this).data();
        // redireciona para o fechamento
        window.open("/admin/unidades/" + data.DT_RowId + "/" + $("select [value='"+$("select").val()+"']").data('comp'), "_blank");
    });

    //
    //Relatório viver
    //

    var $dtAguaGasviver = $('#dt-agua-gas-viver').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
            { data: "gas", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-gas-viver').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });
    
	var $dtGasAguaviver = $('#dt-gas-agua-viver').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
            { data: "gas", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-gas-agua-viver').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtAgua100viver = $('#dt-agua-inf-viver').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-inf-viver').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtAguaZeroviver = $('#dt-agua-z-viver').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "agua", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-z-viver').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtAguaVazamentoviver = $('#dt-agua-v-viver').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "horas", class: "dt-body-center" }, 
            { data: "consumo", class: "dt-body-center" }, 
            { data: "entrada", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-agua-v-viver').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

	var $dtGasVazamentoviver = $('#dt-gas-v-viver').DataTable({
		dom: '<"table-responsive"t>pr',
		processing: true,
        columns: [ 
            { data: "bloco", class: "dt-body-center" }, 
            { data: "unidade", class: "dt-body-center" }, 
            { data: "consumo", class: "dt-body-center" }, 
        ],
		order:[],
		serverSide: true,
		ajax: { 
            url: $('#dt-gas-v-viver').data('url'),
			data: function ( d ) {
				return $.extend( {}, d, {
					c: $("select").val()
				} );
			}
        }
    });

    $('select').on('change', function() {
        $dtAguaGasviver.ajax.reload();
        $dtGasAguaviver.ajax.reload();
        $dtAgua100viver.ajax.reload();
        $dtAguaZeroviver.ajax.reload();
        $dtAguaVazamentoviver.ajax.reload();
        $dtGasVazamentoviver.ajax.reload();
    });

    // **
	// * Handler Baixar Planilha Água
	// **
    $(".btn-download").on('click', function () {        

        var $btn = $(this);
		$btn.trigger('loading-overlay:show').prop('disabled', true);

        // faz a requisição
		$.post("/admin/download_relatorio_viver", { c: $("select").val() }, function(json) {

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
			$btn.trigger('loading-overlay:hide').prop('disabled', false);
        });
    });

}).apply(this, [jQuery]);