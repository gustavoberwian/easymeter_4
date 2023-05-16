(function() {

	'use strict';

    


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
	$('.telefone').mask(SPMaskBehavior, {
		onKeyPress: function(val, e, field, options) {
			field.mask(SPMaskBehavior.apply({}, arguments), options);
		}
	});

    // **
    // * Inicializa tagsinput
    // **

	$('.profile #emails').tagsinput({
		tagClass: 'badge badge-primary',
		allowDuplicates: false,
		maxTags: 3,
	});

	 
	// 
    // abre a modal

     $(document).on('click', '.btn-edit-image', function (e) {
        
        e.preventDefault();
        
       
        $.magnificPopup.open( {
			items: {src: '/shopping/md_profile_image_edit'},
			type: 'ajax',
			modal:true,
			ajax: {
				settings: {
					type: 'POST',
					data: {
                        id: $(this).data('id'), 
                        img: $(this).data('img')
                    }
				}
			},
		});
     });
            

    // **
	// * Handler Fechar Modal 
	// **

    $(document).on('click', '.modal-dismiss', function (e) {
		// para propagação
        e.preventDefault();
      

		// fecha a modal
		$.magnificPopup.close();
    });

  $(document).on('click', '.modal-confirm', function(e) {
	e.preventDefault();
	
	$.ajax({
		method: 'POST',
		url: '/shopping/profile',
		dataType: 'json',
		data: $('#profile').serialize(),
		success: function (response) {
			if(response.status == "success") {
				notifySuccess(response.message);
				
			} else {
				notifyError(response.message);
				
			}
		},
		error: function (xhr, status, error) {
		},
		complete: function () {
		}
	});
  })

 

}).apply(this, [jQuery]);
