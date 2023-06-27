
<!-- start: sidebar -->
<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="<?php if (in_array($method, array('index'))) echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= site_url("/"); ?>">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Início</span>
                        </a>
                    </li>

                    <li class="<?php if (in_array($method, array('view'))) echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= site_url("mapa/view"); ?>">
                            <i class="fas fa-map" aria-hidden="true"></i>
                            <span>Mapa</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- end: sidebar -->