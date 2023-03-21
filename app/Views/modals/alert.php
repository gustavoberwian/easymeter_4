<!-- Modal Form -->
<div id="modalAlert" class="modal-block modal-header-color modal-block-<?php echo alerta_tipo2color($alerta->tipo); ?>">
    <section class="card">
        <header class="card-header">
            <div class="card-actions">
                <?php if ($alerta->lida) { ?>
                    <span class="float-right text-white" title="Lida em"><?php echo time_ago($alerta->lida); ?></span></h2>
                <?php } ?>
            </div>
            <h2 class="card-title"><?php echo $alerta->titulo; ?>
        </header>
        <div class="card-body">
            <div class="modal-wrapper">
                <div class="modal-text">
                    <?php echo $alerta->texto; ?>
                </div>
            </div>
        </div>
        <footer class="card-footer">
            <div class="row">
                <div class="col-12">
                    <p class="float-start mb-0 mt-1"><?= ucfirst($alerta->enviada); ?></p>
                    <button class="btn btn-default modal-dismiss float-end">Fechar</button>
                </div>
            </div>
        </footer>
    </section>
</div>