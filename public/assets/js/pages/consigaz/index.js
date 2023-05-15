(function () {

    "use strict";

    if ($("#dt-entidades").length) {
        let dtEntidades = $("#dt-entidades").DataTable({
            dom        : '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
            processing : true,
            paging     : true,
            columns    : [
                {data: "nome", className: "dt-body-center table-one-line"},
                {data: "ultima_competencia", className: "dt-body-center"},
                {data: "opened", className: "dt-body-center"},
                {data: "closed", className: "dt-body-center"},
                {data: "vermelho", className: "dt-body-center"},
                {data: "amarelo", className: "dt-body-center"},
                {data: "verde", className: "dt-body-center"},
                {data: "ultimo_mes", className: "dt-body-center"},
                {data: "mes_atual", className: "dt-body-center"},
                {data: "previsao", className: "dt-body-center"},
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

    $(document).on('click', '.btn-sheet-condos', function () {
        let _self = this;
        $(_self).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            method: 'POST',
            url: '/consigaz/download_clientes',
            dataType: 'json',
            success: function (json) {
                if (json.status !== "success") {
                    // notifica erro
                    notifyError(json.message);
                }
            },
            error: function (xhr, status, error) {
            },
            complete: function () {
                $(_self).html('<i class="fas fa-file-download"></i> Baixar Planilha');
            }
        });
    })
    

}.apply(this, [jQuery]));