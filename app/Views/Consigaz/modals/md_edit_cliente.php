<div id="md-edit-cliente" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Editar Cliente</h2>
        </header>

        <div class="card-body">

            <div class="alert alert-danger fade show d-none" role="alert">

            </div>

            <form class="form-edit-cliente">

                <?php if (!empty($entidade)) : ?>
                    <input type="hidden" id="entidade" name="entidade" value="<?= $entidade->id; ?>">
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Nome<span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= $entidade->nome; ?>" placeholder="Novo nome" required tabIndex="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">CEP<span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input id="cep" name="cep" type="text" value="<?php if (isset($entidade->cep)) echo $entidade->cep; ?>" placeholder="_____-___" class="form-control vcep" required>
                            <button class="btn btn-success btn-busca overlay-small" type="button" data-loading-overlay disabled>Completar</button>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Logradouro <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <input id="logradouro" name="logradouro" type="text" value="<?php if (isset($entidade->logradouro)) echo $entidade->logradouro; ?>" class="form-control" placeholder="Nome da rua/avenida" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Número/Complemento <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="numero" name="numero" value="<?php if (isset($entidade->numero)) echo $entidade->numero; ?>" placeholder="Número" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <input id="complemento" name="complemento" value="<?php if (isset($entidade->complemento)) echo $entidade->complemento; ?>" placeholder="Complemento" class="form-control" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Bairro <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <input id="bairro" name="bairro" type="text" value="<?php if (isset($entidade->bairro)) echo $entidade->bairro; ?>" class="form-control" placeholder="Nome do bairro" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right pt-2">Cidade/Estado <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input id="cidade" name="cidade" value="<?php if (isset($entidade->cidade)) echo $entidade->cidade; ?>" placeholder="Cidade" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <div class="select-wrap">
                                    <select id="uf" name="uf" class="form-control" required>
                                        <?php $entidade_uf = (!is_null($entidade->uf)) ? $entidade->uf : ''?>
                                        <option <?php if (is_null($entidade_uf)) echo 'selected '; ?> disabled value="">Estado</option>
                                        <option <?php if ($entidade_uf == 'AC') echo 'selected '; ?> value="AC">Acre</option>
                                        <option <?php if ($entidade_uf == 'AL') echo 'selected '; ?> value="AL">Alagoas</option>
                                        <option <?php if ($entidade_uf == 'AP') echo 'selected '; ?> value="AP">Amapá</option>
                                        <option <?php if ($entidade_uf == 'AM') echo 'selected '; ?> value="AM">Amazonas</option>
                                        <option <?php if ($entidade_uf == 'BA') echo 'selected '; ?> value="BA">Bahia</option>
                                        <option <?php if ($entidade_uf == 'CE') echo 'selected '; ?> value="CE">Ceará</option>
                                        <option <?php if ($entidade_uf == 'DF') echo 'selected '; ?> value="DF">Distrito Federal</option>
                                        <option <?php if ($entidade_uf == 'ES') echo 'selected '; ?> value="ES">Espírito Santo</option>
                                        <option <?php if ($entidade_uf == 'GO') echo 'selected '; ?> value="GO">Goiás</option>
                                        <option <?php if ($entidade_uf == 'MA') echo 'selected '; ?> value="MA">Maranhão</option>
                                        <option <?php if ($entidade_uf == 'MT') echo 'selected '; ?> value="MT">Mato Grosso</option>
                                        <option <?php if ($entidade_uf == 'MS') echo 'selected '; ?> value="MS">Mato Grosso do Sul</option>
                                        <option <?php if ($entidade_uf == 'MG') echo 'selected '; ?> value="MG">Minas Gerais</option>
                                        <option <?php if ($entidade_uf == 'PA') echo 'selected '; ?> value="PA">Pará</option>
                                        <option <?php if ($entidade_uf == 'PB') echo 'selected '; ?> value="PB">Paraíba</option>
                                        <option <?php if ($entidade_uf == 'PR') echo 'selected '; ?> value="PR">Paraná</option>
                                        <option <?php if ($entidade_uf == 'PE') echo 'selected '; ?> value="PE">Pernambuco</option>
                                        <option <?php if ($entidade_uf == 'PI') echo 'selected '; ?> value="PI">Piauí</option>
                                        <option <?php if ($entidade_uf == 'RJ') echo 'selected '; ?> value="RJ">Rio de Janeiro</option>
                                        <option <?php if ($entidade_uf == 'RN') echo 'selected '; ?> value="RN">Rio Grande do Norte</option>
                                        <option <?php if ($entidade_uf == 'RS') echo 'selected '; ?> value="RS">Rio Grande do Sul</option>
                                        <option <?php if ($entidade_uf == 'RO') echo 'selected '; ?> value="RO">Rondônia</option>
                                        <option <?php if ($entidade_uf == 'RR') echo 'selected '; ?> value="RR">Roraima</option>
                                        <option <?php if ($entidade_uf == 'SC') echo 'selected '; ?> value="SC">Santa Catarina</option>
                                        <option <?php if ($entidade_uf == 'SP') echo 'selected '; ?> value="SP">São Paulo</option>
                                        <option <?php if ($entidade_uf == 'SE') echo 'selected '; ?> value="SE">Sergipe</option>
                                        <option <?php if ($entidade_uf == 'TO') echo 'selected '; ?> value="TO">Tocantins</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <footer class="card-footer">
            <div class="text-end">
                <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }' tabIndex="8">Salvar</button>
                <button class="btn btn-default modal-dismiss" tabIndex="9">Cancelar</button>
            </div>
        </footer>
    </section>
</div>