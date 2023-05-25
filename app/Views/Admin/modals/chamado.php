
<!-- Modal Form -->
<div id="modalChamado" class="modal-block modal-block-primary">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Abrir Chamado</h2>
		</header>
		<form class="form-chamado">
			<div class="card-body">

                <div class="alert alert-danger notification" style="display:none;"></div>
                <div class="alert alert-success notification" style="display:none;"></div>
                <div class="alert alert-warning notification" style="display:none;">A visita de técnica possui um custo de R$ 80,00 se não verificado nenhum problema.</div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Assunto <span class="required">*</span></label>
					<div class="col-lg-9">
                        <select class="form-control" name="assunto" required>
                            <option disabled selected hidden value="">Selecione o assunto</option>
                            <option value="s">Sugestão</option>
                            <option value="d">Dúvida</option>
                            <option value="r">Revisão</option>
                            <option value="v">Visita Técnica</option>
                        </select>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Mensagem <span class="required">*</span></label>
					<div class="col-lg-9">
                        <textarea class="form-control" name="message" rows="5" style="width:100%;" placeholder="" required></textarea>
					</div>
				</div>
				
			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Enviar</button>
						<button class="btn btn-default modal-dismiss">Cancelar</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>
