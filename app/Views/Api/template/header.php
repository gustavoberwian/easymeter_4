<!doctype html>
<html class="sidebar-left-big-icons">

<head>
		<!-- Basic -->
		<meta charset="UTF-8">

        <title>Easymeter</title>
	    <meta name="keywords" content="Easymeter" />
	    <meta name="description" content="Easymeter - Controle e Economia">
	    <meta name="author" content="www.easymeter.com.br">

        <!-- Favicon -->
        <link rel="shortcut icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon" />        

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
        -->

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap/css/bootstrap.css"); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/animate/animate.compat.css"); ?>">
		<link rel="stylesheet" href="<?php echo base_url("vendor/boxicons/css/boxicons.min.css"); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/magnific-popup/magnific-popup.css"); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css"); ?>" />
        <link rel="stylesheet" href="<?php echo base_url("vendor/daterangepicker/daterangepicker.css"); ?>">
        <link rel="stylesheet" href="<?php echo base_url('vendor/apexcharts/dist/apexcharts.css'); ?>" />
        <link rel="stylesheet" href="<?php echo base_url('vendor/pnotify/pnotify.custom.css'); ?>" />

        <?php if (in_array($method, array('profile'))) : ?>
            <link rel="stylesheet" href="<?php echo base_url('vendor/croppie/croppie.css'); ?>" />
            <link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-fileupload/bootstrap-fileupload.min.css'); ?>" />
        <?php endif; ?>

		<!-- Theme CSS -->
		<link rel="stylesheet" href="<?php echo base_url("assets/css/theme.css"); ?>" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="<?php echo base_url("assets/css/custom.css"); ?>">

        <link rel="stylesheet" href="<?php echo base_url('vendor/select2/css/select2.css'); ?>" />
        <link rel="stylesheet" href="<?php echo base_url('vendor/select2-bootstrap-theme/select2-bootstrap.min.css'); ?>" />

		<!-- Head Libs -->
		<script src="<?php echo base_url("vendor/modernizr/modernizr.js"); ?>"></script>

    <?php if (file_exists('assets/css/pages/' . $class . '/' . $method . '.css')) echo '<link rel="stylesheet" href="' . base_url('assets/css/pages/' . $class . '/' . $method . '.css') . '"/>'; ?>

	</head>

    <body>

        <div class="preloader">
            <div class="speeding-wheel"></div>
        </div>

		<section class="body">

			<!-- start: header -->
			<header class="header d-print-none">
				<div class="logo-container">
					<a href="<?php echo site_url('/'); ?>" class="logo">
						<img src="<?php echo base_url('assets/img/logo.png'); ?>" height="35" alt="Easymeter" />
					</a>

					<div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fas fa-bars" aria-label="Toggle sidebar"></i>
					</div>

				</div>

				<!-- start: search & user box -->
				<div class="header-right">
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">