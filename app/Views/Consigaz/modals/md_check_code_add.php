<div id="md-pin-check-config" class="modal-block modal-block-primary">
    <section class="card card-easymeter">

        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Autenticação</h2>
        </header>

        <form class="form-check-code-config">
            <div class="card-body">
                <div class="alert alert-danger fade show d-none" role="alert"></div>
                    <input type="hidden" class="eid" name="eid" data-eid="<?= $eid ?>" value="<?= $eid ?>">
                    <input type="hidden" class="name" name="name" data-name="<?= $name ?>" value="<?= $name ?>">
                    <div class="form-group row">
                        <label for="code" class="col-lg-3 control-label text-lg-right pt-2">Pin <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <input id="code" name="code" class="form-control" placeholder="Digite o PIN de 6 dígitos" required>
                        </div>
                    </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-success modal-confirm overlay-small" data-loading-overlay>Confirmar</button>
                        <button class="btn btn-default modal-dismiss">Cancelar</button>
                    </div>
                </div>
            </div>
        </form>

    </section>
</div>