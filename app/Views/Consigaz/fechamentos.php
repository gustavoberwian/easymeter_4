<section role="main" class="content-body" data-url="<?= $url ?>">
    <!-- start: page -->
    <header class="page-header">
        <h2>Fechamentos</h2>
    </header>

    <div class="row pt-0 mb-4">
        <div class="col-md-4">
            <section class="card card-comparativo h-100">
                <div class="card-body" style="background-color: #03aeef;">
                    <h6 class="card-body-title mb-3 mt-0 text-light">Cliente <i class="float-end fas fa-microchip"></i></h6>
                    <div class="row">
                        <div class="col-lg-12 pl-1">
                            <select id="entity" name="entity" class="form-control" required>
                                <option selected disabled value="">Selecione o cliente</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?= $cliente->id ?>"><?= $cliente->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="row pt-0">
        <section class="col-md-12 card card-easymeter h-auto mt-0 mb-3">
            <header class="card-header">
                <div class="card-actions buttons">
                    <button class="btn btn-primary btn-gas-download" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
                </div>
                <h2 class="card-title">Fechamentos</h2>
            </header>

            <div class="card-body">
                <div class="d-flex justify-content-end">
                    <button class="ml-3 btn btn-success btn-gas-incluir mb-3">Incluir</button>
                </div>
                <div class="tab-form faturamento">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable table-hover table-click no-footer" id="dt-gas" data-url="/gas/get_fechamentos_gas">
                            <thead>
                            <tr role="row">
                                <th>Competência</th>
                                <th>Data inicial</th>
                                <th>Data final</th>
                                <th class="text-center">Consumo - m³</th>
                                <th>Emissão</th>
                                <th>Ações</th>
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

    <div id="modalExclui" class="modal-block modal-header-color modal-block-danger mfp-hide">
        <input type="hidden" class="id" value="0">
        <input type="hidden" class="type" value="">
        <section class="card">
            <header class="card-header">
                <div class="card-actions"></div>
                <h2 class="card-title">Você tem certeza?</h2>
            </header>
            <div class="card-body">
                <div class="modal-wrapper">
                    <div class="modal-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="modal-text">
                        <h4>Deseja realmente excluir este fechamento?</h4>
                        <p>Ao excluir o fechamento, todas as informações de consumo das unidades também serão excluídos.</p>
                        <p><strong>Atenção:</strong> A exclusão é definitiva e não poderá ser desfeita.</p>
                    </div>
                </div>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-danger modal-confirm overlay-small mr-3" style="min-width:69px;" data-timer="10" data-loading-overlay disabled>10</button>
                        <button class="btn btn-default modal-dismiss">Cancelar</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

    <div id="md-gas-include" class="modal-block modal-block-primary mfp-hide">
        <section class="card card-easymeter">
            <header class="card-header">
                <div class="card-actions"></div>
                <h2 class="card-title">Cadastrar Fechamento - Gás</h2>
            </header>

            <div class="card-body">

                <div class="alert alert-danger fade show d-none" role="alert">

                </div>

                <form class="form-gas-fechamento">

                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Competência<span class="required">*</span></label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control vcompetencia" id="tar-gas-competencia" name="tar-gas-competencia" value="" placeholder="__/____" required tabIndex="1">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Período <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control vdate" id="tar-gas-data-ini" name="tar-gas-data-ini" value="" placeholder="Data inicial" required tabIndex="2">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control vdate" id="tar-gas-data-fim" name="tar-gas-data-fim" value="" placeholder="Data final" required tabIndex="3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Mensagem</label>
                        <div class="col-lg-9">
                            <textarea class="form-control" id="tar-gas-msg" name="tar-gas-msg" rows="5" tabIndex="4"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-6 text-end">
                        <button class="btn btn-primary modal-gas-confirm overlay-small" data-loading-overlay tabIndex="8">Incluir</button>
                        <button class="btn btn-default modal-dismiss" tabIndex="9">Cancelar</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>
    <!-- end: page -->
</section>