<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
        <div class="main-container">

			<a id="principal" class="in-page-link"></a>
			
			<section>
		        <div class="container">
		            <div class="row">
		                <div class="col-md-4 col-sm-6">
		                    <div class="outer-title text-center">
		                        <a href="<?php echo site_url('site/downloads'); ?>#downloads">
                                <img alt="Downloads" src="<?php echo base_url('assets/img/downloads.png'); ?>"></a>
		                        <div class="title mb16">
		                            <a href="<?php echo site_url('site/downloads'); ?>#downloads"><h3 class="mb0">Downloads</h3></a>
		                        </div>
		                        <p class="mb0">Guias do usuário, manuais e folders de produtos.</p>
		                    </div>
		                </div>
		                <div class="col-md-4 col-sm-6">
		                    <div class="outer-title text-center">
		                        <a href="<?php echo site_url('site/faq'); ?>#perguntas">
                                <img alt="FAQs" src="<?php echo base_url('assets/img/faqs.png'); ?>"></a>
		                        <div class="title mb16">
                                    <a href="<?php echo site_url('site/faq'); ?>#perguntas"><h3 class="mb0">FAQs</h3></a>
		                        </div>
		                        <p class="mb0">Perguntas frequentes.</p>
		                    </div>
		                </div>
		                <div class="col-md-4 col-sm-6">
		                    <div class="outer-title text-center">
                                <a href="<?php echo site_url('site/chamados'); ?>#chamados">
		                        <img alt="Support Tickets" src="<?php echo base_url('assets/img/tickets.png'); ?>"></a>
		                        <div class="title mb16">
		                            <a href="<?php echo site_url('site/chamados'); ?>#chamados"><h3 class="mb0">Chamados</h3></a>
		                        </div>
		                        <p class="mb0">Abra ou verifique um chamado de suporte.</p>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </section>
            
            <a id="contato_header" class="in-page-link"></a>
			
			<section class="page-title page-title-4 bg-orange">
		        <div class="container">
		            <div class="row">
		                <div class="col-md-10">
		                    <h2 class="mb0 color-offwhite">Como entrar em contato se você tiver um problema?</h2>
		                </div>
		            </div>
		        </div>
		    </section>

            <a id="contato" class="in-page-link"></a>
			
            <?php $this->load->view('site/contato'); ?>