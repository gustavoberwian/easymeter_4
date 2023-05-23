<div id="md-fechamento-inclui" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Autenticação Por 2 Fatores</h2>
        </header>

        <div class="card-body">
            <?= $holder; ?>
            <div class="row justify-content-center">
                <div class="col-7">
                    <img src="<?= $google2fa_url; ?>" alt="">
                </div>
            </div>
            <p>Abra seu aplicativo do Google Authenticator e pressiona o ícone "+", então clique em "Ler código QR" e aponte a câmera para o QRCode</p>
        </div>

        <footer class="card-footer">
            <button class="btn btn-default modal-dismiss float-end" tabIndex="9">Fechar</button>
        </footer>
    </section>
</div>