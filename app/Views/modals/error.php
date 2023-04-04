<div id="modalError" class="modal-block modal-full-color modal-block-danger modal-block-sm">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Erro</h2>
        </header>
        <div class="card-body">
            <div class="modal-wrapper">
                <div class="modal-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="modal-text">
                    <h4><?php echo $message; ?></h4>
                    <p><?php if(isset($sub_message)) echo $sub_message; ?></p>
                </div>
            </div>
        </div>
        <footer class="card-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-default modal-dismiss">Fechar</button>
                </div>
            </div>
        </footer>
    </section>
</div>