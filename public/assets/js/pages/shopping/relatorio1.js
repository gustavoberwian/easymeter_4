(function () {
  "use strict";

  var chart = "";
  // Função para popular gráfico
  function apexchart() {
    $(".apexchart-consumodiario-body").trigger("loading-overlay:show");

    var medidor_id = $("#page-header").data("medidor");
    var url = "/ajax/apexchart_consumodiario_ano";
    var dados = {
      mid: medidor_id,
      start: moment().subtract(12, "month").format("YYYY-MM-DD"),
      end: moment().format("YYYY-MM-DD"),
    };

    $.ajax({
      method: "POST",
      url: url,
      data: dados,
      dataType: "json",
      success: function (json) {
        json.tooltip.x.formatter = function (
          value,
          { series, seriesIndex, dataPointIndex, w }
        ) {
          return value + " - " + json.extra.tooltip.title[dataPointIndex];
        };
        json.tooltip.y.formatter = function (
          value,
          { series, seriesIndex, dataPointIndex, w }
        ) {
          return value + " kWh";
        };
        json.tooltip.y.title.formatter = (seriesName) => seriesName + ":";
        json.xaxis.labels.formatter = function (value) {
          return value;
        };
        json.yaxis.labels.formatter = function (value) {
          return value + " kWh";
        };
        json.chart.events.click = "";

        if (chart) {
          chart.updateOptions(json);
        } else {
          chart = new ApexCharts(
            document.querySelector("#apexchart-consumodiario"),
            json
          );
          chart.render();
        }
      },
      error: function (xhr, status, error) {
        notifyError(error, "Ocorreu um erro ao processar a solicitação.");
        return false;
      },
      complete: function () {
        $(".apexchart-consumodiario-body").trigger("loading-overlay:hide");
      },
    });
  }

  apexchart();
}.apply(this, [jQuery]));
