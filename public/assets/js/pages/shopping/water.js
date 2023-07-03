(function($) {

    'use strict';

    var isMobile = false; //initiate as false
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
        isMobile = true;
    }

    var start = moment().subtract(6, 'days');
    var end = moment();
    var chart = {};
    var start_last;
    var end_last;
    var start_res;
    var end_res;
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
                compare : $('#compare').val(),
                shopping_id : $('.content-body').data('group')
            };

            $.ajax({
                method  : 'POST',
                url     : "/water/chart",
                data    : dados,
                dataType: 'json',
                success : function (json) {

                    json.yaxis.labels.formatter = function (value) {
                        return (value === null) ? "" : value.toLocaleString("pt-BR", {minimumFractionDigits: json.extra.decimals, maximumFractionDigits: json.extra.decimals}) + " " + json.extra.unit;
                    };

                    json.tooltip.x.formatter = function (value, {series, seriesIndex, dataPointIndex, w}) {
                        return json.extra.tooltip.title[dataPointIndex];
                    };

                    json.tooltip.y.formatter = function (value) {
                        if (value === json.extra.custom.max * 0.005 || json.extra.custom.max === 0) {
                            return 0 + " " + json.extra.unit;
                        } else if (value === json.extra.custom.max_c * 0.005 || json.extra.custom.max_c === 0) {
                            return 0 + " " + json.extra.unit;
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
                    if (isMobile == true) {
                        $(".select2").remove();
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

                    if (el.data("field") === 'consumption') {

                        $(".main").html(json.extra.custom.main);
                        $(".period").html(json.extra.custom.period);
                        $(".period-o").html(json.extra.custom.period_o);
                        $(".period-c").html(json.extra.custom.period_c);
                        $(".month").html(json.extra.custom.month);
                        $(".month-o").html(json.extra.custom.month_o);
                        $(".month-c").html(json.extra.custom.month_c);
                        $(".prevision").html(json.extra.custom.prevision);
                        $(".prevision-o").html(json.extra.custom.prevision_o);
                        $(".prevision-c").html(json.extra.custom.prevision_c);
                        $(".day").html(json.extra.custom.day);
                        $(".day-o").html(json.extra.custom.day_o);
                        $(".day-c").html(json.extra.custom.day_c);
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
                        start_res = start;
                        end_res = end;
                    } else {
                        // Populando seletor de data e ícones
                        $('#daterange-main span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                    }

                    if (start.format("YYYY-MM-DD") !== end.format("YYYY-MM-DD")) {
                        start_last = start;
                        end_last = end;
                        start_res = start;
                        end_res = end;
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

    $('.btn-generate-resume').on('click', function(e){
        e.preventDefault();
        var dados = {
            device  : device,
            start   : start_res.format("YYYY-MM-DD"),
            end     : end_res.format("YYYY-MM-DD"),
            compare : $('#compare').val(),
        };

        $.ajax({
            method  : 'POST',
            url     : "/water/generateResume",
            data    : {
                dados,
                group: $('.content-body').data('group')},
            dataType: 'json',
            success : function (json) {
                var $a = $("<a>");
                $a.attr("href", json.file);
                $("body").append($a);
                $a.attr("download", json.name + ".xlsx");
                $a[0].click();
                $a.remove();
            }
        })
    })
    

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

                $('#daterange-main span').html(Math.round((end - start) / 86400000) == 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
            }
        );
    }

    /**
     * Handler on change select value
     */
    $('#sel-device').on('change', function () {
        device = this.value;
        if (device === 'href') {
            window.location = $(this).find(':selected').data('url');
            return;
        }

        if ($('#compare').hasClass("select2-hidden-accessible")) {
            $('#compare').val(null).trigger('change');
            $("#compare option").prop('disabled', false);
            $("#compare option[value='" + device + "']").prop('disabled', true);
            $('#compare').select2('destroy').select2({"theme": "bootstrap", "placeholder": "Comparar", "allowClear": true});
            $('.select2-container--bootstrap').attr('style', 'width: auto;')

        }

        apexchart(start_last, end_last);
        daterange(start_last, end_last);

        $('#daterange-main span').html(Math.round((end - start) / 86400000) == 1 ? moment(start).format('ddd, DD/MM/YYYY') : moment(start).format('DD/MM/YYYY') + ' - ' + moment(end).format('DD/MM/YYYY'));
        setTimeout(function() {
//            $('#dt-data').DataTable().ajax.reload();
        }, 100);
    })

    let dtResume = $("#dt-resume").DataTable({
        dom: '<"table-responsive"t>r<"row"<"col-md-6"><"col-md-6"p>>',
        processing: true,
        paging: true,
        columns: [
            {data: "device", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "type", className: "dt-body-center"},
            {data: "value_read", className: "dt-body-center"},
            {data: "value_month", className: "dt-body-center"},
            {data: "value_last", className: "dt-body-center"},
            {data: "value_last_month", className: "dt-body-center"},
            {data: "value_future", className: "dt-body-center"},
        ],
        serverSide: true,
        sorting: [],
        order: [[ 0, "asc" ]],
        pagingType: "numbers",
        pageLength: 36,
        searching: true,
        ajax: {
            type: 'POST',
            url: $("#dt-resume").data("url"),
            data: {
                group: $('.content-body').data('group')
            },
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#dt-resume").dataTable().fnProcessingIndicator(false);
                $("#dt-resume_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    
    $("#dt-resume tbody").on("click", "tr", function (event) {
        // se o clique não foi em uma celula ou na última, retorna
        if (event.target.cellIndex == undefined) return;
        

        let data = dtResume.row(this).data();         
        let newWindow = window.open(window.location);
        newWindow.control = 1;
        newWindow.onload = function () {
            newWindow.$("#sel-device option[value=" + data.device + "]").attr('selected', 'selected');
            newWindow.$('#sel-device').trigger('change');
            newWindow.$('.nav-pills button[data-bs-target="#charts"]').tab('show');
        };
    });

    $(document).on("click", ".btn-download", function () {
        
        var $btn = $(this);
        $btn.trigger("loading-overlay:show").prop("disabled", true);

        // faz a requisição
        $.post("/water/downloadResume", {id: $(this).data("group")}, function (json) {

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

    $(".selector").hide();
    $(".consumption").hide();
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

    $(document).on("click", ".btn-reload-chart", function () {
        apexchart(start, end);
    });

    $('#compare').on('change', function () {
        apexchart(start_last, end_last);
    })

    $('#compare').on('select2:unselecting', function(e) {
        $(this).data('unselecting1', true);
        $(this).data('unselecting2', true);
    });

    $('#compare').on('select2:open', function(e) {
        var unselecting1 = $(this).data('unselecting1');
        var unselecting2 = $(this).data('unselecting2');
    
        if(unselecting1 || unselecting2) {
            $(this).select2('close');
    
            if(unselecting1) {
                $(this).data('unselecting1', false);
            } else {
                $(this).data('unselecting2', false);
            }
        }
    });

    if(!window.control){
        window.control = 0;
    }

    if(window.control == 0){
        $('#sel-device').trigger('change');
    }
    
}).apply(this, [jQuery]);