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

    <div id="insights" class="tab-pane">

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

    <div class="mt-3">
        <table class="text-dark w-100">
            <tbody><tr>
                <td>
                    <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" alt="<?= ""; ?>" class="mb-4" height="35"/>
                </td>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="35"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</section>