<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.useMagicLink') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">
        <meta http-equiv="Content-Language" content="pt-BR">

		<title>Easymeter</title>
		<meta name="keywords" content="Easymeter" />
		<meta name="description" content="Easymeter - Controle e Economia">
		<meta name="author" content="www.easymeter.com.br">

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
	</head>

<body>
    <section class="body-sign">
			<div class="center-sign">
				<a href="/" class="logo float-start">
					<img src="<?= base_url('assets/img/logo.png'); ?>" height="54" alt="Easymeter" />
				</a>

				<div class="panel card-sign">
					<div class="card-title-sign mt-3 text-end">
						<h2 class="title text-uppercase font-weight-bold m-0"><i class="bx bx-user-circle me-1 text-6 position-relative top-5"></i> Entrar</h2>
					</div>
					<div class="card-body">
                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <?= $error ?>
                                <br>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= session('errors') ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <?php if (session('message') !== null) : ?>
                <div class="alert alert-success" role="alert"><?= session('message') ?></div>
                <?php endif ?>




                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <?= $error ?>
                                <br>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= session('errors') ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

            <form action="<?= url_to('magic-link') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email -->

                <div class="input-group mb-3">
            
                    <input type="email" class="form-control" name="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>"
                     value="<?= old('email', auth()->user()->email ?? null) ?>" required     />
                

                
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.send') ?></button>
                        </div>
                       

                 <p class="text-center">Lembrou? <a href="<?= url_to('login') ?>"><?= lang('Entrar') ?></a></p>
            </form>
 





</div>
              </div>
				<p class="text-center text-muted mt-3 mb-3">&copy; Copyright 2017-<?= date("Y"); ?>. Todos os direitos reservados.</p>
			</div>
		</section>
		<!-- end: page -->

		<!-- Vendor -->
		<script src="<?= base_url('vendor/jquery/jquery.js'); ?>"></script>
		<script src="<?= base_url('vendor/jquery-browser-mobile/jquery.browser.mobile.js'); ?>"></script>
		<script src="<?= base_url('vendor/popper/umd/popper.min.js'); ?>"></script>
		<script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
		<script src="<?= base_url('vendor/common/common.js'); ?>"></script>
		<script src="<?= base_url('vendor/nanoscroller/nanoscroller.js'); ?>"></script>
        <script src="<?= base_url('vendor/jquery-validation/jquery.validate.min.js'); ?>"></script>

		<!-- Page Specific -->
		<script src="<?= base_url('assets/js/pages/auth/login.js'); ?>"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="<?= base_url('assets/js/theme.js'); ?>"></script>

		<!-- Theme Custom -->
		<script src="<?= base_url('assets/js/custom.js'); ?>"></script>

		<!-- Theme Initialization Files -->
		<script src="<?= base_url('assets/js/theme.init.js'); ?>"></script>

	</body>


