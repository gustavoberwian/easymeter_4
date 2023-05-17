
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
                    <?php if (in_array($method, array('index', 'profile'))): ?>

                        <li class="<?php if (in_array($method, array('index'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url($url); ?>">
                                <i class="fas fa-home" aria-hidden="true"></i>
                                <span>Início</span>
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="<?php if (in_array($method, array('index'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url($url); ?>">
                                <i class="fas fa-arrow-alt-circle-left" aria-hidden="true"></i>
                                <span>Voltar</span>
                            </a>
                        </li>

                        <li class="<?php if (in_array($method, array('unidades', 'unidade'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url($url . '/unidades/' . $entidade_id); ?>">
                                <i class="fas fa-building"></i>
                                <span>Unidades</span>
                            </a>
                        </li>

                        <li class="<?php if (in_array($method, array('fechamentos')) || in_array($method, array('fechamento')) || in_array($method, array('relatorio'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url($url . '/fechamentos/' . $entidade_id); ?>">
                                <i class="fas fa-file-invoice"></i>
                                <span>Fechamentos</span>
                            </a>
                        </li>

                        <li class="<?php if(in_array($method, array('alertas'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url($url . '/alertas/' . $entidade_id); ?>">
                                <span class="float-end badge badge-danger badge-alerta" data-count="<?= $user->alerts; ?>"><?= $user->alerts; ?></span>
                                <i class="fas fa-bell"></i>
                                <span>Alertas</span>
                            </a>
                        </li>

                        <li class="<?php if (in_array($method, array('configuracoes'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url($url . '/configuracoes/' . $entidade_id); ?>">
                                <i class="fas fa-cogs"></i>
                                <span>Configurações</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- end: sidebar -->