<section role="main" class="content-body" data-type="water">
    <header class="page-header" data-url="<?= $url ?>">
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
                    <th colspan="3" class="text-center">Consumo - L</th>
                </tr>
                <tr role="row">
                    <th>Total</th>
                    <th>Aberto</th>
                    <th>Fechado</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row">
                    <td><?= number_format(round($fechamento->consumo_c + $fechamento->consumo_u, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_c_o + $fechamento->consumo_u_o, 0), 0, ',', '.'); ?></td>
                    <td><?= number_format(round($fechamento->consumo_c_c + $fechamento->consumo_u_c, 0), 0, ',', '.'); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="col-md-12 card card-easymeter h-auto mt-0 mb-3">

        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-click" id="dt-unidades" data-url="<?php echo site_url('water/GetLancamentoUnidades'); ?>">
                <thead>
                <tr role="row">
                    <th colspan="2" class="text-center">Medidor</th>
                    <th colspan="2" class="text-center">Leitura</th>
                    <th colspan="3" class="text-center">Consumo - L</th>
                </tr>
                <tr role="row">
                    <th>Nome</th>
                    <th>LUC</th>
                    <th>Anterior</th>
                    <th>Atual</th>
                    <th>Total</th>
                    <th>Aberto</th>
                    <th>Fechado</th>
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