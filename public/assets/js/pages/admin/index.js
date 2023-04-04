(function() {

    'use strict';
    
    $('.card-central').hover(function() {
        if ($(this).data('parent')) {
            $('#'+$(this).data('parent')).addClass('hover');
            $(this).addClass('hover');
        }
    }, function() {
        if ($(this).data('parent')) {
            $('#'+$(this).data('parent')).removeClass('hover');
            $(this).removeClass('hover');
        }
    });

}).apply(this, [jQuery]);