<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header" data-group="<?= $group_id ?>" data-url="<?= $url ?>">
        <h2><?= $unidade->nome; ?></h2>
    </header>

    <div class="row">
        <div class="col-6">
            <ul class="nav nav-pills nav-pills-primary mb-3">
                <?php if (!is_null($group->m_energia)) : ?>
                    <li class="nav-item configs" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#energy" type="button">Energia</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_agua)) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#water" type="button">Água</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_gas)) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#gas" type="button">Gás</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_nivel)) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#nivel" type="button">Nível</button>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="col-6 text-end">
            <img src="<?php echo base_url('assets/img/' . $user->condo->image_url); ?>" alt="<?= ""; ?>" class="mb-3" height="50"/>
        </div>
    </div>

    <div class="tab-content" style="background-color: transparent; box-shadow: none; padding: 0;">

        <div class="tab-pane fade show active" id="energy">

            <section class="card card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                    </div>
                    <h2 class="card-title">Alertas</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-click dt-alerts" id="dt-alerts-energia" data-url="/shopping/GetAlerts" data-tipo="energia">
                        <thead>
                        <tr role="row">
                            <th width="5%"></th>
                            <th width="10%">Categoria</th>
                            <th width="10%">Medidor</th>
                            <th width="15%">Unidade</th>
                            <th width="55%">Mensagem</th>
                            <th width="10%">Enviada Em</th>
                            <th width="5%">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <div class="tab-pane fade" id="water">

            <section class="card card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                    </div>
                    <h2 class="card-title">Alertas</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-click dt-alerts" id="dt-alerts-water" data-url="/shopping/GetAlerts" data-tipo="agua">
                        <thead>
                        <tr role="row">
                            <th width="5%"></th>
                            <th width="10%">Categoria</th>
                            <th width="10%">Medidor</th>
                            <th width="15%">Unidade</th>
                            <th width="55%">Mensagem</th>
                            <th width="10%">Enviada Em</th>
                            <th width="5%">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

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