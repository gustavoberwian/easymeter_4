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
            <div class="nav-wrap-desk">
                <ul class="nav nav-pills nav-pills-primary mb-3">
            <button class="btn btn-light me-4" id='btn-back-last' data-bs-toggle="" data-bs-target="#back" type="button"><i class="fas fa-arrow-left"></i> Voltar</button>
                <?php if (!is_null($group->m_energia)) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#energy" type="button">Energia</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_agua)) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#water" type="button">Água</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_gas)) : ?>
                    <li class="nav-item " role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#gas" type="button">Gás</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_nivel)) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#nivel" type="button">Nível</button>
                    </li>
                <?php endif; ?>
            </ul>
            </div>
            <div class="nav-wrap-mob">
                <button class="btn btn-light me-4" id='btn-back-last' data-bs-toggle="" data-bs-target="#back"
                    type="button"><i class="fas fa-arrow-left"></i> Voltar</button>
                <select class='nav-sel btn btn btn-primary'>
                    <?php if (!is_null($group->m_energia)) : ?>
                        <option value="energy">Energia</option>
                    <?php endif; ?>
                    <?php if (!is_null($group->m_agua)) : ?>
                        <option value="water">Água</option>
                    <?php endif; ?>
                    <?php if (!is_null($group->m_gas)) : ?>
                        <option value="gas">Gás</option>
                    <?php endif; ?>
                    <?php if (!is_null($group->m_nivel)) : ?>
                        <option value="nivel">Nível</option>
                    <?php endif; ?>
                </select>
            </div>        
        </div>
        <div class="col-3 text-end">
            <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" alt="<?= ""; ?>" class="mb-3" height="50"/>
        </div>
    </div>

    <div id="insights" class="tab-pane">
        <div class="tab-content configs">
            <?php if ($user->inGroup('energia')) : ?>
                <div id="energy" class="tab-pane <?= $monitoria === 'energy' ? 'show active' : '' ?>">
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

            <?php if ($user->inGroup('agua')) : ?>
                <div id="water" class="tab-pane <?= $monitoria === 'water' ? 'active' : '' ?>">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
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

                        <div class="col-lg-6 mb-4">
                            <section class="card card-easymeter h-100">
                                <header class="card-header">
                                    <div class="card-actions">
                                    </div>
                                    <h2 class="card-title pr-4 mr-4">Alertas de vazamento</h2>
                                </header>
                                <div class="card-body" style="min-height: 463px;">
                                    <table class="table table-bordered table-responsive-md table-striped mb-0" id="dt-vazamento">
                                        <thead>
                                        <tr>
                                            <th width="10%"></th>
                                            <th width="25%">Nome</th>
                                            <th width="30%">Vazamento</th>
                                            <th width="30%">Participação</th>
                                            <th width="5%"></th>
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