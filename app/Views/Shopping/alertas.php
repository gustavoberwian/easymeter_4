<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header" data-group="<?= $group_id ?>">
        <h2><?= $group->group_name; ?></h2>
    </header>

    <img src="<?php echo base_url('assets/img/logo-north.png'); ?>" alt="<?= "North"; ?>" class="mb-4" height="80"/>

    <section class="card card-easymeter mb-4">
        <header class="card-header">
            <div class="card-actions buttons">
                <button type="button" class="btn btn-primary btn-alert-config">Configurações</button>
            </div>
            <h2 class="card-title">Alertas</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-hover table-click" id="dt-alerts" data-url="/energia/GetAlerts">
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