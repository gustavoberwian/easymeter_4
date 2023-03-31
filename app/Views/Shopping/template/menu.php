
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

                    <?php if ($user->inGroup('admin', 'shopping')): ?>
                        <li class="<?php if (in_array($method, array('index'))) echo 'nav-active'; ?>">
                            <a class="nav-link" href="<?= site_url($url); ?>">
                                <i class="fas fa-building" aria-hidden="true"></i>
                                <?php if ($url !== "shopping") : ?>
                                    <span>Estabelecimentos</span>
                                <?php else : ?>
                                    <span>Shoppings</span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (!in_array($method, array('index', 'profile'))): ?>

                        <?php if ($user->inGroup('unity_shopping')): ?>
                            <li class="<?php if (in_array($method, array('unidade')) && !$alerta && !$faturamento) echo 'nav-active'; ?>">
                                <a class="nav-link" href="<?= site_url($url . '/unidade/' . $group_id . '/' . $unidade_id); ?>">
                                    <i class="fas fa-home" aria-hidden="true"></i>
                                    <span>Inicio</span>
                                </a>
                            </li>
                            <?php if ($permission->acessar_lancamentos): ?>
                                <li class="<?php if (in_array($method, array('unidade')) && $faturamento) echo 'nav-active'; ?>">
                                    <a class="nav-link" href="<?= site_url($url . '/unidade/' . $group_id . '/' . $unidade_id . '/faturamentos'); ?>">
                                        <i class="fas fa-file-invoice"></i>
                                        <span>Lançamentos</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="<?php if(in_array($method, array('unidade')) && $alerta) echo 'nav-active'; ?>">
                                <a class="nav-link" href="<?php echo site_url($url . '/unidade/' . $group_id . '/' . $unidade_id . '/alertas'); ?>">
                                    <i class="fas fa-bell"></i>
                                    <span>Alertas</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <?php if ($group->m_energia) : ?>
                                <li class="<?php if (in_array($method, array('energy'))) echo 'nav-active'; ?>">
                                    <a class="nav-link" href="<?= site_url($url . '/energy/' . $group_id); ?>">
                                        <i class="fas fa-bolt" aria-hidden="true"></i>
                                        <span>Energia</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($group->m_agua) : ?>
                                <li class="<?php if (in_array($method, array('water'))) echo 'nav-active'; ?>">
                                    <a class="nav-link" href="<?= site_url($url . '/water/' . $group_id); ?>">
                                        <i class="fas fa-tint" aria-hidden="true"></i>
                                        <span>Água</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($group->m_gas) : ?>
                                <li class="<?php if (in_array($method, array('gas'))) echo 'nav-active'; ?>">
                                    <a class="nav-link" href="<?= site_url($url . '/gas/' . $group_id); ?>">
                                        <i class="fas fa-fire-alt" aria-hidden="true"></i>
                                        <span>Gás</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($group->m_nivel) : ?>
                                <li class="<?php if (in_array($method, array('level'))) echo 'nav-active'; ?>">
                                    <a class="nav-link" href="<?= site_url( $url . '/level/' . $group_id); ?>">
                                        <i class="fas fa-ruler-vertical" aria-hidden="true"></i>
                                        <span>Nível</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="<?php if (in_array($method, array('faturamentos', 'fechamento', 'relatorio', 'lancamento')) || in_array($method, array('fechamento')) || in_array($method, array('relatorio'))) echo 'nav-active'; ?>">
                                <a class="nav-link" href="<?= site_url($url . '/faturamentos/' . $group_id); ?>">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Lançamentos</span>
                                </a>
                            </li>

                            <li class="<?php if(in_array($method, array('alertas'))) echo 'nav-active'; ?>">
                                <a class="nav-link" href="<?= site_url($url . '/alertas/' . $group_id); ?>">
                                    <span class="float-end badge badge-danger badge-alerta" data-count="<?= $user->alerts; ?>"><?= $user->alerts; ?></span>
                                    <i class="fas fa-bell"></i>
                                    <span>Alertas</span>
                                </a>
                            </li>

                            <li class="<?php if (in_array($method, array('insights'))) echo 'nav-active';  ?>">
                                <a class="nav-link" href="<?= site_url($url . '/insights/' . $group_id);  ?>">
                                    <i class="fas fa-lightbulb"></i>
                                    <span>Insights</span>
                                </a>
                            </li>

                            <?php if (!$user->inGroup('unity_shopping')): ?>
                                <li class="<?php if (in_array($method, array('configuracoes', 'users'))) echo 'nav-active'; ?>">
                                    <a class="nav-link" href="<?= site_url($url . '/configuracoes/' . $group_id); ?>">
                                        <i class="fas fa-cogs"></i>
                                        <span>Configurações</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($user->inGroup('superadmin')): ?>
                            <li class="<?php if (in_array($method, array('historico'))) echo 'nav-active'; ?>">
                                <a class="nav-link" href="<?= site_url($url . '/historico/' . $group_id); ?>">
                                    <span class="float-right badge badge-danger badge-log" data-count="<?= $logs ?>"><?= $logs ?></span>
                                    <i class="fas fa-history"></i>
                                    <span>Histórico</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- end: sidebar -->