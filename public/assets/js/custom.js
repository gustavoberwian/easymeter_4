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