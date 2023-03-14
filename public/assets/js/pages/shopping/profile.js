(function() {

	'use strict';

	var $image_crop = $('.img-preview').croppie({
        enableExif: true,
        viewport: {
            width:300,
            height:300,
            type:'square'
        },
        boundary:{
            width:400,
            height:400
        }
    });
    
    $('#upload_image').on('change', function() {
        var reader = new FileReader();
        
        reader.onload = function (event) {
            $('.croppie-container .cr-boundary').css('background', 'none');
            $image_crop.croppie('bind', {
                url: event.target.result
            });
        };

        if (this.files.length > 0)
            reader.readAsDataURL(this.files[0]);
            
    });
    
    $('.btn-update').on('click', function(e) {
        e.preventDefault();
        // crop image
        $image_crop.croppie('result', { type: 'canvas', size: 'viewport' } )
        .then( function(img) {
            // atualiza field
            $("#crop-image").val(img);
            // envia form
            $('.avatar').submit();
        });
    });
/*
    $('.btn-facebook').on('click', function(e) {
        e.preventDefault();
        // atualiza field
        $('input[name="facebook"]').val('profile');
        // envia form
        $('.avatar').submit();
    });
*/
    $(document).on('click', '.fileupload-exists[data-dismiss="fileupload"]', function (e) {
        $("#crop-image").val('');
        $('.cr-image, .cr-overlay').removeAttr('style src');
        $('.croppie-container .cr-boundary').css('background', 'url(/assets/img/default_avatar.jpg)');
    });

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
        maxTags: 3
    });

}).apply(this, [jQuery]);
