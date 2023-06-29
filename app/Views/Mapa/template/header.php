<!DOCTYPE html>
<html id="header" class="sidebar-left-big-icons" lang="">

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

    <!-- Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <!-- Vendor -->
    <link type="text/css" rel="stylesheet" href="<?= base_url('vendor/maptalks/maptalks.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?= base_url('assets/css/maptalks/custom.css'); ?>" />
    <link rel="stylesheet" href="<?= base_url('vendor/datatables/media/css/dataTables.bootstrap5.css'); ?>" />
    <link rel="stylesheet" href="<?= base_url('vendor/datatables/responsive/css/responsive.bootstrap5.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap/css/bootstrap.css"); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/animate/animate.compat.css"); ?>">
    <link rel="stylesheet" href="<?php echo base_url("vendor/font-awesome/css/all.min.css"); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/font-awesome-6/css/all.min.css"); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/boxicons/css/boxicons.min.css"); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/magnific-popup/magnific-popup.css"); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css"); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap-multiselect/css/bootstrap-multiselect.css"); ?>" />
    <link rel="stylesheet" href="<?php echo base_url("vendor/daterangepicker/daterangepicker.css"); ?>">

    <link rel="stylesheet" href="<?php echo base_url('vendor/apexcharts/dist/apexcharts.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('vendor/pnotify/pnotify.custom.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-multiselect/css/bootstrap-multiselect.css'); ?>" />

    <link rel="stylesheet" href="<?php echo base_url('vendor/croppie/croppie.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-fileupload/bootstrap-fileupload.min.css'); ?>" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/theme.css"); ?>" />

    <!-- Skin CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/skin.css"); ?>" />

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/custom.css"); ?>">
    <link rel="stylesheet" href="<?php echo base_url("assets/css/energy.css"); ?>">

    <link rel="stylesheet" href="<?php echo base_url('vendor/select2/css/select2.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('vendor/select2-bootstrap-theme/select2-bootstrap.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.css'); ?>" />

    <!-- Custom Profile CSS -->
    <?php if(service('uri')->getSegment(2) === 'profile') : ?>
        <link rel="stylesheet" href="<?php echo base_url("assets/css/profile.css"); ?>">
    <?php endif; ?>

    <!-- Head Libs -->
    <script src="<?php echo base_url("vendor/modernizr/modernizr.js"); ?>"></script>

    <script type="text/javascript" src="<?php echo base_url("vendor/three/three.js"); ?>"></script>
    <script type="text/javascript"
            src="<?php echo base_url("vendor/three/TrackballControls.js"); ?>"></script>
</head>
<body>

    <div class="preloader">
        <div class="speeding-wheel"></div>
    </div>

    <section class="body">

        <header class="header d-print-none">
            <div class="logo-container">
                <a href="<?php echo site_url('/'); ?>" class="logo">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" height="35" alt="Easymeter" />
                </a>

                <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
                    <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
                </div>
            </div>
            <div class="header-right">
                <span class="separator"></span>
                <div id="userbox" class="userbox" data-uid="<?= md5("easymeter" . $user->id . "123456"); ?>" data-id="<?= $user->id; ?>">
                    <a href="#" data-bs-toggle="dropdown">
                        <figure class="profile-picture">
                            <img src="<?php echo avatar($user->avatar); ?>" alt="<?php echo $user->username; ?>" class="rounded-circle" />
                        </figure>
                        <div class="profile-info">
                            <span class="name"><?= $user->nickname; ?></span>
                            <span class="role"><?= $user->description; ?></span>
                        </div>

                        <i class="fa custom-caret"></i>
                    </a>

                    <div class="dropdown-menu">
                        <ul class="list-unstyled mb-2">
                            <li class="divider"></li>
                            <li>
                                <a role="menuitem" tabindex="-1" href="<?php echo site_url("/mapa/profile/"); ?>"><i class="fas fa-user btn-profile"></i> Minha Conta</a>
                            </li>
                            <li>
                                <a role="menuitem" tabindex="-1" href="<?php echo site_url('logout'); ?>"><i class="fas fa-power-off"></i> Sair</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="inner-wrapper">