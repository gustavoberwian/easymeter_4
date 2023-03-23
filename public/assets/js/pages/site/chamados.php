<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
    echo $this->recaptcha->getScriptTag();
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
		                        <img alt="Support Tickets" src="<?php echo base_url('assets/img/tickets_pg.png'); ?>"></a>
		                        <div class="title mb16">
		                            <a href="<?php echo site_url('site/chamados'); ?>#chamados""><h3 class="mb0">Chamados</h3></a>
		                        </div>
		                        <p class="mb0">Abra um chamado para solicitações de suporte.</p>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </section>

            <a id="chamados" class="in-page-link"></a>
			
			<section class="bg-green">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="ticket">
                                <h3 class="mb0">Abrir Chamado</h3>
                                <p class="mb12">Preencha o formulário e envie-nos seu problema, dúvida ou sugestão.<br/>Nosso suporte responderá em até 48hrs.</p>
                                <form class="new-ticket" method="post">
                                    <p class="form-success color-white mb-3 hidden"><strong>Chamado criado com sucesso.</strong><br/>Em até 48hrs entraremos em contato pelo e-mail.</p>
                                    <p class="form-error color-white mb-3 hidden">Por favor preencha todos os campos.</p>
                                    <input type="text" name="name" placeholder="Nome Completo" />
                                    <input type="text" name="email" placeholder="Email"/>
                                    <select name="dpto">
                                        <option selected disabled value="" >Departamento</option>
                                        <option value="1">Vendas</option>
                                        <option value="2">Suporte</option>
                                     </select>
                                    <textarea name="message" placeholder="Sua mensagem" rows="3" style="resize:none;"></textarea>
                                     <?php echo $this->recaptcha->getWidget(); ?>
                                     <input type="submit" class="mt24" value="Abrir Chamado">
                                </form>
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