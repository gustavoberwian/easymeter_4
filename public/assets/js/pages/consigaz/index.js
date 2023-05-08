(function () {

    "use strict";

    if ($("#dt-entidades").length) {
        let dtEntidades = $("#dt-entidades").DataTable({
            dom        : '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
            processing : true,
            paging     : true,
            columns    : [
                {data: "nome", className: "dt-body-center"},
                {data: "actions", className: "dt-body-center"},
            ],
            serverSide : true,
            pagingType : "numbers",
            searching  : true,
            ajax       : {
                type: 'POST',
                url: $("#dt-entidades").data("url"),
                error: function () {
                    notifyError(
                        "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                    );
                    $("#dt-entidades").dataTable().fnProcessingIndicator(false);
                    $("#dt-entidades_wrapper .table-responsive").removeClass("processing");
                },
            },
        });

        $("#dt-entidades tbody").on("click", "tr", function (event) {
            // se o clique não foi em uma celula ou na última, retorna
            if (event.target.cellIndex == undefined) return;

            let data = dtEntidades.row(this).data();

            window.location = "/" + $(".content-body").data("class") + "/unidades/" + data.id;
        });
    } else {
        $(document).on('click', '.card-group', function () {
            window.location = $(".content-body").data("class") + "/" + $(".content-body").data("monitoria") + "/" + $(this).data('group');
        });
    }
    

}.apply(this, [jQuery]));