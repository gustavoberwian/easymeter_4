<!-- Modal Form -->
<div id="modalUnidade" class="modal-block modal-block-primary">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Incluir Unidade - Bloco <?= $entity->b_nome;?></h2>
        </header>
        <form class="form-unidade-add">
            <div class="card-body">

                <div class="alert alert-danger notification" style="display:none;"></div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Indentificador <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <input id="nome-unidade" name="nome-unidade" class="form-control" value="" placeholder="Número do Apartamento" required>
                            </div>
                            <div class="col-lg-6">
                                <input id="andar-unidade" name="andar-unidade" class="form-control" value="" placeholder="Andar da Unidade" required>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($entity->d_proprietarios == 1) { ?>
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Proprietário <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <input id="proprietario-unidade" name="proprietario-unidade" class="form-control" value="" placeholder="Nome do Proprietário da Unidade" maxlength="255" required <?php if ($modo) echo 'readonly'; ?> >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Email <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <input id="email-unidade" name="email-unidade" class="form-control" value="" placeholder="Email do Proprietário da Unidade" maxlength="255" required <?php if ($modo) echo 'readonly'; ?> >
                        </div>
                    </div>
                <?php } ?>

                <?php if ($entity->fracao_ideal == 1) { ?>
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Fração Ideal <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <select class="form-control fracao-unidade" name="fracao-unidade" required>
                                <option></option>
                                <?php foreach($fracoes as $f) { ?>
                                    <option value="<?= $f->fracao; ?>"><?= $f->fracao; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right">Entradas</label>
                    <div class="col-lg-9">
                        <div class="checkbox-custom checkbox-default">
                            <input type="checkbox" class="entradas-unidade" id="entradas-unidade">
                            <label for="entradas-unidade">Cadastar as entradas da unidade?</label>
                        </div>
                    </div>
                </div>

                <div class="entradas" style="display:none;">
                    <?php foreach($entradas as $k => $e) { ?>
                        <div class="form-group row">
                            <label class="col-lg-3 control-label text-lg-right pt-2 pl-0">Entrada <?= $e->entrada; ?> <span class="required">*</span></label>
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><?= entrada_icon($e->tipo); ?></span>
                                            <select class="form-control ignore centrais" name="entrada[<?= $e->eid; ?>][<?= $e->tipo; ?>][central]" data-id="<?= $e->eid; ?>" data-tipo="<?= $e->tipo; ?>" required>
                                                <option disabled value="" selected>Central</option>
                                                <option value="null">Não Monitorado</option>
                                                <?php foreach($centrais as $c) { ?>
                                                    <option value="<?= $c->nome; ?>"><?= $c->nome; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-<?= $e->tipo == 'agua' ? 6 : 3; ?>">
                                        <select class="form-control ignore portas-<?= $e->eid; ?>" name="entrada[<?= $e->eid; ?>][<?= $e->tipo; ?>][posicao]" required>
                                            <option disabled selected hidden value="">Porta</option>
                                        </select>
                                    </div>
                                    <?php if($e->tipo != 'agua'): ?>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control " name="entrada[<?= $e->eid; ?>][<?= $e->tipo; ?>][fator]" value="" placeholder="Fator" required>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Salvar</button>
                        <button class="btn btn-default modal-dismiss">Cancelar</button>
                    </div>
                </div>
            </footer>
        </form>
    </section>
</div>
