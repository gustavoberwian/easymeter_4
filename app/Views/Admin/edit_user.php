<style>
    .switch.disabled {
        opacity: 50%;
        pointer-events: none;
    }
</style>
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
                <form class="form-horizontal form-bordered form-user-edit">
                    <header class="card-header">
                        <h2 class="card-title">Editar Usuário -
                            <?= $usuario->username ?>
                        </h2>
                    </header>
                    <div class="card-body">
                        <div class="tab-form user">

                            <div class="form-group row">
                                <label for="classificacao-user"
                                    class="col-lg-3 control-label text-lg-right pt-2">Classificação do usuário <span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="classificacao-user-edit" name="classificacao-user"
                                                class="form-control" required disabled>
                                                <option selected disabled value="">Classificação do usuário</option>
                                                <option value="entidade" <?php if ($classificacao == 'entidades')
                                                    echo ' selected="selected"'; ?>>Entidade</option>
                                                <option value="agrupamento" <?php if ($classificacao == 'agrupamentos')
                                                    echo ' selected="selected"'; ?>>Agrupamento</option>
                                                <option value="unidade" <?php if ($classificacao == 'unidade')
                                                    echo ' selected="selected"'; ?>>Unidade</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row relation-user-entity-edit" data-url="get_entity_for_select"
                                hidden>
                                <label for="entity-user" class="col-lg-3 control-label text-lg-right pt-2">Entidade do
                                    Usuario <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="entity-user-edit" name="entity-user" class="form-control"
                                                required disabled>
                                                <?php if ($val): ?>
                                                    <option value="<?= $val ?>"><?= $val; ?></option>
                                                <?php else: ?>
                                                    <option selected value="">Entidade do usuário</option>
                                                <?php endif; ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row relation-user-unity-edit" hidden>
                                <label for="unity-user" class="col-lg-3 control-label text-lg-right pt-2">Código da
                                    unidade <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="unity-user-edit" name="unity-user" type="text" value="<?= $val ?>"
                                        class="form-control" placeholder="Código da unidade" disabled>
                                </div>
                            </div>


                            <div class="form-group row relation-user-group-edit" data-url="get_groups_for_select"
                                hidden>
                                <label for="group-user" class="col-lg-3 control-label text-lg-right pt-2">Agrupamento do
                                    usuário <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="select-wrap">
                                            <select id="group-user-edit" name="group-user" class="form-control"
                                                disabled>
                                                <?php if ($val): ?>
                                                    <option value="<?= $val ?>"><?= $val; ?></option>
                                                <?php else: ?>
                                                    <option selected value="<?= $val ?>">Agrupamento do usuário</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nome-user" class="col-lg-3 control-label text-lg-right pt-2">Nome <span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="nome-user" data-uid="<?= $id ?>" name="nome-user" type="text"
                                        value="<?= $usuario->username ?>" class="form-control vnome"
                                        placeholder="Nome do Usuário" <?php if ($readonly != '')
                                            echo ' disabled'; ?>
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <input id="id-user" data-uid="<?= $id ?>" name="id-user" type="text" value="<?= $id ?>"
                                    class="form-control" required hidden>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email-user" class="col-lg-3 control-label text-lg-right pt-2">Email <span
                                    class="required">*</span></label></label>
                            <div class="col-lg-6">
                                <input id="email-user" name="email-user" type="text" value="<?= $email ?>"
                                    class="form-control vemail" <?php if ($readonly != '')
                                        echo ' disabled'; ?>
                                    placeholder="Email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="senha-user" class="col-lg-3 control-label text-lg-right pt-2">Senha </label>
                            <div class="col-lg-6">
                                <button id="alt-senha" <?php if ($readonly != '')
                                    echo ' disabled'; ?>
                                    class="btn btn-primary btn-alt-senha">Alterar senha</button>
                                <input id="senha-user" name="senha-user" disabled type="password" value=""
                                    class="form-control vsenha" placeholder="Senha" <?php if ($readonly != '')
                                        echo ' disabled'; ?> autocomplete="new-password" hidden>
                            </div>
                        </div>

                        <div class="form-group row senha" hidden>
                            <label for="confirm-user" class="col-lg-3 control-label text-lg-right pt-2">Confirme a Senha
                                <span class="required">*</span></label></label>
                            <div class="col-lg-6">
                                <input id="confirm-user" name="confirm-user" type="password" value=""
                                    class="form-control vconfirma" placeholder="Confirme" <?php if ($readonly != '')
                                        echo ' disabled'; ?>>
                                <span><i class="fas fa-close text-danger cancel-alt"></i></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="page-user" class="col-lg-3 control-label text-lg-right pt-2">Página do usuário
                                <span class="required">*</span></label></label>
                            <div class="col-lg-6">
                                <input id="page-user" name="page-user" type="text" value="<?= $usuario->page ?>"
                                    class="form-control vpage" placeholder="Página do usuário" <?php if ($readonly != '')
                                        echo ' disabled'; ?>>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 control-label text-lg-right pt-2">Monitoramento<span
                                    class="required">*</span></label>
                            <div class="col-lg-6 align-self-center">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="checkbox-custom checkbox-primary">
                                            <input class="require-one" type="checkbox" <?php echo ($usuario->inGroup("agua") ? 'checked' : ''); ?> id="user-agua"
                                                name="user-agua" <?php if ($readonly != '')
                                                    echo ' disabled'; ?>>
                                            <label for="user-agua">Água</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox-custom checkbox-success">
                                            <input class="require-one" type="checkbox" <?php echo ($usuario->inGroup("gas") ? 'checked' : ''); ?> id="user-gas"
                                                name="user-gas" <?php if ($readonly != '')
                                                    echo ' disabled'; ?>>
                                            <label for="user-gas">Gás</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox-custom checkbox-warning">
                                            <input class="require-one" type="checkbox" <?php echo ($usuario->inGroup("energia") ? 'checked' : ''); ?> id="user-energia"
                                                name="user-energia" <?php if ($readonly != '')
                                                    echo ' disabled'; ?>>
                                            <label for="user-energia">Energia</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox-custom checkbox-info">
                                            <input class="require-one" type="checkbox" <?php echo ($usuario->inGroup("nivel") ? 'checked' : ''); ?> id="user-nivel"
                                                name="user-nivel" <?php if ($readonly != '')
                                                    echo ' disabled'; ?>>
                                            <label for="user-nivel">Nível</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="page-user" class="col-lg-3 control-label text-lg-right pt-2">Grupos adicionais
                                <span class="required">*</span></label></label>
                            <div class="col-lg-6">
                                <input id="groups-user" name="groups-user" type="text"
                                    value="<?php if ($groups)
                                        echo implode(', ', $groups) ?>" class="form-control "
                                        placeholder="Grupos do usuário" <?php if ($readonly != '')
                                        echo ' disabled'; ?>>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="switch" class="col-lg-3 control-label text-lg-right pt-2">Status</label>
                            <div class="col-lg-6">

                                <div class="switch switch-sm switch-primary <?php if ($readonly != '')
                                    echo 'disabled'; ?>"
                                    title="Desativar Usuário">
                                    <input type="checkbox" name="switch" id="switch" <?php echo ($usuario->active == 1 ? 'checked' : ''); ?> data-plugin-ios-switch />
                                </div>
                            </div>
                        </div>
                    </div>

                    <footer class="card-footer">
                        <div class="text-end">
                            <button class="btn btn-primary btn-editar-user" <?php if ($readonly != '')
                                echo ' hidden disabled'; ?>>Editar</button>
                            <button type="reset" class="btn btn-reset-user" <?php if ($readonly != '')
                                echo ' hidden disabled'; ?>>Limpar</button>
                            <?php if ($readonly != '')
                                echo '<button class="btn btn-primary btn-back-user">Voltar</button>'; ?>
                        </div>
                    </footer>
                </form>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>