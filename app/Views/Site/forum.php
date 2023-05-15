<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="utf-8">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Ubuntu:400&amp;subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,khmer,latin,latin-ext,vietnamese,hebrew,arabic,bengali,gujarati,tamil,telugu,thai" media="all">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Javascript -->
    <script src="<?php echo base_url('vendor/jquery/jquery-1.12.4.js'); ?>"></script>
    <script src="<?php echo base_url('vendor/jquery/jquery-ui.js'); ?>"></script>
    <script src="<?php echo base_url('vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/pages/site/forum.js'); ?>"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/site/for.css'); ?>">

</head>

<body>


    <div class="ht-sitecontainer ht-sitecontainer--wide">

        <!-- INICIO HEADER -->


        <div class="site-header">
            <div class="site-header__search">
                <div class="ht-container">
                    <img class="classico" alt="Easymeter" src="http://localhost:8080/assets/img/logo123.png">
                    <br><br><br>
                    <h2 class="site-header__title">O que está procurando?</h2>
                    <form class="hkb-site-search" method="get" action="">
                        <label class="hkb-screen-reader-text" for="hkb-search">Search For</label>
                        <input id="hkb-search" class="hkb-site-search__field" type="text" value="" placeholder="Pesquise aqui..." name="s" autocomplete="off">
                        <input type="hidden" name="ht-kb-search" value="1">
                        <button class="hkb-site-search__button" type="submit"><span>Search</span></button>
                    </form>
                </div>
            </div>
        </div>

            <!-- FINAL HEADER -->


        <!-- topics -->
        <section class="section">
            
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 text-center">
                        <h2 class="section-title">Assuntos mais procurados</h2>
                    </div>

                    <!-- INICIO TÓPICO -->
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <a href="assuntoforum" class="px-4 py-5 bg-white shadow text-center d-block match-height">
                            <i class="ti-package icon text-primary d-block mb-4"></i>
                            <h3 class="mb-3 mt-0">Faturas</h3>
                            <p class="mb-00">Como posso conseguir minha fatura?</p>
                            <br>
                            <button class="botaoabrirmodal">Saber mais</button>
                            <!--  FIM TÓPICO -->
                        </a>
                    </div>

                    <!-- INICIO TÓPICO -->
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <a href="assuntoforum" class="px-4 py-5 bg-white shadow text-center d-block match-height">
                            <i class="ti-package icon text-primary d-block mb-4"></i>
                            <h3 class="mb-3 mt-0">Dúvidas gerais</h3>
                            <p class="mb-00">Como posso tirar minhas dúvidas?</p>
                            <br>
                            <button class="botaoabrirmodal">Saber mais</button>
                            <!--  FIM TÓPICO -->
                        </a>
                    </div>

                    <!-- INICIO TÓPICO -->
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <a href="assuntoforum" class="px-4 py-5 bg-white shadow text-center d-block match-height">
                            <i class="ti-package icon text-primary d-block mb-4"></i>
                            <h3 class="mb-3 mt-0">Trabalhe conosco</h3>
                            <p class="mb-00">Como posso trabalhar com vocês?</p>
                            <br>
                            <button class="botaoabrirmodal">Saber mais</button>
                            <!--  FIM TÓPICO -->
                        </a>
                    </div>

                    <!-- INICIO TÓPICO -->
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <a href="assuntoforum" class="px-4 py-5 bg-white shadow text-center d-block match-height">
                            <i class="ti-package icon text-primary d-block mb-4"></i>
                            <h3 class="mb-3 mt-0">Valores</h3>
                            <p class="mb-00">Quais são os valores dos medidores</p>
                            <br>
                            <button class="botaoabrirmodal">Saber mais</button>
                            <!--  FIM TÓPICO -->
                        </a>
                    </div>

                    <!-- INICIO TÓPICO -->
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <a href="assuntoforum" class="px-4 py-5 bg-white shadow text-center d-block match-height">
                            <i class="ti-package icon text-primary d-block mb-4"></i>
                            <h3 class="mb-3 mt-0">Instalação?</h3>
                            <p class="mb-00">Como é feita a instalação dos medidores?</p>
                            <br>
                            <button class="botaoabrirmodal">Saber mais</button>
                            <!--  FIM TÓPICO -->
                        </a>
                    </div>

                    <!-- INICIO TÓPICO -->
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <a href="assuntoforum" class="px-4 py-5 bg-white shadow text-center d-block match-height">
                            <i class="ti-package icon text-primary d-block mb-4"></i>
                            <h3 class="mb-3 mt-0">Uno/easymeter?</h3>
                            <p class="mb-00">Você sabe quem somos nós?</p>
                            <br>
                            <button id="abrirmodal" class="botaoabrirmodal">Saber mais</button>
                            <!--  FIM TÓPICO -->
                        </a>
                    </div>
                    <!-- FIM TÓPICO -->
                </div>
            </div>
        </section>

        <!-- 
            FOOTER 
        -->
   
        <footer id="footer" class="footer-1">
            <div class="main-footer widgets-dark typo-light">
                <div class="container">
                    <div class="row1">
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="widget subscribe no-box">
                                <div class="feature feature-2 text-center pop-up-chat">
                                    <div class="text-center">
                                        <h6>Easymeter</h6>
                                    </div>
                                    <br>
                                    <li class="footer-list ">
                                        <a href="#sobrenos">Sobre nós</a>
                                    </li>
                                    <li class="footer-list ">
                                        <a href="">Perguntas Frequentes</a>
                                    </li>
                                    <li class="footer-list ">
                                        <a href="#plataforma">Plataforma</a>
                                    </li>
                                    <li class="footer-list ">
                                        <a href="#plataforma">Diferenciais</a>
                                    </li>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="widget no-box">
                                <div class="feature feature-2 text-center pop-up-chat">
                                    <div class="text-center">
                                        <h6>Transparência</h6>
                                    </div>
                                    <br>
                                    <li class="footer-list ">
                                        <a href="">Política de privacidade</a>
                                    </li>
                                    <li class="footer-list ">
                                        <a href="">Ética da empresa</a>
                                    </li>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="widget no-box">
                                <div class="feature feature-2 ">
                                    <div class="">
                                        <h6>Suporte</h6>
                                    </div>
                                    <br>
                                    <li class="footer-list1">
                                        <a href="">Dúvidas técnicas:</a>
                                    </li>
                                    <li class="footer-list1">
                                        <a href="">Easymeter.com.br/suporte</a>
                                    </li>
                                    <li class="footer-list1 ">
                                        <a href="">(51) 99794 3832</a>
                                    </li>
                                    <br>
                                    <li class="footer-list1 ">
                                        <a href=""><strong>De segunda à sexta <br>das 9:00 às 17:00</strong> </a>
                                    </li>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="widget no-box">
                                <div class="feature feature-2 text-center pop-up-chat">
                                    <div class="text-center">
                                        <h6>SAC</h6>
                                    </div>
                                    <br>
                                    <li class="footer-list1 ">
                                        <a class="classic" href="">Dúvidas, sugestões ou reclamações:</a>
                                    </li>
                                    <br>
                                    <li class="footer-list1 ">
                                        <a href="">sac@easymeter.com.br</a>
                                    </li>
                                    <li class="footer-list1 ">
                                        <a href="">0800 591 6181</a>
                                    </li>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="widget no-box">
                                <div class="feature feature-2">
                                    <div class="text-center">
                                        <h6>Baixe o APP</h6>
                                    </div>
                                    <br>
                                    <a href="https://play.google.com/store/apps/details?id=com.uno.easymeter"><img class="image-xs ml200" alt="Play Store" src="http://easymeter.com.br/assets/img/play-store-badge.png"></a>
                                    <br>
                                    <br>
                                    <a href=""><img class="image-xs ml200" alt="App Store" src="http://easymeter.com.br/assets/img/app-store-badge-soon.png"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <img src="https://easymeter.io/assets/img/site/easymeter_rodape_logo_unorobotica.svg" alt="logo" width="90px">
                            <span class="sub">© 2018-2023<a href="https://goo.gl/maps/YbPLxMmcdBpR9gLE7"> - UNO Robótica - Rumania, 172. Bairro Rincão. Novo Hamburgo - 93348-480 </a></span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

</body>

</html>