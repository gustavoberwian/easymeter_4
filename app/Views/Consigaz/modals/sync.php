
<!-- Modal Form -->
<div id="modalSync" class="modal-block modal-block-primary mfp-hide">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Titulo</h2>
        </header>
        <div class="card-body">
            <form class="form-sync-leitura">
                <input type="hidden" class="mid" name="mid">
                <div class="form-group row">
                    <label for="leitura" class="col-lg-3 control-label text-lg-right pt-2">Leitura atual <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <input id="leitura" name="leitura" class="form-control" placeholder="Digite a leitura atual" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-end">
                    <button class="btn btn-success btn-sync-leitura overlay-small" data-loading-overlay>Salvar</button>
                    <button class="btn btn-default modal-dismiss">Cancelar</button>
                </div>
            </div>
        </div>
    </section>
</div>
