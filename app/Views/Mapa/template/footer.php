</body>
<footer>
    <!-- Vendor -->
    <script type="text/javascript" src="<?= base_url('vendor/maptalks/dat.gui.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('vendor/maptalks/maptalks.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('vendor/maptalks/maptalksgl.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('vendor/maptalks/three.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('vendor/maptalks/maptalks.three.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('vendor/jquery/jquery.js'); ?>"></script>

    <!-- Page Specific File -->
    <script type="text/javascript" src="<?= base_url('assets/js/pages/' . strtolower($class) . '/' . strtolower($method) . '.js'); ?>"></script>
</footer>
</html>