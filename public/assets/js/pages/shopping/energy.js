(function($) {

    'use strict';

    var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
    isMobile = true;
}

    let dtResume = $("#dt-resume").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
        processing : true,
        paging     : true,
        columns    : [
            {data: "device", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "type", className: "dt-body-center"},
            {data: "value_read", className: "dt-body-center"},
            {data: "value_month", className: "dt-body-center"},
            {data: "value_last_month", className: "dt-body-center"},
            {data: "value_ponta", className: "dt-body-center"},
            {data: "value_fora", className: "dt-body-center"},
            {data: "value_last", className: "dt-body-center"},
            {data: "value_future", className: "dt-body-center"},
        ],
        serverSide : true,
        sorting    : [],
        order      : [[ 2, "asc" ], [ 1, "asc" ]],
        pagingType : "numbers",
        pageLength : 36,
        searching  : true,
        ajax       : {
            type: 'POST',
            data: {
                group: $(".page-header").data("group")
            },
            url: $("#dt-resume").data("url"),
            /*success: function (json) {
                if (json.status === 'error') {
                    notifyError(json.message);
                    $("#dt-resume_processing").hide()
                    $("#dt-resume").children('tbody').append('<tr class="odd"><td valign="top" colspan="13" class="dataTables_empty">Nenhum registro encontrado</td></tr>');
                }
                if (json.data.length === 0) {
                    $("#dt-resume_processing").hide()
                    $("#dt-resume").children('tbody').append('<tr class="odd"><td valign="top" colspan="13" class="dataTables_empty">Nenhum registro encontrado</td></tr>');
                }
            },*/
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-resume").dataTable().fnProcessingIndicator(false);
                $("#dt-resume_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    var start = moment().subtract(6, 'days');
    var end = moment();
    var chart = {};
    var start_last;
    var end_last;
    var device = 0;

    function apexchart(start = moment().subtract(6, 'days'), end = moment()) {

        $(".chart-main").each(function() {
            $(this).parent().parent().trigger('loading-overlay:show');

            var el = $(this);

            var dados = {
                device  : device,
                start   : start.format("YYYY-MM-DD"),
                end     : end.format("YYYY-MM-DD"),
                field   : el.data("field"),
                group   : $(".page-header").data("group"),
            };

            $.ajax({
                method  : 'POST',
                url     : "/energia/chart_engineering",
                data    : dados,
                dataType: 'json',
                success : function (json) {

                    if (json.status === 'error') {
                        notifyError(json.message);
                        el.addClass('h-100')
                        el.append('<div style="display: flex; justify-content: center; align-items: center; height: 100%;"><div>Nenhum registro encontrado</div></div>');
                        return;
                    }

                    json.yaxis.labels.formatter = function (value) {
                        return (value === null) ? "" : value.toLocaleString("pt-BR", {minimumFractionDigits: json.extra.decimals, maximumFractionDigits: json.extra.decimals}) + " " + json.extra.unit;
                    };

                    json.tooltip.x.formatter = function (value, {series, seriesIndex, dataPointIndex, w}) {
                        return json.extra.tooltip.title[dataPointIndex];
                    };

                    json.tooltip.y.formatter = function (value) {

                        if (el.data("field") === 'mainFactor' || el.data("field") === 'factor') {

                            if (value === null)
                                return null;
                            if (value > 0) {
                                return (1 - value).toLocaleString("pt-BR", {minimumFractionDigits: 3, maximumFractionDigits: 3}) + " ind";
                            } else if (value < 0) {
                                return (1 - (value * -1)).toLocaleString("pt-BR", {minimumFractionDigits: 3, maximumFractionDigits: 3}) + " cap";
                            }
                            return 1;

                        } else {

                            return (value === null) ? "" : value.toLocaleString("pt-BR", {minimumFractionDigits: json.extra.tooltip.decimals, maximumFractionDigits: json.extra.tooltip.decimals}) + " " + json.extra.unit;
                        }
                    };

                    if (json.hasOwnProperty('extra')) {
                        if (json.extra.hasOwnProperty('footer')) {
                            el.parent().parent().parent().children().remove(".card-footer");
                            el.parent().parent().parent().append(json.extra.footer);
                        }
                    }

                    if (json.chart.hasOwnProperty('events') && !isMobile) {
                        if (json.chart.events.hasOwnProperty('click')) {
                            json.chart.events.click = function (event, chartContext, config) {
                                if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {
                                    apexchart(start_last, end_last)
                                    daterange(start_last, end_last)
                                } else {
                                    var data = json.extra.dates[config.dataPointIndex]
                                    apexchart(moment(data), moment(data))
                                    daterange(moment(data), moment(data))
                                }
                            }
                        }
                    }

                    if (el.data("field") === 'mainActivePositive') {

                        $(".main").html(json.extra.custom.main);
                        $(".period").html(json.extra.custom.period);
                        $(".period-f").html(json.extra.custom.period_f);
                        $(".period-p").html(json.extra.custom.period_p);
                        $(".month").html(json.extra.custom.month);
                        $(".month-f").html(json.extra.custom.month_f);
                        $(".month-p").html(json.extra.custom.month_p);
                        $(".prevision").html(json.extra.custom.prevision);
                        $(".prevision-p").html(json.extra.custom.prevision_p);
                        $(".prevision-f").html(json.extra.custom.prevision_f);
                        $(".day").html(json.extra.custom.day);
                        $(".day-p").html(json.extra.custom.day_p);
                        $(".day-f").html(json.extra.custom.day_f);

                    } else if (el.data("field") === 'mainFactor' || el.data("field") === 'factor') {

                        json.yaxis.labels.formatter = function (value, index) {
                            if (value === null)
                                return null;
                            if (value > 0) {
                                return (1 - value).toLocaleString("pt-BR", {minimumFractionDigits: json.extra.decimals, maximumFractionDigits: json.extra.decimals}) + " ind";
                            } else if (value < 0) {
                                return (1 - (value * -1)).toLocaleString("pt-BR", {minimumFractionDigits: json.extra.decimals, maximumFractionDigits: json.extra.decimals}) + " cap";
                            }
                            return 1;
                        };
                    }

                    if (chart[el.data("field")]) {
                        chart[el.data("field")].updateOptions(json);
                    } else {
                        chart[el.data("field")] = new ApexCharts(el[0], json);
                        chart[el.data("field")].render();
                    }

                    if (start.format("YYYY-MM-DD") === end.format("YYYY-MM-DD")) {
                        // Populando seletor de data e ícones
                        $('#daterange-main span').html(start.format('ddd, DD/MM/YYYY'));
                    } else {
                        // Populando seletor de data e ícones
                        $('#daterange-main span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                    }

                    if (start.format("YYYY-MM-DD") !== end.format("YYYY-MM-DD")) {
                        start_last = start;
                        end_last = end;
                    }
                },
                error: function (xhr, status, error) {
                    notifyError(error, 'Ocorreu um erro ao processar a solicitação.');
                    return false;
                },
                complete: function () {
                    el.parent().parent().trigger('loading-overlay:hide');

                    if ($('.card-footer')) {
                        $('.card-footer').trigger('loading-overlay:hide');
                    }
                }
            });
        });
    }

    function daterange(start = moment().subtract(6, 'days'), end = moment()) {
        // Daterange picker
        $('#daterange-main').daterangepicker(
            {
                startDate: start,
                endDate: end,
                maxDate: moment().format('DD/MM/YYYY'),
                maxSpan: {"days": 60},
                opens: "right",
                ranges: {
                    'Hoje': [moment(), moment()],
                    'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
                    'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                    'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "De",
                    "toLabel": "até",
                    "customRangeLabel": "Personalizado"
                },
            },
            function (start, end, label) {

                apexchart(start, end);

                $('#dt-data').DataTable().ajax.reload();

                $('#daterange-main span').html(Math.round((end - start) / 86400000) == 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
            }
        );
    }

    //apexchart(start, end);
    //daterange()

    /**
     * Handler on change select value
     */
    $('#sel-device').on('change', function () {
        device = this.value;
        if (device === 'href') {
            window.location = $(this).find(':selected').data('url');
            return;
        }

        if (this.value == "C" || this.value == "U") {
            $('button[data-bs-target="#data"]').addClass("disabled");
            $('button[data-bs-target="#analysis"]').addClass("disabled");
            $('option[value="data"]').attr("disabled", true);
            $('option[value="analysis"]').attr("disabled", true);
            if ($('button[data-bs-toggle="pill"].active').html() == "Análises" || $('button[data-bs-toggle="pill"].active').html() == "Dados") {
                setTimeout(function() {
                    $('button[data-bs-target="#data"]').removeClass("disabled");
                    $('button[data-bs-target="#analysis"]').removeClass("disabled");
                    $('option[value="data"]').removeAttr("disabled");
                    $('option[value="analysis"]').removeAttr("disabled");
                }, 100);
            }
        } else {
            $('button[data-bs-target="#data"]').removeClass("disabled");
            $('button[data-bs-target="#analysis"]').removeClass("disabled");
            $('option[value="data"]').removeAttr("disabled");
            $('option[value="analysis"]').removeAttr("disabled");
        }

        apexchart(start_last, end_last);
        daterange(start_last, end_last);
        setTimeout(function() {
            $('#dt-data').DataTable().ajax.reload();
        }, 100);
    })

    let dtAbnormal = $("#dt-abnormal").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
        processing: true,
        paging: true,
        columns: [
            {data: "date", className: "dt-body-center"},
            {data: "voltageA", className: "dt-body-center"},
            {data: "voltageB", className: "dt-body-center"},
            {data: "voltageC", className: "dt-body-center"},
            {data: "currentA", className: "dt-body-center"},
            {data: "currentB", className: "dt-body-center"},
            {data: "currentC", className: "dt-body-center"},
            {data: "activeA", className: "dt-body-center"},
            {data: "activeB", className: "dt-body-center"},
            {data: "activeC", className: "dt-body-center"},
            {data: "reactiveA", className: "dt-body-center"},
            {data: "reactiveB", className: "dt-body-center"},
            {data: "reactiveC", className: "dt-body-center"},
            {data: "activePositiveConsumption", className: "dt-body-center"},
        ],
        serverSide: true,
        sorting: [],
        pagingType: "numbers",
        searching: true,
        pageLength: 20,
        deferLoading: 1,
        buttons: [
            {
                extend: 'excel',
                //messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.',
                //title: 'Data export'
            },
            'pdf', 
            'print'
        ],        
        ajax: {
            type: 'POST',
            data: function (d) {
                d.device = device;
                d.init   = start.format("YYYY-MM-DD");
                d.finish = end.format("YYYY-MM-DD");
                d.type   = $(".type").val();
                d.min    = $("#min").val();
                d.max    = $("#max").val();
                d.group  = $(".page-header").data("group");
            },
            url: "/energia/data",
            error: function () {
                notifyError("Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.");
                $("#dt-abnormal").dataTable().fnProcessingIndicator(false);
                $("#dt-abnormal_wrapper .table-responsive").removeClass("processing");
            },
            dataSrc: function(d) {
                $(".btn-download-abnormal").prop("disabled", (d.recordsTotal == 0));
                return d.data;    
            }            
        },
    });

    $('Select.type').on('change', function () {
        $(".btn-view").prop("disabled", false);
        $("#min").val($("select.type [value='"+this.value+"']").data("min"));
        $("#max").val($("select.type [value='"+this.value+"']").data("max"));
    })


    let dtData = $("#dt-data").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
        processing: true,
        paging: true,
        columns: [
            {data: "date", className: "dt-body-center"},
            {data: "activePositive", className: "dt-body-center"},
            {data: "voltageA", className: "dt-body-center"},
            {data: "voltageB", className: "dt-body-center"},
            {data: "voltageC", className: "dt-body-center"},
            {data: "currentA", className: "dt-body-center"},
            {data: "currentB", className: "dt-body-center"},
            {data: "currentC", className: "dt-body-center"},
            {data: "activeA", className: "dt-body-center"},
            {data: "activeB", className: "dt-body-center"},
            {data: "activeC", className: "dt-body-center"},
            {data: "reactiveA", className: "dt-body-center"},
            {data: "reactiveB", className: "dt-body-center"},
            {data: "reactiveC", className: "dt-body-center"},
            {data: "activePositiveConsumption", className: "dt-body-center"},
        ],
        serverSide: true,
        sorting: [],
        order: [[ 0, 'desc' ]],
        pagingType: "numbers",
        pageLength: 36,
        searching: true,
        buttons: [
            {
                extend: 'excel',
                //messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.',
                //title: 'Data export'
            },
            'pdf', 
            'print'
        ],        
        ajax: {
            type: 'POST',
            data: function (d) {
                d.device = device;
                d.init   = start.format("YYYY-MM-DD");
                d.finish = end.format("YYYY-MM-DD");
                d.group  = $(".page-header").data("group");
            },
            url: "/energia/data",
            error: function () {
                notifyError("Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes.");
                $("#dt-data").dataTable().fnProcessingIndicator(false);
                $("#dt-data_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    $(".btn-view").on("click", function (event) {
        if ($("select.type").val() == null) return;
        $(".btn-download-abnormal").prop("disabled", true);
        setTimeout(function() {
            $('#dt-abnormal').DataTable().ajax.reload();
        }, 100);
    });

    $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        var el = $(e.target).data("bs-target");
        $(".selector").show();
        if (el === "#charts" || el === "#engineering") {
            $(".consumption").show();
        } else {
            $(".consumption").hide();
            if (el === "#resume")
                $(".selector").hide();
        }
    });

    $(document).on("click", ".btn-download", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/energia/download_resume", {id: $(this).data("group")}, function (json) {

                if (json.status == "error") {
                    
                    notifyError(json.message);

                } else {
                    var $a = $("<a>");
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

    // **
    // * Handler Row click linha
    // * Abre página do shopping
    // **
    $("#dt-resume tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;

        let data = dtResume.row(this).data();
        let newWindow = window.open(window.location);
        newWindow.control = 1;
        newWindow.onload = function() {
            newWindow.$("#sel-device option[value=" + data.device + "]").attr('selected', 'selected');
            newWindow.$('#sel-device').trigger('change');
            newWindow.$('button[data-bs-target="#charts"]').trigger('click');
            newWindow.$('.nav-sel option[value="charts"]').attr('selected', 'selected');
            newWindow.$('button[data-bs-target="#data"]').removeClass("disabled");
            newWindow.$('button[data-bs-target="#analysis"]').removeClass("disabled"); 
        };
        
    });

    $(document).on("click", ".btn-download-abnormal", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/energia/download_abnormal", {
            id: $(this).data("group"), 
            device: device,
            init: start.format("YYYY-MM-DD"),
            finish: end.format("YYYY-MM-DD"),
            type: $(".type").val(),
            min: $("#min").val(),
            max: $("#max").val()
        }, function (json) {

                if (json.status == "error") {
                    
                    notifyError(json.message);

                } else {
                    var $a = $("<a>");
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

    if(!window.control){
        window.control = 0;
    }

    if(window.control == 0){
        $('#sel-device').trigger('change');
    }

}).apply(this, [jQuery]);