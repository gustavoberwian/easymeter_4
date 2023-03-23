<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header">
        <h2><?= $group->group_name; ?></h2>
    </header>

    <img src="<?php echo base_url('assets/img/logo-north.png'); ?>" alt="<?= "North"; ?>" class="mb-4" height="80"/>

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