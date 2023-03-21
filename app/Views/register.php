<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>
<?php
    $isTrc = false;
    $u = explode('.', $_SERVER['HTTP_HOST']);
    if ($u[0] == "trc")
        $isTrc = true;
?>

<!doctype html>
<html class="fixed">
	<head>
		<!-- Basic -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="pt-BR">	

		<title>Easymeter</title>
		<meta name="keywords" content="Easymeter" />
		<meta name="description" content="Easymeter - Controle e Economia">
		<meta name="author" content="www.easymeter.com.br">

        <html lang="pt-BR" xml:lang="pt-BR" xmlns= "http://www.w3.org/1999/xhtml">
        <meta http-equiv="Content-Language" content="pt-BR">

		<!-- Favicon -->
		<link rel="shortcut icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon" />
          
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		<link rel="shortcut icon" href="<?= base_url('favicon.png'); ?>" type="image/x-icon" />

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="<?= base_url('vendor/bootstrap/css/bootstrap.css'); ?>">
		<link rel="stylesheet" href="<?= base_url('vendor/animate/animate.compat.css'); ?>">
		<link rel="stylesheet" href="<?= base_url('vendor/font-awesome/css/all.min.css'); ?>">
		<link rel="stylesheet" href="<?= base_url('vendor/boxicons/css/boxicons.min.css'); ?>">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="<?= base_url('assets/css/theme.css'); ?>" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="<?= base_url('assets/css/skin.css'); ?>" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="<?= base_url('assets/css/custom.css'); ?>" />

		<!-- Head Libs -->
		<script src="<?= base_url('vendor/modernizr/modernizr.js'); ?>"></script>
		<!-- Vendor CSS -->
		<link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap/css/bootstrap.css'); ?>">
		<link rel="stylesheet" href="<?php echo base_url('vendor/fontawesome/css/all.min.css'); ?>">
		<!-- Theme CSS -->
		<link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>" />
		<link rel="stylesheet" href="<?php echo base_url('assets/css/skin.css'); ?>" />
		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
        <!-- Head Libs -->
	</head>
    <Style>

 .wizard-progress, html.dark .wizard-progress {
    margin: 0 15px;
          }
    .ml-3, .mx-3 {
    margin-left: 1rem !important;
}
.mr-3, .mx-3 {
    margin-right: 1rem !important;
}
*, *::before, *::after {
    box-sizing: border-box;
}

element.style {
}
 .wizard-progress .steps-progress, html.dark .wizard-progress .steps-progress {
    height: 2px;
    margin: 0 38px;
    position: relative;
    top: 15px;
    background: #CCC;
}
 .wizard-progress .wizard-steps, html.dark .wizard-progress .wizard-steps {
list-style: none;
    margin: 0;
    padding: 15px 0 0;
    display: inline-block;
    width: 100%;
    font-size: 0;
    text-align: justify;
}

 .wizard-progress .wizard-steps li, html.dark .wizard-progress .wizard-steps li {
    display: inline-block;
    vertical-align: top;
    min-width: 50px;
    max-width: 100px;
}
    </Style>
   
	<body>
		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
            <a href="/" class="logo float-start">
					<img src="<?= base_url('assets/img/logo.png'); ?>" height="54" alt="Easymeter" />
				</a>

				<div class="panel card-sign">
                <div class="card-title-sign mt-3 text-end">
						<h2 class="title text-uppercase font-weight-bold m-0"><i class="bx bx-user-circle me-1 text-6 position-relative top-5"></i> Entrar</h2>
					</div>

                    <section class="card form-wizard" id="w3">
                        <div class="card-body">
                            <div class="wizard-progress">
                                <div class="steps-progress">
                                    <div class="progress-indicator"></div>
                                </div>
                                <ul class="nav">
                                    <li class="nav-item active">
                                        <a class="nav-link" href="#w3-acesso" data-toggle="tab"><span>1</span>Acesso</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#w3-unidade" data-toggle="tab"><span>2</span>Unidade</a>
                                    </li>
                                </ul>
                            </div>
                            <form class="form-horizontal" action="<?= url_to('register') ?>" method="post">
                                <input type="hidden" name="cid" value="">
                                <input type="hidden" name="uid" value="">

                                <div class="tab-content p-0">
                                    <div id="w3-acesso" class="tab-pane active">
                                     
                                        <div class="form-group mb-3">
								            <label>E-mail</label>
                                            <input name="email" id="email" type="email" class="form-control form-control-lg" required autofocus tabindex="1" value=""/>
                                        </div>
                                        <div class="form-group mb-0">
                                            <div class="row">
                                                <div class="col-sm-6 mb-3">
                                                    <label>Senha</label>
                                                    <input name="password" id="password" type="password" minlength="6" class="form-control form-control-lg" required tabindex="2" value=""/>
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <label>Confirmação de senha</label>
                                                    <input name="password_confirm" id="password_confirm" minlength="6" type="password" class="form-control form-control-lg" required tabindex="3" value="" equalTo="#password"/>
                                                </div>
                                            </div>
                                        </div>
