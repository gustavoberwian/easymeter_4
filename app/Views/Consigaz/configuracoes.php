<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header">
        <h2>Configurações</h2>
    </header>

    <div class="row pt-0">
        <div class="col-md-12 mb-4">
            <section class="card card-easymeter h-100">
                <form class="form-config-geral">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group row pb-4">
                                <label class="col-lg-5 control-label text-lg-end pt-2">Autenticação em 2 etapas</label>
                                <div class="col-lg-6">
                                    <?php if (is_null($secret)): ?>
                                        <button class="btn btn-default generate-code"><i class="fas fa-qrcode" title=""></i> Gerar QR Code</button>
                                    <?php else: ?>
                                        <a class="btn btn-default request-code"><i class="fas fa-qrcode" title=""></i> Solicitar QR Code</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>