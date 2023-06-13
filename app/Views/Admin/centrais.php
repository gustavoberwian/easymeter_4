<section role="main" class="content-body">
    <header class="page-header">
        <h2>Centrais</h2>
        <div class="right-wrapper text-right">
            <ol class="breadcrumbs">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><span>Centrais</span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <div class="row mt-3 mt-md-0">
        <div class="col-md-3">
            <section class="card card-leitura mb-3">
                <div class="card-body">
                    <h6 class="card-body-title mb-2 mt-0 text-primary">Centrais<i
                            class="float-right fas fa-microchip"></i></h6>
                    <h2 class="mt-0 mb-2">
                        <?= $count; ?>
                    </h2>
                </div>
            </section>
        </div>
    </div>

    <div class="row row-eq-height pt-0">
        <div class="col-md-8">
            <section class="card h-100">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <form class="form-inline">
                            <div class="input-group mr-2">
                                <input type="text" class="form-control" name="q" placeholder="Medidor"
                                    style="width:100px;">
                                <span class="input-group-append">
                                    <button class="btn btn-primary search" type="button" data-searching="0"><i
                                            class="fas fa-search"></i></button>
                                    <button class="btn btn-primary btn-centrais-reload"><i
                                            class="fas fa-redo-alt"></i></button>
                                </span>
                            </div>

                        </form>
                    </div>
                    <h2 class="card-title">Listagem</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="dt-centrais"
                        data-url="<?php echo site_url('admin/get_centrais'); ?>">
                        <thead>
                            <tr role="row">
                                <th width="5%">Status</th>
                                <th width="13%">Central</th>
                                <th width="10%">Modo</th>
                                <th width="10%">Versão</th>
                                <th width="15%">Condomínio</th>
                                <th width="23%">Último Envio</th>
                                <th width="13%">Energia</th>
                                <th width="6%">Fraude</th>
                                <th width="5%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-md-4">
            <section class="card h-100">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <button class="btn btn-primary btn-envios-reload"><i class="fas fa-redo-alt"></i></button>
                    </div>
                    <h2 class="card-title">Últimos Envios</h2>
                </header>
                <div class="card-body">
                    <table class="table table-sm table-bordered table-striped" id="dt-postagens"
                        data-url="<?php echo site_url('admin/get_postagens'); ?>">
                        <thead>
                            <tr role="row">
                                <th width="20%">Central</th>
                                <th width="30%">Timestamp</th>
                                <th width="20%">ID</th>
                                <th width="20%">Tamanho</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    <!-- <div class="row row-eq-height">
                        <div class="col-md-3">
                            <section class="card h-100">
                                <header class="card-header">
                                    <h2 class="card-title">Viver Portas 1-8</h2>
                                </header>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered table-striped" id="dt-portas1" data-url="<?php echo site_url('ajax/get_consumo_portas/1'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="60%">Central</th>
                                                <th width="40%">Consumo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-md-3">
                            <section class="card h-100">
                                <header class="card-header">
                                    <h2 class="card-title">Viver Portas 9-16</h2>
                                </header>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered table-striped" id="dt-portas2" data-url="<?php echo site_url('ajax/get_consumo_portas/2'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="60%">Central</th>
                                                <th width="40%">Consumo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-md-3">
                            <section class="card h-100">
                                <header class="card-header">
                                    <h2 class="card-title">Viver Portas 17-24</h2>
                                </header>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered table-striped" id="dt-portas3" data-url="<?php echo site_url('ajax/get_consumo_portas/3'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="60%">Central</th>
                                                <th width="40%">Consumo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-md-3">
                            <section class="card h-100">
                                <header class="card-header">
                                    <h2 class="card-title">Viver Portas 24-32</h2>
                                </header>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered table-striped" id="dt-portas4" data-url="<?php echo site_url('ajax/get_consumo_portas/4'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="60%">Central</th>
                                                <th width="40%">Consumo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div> -->
    </div>
    <!-- end: page -->
</section>