<?php /* ?>                                        
               							<span class="my-3 line-thru text-center text-uppercase">
                                            <span>ou</span>
                                        </span>
                                        <div class="mb-3 text-center">
                                            <a class="btn btn-facebook" href="<?=getFacebookLoginUrl('auth/fb_callback')?>"><i class="fab fa-facebook mr-2"></i> Entrar pelo Facebook</a>
                                        </div>
<?php */ ?>                                        
                                    </div>

                                    <div id="w3-unidade" class="tab-pane">
                                        <div class="alert alert-danger" style="display:none">
                                            <ul class="login-message">
                                        </div>
                                        <div class="form-group mb-3">
								            <label>Nome Completo</label>
                                            <input name="nome" id="nome" type="text" class="form-control form-control-lg vnome" required tabindex="4" value=""/>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Telefone</label>
                                            <input name="telefone" id="telefone" type="text" class="form-control form-control-lg vtelefone" data-msg-required="Campo Obrigatório" placeholder="__ ____-____" maxlength="15" required autofocus tabindex="5" value=""/>
							            </div>
                                        <div class="form-group mb-3">
								            <label>PIN</label>
                                            <input name="codigo" id="codigo" type="text" class="form-control form-control-lg" required tabindex="6" value=""/>
							            </div>
                                        <div class="form-group mb-0">
                                            <div class="row">
                                                <div class="col-sm-6 mb-3">
                                                    <label>Bloco</label>
                                                    <input name="bloco" id="bloco" type="text" class="form-control form-control-lg" tabindex="7" value=""/>
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <label>Apartamento</label>
                                                    <input name="apto" id="apto" type="text" class="form-control form-control-lg" required tabindex="8" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="card-footer">
                            <ul class="pager">
                                <li class="back">
                                    <a class="btn-link login" href="<?php echo site_url('login'); ?>">Login</a>
                                </li>
                                <li class="previous disabled">
                                    <a class="btn-link"><i class="fas fa-angle-left"></i> Anterior</a>
                                </li>
                                <li class="finish hidden float-right">
                                    <a class="btn-link">Finalizar</a>
                                </li>
                                <li class="next">
                                    <a class="btn-link">Próxima <i class="fas fa-angle-right"></i></a>
                                </li>
                            </ul>
                        </div>
                    </section>
				</div>
				<p class="text-center text-muted mt-3 mb-3">© Copyright 2017-<?php echo date('Y'); ?>. Todos os direitos reservados.</p>
			</div>
		</section>
		<!-- end: page -->

		<!-- Vendor -->
		<script src="<?php echo base_url('vendor/jquery/jquery.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/jquery-browser-mobile/jquery.browser.mobile.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/bootstrap/js/bootstrap.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/nanoscroller/nanoscroller.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/jquery-placeholder/jquery.placeholder.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/jquery-validation/jquery.validate.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/jquery-mask-plugin/jquery.mask.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/bootstrap-wizard/jquery.bootstrap.wizard.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/spinner/js/spinner.js'); ?>"></script>


		<!-- Theme Base, Components and Settings -->
        <script src="<?php echo base_url('assets/js/admin.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>

		<!-- Page Specific -->
        <script src="<?php echo base_url('assets/js/pages/auth/completar.js'); ?>"></script>

	</body>
</html>