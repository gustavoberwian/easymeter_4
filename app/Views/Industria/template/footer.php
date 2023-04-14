</div>
</section>

<!-- Vendor -->
<script src="<?= base_url('vendor/jquery/jquery.js'); ?>"></script>
<script src="<?= base_url('vendor/jquery-browser-mobile/jquery.browser.mobile.js'); ?>"></script>
<script src="<?= base_url('vendor/popper/umd/popper.min.js'); ?>"></script>
<script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('vendor/common/common.js'); ?>"></script>
<script src="<?= base_url('vendor/nanoscroller/nanoscroller.js'); ?>"></script>
<script src="<?= base_url('vendor/datatables/media/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('vendor/datatables/media/js/dataTables.bootstrap5.min.js'); ?>"></script>
<script src="<?= base_url('vendor/pnotify/pnotify.custom.js'); ?>"></script>

<!-- Specific Page Vendor -->
<?php if (in_array($method, array('index'))) : ?>
    <script src="<?php echo base_url('vendor/moment/moment.js'); ?>"></script>
    <script src="<?php echo base_url('vendor/moment/locale/pt-br.js'); ?>"></script>
    <script src="<?php echo base_url('vendor/daterangepicker/daterangepicker.js'); ?>"></script>
    <script src="<?php echo base_url('vendor/apexcharts/dist/apexcharts.js'); ?>"></script>
    <script src="<?php echo base_url('vendor/water_tank/waterTank.js'); ?>"></script>
<?php endif; ?>

<!-- Theme Base, Components and Settings -->
<script src="<?= base_url('assets/js/theme.js'); ?>"></script>

<!-- Theme Custom -->
<script src="<?= base_url('assets/js/custom.js'); ?>"></script>

<!-- Theme Initialization Files -->
<script src="<?= base_url('assets/js/theme.init.js'); ?>"></script>

<!-- Page Specific -->
<?php
if (file_exists('assets/js/pages/' . $class . '/' . $method . '.js')) echo '<script src="' . base_url('assets/js/pages/' . $class . '/' . $method) . '.js"></script>';
?>

<script>
    $(document).ready(function () {
        $(".preloader").fadeOut();
    });
</script>

</body>
</html>