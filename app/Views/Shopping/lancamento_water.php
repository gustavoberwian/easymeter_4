<section role="main" class="content-body" data-type="water">
    <header class="page-header" data-url="<?= $url ?>">
        <h2>
            <?= $group->group_name; ?>
        </h2>
    </header>



    <section class="card card-easymeter mb-4">
        <header class="card-header">
            <div class="card-actions buttons">
                <button class="btn btn-primary btn-download" data-group="<?= $group_id; ?>"
                    data-id="<?= $fechamento->id; ?>" data-loading-overlay><i class="fas fa-file-download mr-3"></i>
                    Baixar Planilha</button>
            </div>
            <h2 class="card-title">Lançamento</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered text-center mb-0">
                <thead>
                    <tr role="row">
                        <th width="30%">Competência</th>
                        <th width="30%">Data Inicial</th>
                        <th width="30%">Data Final</th>

                    </tr>
                </thead>
                <tbody>
                    <tr role="row">
                        <td>
                            <?= strftime('%B/%Y', strtotime($fechamento->competencia)); ?>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($fechamento->inicio)); ?>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($fechamento->fim)); ?>
                        </td>

                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered text-center">
                <thead>
                    <tr role="row">
                        <th width="30%">Dias</th>
                        <th width="30%">Emissão</th>
                        <th width="30%">Consumo Atual</th>
                    </tr>
                </thead>
                <tbody>
                    <tr role="row">
                        <td>
                            <?= round((strtotime($fechamento->fim) - strtotime($fechamento->inicio)) / 86400, 0); ?>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($fechamento->cadastro)); ?>
                        </td>
                        <td>
                            <?= number_format(round($fechamento->consumo_c + $fechamento->consumo_u, 0), 0, ',', '.') . ' L'; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="col-md-12 card card-easymeter h-auto mt-0 mb-3">

        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-click" id="dt-unidades"
                data-url="<?php echo site_url('water/GetLancamentoUnidades'); ?>">
                <thead>
                    <tr role="row">
                        <th colspan="1" class="text-center">Medidor</th>
                        <th colspan="2" class="text-center">Leitura</th>
                        <th colspan="3" class="text-center">Consumo - L</th>
                    </tr>
                    <tr role="row">
                        <th>Nome</th>
                        <th>Anterior</th>
                        <th>Atual</th>
                        <th>Consumo Atual</th>
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
                    <td>
                        <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" alt="<?= ""; ?>"
                            class="mb-4" height="35" />
                    </td>
                    <td class="text-end">
                        <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4"
                            height="35" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- end: page -->
</section>