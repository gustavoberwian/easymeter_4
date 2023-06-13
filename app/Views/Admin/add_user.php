<section role="main" class="content-body">
    <header class="page-header">
        <h2>Usuários</h2>
        <div class="right-wrapper text-end">
            <ol class="breadcrumbs pe-4">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><a href="<?php echo site_url('admin/users'); ?>"><span>Usuários</span></a></li>
                <li><span>Incluir</span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <div class="row">
        <div class="col">
            <section class="card">
                <form class="form-horizontal form-bordered form-user">
                    <header class="card-header">
                        <h2 class="card-title">Incluir Usuários</h2>
                    </header>
                    <div class="card-body">
                        <div class="tab-form cadastro">

                            <div class="form-group row">
                                <label for="classificacao-user"
                                    class="col-lg-3 control-label text-lg-right pt-2">Classificação do usuário <span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="classificacao-user" name="classificacao-user"
                                                class="form-control" required>
                                                <option selected disabled value="">Classificação do usuário</option>
                                                <option value="entidades">Entidade</option>
                                                <option value="agrupamentos">Agrupamento</option>
                                                <option value="unidade">Unidade</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row relation-user-entity" data-url="get_entity_for_select" hidden>
                                <label for="entity-user" class="col-lg-3 control-label text-lg-right pt-2">Entidade do
                                    Usuario <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="entity-user" name="entity-user" class="form-control" required>
                                                <option selected disabled value="">Entidade do usuário</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row relation-user-unity" hidden>
                                <label for="unity-user" class="col-lg-3 control-label text-lg-right pt-2">Código da
                                    unidade <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="unity-user" name="unity-user" type="text" value="" class="form-control"
                                        placeholder="Código da unidade">
                                </div>
                            </div>


                            <div class="form-group row relation-user-group" data-url="get_groups_for_select" hidden>
                                <label for="group-user" class="col-lg-3 control-label text-lg-right pt-2">Agrupamento do
                                    usuário <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="group-user" name="group-user" class="form-control">
                                                <option selected disabled value="">Agrupamento do usuário</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nome-user" class="col-lg-3 control-label text-lg-right pt-2">Nome <span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="nome-user" name="nome-user" type="text" value=""
                                        class="form-control vnome" placeholder="Nome do Usuário" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email-user" class="col-lg-3 control-label text-lg-right pt-2">Email <span
                                        class="required">*</span></label></label>
                                <div class="col-lg-6">
                                    <input id="email-user" name="email-user" type="text" value=""
                                        class="form-control vemail" placeholder="Email">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="senha-user" class="col-lg-3 control-label text-lg-right pt-2">Senha <span
                                        class="required">*</span></label></label>
                                <div class="col-lg-6">
                                    <input id="senha-user" name="senha-user" type="password" value=""
                                        class="form-control vsenha" placeholder="Senha" autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="confirm-user" class="col-lg-3 control-label text-lg-right pt-2">Confirme a
                                    Senha <span class="required">*</span></label></label>
                                <div class="col-lg-6">
                                    <input id="confirm-user" name="confirm-user" type="password" value=""
                                        class="form-control vconfirma" placeholder="Confirme">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="page-user" class="col-lg-3 control-label text-lg-right pt-2">Página do
                                    usuário <span class="required">*</span></label></label>
                                <div class="col-lg-6">
                                    <input id="page-user" name="page-user" type="text" value=""
                                        class="form-control vpage" placeholder="Página do usuário">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Monitoramento<span
                                        class="required">*</span></label>
                                <div class="col-lg-6 align-self-center">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input class="require-one" type="checkbox" id="user-agua"
                                                    name="user-agua">
                                                <label for="user-agua">Água</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-success">
                                                <input class="require-one" type="checkbox" id="user-gas"
                                                    name="user-gas">
                                                <label for="user-gas">Gás</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-warning">
                                                <input class="require-one" type="checkbox" id="user-energia"
                                                    name="user-energia">
                                                <label for="user-energia">Energia</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox-custom checkbox-info">
                                                <input class="require-one" type="checkbox" id="user-nivel"
                                                    name="user-nivel">
                                                <label for="user-nivel">Nível</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="page-user" class="col-lg-3 control-label text-lg-right pt-2">Grupos
                                    adicionais <span class="required">*</span></label></label>
                                <div class="col-lg-6">
                                    <input id="groups-user" name="groups-user" type="text" value=""
                                        class="form-control " placeholder="Grupos do usuário">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="switch" class="col-lg-3 control-label text-lg-right pt-2">Status</label>
                                <div class="col-lg-6">
                                    <div class="switch switch-sm switch-primary" title="Desativar Usuário">
                                        <input type="checkbox" name="switch" id="switch" checked="checked"
                                            data-plugin-ios-switch />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <footer class="card-footer">
                        <div class="text-end">
                            <button class="btn btn-primary btn-salvar-user">Salvar</button>
                            <button type="reset" class="btn btn-reset-user">Limpar</button>
                        </div>
                    </footer>
                </form>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>