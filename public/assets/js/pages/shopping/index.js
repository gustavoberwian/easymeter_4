(function () {

    "use strict";

    $(document).on('click', '.card', function () {
        window.location = "/shopping/energy/" + $(this).data('group');
    });

}.apply(this, [jQuery]));