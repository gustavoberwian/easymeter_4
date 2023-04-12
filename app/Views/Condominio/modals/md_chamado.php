<!-- Modal Form -->
<div id="modalChamado" class="modal-block modal-block-primary">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Abrir Chamado</h2>
		</header>
		<form class="form-chamado">
			<div class="card-body">

                <div class="alert alert-danger notification" style="display:none;"></div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Ramal <span class="required">*</span>/Compet. <span class="required">*</span></label>
					<div class="col-lg-9">
                        <div class="row">
							<div class="col-md-6">
                                <select id="tar-ramal" name="tar-ramal" class="form-control" required tabIndex="1">
                                </select>
							</div>
							<div class="col-md-6">
                                <input type="text" class="form-control vcompetencia" id="tar-competencia" name="tar-competencia" value="" placeholder="__/____" required tabIndex="2">
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Período <span class="required">*</span></label>
					<div class="col-lg-9">
						<div class="row">
							<div class="col-md-6">
								<input type="text" class="form-control vdate" id="tar-data-ini" name="tar-data-ini" value="" placeholder="Data inicial" required tabIndex="3">
							</div>
							<div class="col-md-6">
								<input type="text" class="form-control vdate" id="tar-data-fim" name="tar-data-fim" value="" placeholder="Data final" required tabIndex="4">
							</div>
						</div>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Consumo <span class="required">*</span></label>
					<div class="col-lg-9">
						<div class="row">
							<div class="col-md-6">
								<input type="text" class="form-control vlesser" id="tar-leitura-ini" name="tar-leitura-ini" value="" placeholder="Leitura anterior" required tabIndex="5">
							</div>
							<div class="col-md-6">
								<input type="text" class="form-control vgreater" id="tar-leitura-fim" name="tar-leitura-fim" value="" placeholder="Leitura atual" required tabIndex="6">
							</div>
						</div>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Valor da Conta <span class="required">*</span></label>
					<div class="col-lg-9">
                        <a tabindex="-1" class="input-help" data-id="2" data-toggle="popover">
                            <i class="far fa-question-circle"></i>
                        </a>
                        <input id="tar-valor" name="tar-valor" class="form-control" value="" placeholder="Valor da conta em reais" required tabIndex="7">
					</div>
				</div>

                <div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Serviço Básico <span class="required">*</span></label>
					<div class="col-lg-9">
						<a tabindex="-1" class="input-help" data-id="2" data-toggle="popover">
							<i class="far fa-question-circle"></i>
						</a>
						<input id="tar-basico" name="tar-basico" class="form-control" value="0,00" placeholder="Valor da taxa/serviço básico em reais" required tabIndex="8">
					</div>
				</div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Taxas <span class="required">*</span></label>
					<div class="col-lg-9">
                        <input id="tar-taxas" name="tar-taxas" data-type="value" class="form-control btn-clear" value="0,00" placeholder="Valor de taxas adicionais" required tabIndex="9">
					</div>
				</div>

			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay tabIndex="10">Enviar</button>
						<button class="btn btn-default modal-dismiss" tabIndex="11">Cancelar</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>
