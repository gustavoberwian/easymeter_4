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
        <link rel="stylesheet" href="<?= base_url('vendor/datatables/media/css/dataTables.bootstrap5.css'); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap/css/bootstrap.css"); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/animate/animate.compat.css"); ?>">
		<link rel="stylesheet" href="<?php echo base_url("vendor/font-awesome/css/all.min.css"); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/boxicons/css/boxicons.min.css"); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/magnific-popup/magnific-popup.css"); ?>" />
		<link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css"); ?>" />
<!--		<link rel="stylesheet" href="<?php echo base_url("vendor/jquery-ui/jquery-ui.css"); ?>" /> -->
<!--		<link rel="stylesheet" href="<?php echo base_url("vendor/jquery-ui/jquery-ui.theme.css"); ?>" /> -->
		<link rel="stylesheet" href="<?php echo base_url("vendor/bootstrap-multiselect/css/bootstrap-multiselect.css"); ?>" />
<!--		<link rel="stylesheet" href="<?php echo base_url("vendor/morris/morris.css"); ?>" /> -->
        <link rel="stylesheet" href="<?php echo base_url("vendor/daterangepicker/daterangepicker.css"); ?>">
        <!--<link rel="stylesheet" href="<?php echo base_url('vendor/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.css'); ?>" /> -->

        <link rel="stylesheet" href="<?php echo base_url('vendor/apexcharts/dist/apexcharts.css'); ?>" />
        <link rel="stylesheet" href="<?php echo base_url('vendor/pnotify/pnotify.custom.css'); ?>" />
		<link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-multiselect/css/bootstrap-multiselect.css'); ?>" />

        <?php if (in_array($method, array('profile'))) : ?>
            <link rel="stylesheet" href="<?php echo base_url('vendor/croppie/croppie.css'); ?>" />
            <link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-fileupload/bootstrap-fileupload.min.css'); ?>" />
        <?php endif; ?>

		<!-- Theme CSS -->
		<link rel="stylesheet" href="<?php echo base_url("assets/css/theme.css"); ?>" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="<?php echo base_url("assets/css/skin.css"); ?>" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="<?php echo base_url("assets/css/custom.css"); ?>">
        <link rel="stylesheet" href="<?php echo base_url("assets/css/energy.css"); ?>">

        <link rel="stylesheet" href="<?php echo base_url('vendor/select2/css/select2.css'); ?>" />
        <link rel="stylesheet" href="<?php echo base_url('vendor/select2-bootstrap-theme/select2-bootstrap.min.css'); ?>" />
		

		<!-- Head Libs -->
		<script src="<?php echo base_url("vendor/modernizr/modernizr.js"); ?>"></script>

			<!-- Custom Profile CSS -->
			<?php if(service('uri')->getSegment(2) === 'profile') : ?>
		<link rel="stylesheet" href="<?php echo base_url("assets/css/profile.css"); ?>">
		<link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.css'); ?>" />
		<?php endif; ?>
		
		

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
                    <!--
					<form action="pages-search-results.html" class="search nav-form">
						<div class="input-group">
							<input type="text" class="form-control" name="q" id="q" placeholder="Search...">
							<button class="btn btn-default" type="submit"><i class="bx bx-search"></i></button>
						</div>
					</form>

					<span class="separator"></span>
                    -->
					<!--<ul class="notifications">
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-bs-toggle="dropdown">
								<i class="fa fa-envelope"></i>
								<span class="badge">3</span>
							</a>

							<div class="dropdown-menu notification-menu large">
								<div class="notification-title">
									<span class="float-end badge badge-default">3</span>
									Tasks
								</div>

								<div class="content">
									<ul>
										<li>
											<p class="clearfix mb-1">
												<span class="message float-start">Generating Sales Report</span>
												<span class="message float-end text-dark">60%</span>
											</p>
											<div class="progress progress-xs light">
												<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
											</div>
										</li>

										<li>
											<p class="clearfix mb-1">
												<span class="message float-start">Importing Contacts</span>
												<span class="message float-end text-dark">98%</span>
											</p>
											<div class="progress progress-xs light">
												<div class="progress-bar" role="progressbar" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100" style="width: 98%;"></div>
											</div>
										</li>

										<li>
											<p class="clearfix mb-1">
												<span class="message float-start">Uploading something big</span>
												<span class="message float-end text-dark">33%</span>
											</p>
											<div class="progress progress-xs light mb-1">
												<div class="progress-bar" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%;"></div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-bs-toggle="dropdown">
								<i class="fa fa-bell"></i>
								<span class="badge">4</span>
							</a>

							<div class="dropdown-menu notification-menu">
								<div class="notification-title">
									<span class="float-end badge badge-default">230</span>
									Messages
								</div>

								<div class="content">
									<ul>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/img/!sample-user.jpg" alt="Joseph Doe Junior" class="rounded-circle" />
												</figure>
												<span class="title">Joseph Doe</span>
												<span class="message">Lorem ipsum dolor sit.</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/img/!sample-user.jpg" alt="Joseph Junior" class="rounded-circle" />
												</figure>
												<span class="title">Joseph Junior</span>
												<span class="message truncate">Truncated message. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet lacinia orci. Proin vestibulum eget risus non luctus. Nunc cursus lacinia lacinia. Nulla molestie malesuada est ac tincidunt. Quisque eget convallis diam, nec venenatis risus. Vestibulum blandit faucibus est et malesuada. Sed interdum cursus dui nec venenatis. Pellentesque non nisi lobortis, rutrum eros ut, convallis nisi. Sed tellus turpis, dignissim sit amet tristique quis, pretium id est. Sed aliquam diam diam, sit amet faucibus tellus ultricies eu. Aliquam lacinia nibh a metus bibendum, eu commodo eros commodo. Sed commodo molestie elit, a molestie lacus porttitor id. Donec facilisis varius sapien, ac fringilla velit porttitor et. Nam tincidunt gravida dui, sed pharetra odio pharetra nec. Duis consectetur venenatis pharetra. Vestibulum egestas nisi quis elementum elementum.</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/img/!sample-user.jpg" alt="Joe Junior" class="rounded-circle" />
												</figure>
												<span class="title">Joe Junior</span>
												<span class="message">Lorem ipsum dolor sit.</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<figure class="image">
													<img src="assets/img/!sample-user.jpg" alt="Joseph Junior" class="rounded-circle" />
												</figure>
												<span class="title">Joseph Junior</span>
												<span class="message">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet lacinia orci. Proin vestibulum eget risus non luctus. Nunc cursus lacinia lacinia. Nulla molestie malesuada est ac tincidunt. Quisque eget convallis diam.</span>
											</a>
										</li>
									</ul>

									<hr />

									<div class="text-end">
										<a href="#" class="view-more">View All</a>
									</div>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="dropdown-toggle notification-icon" data-bs-toggle="dropdown">
								<i class="fa fa-bell"></i>
								<span class="badge">3</span>
							</a>

							<div class="dropdown-menu notification-menu">
								<div class="notification-title">
									<span class="float-end badge badge-default">3</span>
									Alerts
								</div>

								<div class="content">
									<ul>
										<li>
											<a href="#" class="clearfix">
												<div class="image">
													<i class="fas fa-thumbs-down bg-danger text-light"></i>
												</div>
												<span class="title">Server is Down!</span>
												<span class="message">Just now</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<div class="image">
													<i class="bx bx-lock bg-warning text-light"></i>
												</div>
												<span class="title">User Locked</span>
												<span class="message">15 minutes ago</span>
											</a>
										</li>
										<li>
											<a href="#" class="clearfix">
												<div class="image">
													<i class="fas fa-signal bg-success text-light"></i>
												</div>
												<span class="title">Connection Restaured</span>
												<span class="message">10/10/2021</span>
											</a>
										</li>
									</ul>

									<hr />

									<div class="text-end">
										<a href="#" class="view-more">View All</a>
									</div>
								</div>
							</div>
						</li>
					</ul>-->

					<span class="separator"></span>

					<div id="userbox" class="userbox" data-uid="<?= md5("easymeter" . $user->id . "123456"); ?>" data-id="<?= $user->id; ?>">
						<a href="#" data-bs-toggle="dropdown">
							<figure class="profile-picture">
								<img src="<?php echo avatar($user->avatar); ?>" alt="<?php echo $user->username; ?>" class="rounded-circle" />
							</figure>
							<div class="profile-info">
								<span class="name"><?= $user->nickname; ?></span>
								<span class="role"><?= $user->group; ?></span>
							</div>

							<i class="fa custom-caret"></i>
						</a>

						<div class="dropdown-menu">
							<ul class="list-unstyled mb-2">
								<li class="divider"></li>
								<li>
									<a role="menuitem" tabindex="-1" href="<?php echo site_url('admin/profile/'); ?>"><i class="fas fa-user"></i> Minha Conta</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="<?php echo site_url('logout'); ?>"><i class="fas fa-power-off"></i> Sair</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">