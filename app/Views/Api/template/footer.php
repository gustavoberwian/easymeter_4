            </div>
        </section>

        <!-- Vendor -->
		<script src="<?php echo base_url('vendor/jquery/jquery.js'); ?>"></script>
		<script src="<?php echo base_url('vendor/jquery-browser-mobile/jquery.browser.mobile.js'); ?>"></script>
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

        <script src="<?php echo base_url('vendor/select2/js/select2.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/select2/js/i18n/pt-BR.js'); ?>"></script>
        <script src="<?php echo base_url('vendor/pnotify/pnotify.custom.js'); ?>"></script>

		<!-- Specific Page Vendor -->
		<!--<script src="vendor/jquery-ui/jquery-ui.js"></script>-->
		<!--<script src="vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js"></script>-->
		<!--<script src="vendor/jquery-appear/jquery.appear.js"></script>-->
		<!--<script src="vendor/bootstrapv5-multiselect/js/bootstrap-multiselect.js"></script>-->
		<!--<script src="vendor/jquery.easy-pie-chart/jquery.easypiechart.js"></script>-->
		<!--<script src="vendor/flot/jquery.flot.js"></script>-->
		<!--<script src="vendor/flot.tooltip/jquery.flot.tooltip.js"></script>-->
		<!--<script src="vendor/flot/jquery.flot.pie.js"></script>-->
		<!--<script src="vendor/flot/jquery.flot.categories.js"></script>-->
		<!--<script src="vendor/flot/jquery.flot.resize.js"></script>-->
		<!--<script src="vendor/jquery-sparkline/jquery.sparkline.js"></script>-->
		<!--<script src="vendor/raphael/raphael.js"></script>-->
		<!--<script src="vendor/morris/morris.js"></script>-->
		<!--<script src="vendor/gauge/gauge.js"></script>-->
		<!--<script src="vendor/snap.svg/snap.svg.js"></script>-->
		<!--<script src="vendor/liquid-meter/liquid.meter.js"></script>-->
		<!--<script src="vendor/jqvmap/jquery.vmap.js"></script>-->
		<!--<script src="vendor/jqvmap/data/jquery.vmap.sampledata.js"></script>-->
		<!--<script src="vendor/jqvmap/maps/jquery.vmap.world.js"></script>-->
		<!--<script src="vendor/jqvmap/maps/continents/jquery.vmap.africa.js"></script>-->
		<!--<script src="vendor/jqvmap/maps/continents/jquery.vmap.asia.js"></script>-->
		<!--<script src="vendor/jqvmap/maps/continents/jquery.vmap.australia.js"></script>-->
		<!--<script src="vendor/jqvmap/maps/continents/jquery.vmap.europe.js"></script>-->
		<!--<script src="vendor/jqvmap/maps/continents/jquery.vmap.north-america.js"></script>-->
		<!--<script src="vendor/jqvmap/maps/continents/jquery.vmap.south-america.js"></script>-->

		<!-- Theme Base, Components and Settings -->
		<script src="<?php echo base_url('assets/js/theme.js'); ?>"></script>

		<!-- Theme Custom -->
		<script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>

		<!-- Theme Initialization Files -->
		<script src="<?php echo base_url('assets/js/theme.init.js'); ?>"></script>

        <?php if (in_array($method, array('profile'))) : ?>
            <script src="<?php echo base_url('vendor/bootstrap-tagsinput/bootstrap-tagsinput.js'); ?>"></script>
            <script src="<?php echo base_url('vendor/croppie/croppie.js'); ?>"></script>
            <script src="<?php echo base_url('vendor/bootstrap-fileupload/bootstrap-fileupload.min.js'); ?>"></script>
        <?php endif; ?>

        <!-- Page Specific -->
        <?php //if (file_exists('assets/js/pages/' . $class . '/' . $method . '.js')) echo '<script src="' . base_url('assets/js/pages/' . $class . '/' . $method . '.js?r=' . time()) . '"></script>'; ?>
        <?php if (file_exists('assets/js/pages/' . $class . '/' . $method . '.js')) echo '<script src="' . base_url('assets/js/pages/' . $class . '/' . $method . '.js') . '"></script>'; ?>

        <script>
            $(document).ready(function() {
                $(".preloader").fadeOut();
            });
        </script>

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