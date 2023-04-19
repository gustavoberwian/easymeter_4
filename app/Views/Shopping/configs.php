<section role="main" class="content-body">
    <!-- start: page -->

    <header class="page-header" data-group="<?= $group_id ?>">
        <h2><?= $group->group_name; ?></h2>
    </header>

    <div class="row">
        <div class="col-8">
            <ul class="nav nav-pills nav-pills-primary mb-3" role="tablist">
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs left active" data-bs-toggle="pill" data-bs-target="#geral" type="button" aria-selected="true" role="tab">Geral</button>
                </li>
                <li class="nav-item configs d-none" role="presentation">
                    <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#usuarios" type="button" aria-selected="false" role="tab" tabindex="-1">Usuários</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#unidades" type="button" aria-selected="false" role="tab" tabindex="-1">Unidades</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#agrupamentos" type="button" aria-selected="false" tabindex="-1" role="tab">Agrupamentos</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#alertas" type="button" aria-selected="false" tabindex="-1" role="tab">Alertas</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs right" data-bs-toggle="pill" data-bs-target="#api" type="button" aria-selected="false" tabindex="-1" role="tab">API</button>
                </li>
            </ul>
        </div>
        <div class="col-4 text-end">
            <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" alt="<?= ""; ?>" class="mb-3" height="50"/>
        </div>
    </div>

    <div class="tab-content configs">
        <div class="tab-pane fade active show" id="geral" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100">
                        <form class="form-config-geral">
                            <input type="hidden" value="<?= $group_id; ?>" id="group-id" name="group_id">
                            <header class="card-header">
                                <div class="card-actions"></div>
                                <h2 class="card-title">Geral</h2>
                            </header>
                            <div class="card-body">

                                <div class="row">
                                    <?php if ($user->inGroup("energia")) : ?>
                                        <div class="col-md-4 mb-3">
                                            <label for="ponta-start" class="form-label">Horário Início Ponta</label>
                                            <input id="ponta-start" value="<?= isset($client_config->ponta_start) ? date('H:i', $client_config->ponta_start) : ''; ?>" name="ponta_start" type="time" class="form-control" placeholder="">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ponta-end" class="form-label">Horário Fim Ponta</label>
                                            <input id="ponta-end" value="<?= isset($client_config->ponta_end) ? date('H:i', $client_config->ponta_end) : ''; ?>" name="ponta_end" type="time" class="form-control" placeholder="">
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="area-comum" class="form-label">Identificador da Área Comum</label>
                                        <input id="area-comum" value="<?= $client_config->area_comum ?? ''; ?>" name="area_comum" type="text" class="form-control" placeholder="">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="open" class="form-label">Horário Abertura <?= ucfirst($url) ?></label>
                                        <input id="open" value="<?= isset($client_config->open) ? date('H:i', $client_config->open) : ''; ?>" name="open" type="time" class="form-control" placeholder="">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="close" class="form-label">Horário Fechamento <?= ucfirst($url) ?></label>
                                        <input id="close" value="<?= isset($client_config->close) ? date('H:i', $client_config->close) : ''; ?>" name="close" type="time" class="form-control" placeholder="">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="split-report" class="form-label">Separar Área Comum e Unidades nos relatórios</label></br>
                                        <div class="switch switch-sm switch-primary">
                                            <input type="checkbox" <?= $client_config->split_report ?? 0 ? 'checked' : ''; ?> class="switch-input" id="split-report" name="split_report" data-plugin-ios-switch>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <?php if (!$user->demo) : ?>
                            <div class="card-footer text-end">
                                <button type="button" class="btn btn-primary btn-save btn-save-geral">Salvar</button>
                                <button type="reset" class="btn btn-default btn-reset">Descartar</button>
                            </div>
                            <?php endif; ?>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <div class="tab-pane fade d-none" id="usuarios" role="tabpanel">
            <div class="row pt-0" id="usuarios">
                <div class="col-md-12 mb-4">
                    <section class="card card-users card-easymeter h-100">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <?php if (!$user->inGroup('unity', 'shopping') && !$user->demo): ?>
                                    <button class="btn btn-primary btn-new-user">Criar Usuário</button>
                                <?php endif; ?>
                            </div>
                            <h2 class="card-title">Usuários</h2>
                        </header>
                        <div class="card-body">
                            <div class="tab-form agrupamentos">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped dataTable table-hover no-footer"
                                           id="dt-usuarios" data-url="/shopping/get_users">
                                        <thead>
                                        <tr role="row">
                                            <th class="text-center">Nome</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="unidades" role="tabpanel">

            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-agrupamentos card-easymeter h-100">
                        <header class="card-header">
                            <div class="card-actions buttons"></div>
                            <h2 class="card-title">Unidades</h2>
                        </header>
                        <div class="card-body bordered">
                            <ul class="nav nav-pills nav-pills-primary mb-3" role="tablist">
                                <?php if ($user->entity->m_energia) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-energy <?= $monitoria === 'energy' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#energia1" type="button" aria-selected="true" role="tab">Energia</button>
                                    </li>
                                <?php endif; ?>
                                <?php if ($user->entity->m_agua) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-water <?= $monitoria === 'water' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#agua1" type="button" aria-selected="false" role="tab" tabindex="-1">Água</button>
                                    </li>
                                <?php endif; ?>
                                <?php if ($user->entity->m_gas) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-gas <?= $monitoria === 'gas' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#gas1" type="button" aria-selected="false" role="tab" tabindex="-1">Gás</button>
                                    </li>
                                <?php endif; ?>
                                <?php if ($user->entity->m_nivel) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-info <?= $monitoria === 'nivel' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#nivel1" type="button" aria-selected="false" role="tab" tabindex="-1">Nível</button>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <div class="tab-content configs p-0" style="border: none; box-shadow: none;">

                                <?php if ($user->entity->m_energia) : ?>
                                    <div id="energia1" class="tab-pane <?= $monitoria === 'energy' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100">
                                                <table class="table table-bordered table-striped dataTable table-hover no-footer" id="dt-unidades-energia" data-url="/shopping/get_unidades" data-tipo="energia">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none">id</th>
                                                        <th class="text-center">Medidor</th>
                                                        <th class="text-center">LUC</th>
                                                        <th class="text-center">Subtipo</th>
                                                        <th class="text-center">Tipo</th>
                                                        <th class="text-center">Identificador</th>
                                                        <th class="text-center">Localizador</th>
                                                        <th class="text-center">Capacidade</th>
                                                        <th class="text-center">Lançamentos</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($user->entity->m_agua) : ?>
                                    <div id="agua1" class="tab-pane <?= $monitoria === 'water' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100">
                                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                                       id="dt-unidades-agua" data-url="/shopping/get_unidades" data-tipo="agua">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none">id</th>
                                                        <th class="text-center">Medidor</th>
                                                        <th class="text-center">LUC</th>
                                                        <th class="text-center">Subtipo</th>
                                                        <th class="text-center">Tipo</th>
                                                        <th class="text-center">Identificador</th>
                                                        <th class="text-center">Localizador</th>
                                                        <th class="text-center d-none">Capacidade</th>
                                                        <th class="text-center">Lançamentos</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($user->entity->m_gas) : ?>
                                    <div id="gas1" class="tab-pane <?= $monitoria === 'gas' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100">
                                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                                       id="dt-unidades-gas" data-url="/shopping/get_unidades" data-tipo="gas">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none">id</th>
                                                        <th class="text-center">Medidor</th>
                                                        <th class="text-center">LUC</th>
                                                        <th class="text-center">Subtipo</th>
                                                        <th class="text-center">Tipo</th>
                                                        <th class="text-center">Identificador</th>
                                                        <th class="text-center">Localizador</th>
                                                        <th class="text-center d-none">Capacidade</th>
                                                        <th class="text-center">Lançamentos</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($user->entity->m_nivel) : ?>
                                    <div id="nivel1" class="tab-pane <?= $monitoria === 'nivel' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100">
                                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                                       id="dt-unidades-nivel" data-url="/shopping/get_unidades" data-tipo="nivel">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none">id</th>
                                                        <th class="text-center">Medidor</th>
                                                        <th class="text-center">LUC</th>
                                                        <th class="text-center">Subtipo</th>
                                                        <th class="text-center">Tipo</th>
                                                        <th class="text-center">Identificador</th>
                                                        <th class="text-center">Localizador</th>
                                                        <th class="text-center d-none">Capacidade</th>
                                                        <th class="text-center">Lançamentos</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="agrupamentos" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-agrupamentos card-easymeter h-100">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <?php if (!$user->demo): ?>
                                    <button class="btn btn-primary btn-new-agrupamento-energia <?= $monitoria === 'energy' ? '' : 'd-none' ?>">Criar Agrupamento</button>
                                    <button class="btn btn-primary btn-new-agrupamento-agua <?= $monitoria === 'water' ? '' : 'd-none' ?>">Criar Agrupamento</button>
                                    <button class="btn btn-primary btn-new-agrupamento-gas <?= $monitoria === 'gas' ? '' : 'd-none' ?>">Criar Agrupamento</button>
                                    <button class="btn btn-primary btn-new-agrupamento-nivel <?= $monitoria === 'nivel' ? '' : 'd-none' ?>">Criar Agrupamento</button>
                                <?php endif; ?>
                            </div>
                            <h2 class="card-title">Agrupamentos</h2>
                        </header>
                        <div class="card-body bordered">
                            <ul class="nav nav-pills nav-pills-primary mb-3">
                                <?php if ($user->entity->m_energia) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-energy agrupamento-pill <?= $monitoria === 'energy' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#energia2" type="button" aria-selected="true" role="tab">Energia</button>
                                    </li>
                                <?php endif; ?>
                                <?php if ($user->entity->m_agua) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-water agrupamento-pill <?= $monitoria === 'water' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#agua2" type="button" aria-selected="false" role="tab" tabindex="-1">Água</button>
                                    </li>
                                <?php endif; ?>
                                <?php if ($user->entity->m_gas) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-gas agrupamento-pill <?= $monitoria === 'gas' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#gas2" type="button" aria-selected="false" role="tab" tabindex="-1">Gás</button>
                                    </li>
                                <?php endif; ?>
                                <?php if ($user->entity->m_nivel) : ?>
                                    <li class="nav-item me-2" role="presentation">
                                        <button class="nav-link color-info agrupamento-pill <?= $monitoria === 'nivel' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#nivel2" type="button" aria-selected="false" role="tab" tabindex="-1">Nível</button>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <div class="tab-content configs">
                                <?php if ($user->entity->m_energia) : ?>
                                    <div id="energia2" class="tab-pane <?= $monitoria === 'energy' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100" style="min-height: 230px;">
                                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                                       id="dt-agrupamentos-energia" data-url="/shopping/get_agrupamentos" data-tipo="energia">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none"></th>
                                                        <th class="d-none"></th>
                                                        <th class="text-center">Grupo</th>
                                                        <th class="text-center">Unidades</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($user->entity->m_agua) : ?>
                                    <div id="agua2" class="tab-pane <?= $monitoria === 'water' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100">
                                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                                       id="dt-agrupamentos-agua" data-url="/shopping/get_agrupamentos" data-tipo="agua">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none"></th>
                                                        <th class="d-none"></th>
                                                        <th class="text-center">Grupo</th>
                                                        <th class="text-center">Unidades</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($user->entity->m_gas) : ?>
                                    <div id="gas2" class="tab-pane <?= $monitoria === 'gas' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100">
                                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                                       id="dt-agrupamentos-gas" data-url="/shopping/get_agrupamentos" data-tipo="gas">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none"></th>
                                                        <th class="d-none"></th>
                                                        <th class="text-center">Grupo</th>
                                                        <th class="text-center">Unidades</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($user->entity->m_nivel) : ?>
                                    <div id="nivel2" class="tab-pane <?= $monitoria === 'nivel' ? 'active' : '' ?>">
                                        <div class="tab-form agrupamentos h-100">
                                            <div class="table-responsive h-100">
                                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                                       id="dt-agrupamentos-nivel" data-url="/shopping/get_agrupamentos" data-tipo="nivel">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="d-none"></th>
                                                        <th class="d-none"></th>
                                                        <th class="text-center">Grupo</th>
                                                        <th class="text-center">Unidades</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <?php if ($user->inGroup("admin", "shopping")): ?>
            <div class="tab-pane fade" id="alertas" role="tabpanel">
                <div class="row pt-0">
                    <div class="col-md-12 mb-4">
                        <section class="card card-agrupamentos card-easymeter h-100">
                            <header class="card-header">
                                <div class="card-actions buttons">
                                </div>
                                <h2 class="card-title">Configurações dos Alertas</h2>
                            </header>

                            <div class="card-body bordered">
                                <ul class="nav nav-pills nav-pills-primary mb-3" role="tablist">
                                    <?php if ($user->entity->m_energia) : ?>
                                        <li class="nav-item me-2" role="presentation">
                                            <button class="nav-link color-energy <?= $monitoria === 'energy' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#energia3" type="button" aria-selected="true" role="tab">Energia</button>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($user->entity->m_agua) : ?>
                                        <li class="nav-item me-2" role="presentation">
                                            <button class="nav-link color-water <?= $monitoria === 'water' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#agua3" type="button" aria-selected="false" role="tab" tabindex="-1">Água</button>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($user->entity->m_gas) : ?>
                                        <li class="nav-item me-2" role="presentation">
                                            <button class="nav-link color-gas <?= $monitoria === 'gas' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#gas3" type="button" aria-selected="false" role="tab" tabindex="-1">Gás</button>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($user->entity->m_nivel) : ?>
                                        <li class="nav-item me-2" role="presentation">
                                            <button class="nav-link color-info <?= $monitoria === 'nivel' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#nivel3" type="button" aria-selected="false" role="tab" tabindex="-1">Nível</button>
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <div class="tab-content configs">
                                    <?php if ($user->entity->m_energia) : ?>
                                        <div id="energia3" class="tab-pane <?= $monitoria === 'energy' ? 'active' : '' ?>">
                                            <div class="tab-form agrupamentos">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped dataTable table-hover no-footer"
                                                           id="dt-alertas-conf-energia" data-url="/shopping/get_alertas_conf" data-tipo="energia">
                                                        <thead>
                                                        <tr role="row">
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="text-center">Status</th>
                                                            <th rowspan="2" class="text-center">Alerta</th>
                                                            <th rowspan="2" class="text-center">Medidores</th>
                                                            <th rowspan="2" class="text-center">Quando</th>
                                                            <th colspan="2" class="text-center">Notificar</th>
                                                            <th rowspan="2" class="text-center">Ações</th>
                                                        </tr>
                                                        <tr role="row">
                                                            <th>Shopping</th>
                                                            <th>Unidade</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($user->entity->m_agua) : ?>
                                        <div id="agua3" class="tab-pane <?= $monitoria === 'energy' ? 'active' : '' ?>">
                                            <div class="tab-form agrupamentos">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped dataTable table-hover no-footer"
                                                           id="dt-alertas-conf-agua" data-url="/shopping/get_alertas_conf" data-tipo="agua">
                                                        <thead>
                                                        <tr role="row">
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="text-center">Status</th>
                                                            <th rowspan="2" class="text-center">Alerta</th>
                                                            <th rowspan="2" class="text-center">Medidores</th>
                                                            <th rowspan="2" class="text-center">Quando</th>
                                                            <th colspan="2" class="text-center">Notificar</th>
                                                            <th rowspan="2" class="text-center">Ações</th>
                                                        </tr>
                                                        <tr role="row">
                                                            <th>Shopping</th>
                                                            <th>Unidade</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($user->entity->m_gas) : ?>
                                        <div id="gas3" class="tab-pane <?= $monitoria === 'energy' ? 'active' : '' ?>">
                                            <div class="tab-form agrupamentos">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped dataTable table-hover no-footer"
                                                           id="dt-alertas-conf-gas" data-url="/shopping/get_alertas_conf" data-tipo="gas">
                                                        <thead>
                                                        <tr role="row">
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="text-center">Status</th>
                                                            <th rowspan="2" class="text-center">Alerta</th>
                                                            <th rowspan="2" class="text-center">Medidores</th>
                                                            <th rowspan="2" class="text-center">Quando</th>
                                                            <th colspan="2" class="text-center">Notificar</th>
                                                            <th rowspan="2" class="text-center">Ações</th>
                                                        </tr>
                                                        <tr role="row">
                                                            <th>Shopping</th>
                                                            <th>Unidade</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($user->entity->m_nivel) : ?>
                                        <div id="nivel3" class="tab-pane <?= $monitoria === 'energy' ? 'active' : '' ?>">
                                            <div class="tab-form agrupamentos">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped dataTable table-hover no-footer"
                                                           id="dt-alertas-conf-nivel" data-url="/shopping/get_alertas_conf" data-tipo="nivel">
                                                        <thead>
                                                        <tr role="row">
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="d-none"></th>
                                                            <th rowspan="2" class="text-center">Status</th>
                                                            <th rowspan="2" class="text-center">Alerta</th>
                                                            <th rowspan="2" class="text-center">Medidores</th>
                                                            <th rowspan="2" class="text-center">Quando</th>
                                                            <th colspan="2" class="text-center">Notificar</th>
                                                            <th rowspan="2" class="text-center">Ações</th>
                                                        </tr>
                                                        <tr role="row">
                                                            <th>Shopping</th>
                                                            <th>Unidade</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="tab-pane fade" id="api" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100">
                        <form class="form-config-api" data-grupo="<?= $group_id; ?>">
                            <input type="hidden" value="<?= $group_id; ?>" id="group-id" name="group_id">
                            <header class="card-header">
                                <div class="card-actions"></div>
                                <h2 class="card-title">API</h2>
                            </header>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="token" class="form-label">Chave</label>
                                        <div class="row">
                                            <div class="col-md-8 input-group">
                                                <input onClick="this.select();"  id="token" value="<?= $token ?>" name="token" type="text" class="form-control" placeholder="" aria-describedby="button-addon2">
                                                <button class="btn btn-primary btn-generate-token <?= $user->demo ? 'disabled' : '' ?> <?= $token ? 'renew' : '' ?>" type="button-addon2"><?= $token ? 'Renovar Chave' : 'Gerar Chave' ?></button>
                                            </div>
                                            <a href="/api/doc" class="text-right" target="_blank">Documentação <i class="fas fa-arrow-up-right-from-square"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- end: page -->

    <div>
        <table class="text-dark w-100">
            <tbody><tr>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="30"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</section>

