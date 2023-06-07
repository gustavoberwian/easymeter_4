<div id="md-edit-cliente" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title"><?= $entidade->nome; ?></h2>
        </header>

        <div class="card-body">
            <p><?= $entidade->nome; ?></p>
            <p>TODO: quais infos trazer nesse modal?</p>
        </div>

        <footer class="card-footer">
            <div class="text-end">
                <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }' tabIndex="8">Incluir</button>
                <button class="btn btn-default modal-dismiss" tabIndex="9">Cancelar</button>
            </div>
        </footer>
    </section>
</div>