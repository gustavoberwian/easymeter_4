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

	<body>
		<!-- start: page -->
				<!-- start: page -->
                <section class="body-sign">
			<div class="center-sign">
            <a href="/" class="logo float-start">
					<img src="<?= base_url('assets/img/logo.png'); ?>" height="54" alt="Easymeter" />
				</a>


				<div class="panel card-sign">
                <div class="card-title-sign mt-3 text-end">
						<h2 class="title text-uppercase font-weight-bold m-0"><i class="bx bx-user-circle me-1 text-6 position-relative top-5"></i> Registrar</h2>
					</div>
                    <section class="card" id="w3">
                        <div class="card-body">
                         
                            <form id="regForm" class="form-horizontal" action="<?php echo site_url('register'); ?>" method="post" accept-charset="utf-8">
                                <input type="hidden" name="cid" value="">
                                <input type="hidden" name="uid" value="">

                                    <div id="w3-acesso" class="tab">
                                     
                                        <div class="form-group mb-3">
								            <label>E-mail</label>
                                            <input name="identity" id="identity" type="email" class="form-control form-control-lg" required autofocus tabindex="1" value=""/>
                                        </div>
                                        <div class="form-group mb-0">
                                            <div class="row">
                                                <div class="col-sm-6 mb-3">
                                                    <label>Senha</label>
                                                    <input name="password" id="password" type="password" minlength="6" class="form-control form-control-lg" required tabindex="2" value=""/>
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <label>Confirmação</label>
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

                                    <div id="w3-unidade" class="tab">
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
                                    <div id="" class="tab"><h1>registration successfuly</h1>
                                </div>


 <div style="overflow:auto;">
    <div style="float:right;">
    <a  id="loginBtn" class='btn btn-primary btn-block float' href="<?= url_to('login') ?>">Login </a>
      <a   id="prevBtn" class ='btn btn-primary btn-block float'onclick="nextPrev(-1)">Previous</a>
      <a  id="nextBtn" class ='btn btn-primary btn-block float' onclick="nextPrev(1)">Next</a>
    
        </div>
        </div>
        <!-- Circles which indicates the steps of the form: -->
        <div style="text-align:center;margin-top:40px;">
            <span class="step"></span>
            <span class="step"></span>

        </div>
                                
           </form>
                            
             </div>
                         
         </section>
		</div>
				<p class="text-center text-muted mt-3 mb-3">© Copyright 2017-<?php echo date('Y'); ?>. Todos os direitos reservados.</p>
			</div>
		</section>
		<!-- Vendor -->
		<script src="<?php echo base_url('vendor/jquery/jquery.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/jquery-browser-mobile/jquery.browser.mobile.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/bootstrap/js/bootstrap.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/nanoscroller/nanoscroller.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/jquery-placeholder/jquery.placeholder.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/jquery-validation/jquery.validate.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/jquery-mask-plugin/jquery.mask.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/spinner/js/spinner.js'); ?>"></script>


		<!-- Theme Base, Components and Settings -->
        <script src="<?php echo base_url('assets/js/admin.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>

		<!-- Page Specific -->
        <script src="<?php echo base_url('assets/js/pages/auth/completar.js'); ?>"></script>

	</body>
</html>








<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "initial";
  //... and fix the Previous/Next buttons:
   if (n == 0) {
    document.getElementById("loginBtn").style.display = "";
    document.getElementById("prevBtn").style.display = "none";
  } else if(n==2)
         {document.getElementById("prevBtn").style.display = "none";
          document.getElementById("loginBtn").style.display = "none";
          document.getElementById("nextBtn").style.display = "none";
        }  
  else {
    document.getElementById("loginBtn").style.display = "none";
    document.getElementById("prevBtn").style.display = "";
    }
  if (n == (x.length - 1)) {
    document.getElementById("loginBtn").style.display = "";

    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
    
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}
</script>