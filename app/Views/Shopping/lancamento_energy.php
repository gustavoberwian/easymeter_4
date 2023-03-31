<section role="main" class="content-body" data-type="energy">
    <header class="page-header">
        <h2><?= $group->group_name; ?></h2>
    </header>

    

    <section class="card card-easymeter mb-4">
        <header class="card-header">
            <div class="card-actions buttons">
                <button class="btn btn-primary btn-download" data-group="<?= $group_id; ?>" data-id="<?= $fechamento->id; ?>" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
            </div>
            <h2 class="card-title">Lançamento</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered text-center mb-0">
                <thead>
                <tr role="row">
                    <th>Competência</th>
                    <th>Data Inicial</th>
                    <th>Data Final</th>
                    <th>Dias</th>
                    <th>Emissão</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row">
                    <td><?= competencia_nice($fechamento->competencia); ?></td>
                    <td><?= date('d/m/Y', $fechamento->inicio); ?></td>
                    <td><?= date('d/m/Y', $fechamento->fim); ?></td>
                    <td><?= round(($fechamento->fim - $fechamento->inicio) / 86400, 0); ?></td>
                    <td><?= date('d/m/Y', strtotime($fechamento->cadastro)); ?></td>
                </tr>
                </tbody>
            </table>

            <table class="table table-bordered text-center">
                <thead>
                <tr role="row" style="border-top-width: 0;">
                    <th rowspan="2"></th>
                    <th colspan="3" class="text-center">Consumo - kWh</th>
                    <th colspan="3" class="text-center">Demanda - kW</th>
                </tr>
                <tr role="row">
                    <th>Total</th>
                    <th>Ponta</th>
                    <th>Fora Ponta</th>
                    <th>Total</th>
                    <th>Ponta</th>
                    <th>Fora Ponta</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row">
                    <td class="text-start"><?= $area_comum; ?></td>
                    <td><?= number_format(round($fechamento->consumo, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_p, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_f, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->demanda, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->demanda_p, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->demanda_f, 0), 0, ',', '.'); ?></td>
                </tr>
                <tr role="row">
                    <td class="text-start">Unidades</td>
                    <td><?= number_format(round($fechamento->consumo_u, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_u_p, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_u_f, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->demanda_u, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->demanda_u_p, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->demanda_u_f, 0), 0, ',', '.'); ?></td>
                </tr>
                <tr role="row">
                    <td class="text-start"><b>Total</b></td>
                    <td><?= number_format(round($fechamento->consumo + $fechamento->consumo_u, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_p + $fechamento->consumo_u_p, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_f + $fechamento->consumo_u_f, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round(max($fechamento->demanda, $fechamento->demanda_u), 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round(max($fechamento->demanda_p, $fechamento->demanda_u_p), 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round(max($fechamento->demanda_f, $fechamento->demanda_u_f), 0), 0, ',', '.'); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="tabs">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-target="#comum" href="#comum" data-bs-toggle="tab"><?= $area_comum; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-target="#unidades" href="#unidades" data-bs-toggle="tab">Unidades</a>
            </li>
        </ul>
        <div class="tab-content card-body">
            <div id="comum" class="tab-pane active">
                <table class="table table-bordered table-striped table-hover table-click" id="dt-fechamento_comum" data-url="<?php echo site_url('energia/GetFechamentoUnidades/1'); ?>">
                    <thead>
                    <tr role="row">
                        <th rowspan="2">Unidade</th>
                        <th rowspan="2" class="text-center">LUC</th>
                        <th colspan="2" class="text-center">Leitura</th>
                        <th colspan="3" class="text-center">Consumo - kWh</th>
                        <th colspan="3" class="text-center">Demanda - kW</th>
                    </tr>
                    <tr role="row">
                        <th>Anterior</th>
                        <th>Atual</th>
                        <th>Total</th>
                        <th>Ponta</th>
                        <th>Fora Ponta</th>
                        <th>Total</th>
                        <th>Ponta</th>
                        <th>Fora Ponta</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="unidades" class="tab-pane">
                <table class="table table-bordered table-striped table-hover table-click" id="dt-fechamento_unidades" data-url="<?php echo site_url('energia/GetFechamentoUnidades/2'); ?>">
                    <thead>
                    <tr role="row">
                        <th rowspan="2">Unidade</th>
                        <th rowspan="2" class="text-center">LUC</th>
                        <th colspan="2" class="text-center">Leitura</th>
                        <th colspan="3" class="text-center">Consumo - kWh</th>
                        <th colspan="3" class="text-center">Demanda - kW</th>
                    </tr>
                    <tr role="row">
                        <th>Anterior</th>
                        <th>Atual</th>
                        <th>Total</th>
                        <th>Ponta</th>
                        <th>Fora Ponta</th>
                        <th>Total</th>
                        <th>Ponta</th>
                        <th>Fora Ponta</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <table class="text-dark w-100">
            <tbody><tr>
                <td>
                    <img src="<?php echo base_url('assets/img/' . $user->condo->image_url); ?>" alt="<?= ""; ?>" class="mb-4" height="35"/>
                </td>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="35"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- end: page -->
</section>