            </div>
        </section>

        <!-- Vendor -->
        <script type="text/javascript" src="<?= base_url('vendor/maptalks/dat.gui.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/maptalks/maptalks.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/maptalks/maptalksgl.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/maptalks/three.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/maptalks/maptalks.three.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/jquery/jquery.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/jquery-browser-mobile/jquery.browser.mobile.js'); ?>"></script>
        <script src="<?= base_url('vendor/popper/umd/popper.min.js'); ?>"></script>
        <script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
        <script src="<?= base_url('vendor/common/common.js'); ?>"></script>
        <script src="<?= base_url('vendor/nanoscroller/nanoscroller.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/moment/moment.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/moment/locale/pt-br.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/pnotify/pnotify.custom.js'); ?>"></script>

        <!-- Theme Base, Components and Settings -->
        <script type="text/javascript" src="<?= base_url('assets/js/theme.js'); ?>"></script>

        <!-- Theme Custom -->
        <script type="text/javascript" src="<?= base_url('assets/js/custom.js'); ?>"></script>

        <!-- Theme Initialization Files -->
        <script type="text/javascript" src="<?= base_url('assets/js/theme.init.js'); ?>"></script>

        <!-- Page Specific File -->
        <script type="text/javascript" src="<?= base_url('assets/js/pages/' . strtolower($class) . '/' . strtolower($method) . '.js'); ?>"></script>

        <script type="text/javascript" src="<?= base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/croppie/croppie.js'); ?>"></script>
        <script type="text/javascript" src="<?= base_url('vendor/bootstrap-fileupload/bootstrap-fileupload.min.js'); ?>"></script>

        <script>
            $(document).ready(function() {
                $(".preloader").fadeOut();
            });
        </script>

        <?php if ($user->demo) : ?>
            <script>
                notifyAlert('Esta é uma versão de demonstração. Todos os dados contidos aqui são fictícios.');
            </script>
        <?php endif; ?>

        <script>
            var unsaved = false;
            <?php if (in_array($method, array('configuracoes'))) : ?>
            $(window).bind('beforeunload', function() {
                if(unsaved){
                    return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
                }
            });

            $(document).on('change', ':input', function(){
                unsaved = true;
            });

            $(document).on('change', 'select', function(){
                unsaved = true;
            });
            <?php endif; ?>
        </script>
        <script>
            $(document).on("click", ".sidebar-left a.nav-link", function (event) {
                $(".sidebar-left-opened .toggle-sidebar-left").trigger("click");
                if (!window.event.ctrlKey && !unsaved) {
                    $(".preloader").fadeIn();
                }
            });
        </script>
    </body>
</html>