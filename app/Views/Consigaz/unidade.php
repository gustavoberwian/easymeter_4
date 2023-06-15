<section role="main" class="content-body">

    <header class="page-header" data-medidor="<?= $unidade->medidor_id ?>">
        <h2><?= $unidade->agrupamento_nome . ' / ' . $unidade->unidade_nome . ' - Gás'; ?></h2>
    </header>

    <div class="row pt-0">

        <div class="col-md-2 mb-4">
            <section class="card card-comparativo h-100">
                <div class="card-body">
                    <h6 class="card-body-title mb-3 mt-0 text-primary">Leitura Atual</h6>
                    <div class="row">
                        <div class="h5 mb-0 mt-1"><?= number_format($leitura_atual, 0, '', ''); ?> <small>m³</small></div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-md-6 mb-4">
            <section class="card card-comparativo h-100">
                <div class="card-body">
                    <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Total</h6>
                    <div class="row">
                        <div class="col-lg-4 pr-1">
                            <div class="h5 mb-0 mt-1"><?= "<span class='period'>-</span>" ?></div>
                            <p class="text-3 text-muted mb-0">Período selecionado</p>
                        </div>
                        <div class="col-lg-4 pl-1">
                            <div class="h5 mb-0 mt-1"><?= "<span class='month'>-</span>" ?></div>
                            <p class="text-3 text-muted mb-0">No mês atual</p>
                        </div>
                        <div class="col-lg-4 pl-1">
                            <div class="h5 mb-0 mt-1"><?= "<span class='prevision'>-</span>" ?></div>
                            <p class="text-3 text-muted mb-0">Previsão no mês</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-md-4 mb-4">
            <section class="card card-comparativo h-100">
                <div class="card-body" style="background-color: #03aeef;">
                    <h6 class="card-body-title mb-3 mt-0 text-light">Período <i class="float-end fas fa-calendar"></i></h6>
                    <div id="daterange-main" class="btn btn-light w-100 overflow-hidden" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                        <span></span>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <div class="row pt-0">

        <div class="col-md-12 mb-4">
            <section class="card card-easymeter h-100 mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <button type="button" class="btn btn-primary btn-sheet-consumo"><i class="fas fa-file-download"></i> Gerar Planilha</button>
                    </div>
                    <h2 class="card-title">Consumo</h2>
                </header>
                <div class="card-body chart_activePositive-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                    <div class="chart-container">
                        <div class="chart-main" data-field="consumption"></div>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <div class="row pt-0">

        <div class="col-md-12 mb-4">
            <section class="card card-easymeter h-100 mb-4">
                <header class="card-header">
                    <h2 class="card-title">Nível de Bateria</h2>
                </header>
                <div class="card-body chart_battery-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                    <div class="chart-container">
                        <div class="chart-main" data-field="battery"></div>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <div class="row pt-0">

        <div class="col-md-12 mb-4">
            <section class="card card-easymeter h-100 mb-4">
                <header class="card-header">
                    <h2 class="card-title">Sensor</h2>
                </header>
                <div class="card-body chart_sensor-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                    <div class="chart-container">
                        <div class="chart-main" data-field="sensor"></div>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <div>

        <table class="text-dark w-100">
            <tbody>
                <tr>
                    <td class="text-end">
                        <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="30"/>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

</section>