<!-- Modal Form -->
<div id="modalEntrada" class="modal-block modal-block-primary">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Incluir Entrada - Bloco <?= $condo->b_nome;?></h2>
		</header>
		<form class="form-entrada-add" autocomplete="off">
			<div class="card-body">

				<div class="alert alert-danger notification" style="display:none;"></div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Indentificadores <span class="required">*</span></label>
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

				<?php if ($condo->d_proprietarios == 1) { ?>
					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Proprietário <span class="required">*</span></label>
						<div class="col-lg-9">
                            <input id="proprietario-unidade" name="proprietario-unidade" class="form-control" value="" placeholder="Nome do Proprietário da Unidade" maxlength="255" <?php if ($modo) echo 'readonly'; ?> required >
                        </div>
					</div>
					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Email</label>
						<div class="col-lg-9">
                            <input type="email" id="email-unidade" name="email-unidade" class="form-control" value="" placeholder="Email do Proprietário da Unidade" maxlength="255" <?php if ($modo) echo 'readonly'; ?> >
                        </div>
					</div>
					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Telefone</label>
						<div class="col-lg-9">
                            <input id="telefone-unidade" name="telefone-unidade" class="form-control vtelefone" value="" placeholder="__ ____-____" maxlength="11" <?php if ($modo) echo 'readonly'; ?> >
                        </div>
					</div>
                <?php } ?>

				<?php if ($condo->fracao_ideal == 1) { ?>
					<div class="form-group row">
						<label class="col-lg-3 control-label text-lg-right pt-2">Fração Ideal/Tipo <span class="required">*</span></label>
						<div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-6">
                                    <select class="form-control fracao-entrada" name="fracao-entrada" required>
                                        <option></option>
                                        <?php foreach($fracoes as $f) { ?>
                                            <option value="<?= $f->fracao; ?>"><?= $f->fracao; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <select class="form-control tipo-entrada" name="tipo-entrada" required>
                                        <option value="" disabled selected hidden>Tipo</option>
                                        <?php foreach(explode(',', $condo->tipo_unidades) as $t) { ?>
                                            <option value="<?= $t; ?>"><?= $t; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
					</div>
                <?php } ?>
			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-right">
                        <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Salvar</button>
                        <button class="btn btn-default modal-dismiss">Cancelar</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>
