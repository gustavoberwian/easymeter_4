<div id="md-fechamento-inclui" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Cadastrar Fechamento - Gás</h2>
        </header>

        <div class="card-body">

            <div class="alert alert-danger fade show d-none" role="alert">

            </div>

            <form class="form-gas-fechamento">

                <?php if (!empty($entidade)) : ?>
                    <input type="hidden" id="tar-gas-entidade" name="tar-gas-entidade" value="<?= $entidade->id; ?>">
                <?php endif; ?>
                <?php if (!empty($ramal)) : ?>
                    <input type="hidden" id="tar-gas-ramal" name="tar-gas-ramal" value="<?= $ramal->id ?>">
                <?php endif; ?>
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
                <div class="col-md-6">
                    <button class="btn btn-primary btn-cfg" href="<?= site_url('shopping/configuracoes/'.$entidade->id.'#unidades'); ?>" tabIndex="8">Configurar Unidades</button>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }' tabIndex="8">Incluir</button>
                    <button class="btn btn-default modal-dismiss" tabIndex="9">Cancelar</button>
                </div>
            </div>
        </footer>
    </section>
</div>