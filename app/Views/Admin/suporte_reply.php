<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header">
        <h2>Histórico do Chamado</h2>
        <div class="right-wrapper text-end">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.html">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li><a href="<?= site_url('admin/suporte'); ?>">Suporte</a></li>
                <li><span>Chamado #<?= $chamado->id; ?></span></li>
            </ol>
        </div>
    </header>
    <section class="content-with-menu mailbox">
        <div class="content-with-menu-container">
            <div class="inner-body mailbox-email">
                <div class="mailbox-email-header mb-3">
                    <h3 class="mailbox-email-subject m-0 font-weight-light">
                        <?= $chamado->nome; ?>
                        <?= (!is_null($chamado->telefone)) ? " - " . $chamado->telefone : ""; ?>
                        <?= $status; ?>
                        <?php if ($chamado->status != 'fechado'): ?>
                            <form method="post">
                                <button type="submit" class="btn btn-sm btn-success float-end" name="fechar"
                                    value="fechar">Fechar Chamado</button>
                            </form>
                        <?php endif; ?>
                    </h3>

                    <p class="mt-2 mb-0 text-5">
                        <?= (!is_null($chamado->entidade)) ? $chamado->entidade . " - " . ((!is_null($chamado->agrupamento)) ? $chamado->agrupamento . "/" : "") . $chamado->unidade . "<br>" : ''; ?>
                        <?= (!is_null($chamado->email)) ? $chamado->email . ' em ' : ''; ?>
                        <?= date("d/m/Y \à\s H:i", strtotime($chamado->cadastro)); ?>
                    </p>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <p>
                            <?= $chamado->mensagem; ?>
                        </p>
                    </div>
                </div>

                <div class="mailbox-email-container">
                    <div class="mailbox-email-screen pt-4">
                        <?php if ($replys): ?>
                            <?php foreach ($replys as $r) { ?>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <p class="card-title">Resposta de
                                            <?= (is_null($r->nome)) ? $chamado->nome : $r->nome; ?>
                                        </p>
                                    </div>
                                    <div class="card-body">
                                        <p>
                                            <?= $r->mensagem; ?>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <p class="m-0"><small>
                                                <?= date("d/m/Y H:i", strtotime($r->cadastro)); ?>
                                            </small></p>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php endif; ?>
                        <?php if ($chamado->status == 'fechado'): ?>
                            <div class="alert alert-success">
                                Chamado Fechado em
                                <?= date("d/m/Y \à\s H:i", strtotime($chamado->fechado_em)); ?> por
                                <?= $chamado->user_name; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                    <form method="post">
                        <div class="compose pt-3">
                            <div id="compose-field" class="compose">
                                <textarea name="message" rows="5" style="width:100%; border-radius:5px;"
                                    required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" name="reply"
                                    value="reply">Responder</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- end: page -->
</section>