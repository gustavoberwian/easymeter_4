/* Add here all your JS customizations */
if ($.fn.dataTableExt) {
    $.fn.dataTableExt.oApi.fnProcessingIndicator = function ( oSettings, onoff ) {
        if ( typeof( onoff ) == 'undefined' ) {
            onoff = true;
        }
        this.oApi._fnProcessingDisplay( oSettings, onoff );
    };
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