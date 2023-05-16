<section role="main" class="content-body" data-entidade="<?= $entidade->id ?>" data-ramal="<?= $ramal->id ?>">
    <!-- start: page -->
    <header class="page-header">
        <h2><?= $entidade->nome ?> - Configurações</h2>
    </header>

    <div class="row">
        <div class="col">
            <ul class="nav nav-pills nav-pills-primary mb-3" role="tablist">
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs left active" data-bs-toggle="pill" data-bs-target="#geral" type="button" aria-selected="true" role="tab">Geral</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#unidades" type="button" aria-selected="false" role="tab" tabindex="-1">Unidades</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#fechamentos" type="button" aria-selected="false" tabindex="-1" role="tab">Fechamentos</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs right" data-bs-toggle="pill" data-bs-target="#alertas" type="button" aria-selected="false" tabindex="-1" role="tab">Alertas</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content configs">
        <div class="tab-pane fade active show" id="geral" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100">
                        <form class="form-config-geral">
                            <input type="hidden" value="<?= $entidade->id; ?>" id="entidade_id" name="entidade_id">
                            <header class="card-header">
                                <div class="card-actions"></div>
                                <h2 class="card-title">Geral</h2>
                            </header>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="area-comum" class="form-label">Identificador da Área Comum</label>
                                        <input id="area-comum" value="" name="area_comum" type="text" class="form-control" placeholder="">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="split-report" class="form-label">Separar Área Comum e Unidades nos relatórios</label></br>
                                        <div class="switch switch-sm switch-primary">
                                            <input type="checkbox" class="switch-input" id="split-report" name="split_report" data-plugin-ios-switch>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer text-end">
                                <button type="button" class="btn btn-primary btn-save btn-save-geral">Salvar</button>
                                <button type="reset" class="btn btn-default btn-reset">Descartar</button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="unidades" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-agrupamentos card-easymeter h-100">
                        <header class="card-header">
                            <div class="card-actions buttons"></div>
                            <h2 class="card-title">Unidades</h2>
                        </header>
                        <div class="card-body bordered">
                            <div class="tab-form agrupamentos h-100">
                                <div class="table-responsive h-100">
                                    <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                           id="dt-unidades" data-url="/consigaz/get_unidades_config" data-tipo="gas">
                                        <thead>
                                        <tr role="row">
                                            <th class="d-none"></th>
                                            <th class="d-none"></th>
                                            <th class="text-center">Medidor</th>
                                            <th class="text-center">Unidade</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="fechamentos" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-agrupamentos card-easymeter h-100">
                        <header class="card-header">
                            <div class="card-actions buttons"></div>
                            <h2 class="card-title">Fechamentos</h2>
                        </header>
                        <div class="card-body bordered">
                            <div class="tab-form agrupamentos h-100">
                                <div class="table-responsive h-100">
                                    <table class="table table-bordered table-striped dataTable table-hover table-click no-footer"
                                           id="dt-unidades" data-url="/consigaz/get_unidades_config" data-tipo="gas">
                                        <thead>
                                        <tr role="row">
                                            <th class="d-none">id</th>
                                            <th class="text-center">Medidor</th>
                                            <th class="text-center">Unidade</th>
                                            <th class="text-center">Bloco</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="alertas" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-agrupamentos card-easymeter h-100">
                        <header class="card-header">
                            <div class="card-actions buttons">
                            </div>
                            <h2 class="card-title">Configurações dos Alertas</h2>
                        </header>
                        <div class="card-body bordered">
                            <div class="tab-content configs">
                                <div class="tab-form agrupamentos">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped dataTable table-hover no-footer"
                                               id="dt-alertas-conf" data-url="/consigaz/get_alertas_conf">
                                            <thead>
                                            <tr role="row">
                                                <th rowspan="2" class="d-none"></th>
                                                <th rowspan="2" class="d-none"></th>
                                                <th rowspan="2" class="text-center">Status</th>
                                                <th rowspan="2" class="text-center">Alerta</th>
                                                <th rowspan="2" class="text-center">Medidores</th>
                                                <th rowspan="2" class="text-center">Quando</th>
                                                <th colspan="2" class="text-center">Notificar</th>
                                                <th rowspan="2" class="text-center">Ações</th>
                                            </tr>
                                            <tr role="row">
                                                <th>Shopping</th>
                                                <th>Unidade</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <!-- end: page -->
</section>