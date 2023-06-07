<div id="md-edit-cliente" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Editar Cliente</h2>
        </header>

        <div class="card-body">

            <div class="alert alert-danger fade show d-none" role="alert">

            </div>

            <form class="form-edit-cliente">

                <?php if (!empty($entidade)) : ?>
                    <input type="hidden" id="entidade" name="entidade" value="<?= $entidade->id; ?>">
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Nome<span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= $entidade->nome; ?>" placeholder="Novo nome" required tabIndex="1">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <footer class="card-footer">
            <div class="text-end">
                <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }' tabIndex="8">Incluir</button>
                <button class="btn btn-default modal-dismiss" tabIndex="9">Cancelar</button>
            </div>
        </footer>
    </section>
</div>