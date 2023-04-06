<!-- Modal Form -->
<div id="modalAdm" class="modal-block modal-block-primary modal-block-lg mfp-hide">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Incluir Administradora</h2>
        </header>
        <div class="card-body">
            <form class="form-adm">
                <div class="alert alert-danger notification" style="display:none;"></div>
                <div class="form-group row">
                    <label for="nome-adm" class="col-lg-2 control-label text-lg-right pt-2">Nome <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <input id="nome-adm" name="nome-adm" class="form-control vnome" placeholder="Nome da administradora" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 control-label text-lg-right pt-2">CNPJ <span class="required">*</span>/ Contato</label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="cnpj-adm" name="cnpj-adm" class="form-control cnpj" placeholder="__.___.___/____-__" required>
                            </div>
                            <div class="col-md-6">
                                <input id="contato-adm" name="contato-adm" class="form-control vnome" placeholder="Nome do contato" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 control-label text-lg-right pt-2">E-mail <span class="required">*</span>/ Site <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="email-adm" name="email-adm" class="form-control" placeholder="Email da administradora" type="email" required>
                            </div>
                            <div class="col-md-6">
                                <input id="site-adm" name="site-adm" class="form-control" placeholder="Site da administradora" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="cep-adm" class="col-lg-2 control-label text-lg-right pt-2">CEP <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="cep-adm" name="cep-adm" type="text" placeholder="_____-___" class="form-control vcep" required>
                                    <button class="btn btn-success btn-busca-adm overlay-small" type="button" data-loading-overlay disabled>Completar</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 control-label text-lg-right pt-2">Endereço <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="logradouro-adm" name="logradouro-adm" type="text" value="" class="form-control" placeholder="Nome da rua/avenida" required>
                            </div>
                            <div class="col-md-3">
                                <input id="numero-adm" name="numero-adm" placeholder="Número" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <input id="complemento-adm" name="complemento-adm" placeholder="Complemento" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 control-label text-lg-right pt-2">Cidade <span class="required">*</span>/UF <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="bairro-adm" name="bairro-adm" type="hidden">
                                <input id="cidade-adm" name="cidade-adm" placeholder="Cidade" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <select id="estado-adm" name="estado-adm" class="form-control" required>
                                    <option disabled selected value="">Estado</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 control-label text-lg-right pt-2">Telefone <span class="required">*</span></label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="telefone1-adm" name="telefone1-adm" class="form-control phone-group-adm celular vtelefone" placeholder="(__) ____-____">
                            </div>
                            <div class="col-md-6">
                                <input id="telefone2-adm" name="telefone2-adm" class="form-control phone-group-adm vtelefone celular" placeholder="(__) ____-____">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <footer class="card-footer">
            <div class="row">
                <div class="col-md-12 text-end">
                    <button class="btn btn-primary modal-adm-confirm overlay-small" data-loading-overlay>Incluir</button>
                    <button class="btn btn-default modal-dismiss">Cancelar</button>
                </div>
            </div>
        </footer>
    </section>
</div>
