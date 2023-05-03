<!-- Modal Form -->
<div id="modalRamal" class="modal-block modal-block-primary modal-block-lg mfp-hide">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Incluir Ramal</h2>
        </header>
        <div class="card-body">
            <form class="form-ramal">
            <div class="form-group row">
                <label for="nome-ramal" class="col-lg-3 control-label text-lg-right pt-2">Nome <span class="required">*</span></label>
                <div class="col-lg-9">
                    <input id="nome-ramal" name="nome-ramal" class="form-control vnome" placeholder="Nome do Ramal" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="tipo-ramal" class="col-lg-3 control-label text-lg-right pt-2">Tipo <span class="required">*</span></label>
                <div class="col-lg-9">
                    <select id="tipo-ramal" name="tipo-ramal" class="form-control" required >
                        <option value="" disabled selected>Tipo do ramal</option>
                        <option value="energia">Energia</option>
                        <option value="agua">Água</option>
                        <option value="nivel">Nível</option>
                        <option value="gas">Gás</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="tipo-ramal" class="col-lg-3 control-label text-lg-right pt-2">Entidade <span class="required">*</span></label>
                <div class="col-lg-9">
                    <select id="sel-entity" name="sel-entity" class="form-control populate" data-url="<?= site_url('admin/get_entity'); ?>" required ></select>
                </div>
            </div>                               
            </form>
        </div>
        <footer class="card-footer">
            <div class="row">
                <div class="col-md-12 text-end">
                    <button class="btn btn-primary modal-ramal-confirm overlay-small" data-loading-overlay>Incluir</button>
                    <button class="btn btn-default modal-dismiss">Cancelar</button>
                </div>
            </div>
        </footer>
    </section>
</div>

