<section role="main" class="content-body">
    <header class="page-header">
        <h2>Bauducco CD Extrema/MG</h2>
    </header>

    <img src="/assets/img/bauducco.png" height="100" alt="Bauducco" class="mb-4">

    <ul class="nav nav-pills nav-pills-primary mb-2">
        <li class="nav-item">
            <button class="nav-link active me-3" data-bs-toggle="tab" data-bs-target="#agua" type="button"><i
                        class="fas fa-tint"></i> Água
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pocos" type="button"><i
                        class="fas fa-database"></i> Poços
            </button>
        </li>
    </ul>

    <div class="tab-content my-4" style="background-color: transparent;box-shadow: none;padding: 0;">

        <div class="tab-pane active" id="agua">

            <div class="row mb-3">

                <div class="col-lg-4 mt-3 mt-lg-0">
                    <section class="card card-comparativo h-100 h-100">
                        <div class="card-body" style="background-color: #03aeef;">
                            <h6 class="card-body-title mb-3 mt-0 text-light">Período <i
                                        class="float-end fas fa-calendar"></i></h6>
                            <div id="daterange" class="btn btn-light w-100 overflow-hidden text-muted daterange agua">
                                <i class="fa fa-calendar"></i>&nbsp;<span></span>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-lg-4 mt-3 mt-lg-0">
                    <section class="card card-comparativo mb-4 h-100">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title mb-2 mt-0 text-primary">Consumo do Período</span></h6>
                            <div class="row">
                                <div class="col">
                                    <div class="h5 mb-0 mt-1" id="total">0</div>
                                    <p class="text-3 text-muted mb-0">Total do período</p>
                                </div>
                                <div class="col">
                                    <div class="h5 mb-0 mt-1" id="average">0</div>
                                    <p class="text-3 text-muted mb-0">Média <span id="label">dia</span></p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-lg-4 mt-3 mt-lg-0 order-lg-3">
                    <section class="card card-previsao mb-4 h-100">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title mb-2 mt-0 text-light">Previsão do mês atual</h6>
                            <div class="row">
                                <div class="col">
                                    <div class="h5 mb-0 mt-1 text-white consumo-mes"><?= $mes; ?></div>
                                    <p class="text-3 text-white mb-0">Até agora</p>
                                </div>
                                <div class="col">
                                    <div class="h5 mb-0 mt-1 text-white previsao"><?= $previsao; ?></div>
                                    <p class="text-3 text-white mb-0">Previsão</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <?php foreach ($medidores as $m): ?>
                <section class="card card-chart card-easymeter" id="card-<?= $m->id; ?>">
                    <header class="card-header">
                        <h2 class="card-title mt-2 mt-lg-0">Consumo <?= $m->unidade; ?></h2>
                    </header>
                    <div class="card-body chart-body" data-loading-overlay
                         data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                        <div class="chart-container agua" data-mid="<?= $m->id; ?>">
                            <div id="chart-<?= $m->id; ?>" data-monitoramento="agua"></div>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="tab-pane" id="pocos">
            <div class="row mb-4">
                <?php foreach ($pocos as $p): ?>
                    <div class="col-md-6">
                        <section class="card card-leitura card-easymeter">
                            <header class="card-header">
                                <h2 class="card-title mt-2 mt-lg-0">Poço <?= $p['unidade']; ?></h2>
                            </header>
                            <div class="card-body">
                                <h6 class="card-body-title mb-2 mt-0 text-primary">Nível Dinâmico</h6>
                                <h2 class="mt-0 mb-2 leitura-agua unidade"><?= $p['dinamico']; ?><small>m</small></h2>
                                <hr class="solid short mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="h5 mb-0 mt-3"><?= $p['estatico']; ?> <small>m</small></div>
                                        <p class="text-3 text-muted mb-0">Nível Estático</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="h5 mb-0 mt-3"><?= $p['minimo']; ?> <small>m</small></div>
                                        <p class="text-3 text-muted mb-0">Nivel Mínimo</p>
                                    </div>
                                </div>
                                <hr class="solid short mt-3">
                                <div class="reservatorio" id="reservatorio-<?= $p['id']; ?>"
                                     data-volume="<?= number_format($p['tank'], 1, '.', '.'); ?>"
                                     style="padding-bottom: 80px !important; padding-top: 20px !important; "></div>
                            </div>
                        </section>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php foreach ($pocos as $p): ?>
                <section class="card card-chart card-easymeter" id="card-<?= $p['id']; ?>">
                    <header class="card-header">
                        <div class="card-actions buttons">
                            <div id="daterange-nivel-<?= $p['id']; ?>" class="btn btn-primary daterange-nivel">
                                <i class="fa fa-calendar"></i>&nbsp;<span></span>
                            </div>
                        </div>
                        <h2 class="card-title mt-2 mt-lg-0">Volume <?= $p['unidade']; ?></h2>
                    </header>
                    <div class="card-body chart-body" data-loading-overlay
                         data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                        <div class="chart-container nivel" data-mid="<?= $p['id']; ?>">
                            <div id="chart-<?= $p['id']; ?>" data-monitoramento="nivel"></div>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>

        </div>
    </div>

</section>
