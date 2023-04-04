<!-- Modal Form -->
<div id="modalGestor" class="modal-block modal-block-primary mfp-hide">
	<section class="card">
		<header class="card-header">
			<h2 class="card-title">Incluir Gestor</h2>
		</header>
        <div class="card-body">
		    <form class="form-gestor">
				<div class="alert alert-danger notification" style="display:none;"></div>
                <div class="form-group row">
                    <label for="nome-gestor" class="col-lg-2 control-label text-lg-right pt-2">Nome <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <input id="nome-gestor" name="nome-gestor" class="form-control vnome" placeholder="Nome completo do gestor" required>
                    </div>
                </div>
				<div class="form-group row">
                    <label for="cpf-gestor" class="col-lg-2 control-label text-lg-right pt-2">CPF <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <input id="cpf-gestor" name="cpf-gestor" class="form-control vcpf" placeholder="___.___.___-__" required>
                    </div>
				</div>
                <div class="form-group row">
                    <label for="nasc-gestor" class="col-lg-2 control-label text-lg-right pt-2">Nascimento</label>
                    <div class="col-lg-10">
                        <input id="nasc-gestor" name="nasc-gestor" class="form-control vdate" placeholder="__/__/____">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email-gestor" class="col-lg-2 control-label text-lg-right pt-2">Email</label>
                    <div class="col-lg-10">
                        <input id="email-gestor" name="email-gestor" class="form-control" placeholder="Email do gestor" type="email">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-2">
                        <label for="email-gestor" class="control-label text-lg-right pt-2">Tipo / Site</label>
                    </div>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="select-wrap">
                                    <select id="tipo-gestor" name="tipo-gestor" class="form-control" required>
                                        <option selected disabled value="">Tipo do Gestor</option>
                                        <option value="física">Pessoa Física</option>
                                        <option value="juridica">Pessoa Jurídica</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input id="site-gestor" name="site-gestor" class="form-control" placeholder="Site do gestor" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-2">
                        <label class="control-label text-lg-right pt-2">Telefone</label>
                    </div>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="telefone1-gestor" name="telefone1-gestor" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____">
                            </div>
                            <div class="col-md-6">
                                <input id="telefone2-gestor" name="telefone2-gestor" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-2">
                        <label class="control-label text-lg-right pt-2">Celular</label>
                    </div>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="celular1-gestor" name="celular1-gestor" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____">
                            </div>
                            <div class="col-md-6">
                                <input id="celular2-gestor" name="celular2-gestor" class="form-control phone-group celular vtelefone" placeholder="(__) ____-____">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <footer class="card-footer">
            <div class="row">
                <div class="col-md-12 text-end">
                    <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Incluir</button>
                    <button class="btn btn-default modal-dismiss">Cancelar</button>
                </div>
            </div>
        </footer>
	</section>
</div>
