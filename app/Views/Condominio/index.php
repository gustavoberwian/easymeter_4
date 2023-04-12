<section role="main" class="content-body">
    <!-- start: page -->

    <?php if($user->inGroup('administradora')): ?>

        <section class="card">
            <header class="card-header">
                <h2 class="card-title">Condomínios</h2>
            </header>
            <div class="card-body">
                <div id="dt-condos_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable table-hover table-click no-footer" id="dt-condos">
                            <thead>
                            <tr role="row">
                                <th width="20%">Nome</th>
                                <th width="5%">Tipo</th>
                                <th width="5%">Monitora</th>
                                <th width="25%">Endereço</th>
                                <th width="20%">Municipio</th>
                                <th width="25%">Síndico</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($user->condos as $c): ?>
                                <tr data-id="<?= $c->id; ?>">
                                    <td><?= $c->nome; ?></td>
                                    <td><?= ucfirst($c->tipo); ?></td>
                                    <td class="dt-body-center monitor">
                                        <?php
                                        $ret = '';
                                        if ($c->m_agua == 1) $ret .= '<i class="fas fa-tint color-agua" title="Água"></i> ';
                                        if ($c->m_gas == 1) $ret .= '<i class="fas fa-fire color-gas" title="Gás"></i> ';
                                        if ($c->m_gas == 2) $ret .= '<i class="fas fa-fire color-gas" title="Gás Mensal"></i> ';
                                        if ($c->m_energia == 1) $ret .= '<i class="fas fa-bolt color-energia" title="Energia Elétrica"></i>';
                                        echo $ret;
                                        ?>
                                    </td>
                                    <td><?= $c->logradouro.', '.$c->numero; ?></td>
                                    <td><?= $c->cidade.'/'.$c->uf; ?></td>
                                    <td><?= $c->sindico; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>



    <?php elseif($user->inGroup('admin') && !$user->inGroup('unity')): ?>

        <div class="row mt-3 mt-md-0">
            <?php if ($user->condo->m_agua): ?>
                <div class="col-xl-4">
                    <section class="card card-horizontal mb-4">
                        <header class="card-header bg-primary" style="width:150px">
                            <div class="card-header-icon">
                                <i class="fas fa-tint"></i>
                            </div>
                        </header>
                        <div class="card-body p-4">
                            <a class="nav-link p-0" href="<?= site_url('painel/gestao'); ?>">
                                <h3 class="font-weight-semibold mt-3">Gestão da Água</h3>
                            </a>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
            <?php if ($user->condo->m_gas): ?>
                <div class="col-xl-4">
                    <section class="card card-horizontal mb-4">
                        <header class="card-header bg-primary" style="width:150px">
                            <div class="card-header-icon">
                                <i class="fas fa-fire"></i>
                            </div>
                        </header>
                        <div class="card-body p-4">
                            <a class="nav-link p-0" href="<?= site_url('painel/leituras'); ?>">
                                <h3 class="font-weight-semibold mt-3">Leituras do Gás</h3>
                            </a>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
            <div class="col-xl-4">
                <section class="card card-horizontal mb-4">
                    <header class="card-header bg-primary" style="width:150px">
                        <div class="card-header-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </header>
                    <div class="card-body p-4">
                        <a class="nav-link p-0" href="<?= site_url('painel/unidades'); ?>">
                            <h3 class="font-weight-semibold mt-3">Unidades</h3>
                        </a>
                    </div>
                </section>
            </div>
        </div>

    <?php else: ?>

    <?php if((substr($central->central, 0, 2) != "53") && $ultima_leitura < time() - 3600 * 3) : ?>
        <div class="alert alert-warning mt-3 mt-md-0">
            <button type="button" class="close d-block d-lg-none" data-dismiss="alert" aria-hidden="true">×</button>
            Devido a uma oscilação na rede GSM, as últimas leituras do consumo da sua unidade não foram enviadas.
            Estamos trabalhando para normalizar a situação o mais rápido possível.
            <br/>Esta instabilidade não causa nenhum problema na leitura dos medidores ou perda de dados.
        </div>
    <?php endif; ?>
    <?php /*
                    <div class="row mt-3 mt-md-0">
                        <div class="col">
                            <button class="btn btn-primary btn-chamado" type="button"><span>Abrir Chamado</span></button>
                        </div>
                    </div>
*/ ?>
        <div class="row pt-3 pt-lg-0">
            <?php if($user->inGroup('agua') and ($user->inGroup('unity') or $user->inGroup('admin'))) : ?>
                <div class="col-md-4">
                    <section class="card card-leitura mb-3">
                        <div class="card-body">
                            <h6 class="card-body-title mb-2 mt-0 text-primary">Água - Leitura Atual<i class="float-end fas fa-tint fa-vazamento"></i></h6>
                            <h2 class="mt-0 mb-2 text-dark"><?= $leitura_agua; ?></h2>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
            <?php if($user->inGroup('gas') and ($user->inGroup('unity') or $user->inGroup('admin'))) : ?>
                <div class="col-md-4">
                    <section class="card card-leitura mb-3">
                        <div class="card-body">
                            <h6 class="card-body-title mb-2 mt-0 text-success">Gás<i class="float-end fas fa-fire"></i></h6>
                            <h2 class="mt-0 mb-2 text-dark"><?= $leitura_gas; ?></h2>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
            <?php if($user->inGroup('energia') and ($user->inGroup('unity') or $user->inGroup('admin'))) : ?>
                <div class="col-md-4">
                    <section class="card card-leitura mb-3">
                        <div class="card-body">
                            <h6 class="card-body-title mb-2 mt-0 text-warning">Energia Elétrica<i class="float-end fas fa-bolt"></i></h6>
                            <h2 class="mt-0 mb-2 text-dark"><?= $leitura_energia; ?></h2>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    <?php if (substr($central->central, 0, 2) != "53") : ?>
        <div class="row pt-0">
            <?php if($user->inGroup('agua') and $user->inGroup('unity', 'admin')) : ?>
                <div class="col-md-4">
                    <section class="card card-leitura mb-3">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title text-primary">Resumo do seu consumo de água</h6>
                            <ul class="simple-bullet-list mb-3">
                                <?php if (!is_null($hora_agua)) : ?>
                                    <li class="blue">
                                        <span class="title">Última Hora <span class="float-end"><?= number_format($hora_agua, 0, '', '.'); ?> L</span></span>
                                        <span class="description truncate">Seu consumo na última hora</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($hoje_agua)) : ?>
                                    <li class="blue">
                                        <span class="title">Hoje <span class="float-end"><?= number_format($hoje_agua, 0, '', '.'); ?> L</span></span>
                                        <span class="description truncate">Seu consumo hoje</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($last_agua)) : ?>
                                    <li class="blue">
                                        <span class="title">Últimas 24h <span class="float-end"><?= number_format($last_agua, 0, '', '.'); ?> L</span></span>
                                        <span class="description truncate">Seu consumo nas últimas 24h</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($fatu_agua) and false) : ?>
                                    <li class="blue">
                                        <span class="title">Período <span class="float-end"><?= number_format($fatu_agua, 0, '', '.'); ?> L</span></span>
                                        <span class="description truncate">Seu consumo desde o último período faturado</span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
            <?php if($user->inGroup('gas') and ($user->inGroup('unity') or $user->inGroup('admin'))) : ?>
                <div class="col-md-4">
                    <section class="card card-leitura mb-3">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title text-success">Resumo do seu consumo de gás</h6>
                            <ul class="simple-bullet-list mb-3">
                                <?php if (!is_null($hora_gas)) : ?>
                                    <li class="green">
                                        <span class="title">Última Hora <span class="float-end"><?= number_format($hora_gas, 0, '', '.'); ?> m³</span></span>
                                        <span class="description truncate">Seu consumo na última hora</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($hoje_gas)) : ?>
                                    <li class="green">
                                        <span class="title">Hoje <span class="float-end"><?= number_format($hoje_gas, 0, '', '.'); ?> m³</span></span>
                                        <span class="description truncate">Seu consumo hoje</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($last_gas)) : ?>
                                    <li class="green">
                                        <span class="title">Últimas 24h <span class="float-end"><?= number_format($last_gas, 0, '', '.'); ?> m³</span></span>
                                        <span class="description truncate">Seu consumo nas últimas 24h</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($fatu_gas) and false) : ?>
                                    <li class="green">
                                        <span class="title">Período <span class="float-end"><?= number_format($fatu_gas, 0, '', '.'); ?> m³</span></span>
                                        <span class="description truncate">Seu consumo desde o último período faturado</span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
            <?php if($user->inGroup('energia') and ($user->inGroup('unity') or $user->inGroup('admin'))) : ?>
                <div class="col-md-4">
                    <section class="card card-leitura mb-3">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title text-warning">Resumo do seu consumo de energia</h6>
                            <ul class="simple-bullet-list mb-3">
                                <?php if (!is_null($hora_energia)) : ?>
                                    <li class="yellow">
                                        <span class="title">Última Hora <span class="float-end"><?= number_format($hora_energia, 0, '', '.'); ?> kWh</span></span>
                                        <span class="description truncate">Seu consumo na última hora</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($hoje_energia)) : ?>
                                    <li class="yellow">
                                        <span class="title">Hoje <span class="float-end"><?= number_format($hoje_energia, 0, '', '.'); ?> kWh</span></span>
                                        <span class="description truncate">Seu consumo hoje</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($last_energia)) : ?>
                                    <li class="yellow">
                                        <span class="title">Últimas 24h <span class="float-end"><?= number_format($last_energia, 0, '', '.'); ?> kWh</span></span>
                                        <span class="description truncate">Seu consumo nas últimas 24h</span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!is_null($fatu_energia) and false) : ?>
                                    <li class="yellow">
                                        <span class="title">Período <span class="float-end"><?= number_format($fatu_energia, 0, '', '.'); ?> kWh</span></span>
                                        <span class="description truncate">Seu consumo desde o último período faturado</span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

        <div class="row pt-0">
            <div class="col-md-4">
                <button class="btn btn-warning btn-block btn-chamado"><i class="fas fa-life-ring"></i> Abrir Chamado</button>
            </div>
        </div>




    <?php endif; ?>
    <!-- end: page -->
</section>