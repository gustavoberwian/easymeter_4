// **
// * Datatables
// **
(function ($) {
  "use strict";

  // ***********************************************************************************************
  // * Inicializadores
  // ***********************************************************************************************

  // **
  // * Inicializa datatable relatórios
  // **
  var $dtRelatorios = $("#dt-relatorios").DataTable({
    dom: '<"table-responsive"t>pr',
    processing: true,
    columns: [
      { data: "unidade", class: "dt-body-center" },
      { data: "energia", class: "dt-body-center" },
    ],
    order: [],
    serverSide: true,
    ajax: {
      url: $("#dt-relatorios").data("url"),
      data: function (d) {
        return $.extend({}, d, {
          c: $("select").val(),
        });
      },
    },
  });

  $("select").on("change", function () {
    $dtRelatorios.ajax.reload();
  });

  // **
  // * Handler Baixar Planilha Água
  // **
  $(".btn-download").on("click", function () {
    var $btn = $(this);
    $btn.trigger("loading-overlay:show").prop("disabled", true);

    // faz a requisição
    $.post(
      "/hortolandia/download_relatorio",
      { c: $("select").val() },
      function (json) {
        var $a = $("<a>");
        $a.attr("href", json.file);
        $("body").append($a);
        $a.attr("download", json.name + ".xlsx");
        $a[0].click();
        $a.remove();
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

  // **
  // * Handler Row click fechamentos
  // **
  $("table.table-clickable").on("click", "tbody tr", function (event) {
    // se o clique não foi em uma celula, retorna
    if (event.target.cellIndex == undefined) return;
    // pega dados da linha
    var data = $("#" + event.delegateTarget.id)
      .DataTable()
      .row(this)
      .data();
    // redireciona para o fechamento
    window.open("/hortolandia/relatorio/" + data.linha, "_blank");
  });
}.apply(this, [jQuery]));
