<div id="md-generate-code" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Autenticação Por 2 Fatores</h2>
        </header>

        <div class="card-body">
            <div class="alert alert-danger"><b>Atenção</b>: o QR Code estará disponível apenas UMA VEZ, tenha certeza de salvá-lo corretamente.</div>
            <p class="mb-0">Identificador: <?= $holder ?></p>
            <div class="row justify-content-center">
                <div class="col-7">
                    <img src="<?= $google2fa_url; ?>" alt="">
                </div>
            </div>
            <p>Abra seu aplicativo do Google Authenticator e pressiona o ícone "+", então clique em "Ler código QR" e aponte a câmera para o QRCode.</p>
        </div>

        <footer class="card-footer">
            <button class="btn btn-default modal-dismiss float-end" tabIndex="9">Fechar</button>
        </footer>
    </section>
</div>