
<section role="main" class="content-body" data-url="<?= $url ?>" data-monitoria="<?= $monitoria ?>">

    <!-- start: page -->
    <div class="row pt-4">
        <div class="col-md-4">
            <section class="card card-easymeter mb-4">
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

        <div class="col-md-4">
            <section class="card card-easymeter mb-4">
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

        <div class="col-md-4">
            <section class="card card-easymeter mb-4">
                <div class="card-body">
                    <h6 class="card-body-title mb-3 mt-0 text-warning">Alertas <i class="float-end fas fa-exclamation-triangle"></i></h6>
                    <div class="row">
                        <div class="col-lg-6 pr-1">
                            <div class="h5 mb-0 mt-1"><span class="vazamentos">-</span></div>
                            <p class="text-3 text-muted mb-0">Vazamentos</p>
                        </div>
                        <div class="col-lg-6 pl-1">
                            <div class="h5 mb-0 mt-1"><span class="outros">-</span></div>
                            <p class="text-3 text-muted mb-0">Outros</p>
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
                    <button type="button" class="btn btn-primary btn-sheet-condos"><i class="fas fa-file-download"></i> <span class="d-none d-sm-inline">Baixar Planilha</span></button>
                    <button type="button" class="btn btn-success btn-inclui-all-fechamentos"><i class="fas fa-file-import"></i> <span class="d-none d-sm-inline">Faturar Todos</span></button>
                </div>
                <h2 class="card-title">Clientes</h2>
            </header>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover table-click dataTable no-footer display responsive nowrap" id="dt-entidades" data-url="/consigaz/get_entidades">
                    <thead>
                    <tr role="row">
                        <th class="text-center" data-priority="1">Cliente</th>
                        <th class="text-center">Competência do último fechamento</th>
                        <th class="text-center">Abertas</th>
                        <th class="text-center">Fechadas</th>
                        <th class="text-center">Erros</th>
                        <th class="text-center">Alertas</th>
                        <th class="text-center">Vazamentos</th>
                        <th class="text-center">Último mês</th>
                        <th class="text-center">Mês atual</th>
                        <th class="text-center">Previsão</th>
                        <th class="text-center" data-priority="2">Ações</th>
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