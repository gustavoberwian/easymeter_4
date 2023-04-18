(function () {

    "use strict";

    if ($("#dt-entidades").length) {
        $("#dt-entidades tbody").on("click", "tr", function (event) {
            window.location = $(".content-body").data("class") + "/" + $(".content-body").data("monitoria") + "/" + $(this).data('group');
        });
    } else {
        $(document).on('click', '.card', function () {
            window.location = $(".content-body").data("class") + "/" + $(".content-body").data("monitoria") + "/" + $(this).data('group');
        });
    }

}.apply(this, [jQuery]));