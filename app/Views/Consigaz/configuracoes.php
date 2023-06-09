<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header">
        <h2>Configurações</h2>
    </header>

    <div class="row">
        <div class="col-12">
            <ul class="nav nav-pills nav-pills-primary mb-3" role="tablist">
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs active" data-bs-toggle="pill" data-bs-target="#geral" type="button" aria-selected="true" role="tab">Geral</button>
                </li>
                <li class="nav-item configs" role="presentation">
                    <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#usuarios" type="button" aria-selected="false" role="tab" tabindex="-1">Usuários</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content configs">
        <div class="tab-pane fade active show" id="geral" role="tabpanel">
            <div class="row pt-0">
                <div class="col-md-12 mb-4">
                    <section class="card card-easymeter h-100">
                        <form class="form-config-geral">
                            <div class="card-body">
                                <a class="glue-header__logo-link d-flex pb-2" target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=pt_BR&gl=US">
                                    <div class="google-logo">
                                        <svg class="google-logo-svg" role="presentation" aria-hidden="true" viewBox="0 0 74 24">
                                            <path fill="#4285F4" d="M9.24 8.19v2.46h5.88c-.18 1.38-.64 2.39-1.34 3.1-.86.86-2.2 1.8-4.54 1.8-3.62 0-6.45-2.92-6.45-6.54s2.83-6.54 6.45-6.54c1.95 0 3.38.77 4.43 1.76L15.4 2.5C13.94 1.08 11.98 0 9.24 0 4.28 0 .11 4.04.11 9s4.17 9 9.13 9c2.68 0 4.7-.88 6.28-2.52 1.62-1.62 2.13-3.91 2.13-5.75 0-.57-.04-1.1-.13-1.54H9.24z"></path>
                                            <path fill="#EA4335" d="M25 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z"></path>
                                            <path fill="#4285F4" d="M53.58 7.49h-.09c-.57-.68-1.67-1.3-3.06-1.3C47.53 6.19 45 8.72 45 12c0 3.26 2.53 5.81 5.43 5.81 1.39 0 2.49-.62 3.06-1.32h.09v.81c0 2.22-1.19 3.41-3.1 3.41-1.56 0-2.53-1.12-2.93-2.07l-2.22.92c.64 1.54 2.33 3.43 5.15 3.43 2.99 0 5.52-1.76 5.52-6.05V6.49h-2.42v1zm-2.93 8.03c-1.76 0-3.1-1.5-3.1-3.52 0-2.05 1.34-3.52 3.1-3.52 1.74 0 3.1 1.5 3.1 3.54.01 2.03-1.36 3.5-3.1 3.5z"></path>
                                            <path fill="#FBBC05" d="M38 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z"></path>
                                            <path fill="#34A853" d="M58 .24h2.51v17.57H58z"></path>
                                            <path fill="#EA4335" d="M68.26 15.52c-1.3 0-2.22-.59-2.82-1.76l7.77-3.21-.26-.66c-.48-1.3-1.96-3.7-4.97-3.7-2.99 0-5.48 2.35-5.48 5.81 0 3.26 2.46 5.81 5.76 5.81 2.66 0 4.2-1.63 4.84-2.57l-1.98-1.32c-.66.96-1.56 1.6-2.86 1.6zm-.18-7.15c1.03 0 1.91.53 2.2 1.28l-5.25 2.17c0-2.44 1.73-3.45 3.05-3.45z"></path>
                                        </svg>
                                    </div>
                                    <span class="google-logo-2" aria-hidden="true">Authenticator</span>
                                </a>
                                <div class="row">
                                    <p>Necessário ter o aplicativo do <a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=pt_BR&gl=US">Google Authenticator</a> instalado.</p>
                                    <p>Instruções: clique no botão para gerar seu QR Code, em seguida escaneie o código gerado com o Authenticator.</p>
                                    <p class="text-danger">Atenção: o QR Code estará disponível apenas UMA VEZ, tenha certeza de salvá-lo corretamente. Caso necessite visualizá-lo novamente deverá <a class="request-code cur-pointer">entrar em contato</a> conosco.</p>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <button class="btn btn-primary generate-code" <?= !is_null($secret) ? 'style="display: none"' : ''; ?>><i class="fas fa-qrcode" title=""></i> Gerar QR Code</button>
                                        <div class="alert alert-warning request-code-btn" role="alert" <?= is_null($secret) ? 'style="display: none"' : ''; ?>>
                                            Seu QR Code já foi gerado, <a class="request-code cur-pointer">entre em contato</a> caso o necessite novamente.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="usuarios" role="tabpanel">
            <div class="row pt-0 mb-4">
                <div class="col-md-4">
                    <section class="card card-comparativo h-100">
                        <div class="card-body" style="background-color: #03aeef;">
                            <h6 class="card-body-title mb-3 mt-0 text-light">Cliente <i class="float-end fas fa-microchip"></i></h6>
                            <div class="row">
                                <div class="col-lg-12 pl-1">
                                    <select id="sel-entity" name="sel-entity" class="form-control" required>
                                        <option disabled value="">Selecione o cliente</option>
                                        <?php foreach ($clientes as $i => $cliente) : ?>
                                            <option <?= (array_key_first($clientes) == $i) ? 'selected' : '' ?> value="<?= $cliente->id ?>"><?= $cliente->nome ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <section class="card card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <button type="button" data-eid="<?= $cliente->id ?>"  data-name="<?= $cliente->nome ?>" class="btn btn-primary btn-incluir-usuario"><i class="fas fa-user-plus"></i> Incluir Usuário</button>
                    </div>
                    <h2 class="card-title">Usuários</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-click dataTable no-footer display responsive nowrap" id="dt-usuarios" data-url="/consigaz/get_usuarios">
                        <thead>
                        <tr role="row">
                            <th class="text-center"></th>
                            <th class="text-center"></th>
                            <th class="text-center">Nome</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Bloco</th>
                            <th class="text-center">Apto</th>
                            <th class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>
<?php
$data['modal_id'] = 'modalExclui';
$data['modal_title'] = 'Você tem certeza?';
$data['modal_message'] = 'Deseja realmente excluir este Usuário?';
$data['button'] = array('Excluir', 'Cancelar');
echo view('modals/confirm', $data);
?>