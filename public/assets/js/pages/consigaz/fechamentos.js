(function () {

    "use strict";

    let $dtGas = $("#dt-gas");
    let dtGas = $dtGas.DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "competencia", class: "dt-body-center"},
            {data: "inicio", class: "dt-body-center" },
            {data: "fim", class: "dt-body-center"},
            {data: "consumo", class: "dt-body-center"},
            {data: "emissao", class: "dt-body-center"},
            {data: "action", class: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        pageLength: 10,
        pagingType: "numbers",
        searching: false,
        ajax: {
            type: 'POST',
            url: $dtGas.data("url"),
            data: function (d) {
                d.entidade = $(".content-body").data("entidade");
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $dtGas.dataTable().fnProcessingIndicator(false);
                $("#dt-gas_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    $("#dt-gas tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma célula ou na última, retorna
        if (event.target.cellIndex === undefined || event.target.cellIndex === 12) return;

        let data = dtGas.row(this).data();
        // redireciona para o fechamento
        window.location = "/consigaz/fechamentos/" + $(".content-body").data("entidade") + "/" + data.id;
    });

    $(document).on('click', '.action-gas-delete', function () {

        let dis_timer, id = $(this).data('id');

        // abre a modal
        $.magnificPopup.open( {
            items: {src: '#modalExclui'}, type: 'inline',
            callbacks: {
                beforeOpen: function() {
                    $('#modalExclui .id').val( id );
                    $('#modalExclui .type').val('gas');
                },
                open: function() {
                    // desabilita botão
                    let btn = $('#modalExclui .modal-confirm');
                    btn.prop("disabled", true);
                    // inicializa timer
                    let sec = btn.data('timer');
                    // declaração do timer regressimo
                    function countDown() {
                        // mostra valor
                        btn.html(sec);
                        if (sec <= 0) {
                            // terminou. Habilita botão e atualiza texto
                            btn.prop("disabled", false);
                            btn.html('Excluir');
                            return;
                        }
                        // continua contando
                        sec -= 1;
                        dis_timer = setTimeout(countDown, 1000);
                    }
                    countDown();
                },
                close: function() {
                    clearTimeout(dis_timer);
                }
            }
        });
    });

    $(document).on('click', '#modalExclui .modal-confirm', function () {
        // mostra indicador
        let $btn = $(this);
        $btn.trigger('loading-overlay:show');
        // desabilita botões
        let $btn_d = $('.btn:enabled').prop('disabled', true);
        // pega o valor do id
        let id = $('#modalExclui .id').val();
        let type = $('#modalExclui .type').val();
        // faz a requisição
        $.post("/"+type+"/delete_fechamento", {id: id}, function(json) {
            if (json.status === 'success') {
                // fecha modal
                $.magnificPopup.close();
                // atualiza tabela
                dtGas.ajax.reload();
                // mostra notificação
                notifySuccess(json.message);
            } else {
                // fecha modal
                $.magnificPopup.close();
                // mostra erro
                notifyError(json.message);
            }
        }, 'json')
            .fail(function(xhr, status, error) {
                // fecha modal
                $.magnificPopup.close();
                // mostra erro
                notifyError(error, 'Ajax Error');
            })
            .always(function() {
                // oculta indicador e habilita botão
                $btn.trigger('loading-overlay:hide');
                // habilita botões
                $btn_d.prop('disabled', false);
                // limpa id
                $('#modalExclui .id').val('');
                $('#modalExclui .type').val('');
            });
    });

    $(document).on("click", ".action-gas-download", function () {

        let $btn = $(this);
        $btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // faz a requisição
        $.post("/gas/download", { id: $(this).data('id') }, function(json) {

            if (json.status === "error") {

                notifyError(json.message);

            } else {

                let $a = $("<a>");
                $a.attr("href", json.file);
                $("body").append($a);
                $a.attr("download", json.name + '.xlsx');
                $a[0].click();
                $a.remove();
            }
        }, 'json')
            .fail(function(xhr, status, error) {
                // mostra erro
                notifyError(error, 'Ajax Error');
            })
            .always(function() {
                // oculta indicador e habilita botão
                $btn.html('<i class="fas fa-file-download"></i>');
            });
    });

    $(document).on("click", ".btn-gas-download", function () {

        let $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/gas/download_fechamentos", {id: $('.content-body').data("entidade")}, function (json) {

                if (json.status === "error") {

                    notifyError(json.message);

                } else {
                    let $a = $("<a>");
                    $a.attr("href", json.file);
                    $("body").append($a);
                    $a.attr("download", json.name + ".xlsx");
                    $a[0].click();
                    $a.remove();
                }
            },
            "json"
        )
            .fail(function (xhr, status, error) {
                // mostra erro
                notifyError(error, "Ajax Error");
            })
            .always(function () {
                // oculta indicador e habilita botão
                $btn.trigger("loading-overlay:hide").prop("disabled", false);
            });
    });

    $(document).on('click', '.btn-gas-incluir', function (e) {
        // para propagação
        e.preventDefault();

        $("#md-gas-include .alert").html("").addClass("d-none");

        // abre modal
        $.magnificPopup.open( {
            items: {src: '#md-gas-include'},
            type: 'inline',
            modal:true,
        });
    });

    let $formGas = $(".form-gas-fechamento");

    $(document).on('click', '.modal-gas-confirm', function (e) {
        // para propagação
        e.preventDefault();

        if (!$formGas.valid())
            return;

        let $btn = $(this);
        //$btn.trigger("loading-overlay:show").prop("disabled", true);
        //$("#md-gas-include .btn").prop("disabled", true);

        $("#md-gas-include .alert").html("").addClass("d-none");

        // valida formulário
        if ( $formGas.valid() ) {
            // captura dados
            let data = $formGas.serializeArray();
            // envia os dados
            $.post('/gas/add_fechamento', data, function(json) {
                if (json.status === 'success') {
                    // vai para a pagina do fechamento
                    window.location = "/" + $(".page-header").data("url") + "/fechamento/" + $(".content-body").data("entidade") + "/" + json.id;

                    // fecha a modal
                    $.magnificPopup.close();

                } else {

                    $("#md-gas-include .alert").html(json.message).removeClass("d-none");
                }
            }, 'json')
                .fail(function(xhr, status, error) {
                    // mostra erro
                    notifyError(error, 'Ajax Error');
                })
                .always(function() {
                    $btn.trigger("loading-overlay:hide");
                    $("#md-gas-include .btn").removeAttr("disabled");
                });
        }
    });

    $(document).on('click', '.modal-dismiss', function (e) {
        e.preventDefault();

        $.magnificPopup.close();
    });

    $.validator.addClassRules("vdate", { dateBR : true });
    $.validator.addClassRules("vcompetencia", { competencia : true });
    $.validator.addClassRules("vnumber", { number : true });

    $('#tar-competencia').mask('00/0000');
    $('#tar-data-ini').mask('00/00/0000');
    $('#tar-data-fim').mask('00/00/0000');
    $('#tar-gas-competencia').mask('00/0000');
    $('#tar-gas-data-ini').mask('00/00/0000');
    $('#tar-gas-data-fim').mask('00/00/0000');

    // configura validação
    $formGas.validate();

    let $dtUnidades = $("#dt-unidades");
    let dtUnidades = $dtUnidades.DataTable({
        dom: '<"table-responsive"t>pr',
        processing: true,
        columns: [
            {data: "medidor", class: "dt-body-center"},
            {data: "leitura_anterior", class: "dt-body-center" },
            {data: "leitura_atual", class: "dt-body-center"},
            {data: "consumo", class: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        pageLength: 10,
        pagingType: "numbers",
        searching: false,
        ajax: {
            type: 'POST',
            url: $dtUnidades.data("url"),
            data: function (d) {
                d.fid = $(".content-body").data("fechamento");
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $dtUnidades.dataTable().fnProcessingIndicator(false);
                $("#dt-gas_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    $("#dt-unidades tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma célula ou na última, retorna
        if (event.target.cellIndex === undefined || event.target.cellIndex === 12) return;

        let data = dtUnidades.row(this).data();
        // redireciona para o fechamento
        window.location = "/consigaz/relatorio/" + $(".content-body").data("entidade") + "/" + data.DT_RowId;
    });

    $(document).on("click", ".btn-download", function () {

        let $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/gas/download", {id: $('.content-body').data("fechamento")}, function (json) {

                if (json.status === "error") {

                    notifyError(json.message);

                } else {
                    let $a = $("<a>");
                    $a.attr("href", json.file);
                    $("body").append($a);
                    $a.attr("download", json.name + ".xlsx");
                    $a[0].click();
                    $a.remove();
                }
            },
            "json"
        )
            .fail(function (xhr, status, error) {
                // mostra erro
                notifyError(error, "Ajax Error");
            })
            .always(function () {
                // oculta indicador e habilita botão
                $btn.trigger("loading-overlay:hide").prop("disabled", false);
            });
    });

}.apply(this, [jQuery]));