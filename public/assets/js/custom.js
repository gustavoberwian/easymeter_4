/* Add here all your JS customizations */
if ($.fn.dataTableExt) {
    $.fn.dataTableExt.oApi.fnProcessingIndicator = function ( oSettings, onoff ) {
        if ( typeof( onoff ) == 'undefined' ) {
            onoff = true;
        }
        this.oApi._fnProcessingDisplay( oSettings, onoff );
    };
}
if ($.validator) {
    $.validator.addMethod( "dateBR", function( value, element ) {
        return this.optional( element ) || /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/.test( value );
    }, $.validator.messages.date );

    $.validator.addMethod( "competencia", function( value, element ) {
        //range between 01/2000 to 12/9999
        return this.optional( element ) || /^(0?[1-9]|1[012])\/([2-9][0-9]{3})$/.test(value);
    }, "Mês de competência inválido." );

    $.validator.methods.number = function (value, element) {
        return this.optional(element) || /^-?(?:\d+|\d{1,3}(?:[\s\.,]\d{3})+)(?:[\.,]\d+)?$/.test(value);
    }
}
var notifyError = function(msg, title='Ocorreu um erro', visibility=true) {
    new PNotify({
        title: title,
        text: msg,
        type: 'error',
        addclass: 'stack-bar-top',
        stack: {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0},
        width: "100%",
        hide: visibility,
        buttons: {sticker: false}
    });
};

var notifySuccess = function(msg) {
    new PNotify({
        title: 'Successo',
        text: msg,
        type: 'success',
        buttons: {sticker: false}
    });
};

var notifyWarning = function(msg) {
    new PNotify({
        title: 'Atenção',
        text: msg,
        type: 'error',
        buttons: {sticker: false}
    });
};

var notifyAlert = function(msg) {
    new PNotify({
        title: 'Atenção',
        text: msg,
        addclass: 'stack-bar-top',
        stack: {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0},
        width: "100%",
        buttons: {sticker: false}
    });
};
if ($.validator !== undefined) {
    $.validator.addMethod("twostring", function(value, element) {
        if (!element.required && value == '') return true;

        return (/\w+\s+\w+/.test(value));
    }, "Informe o nome completo");

    $.validator.addMethod("cnpj", function(value, element) {
        var cnpj = value.replace(/[^\d]+/g,'');

        if (!element.required && cnpj == '') return true;

        if(cnpj.length != 14) return false;

        if (cnpj == "00000000000000" || cnpj == "11111111111111" || cnpj == "22222222222222" || cnpj == "33333333333333" ||
            cnpj == "44444444444444" || cnpj == "55555555555555" || cnpj == "66666666666666" || cnpj == "77777777777777" ||
            cnpj == "88888888888888" || cnpj == "99999999999999")
            return false;

        // Valida DVs
        var tamanho = cnpj.length - 2;
        var numeros = cnpj.substring(0, tamanho);
        var digitos = cnpj.substring(tamanho);
        var soma = 0;
        var pos = tamanho - 7;

        for (var i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }

        var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

        if (resultado != digitos.charAt(0)) return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0,tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

        if (resultado != digitos.charAt(1)) return false;

        return true;

    }, "CNPJ inválido.");

    $.validator.addMethod( "dateBR", function( value, element ) {
        return this.optional( element ) || /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/.test( value );
    }, $.validator.messages.date );

    $.validator.addMethod( "competencia", function( value, element ) {
        //range between 01/2000 to 12/9999
        return this.optional( element ) || /^(0?[1-9]|1[012])\/([2-9][0-9]{3})$/.test(value);
    }, "Mês de competência inválido." );

    $.validator.addMethod("telefone", function(value, element) {
        var telefone = value.replace(/[^\d]+/g,'');

        if (!element.required && telefone == '') return true;

        //verifica se tem a qtde de numero correto
        if(!(telefone.length >= 10 && telefone.length <= 11)) return false;

        if (telefone.length == 11 && parseInt(telefone.substring(2, 3)) != 9) return false;

        return true;

    }, "Telefone inválido.");

    $.validator.addMethod('greaterThan', function(value, element, param) {
        return ( parseInt(value) > parseInt(jQuery(param).val()) );
    }, 'O valor deve ser maior que o inicial' );

    $.validator.addMethod('lesserThan', function(value, element, param) {
        return ( parseInt(value) < parseInt(jQuery(param).val()) );
    }, 'O valor deve ser menor que o final' );

    $.validator.addMethod('dateLesserThan', function(value, element, param) {
        var v_i = value.split("/");
        var v_f = $(param).val().split("/");
        var ini = new Date(v_i[2], v_i[1] - 1, v_i[0]);
        var end = new Date(v_f[2], v_f[1] - 1, v_f[0]);

        return ( ini < end );
    }, 'A data deve ser menor que a final' );

    $.validator.addMethod('dateGreaterThan', function(value, element, param) {
        var v_i = value.split("/");
        var v_f = $(param).val().split("/");
        var ini = new Date(v_i[2], v_i[1] - 1, v_i[0]);
        var end = new Date(v_f[2], v_f[1] - 1, v_f[0]);

        return ( ini > end );
    }, 'A data deve ser maior que a inicial' );

    $.validator.addMethod('require-one', function (value) {
        return ($('input[type=checkbox].require-one').filter(':checked').length > 0);
    }, 'Selecione pelo menos uma opção');

    $.validator.addMethod( "cpfBR", function( value, element ) {
        "use strict";

        if ( this.optional( element ) ) {
            return true;
        }

        // Removing special characters from value
        value = value.replace( /([~!@#$%^&*()_+=`{}\[\]\-|\\:;'<>,.\/? ])+/g, "" );

        // Checking value to have 11 digits only
        if ( value.length !== 11 ) {
            return false;
        }

        var sum = 0,
            firstCN, secondCN, checkResult, i;

        firstCN = parseInt( value.substring( 9, 10 ), 10 );
        secondCN = parseInt( value.substring( 10, 11 ), 10 );

        checkResult = function( sum, cn ) {
            var result = ( sum * 10 ) % 11;
            if ( ( result === 10 ) || ( result === 11 ) ) {
                result = 0;
            }
            return ( result === cn );
        };

        // Checking for dump data
        if ( value === "" ||
            value === "00000000000" ||
            value === "11111111111" ||
            value === "22222222222" ||
            value === "33333333333" ||
            value === "44444444444" ||
            value === "55555555555" ||
            value === "66666666666" ||
            value === "77777777777" ||
            value === "88888888888" ||
            value === "99999999999"
        ) {
            return false;
        }

        // Step 1 - using first Check Number:
        for ( i = 1; i <= 9; i++ ) {
            sum = sum + parseInt( value.substring( i - 1, i ), 10 ) * ( 11 - i );
        }

        // If first Check Number (CN) is valid, move to Step 2 - using second Check Number:
        if ( checkResult( sum, firstCN ) ) {
            sum = 0;
            for ( i = 1; i <= 10; i++ ) {
                sum = sum + parseInt( value.substring( i - 1, i ), 10 ) * ( 12 - i );
            }
            return checkResult( sum, secondCN );
        }
        return false;

    }, "Please specify a valid CPF number." );
}

if ($.magnificPopup) {
    $.extend(true, $.magnificPopup.defaults, {
        tClose: 'Fechar (Esc)',
        tLoading: '<i class="fas fa-spinner fa-spin fa-3x"></i>',
        ajax: {
            tError: 'O conteúdo não pode ser carregado. Tente novamente em alguns instantes.'
        }
    });
}

$(".nav-pills").children().first().children().addClass('active left');
$(".nav-pills").children().last().children().addClass('right')