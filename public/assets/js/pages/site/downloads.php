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
                                <img alt="Downloads" src="<?php echo base_url('assets/img/downloads_pg.png'); ?>"></a>
		                        <div class="title mb16">
		                            <a href="#downloads"><h3 class="mb0">Downloads</h3></a>
		                        </div>
		                        <p class="mb0">Guias do usuário, manuais e folders de produtos.</p>
		                    </div>
		                </div>
		                <div class="col-md-4 col-sm-6">
		                    <div class="outer-title text-center">
		                        <a href="<?php echo site_url('site/faq'); ?>#perguntas"><img alt="FAQs" src="<?php echo base_url('assets/img/faqs.png'); ?>"></a>
		                        <div class="title mb16">
                                    <a href="<?php echo site_url('site/faq'); ?>#perguntas"><h3 class="mb0">FAQs</h3></a>
		                        </div>
		                        <p class="mb0">Perguntas frequentes.</p>
		                    </div>
		                </div>
		                <div class="col-md-4 col-sm-6">
		                    <div class="outer-title text-center">
                                <a href="<?php echo site_url('site/chamados'); ?>#chamados"><img alt="Suppot Tickets" src="<?php echo base_url('assets/img/tickets.png'); ?>"></a>
		                        <div class="title mb16">
		                            <a href="<?php echo site_url('site/chamados'); ?>#chamados"><h3 class="mb0">Chamados</h3></a>
		                        </div>
		                        <p class="mb0">Abra um chamado para solicitações de suporte.</p>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </section>
            
            <a id="downloads" class="in-page-link"></a>

			<section class="bg-secondary">
		        <div class="container">
		            <div class="row">
		                <div class="col-sm-12 text-center">
		                    <h2 class="mb40">Nenhum Documento Disponível</h2>
		                </div>
		            </div>
                </div>
            </section>
<?php /*
			<section class="bg-secondary">
		        <div class="container">
		            <div class="row">
		                <div class="col-sm-12 text-center">
		                    <h2 class="mb40">Central de Downloads</h2>
		                </div>
		            </div>
		            <div class="row">
		                <div class="col-md-4 col-sm-6">
		                    <div class="feature feature-1 boxed text-center">
		                        <h4 class="bold color-primary">Guia de Inicio Rápido</h4>
		                        <p class="lead">Aplicativo Easymeter</p>
		                        <a class="btn btn-lg" href="downloads/Quick-Start-Guide-WaterOn-App.pdf" target="_blank">Baixar</a>
		                        <p class="lead">Painel Easymeter</p>
		                        <a class="btn btn-lg" href="downloads/Quick_Start_Guide_WaterOn_WOW_lite-V2.pdf" target="_blank">Baixar</a>
		                    </div>
		                </div>
                        <div class="col-md-4 col-sm-6">
		                    <div class="feature feature-1 boxed text-center">
		                        <h4 class="bold color-primary">Especificações Técnicas</h4>
		                        <p class="lead">Medidor Easymeter</p>
		                        <a class="btn btn-lg" href="downloads/WaterOn_WOS2A_v3.1.pdf" target="_blank">Baixar</a>
                                <p class="lead">Central Easymeter</p>
		                        <a class="btn btn-lg" href="downloads/WaterOn-WOM1-2015.pdf" target="_blank">Baixar</a>
		                    </div>
		                </div>
		                <div class="col-md-4 col-sm-6">
		                    <div class="feature feature-1 boxed text-center">
		                        <h4 class="bold color-primary">Outros</h4>
		                        <p class="lead">Termo de Garantia</p>
		                        <a class="btn btn-lg" href="downloads/warranty.pdf" target="_blank">Baixar</a>
                                <p class="lead">Comparativo</p>
		                        <a class="btn btn-lg" href="downloads/WaterOn-vs-Normal%20Meters.pdf" target="_blank">Baixar</a>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </section>
*/ ?>            
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