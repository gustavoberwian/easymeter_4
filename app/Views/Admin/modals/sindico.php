<!-- Modal Form -->
<div id="modalSindico" class="modal-block modal-block-primary mfp-hide">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Incluir Síndico/Gestor</h2>
		</header>
		<form class="form-sindico">
			<div class="card-body">
				<div class="alert alert-danger notification" style="display:none;"></div>
				<div class="form-group">
					<label>Nome <span class="required">*</span></label>
					<input id="nome-sindico" name="nome-sindico" class="form-control vnome" placeholder="Nome completo do síndico/gestor" required>
				</div>
				<div class="form-group">
					<label>CPF <span class="required">*</span></label>
					<input id="cpf-sindico" name="cpf-sindico" class="form-control pf" placeholder="___.___.___-__" required>
				</div>
				<div class="form-group">
					<label>Data de Nascimento</label>
					<input id="nasc-sindico" name="nasc-sindico" class="form-control vdate" placeholder="__/__/____">
				</div>
				<div class="form-group">
					<label>E-mail <span class="required">*</span></label>
					<input id="email-sindico" name="email-sindico" class="form-control" placeholder="Email do síndico/gestor" type="email" required>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Telefone</label>
						<input id="telefone1-sindico" name="telefone1-sindico" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____">
					</div>
					<div class="form-group col-md-6">
						<label>Telefone</label>
						<input id="telefone2-sindico" name="telefone2-sindico" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____" >
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Celular</label>
						<input id="celular1-sindico" name="celular1-sindico" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____">
					</div>
					<div class="form-group col-md-6">
						<label>Celular</label>
						<input id="celular2-sindico" name="celular2-sindico" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____" >
					</div>
				</div>
			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Incluir</button>
						<button class="btn btn-default modal-dismiss">Cancelar</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>
