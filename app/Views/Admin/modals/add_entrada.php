<link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.css'); ?>" />
<!-- Modal Form -->
<div id="modalEntrada" class="modal-block modal-block-primary1">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Adicionar Entrada</h2>
		</header>
		<form class="form-entrada-add">
			<div class="card-body">
            <input id="id-entidade" name="id-entidade" type="hidden"
                        value="<?php if (isset($eid))
                            echo $eid; ?>" readonly>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-start pt-2">Nome <span
                        class="required">*</span></label>
                    <div class="col-lg-9">
                        <input id="nome-entrada" name="nome-entrada" class="form-control" value="" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-start pt-2">Cor <span
                        class="required">*</span></label>
                    <div class="col-lg-9">
                        <input id="cor-entrada" name="cor-entrada" class="form-control" value="" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-start pt-2">Tipo <span
                        class="required">*</span></label>
                    <div class="col-lg-9">
                        <select id="tipo-entrada" name="tipo-entrada" class="form-control" required>
                            <option value="energia">Energia</option>
                            <option value="agua">Água</option>
                            <option value="gas">Gás</option>
                            <option value="nivel">Nível</option>
                        </select>
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
<script src="<?php echo base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.js'); ?>"></script>

