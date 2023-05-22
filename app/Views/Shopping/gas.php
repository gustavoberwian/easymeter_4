<section role="main" class="content-body">

    <header class="page-header" data-group="<?= $group_id; ?>" <?= $user->inGroup('shopping', 'unity') ? 'data-device="'.$unidade->device.'"' : '' ?> >
        <?php if ($user->inGroup('shopping', 'unity')): ?>
            <h2><?= $unidade->nome; ?></h2>
        <?php else: ?>
            <h2><?= $group->group_name; ?></h2>
        <?php endif; ?>
    </header>

    <div class="row">
        <div class="col-6">
        <button class="btn btn-light me-4" id='btn-back-last' data-bs-toggle="" data-bs-target="#back" type="button"><i class="fas fa-arrow-left"></i> Voltar</button>
            <ul class="nav nav-pills nav-pills-primary mb-3">
                <?php if (!$user->inGroup("unity", "shopping")): ?>
                    <li class="nav-item configs" role="presentation">
                        <button class="nav-link configs left active" data-bs-toggle="pill" data-bs-target="#resume" type="button">Resumo</button>
                    </li>
                <?php endif; ?>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs <?= $user->inGroup("unity") ? 'left active' : '' ?>" data-bs-toggle="pill" data-bs-target="#charts" type="button">Medição</button>
                </li>
            </ul>
        </div>
        <div class="col-6 text-end">
            <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" alt="<?= ""; ?>" class="mb-3" height="50"/>
        </div>
    </div>

    <div class="tab-content" style="background-color: transparent; box-shadow: none; padding: 0; border: none">

        <div class="row pt-0 selector" <?= !$user->inGroup("unity") ? 'style="display: none;"' : '' ?>>

            <div class="<?= $user->inGroup("unity") ? 'col-md-4' : 'col-md-2' ?> mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Leitura Atual</h6>
                        <div class="row">
                            <div class="h5 mb-0 mt-1"><?= ($user->demo) ? "<span>" . number_format(mt_rand(10000, 100000), 0, ',', '.') . "</span> <span style='font-size:12px;'>m³</span>" : "<span class='main'>-</span>" ?></div>
                        </div>
                    </div>
                </section>
            </div>

            <?php if (!$user->inGroup("unity")): ?>
                <div class="col-md-2 mb-4">
                    <section class="card card-comparativo h-100 h-100">
                        <div class="card-body" style="background-color: #03aeef;">
                            <h6 class="card-body-title mb-3 mt-0 text-light"><label for="sel-device">Medidor</label><i class="float-end fas fa-microchip"></i></h6>
                            <select class="form-control" name="sel-device" id="sel-device">
                                <optgroup label="Tipo">
                                    <option value="C"><?= $area_comum; ?></option>
                                    <option value="U">Unidades</option>
                                </optgroup>
                                <optgroup label="Medidores">
                                    <?php foreach ($unidades as $u) : ?>
                                        <option value="<?= $u["medidor_id"] ?>"><?= $u["unidade_nome"]; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Agrupamentos">
                                    <?php foreach ($device_groups as $u) : ?>
                                        <option value="<?= $u["id"] ?>"><?= $u["name"]; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>
                    </section>
                </div>
            <?php endif; ?>

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
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? '<span>' . number_format(mt_rand(1000, 10000), 0, ',', '.') . "</span> <span style='font-size:12px;'>m³</span>" : "<span class='day'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Consumo</p>
                            </div>
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? '<span style="color: #268ec3;">' . number_format(mt_rand(1000, 10000), 0, ',', '.') . "</span> <span style='font-size:12px;'>m³</span>" : "<span class='day-f'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Fora Ponta</p>
                            </div>
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? '<span style="color: #ff6178;">' . number_format(mt_rand(1000, 10000), 0, ',', '.') . "</span> <span style='font-size:12px;'>m³</span>" : "<span class='day-p'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Ponta</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>

        <div class="row pt-0 consumption" <?= !$user->inGroup("unity") ? 'style="display: none;"' : '' ?>>

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Total</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='period'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='month'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='prevision'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Previsão no mês</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Fora da Ponta</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='period-f'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='month-f'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='prevision-f'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Previsão no mês</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-md-4 mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Ponta</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "-<span class='period-p'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='month-p'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><?= ($user->demo) ? number_format(mt_rand(1000, 10000), 0, ',', '.') . " <span style='font-size:12px;'>m³</span>" : "<span class='prevision-p'>-</span>" ?></div>
                                <p class="text-3 text-muted mb-0">Previsão no mês</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>

        <?php if (!$user->inGroup("unity")): ?>
            <div class="tab-pane fade show active" id="resume">

                <section class="card card-easymeter mb-4">
                    <header class="card-header">
                        <div class="card-actions buttons">
                            <?php if (!empty($unidades)) : ?>
                                <button class="btn btn-primary btn-download" data-group="<?= $group_id; ?>" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
                            <?php endif; ?>
                        </div>
                        <h2 class="card-title">Resumo do Mês</h2>
                    </header>
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover table-click" id="dt-resume" data-url="<?= "/gas/resume" ?>" style="min-height: 300px;">
                            <thead>
                            <tr role="row">
                                <th rowspan="2">Medidor</th>
                                <th rowspan="2">LUC</th>
                                <th rowspan="2">Nome</th>
                                <th rowspan="2">Tipo</th>
                                <th rowspan="2">Leitura - m³</th>
                                <th colspan="5" class="text-center">Consumo - m³</th>
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
        <?php endif; ?>

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

</section>