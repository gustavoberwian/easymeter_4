(function($) {

	'use strict';

    var dtMensal = $('#dt-alerts').DataTable({
        dom: '<"table-responsive"t>p',
		processing: true,
        columns: [  {data: "tipo", class: "dt-body-center", orderable: false}, 
                    {data: "titulo"}, 
                    {data: "mensagem"}, 
                    {data: "enviada", class: "dt-body-center"}, 
                    {data: "actions", class: "dt-body-center", orderable: false}
        ],
        serverSide: true,
        sorting: [],
		pagingType: "numbers",
		searching: false,
		ajax: {
			url: $('#dt-alerts').data('url'),
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
            $('#dt-alerts_paginate').appendTo('.card-mensal .card-footer');
        }        
    });

}).apply(this, [jQuery]);