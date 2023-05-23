<section role="main" class="content-body" data-entidade="<?= $entidade->id ?>" data-ramal="<?= $ramal->id ?>" data-fechamento="<?= $fechamento->id ?>">

    <header class="page-header">
        <h2><?= $entidade->nome ?> - Lançamento competência <?= $fechamento->competencia ?></h2>
    </header>

    <section class="card card-easymeter mb-4">
        <header class="card-header">
            <div class="card-actions buttons">
                <button class="btn btn-primary btn-download" data-id="<?= $fechamento->id; ?>" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
            </div>
            <h2 class="card-title">Lançamento</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered text-center mb-0">
                <thead>
                <tr role="row">
                    <th>Competência</th>
                    <th>Consumo - m³</th>
                    <th>Data Inicial</th>
                    <th>Data Final</th>
                    <th>Dias</th>
                    <th>Emissão</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row">
                    <td><?= strftime('%b/%Y', strtotime($fechamento->competencia)); ?></td>
                    <td><?= number_format(round($fechamento->leitura_atual - $fechamento->leitura_anterior, 0), 0, ',', '.'); ?></td>
                    <td><?= date('d/m/Y', $fechamento->inicio); ?></td>
                    <td><?= date('d/m/Y', $fechamento->fim); ?></td>
                    <td><?= round(($fechamento->fim - $fechamento->inicio) / 86400, 0); ?></td>
                    <td><?= date('d/m/Y', strtotime($fechamento->cadastro)); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="col-md-12 card card-easymeter h-auto mt-0 mb-3">

        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-click" id="dt-unidades" data-url="<?php echo site_url('gas/get_fechamentos_unidades'); ?>">
                <thead>
                <tr role="row">
                    <th></th>
                    <th colspan="2" class="text-center">Leitura</th>
                    <th></th>
                </tr>
                <tr role="row">
                    <th>Medidor</th>
                    <th>Anterior</th>
                    <th>Atual</th>
                    <th>Consumo - m³</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-3">
        <table class="text-dark w-100">
            <tbody>
            <tr>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="35"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- end: page -->
</section>