<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="pt-BR" xml:lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-127828864-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-127828864-1');
    </script>

    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Easymeter</title>
    <meta name="keywords" content="Easymeter" />
    <meta name="description" content="Easymeter - Controle e Economia">
    <meta name="author" content="www.easymeter.com.br">
    <meta http-equiv="Content-Language" content="pt-BR">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo base_url('vendor/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css'); ?>" rel="stylesheet" type="text/css" media="all">
    <link href="<?php echo base_url('vendor/themify-icons/themify-icons.css'); ?>" rel="stylesheet" type="text/css" media="all" />
    <link href="<?php echo base_url('vendor/font-awesome/css/all.min.css'); ?>" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="<?php echo base_url('vendor/flexslider/flexslider.css'); ?>" rel="stylesheet" type="text/css" media="all" />
    <link href="<?php echo base_url('assets/css/pages/site/theme-fonts.css'); ?>" rel="stylesheet" type="text/css" media="all" />
    <link href="<?php echo base_url('assets/css/pages/site/theme.css'); ?>" rel="stylesheet" type="text/css" media="all" />
    <link href="<?php echo base_url('vendor/swiper/swiper-bundle.min.css'); ?>" rel="stylesheet" type="text/css" media="all" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/pages/site/global.css'); ?>">

</head>

<body class="scroll-assist">
<header id="navigation">
    <div class="nav-container">
        <nav>
            <div class="nav-utility color-primary border-0 d-flex flex-row-reverse justify-content-sm-center justify-content-md-center justify-content-lg-start justify-content-xl-start">
                <div class="module right">
                    <?php if ($this->ion_auth->is_admin()) : ?>
                        <a class="link_nav" href="<?php echo site_url('admin'); ?>">
                            <i class="ti-user">&nbsp;</i>
                            <span class="sub bold">Voltar</span>
                        </a>
                    <?php else : ?>
                        <a class="link_nav" href="<?php echo site_url('auth/login'); ?>">
                            <i class="ti-user bold">&nbsp;</i>
                            <b class="sub ">Entrar</b>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="d-flex">
                    <i class="ti-email d-flex align-self-center">&nbsp;</i>
                    <span class="sub"><a class="link_nav" href="mailto:contato@easymeter.com.br">contato@easymeter.com.br</a></span>
                </div>

                <div class="d-flex mr-4">
                    <i class="ti ti-headphone-alt d-flex align-self-center">&nbsp;</i>
                    <span class="sub d-flex align-self-center"><a class="link_nav" href="tel:+5508005916181">0800 591 6181</a></span>
                </div>
            </div>
            <div class="nav-bar mb-4">
                <div class="module">
                    <a href="<?php echo site_url('/site/home'); ?>">
                        <img class="logo logo-dark p-1" alt="Easymeter" src="<?php echo base_url('assets/img/logo.png'); ?>">
                    </a>
                </div>
                <div class="module mobile-toggle right d-block d-lg-none">
                    <i class="ti-menu"></i>
                </div>
                <div class="module-group right">
                    <div class="module language left">
                        <ul class="menu bold square">
                            <li>
                                <a class="link_nav" href="#sobrenos">Sobre nós </a>
                            </li>
                            <li>
                                <a class="link_nav" href="#plataforma">A Plataforma</a>
                            </li>
                            <li>
                                <a class="link_nav" href="#diferenciais">Diferenciais</a>
                            </li>
                            <li>
                                <a class="link_nav" href="#individualizacao">Individualização</a>
                            </li>
                            <li>
                                <a class="link_nav" href="#aplicacao">Onde se aplica?</a>
                            </li>
                            <li>
                                <a class="link_nav btn-contato">Contato</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="module-group right d-none">
                    <div class="module language left">
                        <ul class="menu bold square">
                            <li class="">
                                <a href="<?php echo site_url('/'); ?>">Por que individualizar</a>
                                <ul>
                                    <li><a href="<?php echo site_url('/'); ?>#mitos" class="inner-link" target="_self">3 Mitos</a></li>
                                    <li><a href="<?php echo site_url('/'); ?>#porque_medir" class="inner-link" target="_self">Porque Easymeter</a></li>
                                    <li><a href="<?php echo site_url('/'); ?>#destaques" class="inner-link" target="_self">Fatos Importantes</a></li>
                                    <li><a href="<?php echo site_url('/'); ?>#porque_easymeter" class="inner-link" target="_self">Porque nós</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="module language left">
                        <ul class="menu">
                            <li class="">
                                <a href="<?php echo site_url('site/solucoes'); ?>">Nossa Solução</a>
                                <ul>
                                    <li><a href="<?php echo site_url('site/solucoes'); ?>#tecnologia">Tecnologia</a></li>
                                    <li><a href="<?php echo site_url('site/solucoes'); ?>#caracteristicas">Características</a></li>
                                    <li><a href="<?php echo site_url('site/solucoes'); ?>#modelos">Modelos</a></li>
                                    <li><a href="<?php echo site_url('site/solucoes'); ?>#orcamento">Orçamento</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="module language left">
                        <ul class="menu">
                            <li class="">
                                <a href="<?php echo site_url('site/tecnologia'); ?>">Tecnologia</a>
                                <ul>
                                    <li><a href="<?php echo site_url('site/tecnologia'); ?>#remoto">Sem Leitura</a></li>
                                    <li><a href="<?php echo site_url('site/tecnologia'); ?>#cortar_colar">Instalação</a></li>
                                    <li><a href="<?php echo site_url('site/tecnologia'); ?>#plataforma">Plataforma</a></li>
                                    <li><a href="<?php echo site_url('site/tecnologia'); ?>#envolva">Envolvimento</a></li>
                                    <li><a href="<?php echo site_url('site/tecnologia'); ?>#gamificacao">Futuro</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="module language left">
                        <ul class="menu">
                            <li class="">
                                <a href="<?php echo site_url('site/sobre'); ?>">Sobre Nós</a>
                                <ul>
                                    <li><a href="<?php echo site_url('site/equipe'); ?>">Equipe</a></li>
                                    <li><a href="<?php echo site_url('site/trabalhe'); ?>">Oportunidades</a></li>
                                    <li><a href="<?php echo site_url('site/sobre'); ?>#parceiros">Parceiros</a></li>
                                    <?php /*                                        <li><a href="<?php echo site_url('site/imprensa'); ?>">Na Imprensa</a></li> */ ?>
                                    <li><a href="<?php echo site_url('site/sobre'); ?>#contato_header">Contato</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="module language left">
                        <ul class="menu">
                            <li class="">
                                <a href="<?php echo site_url('site/suporte'); ?>">Suporte</a>
                                <ul>
                                    <li><a href="<?php echo site_url('site/faq'); ?>#perguntas">FAQs </a></li>
                                    <li><a href="<?php echo site_url('site/downloads'); ?>#downloads">Downloads</a></li>
                                    <li><a href="<?php echo site_url('site/chamados'); ?>#chamados">Chamados </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>