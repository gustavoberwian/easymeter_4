<footer id="footer">
    <div class="row footer-widgets d-flex justify-content-center ">
        <div class="col-md-4 d-flex col-sm-6 footer-card ">
            <img src="/assets/img/site/easymeter_rodape_logo.svg" alt="logo" width="110px">
        </div>
        <div class="col-md-2 col-sm-6 footer-card ">
            <div class="widget">
                <ul>
                    <li><a href="#sobrenos" class="footer-link">Sobre nós</a></li>
                    <hr class="footer-hr">
                    <li><a href="#plataforma" class="footer-link">A Plataforma</a></li>
                    <hr class="footer-hr">
                    <li><a href="#diferenciais" class="footer-link">Diferenciais</a></li>
                    <hr class="footer-hr">
                    <li><a href="#individualizacao" class="footer-link">Individualização</a></li>
                    <hr class="footer-hr">
                    <li><a href="#aplicacao" class="footer-link">Onde se Aplica</a></li>
                    <hr class="footer-hr">
                    <li>
                        <p class="footer-link btn-contato ">Contato</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 footer-card ">
            <div class="widget">
                <ul>
                    <li class="footer-list ">
                        <img src="/assets/img/site/easymeter_rodape_icone_fone.svg" alt="fone" width="30px">
                        <a href="tel:+5508005916181" class="number_footer">0800 591 6181</a>
                    </li>
                    <li class="footer-list ">
                        <img src="/assets/img/site/easymeter_rodape_icone_email.svg" alt="hello" width="30px">
                        <a href="mailto:contato@easymeter.com.br">contato@easymeter.com.br</a>
                    </li>

                    <li class="footer-list ">
                        <img src="/assets/img/site/easymeter_rodape_icone_endereco.svg" class="point_icon" alt="hello" width="30px">
                        <address class="footer-address">
                            Rua Rumania, 172.<br>
                            Bairro Rincão.<br>
                            Novo Hamburgo - RS.<br>
                            CEP: 93348-480
                            <hr>
                            <div class="footer-uno">
                                <small>Uma solução</small>
                                <img src="/assets/img/site/easymeter_rodape_logo_unorobotica.svg" alt="UNO_Robótica" width="70px">
                            </div>
                        </address>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<div class="d-flex copywrite">
    <div class="col-sm-6 ">
        <span class="sub">© Copyright 2018-<?= date('Y'); ?> - UNO Robótica</span>
    </div>
    <div class="col-sm-6 text-right ">
        <ul class="list-inline social-list">
            <li class="list-inline-item"><a href="https://twitter.com/easymeterbr" target="_blank"><i class="ti-twitter-alt"></i></a></li>
            <li class="list-inline-item"><a href="https://www.facebook.com/easymeterbr" target="_blank"><i class="ti-facebook"></i></a></li>
            <li class="list-inline-item"><a href="https://www.instagram.com/easymeter/" target="_blank"><i class="ti-instagram"></i></a></li>
        </ul>
    </div>
</div>
</div>


<script src="<?php echo base_url('vendor/swiper/swiper-bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('vendor/flexslider/jquery.flexslider-min.js'); ?>"></script>

<?php if (in_array($method, array('imprensa'))) : ?>
    <script src="<?php echo base_url('vendor/masonry/dist/masonry.pkgd.min.js'); ?>"></script>
<?php endif; ?>

<script src="<?php echo base_url('vendor/jquery-smooth-scroll/jquery.smooth-scroll.min.js'); ?>"></script>
<!-- <script src="<?php echo base_url('vendor/parallax/parallax.js'); ?>"></script> -->
<script src="<?php echo base_url('vendor/bootbox/bootbox.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/scripts.js'); ?>"></script>

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

<script src="https://unpkg.com/scrollreveal"></script>
<script src="<?php echo base_url('assets/js/pages/site/custom.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/pages/site/jquery.js'); ?>"></script>


</body>

</html>