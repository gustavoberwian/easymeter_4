<?php header('Access-Control-Allow-Origin: *'); ?>
<!-- Custom Style -->
<style>
    #map {
        position: fixed;
        width: 100%;
        height: 100%;
    }
    #box {
        position: fixed;
        top: 5px;
        right: 5px;
    }
</style>
<div id="map"></div>
<div id="styleSwitcher" class="style-switcher" style="right: -260px;">

    <a id="styleSwitcherOpen" class="style-switcher-open" href="#"><i class="fas fa-cogs"></i></a>

    <div class="style-switcher-wrap">

        <h4>Style Switcher</h4>

        <h5>Primary Color</h5>

        <div class="style-switcher-buttons options-links">
            <a href="#" class="reset">Reset</a>
        </div>

    </div>

</div>