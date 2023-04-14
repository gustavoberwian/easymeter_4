<!doctype html>
<html class="fixed sidebar-left-big-icons">
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
		<link rel="stylesheet" href="<?= base_url('vendor/bootstrap/css/bootstrap.css'); ?>" />
		<link rel="stylesheet" href="<?= base_url('vendor/animate/animate.compat.css'); ?>">
		<link rel="stylesheet" href="<?= base_url('vendor/font-awesome/css/all.min.css'); ?>" />
		<link rel="stylesheet" href="<?= base_url('vendor/boxicons/css/boxicons.min.css'); ?>" />
        <link rel="stylesheet" href="<?= base_url('vendor/datatables/media/css/dataTables.bootstrap5.css'); ?>" />
        <link rel="stylesheet" href="<?= base_url('vendor/pnotify/pnotify.custom.css'); ?>" />

        <!-- Specific Page Vendor -->
        <?php if(in_array($method, array('index'))) : ?>
            <link rel="stylesheet" href="<?php echo base_url('vendor/daterangepicker/daterangepicker.css'); ?>" />
            <link rel="stylesheet" href="<?php echo base_url('vendor/apexcharts/dist/apexcharts.css'); ?>" />
        <?php endif; ?>

		<!-- Theme CSS -->
		<link rel="stylesheet" href="<?= base_url('assets/css/theme.css'); ?>" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="<?= base_url('assets/css/skin.css'); ?>" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="<?= base_url('assets/css/custom.css'); ?>">

		<!-- Head Libs -->
		<script src="<?= base_url('vendor/modernizr/modernizr.js'); ?>"></script>

	</head>

    <body>

        <section class="body">

            <header class="header">

                <div class="logo-container">
					<a href="/" class="logo">
                        <img src="<?= base_url('assets/img/logo.png'); ?>" height="35" alt="Easymeter" />
					</a>

					<div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fas fa-bars" aria-label="Toggle sidebar"></i>
					</div>

				</div>

                <div class="header-right">

					<span class="separator"></span>

					<div id="userbox" class="userbox">
						<a href="#" data-bs-toggle="dropdown">
							<figure class="profile-picture">
								<img src="<?= avatar($user->avatar); ?>" alt="<?= $user->username; ?>" class="rounded-circle" />
							</figure>
							<div class="profile-info">
								<span class="name"><?= $user->nickname; ?></span>
								<span class="role">Indústria</span>
							</div>

							<i class="fa custom-caret"></i>
						</a>

						<div class="dropdown-menu">
							<ul class="list-unstyled mb-2">
								<li class="divider"></li>
								<!--<li>
									<a role="menuitem" tabindex="-1" href="<?/*= site_url('industria/profile'); */?>"><i class="bx bx-user-circle"></i> Minha Conta</a>
								</li>-->
								<li>
									<a role="menuitem" tabindex="-1" href="<?= site_url('logout'); ?>"><i class="bx bx-power-off"></i> Sair</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">
