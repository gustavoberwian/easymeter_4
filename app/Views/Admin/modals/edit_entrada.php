<link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.css'); ?>" />
<!-- Modal Form -->
<div id="modalEntrada" class="modal-block modal-block-primary1">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Editar Entrada</h2>
		</header>
		<form class="form-entrada-edit">
			<div class="card-body">
            <input id="id-entrada" name="id-entrada" type="hidden"
                        value="<?php if (isset($entrada->id))
                            echo $entrada->id; ?>" readonly>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-start pt-2">Nome</label>
                    <div class="col-lg-9">
                        <input id="nome-entrada" name="nome-entrada" class="form-control" value="<?php echo($entrada->nome); ?>" required <?php if ($readonly != 'true')
                        echo 'disabled="true"'; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-start pt-2">Cor</label>
                    <div class="col-lg-9">
                        <input id="cor-entrada" name="cor-entrada" class="form-control" value="<?php echo($entrada->color); ?>" required <?php if ($readonly != 'true')
                        echo 'disabled="true"'; ?>>
                    </div>
                </div>
                <div class="row">
                    <label class="col-lg-3 control-label text-lg-start pt-2">Medidores</label>
                    <div class="col-lg-9">
                        <input id="medidores-entrada" name="medidores-entrada" data-role="tagsinput" type="text" value="<?php foreach($medidores as $m) {
                            echo($m['nome'] .', '); } ?>" readonly disabled="true">
                    </div>
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

