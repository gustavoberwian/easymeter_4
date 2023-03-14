(function ($) {

    "use strict";

    let dtPonta = $("#dt-ponta").DataTable({
        dom: '<"table-responsive"t>r',
        processing: true,
        paging: true,
        columns: [
            {data: "id", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "value", className: "dt-body-center"},
            {data: "percentage", className: "dt-body-center"},
            {data: "participation", className: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        sorting: [],
        pagingType: "numbers",
        pageLength: 10,
        ajax: {
            type: 'POST',
            url: "/energia/insights/1",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#" + this.api().context[0].sTableId).dataTable().fnProcessingIndicator(false);
                $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    dtPonta.on( 'draw.dt', function () {
        var info = $('#dt-ponta').DataTable().page.info();
        dtPonta.column(0, {page: 'current'}).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

    let dtFora = $("#dt-fora").DataTable({
        dom: '<"table-responsive"t>r',
        processing: true,
        paging: true,
        columns: [
            {data: "id", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "value", className: "dt-body-center"},
            {data: "percentage", className: "dt-body-center"},
            {data: "participation", className: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        ajax: {
            type: 'POST',
            url: "/energia/insights/2",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#" + this.api().context[0].sTableId).dataTable().fnProcessingIndicator(false);
                $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    dtFora.on( 'draw.dt', function () {
        var info = $('#dt-fora').DataTable().page.info();
        dtFora.column(0, {page: 'current'}).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

    let dtOpen = $("#dt-open").DataTable({
        dom: '<"table-responsive"t>r',
        processing: true,
        paging: true,
        columns: [
            {data: "id", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "value", className: "dt-body-center"},
            {data: "percentage", className: "dt-body-center"},
            {data: "participation", className: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        ajax: {
            type: 'POST',
            url: "/energia/insights/3",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#" + this.api().context[0].sTableId).dataTable().fnProcessingIndicator(false);
                $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    dtOpen.on( 'draw.dt', function () {
        var info = $('#dt-open').DataTable().page.info();
        dtOpen.column(0, {page: 'current'}).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

    let dtClose = $("#dt-close").DataTable({
        dom: '<"table-responsive"t>r',
        processing: true,
        paging: true,
        columns: [
            {data: "id", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "value", className: "dt-body-center"},
            {data: "percentage", className: "dt-body-center"},
            {data: "participation", className: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        ajax: {
            type: 'POST',
            url: "/energia/insights/4",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#" + this.api().context[0].sTableId).dataTable().fnProcessingIndicator(false);
                $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    dtClose.on( 'draw.dt', function () {
        var info = $('#dt-close').DataTable().page.info();
        dtClose.column(0, {page: 'current'}).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

    let dtCarbon = $("#dt-carbon").DataTable({
        dom: '<"table-responsive"t>r',
        processing: true,
        paging: true,
        columns: [
            {data: "id", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "value", className: "dt-body-center"},
            {data: "percentage", className: "dt-body-center"},
            {data: "participation", className: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        ajax: {
            type: 'POST',
            url: "/energia/insights/5",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#" + this.api().context[0].sTableId).dataTable().fnProcessingIndicator(false);
                $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    dtCarbon.on( 'draw.dt', function () {
        var info = $('#dt-carbon').DataTable().page.info();
        dtCarbon.column(0, {page: 'current'}).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

    let dtFactor = $("#dt-factor").DataTable({
        dom: '<"table-responsive"t>r',
        processing: true,
        paging: true,
        columns: [
            {data: "id", className: "dt-body-center"},
            {data: "name", className: "dt-body-left"},
            {data: "value", className: "dt-body-center"},
            {data: "type", className: "dt-body-center"},
        ],
        serverSide: true,
        ordering: false,
        ajax: {
            type: 'POST',
            url: "/energia/insights/6",
            error: function () {
                notifyError(
                    "Ocorreu um erro no servidor. Por favor tente novamente em alguns instantes."
                );
                $("#" + this.api().context[0].sTableId).dataTable().fnProcessingIndicator(false);
                $("#" + this.api().context[0].sTableId + "_wrapper .table-responsive").removeClass("processing");
            },
        },
    });

    dtFactor.on( 'draw.dt', function () {
        var info = $('#dt-factor').DataTable().page.info();
        dtFactor.column(0, {page: 'current'}).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

}.apply(this, [jQuery]));
