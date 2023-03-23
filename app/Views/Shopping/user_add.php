<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header" data-group="<?=$group_id ?>">
        <h2><?= $group->group_name; ?> - Criar Usuário</h2>
    </header>

    <img src="<?php echo base_url('assets/img/logo-north.png'); ?>" alt="<?= "North"; ?>" class="mb-4" height="80"/>

    <div class="row">
        <div class="col">
            <section class="card card-easymeter">
                <form class="form-horizontal form-bordered form-user">
                    <header class="card-header">
                        <div class="card-actions buttons"></div>
                        <h2 class="card-title">Incluir Usuário</h2>
                    </header>
                    <div class="card-body">
                        <div class="tab-form cadastro">
                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="nome-user">Nome </label>
                                <div class="col-lg-6">
                                    <input id="nome-user" name="nome-user" type="text" value="" class="form-control" placeholder="Nome do usuário">
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="email-user">Email <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input id="email-user" name="email-user" value="" placeholder="exemplo@exemplo.com" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="telefone-user">Contato</label>
                                <div class="col-md-3">
                                    <input id="telefone-user" name="telefone-user" value="" placeholder="Telefone" class="form-control ">
                                </div>
                                <div class="col-md-3">
                                    <input id="celular-user" name="celular-user" value="" placeholder="Celular" class="form-control ">
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="username-user">Usuário <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="username-user" name="username-user" type="text" value="" class="form-control" placeholder="Seu nome de usuário" required>
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="password-user">Senha <span class="required">*</span></label>
                                <div class="col-lg-3 password-div">
                                    <input id="password-user" name="password_user" type="password" value="" class="form-control" placeholder="Senha" required><i class="fa fa-eye show-password d-none cur-pointer" style="position: absolute; right: 20px; top: 13px;"></i>
                                </div>
                                <div class="col-lg-3 password-div">
                                    <input id="password-confirmation-user" name="password_confirmation_user" type="password" value="" class="form-control" placeholder="Confirmação de senha" required><i class="fa fa-eye show-password d-none cur-pointer" style="position: absolute; right: 20px; top: 13px;"></i>
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="username-user">Permissões</label>
                                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                                    <div class="switch switch-sm switch-primary">
                                        <label for="acessar-lancamentos">Lançamentos</label>
                                        <input type="checkbox" name="acessar_lancamentos" id="acessar-lancamentos" data-plugin-ios-switch>
                                    </div>
                                </div>
                                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                                    <div class="switch switch-sm switch-primary">
                                        <label for="acessar-engenharia">Engenharia</label>
                                        <input type="checkbox" name="acessar_engenharia" id="acessar-engenharia" data-plugin-ios-switch>
                                    </div>
                                </div>
                                <div class="col-lg-2 d-flex align-items-center justify-content-center">
                                    <div class="switch switch-sm switch-primary">
                                        <label for="baixar-planilhas">Planilhas</label>
                                        <input type="checkbox" name="baixar_planilhas" id="baixar-planilhas" data-plugin-ios-switch>
                                    </div>
                                </div>
                            </div>

                            <?php /*if ($this->ion_auth->in_group("entity_shopping")): */?><!--
                                <div class="form-group row pb-3">
                                    <label class="col-lg-3 control-label text-lg-end pt-2" for="select-shopping">Shopping <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <select id="select-shopping" name="select-shopping" class="form-select form-control" required>
                                            <option value="" disabled selected>Selecione o Shopping</option>
                                            <?php /*foreach ($shoppings as $shopping): */?>
                                                <option value="<?/*= $shopping->bloco_id */?>" ><?/*= $shopping->nome */?></option>
                                            <?php /*endforeach; */?>
                                        </select>
                                    </div>
                                </div>
                            --><?php /*endif; */?>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2" for="select-shopping">Loja</label>
                                <div class="col-lg-6">
                                    <select id="select-loja" name="select-loja" class="form-select form-control">
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-9 text-end">
                                    <button class="btn btn-primary btn-incluir-user mr-3">Salvar</button>
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
                    <img src="<?php echo base_url('assets/img/logo-ancar.png'); ?>" alt="<?= "Ancar"; ?>" class="mb-4" height="35"/>
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