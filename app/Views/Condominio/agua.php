    <script>
        var primeira_leitura = '<?= $primeira_leitura; ?>';
    </script>
    <section role="main" class="content-body">
        <!-- start: page -->
        <!--data-unidade="<?/*=$unidade->id; */?>" data-medidor="<?/*=$alertas_unidade_conf ? $alertas_unidade_conf['medidor_id'] : ''; "*/?> -->
        <header class="page-header">
			<?php if ($user->inGroup('industria')) : ?>
                <h2>Consumo <?= (!is_null($unidade->bloco) ? $unidade->bloco.' - ' : '').$unidade->nome; ?></h2>
			<?php else: ?>
                <h2>Consumo de Água <?= ($acesso) ? '- Unidade '.(!is_null($unidade->bloco) ? $unidade->bloco.'/' : '').$unidade->nome : ''; ?></h2>

                <div class="right-wrapper text-right">
                    <ol class="selector">
                        <li><a><i class="fas fa-tint"></i></a></li>
                        <?php if($user->inGroup('gas')): ?>
                            <li><a href="<?= site_url('painel/gas'); ?>"><i class="fas fa-fire text-muted"></i></a></li>
                        <?php endif; ?>
                        <?php if($user->inGroup('energia')): ?>
                            <li><a href="<?= site_url('painel/energia'); ?>"><i class="fas fa-bolt text-muted"></i></a></li>
                        <?php endif; ?>
                    </ol>
                </div>
			<?php endif; ?>
        </header>
        <!-- Cards -->
		<?php /*if($ultima_leitura < time() - 3600 * 3) : ?>
                        <div class="alert alert-warning mt-3 mt-md-0">
                            <button type="button" class="close d-block d-lg-none" data-dismiss="alert" aria-hidden="true">×</button>
                            Devido a uma oscilação na rede GSM, as últimas leituras do consumo da sua unidade não foram enviadas.
                            Estamos trabalhando para normalizar a situação o mais rápido possível. 
                            <br/>Esta instabilidade não causa nenhum problema na leitura dos medidores ou perda de dados.
                        </div>
                    <?php endif;*/ ?>
		<?php if ($user->inGroup('industria')) : ?>
            <div class="col-md-2 text-center text-lg-left mb-3">
				<?php if ($user->inGroup('btp')) : ?>
                    <img src="/assets/img/btp.png" height="100">
				<?php elseif ($user->inGroup('ambev')): ?>
                    <img src="/assets/img/ambev.png" height="50" class="mb-4">
                <?php elseif ($user->inGroup('bauducco')): ?>
                    <img src="/assets/img/bauducco.png" height="100" class="mb-4">
				<?php endif; ?>
            </div>
		<?php endif; ?>
        <div class="row">
			<?php if ($user->entity->id != 9 && !$user->inGroup('industria')) : ?>
                <div class="col-md-4">
                    <!-- Leitura Atual -->
                    <section class="card card-leitura mb-3 h-100">
                        <div class="card-body">
                            <h6 class="card-body-title mb-2 mt-0 text-primary">Leitura Atual<i class="float-right fas fa-tint fa-vazamento"></i></h6>
                            <h2 class="mt-0 mb-2 leitura-agua unidade" <?= ($acesso) ? 'data-unidade="'.$unidade->id.'"' : ''; ?> data-monitoramento="agua" data-central="<?= $central->central; ?>"><?= $leitura; ?></h2>
                        </div>
                    </section>
                </div>
			<?php endif; ?>
			<?php if ($user->entity->id != 9 && $user->inGroup('industria')) : ?>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <section class="card card-comparativo h-100 h-100">
                        <div class="card-body" style="background-color: #03aeef;">
                            <h6 class="card-body-title mb-3 mt-0 text-light">Período<i class="float-right fas fa-calendar"></i></h6>
                            <h2 class="d-none mt-0 mb-2 leitura-agua unidade" <?= ($acesso) ? 'data-unidade="'.$unidade->id.'"' : ''; ?> data-monitoramento="<?=$monitoramento; ?>" data-central="<?= $central->central; ?>"><?= $leitura; ?></h2>
                            <div id="daterange" class="btn btn-light w-100 overflow-hidden text-muted">
                                <i class="fa fa-calendar"></i>&nbsp;<span></span>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <!-- Comparativo -->
                    <section class="card card-comparativo mb-4 h-100">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title mb-2 mt-0 text-primary">Consumo do Período</span></h6>
                            <div class="row">
                                <div class="col">
                                    <div class="h5 mb-0 mt-1 consumo-mes-atual"></div>
                                    <p class="text-3 text-muted mb-0">Total do período</p>
                                </div>
                                <div class="col">
                                    <div class="h5 mb-0 mt-1 consumo-ano-atual"></div>
                                    <p class="text-3 text-muted mb-0">Média do período</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
			<?php endif; ?>
			<?php if ($user->entity->id != 9 && !$user->inGroup('industria')) : ?>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <!-- Comparativo -->
                    <section class="card card-comparativo mb-4 h-100">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title mb-2 mt-0 text-primary">Comparativo <span class="float-right"><?= $aviso; ?></span></h6>
                            <div class="row">
                                <div class="col">
                                    <div class="h5 mb-0 mt-1"><?= $voce; ?> L</div>
                                    <p class="text-3 text-muted mb-0">Você</p>
                                </div>
                                <div class="col">
                                    <div class="h5 mb-0 mt-1"><?= $vizinhos; ?> L</div>
                                    <p class="text-3 text-muted mb-0">Vizinhos</p>
                                </div>
                                <div class="col">
                                    <div class="h5 mb-0 mt-1"><?= $brasil; ?> L</div>
                                    <p class="text-3 text-muted mb-0">Brasil</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
			<?php endif; ?>
			<?php if($previsao['mostra']) : ?>
                <div class="col-lg-4 mt-3 mt-lg-0 order-lg-3">
                    <!-- Previsão -->
                    <section class="card card-previsao mb-4 h-100">
                        <div class="card-body pb-0">
                            <h6 class="card-body-title mb-2 mt-0 text-light">Previsão do mês atual
								<?php /*if($previsao['comparativo']) : */?><!--
                                <img src="/assets/img/trending_up.svg" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="A conta deverá ser maior que a anterior">
                            <?php /*else : */?>
                                <img src="/assets/img/trending_down.svg" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="A conta deverá ser menor que a anterior">
                            --><?php /*endif; */?>
                                <!--<i class="fas fa-exclamation-circle ml-1 float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="O valor previsto não inclui o consumo da área comum"></i>-->
                            </h6>
                            <div class="row">

                                <div class="col">
                                    <div class="h5 text-white mb-0 mt-1"><?= $previsao['ate_agora']; ?> <span style="font-size:10px;"></span></div>
                                    <p class="text-3 text-white mb-0">Até agora</p>
                                </div>
                                <div class="col">
                                    <div class="h5 text-white mb-0 mt-1"><?= $previsao['fechamento']; ?> <span style="font-size:10px;"></span></div>
                                    <p class="text-3 text-white mb-0">Previsão</p>
                                </div>
                                <!--<div class="col">
                                <?php /*if(is_null($user->meta_agua)): */?>
                                    <button type="button" class="btn btn-xs btn-primary btn-meta" style="padding: 0.0rem 0.5rem; margin: 7px 0 2px;">Definir Meta</button>
                                <?php /*else: */?>
                                    <div class="h5 text-white mb-0 mt-1" data-toggle="tooltip" data-placement="bottom" title="Você já atingiu 60% da sua meta."><span style="font-size:10px;">R$</span> <abbr><?/*= number_format($user->meta_agua, 2, ',', '.');*/?></abbr></div>
                                <?php /*endif; */?>
                                <p class="text-3 text-white mb-0">Meta</p>
                            </div>-->
                            </div>
                        </div>
                    </section>
                </div>
			<?php endif; ?>
        </div>

		<?php /* if (substr($central->central, 0, 2) == "53") : ?>
                        <div class="alert alert-warning mt-3">
                            A próxima atualização das leituras ocorrerá em 9 horas.
                        </div>
                    <?php endif; */ ?>

        <!-- Faturamentos e Alertas -->
		<?php if ($user->entity->id != 9) : ?>
			<?php if(!$acesso): ?>
                <div class="row d-none d-lg-flex">
                    <div class="col-md-8">
                        <section class="card h-100 card-faturamentos card-easymeter mb-4">
                            <header class="card-header">
                                <div class="card-actions buttons">
                                    <button class="btn btn-primary btn-reload"><i class="fas fa-sync"></i></button>
                                </div>
                                <h2 class="card-title"><i class="fas fa-file-invoice-dollar mr-1"></i> Faturamentos</h2>
                            </header>
                            <div class="card-body">
                                <table class="table table-hover" id="dt-faturamentos" data-url="<?= site_url('ajax/get_fechamentos_unidade'); ?>">
                                    <thead>
                                    <tr role="row">
                                        <th width="0%" class="d-none">id</th>
                                        <th width="14%">Comp</th>
                                        <th width="15%">Consumo</th>
                                        <th width="12%">Básico</th>
                                        <th width="12%">Comum</th>
                                        <th width="15%">Taxas</th>
                                        <th width="12%">Gestão</th>
                                        <th width="15%">Total</th>
                                        <th width="5%">Ações</th>
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
                                <h2 class="card-title"><i class="fas fa-bell mr-1"></i> Alertas Ativos</h2>
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
														<?= time_ago($a->enviada); ?>
														<?php if(!is_null($a->lida)) { ?>
                                                            <span class="float-right"><i class="far fa-eye text-success" title="Visualizado"></i> <?= time_ago($a->lida); ?></span>
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
                                <a href="<?= site_url('painel/alertas/agua'); ?>" class="tx-12">Ver todos</a>
                            </div>
                        </section>
                    </div>
                </div>
			<?php endif; ?>
		<?php endif; ?>
        <!-- Gráfico -->
        <div class="row">
            <div class="col-md-12">
                <section class="card h-100 card-chart-bar card-easymeter mb-4">
                    <header class="card-header">
						<?php if(!$user->inGroup('industria') && count($entradas) > 1): ?>
                            <div class="card-actions buttons d-none d-lg-block" style="right:225px">
                                <button class="btn btn-primary btn-ops" data-toggle="dropdown"><i class="fas fa-filter"></i></button>
                                <ul class="dropdown-menu dropdown-menu-config" role="menu">
                                    <li><a href="#" class="filter" data-filter="todos"><i class="fas fa-check"></i> Todas</a></li>
									<?php $i = count($entradas) - 1; foreach($entradas as $e) { ?>
                                        <li><a href="#" class="filter" data-filter="<?= $i; ?>"><i class="fas fa-none"></i> <?= ($user->inGroup('demo') && $e->nome == "Escada") ? "Cozinha" : $e->nome; ?></a></li>
										<?php
										$i--;
									} ?>
                                </ul>
                            </div>
						<?php endif; ?>
                        <div class="card-actions buttons d-none" style="right:225px">
                            <button class="btn btn-primary btn-units" data-toggle="dropdown" style="width: 45px;">L</button>
                            <ul class="dropdown-menu dropdown-menu-unit" role="menu">
                                <li><a href="#" class="filter" data-filter="litros"><i class="fas fa-check"></i> Litros</a></li>
                                <li><a href="#" class="filter" data-filter="metros"><i class="fas fa-none"></i> M&sup3;</a></li>
                            </ul>
                        </div>
						<?php if(!$user->inGroup('industria')): ?>
                            <div class="card-actions buttons">
                                <div id="daterange" class="btn btn-primary">
                                    <i class="fa fa-calendar"></i>&nbsp;<span></span>
                                </div>
                            </div>
						<?php endif; ?>
                        <h2 class="card-title mt-2 mt-lg-0"><i class="fas fa-chart-bar mr-1"></i> Consumo</h2>
                    </header>
                    <div class="card-body chart-bar-body" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                        <div class="chart-container">
                            <canvas id="bar-chart"></canvas>
                        </div>
                    </div>
					<?php if ($user->inGroup('industria')) : ?>
                        <div class="card-footer total d-none text-right">
                            <div class="row">
                                <div class="col-12 col-lg-3 text-center">
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <p class="text-3 text-muted mb-0">Do Período:</p>
                                        </div>
                                        <div class="col-6 ">
                                            <p class="text-3 text-muted mb-0"><span class="h6 text-primary total"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3 text-center">
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <p class="text-3 text-muted mb-0">Média:</p>
                                        </div>
                                        <div class="col-6 ">
                                            <p class="text-3 text-muted mb-0"><span class="h6 text-primary medio"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3 text-center">
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <p class="text-3 text-muted mb-0">Mínimo:</p>
                                        </div>
                                        <div class="col-6 ">
                                            <p class="text-3 text-muted mb-0"><span class="h6 text-primary minimo"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3 text-center">
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <p class="text-3 text-muted mb-0">Máximo:</p>
                                        </div>
                                        <div class="col-6 ">
                                            <p class="text-3 text-muted mb-0"><span class="h6 text-primary maximo"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php endif; ?>
                </section>
            </div>
        </div>
		<?php if ($user->inGroup('industria')) : ?>
            <div class="row">
                <div class="col-md-8">
                    <section class="card h-100 card-chart-bar card-easymeter mb-4">
                        <header class="card-header">
                            <!--<div class="card-actions buttons">
                                <div id="daterange" class="btn btn-primary">
                                    <i class="fa fa-calendar"></i>&nbsp;<span></span>
                                </div>
                            </div>-->
                            <h2 class="card-title mt-2 mt-lg-0"><i class="fas fa-chart-bar mr-1"></i> Histórico</h2>
                        </header>
						<?php if ($user->inGroup('industria')) : ?>
                            <div class="card-body chart-bar-body-historico" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <canvas id="bar-chart-historico" data-tipo="geral"></canvas>
                                </div>
                            </div>
						<?php else: ?>
                            <div class="card-body chart-bar-body-geral" data-loading-overlay data-loading-overlay-options='{ "css": { "backgroundColor": "#00000080" } }'>
                                <div class="chart-container">
                                    <canvas id="bar-chart-geral" data-tipo="geral"></canvas>
                                </div>
                            </div>
						<?php endif; ?>
						<?php if (!$user->inGroup('industria')) : ?>
                            <div class="card-footer total d-none text-right">
                                <div class="row">
                                    <div class="col-12 col-lg-3 text-center">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <p class="text-3 text-muted mb-0">Do Período:</p>
                                            </div>
                                            <div class="col-6 ">
                                                <p class="text-3 text-muted mb-0"><span class="h6 text-primary total-geral"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-3 text-center">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <p class="text-3 text-muted mb-0">Média:</p>
                                            </div>
                                            <div class="col-6 ">
                                                <p class="text-3 text-muted mb-0"><span class="h6 text-primary medio-geral"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-3 text-center">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <p class="text-3 text-muted mb-0">Mínimo:</p>
                                            </div>
                                            <div class="col-6 ">
                                                <p class="text-3 text-muted mb-0"><span class="h6 text-primary minimo-geral"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-3 text-center">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <p class="text-3 text-muted mb-0">Máximo:</p>
                                            </div>
                                            <div class="col-6 ">
                                                <p class="text-3 text-muted mb-0"><span class="h6 text-primary maximo-geral"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
                    </section>
                </div>

                <div class="col-md-4">
                    <section class="card h-100 card-chart-bar card-easymeter mb-4">
                        <header class="card-header">
                            <h2 class="card-title">Alertas</h2>

                            <div class="card-actions buttons alerts">
                                <button class="btn btn-primary btn-incluir dropdown-toggle1 ml-3" data-toggle="dropdown">
                                    <i class="fa fa-filter"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-config">
                                    <li><a href="#" class="monitor" data-filter=""><i class="far fa-check-square"></i>Todos</a></li>
                                    <li><a href="#" class="monitor" data-filter="0"><i class="far fa-square"></i>Ativos</a></li>
                                    <li><a href="#" class="monitor" data-filter="0"><i class="far fa-square"></i>Inativos</a></li>
                                    <li><a href="#" class="monitor" data-filter="1"><i class="far fa-square"></i>Finalizados</a></li>
                                </ul>
                                <button class="btn btn-primary btn-newAlerta"><i class="fa fa-cog"></i></button>
                            </div>
                        </header>
                        <div class="card-body" id="alertas-industria">
							<?php foreach($alertas_unidade as $a) { ?>
                                <div class="list-group-item" style="cursor: pointer" data-alerta="<?=$a->id; ?>">
                                    <div class="media">
                                        <!--<img src="/assets/img/<?/*= $a->tipo; */?>.png" class="wd-30 rounded-circle" alt="">-->
                                        <div class="media-body mg-l-10">
                                            <h6 class="my-0 tx-inverse tx-13">
                                                <a class="alerta <?php if(is_null($a->lida)) echo 'unread' ?>" data-id="<?= $a->id; ?>"><?= $a->titulo; ?></a>
                                            </h6>
                                            <p class="mb-0 text-muted tx-12 info-<?= $a->id; ?>">
												<?= time_ago($a->enviada); ?>
												<?php if(!is_null($a->lida)) { ?>
                                                    <span class="float-right"><i class="far fa-eye text-success" title="Visualizado"></i> <?= date('d/m/Y', strtotime($a->lida)); ?></span>
												<?php } else { ?>
                                                    <span class="float-right"><i class="far fa-eye text-danger" title="Não Visualizado"></i> Não lida</span>
												<?php } ?>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="mt-0 mb-0 tx-13"><?= $a->texto; ?></p>
                                </div>
							<?php } ?>
							<?php if(!$alertas_unidade) { ?>
                                <div class="text-center text-muted mt-3">Nenhum alerta recebido</div>
							<?php } ?>
                        </div>
                        <div class="card-footer text-right">
                            <a href="<?= site_url('painel/alertas/'); ?>" class="tx-12">Ver todos</a>
                        </div>
                    </section>
                </div>
            </div>
		<?php endif; ?>


        <!-- end: page -->
    </section>
<?php
if ($user->inGroup('industria')) {
	$data_new_alert['unidade'] = $unidade->id;
	$data_new_alert['medidor'] = $medidor;
	$this->load->view('modals/painel/new-alert', $data_new_alert);
	$this->load->view('modals/painel/modalInfoAlerta');
}
