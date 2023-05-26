<!-- Modal Form -->
<div id="mdEnvio" class="modal-block modal-block-primary" style="max-width:720px;">
    <section class="card card-easymeter">
        <header class="card-header">
            <h2 class="card-title">Envio <?= $id; ?> - Central <?= $central; ?></h2>
        </header>
        <div class="card-body">
            <div class="tabs">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#int" data-bs-toggle="tab">Dados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#hex" data-bs-toggle="tab">Hexadecimal</a>
                    </li>
                    <?php if (substr($central, 0, 2) == "53") : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#count" data-bs-toggle="tab">Contagem</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#header" data-bs-toggle="tab">Header</a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div id="hex" class="tab-pane" style="font-family: monospace; min-height: 280px; max-height: 280px; overflow-y: scroll;">
                        <?= $hex; ?>
                    </div>
                    <div id="int" class="tab-pane active" style="font-family: monospace; min-height: 280px; max-height: 280px; overflow-y: scroll;">
                        <?= $dec[0]; ?>
                    </div>
                    <?php if (substr($central, 0, 2) == "53") : ?>
                        <div id="count" class="tab-pane" style="font-family: monospace; min-height: 280px; max-height: 280px; overflow-y: scroll;">
                            <?= $dec[1]; ?>
                        </div>
                    <?php endif; ?>
                    <div id="header" class="tab-pane" style="font-family: monospace; min-height: 280px; max-height: 280px; overflow-y: scroll;">
                        <?= $hea; ?>
                    </div>

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
