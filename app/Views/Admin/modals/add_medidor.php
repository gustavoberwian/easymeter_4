<!-- Modal Form -->
<div id="modalEntrada" class="modal-block modal-block-primary">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Incluir Medidor</h2>
		</header>
		<form class="form-entrada-add" autocomplete="off">
			<div class="card-body">
				<div class="alert alert-danger notification" style="display:none;"></div>
				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Indentificadores <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <input id="nome-medidor" name="nome-medidor" class="form-control" value="" placeholder="Medidor" required>
                            </div>
                            <div class="col-lg-6">
                                <input id="entrada-medidor" name="entrada-medidor" class="form-control" value="" placeholder="Entrada" required>
                            </div>
                        </div>
                    </div>
				</div>

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
