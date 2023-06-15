
<section role="main" class="content-body" data-class="<?= $url ?>" data-monitoria="<?= $monitoria ?>">

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
        <h2>Unidades</h2>
    </header>

    <div class="row pt-0 mb-4">
        <div class="col-md-4">
            <section class="card card-comparativo h-100">
                <div class="card-body" style="background-color: #03aeef;">
                    <h6 class="card-body-title mb-3 mt-0 text-light">Cliente <i class="float-end fas fa-microchip"></i></h6>
                    <div class="row">
                        <div class="col-lg-12 pl-1">
                            <select id="sel-entity" name="sel-entity" class="form-control" required>
                                <option disabled value="">Selecione o cliente</option>
                                <?php foreach ($clientes as $i => $cliente) : ?>
                                    <option <?= (array_key_first($clientes) == $i) ? 'selected' : '' ?> value="<?= $cliente->id ?>"><?= $cliente->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-md-4 pt-md-0 pt-4">
            <section class="card card-easymeter h-100">
                <div class="card-body">
                    <h6 class="card-body-title mb-3 mt-0 text-primary">Consumo <i class="float-end fas fa-calendar"></i></h6>
                    <div class="row">
                        <div class="col-lg-6 pl-1">
                            <div class="h5 mb-0 mt-1"><span class="consumo-mes-atual">-</span></div>
                            <p class="text-3 text-muted mb-0">Mês atual</p>
                        </div>
                        <div class="col-lg-6 pr-1">
                            <div class="h5 mb-0 mt-1"><span class="consumo-mes-anterior">-</span></div>
                            <p class="text-3 text-muted mb-0">Último mês</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-md-4 pt-md-0 pt-4">
            <section class="card card-easymeter h-100">
                <div class="card-body">
                    <h6 class="card-body-title mb-3 mt-0 text-success">Válvulas <i class="float-end fas fa-life-ring"></i></h6>
                    <div class="row">
                        <div class="col-lg-4 pr-1">
                            <div class="h5 mb-0 mt-1"><span class="abertas">-</span></div>
                            <p class="text-3 text-muted mb-0">Abertas</p>
                        </div>
                        <div class="col-lg-4 pl-1">
                            <div class="h5 mb-0 mt-1"><span class="fechadas">-</span></div>
                            <p class="text-3 text-muted mb-0">Fechadas</p>
                        </div>
                        <div class="col-lg-4 pl-1">
                            <div class="h5 mb-0 mt-1"><span class="erros">-</span></div>
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
                <table class="table table-bordered table-striped table-hover table-click dataTable no-footer display responsive nowrap" id="dt-unidades" data-url="/consigaz/get_unidades">
                    <thead>
                    <tr role="row">
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