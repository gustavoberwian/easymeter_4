<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header">
        <h2>Consumo de Gás</h2>
        <div class="right-wrapper text-right">
            <ol class="selector">
                <?php if($this->ion_auth->in_group('agua')): ?>
                    <li><a href="<?= site_url('painel/agua'); ?>"><i class="fas fa-tint text-muted"></i></a></li>
                <?php endif; ?>
                <li><a><i class="fas fa-fire"></i></a></li>
                <?php if($this->ion_auth->in_group('energia')): ?>
                    <li><a href="<?= site_url('painel/energia'); ?>"><i class="fas fa-bolt text-muted"></i></a></li>
                <?php endif; ?>
            </ol>
        </div>
    </header>
    <?php if($notificacao) : ?>
        <div class="alert alert-warning">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>Cadastre sua última conta!</strong> Cadastrando sua última conta a previsão fica mais correta. Clique <a href="" class="alert-link btn-cadastrar">aqui</a> para cadastrar.
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-4 order-3">
            <section class="card card-previsao mb-4 h-100">
                <div class="card-body pb-0 loading-overlay-showing" data-loading-overlay style="background-color: #ed9c28;">
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="card card-leitura mb-4 h-100">
                <div class="card-body pb-0">
                    <h6 class="card-body-title mb-2 mt-0 text-primary">Leitura Atual<i class="float-right fas fa-fire color-gas"></i></h6>
                    <h2 class="mt-0 mb-2 leitura"><?= $leitura; ?></h2>
                    <?php if($diferenca) : ?>
                        <p class="my-0">O consumo hoje está <span class="text-<?= $diferenca['color']?>"><?= $diferenca['value']?></span> do que ontem.</p>
                    <?php endif; ?>
                </div>
                <script>
                    var bar_data = <?php echo json_encode($bar_data, JSON_NUMERIC_CHECK ); ?>;
                </script>
                <div class="card-footer p-0">
                    <div id="rs3" class="last-24h"></div>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="card card-comparativo mb-4 h-100">
                <div class="card-body pb-0">
                    <h6 class="card-body-title mb-2 mt-0 text-primary">Consumo <?= $aviso; ?></h6>
                    <div class="d-flex mb-3">
                        <div class="bd-r pr-2">
                            <label>Você</label>
                            <p class="font-weight-bold mb-2"><?= $voce; ?> kg</p>
                        </div>
                        <?php if ($vizinhos): ?>
                            <div class="bd-r px-2">
                                <label>Vizinhos</label>
                                <p class="font-weight-bold mb-2"><?= $vizinhos; ?> kg</p>
                            </div>
                        <?php endif; ?>
                        <div class="pl-2">
                            <label>Brasil</label>
                            <p class="font-weight-bold mb-2"><?= $brasil; ?> kg</p>
                        </div>
                    </div>
                    <div class="progress progress-md mb-1">
                        <div class="progress-bar" role="progressbar" style="width:<?= $voce / $max * 100; ?>%; background-color:#7CBDDF;"><div class="progress-bar-title">Você</div></div>
                    </div>
                    <?php if ($vizinhos): ?>
                        <div class="progress progress-md mb-1">
                            <div class="progress-bar" role="progressbar" style="width:<?= $vizinhos / $max * 100; ?>%; background-color:#03aeef;"><div class="progress-bar-title">Vizinhos</div></div>
                        </div>
                    <?php endif ;?>
                    <div class="progress progress-md mb-0">
                        <div class="progress-bar" role="progressbar" style="width:<?= $brasil / $max * 100; ?>%; background-color:#5B93D3;"><div class="progress-bar-title">Brasil</div></div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <section class="card h-100 card-contas card-easymeter mb-4">
                <header class="card-header">
                    <div class="card-actions buttons">
                        <button class="btn btn-xs btn-primary btn-cadastrar">Cadastrar Conta</button>
                    </div>
                    <h2 class="card-title"><i class="fas fa-file-invoice-dollar mr-1"></i> Contas Cadastradas</h2>
                </header>
                <div class="card-body">
                    <table class="table table-hover" id="dt-contas" data-url="<?= site_url('ajax/get_contas_unidade'); ?>">
                        <thead>
                        <tr role="row">
                            <th width="12%">Usuário</th>
                            <th width="16%">Competência</th>
                            <th width="16%">Inicio</th>
                            <th width="16%">Final</th>
                            <th width="16%">Consumo</th>
                            <th width="14%">Valor</th>
                            <th width="10%">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer"></div>
            </section>
        </div>
        <div class="col-md-4">
            <section class="card h-100 card-easymeter card-alertas mb-4">
                <header class="card-header">
                    <h2 class="card-title"><i class="fas fa-bell mr-1"></i> Últimos Alertas</h2>
                </header>
                <div class="card-body p-0 pt-2">

                    <div class="list-group list-group-flush">

                        <?php foreach($alertas as $a) { ?>
                            <div class="list-group-item">
                                <div class="media">
                                    <img src="/assets/img/<?= $a->tipo; ?>.png" class="wd-30 rounded-circle" alt="">
                                    <div class="media-body mg-l-10">
                                        <h6 class="my-0 tx-inverse tx-13">
                                            <a class="alerta <?php if(is_null($a->lida)) echo 'unread' ?>" href="#" data-id="<?= $a->id; ?>"><?= $a->titulo; ?></a>
                                        </h6>
                                        <p class="mb-0 text-muted tx-12 info-<?= $a->id; ?>">
                                            <?= $a->enviada; ?>
                                            <?php if(!is_null($a->lida)) { ?>
                                                <span class="float-right"><i class="far fa-check-circle text-success"></i> <?= $a->lida; ?></span>
                                            <?php } ?>
                                        </p>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 tx-13"><?= $a->texto; ?></p>
                            </div>
                        <?php } ?>
                        <?php if(count($alertas) == 0) { ?>
                            <div class="text-center text-muted mt-3">Nenhum alerta recebido</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="<?= site_url('painel/alertas/gas'); ?>" class="tx-12">Ver todos</a>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <section class="card h-100 card-chart-bar card-easymeter mb-4">
                <header class="card-header">
                    <?php if(count($entradas) > 1): ?>
                        <div class="card-actions buttons" style="right:230px">
                            <button class="btn btn-primary btn-ops" data-toggle="dropdown"><i class="fas fa-filter"></i></button>
                            <ul class="dropdown-menu dropdown-menu-config" role="menu">
                                <li><a href="#" class="filter" data-filter="todos"><i class="fas fa-check"></i> Todas</a></li>
                                <?php $i = 0; foreach($entradas as $e) { ?>
                                    <li><a href="#" class="filter" data-filter="<?= $i; ?>"><i class="fas fa-none"></i> <?= $e->nome; ?></a></li>
                                    <?php
                                    $i++;
                                } ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="card-actions buttons">
                        <div id="daterange" class="btn btn-primary">
                            <i class="fa fa-calendar"></i>&nbsp;<span></span>
                        </div>
                    </div>
                    <h2 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Ciclo</h2>
                </header>
                <div class="card-body chart-bar-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                    <div class="chart-container">
                        <canvas id="bar-chart"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>