<section role="main" class="content-body">

    <header class="page-header" data-group="<?= $group_id; ?>" <?= $user->inGroup('shopping', 'unity') ? 'data-device="'.$unidade->device.'"' : '' ?>">
        <?php if ($user->inGroup('shopping', 'unity')): ?>
            <h2><?= $unidade->nome; ?></h2>
        <?php else: ?>
            <h2><?= $group->group_name; ?></h2>
        <?php endif; ?>
    </header>

    <img src="<?php echo base_url('assets/img/logo-north.png'); ?>" alt="<?= "North"; ?>" class="mb-4" height="80"/>

    <?php if (empty($user->config)) : ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Atenção!</strong> Configurações gerais do shopping não fornecidas. <a href="/shopping/configuracoes/<?=$group_id ?>" class="alert-link">Clique aqui</a> e configure-os para visualizar os dados corretamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-pills nav-pills-primary mb-3">
        <?php if (!$user->inGroup("unity", "shopping")): ?>
            <li class="nav-item configs" role="presentation">
                <button class="nav-link configs left active" data-bs-toggle="pill" data-bs-target="#resume" type="button">Resumo</button>
            </li>
        <?php endif; ?>
        <li class="nav-item configs" role="presentation">
            <button class="nav-link configs <?= $user->inGroup("unity_shopping") ? 'left active' : '' ?>" data-bs-toggle="pill" data-bs-target="#charts" type="button">Medição</button>
        </li>
        <?php if (!$user->inGroup("unity_shopping") || $permission->acessar_engenharia): ?>
            <li class="nav-item configs" role="presentation">
                <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#engineering" type="button">Engenharia</button>
            </li>
        <?php endif; ?>
        <li class="nav-item configs" role="presentation">
            <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#analysis" type="button">Análises</button>
        </li>
        <li class="nav-item configs" role="presentation">
            <button class="nav-link configs right" data-bs-toggle="pill" data-bs-target="#data" type="button">Dados</button>
        </li>

    </ul>

    <div class="tab-content" style="background-color: transparent; box-shadow: none; padding: 0;">

        <div class="row pt-0 selector" <?= !$user->inGroup("unity_shopping") ? 'style="display: none;"' : '' ?>>

            <div class="<?= $user->inGroup("unity_shopping") ? 'col-md-4' : 'col-md-2' ?> mb-4">
                <section class="card card-comparativo h-100">
                    <div class="card-body">
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Leitura Atual</h6>
                        <div class="row">
                            <div class="h5 mb-0 mt-1"><span class="main">-</span></div>
                        </div>
                    </div>
                </section>
            </div>

            <?php if (!$user->inGroup("unity_shopping")): ?>
                <div class="col-md-2 mb-4">
                    <section class="card card-comparativo h-100 h-100">
                        <div class="card-body" style="background-color: #03aeef;">
                            <h6 class="card-body-title mb-3 mt-0 text-light">Medidor <i class="float-end fas fa-microchip"></i></h6>
                            <select class="form-control" name="sel-device" id="sel-device">
                                <optgroup label="Tipo">
                                    <option value="C"><?= $area_comum; ?></option>
                                    <option value="U">Unidades</option>
                                <optgroup label="Medidores">
                                <?php foreach ($unidades as $u) { ?>
                                    <option value="<?= $u["medidor_id"] ?>"><?= $u["unidade_nome"]; ?></option>
                                <?php } ?>
                                <optgroup label="Agrupamentos">
                                <?php foreach ($device_groups as $u) { ?>
                                    <option value="<?= $u["id"] ?>"><?= $u["name"]; ?></option>
                                <?php } ?>
                                <!--<option value="href" data-url="<?/*= site_url("shopping/configuracoes/" . $group_id) */?>" class="text-primary">&#x2b; Criar Agrupamento</option>-->
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
                                <div class="h5 mb-0 mt-1"><span class="day">-</span></div>
                                <p class="text-3 text-muted mb-0">Consumo</p>
                            </div>
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="day-f" style="color: #268ec3;">-</span></div>
                                <p class="text-3 text-muted mb-0">Fora Ponta</p>
                            </div>
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="day-p" style="color: #ff6178;">-</span></div>
                                <p class="text-3 text-muted mb-0">Ponta</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>

        <div class="row pt-0 consumption" <?= !$user->inGroup("unity_shopping") ? 'style="display: none;"' : '' ?>>

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
                        <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo Fora da Ponta</h6>
                        <div class="row">
                            <div class="col-lg-4 pr-1">
                                <div class="h5 mb-0 mt-1"><span class="period-f">-</span></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="month-f">-</span></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="prevision-f">-</span></div>
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
                                <div class="h5 mb-0 mt-1"><span class="period-p">-</span></div>
                                <p class="text-3 text-muted mb-0">Período selecionado</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="month-p">-</span></div>
                                <p class="text-3 text-muted mb-0">No mês atual</p>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <div class="h5 mb-0 mt-1"><span class="prevision-p">-</span></div>
                                <p class="text-3 text-muted mb-0">Previsão no mês</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>

        <?php if (!$user->inGroup("unity_shopping")): ?>
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
                        <table class="table table-bordered table-striped table-hover table-click" id="dt-resume" style="min-height: 300px;">
                            <thead>
                            <tr role="row">
                                <th rowspan="2">Medidor</th>
                                <th rowspan="2">LUC</th>
                                <th rowspan="2">Nome</th>
                                <th rowspan="2">Tipo</th>
                                <th rowspan="2">Leitura - kWh</th>
                                <th colspan="7" class="text-center">Consumo - kWh</th>
                            </tr>
                            <tr role="row">
                                <th>Mês</th>
                                <th>Aberto</th>
                                <th>Fechado</th>
                                <th>Ponta</th>
                                <th>Fora Ponta</th>
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

        <div class="tab-pane fade <?= $user->inGroup("unity_shopping") ? 'show active' : '' ?>" id="charts">

            <div class="row pt-0">

                <div class="col-md-8 mb-4">
                    <section class="card card-easymeter h-100 mb-4">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Consumo Energia Ativa" data-bs-content="Energia real consumida pela carga (iluminação, ar-condicionado e equipamentos em geral) sendo o principal fator utilizado para conversões monetárias."><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Consumo</h2>
                        </header>
                        <div class="card-body chart_activePositive-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div class="chart-main" data-field="mainActivePositive"></div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-md-4 mb-4">
                    <section class="card card-easymeter h-100 mb-4">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Consumo Por Posto" data-bs-content="Postos tarifários são períodos do dia em que as tarifas de energia são diferentes. São divididos em: PONTA (horário de maior consumo da região), FORA PONTA e, em alguns casos, INTERMEDIÁRIO."><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Consumo por Posto</h2>
                        </header>
                        <div class="card-body chart_station-body d-flex align-items-center" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div class="chart-main" data-field="mainStation"></div>
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
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Demanda de energia ativa" data-bs-content="Máxima potência ativa consumida em um determinado período (normalmente dentro de 15min, mas varia entre concessionárias de energia). A rede de transmissão e o contrato de fornecimento de energia devem ser dimensionados considerando-se este valor. O subdimensionamento pode causar danos e acidentes e o superdimensionamento gera cobranças extras em favor da concessionária"><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Demandas</h2>
                        </header>
                        <div class="card-body chart_demand-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div class="chart-main" data-field="mainActiveDemand"></div>
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
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Energia Reativa" data-bs-content="Refere-se àquela parte da energia que não realiza diretamente trabalho. Tem origem, por exemplo, na formação de campos magnéticos de alguns equipamentos como motores, transformadores e bobinas. As concessionárias cobram multas pelo excesso deste consumo."><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Energia Reativa</h2>
                        </header>
                        <div class="card-body chart_reactiv-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div class="chart-main" data-field="mainReactive"></div>
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
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Fator de Potência" data-bs-content="Resultado da relação entre o consumo da energia ativa e da reativa. É o fator de potência que determina a aplicação de multas quando os valores fogem dos limites estipulados (por padrão deve ficar entre 0,92 e 1). Quanto mais próximo de 1, melhor."><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Fator de Potência</h2>
                        </header>
                        <div class="card-body chart_factor1-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div class="chart-main" data-field="mainFactor"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!--<div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100 mb-4">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Popover title" data-bs-content="And here's some amazing content. It's very engaging. Right?"><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Mapa Calor Potência</h2>
                        </header>
                        <div class="card-body chart_heat_map_power-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div id="chart_heat_map_power" data-field="chart_heat_map_power" style="width: 100% !important; max-height: 100%; min-height: unset"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>-->

            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100 mb-4">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Fator de Carga" data-bs-content="Determina a eficiência energética da instalação. Pode ser calculado de duas formas, mas aqui é o resultado da razão entre a potência média e máxima consumidas no intervalo de tempo estipulado. Quanto mais próximo de 1, melhor."><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Fator de Carga</h2>
                        </header>
                        <div class="card-body chart_profile-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div class="chart-main" data-field="mainLoad"></div>
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
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Emissão de GEE" data-bs-content="Representação do volume total de gases de efeito estufa (GEE) gerado pelo consumo de energia ativa."><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Pegada de Carbono </h2>
                        </header>
                        <div class="card-body chart_carbon1-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container">
                                <div class="chart-main" data-field="mainCarbon"></div>
                            </div>
                        </div>
                    </section>
                    <h3></h3>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="analysis">

            <section class="card card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <?php if ($permission->baixar_planilhas) : ?>
                            <button type="button" class="btn btn-primary btn-download-abnormal me-2" data-group="<?= $group_id; ?>" data-loading-overlay disabled><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Lista de Anomalias" data-bs-content="Aqui é possível identificar anomalias ocorridas no sistema a partir de parâmetros estipulados pelo usuário"><i class="float-end fas fa-info-circle"></i></button>
                    </div>
                    <h2 class="card-title">Análise de Anomalias</h2>
                </header>
                <div class="card-body chart_abnormal-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>

                    <form class="row gx-3 gy-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="control-label">Grandeza:</label>
                            <select class="form-control mb-3 type">
                                <option value="" disabled selected>Selecione</option>
                                <option value="voltage" data-min="209" data-max="231">Tensão</option>
                                <option value="current" data-min="9" data-max="10">Corrente</option>
                                <option value="active">Potência ativa instantânea</option>
                                <option value="reactive">Potência reativa instantânea</option>
                                <option value="activePositiveConsumption">Consumo</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="control-label">Valores Válidos:</label>
                            <div class="row mb-2">
                                <div class="col-lg-4">
                                    <input type="text" placeholder="Mínimo" class="form-control mb-2" name="min" id="min" value="">
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" placeholder="Máximo" class="form-control mb-2" name="max"  id="max" value="">
                                </div>
                                <div class="col-lg-4">
                                    <button type="button" class="btn btn-primary btn-view" disabled>Visualizar</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered table-striped table-hover" id="dt-abnormal" data-buttons="<?= $permission->baixar_planilhas ? 'true' : 'false' ?>">
                        <thead>
                        <tr role="row">
                            <th rowspan="2">Data</th>
                            <th colspan="3">Tensão (V)</th>
                            <th colspan="3">Corrente (A)</th>
                            <th colspan="3">Potência Ativa Instantânea (kW)</th>
                            <th colspan="3">Potência Reativa Instantânea (kVAr)</th>
                            <th rowspan="2">Consumo (Wh)</th>
                        </tr>
                        <tr role="row">
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <?php if (!$user->inGroup("unity_shopping") || $permission->acessar_engenharia): ?>
            <div class="tab-pane fade" id="engineering">

                <section class="card card-easymeter mb-4">
                    <header class="card-header">
                        <div class="card-actions buttons">
                        </div>
                        <h2 class="card-title">Potência Ativa Instantânea</h2>
                    </header>
                    <div class="card-body chart_active-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                        <div class="chart-container">
                            <div class="chart-main" data-field="active"></div>
                        </div>
                    </div>
                </section>

                <div class="row pt-0">
                    <div class="col-md-6 mb-4">
                        <section class="card card-easymeter h-100 mb-4">
                            <header class="card-header">
                                <div class="card-actions buttons">
                                </div>
                                <h2 class="card-title">Corrente Média</h2>
                            </header>
                            <div class="card-body chart_current-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <div class="chart-main" data-field="current"></div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="col-md-6 mb-4">
                        <section class="card card-easymeter h-100 mb-4">
                            <header class="card-header">
                                <div class="card-actions buttons">
                                </div>
                                <h2 class="card-title">Tensão Média</h2>
                            </header>
                            <div class="card-body chart_voltage-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <div class="chart-main" data-field="voltage"></div>
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
                                </div>
                                <h2 class="card-title">Potência Máxima Instantânea</h2>
                            </header>
                            <div class="card-body chart_activedemand-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <div class="chart-main" data-field="power"></div>
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
                                </div>
                                <h2 class="card-title">Fator de Carga Instantâneo</h2>
                            </header>
                            <div class="card-body chart-factor-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <div class="chart-main" data-field="load"></div>
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
                                </div>
                                <h2 class="card-title">Energia Reativa Instantânea</h2>
                            </header>
                            <div class="card-body chart-reactive-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <div class="chart-main" data-field="reactive"></div>
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
                                </div>
                                <h2 class="card-title">Fator de Potência Instantâneo</h2>
                            </header>
                            <div class="card-body chart-factor-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <div class="chart-main" data-field="factor"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="tab-pane fade" id="data">

            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100 mb-4">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Consumo Últimas 24h" data-bs-content="Consumo de energia ativa consumida a cada 10 min nas últimas 24h."><i class="float-end fas fa-info-circle"></i></button>
                            </div>
                            <h2 class="card-title">Consumo Últimas 24h</h2>
                        </header>
                        <div class="card-body chart_consumption-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                            <div class="chart-container" style="height: 200px;">
                                <div class="chart-main" data-field="consumption"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <section class="card card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <button type="button" class="btn btn-lg btn-primary" data-bs-trigger="focus" data-bs-toggle="popover" title="Dados" data-bs-content="Lista dos dados enviados pelos equipamentos de medição Easymeter instalados."><i class="float-end fas fa-info-circle"></i></button>
                    </div>
                    <h2 class="card-title">Dados</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="dt-data">
                        <thead>
                        <tr role="row">
                            <th rowspan="2">Data</th>
                            <th rowspan="2">Leitura (kWh)</th>
                            <th colspan="3">Tensão (V)</th>
                            <th colspan="3">Corrente (A)</th>
                            <th colspan="3">Potência Ativa Instantânea (kW)</th>
                            <th colspan="3">Potência Reativa Instantânea (kVAr)</th>
                            <th rowspan="2">Consumo (Wh)</th>
                        </tr>
                        <tr role="row">
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                            <th>Fase R</th>
                            <th>Fase S</th>
                            <th>Fase T</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </section>
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