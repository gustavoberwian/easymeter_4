
<section role="main" class="content-body" data-class="<?= $url ?>" data-monitoria="<?= $monitoria ?>" data-entidade="<?= $entidade->id ?>">

    <style>
        .ios-switch .state-background {
            background: none;
            border: none;
        }
        .ios-switch .on-background {
            opacity: 100;
        }
        .switch.disabled {
            opacity: 50%;
            pointer-events: none;
        }
        .ios-switch.on .handle {
            background-color: #47a447;
            border: #fff
        }
        .ios-switch .handle {
            background-color: #47a447;
            border: #fff
        }
        .switch.warning .ios-switch .handle {
            background-color: #f1d163;
            border: #fff
        }
        .switch.danger .ios-switch .handle {
            background-color: #d2322d;
            border: #fff
        }
    </style>

    <!-- start: page -->
    <header class="page-header">
        <h2><?= $entidade->nome ?> - Unidades</h2>
    </header>

    <div class="row pt-0">
        <div class="col-md-4">
            <section class="card card-easymeter mb-4">
                <div class="card-body">
                    <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo <i class="float-end fas fa-calendar"></i></h6>
                    <div class="row">
                        <div class="col-lg-6 pl-1">
                            <div class="h5 mb-0 mt-1"><?= $consumo['mes_atual']; ?></div>
                            <p class="text-3 text-muted mb-0">Mês atual</p>
                        </div>
                        <div class="col-lg-6 pr-1">
                            <div class="h5 mb-0 mt-1"><?= $consumo['ultimo_mes']; ?></div>
                            <p class="text-3 text-muted mb-0">Último mês</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-md-4">
            <section class="card card-easymeter mb-4">
                <div class="card-body">
                    <h6 class="card-body-title mb-3 mt-0 text-success">Válvulas <i class="float-end fas fa-life-ring"></i></h6>
                    <div class="row">
                        <div class="col-lg-4 pr-1">
                            <div class="h5 mb-0 mt-1"><?= $valvulas['abertas']; ?></div>
                            <p class="text-3 text-muted mb-0">Abertas</p>
                        </div>
                        <div class="col-lg-4 pl-1">
                            <div class="h5 mb-0 mt-1"><?= $valvulas['fechadas']; ?></div>
                            <p class="text-3 text-muted mb-0">Fechadas</p>
                        </div>
                        <div class="col-lg-4 pl-1">
                            <div class="h5 mb-0 mt-1"><?= $valvulas['erros']; ?></div>
                            <p class="text-3 text-muted mb-0">Com erro</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="row pt-0">
        <section class="card card-easymeter mb-4">
            <header class="card-header">
                <div class="card-actions buttons">
                    <button type="button" class="btn btn-primary btn-sheet-unidades"><i class="fas fa-file-download"></i> Baixar Planilha</button>
                </div>
                <h2 class="card-title">Unidades</h2>
            </header>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover dataTable no-footer" id="dt-unidades" data-url="/consigaz/get_unidades">
                    <thead>
                    <tr role="row">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="3" class="text-center">Consumo</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th class="text-center">Medidor</th>
                        <th class="text-center">Dispositivo</th>
                        <th class="text-center">Bloco</th>
                        <th class="text-center">Apto</th>
                        <th class="text-center">Último mês</th>
                        <th class="text-center">Mês atual</th>
                        <th class="text-center">Previsão</th>
                        <th class="text-center">Válvula</th>
                        <th class="text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <!-- end: page -->
</section>

<?php
echo view('Consigaz/modals/sync');
?>