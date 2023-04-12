

            </div>
        </section>

        <!-- Vendor -->
		<script src="<?php echo base_url('vendor/jquery/jquery.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/jquery-browser-mobile/jquery.browser.mobile.js'); ?>"></script>
        <script src="<?= base_url('vendor/datatables/media/js/jquery.dataTables.min.js'); ?>"></script>
        <script src="<?= base_url('vendor/datatables/media/js/dataTables.bootstrap5.min.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/popper/umd/popper.min.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/bootstrap-datepicker/js/bootstrap-datepicker.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/common/common.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/nanoscroller/nanoscroller.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/magnific-popup/jquery.magnific-popup.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/jquery-placeholder/jquery.placeholder.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/moment/moment.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/moment/locale/pt-br.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/daterangepicker/daterangepicker.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/apexcharts/dist/apexcharts.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/ios7-switch/ios7-switch.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/bootstrapv5-multiselect/js/bootstrap-multiselect.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/jquery-mask-plugin/jquery.mask.min.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/select2/js/select2.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/select2/js/i18n/pt-BR.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/pnotify/pnotify.custom.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/jquery-validation/jquery.validate.min.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/jquery-validation/localization/messages_pt_BR.min.js'); ?>"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="<?php echo base_url('assets/js/theme.js'); ?>"></script>

		<!-- Theme Custom -->
		<script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>

		<!-- Theme Initialization Files -->
		<script src="<?php echo base_url('assets/js/theme.init.js'); ?>"></script>

        <!-- For specific pages -->
        <?php if (in_array($method, array('profile'))) : ?>
            <script src="<?php echo base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.js'); ?>"></script>
            <script src="<?php echo base_url('vendor/croppie/croppie.js'); ?>"></script>
            <script src="<?php echo base_url('vendor/bootstrap-fileupload/bootstrap-fileupload.min.js'); ?>"></script>
        <?php endif; ?>

        <!-- Actual Specific Page -->
        <?php if (file_exists('assets/js/pages/' . strtolower($class) . '/' . strtolower($method) . '.js') || file_exists('public/assets/js/pages/' . strtolower($class) . '/' . strtolower($method) . '.js')) : ?>
            <script src="<?= base_url('assets/js/pages/' . strtolower($class) . '/' . strtolower($method) . '.js'); ?>"></script>
        <?php endif; ?>

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