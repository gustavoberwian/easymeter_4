<!-- start: sidebar -->
<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html"
            data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="<?php if ($method == 'index')
                        echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= base_url('admin'); ?>">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li class="<?php if ($method == 'entities')
                        echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= base_url('admin/entities'); ?>">
                            <i class="fas fa-building" aria-hidden="true"></i>
                            <span>Entidades</span>
                        </a>
                    </li>
                    <li class="<?php if (in_array($method, array('centrais')))
                        echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?php echo site_url('admin/centrais'); ?>">
                            <i class="fas fa-microchip"></i>
                            <span>Centrais</span>
                        </a>
                    </li>

                    <li
                        class="nav-parent <?php if (in_array($method, array('relatorios')))
                            echo 'nav-expanded nav-active'; ?>">
                        <a class="nav-link" href="<?php echo site_url('admin/relatorios'); ?>">
                            <i class="fas fa-file-alt"></i>
                            <span>Relatórios</span>
                        </a>

                    </li>

                    <li class="<?php if ($method == 'users')
                        echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= base_url('admin/users'); ?>">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <span>Usuários</span>
                        </a>
                    </li>
                    <li class="<?php if (in_array($method, array('contatos')))
                        echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?php echo site_url('admin/contatos'); ?>">
                            <span class="float-right badge badge-danger badge-contato"
                                data-count="<?= $chamados_unread; ?>"><?= $chamados_unread; ?></span>
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Contatos</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- end: sidebar -->