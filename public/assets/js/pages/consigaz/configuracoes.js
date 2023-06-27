(function () {

    "use strict";

    let $dtUnidades = $("#dt-unidades");
    let dtUnidades = $dtUnidades.DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "m_id", className: "d-none", orderable: false},
            {data: "u_id", className: "d-none", orderable: false},
            {data: "medidor", className: "dt-body-center", orderable: false},
            {data: "device", className: "dt-body-center", orderable: false},
            {data: "unidade", className: "dt-body-center", orderable: false},
            {data: "bloco", className: "dt-body-center table-one-line", orderable: false},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
        serverSide: true,
        sorting: [],
        pageLength: 25,
        pagingType: "numbers",
        searching: true,
        ajax: {
            url: $dtUnidades.data("url"),
            method: 'POST',
            data: {
                entidade: $(".content-body").data("entidade"),
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $dtUnidades.dataTable().fnProcessingIndicator(false);
                $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function() {
            $("#dt-unidades_wrapper .table-responsive").removeClass("processing");
        }
    });

    $(document).on('click', '.generate-code', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_generate_code'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {}
                },
            }
        });
    });

    $(document).on('click', '#md-generate-code .modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();

        $(".generate-code").remove();
        $(".request-code-btn").show(300);
    });

    $(document).on('click', '.request-code', function (e) {
        // para propagação
        e.preventDefault();

        // abre modal
        $.magnificPopup.open( {
            items: {src: '/consigaz/md_request_code'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {}
                },
            }
        });
    });

    $(document).on('click', '#md-request-code .modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

    let dtUsuarios = $("#dt-usuarios").DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "image", className: "d-none", orderable: false},
            {data: "nome", className: "dt-body-center", orderable: false},
            {data: "email", className: "dt-body-center", orderable: false},
            {data: "bloco", className: "dt-body-center", orderable: false},
            {data: "apto", className: "dt-body-center table-one-line", orderable: false},
            {data: "actions", className: "dt-body-center", orderable: false},
        ],
        serverSide: true,
        sorting: [],
        pageLength: 25,
        pagingType: "numbers",
        searching: true,
        ajax: {
            url: $("#dt-usuarios").data("url"),
            method: 'POST',
            data: function(d) {
                d.entidade = $("#sel-entity").val();
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-usuarios").dataTable().fnProcessingIndicator(false);
                $("#dt-usuarios_wrapper .table-responsive").removeClass("processing");
            },
        },
        fnDrawCallback: function() {
            $("#dt-usuarios_wrapper .table-responsive").removeClass("processing");
        }
    });

    $(document).on('click', '.btn-incluir-usuario', function (e) {
        e.preventDefault();

        $.magnificPopup.open( {
            items: {src: '/consigaz/md_add_user'},
            type: 'ajax',
            modal: true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: {}
                },
            }
        });
    })

    $(document).on('click', '#md-add-user .modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

}.apply(this, [jQuery]));