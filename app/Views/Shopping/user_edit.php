<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header" data-group="<?=$group_id ?>">
        <h2><?= $group->group_name; ?> - Editar Usuário</h2>
    </header>

    <div class="row">
        <div class="col">
            <section class="card card-easymeter">
                <form class="form-horizontal form-bordered form-user">
                    <input <?= $readonly ? 'readonly' : ''; ?> type="hidden" value="<?= $user_info->id; ?>" id="user-id" name="user_id">
                    <header class="card-header">
                        <div class="card-actions buttons"></div>
                        <h2 class="card-title">Editar Usuário - <?= $user_info->username ?></h2>
                    </header>
                    <div class="card-body">
                        <div class="tab-form cadastro">
                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="nome-user">Nome </label>
                                <div class="col-lg-6">
                                    <input <?= $readonly ? 'readonly' : ''; ?> id="nome-user" name="nome-user" type="text" value="<?= $user_info->username ?>" class="form-control" placeholder="Nome do usuário">
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="email-user">Email <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input <?= $readonly ? 'readonly' : ''; ?> id="email-user" name="email-user" value="<?= $user_info->email ?>" placeholder="exemplo@exemplo.com" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="telefone-user">Contato</label>
                                <div class="col-md-3">
                                    <input <?= $readonly ? 'readonly' : ''; ?> id="telefone-user" name="telefone-user" value="<?= $user_info->telefone ?>" placeholder="Telefone" class="form-control ">
                                </div>
                                <div class="col-md-3">
                                    <input <?= $readonly ? 'readonly' : ''; ?> id="celular-user" name="celular-user" value="<?= $user_info->celular ?>" placeholder="Celular" class="form-control ">
                                </div>
                            </div>

                            <?php if (!$readonly): ?>
                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="password-user">Senha</label>
                                <div class="col-lg-3">
                                    <input disabled id="password-user" name="password_user" type="password" value="" class="form-control" placeholder="Senha" required><i class="fa fa-eye show-password d-none cur-pointer" style="position: absolute; right: 20px; top: 13px;"></i>
                                </div>
                                <label class="col-lg-3 control-label text-lg-start cur-pointer change-password" for="password-confirmation-user"><button type="button" class="btn btn-success">Alterar senha</button></label>
                                <div class="col-lg-3">
                                    <input disabled id="password-confirmation-user" name="password_confirmation_user" type="password" value="" class="form-control d-none" placeholder="Confirmação de senha" required><i class="fa fa-eye show-password d-none cur-pointer" style="position: absolute; right: 20px; top: 13px;"></i>
                                </div>
                                <label class="col-lg-1 control-label d-none text-danger text-lg-start cur-pointer cancel-change-password" for="password-confirmation-user"><button type="button" class="btn btn-danger">Cancelar</button></label>
                            </div>
                            <?php endif; ?>

                            <?php if ($user_info->inGroup("unity", "shopping")): ?>
                                <div class="form-group row pb-3">
                                    <label class="col-lg-3 control-label text-lg-end pt-2" for="username-user">Permissões</label>
                                    <div class="col-lg-2 d-flex align-items-center justify-content-center">
                                        <div class="switch switch-sm switch-primary">
                                            <label for="acessar-lancamentos">Lançamentos</label>
                                            <input <?= $user_info->acessar_lancamentos ? 'checked' : ''; ?> type="checkbox" name="acessar_lancamentos" id="acessar-lancamentos" data-plugin-ios-switch>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-flex align-items-center justify-content-center">
                                        <div class="switch switch-sm switch-primary">
                                            <label for="acessar-engenharia">Engenharia</label>
                                            <input <?= $user_info->acessar_engenharia ? 'checked' : ''; ?> type="checkbox" name="acessar_engenharia" id="acessar-engenharia" data-plugin-ios-switch>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-flex align-items-center justify-content-center">
                                        <div class="switch switch-sm switch-primary">
                                            <label for="baixar-planilhas">Planilhas</label>
                                            <input <?= $user_info->baixar_planilhas ? 'checked' : ''; ?> type="checkbox" name="baixar_planilhas" id="baixar-planilhas" data-plugin-ios-switch>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php /*if ($this->ion_auth->in_group("entity_shopping")): */?><!--
                                <div class="form-group row pb-3">
                                    <label class="col-lg-3 control-label text-lg-end pt-2" for="select-shopping">Shopping <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <select <?/*= $readonly ? 'readonly disabled' : ''; */?> id="select-shopping" name="select-shopping" class="form-select form-control" required>
                                            <option value="" disabled selected>Selecione o Shopping</option>
                                            <?php /*foreach ($shoppings as $shopping): */?>
                                                <option value="<?/*= $shopping->bloco_id */?>" <?/*= $user_info->bloco_id === $shopping->bloco_id ? 'selected' : '' */?> ><?/*= $shopping->nome */?></option>
                                            <?php /*endforeach; */?>
                                        </select>
                                    </div>
                                </div>
                            --><?php /*endif; */?>

                            <?php if (!$user_info->inGroup("unity", "shopping")) : ?>
                                <div class="form-group row pb-3">
                                    <label class="col-lg-3 control-label text-lg-end pt-2" for="select-shopping">Loja</label>
                                    <div class="col-lg-6">
                                        <?php if ($readonly): ?>
                                            <input <?= $readonly ? 'readonly' : ''; ?> id="select-loja" name="select-loja" type="text" value="<?= $user_info->unidade_nome ?>" class="form-control" placeholder="Nome da unidade" required>
                                        <?php else: ?>
                                        <select id="select-loja" name="select-loja" class="form-select form-control">
                                            <?php if ($user_info->unidade_nome): ?>
                                                <option value="<?= $user_info->unidade_id; ?>" selected><?= $user_info->unidade_nome; ?></option>
                                            <?php else: ?>
                                                <option value="" selected>Selecione a Loja</option>
                                            <?php endif; ?>
                                        </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-lg-9 text-end">
                                    <button type="button" class="btn btn-primary btn-redir-edit-user mr-3"><?= $readonly ? 'Editar' : 'Salvar'; ?></button>
                                    <button type="reset" class="btn btn-default btn-back">Voltar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="mt-3">
        <table class="text-dark w-100">
            <tbody><tr>
                <td>
                    <img src="<?php echo base_url('assets/img/' . $user->condo->image_url); ?>" alt="<?= ""; ?>" class="mb-4" height="35"/>
                </td>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="35"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!-- end: page -->
</section>