<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header" data-group="<?=$group_id ?>">
        <h2><?= $group->group_name; ?></h2>
    </header>

    <?php if (empty($user->config)) : ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Atenção!</strong> Configurações gerais do shopping não fornecidas. <a href="/shopping/configuracoes/<?=$group_id ?>" class="alert-link">Clique aqui</a> e configure-os para visualizar os dados corretamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-9">
            <ul class="nav nav-pills nav-pills-primary mb-3 position-absolute" role="tablist" style="z-index: 999;">
                <?php if ($user->entity->m_energia) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link color-energy <?= $monitoria === 'energy' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#energia" type="button" aria-selected="true" role="tab">Energia</button>
                    </li>
                <?php endif; ?>
                <?php if ($user->entity->m_agua) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link color-water <?= $monitoria === 'water' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#agua" type="button" aria-selected="false" role="tab" tabindex="-1">Água</button>
                    </li>
                <?php endif; ?>
                <?php if ($user->entity->m_gas) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link color-gas <?= $monitoria === 'gas' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#gas" type="button" aria-selected="false" role="tab" tabindex="-1">Gás</button>
                    </li>
                <?php endif; ?>
                <?php if ($user->entity->m_nivel) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link color-info <?= $monitoria === 'nivel' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#nivel" type="button" aria-selected="false" role="tab" tabindex="-1">Nível</button>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="col-3 text-end">
            <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" alt="<?= ""; ?>" class="mb-3" height="50"/>
        </div>
    </div>

    <div id="insights" class="tab-pane">
        <div class="tab-content configs">
            <?php if ($user->entity->m_energia) : ?>
                <div id="energia" class="tab-pane <?= $monitoria === 'energy' ? 'show active' : '' ?>">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Medidores com maior consumo no mês em ponta</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-ponta">
                                        <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Medidor</th>
                                            <th width="20%">Consumo</th>
                                            <th width="40%">Participação</th>
                                            <th width="10%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Medidores com maior consumo no mês fora de ponta</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-fora">
                                        <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Medidor</th>
                                            <th width="20%">Consumo</th>
                                            <th width="40%">Participação</th>
                                            <th width="10%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Medidores com maior consumo no mês com o Shopping aberto</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-open">
                                        <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Medidor</th>
                                            <th width="20%">Consumo</th>
                                            <th width="40%">Participação</th>
                                            <th width="10%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Medidores com maior consumo no mês com o Shopping fechado</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-close">
                                        <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Medidor</th>
                                            <th width="20%">Consumo</th>
                                            <th width="40%">Participação</th>
                                            <th width="10%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Lojas com maior emissão de CO² no mês</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-carbon">
                                        <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Medidor</th>
                                            <th width="20%">Consumo</th>
                                            <th width="40%">Participação</th>
                                            <th width="10%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Medidores com maior desvio no fator de potência no mês</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-factor">
                                        <thead>
                                        <tr>
                                            <th width="10%"></th>
                                            <th width="30%">Medidor</th>
                                            <th width="30%">Fator</th>
                                            <th width="30%">Tipo</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($user->entity->m_agua) : ?>
                <div id="agua" class="tab-pane <?= $monitoria === 'water' ? 'show active' : '' ?>">
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Maior consumo de água</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-consumo">
                                        <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="25%">Medidor</th>
                                            <th width="20%">Consumo</th>
                                            <th width="40%">Participação</th>
                                            <th width="10%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($user->entity->m_gas) : ?>
                <div id="gas" class="tab-pane <?= $monitoria === 'gas' ? 'show active' : '' ?>">
                    <!-- TODO -> insights gás -->
                </div>
            <?php endif; ?>
            <?php if ($user->entity->m_nivel) : ?>
                <div id="nivel" class="tab-pane <?= $monitoria === 'nivel' ? 'show active' : '' ?>">
                    <!-- TODO -> insights nivel -->
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-3">
        <table class="text-dark w-100">
            <tbody><tr>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="35"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</section>