<section role="main" class="content-body">
    <header class="page-header">
        <h2>Entidades</h2>
        <div class="right-wrapper text-end">
            <ol class="breadcrumbs pe-4">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><a href="<?php echo site_url('admin/entities'); ?>"><span>Entidades</span></a></li>
                <li><span>Incluir</span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <div class="row">
        <div class="col">
            <section class="card">
                <form class="form-horizontal form-bordered form-entity">
                    <header class="card-header">
                        <h2 class="card-title">Incluir Entidade</h2>
                    </header>
                    <div class="card-body">
                        <div class="tab-form cadastro">

                            <div class="form-group row">
                                <label for="classificacao-entity"
                                    class="col-lg-3 control-label text-lg-right pt-2">Classificação da Entidade <span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="classificacao-entity" name="classificacao-entity"
                                                class="form-control" required>
                                                <option selected disabled value="">Classificação da Entidade</option>
                                                <option value="condominio">Condomínio</option>
                                                <option value="shopping">Shopping</option>
                                                <option value="industria">Indústria</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nome-entity" class="col-lg-3 control-label text-lg-right pt-2">Nome <span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="nome-entity" name="nome-entity" type="text" value=""
                                        class="form-control vnome" placeholder="Nome da entidade" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="cnpj-entity" class="col-lg-3 control-label text-lg-right pt-2">CNPJ</label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cnpj-entity" name="cnpj-entity" value=""
                                                placeholder="___.___.___/____-__" class="form-control vcnpj">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="tipo-entity" name="tipo-entity" class="form-control">
                                                    <option selected disabled value="">Tipo da Entidade</option>
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
                                <label for="cep-entity" class="col-lg-3 control-label text-lg-right pt-2">CEP</label>
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <input id="cep-entity" name="cep-entity" type="text" value=""
                                            placeholder="_____-___" class="form-control vcep"
                                            aria-describedby="complete-cep-button">
                                        <button class="btn btn-success btn-busca overlay-small" type="button"
                                            id="complete-cep-button" data-loading-overlay disabled>Completar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="logradouro-entity"
                                    class="col-lg-3 control-label text-lg-right pt-2">Logradouro</label>
                                <div class="col-lg-6">
                                    <input id="logradouro-entity" name="logradouro-entity" type="text" value=""
                                        class="form-control" placeholder="Nome da rua/avenida">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Número/Complemento</label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="numero-entity" name="numero-entity" value="" placeholder="Número"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <input id="complemento-entity" name="complemento-entity" value=""
                                                placeholder="Complemento" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bairro-entity"
                                    class="col-lg-3 control-label text-lg-right pt-2">Bairro</label>
                                <div class="col-lg-6">
                                    <input id="bairro-entity" name="bairro-entity" type="text" value=""
                                        class="form-control" placeholder="Nome do bairro" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Cidade/Estado</label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="cidade-entity" name="cidade-entity" value="" placeholder="Cidade"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="select-wrap">
                                                <select id="estado-entity" name="estado-entity" class="form-control"
                                                    required>
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
                                <label for="select-adm"
                                    class="col-lg-3 control-label text-lg-right pt-2">Administradora</label>
                                <div class="col-lg-6">
                                    <select id="select-adm" name="select-adm" class="form-control populate"
                                        data-url="<?php echo site_url('admin/get_admnistadoras'); ?>">
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="select-gestor" class="col-lg-3 control-label text-lg-right pt-2">Gestor
                                    <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <select id="select-gestor" name="select-gestor" class="form-control populate"
                                        data-url="<?php echo site_url('admin/get_gestor'); ?>" required>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Inicio e Fim do Mandato <span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input id="inicio-entity" name="inicio-entity" value=""
                                                placeholder="__/__/____" class="form-control vdate" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input id="fim-entity" name="fim-entity" value="" placeholder="__/__/____"
                                                class="form-control vdate" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Monitoramento<span
                                        class="required">*</span></label>
                                <div class="col-lg-6 align-self-center">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input class="require-one" type="checkbox" id="agua-entity"
                                                    name="agua-entity">
                                                <label for="agua-entity">Água</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-success">
                                                <input class="require-one" type="checkbox" id="gas-entity"
                                                    name="gas-entity">
                                                <label for="gas-entity">Gás</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-warning">
                                                <input class="require-one" type="checkbox" id="energia-entity"
                                                    name="energia-entity">
                                                <label for="energia-entity">Energia</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-info">
                                                <input class="require-one" type="checkbox" id="nivel-entity"
                                                    name="nivel-entity">
                                                <label for="nivel-entity">Nível</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Frações Ideais</label>
                                <div class="col-lg-6 align-self-center">
                                    <div class="checkbox-custom checkbox-default">
                                        <input type="checkbox" id="fracao-entity" name="fracao-entity">
                                        <label for="fracao-entity">Cadastrar as Frações Ideais das unidades para
                                            permitir rateio de valores da inadimplência?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="textareaAutosize"
                                    class="col-lg-3 control-label text-lg-right pt-2">Observações</label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" rows="3" id="textareaAutosize"
                                        data-plugin-textarea-autosize></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="switch" class="col-lg-3 control-label text-lg-right pt-2">Status</label>
                                <div class="col-lg-6">

                                    <div class="switch switch-sm switch-primary" title="Desativar Entidade">
                                        <input type="checkbox" name="switch" id="switch" checked="checked"
                                            data-plugin-ios-switch />
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <footer class="card-footer text-end">
                        <button class="btn btn-primary btn-salvar">Salvar</button>
                        <button type="reset" class="btn btn-reset">Limpar</button>
                    </footer>
                </form>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>
<?php
echo view('Admin/modals/gestor');
echo view('Admin/modals/administradora');
?>