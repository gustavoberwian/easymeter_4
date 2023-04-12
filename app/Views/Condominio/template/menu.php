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
                    <?php if (!$user->inGroup('industria')) { ?>
                        <li class="<?php if(in_array($method, array('index'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url('condominio'); ?>">
                                <i class="fas fa-home" aria-hidden="true"></i>
                                <span>Inicio</span>
                            </a>
                        </li>
                    <?php } if ($user->inGroup('trc')) { ?>
                        <li class="<?php if(in_array($method, array('clientes', 'cdd'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?php echo site_url('condominio/clientes'); ?>">
                                <i class="fas fa-address-card"></i>
                                <span>CDD's</span>
                            </a>
                        </li>
                    <?php } if ($user->inGroup('agua') && !$user->inGroup('industria')) { ?>
                        <li class="<?php if(in_array($method, array('agua'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?php echo site_url('condominio/agua'); ?>">
                                <i class="fas fa-tint"></i>
                                <span>Água</span>
                            </a>
                        </li>
                    <?php } if($user->inGroup('gas') && !$user->inGroup('industria')) { ?>
                        <li class="<?php if(in_array($method, array('gas'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?php echo site_url('condominio/gas'); ?>">
                                <i class="fas fa-fire"></i>
                                <span>Gás</span>
                            </a>
                        </li>
                    <?php } if($user->inGroup('energia')) { ?>
                        <li class="<?php if(in_array($method, array('energia'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?php echo site_url('condominio/energia'); ?>">
                                <i class="fas fa-bolt"></i>
                                <span>Energia Elétrica</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (($user->inGroup('sindicos') && !$user->inGroup('industria')) or $user->inGroup('superadmin')) { ?>
                        <li class="d-none d-lg-block <?php if(in_array($method, array('gestao'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url('condominio/gestao'); ?>">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Gestão da Água</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($user->inGroup('sindicos') or $user->inGroup('superadmin')) { ?>
                        <?php if ($user->condo->m_gas == 2) { ?>
                            <li class="d-none d-lg-block <?php if(in_array($method, array('leituras', 'leitura'))) echo 'nav-active'; ?>">
                                <a class="nav-link" href="<?= site_url('condominio/leituras'); ?>">
                                                    <span class="fa-stack">
                                                        <i class="fas fa-file fa-stack-1x"></i>
                                                        <i class="fas fa-fire fa-stack-1x fa-inverse"></i>
                                                    </span>
                                    <span>Leituras do Gás</span>
                                </a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($user->inGroup('demo') or $user->inGroup('superadmin')) { ?>
                        <li class="<?php if(in_array($method, array('reservatorio'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url('condominio/reservatorio'); ?>">
                                <i class="fas fa-database"></i>f
                                <span>Reservatórios</span>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($user->inGroup('sindicos', 'zelador', 'industria') or $user->inGroup('superadmin')) { ?>
                        <li class="<?php if(in_array($method, array('unidades'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url('condominio/unidades'); ?>">
                                <i class="fas fa-building"></i>
                                <?php if ($user->inGroup('industria')) { ?>
                                    <span>Medidores</span>
                                <?php } else { ?>
                                    <span>Unidades</span>
                                <?php } ?>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($user->inGroup('industria')) { ?>
                        <li class="<?php if(in_array($method, array('relatorios'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url('condominio/relatorios'); ?>">
                                <i class="fas fa-file-invoice"></i>
                                <span>Relatórios</span>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="<?php if(in_array($method, array('alertas'))) echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= site_url('condominio/alertas'); ?>">
                            <span class="float-right badge badge-danger badge-alerta" data-count="<?= $user->alerts; ?>"><?= $user->alerts; ?></span>
                            <i class="fas fa-bell"></i>
                            <span>Alertas</span>
                        </a>
                    </li>
                    <?php /*if($user->inGroup('sindicos') and !($user->inGroup('unidades') or $user->inGroup('proprietarios'))) : ?>
                                        <li class="<?php if(in_array($method, array('suporte'))) echo 'nav-active'; ?>">
                                            <a class="nav-link" href="<?= site_url('condominio/suporte'); ?>">
                                                <span class="float-right badge badge-success">1</span>
                                                <i class="fas fa-ambulance"></i>
                                                <span>Suporte</span>
                                            </a>
                                        </li>
                                    <?php endif; */?>
                    <?php if ($user->inGroup('proprietarios')) { ?>
                        <li class="d-md-none <?php if(in_array($method, array('usuarios'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url('condominio/usuarios'); ?>">
                                <i class="fas fa-user-friends"></i>
                                <span>Usuários</span>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="d-md-none <?php if(in_array($method, array('profile'))) echo 'nav-active'; ?>">
                        <a class="nav-link" href="<?= site_url('condominio/profile'); ?>">
                            <i class="fas fa-cog"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                   <!-- <?php /*if (strpos($this->agent->agent_string(), 'Easymeter') === false) : */?>
                        <li class="d-md-none">
                            <a class="nav-link" href="<?/*= site_url('auth/logout'); */?>">
                                <i class="fas fa-power-off"></i>
                                <span>Sair</span>
                            </a>
                        </li>
                    <?php /*else: */?>
                        <li class="d-md-none">
                            <a class="nav-link logout" href="<?/*= site_url('auth/logout'); */?>">
                                <i class="fas fa-user-times"></i>
                                <span>Trocar Unidade</span>
                            </a>
                        </li>
                        <li class="d-md-none">
                            <a class="nav-link quit" href="#">
                                <i class="fas fa-power-off"></i>
                                <span>Fechar</span>
                            </a>
                        </li>
                    --><?php /*endif; */?>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- end: sidebar -->
