(function($) {

	'use strict';

    $(document).on("click", ".btn-cadastro", function(e)
    {
        e.preventDefault();
        $('.tab-form').addClass('d-none');
        $('.tab-form.cadastro').removeClass('d-none');
        $('.card-actions.buttons button').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on("click", ".btn-entradas", function(e)
    {
        e.preventDefault();
        $('.tab-form').addClass('d-none');
        $('.tab-form.entradas').removeClass('d-none');
        $('.card-actions.buttons button').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on("click", ".btn-unidades", function(e)
    {
        e.preventDefault();
        $('.tab-form').addClass('d-none');
        $('.tab-form.unidades').removeClass('d-none');
        $('.card-actions.buttons button').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on("click", ".btn-leituras", function(e)
    {
        e.preventDefault();
        $('.tab-form').addClass('d-none');
        $('.tab-form.leituras').removeClass('d-none');
        $('.card-actions.buttons button').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on("click", ".btn-configuracoes", function(e)
    {
        e.preventDefault();
        $('.tab-form').addClass('d-none');
        $('.tab-form.configuracoes').removeClass('d-none');
        $('.card-actions.buttons button').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on("click", ".btn-consumo", function(e)
    {
        e.preventDefault();
        $('.tab-form').addClass('d-none');
        $('.tab-form.consumo').removeClass('d-none');
        $('.card-actions.buttons button').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on("click", ".btn-geral", function(e)
    {
        e.preventDefault();
        $('.tab-form').addClass('d-none');
        $('.tab-form.geral').removeClass('d-none');
        $('.card-actions.buttons button').removeClass('active');
        $(this).addClass('active');
    });

    var dtUnidades = $('#dt-unidades').DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        pagingType: "numbers",
        ordering: false,
        searching: false,
        lengthChange: false,
        serverSide: true,
        ajax: {
            url: "/condominio/get_unidades_bloco",
            data: function ( d ) {
                return $.extend( {}, d, {
                    bloco: $("#sel-bloco option:selected").val(), mode: url[4]
                } );
            },
            complete: function (json, type) {
                if (type == "success") {
                    $('.action-medidor').popover({container: 'body', placement: 'bottom', trigger: 'hover', html: true});
                }
            },
            error: function () {
                notifyError( 'Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.' );
                $('#dt-alertas').dataTable().fnProcessingIndicator(false);
                $('.table-responsive').removeClass('processing');
            }
        },
        columns: [ { data: "apto", class: "dt-body-center" }, { data: "andar", class: "dt-body-center" }, { data: "codigo", class: "dt-body-center" },
            { data: "tipo", class: "dt-body-center" }, { data: "nome" }, { data: "email" }, { data: "telefone", class: "dt-body-center" }, { data: "medidores" }, { data: "action", class: "actions dt-body-center" } ],
        fnPreDrawCallback: function() { $('.table-responsive').addClass('processing'); },
        fnDrawCallback: function(oSettings) { $('.table-responsive').removeClass('processing'); }
    });

}).apply(this, [jQuery]);
