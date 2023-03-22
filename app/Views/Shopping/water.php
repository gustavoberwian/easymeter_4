<section role="main" class="content-body" data-group="<?=$group_id ?>">

    <header class="page-header" <?= $user->inGroup('shopping', 'unity') ? 'data-device="'.$unidade->device.'"' : '' ?>">
        <?php if ($user->inGroup('shopping', 'unity')): ?>
            <h2><?= $unidade->nome; ?> - Água</h2>
        <?php else: ?>
            <h2><?= $group->group_name; ?> - Água</h2>
        <?php endif; ?>
    </header>

    <img src="<?php echo base_url('assets/img/logo-north.png'); ?>" alt="<?= "North"; ?>" class="mb-4" height="80"/>

    <ul class="nav nav-pills nav-pills-primary mb-3">
        <li class="nav-item configs" role="presentation">
            <button class="nav-link configs left active" data-bs-toggle="pill" data-bs-target="#resume" type="button">Resumo</button>
        </li>
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link configs right" data-bs-toggle="pill" data-bs-target="#charts" type="button">Medição</button>
        </li>
    </ul>

    <div class="tab-content" style="background-color: transparent; box-shadow: none; padding: 0;">

        <div class="row pt-0 selector">

            <div class="col-md-2 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Leitura Atual</h6>
                        <div class="row">
                            <div class="h5 mb-0 mt-1"><span class="main">-</span></div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-md-2 mb-4">
                <section class="card card-comparativo h-100 h-100">
                    <div class="card-body" style="background-color: #03aeef;">
                        <h6 class="card-body-title mb-3 mt-0 text-light">Medidor <i class="float-end fas fa-microchip"></i></h6>
                        <select class="form-control" name="sel-device" id="sel-device">
                            <option value="T">Todos</option>
                            <?php foreach ($unidades as $u) { ?>
                                <option value="<?= $u["medidor_id"] ?>"><?= $u["unidade_nome"]; ?></option>
                            <?php } ?>
                            <?php if ($device_groups) { ?>
                                <optgroup label="Agrupamentos">
                                <?php foreach ($device_groups as $u) { ?>
                                    <option value="<?= $u["id"] ?>"><?= $u["name"]; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </section>
            </div>

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100 h-100">
                    <div class="card-body" style="background-color: #03aeef;">
                        <h6 class="card-body-title mb-3 mt-0 text-light">Período <i class="float-end fas fa-calendar"></i></h6>
                        <div id="daterange-main" class="btn btn-light w-100 overflow-hidden" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <span></span>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Média Diária Nos últimos 30 dias</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="day">-</span></div>
                                <p class="text-3 text-muted mb-0">Consumo</p>
                            </div>
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="day-o">-</span></div>
                                <p class="text-3 text-muted mb-0">Shopping Aberto</p>
                            </div>
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="day-c">-</span></div>
                                <p class="text-3 text-muted mb-0">Shopping Fechado</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            
        </div>

        <div class="row pt-0 consumption">

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Total</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="period">-</span></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="month">-</span></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="prevision">-</span></div>
                                <p class="text-3 text-muted mb-0">Previsão no mês</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Shopping Aberto</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="period-o">-</span></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="month-o">-</span></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="prevision-o">-</span></div>
                                <p class="text-3 text-muted mb-0">Previsão no mês</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Shopping Fechado</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="period-c">-</span></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="month-c">-</span></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="prevision-c">-</span></div>
                                <p class="text-3 text-muted mb-0">Previsão no mês</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>

        <div class="tab-pane fade show active" id="resume">

            <section class="card card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <button class="btn btn-primary btn-download" data-group="<?= $group_id; ?>" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
                    </div>
                    <h2 class="card-title">Resumo do Mês</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-click" id="dt-resume" style="min-height: 300px;">
                        <thead>
                        <tr role="row">
                            <th rowspan="2">Medidor</th>
                            <th rowspan="2">LUC</th>
                            <th rowspan="2">Nome</th>
                            <th rowspan="2">Tipo</th>
                            <th rowspan="2">Leitura - M³</th>
                            <th colspan="5" class="text-center">Consumo - L</th>
                        </tr>
                        <tr role="row">
                            <th>Mês</th>
                            <th>Aberto</th>
                            <th>Fechado</th>
                            <th>Últimas 24h</th>
                            <th>Previsão Mês</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="tab-pane fade" id="charts">

            <div class="row pt-0">

                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100 mb-4">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <select data-plugin-selectTwo class="form-control populate placeholder" id="compare" data-plugin-options='{ "placeholder": "Comparar", "allowClear": true }' style="width: 150px">
                                    <option></option>
                                    <?php foreach ($unidades as $u) { ?>
                                        <option value="<?= $u["medidor_id"] ?>"><?= $u["unidade_nome"]; ?></option>
                                    <?php } ?>
                                </select>
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
        </div>
    </div>

    <div class="mt-3">
        <table class="text-dark w-100">
            <tbody><tr>
                <td>
                    <img src="<?php echo base_url('assets/img/logo-ancar.png'); ?>" alt="<?= "Ancar"; ?>" class="mb-4" height="35"/>
                </td>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="35"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</section>