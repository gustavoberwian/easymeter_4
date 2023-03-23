<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
        <div class="main-container">
            
            <a id="remoto" class="in-page-link"></a>

            <section class="bg-dark image-edge">
                <div class="col-md-6 col-sm-4 p0 col-md-push-6 col-sm-push-8">
                    <img alt="Sem leiturista, sem coletores de dados" class="mb-xs-24" src="<?php echo base_url('assets/img/tech_bg.png'); ?>">
                </div>
                <div class="container">
                    <div class="col-md-6 col-md-pull-0 col-sm-7 col-sm-pull-4 v-align-transform">
						<h1 class="mb40 color-primary mb-xs-16">Sem leiturista, sem coletores de dados.</h1>
						<h4 class="uppercase bold mb16">Os medidores Easymeter não precisam ser lidos. Ponto.</h4>
						<p class="lead mb40">Ao contrário dos hidrômetros tradicionais, que precisam ser lidos manualmente ou por um dispositivo portátil que depois baixa os dados em uma planilha, os medidores Easymeter enviam dados de consumo em tempo real para o nosso sistema na nuvem para processamento e as contas de consumo mensais são geradas e enviadas aos usuários automaticamente. <a class="bold color-white"> E você não paga mais por nada disso.</a></p>
						<a class="btn btn-lg btn-white" href="<?php echo site_url('site/tecnologia'); ?>#cortar_colar">Saiba mais</a>
                    </div>
                </div>
            </section>
            
            <a id="cortar_colar" class="in-page-link"></a>
            
            <section class="image-edge bg-secondary">
                <div class="col-md-6 col-sm-4 p0">
                    <img alt="Cortar, Colar, Economizar" class="mb-xs-24" src="<?php echo base_url('assets/img/cpc.png'); ?>">
                </div>
                <div class="container">
                    <div class="col-md-6 col-sm-7 col-sm-offset-1 v-align-transform right">
						<h2 class="mb40 bold mb-xs-16">Cortar, Colar, Economizar</h2>
						<h4 class="uppercase bold mb16">PROCESSO DE INSTALAÇÃO "PLUG & PLAY"</h4>
						<p class="lead fade-1-4 mb40">Nossa experiência nos ensinou que não basta fazer um produto revolucionário, ele deve ser instalado e mantido com perfeição. Assim, continuamos a aprender com nossas instalações e temos dominado e simplificado não apenas o encanamento necessário, mas também o cabeamento, o roteamento e as conexões.</br></br> A Central Easymeter, reponsável pela comunicação com a nuvem possui backup por baterias. Em caso de falta de luz elas garantem a energia não apenas para a Central, mas também para os medidores conectados a ela. Os medidores sem fio se comunicam através de rede mesh e oferecem uma solução limpa e adequada para apartamentos e edifícios quem precisem de uma solução de medição individual. A Central Easymeter se conecta ao nosso sistema em nuvem usando uma rede M2M dedicada.</p>
						<a class="btn-lg btn" href="<?php echo site_url('site/tecnologia'); ?>#plataforma">Saiba mais</a>
                    </div>
                </div>
            </section>
            
            <a id="plataforma" class="in-page-link"></a>
            
            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h2 class="mb16 fade-1-4 thin">
                                Levamos a leitura para uma tela mais próxima de você.<br/>E a melhoramos <b>3X - Monitore. Controle. Economize.</b>
                            </h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="image-slider slider-all-controls controls-inside">
                                <ul class="slides">
                                    <li><img alt="Transformamos digitos em dados" src="<?php echo base_url('assets/img/pf_app.png'); ?>"></li>
                                    <li><img alt="Telas do aplicativo Easymeter" src="<?php echo base_url('assets/img/pf_3screens.png'); ?>"></li>
                                    <li><img alt="Painel Easymeter" src="<?php echo base_url('assets/img/pf_ipad.png'); ?>"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <a id="envolva" class="in-page-link"></a>

            <section class="bg-red">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h2 class="mb0 color-offwhite inline-block p32 pb40 p0-xs">Envolva os moradores nos esforços para reduzir o consumo de água no condomínio.</h2>
                            <div class="modal-container" id="requestdemomodal">
                                <a class="btn btn-lg btn-white btn-modal" href="#">Solicite uma Demonstração</a>
                                <div class="foundry_modal">
                                    <div class="col-sm-12">
                                        <h3 class="mb16 color-primary">Por favor, preencha com seus dados</h3>
                                        <p class="lead">Nós respeitamos sua privacidade e não compartilhamos seus dados.</p>
                                        <form class="home-contact" method="post">
											<p class="form-error mb-3 hidden">Por favor preencha todos os campos.</p>
                                            <input type="text" name="name" maxlength="255" placeholder="Seu nome" />
                                            <input type="text" name="email" maxlength="255" placeholder="Endereço de E-mail" />
                                            <input type="text" name="phone" maxlength="16" placeholder="Celular" />
											<input type="text" class="form-col-left" name="condo" maxlength="255" placeholder="Nome do Condomínio" />
											<input type="text" class="form-col-right" name="units" maxlength="5" placeholder="N&ordm; Unidades"/>
                                            <input type="text" class="form-col-left" name="city" maxlength="255" placeholder="Cidade" />
                                            <select name="state" class="form-col-right">
                                                <option selected disabled value="" >Estado</option>
                                                <option value="AC">Acre</option>
                                                <option value="AL">Alagoas</option>
                                                <option value="AP">Amapá</option>
                                                <option value="AM">Amazonas</option>
                                                <option value="BA">Bahia</option>
                                                <option value="CE">Ceará</option>
                                                <option value="DF">Distrito Federal</option>
                                                <option value="ES">Espírito Santo</option>
                                                <option value="GO">Goiás</option>
                                                <option value="MA">Maranhão</option>
                                                <option value="MT">Mato Grosso</option>
                                                <option value="MS">Mato Grosso do Sul</option>
                                                <option value="MG">Minas Gerais</option>
                                                <option value="PA">Pará</option>
                                                <option value="PB">Paraíba</option>
                                                <option value="PR">Paraná</option>
                                                <option value="PE">Pernambuco</option>
                                                <option value="PI">Piauí</option>
                                                <option value="RJ">Rio de Janeiro</option>
                                                <option value="RN">Rio Grande do Norte</option>
                                                <option value="RS">Rio Grande do Sul</option>
                                                <option value="RO">Rondônia</option>
                                                <option value="RR">Roraima</option>
                                                <option value="SC">Santa Catarina</option>
                                                <option value="SP">São Paulo</option>
                                                <option value="SE">Sergipe</option>
                                                <option value="TO">Tocantins</option>
                                            </select>
                                            <textarea name="message" maxlength="2048" placeholder="Sua mensagem para nós" rows="3" style="resize:none;"></textarea>
											<input type="submit" value="Enviar">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <a id="conta" class="in-page-link"></a>

            <section class="image-edge pt120 pb120 pt-xs-40 pb-xs-40">
                <div class="col-md-6 col-sm-4 p0">
                    <img alt="Relatório Easymeter" class="mb-xs-24" src="<?php echo base_url('assets/img/relatorio_demo.png'); ?>">
                </div>
                <div class="container">
                    <div class="col-md-5 col-md-offset-1 col-sm-7 col-sm-offset-1 v-align-transform right">
						<h3 class="mb16 color-dark mb-xs-16">Um medidor de água inteligente deve fornecer um relatório exato e <b>muito mais</b>.</h3>
						<p class="lead fade-1-4 mb24">A plataforma oferece controle total sobre o processo de tarifação. Uma interface simples dá a você a opção de optar por tarifas fixas, variáveis ou por faixas de consumo. Você pode até especificar uma taxa básica e incluir valores referente a inadimplência. Depois de incluir os dados das contas de todos os ramais que abastecem o condômio, as tabulações são geradas automaticamente e enviadas aos moradores e a administradora, se for o caso.</p>
						<div class="feature feature-3 boxed">
							<div class="right">
								<h4 class="mb8 color-red bold">Medição Individual por Entradas</h4>
								<p class="fade-1-4">O relatório mensal oferece um resumo completo do consumo em cada entrada de água da casa, com leitura inicial e final, custo da água e, claro, o consumo total e o valor a ser pago.</p>
								<h4 class="mb8 color-red bold">Comparações Fáceis de Entender</h4>
								<p class="fade-1-4">Os relatórios mostram aos moradores o padrão de consumo dos últimos 6 meses para ajudá-los a determinar o comportamento de uso. O relatório mensal de água também mostra a comparação do consumo de água do morador com sua vizinhança. Afinal, você sempre quer ser melhor que seu vizinho. <b>E ainda economizar!</b></p>
							</div>
						</div>
                    </div>
                </div>
            </section>
            
            <a id="boletim" class="in-page-link"></a>

            <section class="image-square left bg-red">
                <div class="col-md-6 image">
                    <div class="background-image-holder">
                        <img alt="Boletim semanal Easymeter" class="background-image" src="<?php echo base_url('assets/img/small.png'); ?>">
                    </div>
                </div>
                <div class="col-md-7 col-md-offset-1 color-offwhite content">
                    <h3 class="color-offwhite bold">Boletim Semanal e Previsão para o Mês</h3>
                    <p class="mb0 fade-1-2 lead color-offwhite">
                        Toda semana enviamos aos moradores um instantâneo de seu consumo semanal, comparado com a semana anterior, juntamente com
                        os valores projetados para o mês. A maioria dos moradores considera este relatório muito útil, pois lhes dá a oportunidade
                        de tomar atitudes e realizar ações que venham a economizar água e que geram economia no fim do mês.</p>
                </div>
            </section>
            
            <a id="gamificacao" class="in-page-link"></a>

            <section class="image-bg pt180 pb180 pt-xs-80 pb-xs-80">
                <div class="background-image-holder">
                    <img alt="Gamificação Easymeter" class="background-image" src="<?php echo base_url('assets/img/gamification.png'); ?>">
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-sm-push-6">
                            <h2>Jogos para impulsionar a mudança de comportamento.</h2>
                            <p class="lead mb48 mb-xs-32">A próxima versão do aplicativo usará a gamificação para motivar e recompensar os usuários por várias atividades voltadas para a conservação da água. Se o condominío optar, os usuários poderão usar seus pontos de recompensa para aproveitar descontos em suas contas de água e outros itens.</p>
                        </div>
                    </div>
                </div>
            </section>
