(function($) {

	'use strict';

    var dtMensal = $('#dt-mensal').DataTable({
        dom: '<"table-responsive"t>p',
		processing: true,
        columns: [  {data: "competencia", class: "dt-body-center", orderable: false}, 
                    {data: "consumo", class: "dt-body-center"}, 
                    {data: "gerado", class: "dt-body-center"}, 
                    {data: "gerado1", class: "dt-body-center"}, 
                    {data: "gerado2", class: "dt-body-center"}, 
                    {data: "actions", class: "dt-body-center", orderable: false}
        ],
        serverSide: true,
        sorting: [],
		pagingType: "numbers",
		searching: false,
		ajax: {
			url: $('#dt-mensal').data('url'),
			error: function () {

                new PNotify({
                    title: 'Erro',
                    text: "Ocorreu um erro no servidor.<br/>Por favor tente novamente em alguns instantes.",
                    type: 'error',
                    buttons: {sticker: false}
                });
			}		
		},
        initComplete: (settings, json)=>{
            $('#dt-mensal_paginate').appendTo('.card-mensal .card-footer');
        }        
    });

    var dtMensal = $('#dt-diario').DataTable({
        dom: '<"table-responsive"t>p',
		processing: true,
        columns: [  {data: "competencia", class: "center", orderable: false}, 
                    {data: "consumo", class: "center"}, 
                    {data: "gerado", class: "center"}, 
                    {data: "actions", class: "center", orderable: false}
        ],
        serverSide: true,
        sorting: [],
		pagingType: "numbers",
		searching: false,
		ajax: {
			url: $('#dt-diario').data('url'),
			error: function () {

                new PNotify({
                    title: 'Erro',
                    text: "Ocorreu um erro no servidor.<br/>Por favor tente novamente em alguns instantes.",
                    type: 'error',
                    buttons: {sticker: false}
                });	            
			}		
		},
        initComplete: (settings, json)=>{
            $('#dt-diario_paginate').appendTo('.card-diario .card-footer');
        }        
    });

}).apply(this, [jQuery]);