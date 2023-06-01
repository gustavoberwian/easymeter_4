<style>
    .container {
        margin-top: 20px;
    }

    .image-preview-input {
        position: relative;
        overflow: hidden;
        margin: 0px;
        color: #333;
        background-color: #fff;
        border-color: #ccc;
    }

    .image-preview-input input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
    }

    .image-preview-input-title {
        margin-left: 2px;
    }

    .image-preview-clear {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
</style>
<section role="main" class="content-body">
    <!-- start: page -->
    <div class="row">
        <div class="col-lg-4 col-xl-4 mb-4 mb-xl-0 mt-4 mt-xl-0">
            <section class="card">
                <div class="card-body">
                    <div class="thumb-info mb-3 text-center">
                        <img src="<?php echo avatar($user->avatar); ?>" class="rounded img-fluid"
                            alt="<?php echo $user->username; ?>">
                        <div><i class="fas fa-edit btn btn-edit-image" data-id="<?= $user->id; ?>"
                                data-img="<?php echo avatar($user->avatar); ?>"></i></i> </div>
                        <div class="thumb-info-title">
                            <span class="thumb-info-inner">
                                <?php echo $user->username; ?>
                            </span>
                            <span class="thumb-info-type">
                                <?php echo $url; ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!$user->inGroup("demo", "superadmin") && $condo && !is_null($condo->logradouro)): ?>
                        <h5 class="mb-2 mt-3">Endereço</h5>
                        <p class="mb-0">
                            <?php echo $condo->logradouro; ?>,
                            <?php echo $condo->numero; ?>
                        </p>
                        <p class="mb-0">
                            <?php echo $condo->bairro; ?> -
                            <?php echo $condo->cidade; ?>/
                            <?php echo $condo->uf; ?>
                        </p>
                        <p class="mb-0">CEP
                            <?php echo $condo->cep; ?>
                        </p>
                    <?php endif; ?>
                    <hr class="dotted short">
                    <h5 class="mb-2 mt-3">Estatísticas</h5>
                    <p class="mb-0">Membro desde
                        <?php echo date("d/m/Y", $user->created_on); ?>
                    </p>
                    <?php if ($user->inGroup('unidades1', 'proprietarios1')): ?>
                        <p class="mb-0">Consumo total medido:</p>
                        <p class="mb-0"><i class="fas fa-tint mx-3"></i> 351.562 litros de água</p>
                        <p class="mb-0"><i class="fas fa-fire mx-3"></i> 5.142 Kg de gás</p>
                        <p class="mb-0"><i class="fas fa-bolt mx-3"></i> 822 Kw de energia</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <div class="col-lg-8 col-xl-8">
            <div class="tabs">
                <ul class="nav nav-tabs tabs-primary">
                <button class="btn btn-light me-4" id='btn-back-last' data-bs-toggle="" data-bs-target="#back" type="button"><i class="fas fa-arrow-left"></i> Voltar</button>
                    <li class="nav-item active">
                        <a class="nav-link" data-toggle="tab">Informações</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="edit" class="tab-pane active">
                        <form id="profile" class="profile p-3" autocomplete="off">
                            <input type="hidden" name="user" value="<?php echo md5("easymeter" . $user->id . "123456"); ?>">
                            <div class="form-row mb-3">
                                <div class="form-group col-md-6">
                                    <label>E-mail principal</label>
                                    <input id="principal" type="text" class="form-control"
                                        value="<?php echo $user->email; ?>" readonly>
                                </div>
                                <?php if (!$condo): ?>
                                    <div class="form-group col-md-6">
                                        <label>Código da Unidade</label>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Telefone</label>
                                    <input type="text" class="form-control telefone" name="telefone" id="telefone"
                                        placeholder="(00) 0000-0000"
                                        value="<?php echo set_value('telefone', $user->telefone); ?>">
                                    <?php echo $validation->getError('telefone'); ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Celular</label>
                                    <input type="text" class="form-control telefone" name="celular" id="celular"
                                        placeholder="(00) 0000-0000"
                                        value="<?php echo set_value('celular', $user->celular); ?>">
                                    <?php echo $validation->getError('celular'); ?>
                                </div>
                            </div>
                            <?php if (!$user->inGroup('administradora')): ?>
                                <div class="form-row">
                                    <div class="form-group col-md-12 email-input">
                                        <label>Emails adicionais</label>
                                        <input name="emails" id="emails" class="form-control"
                                            value="<?php echo ($emails != '') ? $emails : $emails = ''; ?>" />
                                        <span class="help-block mb-0 float-right">Até 3 emails que também receberão
                                            notificações.</span>
                                        <?php if (isset($email_error))
                                            echo $email_error; ?>
                                    </div>
                                </div>
                                <hr class="dotted">
                            <?php endif; ?>
                            <?php if (!$user->inGroup('demo')): ?>
                                <h4 class="mb-3">Alterar Senha</h4>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Nova Senha</label>
                                        <input type="password" class="form-control" name="password" id="password"
                                            autocomplete="new-password">
                                        <?php echo $validation->getError('password'); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Confirmação</label>
                                        <input type="password" class="form-control" name="confirm" id="confirm">
                                        <?php echo $validation->getError('confirm'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="form-row">
                                <div class="col-md-12 mt-3">
                                    <button class="d-none" type="reset">Clear</button>
                                    <button class="btn btn-primary modal-confirm" <?= ($user->inGroup('demo')) ? "disabled" : ""; ?>>Salvar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end: page -->
</section>