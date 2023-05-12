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
                                <!-- USAR ESTE DE EXEMPLO: <img src="<?= base_url('/assets/img/site/easymeter_rodape_icone_email.svg') ?>" alt="hello" width="30px">-->
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
    </div>
    <br>
    <br>
    <div class="footer-copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="https://easymeter.io/assets/img/site/easymeter_rodape_logo_unorobotica.svg" alt="logo" width="90px">
                    <span class="sub">© 2018-<?= date('Y'); ?><a href="https://goo.gl/maps/YbPLxMmcdBpR9gLE7"> - UNO Robótica - Rumania, 172. Bairro Rincão. Novo Hamburgo - 93348-480 </a></span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="<?php echo base_url('vendor/swiper/swiper-bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('vendor/flexslider/jquery.flexslider-min.js'); ?>"></script>

<?php if (in_array($method, array('imprensa'))) : ?>
    <script src="<?php echo base_url('vendor/masonry/dist/masonry.pkgd.min.js'); ?>"></script>
<?php endif; ?>

<script src="<?php echo base_url('vendor/jquery-smooth-scroll/jquery.smooth-scroll.min.js'); ?>"></script>
<!-- <script src="<?php echo base_url('vendor/parallax/parallax.js'); ?>"></script> -->
<script src="<?php echo base_url('vendor/bootbox/bootbox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/pages/site/home.js'); ?>"></script>

<script type="text/javascript">
    $('button#requestdemosubmit').on("click", function(event) {
        // submit form via ajax, then
        alert('on click');
        event.preventDefault();
        $('#requestdemomodal').modal('hide');
    });
</script>

<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/5b7564bfafc2c34e96e7a0e5/default';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s1.setAttribute('class', 'btn-chat')
        s0.parentNode.insertBefore(s1, s0);
    })();

    Tawk_API.customStyle = {
        visibility: {
            desktop: {
                position: 'br',
                xOffset: 100,
                yOffset: 50
            },
            mobile: {
                position: 'br',
                xOffset: 30,
                yOffset: 30
            },
            bubble: {
                rotate: '0deg',
                xOffset: 20,
                yOffset: 0
            }
        }
    }
</script>
<!--End of Tawk.to Script-->

<a href="https://api.whatsapp.com/send?phone=5551999359616&amp;text=Olá, gostaria de entrar em contato com a Easymeter" class="whatsapp" target="_blank">
    <i class="fab fa-whatsapp whatsapp-float"></i>
</a>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>