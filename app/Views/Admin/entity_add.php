<section role="main" class="content-body">
    <header class="page-header">
        <h2>Condomínios</h2>
        <div class="right-wrapper text-right">
            <ol class="breadcrumbs">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><a href="<?php echo site_url('admin/condominios'); ?>"><span>Condomínios</span></a></li>
                <li><span>Incluir</span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <div class="row">
        <div class="col">
            <section class="card">
                <form class="form-horizontal form-bordered form-condo">
                    <input id="id-condo" name="id-condo" type="hidden" value="<?php if (isset($condo->id)) echo $condo->id; ?>" readonly>
                    <header class="card-header">
                        <h2 class="card-title">Incluir Condomínio</h2>
                    </header>
                    <div class="card-body">
                        <div class="tab-form cadastro">

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Nome <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="nome-condo" name="nome-condo" type="text" value="" class="form-control vnome" placeholder="Razão Social do Condomínio" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">CNPJ <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cnpj-condo" name="cnpj-condo" value="" placeholder="___.___.___/____-__" class="form-control vcnpj" required>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="tipo-condo" name="tipo-condo" class="form-control" required>
                                                    <option selected disabled value="">Tipo do Condomínio</option>
                                                    <option value="vertical">Residencial Vertical</option>
                                                    <option value="horizontal">Residencial Horizontal</option>
                                                    <option value="comercial">Comercial</option>
                                                    <option value="misto">Misto</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">CEP<span class="required">*</span></label>
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <input id="cep-condo" name="cep-condo" type="text" value="" placeholder="_____-___" class="form-control vcep" required>
                                        <span class="input-group-append">
															<button class="btn btn-success btn-busca overlay-small" type="button" data-loading-overlay disabled>Completar</button>
														</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Logradouro <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="logradouro-condo" name="logradouro-condo" type="text" value="" class="form-control" placeholder="Nome da rua/avenida" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Número/Complemento <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="numero-condo" name="numero-condo" value="" placeholder="Número" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="complemento-condo" name="complemento-condo" value="" placeholder="Complemento" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Bairro <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="bairro-condo" name="bairro-condo" type="text" value="" class="form-control" placeholder="Nome do bairro" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Cidade/Estado <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cidade-condo" name="cidade-condo" value="" placeholder="Cidade" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="estado-condo" name="estado-condo" class="form-control" required>
                                                    <option selected disabled value="">Estado</option>
                                                    <option value="AC">Acre</option>
                                                    <option value="AL">Alagoas</option>
                                                    <option value="AP">Amapá</option>
                                                    <option value="AM">Amazonas</option>
                                                    <option value="BA">Bahia</option>
                                                    <option value="CE">Ceará</option>
                                                    <option value="DF">Distrito Federal</option>
                                                    <option value="ES">Espírito Santo</option>
                                                    <option value="GO">Goiás</option>
                                                    <option value="MA">Maranhão</option>
                                                    <option value="MT">Mato Grosso</option>
                                                    <option value="MS">Mato Grosso do Sul</option>
                                                    <option value="MG">Minas Gerais</option>
                                                    <option value="PA">Pará</option>
                                                    <option value="PB">Paraíba</option>
                                                    <option value="PR">Paraná</option>
                                                    <option value="PE">Pernambuco</option>
                                                    <option value="PI">Piauí</option>
                                                    <option value="RJ">Rio de Janeiro</option>
                                                    <option value="RN">Rio Grande do Norte</option>
                                                    <option value="RS">Rio Grande do Sul</option>
                                                    <option value="RO">Rondônia</option>
                                                    <option value="RR">Roraima</option>
                                                    <option value="SC">Santa Catarina</option>
                                                    <option value="SP">São Paulo</option>
                                                    <option value="SE">Sergipe</option>
                                                    <option value="TO">Tocantins</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Administradora</label>
                                <div class="col-lg-6">
                                    <select id="select-adm" name="select-adm" class="form-control populate" data-url="<?php echo site_url('ajax/get_admnistadoras'); ?>">
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Síndico/Gestor <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <select id="select-sindico" name="select-sindico" class="form-control populate" data-url="<?php echo site_url('ajax/get_sindicos'); ?>" required>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Inicio e Fim do Mandato <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="inicio-condo" name="inicio-condo" value="" placeholder="__/__/____" class="form-control vdate" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="fim-condo" name="fim-condo" value="" placeholder="__/__/____" class="form-control vdate" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Monitoramento<span class="required">*</span></label>
                                <div class="col-lg-6 align-self-center">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input class="require-one" type="checkbox" id="agua-condo" name="agua-condo">
                                                <label for="agua-condo">Água</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="checkbox-custom checkbox-warning">
                                                <input class="require-one" type="checkbox" id="gas-condo" name="gas-condo">
                                                <label for="gas-condo">Gás</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="checkbox-custom checkbox-danger">
                                                <input class="require-one" type="checkbox" id="energia-condo" name="energia-condo">
                                                <label for="energia-condo">Energia</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group row ramais" style="display:none;">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Ramais de Água<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="ramais-condo" name="ramais-condo" data-role="tagsinput" data-tag-class="badge badge-info" class="form-control ramais-input no-validate" value="" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Frações Ideais</label>
                                <div class="col-lg-6 align-self-center">
                                    <div class="checkbox-custom checkbox-default">
                                        <input type="checkbox" id="fracao-condo" name="fracao-condo">
                                        <label for="fracao-condo">Cadastar as Frações Ideais das unidades para permitir rateio de valores da inadimplência?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Observações</label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" rows="3" id="textareaAutosize" data-plugin-textarea-autosize></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Status</label>
                                <div class="col-lg-6">
                                    <div class="switch switch-sm switch-primary" title="Desativar Condomínio">
                                        <span>Inativo</span>
                                        <input type="checkbox" name="switch" id="switch" checked="checked" />
                                        <span>Ativo</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-lg-9 text-right">
                                <button class="btn btn-primary btn-salvar">Salvar</button>
                                <button type="reset" class="btn btn-reset">Limpar</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>
<?php
$this->load->view('modals/admin/sindico');
$this->load->view('modals/admin/administradora');
?>