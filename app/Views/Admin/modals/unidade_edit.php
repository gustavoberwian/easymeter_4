<!-- Modal Form -->
<div id="modalUnidade" class="modal-block modal-block-primary">
    <section class="card">

        <header class="card-header">
            <h2 class="card-title"><?php echo $modal_title; ?></h2>
        </header>
        <form class="form-unidade-edit">
            <div class="card-body">

                <?php if (isset($unidade->id)) { ?>
                    <input name="id" type="hidden" value="<?php echo $unidade->id;?>">
                <?php } ?>
                <div class="alert alert-danger notification" style="display:none;"></div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Indentificador <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <input id="nome-unidade" name="nome-unidade" class="form-control" value="<?php if (isset($unidade->nome)) echo $unidade->nome;?>" placeholder="Número do Apartamento" required <?php if ($modo) echo 'readonly'; ?> >
                            </div>
                            <div class="col-lg-6">
                                <input id="andar-unidade" name="andar-unidade" class="form-control" value="<?php if (isset($unidade->andar)) echo $unidade->andar;?>" placeholder="Andar da Unidade" required <?php if ($modo) echo 'readonly'; ?> >
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($condo->fracao_ideal) and $condo->fracao_ideal == 1) { ?>
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Fração Ideal <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <select class="form-control fracao-unidade" name="fracao-unidade" required <?php if ($modo) echo 'disabled'; ?>>
                                <option></option>
                                <?php foreach($fracoes as $f) { ?>
                                    <option value="<?= $f->fracao; ?>" <?php if (isset($unidade->fracao) and $unidade->fracao == $f->fracao) echo 'selected';?>><?= $f->fracao; ?></option>
                                <?php } ?>
                            </select>
                            <!--                            <input id="fracao-unidade1" name="fracao-unidade1" class="form-control" value="<?php if (isset($unidade->fracao)) echo $unidade->fracao;?>" placeholder="Fração Ideal da Unidade" maxlength="12" required <?php if ($modo) echo 'readonly'; ?> >-->
                        </div>
                    </div>
                <?php } ?>

                <?php if (!$modo) { ?>
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right">Entradas</label>
                        <div class="col-lg-9">
                            <div class="checkbox-custom checkbox-default">
                                <input type="checkbox" class="entradas-unidade" id="entradas-unidade" <?= (isset($unidade)) ? 'checked' : ''; ?>>
                                <label for="entradas-unidade">Cadastar as entradas da unidade?</label>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!--                <div class="entradas" style="display: <?= ($modo && count($entradas)) ? 'block' : 'none'; ?>;">-->
                <div class="entradas">
                    <?php foreach($entradas as $k => $e) : ?>
                        <div class="form-group row">
                            <label class="col-lg-3 control-label text-lg-right pt-2 pl-0">Entrada <?= $e->entrada; ?> <span class="required">*</span></label>
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><?= entrada_icon($e->tipo); ?></span>
                                            <select class="form-control ignore centrais" name="entrada[<?= $e->eid; ?>][<?= $e->tipo; ?>][central]" data-id="<?= $e->eid; ?>" data-tipo="<?= $e->tipo; ?>" data-porta="<?= $e->posicao; ?>" required <?php if ($modo) echo 'disabled'; ?>>
                                                <option disabled value="" selected>Central</option>
                                                <option value="null">Não Monitorado</option>
                                                <?php foreach($centrais as $c) { ?>
                                                    <option value="<?= $c->nome; ?>" <?php if(isset($e->central) and $c->nome == $e->central) echo 'selected';?>><?= $c->nome; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-<?= $e->tipo == 'agua' ? 6 : 3; ?>">
                                        <select class="form-control ignore portas-<?= $e->eid; ?>" name="entrada[<?= $e->eid; ?>][<?= $e->tipo; ?>][posicao]" required <?php if ($modo) echo 'disabled'; ?>>
                                            <option disabled selected hidden value=""><?= ($modo) ? $e->posicao : 'Porta'; ?></option>
                                        </select>
                                    </div>
                                    <?php if($e->tipo != 'agua'): ?>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control " name="entrada[<?= $e->eid; ?>][<?= $e->tipo; ?>][fator]" value="<?php if(isset($e->fator)) echo $e->fator;?>" placeholder="Fator" required <?php if ($modo) echo 'readonly'; ?>>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (false) { ?>
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2 pl-0">Medidor de Gás</label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-fire text-warning"></i>
                                            </span>
                                        </span>
                                        <select class="form-control centrais" name="central">
                                            <option disabled value="" selected>Central</option>
                                            <option value="null">Não Monitorado</option>
                                            <?php foreach($centrais as $c) { ?>
                                                <option value="<?= $c->nome; ?>"><?= $c->nome; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control portas" name="porta" style="display:none;"></select>
                                    <div class="text-center p-2 portas-spinner" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <?php if ($modo) : ?>
                            <button class="btn btn-default modal-dismiss">Fechar</button>
                        <?php else : ?>
                            <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Salvar</button>
                            <button class="btn btn-default modal-dismiss">Cancelar</button>
                        <?php endif; ?>
                    </div>
                </div>
            </footer>
        </form>

    </section>
</div>