<?php
$data['modal_id'] = 'modalExcluiUser';
$data['modal_title'] = 'Você tem certeza?';
$data['modal_message'] = 'Deseja realmente excluir este Usuário?';
$data['button'] = array('Excluir', 'Cancelar');
echo view('modals/confirm', $data);
?>

<?php
$data_unidade['modal_id'] = 'modalExcluiUnidade';
$data_unidade['modal_title'] = 'Você tem certeza?';
$data_unidade['modal_message'] = 'Deseja realmente excluir esta Unidade?';
$data_unidade['button'] = array('Excluir', 'Cancelar');
echo view('modals/confirm', $data_unidade);
?>

<?php
$data_unidade['modal_id'] = 'modalExcluiAgrupamentoAgua';
$data_unidade['modal_title'] = 'Você tem certeza?';
$data_unidade['modal_message'] = 'Deseja realmente excluir este agrupamento?';
$data_unidade['button'] = array('Excluir', 'Cancelar');
echo view('modals/confirm', $data_unidade);
?>

<?php
$data_unidade['modal_id'] = 'modalExcluiAgrupamentoEnergia';
$data_unidade['modal_title'] = 'Você tem certeza?';
$data_unidade['modal_message'] = 'Deseja realmente excluir este agrupamento?';
$data_unidade['button'] = array('Excluir', 'Cancelar');
echo view('modals/confirm', $data_unidade);
?>

<?php
$dataKey['modal_id'] = 'modalGenerateKey';
$dataKey['modal_title'] = 'Você tem certeza?';
$dataKey['modal_message'] = 'Ao renovar, sua chave atual será invalidada, continuar?';
$dataKey['button'] = array('Renovar', 'Cancelar');
echo view('modals/confirm_key', $dataKey);
?>
