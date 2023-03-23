<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header" data-group="<?= $group_id ?>">
        <h2><?= $group->group_name; ?></h2>
    </header>

    <img src="<?php echo base_url('assets/img/logo-north.png'); ?>" alt="<?= "North"; ?>" class="mb-4" height="80"/>

    <ul class="nav nav-pills nav-pills-primary mb-3">
        <li class="nav-item configs" role="presentation">
            <button class="nav-link configs left active" data-bs-toggle="pill" data-bs-target="#energy" type="button">Energia</button>
        </li>
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link configs right" data-bs-toggle="pill" data-bs-target="#water" type="button">Água</button>
        </li>
    </ul>

    <div class="tab-content" style="background-color: transparent; box-shadow: none; padding: 0;">

        <div class="tab-pane fade show active" id="energy">

            <section class="card card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <button type="button" class="btn btn-primary btn-alert-config">Configurações</button>
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
                        <button type="button" class="btn btn-primary btn-alert-config">Configurações</button>
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