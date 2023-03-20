(function () {

    "use strict";

    var atual = 0;
    var last = 0;
    var evtSource = new EventSource('sse/event?atual=' + atual + '&last=' + last);
    var chart;
    
    var alertListener = function (event) {
        var json = JSON.parse(event.data);

        $.each(json, function(key, value) {
            if (key === 0) {
                last = value.last;
            }

            if (value.prepend === 'prepend') {
                $(".body-alerts").children().last().hide('slow', function(){ $(".body-alerts").children().last().remove(); })

                setTimeout(function () {
                    $(".body-alerts").prepend($(value.data).hide().delay(500).show('slow'));
                    setTimeout( function () {
                        $(".body-alerts").children().first().addClass('blink')
                    }, 500 );

                    for(let i = 900; i < 4500; i = i + 900) {
                        setTimeout(function () { $(".blink").css('visibility', "hidden") }, i);
                        setTimeout(function () { $(".blink").css('visibility', "visible") },i+450);
                    }

                    setTimeout( function () {
                        $(".body-alerts").children().removeClass('blink');
                    }, 5500);
                }, 500);
            } else {
                $(".body-alerts").append(value.data);
            }
        });
    }

    var chartListener = function (event) {

        $(".chart-main").each(function () {
            var el = $(this);
            var json = JSON.parse(event.data);

            json.yaxis.labels.formatter = function (value) {
                return (value === null) ? "" : value.toLocaleString("pt-BR", {
                    minimumFractionDigits: json.extra.decimals,
                    maximumFractionDigits: json.extra.decimals
                }) + " " + json.extra.unit;
            };

            json.tooltip.x.formatter = function (value, {series, seriesIndex, dataPointIndex, w}) {
                return json.extra.tooltip.title[dataPointIndex];
            };

            json.tooltip.y.formatter = function (value) {
                return (value === null) ? "" : value.toLocaleString("pt-BR", {
                    minimumFractionDigits: json.extra.tooltip.decimals,
                    maximumFractionDigits: json.extra.tooltip.decimals
                }) + " " + json.extra.unit;
            };

            $(".total").html(json.extra.custom.period);

            $(".card-unidade").each(function (i) {
                $(this).find('.last-unidade').html(json.extra.units.last[$(this).data("unidade")] + ' <small>L</small>');
                $(this).find('.total-unidade').html(json.extra.units.total[$(this).data("unidade")] + ' <small>L</small>');
                $(this).find('.status').children().remove();
                $(this).find('.status').append('<i class="card-body-online float-right fas fa-circle status" title=""></i>');
                $(this).find('.status').children().addClass(json.extra.units.status[$(this).data("unidade")])
            });

            if (json.hasOwnProperty('extra')) {
                if (json.extra.hasOwnProperty('footer')) {
                    el.parent().parent().parent().children().remove(".card-footer");
                    el.parent().parent().parent().append(json.extra.footer);
                }
            }

            if (chart) {
                chart.updateOptions(json);
            } else {
                chart = new ApexCharts(el[0], json);
                chart.render();
            }
        });
    }

    var timestampListener = function (event) {

        var atual = event.data;

        evtSource.removeEventListener('chart', chartListener);
        evtSource.removeEventListener('timestamp', timestampListener);
        evtSource.removeEventListener('alertas', alertListener);

        evtSource.close();
        evtSource = null;

        setTimeout(function () {

            evtSource = new EventSource('sse/event?atual=' + atual + '&last=' + last);

            evtSource.addEventListener('chart', chartListener, false);
            evtSource.addEventListener('timestamp', timestampListener, false);
            evtSource.addEventListener('alertas', alertListener, false);

        }, 15000);

    }

    evtSource.addEventListener('chart', chartListener, false);
    evtSource.addEventListener('timestamp', timestampListener, false);
    evtSource.addEventListener('alertas', alertListener, false);

    $(".flip").flip({
        trigger: 'manual'
    });

    setInterval( function () {
        $(".flip").each(function(i){
            let el = $(this);
            setTimeout(function () {
                el.flip('toggle');
            }, 100 * i);
        });
    }, 30000 );

}.apply(this, [jQuery]));