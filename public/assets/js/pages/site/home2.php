<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<main>

    <section class="p-0" id="slider">

        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">

            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>

            <div class="carousel-inner">
                <div class="carousel-item carousel-one active">
                    <!-- <img class="d-block w-100" src="<?= base_url("assets/img/site/easymeter_banner01.jpg"); ?>" alt="First slide"> -->
                    <div class="jumbotron banner01">
                        <div class="container">
                            <div class="padding-swiper pt-0 pb-0 text-center">
                                <h1 class="title-banner text-light">Plataforma para gestão e monitoramento inteligente de água, energia e gás!</h1>
                                <p class="subtitle-banners text-light p-4">Monitoramento em tempo real, alertando sobre excessos de consumo e possíveis vazamento. Tudo isso desenvolvido por uma empresa de robótica</p>
                                <p class="d-flex justify-content-center">
                                    <a class="btn-banner text-light" href="#plataforma" role="button">Saiba mais</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item carousel-two">
                    <!-- <img class="d-block w-100" src="<?= base_url("assets/img/site/easymeter_banner02.jpg"); ?>" alt="Second slide"> -->
                    <div class="jumbotron banner02">
                        <div class="container">
                            <div class="padding-swiper pt-0 pb-0 text-center">
                                <div class="row">
                                    <div class="col">
                                        <h1 class="title-banner text-light text-right">Condomínios com água individualizada geram economia de mais 35%</h1>
                                        <p class="subtitle-banners mt-3 text-light text-right">Sem medição não há condições nem incentivo para alguém economizar água.
                                            Pesquisas comprovam que o consumo cai até 35% quando começamos a medir
                                        </p>
                                        <p class="d-flex justify-content-end">
                                            <a class="btn-banner mt-3 text-light" href="#individualizacao" role="button">Saiba mais</a>
                                        </p>
                                    </div>
                                    <div class="col banner-icons-item">
                                        <ul class="list-unstyled banner2-ul-list">
                                            <li class="d-flex align-items-center list-banner2">
                                                <div class="banner2-icon">
                                                    <img src="<?= base_url("assets/img/site/easymeter_banner02_icone01.svg"); ?>" class="banner2-icon" height="100" alt="Gota de água">
                                                </div>
                                                <div class="div-text-banner">
                                                    <span class="text-banner2">Redução no consumo</span>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-center list-banner2">
                                                <div class="banner2-icon">
                                                    <img src="<?= base_url("assets/img/site/easymeter_banner02_icone02.svg"); ?>" class="banner2-icon" alt="Redução custos de energia">
                                                </div>
                                                <div class="div-text-banner">
                                                    <span class="text-banner2">Redução custos de energia</span>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-center list-banner2">
                                                <div class="banner2-icon">
                                                    <img src="<?= base_url("assets/img/site/easymeter_banner02_icone03.svg"); ?>" class="banner2-icon" alt="Aumento na justiça e satisfação">
                                                </div>
                                                <div class="div-text-banner">
                                                    <span class="text-banner2">Aumento na justiça e satisfação</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item carousel-three">
                    <!-- <img class="d-block w-100" src="<?= base_url("assets/img/site/easymeter_banner03.jpg"); ?>" alt="Third slide"> -->
                    <div class="jumbotron banner03">
                        <div class="container">
                            <div class="padding-swiper pt-0 pb-0 text-center">
                                <h1 class="title-banner text-light">O sistema mais completo para melhorar a performance e gestão</h1>
                                <p class="subtitle-banners text-light">de água, energia e gás do seu empreendimento.</p>
                                <p class="d-flex justify-content-center">
                                    <a class="btn-banner mt-3 text-light" href="#aplicacao" role="button">Saiba mais</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>

            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

    </section>

    <section id="sobrenos">
        <div class="container">
            <div class="padding-swiper pt-0 pb-0 text-center">
                <h2>Sobre Nós</h2>
                <p class="text">
                    O Easymeter foi desenvolvido pela UNO Robótica, empresa com mais
                    de 10 anos de atuação no mercado, fundada por dois especialistas
                    nas áreas de desenvolvimento de produtos eletrônicos, IoT (Internet
                    das coisas), tecnologias embarcadas e serviços web.
                </p>
                <p class="text">
                    O Sistema Easymeter foi primordialmente pensado para atender
                    demandas em condomínios no que se refere a medição e
                    individualização dos consumos com o pensamento de "o que não se
                    monitora, não se controla". E essa ideia se reflete na prática como:
                    condomínios que passam a utilizar nosso sistema apresentam uma
                    diminuição no consumo em cerca de 30%
                </p>
                <p class="text">
                    Atualmente o sistema atende demandas voltadas ao monitoramento
                    e controle remoto e assistido de processos ligados à água, energia,
                    gás e resíduos tanto nos condomínios quanto de indústrias,
                    comércios e agronegócio.
                </p>
                <div class="space"></div>
                <img alt src="<?= base_url('assets/img/site/easymeter_mandala.svg'); ?>" style="max-height: 550px;" />
            </div>

        </div>
    </section>

    <section id="plataforma">
        <div class="container ">
            <div class="padding-swiper pt-0 pb-0 text-center ">
                <h2 class="color-primary-site ">A Plataforma</h2>
            </div>
        </div>

        <div class="container-blue ">
            <div class="container">
                <div class="d-flex justify-content-center">
                    <img class="devices" src="<?= base_url('assets/img/site/easymeter_telas.png') ?>" alt="devices" width="80%" />
                    <img class="d-flex align-self-end" src="<?= base_url('assets/img/site/easymeter_logos_apps.svg') ?>" alt="plataformas" width="10%" />
                </div>
            </div>
            <div class="container">
                <div class="padding-swiper text-center">
                    <h2 class="text-light">Painel de visualização simples e completo!</h2>
                    <p class="text text-light">
                        Informações em tempo real dos medidores de água, energia e gás,
                        identificando padrões de consumo, tendências e diversas outras
                        informações importantes para a tomada de decisão. Tudo isso online
                        e em tempo real no Tablet e Celular <b>Dentre as principais aplicações
                            da Plataforma Easymeter, temos:</b>
                    </p>

                    <div class="space-light"></div>

                    <div class="row  justify-content-center mb-4">
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone01.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>ALERTAS</b></p>
                                <p class="text text-dark m-0">Configuração de alarmes automáticos e notificações para usuários por email ou SMS.</p>
                                <hr class="separator-plataforma">
                            </div>
                        </div>
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone02.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>BENCHMARKING E RANKING</b></p>
                                <p class="text text-dark m-0">Recursos para comparação de desempenho entre unidades (KPI).</p>
                                <hr class="separator-plataforma">
                            </div>
                        </div>
                    </div>

                    <div class="row  justify-content-center mb-4">
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone03.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>FATURA SIMULADA</b></p>
                                <p class="text text-dark m-0">Emissão de faturas comparativas para provisão e auditoria de custos.</p>
                                <hr class="separator-plataforma">
                            </div>
                        </div>
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone04.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>GERENCIAMENTO DE UTILITIES</b></p>
                                <p class="text text-dark m-0">Medição de consumo de água, gás, ar comprimido, vapor e água gelada.</p>
                                <hr class="separator-plataforma">
                            </div>
                        </div>
                    </div>

                    <div class="row  justify-content-center mb-4">
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone05.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>MEDIÇÕES SETORIAIS</b></p>
                                <p class="text text-dark m-0">Sistema de medição individualizada com rateio automático da conta de energia elétrica e utilidades para diversos centros de custo.</p>
                                <hr class="separator-plataforma">
                            </div>
                        </div>
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone06.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>ENVIO ONLINE DOS DADOS DE CADA PONTO DE CONSUMO</b></p>
                                <p class="text text-dark m-0">(sem necessidade de "leiturista")</p>
                                <hr class="separator-plataforma">
                            </div>
                        </div>
                    </div>

                    <div class="row  justify-content-center mb-4">
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone07.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>DASHBOARDS COM TELAS INTELIGENTES E INTUITIVAS</b></p>
                            </div>
                        </div>
                        <div class="col-md-6 row align-items-center justify-content-center">
                            <div class="col-md-4">
                                <img alt src="<?= base_url('assets/img/site/easymeter_plataforma_icone08.svg') ?>" class="plataforma-icones" />
                            </div>
                            <div class="col-md-8 text-md-left text-sm-center">
                                <p class="subtitle text-dark m-0"><b>MEDIÇÃO DE CONCESSIONÁRIA</b></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-light"></div>
                    <a class="btn-home-page-2">Entre em contato</a>
                </div>
            </div>
        </div>
    </section>

    <section id="diferenciais">
        <div class="container">
            <div class="padding-swiper pt-0 pb-0 text-center">
                <h2>Diferenciais Easymeter</h2>
                <p class="text black">
                    Diferente de muitas soluções de mercado que utilizam hardware de
                    terceiros para medição do consumo,<b>
                        nossa solução contempla
                        hardware e software de desenvolvimento próprio, trazendo maior
                        qualidade e muito mais precisão nas informações.
                    </b>Além disso, como
                    nosso hardware tem capacidade de identificar consumos em
                    mililitros, qualquer sinal de vazamento é alertado instantaneamente.
                </p>

                <div class="row mt-5  justify-content-around mb-4">
                    <div class="col-md-2 align-items-center justify-content-center align-content-start">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone01.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Não utilizamos Wi-Fi do local</p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone02.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Medidores com alta precisão na leitura de consumo<br /> <small>(inferior a 5mL)</small></p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone03.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Identificação de alertas de vazamentos</p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone04.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Envio online de dados de cada ponto<br /> <small>(sem necessidade de leiturista)</small></p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone05.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Instalação rápida e segura</p>
                    </div>
                </div>

                <div class="row  justify-content-around mb-4">
                    <div class="col-md-2 align-items-center justify-content-center align-content-start">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone06.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Monitoramento via plataforma Web e Mobile</p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone07.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Gestão de faturas</p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone08.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Automação e controle de cargas</p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone09.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Machine learning e sistema de alertas</p>
                    </div>
                    <div class="col-md-2 align-items-center justify-content-center">
                        <img alt src="<?= base_url('assets/img/site/easymeter_diferenciais_icone10.svg') ?>" class="plataforma-icones" />
                        <p class="text text-dark m-0">Monitoramento online de poços artesianos, reservatórios, etc</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="aplica" class="bg-gray">
        <div class="container">
            <div class="padding-swiper pt-0 pb-0 text-center">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img alt class="foto_medidor" src="<?= base_url('assets/img/site/easymeter_aparelho.png') ?>" />
                    </div>
                    <div class="col-md-6">
                        <h3 class="text-left"><strong class="color-primary-site">Easymeter</strong> • O primeiro medidor de água 100% automatizado do Brasil</h3>
                        <p class="aplica-text text-left">
                            Condomínios já individualizados quanto a análise dos dados
                            de nossos clientes comprovam que os <b>
                                condomínios que
                                implementaram a medição individual reduziram seu
                                consumo total de água em pelo menos 35%
                            </b>Além disso,
                            eles também economizam custos de energia, uma vez que
                            uma menor quantidade de água é bombeada para as caixas
                            de água. Menos consumo também significa menos
                            desperdício de água, resultando em muita economia
                        </p>
                    </div>
                    <div class="stats">
                        <div class="stat">
                            <img src="/assets/img/site/easymeter_diferenciais_icone11_a.svg" alt="" width="80px">
                            <p class="subtitle">Custos ocultos zero</p>
                            <p class="text">Valor bem definido, sem mutios elementos de custos complicados</p>
                        </div>
                        <div class="stat">
                            <img src="<?= base_url('assets/img/site/easymeter_diferenciais_icone05.svg') ?>" width="80px" alt="chave_de_fenda">
                            <p class="subtitle">Execução do projeto</p>
                            <p class="text"> Experientes e dedicados parceiros encanadores e engenheiros civis</p>
                        </div>
                        <div class="stat">
                            <img src="<?= base_url('assets/img/site/easymeter_diferenciais_icone13_a.svg') ?>" width="80px" alt="suporte_proativo">
                            <p class="subtitle">Suporte proativo</p>
                            <p class="text">Rotina de manutenção preventiva</p>
                        </div>
                        <div class="stat">
                            <img src="<?= base_url('assets/img/site/easymeter_diferenciais_icone14_a.svg') ?>" width="80px" alt="solucao_ponta">
                            <p class="subtitle">Solução de ponta a ponta</p>
                            <p class="text">Dados entregues em tempo real e sem falhas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="destaques">
        <div class="container ">
            <div class="destaques-box">
                <h3 class="text-left text-uppercase">Alguns destaques interessantes</h3>
                <div class="box mb-3">
                    <b class="subtitle-bold">
                        O Easymeter está trabalhando 24 horas por dia, 7 dias por semana para
                        oferecer um valor execpcional aos seus clientes.
                    </b>
                </div>
                <div class="group-stats">
                    <div class="small-stats">
                        <div class="d-flex">
                            <h3 class="title-one">61</h3>
                            <h3 class="title-pont">.</h3>
                            <h3 class="title-one">473</h3>
                            <h3 class="title-pont">.</h3>
                            <h3 class="title-one">215</h3>
                            <span class="span-medida d-flex align-self-center">L</span>
                        </div>
                        <p class="destaques-text">Litros medidos</p>
                    </div>
                    <div class="small-stats">
                        <h3 class="title-one" style=" color: #B22222">29700</h3>
                        <p class="destaques-text"> Alertas de vazamento</p>
                    </div>
                    <div class="small-stats">
                        <div class="d-flex">
                            <h3 class="title-one">35</h3>
                            <span class="span-medida d-flex align-self-center">%</span>
                        </div>
                        <p class="destaques-text">Redução no consumo*</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex">
            <img src="/assets/img/site/easymeter_destaques_simbolo.svg" width="50%" alt="">
            <small class="small-msg">*Comparado com o consumo pré-medição informado pelos clientes</small>
        </div>
    </section>

    <section id="individualizacao" class="gray-wallpaper">
        <div class="container">
            <div class="individualizacao-box">
                <h2 class="text-center text-uppercase title-solution"><strong class="title-color">A melhor solução</strong><br> para individualização de <br>consumo em condominios</h2>
                <div class="row d-flex">
                    <div class="col-5 left-side">
                        <h1 class="super-title">3 Mitos</h1>
                        <h3> porque os condomínios <strong> não individualizam a medição de água, energia e gás.</strong></h3>
                    </div>
                    <div class="col-7 right-side">
                        <div class="bloco d-flex">
                            <!-- <img src="#" alt="imagem"> -->
                            <?php include_once './application/views/site/svgs/icon_conta.php' ?>
                            <div class="text-structure">
                                <h4 class="small-title">Minha conta de água é cara? mesmo?</h4>
                                <p class="text-structure">
                                    <b>Sim, e sua conta de água é tão cara quanto de luz e gás. </b>A maioria dos moradores
                                    dividem a conta geral do condomínio, com base na fração ideal do imóvel. Você pode se
                                    surpreender ao saber que 40% a 50% do valor da conta é desperdício, custo das bombas e a
                                    inadimplência.
                                </p>
                            </div>
                        </div>
                        <div class="bloco d-flex">
                            <!-- <img src="#" alt="imagem"> -->
                            <?php include_once './application/views/site/svgs/icon_stop.php' ?>
                            <div class="text-structure">
                                <h4 class="small-title">Não é viável no meu prédio</h4>
                                <p class="text-structure"> <b>A maioria dos apartamentos atuais possuem várias prumadas.</b> Até poucos anos, os apartamentos não eram projetados para mediçao individual devido às práticas
                                    predominantes e custos envolvidos. A implementação de medidores individuais realmente é difícil com as soluções existentes no mercado.
                                </p>
                            </div>
                        </div>
                        <div class="bloco d-flex">
                            <!-- <img src="#" alt="imagem"> -->
                            <?php include_once './application/views/site/svgs/icon_coin.php' ?>
                            <div class="text-structure">
                                <h4 class="small-title">Vou ter gastos extras com pessoal e serviços</h4>
                                <p class="text-structure">
                                    <b>É verdade se os medidores forem lidos manualmente.</b> A leitura dos
                                    medidores e o cálculos das tarifas são propensos a erros quando feitas manualmente.
                                    É pior, a tarifa da água por m3 mudará a cada mês com base nos consumos individuais
                                    e comuns, e ainda com a inadimplência.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="aplicacao" class="aplicacao-wallpaper">
        <div class="container">
            <div class="aplicacao-box">
                <h2 class="text-center mb-5">Onde se aplica?</h2>
                <div class="row card-corp ">
                    <div class="col-4 box-card ">
                        <p class="subtitle-card">individualização em <br><b class="strong_card">condomínios</b></p>
                        <div class="content photo-background ">
                            <span class="detail"></span>
                            <p class="text-card">Além de monitorar em tempo real o consumo de cada apartamento
                                , o Sistema Easymeter torna automática a gestão de fatura de água, energia e gás,
                                eliminando as injustiças e eliminando as necessidades de leituristas.
                            </p>
                        </div>
                    </div>
                    <div class="col-4 box-card ">
                        <p class="subtitle-card">monitoramento<br><b class="strong_card">industrial</b></p>
                        <div class="content photo-background ">
                            <span class="detail"></span>
                            <p class="text-card">Text </p>
                        </div>
                    </div>
                </div>
                <div class="row card-corp ">
                    <div class="col-4 box-card ">
                        <p class="subtitle-card">Monitoramento de <br><strong class="strong_card"> poços, ETA<small style="font-weight:bold;">s</small> e ETE<small style="font-weight:bold;">s</small> </strong></p>
                        <div class="content photo-background ">
                            <span class="detail"></span>
                            <p class="text-card">Text </p>
                        </div>
                    </div>
                    <div class="col-4 box-card ">
                        <p class="subtitle-card">monitoramento<br><strong class="strong_card"> agronegócio </strong></p>
                        <div class="content photo-background ">
                            <span class="detail"></span>
                            <p class="text-card">Text </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="clientes">
        <div class="container ">
            <h3 class="title-panel "><strong>Alguns dos clientes </strong>que já utilizam nosso sistema de monitoramento</h3>
            <div class="clients-logos">
                <div class="row text-center ">
                    <div class="little-logo col-4 ">
                        <img src="/assets/img/site/easymeter_clientes_logo01.jpg" class="logo-little" alt="Ambev">
                    </div>
                    <div class="little-logo col-4 ">
                        <img src="/assets/img/site/easymeter_clientes_logo02.jpg" class="logo-little" alt="Botanique">
                    </div>
                    <div class="little-logo col-4 ">
                        <img src="/assets/img/site/easymeter_clientes_logo03.jpg" class="logo-little" alt="Serasa Experian">
                    </div>
                </div>
                <div class="row text-center ">
                    <div class="little-logo col-4 ">
                        <img src="/assets/img/site/easymeter_clientes_logo04.jpg" class="logo-little" alt="Hortolândia">
                    </div>
                    <div class="little-logo col-4 ">
                        <img src="/assets/img/site/easymeter_clientes_logo05.jpg" class="logo-little" alt="Hyundai">
                    </div>
                    <div class="little-logo col-4 ">
                        <img src="/assets/img/site/easymeter_clientes_logo06.jpg" class="logo-little" alt="Randon">
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<div class="pop-up" id="pop-up">
    <div class="box_pop_up">
        <div class="msg_contact_title">
            <!-- <img src="<?= base_url("assets/img/site/carta.png"); ?>" width="50px" alt="carta"> -->
            <h4 class="text_pop_up">Nos envie sua mensagem</h4>
        </div>
        <form id="enviar_contato">
            <div class="input_div">
                <input type="text" class="input-control" name=" nome" placeholder="Seu nome" />
            </div>
            <div class="input_div">
                <input type="email" class="input-control" name="email" placeholder="email" />
            </div>
            <div class="input_div">
                <input type="text" class="input-control" name="assunto" placeholder="Assunto" />
            </div>
            <div class="input_div">
                <textarea type="text" class="input-control" name="mensagem" placeholder="Seu nome"></textarea>
            </div>
            <div class="group-buttons">
                <button type="button" class="modal-button close-md">Fechar</button>
                <button type="submit" class="modal-button send">Enviar</button>
            </div>
        </form>
    </div>
</div>