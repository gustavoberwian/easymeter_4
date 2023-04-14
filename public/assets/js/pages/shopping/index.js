(function () {

    "use strict";

    $(document).on('click', '.card', function () {
        window.location = $(".content-body").data("class") + "/" + $(".content-body").data("monitoria") + "/" + $(this).data('group');
    });

}.apply(this, [jQuery]));