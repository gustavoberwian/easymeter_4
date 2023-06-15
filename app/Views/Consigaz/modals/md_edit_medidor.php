<div id="md-edit-medidor" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Editar Unidade</h2>
        </header>

        <div class="card-body">

            <div class="alert alert-danger fade show d-none" role="alert">

            </div>

            <form class="form-edit-medidor">

                <?php if (!empty($medidor)) : ?>
                    <input type="hidden" id="medidor" name="medidor" value="<?= $medidor->id; ?>">
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Nome<span class="required"></span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control" id="device" name="device" value="<?= $medidor->device; ?>" placeholder="Novo nome" tabIndex="1">
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