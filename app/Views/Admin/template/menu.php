
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
                    <li class="<?php if ($method == 'index') echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= base_url('admin'); ?>">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li class="<?php if ($method == 'entities') echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= base_url('admin/entities'); ?>">
                            <i class="fas fa-building" aria-hidden="true"></i>
                            <span>Entidades</span>
                        </a>
                    </li>
                    <li class="<?php if ($method == 'users') echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= base_url('admin/users'); ?>">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <span>Usuários</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- end: sidebar -->