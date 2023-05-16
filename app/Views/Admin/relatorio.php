				<section role="main" class="content-body">
					<!-- start: page -->
					<section class="card">
						<div class="card-body">
							<div class="invoice report" id="print">
								<header class="clearfix">
									<div class="row align-items-end">
										<div class="col-sm-6 mt-3">
										<li><a href="<?php echo site_url('admin/relatorios'); ?>"><span>Entidades</span></a></li>
											<div class="ib">
												<?php if ($isTrc) : ?>
													<img src="<?php echo base_url('assets/img/trc1.png'); ?>" height="51" alt="TRC Easymeter" />
												<?php else : ?>
													<img src="<?php echo base_url('assets/img/logo.png'); ?>" width="220" height="51" alt="Easymeter" />
												<?php endif; ?>
											</div>
										</div>
										<div class="col-sm-6 text-right">
											<span class="h5 m-0 text-dark font-weight-bold">Relatório de Consumo de Água</span>
										</div>
									</div>
								</header>

								<table class="table invoice-items1">
									<tbody>
										<tr>
											<td width="50%" class="text-dark align-middle">
												<address class="mb-0">
													<h4 class="mt-0">CONDOMÍNIO <?php echo strtoupper($unidade_info->entity); ?></h4>
													<strong><?= is_null($unidade_info->nome) ? "Não Cadastrado" : $unidade_info->nome; ?></strong><br />
													<?php echo $unidade_info->logradouro; ?>, <?php echo $unidade_info->numero; ?><br />
													<?php echo $unidade_info->bairro; ?> - <?php echo $unidade_info->cidade; ?>/<?php echo $unidade_info->uf; ?><br />
													CEP <?php echo $unidade_info->cep; ?>
												</address>
											</td>
											<td width="35%" class="text-dark">
												<p class="text-1 text-muted mb-0">Consumo Comparado</p>
												<div class="progress light">
													<div class="progress-bar progress-bar-<?= format_pb_comparativo($consumo['voce'], array($consumo['voce'], $consumo['vizinhos'], $consumo['brasil'])); ?>" role="progressbar" aria-valuenow="<?php echo $consumo['voce']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $consumo['maximo']; ?>" style="width: <?php echo $consumo['voce'] / $consumo['maximo'] * 100; ?>%;">
														<div class="progress-bar-title">Você: <?php echo number_format($consumo['voce'], 0, '', '.'); ?> litros</div>
													</div>
												</div>
												<div class="progress light">
													<div class="progress-bar progress-bar-<?= format_pb_comparativo($consumo['vizinhos'], array($consumo['voce'], $consumo['vizinhos'], $consumo['brasil'])); ?>" role="progressbar" aria-valuenow="<?php echo $consumo['vizinhos']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $consumo['maximo']; ?>" style="width: <?php echo $consumo['vizinhos'] / $consumo['maximo'] * 100; ?>%;">
														<div class="progress-bar-title">Vizinhos: <?php echo number_format($consumo['vizinhos'], 0, '', '.'); ?> litros</div>
													</div>
												</div>
												<div class="progress light">
													<div class="progress-bar progress-bar-<?= format_pb_comparativo($consumo['brasil'], array($consumo['voce'], $consumo['vizinhos'], $consumo['brasil'])); ?>" role="progressbar" aria-valuenow="<?php echo $consumo['brasil']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $consumo['maximo']; ?>" style="width: <?php echo $consumo['brasil'] / $consumo['maximo'] * 100; ?>%;">
														<div class="progress-bar-title">Brasil: <?php echo number_format($consumo['brasil'], 0, '', '.'); ?> litros</div>
													</div>
												</div>
											</td>
											<td width="15%" class="text-dark ranking">

												<div class="text-4 font-weight-bold text-center"><?php echo $f_unidade->ranking; ?></div>
												<p class="text-1 text-muted mb-0">Ranking</p>
											</td>
											<!--
											<td width="15%" class="text-dark text-center">
                                                <p class="text-1 text-muted mb-0 text-left">QRCode</p>
                                                <?php echo QRcode::svg('https://www.easymeter.com.br/painel/visualizar/' . $f_unidade->uid, 'qrcode', false, QR_ECLEVEL_L, 90, false, 0); ?>
                                            </td>
                                            -->
										</tr>
									</tbody>
								</table>

								<table class="table invoice-items1">
									<tbody>
										<tr>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0">Unidade</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><?= (!is_null($unidade_info->agrupamento) ? ($unidade_info->agrupamento . "/") : '') . $unidade_info->apto; ?></div>
											</td>
											<td width="30%" class="text-dark">
												<p class="text-1 text-muted mb-0">Ciclo</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><?php echo date('d/m/Y', $fechamento->data_inicio) . ' a ' . date('d/m/Y', $fechamento->data_fim); ?></div>
											</td>
											<td width="10%" class="text-dark">
												<p class="text-1 text-muted mb-0">Dias</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><?php echo $fechamento->dias; ?></div>
											</td>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0">Fechamento</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><?php echo date('d/m/Y', strtotime($fechamento->cadastro)); ?></div>
											</td>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0">Referência</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><?php echo $fechamento->competencia; ?></div>
											</td>
										</tr>
									</tbody>
								</table>

								<table class="table invoice-items1 bg-gray">
									<tbody>
										<tr>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0">Valor Consumo</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><small>R$</small> <?php echo number_format($f_unidade->v_consumo, 2, ',', '.'); ?></div>
											</td>
											<td width="5" class="separator"></td>
											<td width="5" class="separator"></td>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0">Área Comum</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><small>R$</small> <?php echo number_format($f_unidade->v_acomum, 2, ',', '.'); ?></div>
											</td>
											<td width="5" class="separator"></td>
											<td width="5" class="separator"></td>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0"><?= $unidade_info->t_basico; ?> <?= ($f_unidade->v_taxas > 0) ? '+ Taxas' : ''; ?></p>
												<div class="text-4 font-weight-bold mb-0 text-center"><small>R$</small> <?php echo number_format($f_unidade->v_basico + $f_unidade->v_taxas, 2, ',', '.'); ?></div>
											</td>
											<td width="5" class="separator"></td>
											<td width="5" class="separator"></td>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0">Gestão</p>
												<div class="text-4 font-weight-bold mb-0 text-center"><small>R$</small> <?php echo number_format($f_unidade->v_gestao, 2, ',', '.'); ?></div>
											</td>
											<td width="5" class="separator"></td>
											<td width="5" class="separator"></td>
											<td width="20%" class="text-dark">
												<p class="text-1 text-muted mb-0">Total</p>
												<div class="text-5 font-weight-bold mb-0 text-center"><small>R$</small> <?php echo number_format($f_unidade->v_total, 2, ',', '.'); ?></div>
											</td>
										</tr>
										<tr></tr>
										<tr>
											<td colspan="13" class="separator">&nbsp;</td>
										</tr>
										<tr></tr>
										<tr>
											<td colspan="4" class="text-center font-weight-semibold text-muted">CONSUMO DO CONDOMÍNIO</td>
											<td colspan="2" class="separator"></td>
											<td colspan="<?= count($details) + 6; ?>" width="51%" class="text-center font-weight-semibold text-muted">CONSUMO DA UNIDADE</td>
										</tr>
										<tr>
											<td colspan="2" class="text-muted">Consumo</td>
											<td colspan="2" class="bg-w text-center text-dark font-weight-semibold"><?php echo number_format($fechamento->leitura_atual - $fechamento->leitura_anterior, 0, '', '.'); ?> m<sup>3</sup></td>
											<td colspan="2" class="separator"></td>
											<td colspan="2" class="text-muted">Medidor</td>
											<?php foreach ($details as $u) { ?>
												<td colspan="<?= 6 - count($details); ?>" class="bg-w text-center text-dark font-weight-semibold"><?php echo $u->medidor; ?></td>
											<?php } ?>
										</tr>
										<tr>
											<td colspan="2" class="text-muted">Valor Geral</td>
											<td colspan="2" class="bg-w text-center text-dark font-weight-semibold"><small>R$</small> <?php echo number_format($fechamento->v_concessionaria, 2, ',', '.'); ?></td>
											<td colspan="2" class="separator"></td>
											<td colspan="2" class="text-muted">Entrada</td>
											<?php foreach ($details as $u) { ?>
												<td colspan="<?= 6 - count($details); ?>" class="bg-w text-center text-dark font-weight-semibold"><?php echo $u->entrada; ?></td>
											<?php } ?>
										</tr>
										<tr>
											<td colspan="2" class="text-muted">Valor m<sup>3</sup></td>
											<td colspan="2" class="bg-w text-center text-dark font-weight-semibold"><small>R$</small> <?php echo number_format($fechamento->v_litro * 1000, 2, ',', '.'); ?></td>
											<td colspan="2" class="separator"></td>
											<td colspan="2" class="text-muted">Leitura Anterior</td>
											<?php foreach ($details as $u) { ?>
												<td colspan="<?= 6 - count($details); ?>" class="bg-w text-center text-dark font-weight-semibold"><?php echo $u->leitura_anterior; ?></td>
											<?php } ?>
										</tr>
										<tr>
											<td colspan="2" class="text-muted">Valor do Litro</td>
											<td colspan="2" class="bg-w text-center text-dark font-weight-semibold"><small>R$</small> <?php echo number_format($fechamento->v_litro, 5, ',', '.'); ?></td>
											<td colspan="2" class="separator"></td>
											<td colspan="2" class="text-muted">Leitura Atual</td>
											<?php foreach ($details as $u) { ?>
												<td colspan="<?= 6 - count($details); ?>" class="bg-w text-center text-dark font-weight-semibold"><?php echo $u->leitura_atual; ?></td>
											<?php } ?>
										</tr>

										<tr>
											<td colspan="4" class="separator"></td>
											<td colspan="2" class="separator"></td>
											<td colspan="2" class="text-muted">Consumo</td>
											<?php foreach ($details as $u) { ?>
												<td colspan="<?= 6 - count($details); ?>" class="bg-w text-center text-dark font-weight-semibold"><?php echo number_format($u->consumo, 0, '', '.'); ?> L/ <?php echo number_format($u->consumo / 1000, 1, ',', ''); ?>m³</td>
											<?php } ?>
										</tr>
										<tr>
											<td colspan="2" class="text-muted">Área Comum</td>
											<td colspan="2" class="bg-w text-center text-dark font-weight-semibold"><?php echo number_format($fechamento->acomum, 0, '', '.'); ?> m<sup>3</sup></td>
											<td colspan="2" class="separator"></td>
											<td colspan="2" class="text-muted">Consumo Total</td>
											<td colspan="5" class="bg-w text-center text-dark font-weight-semibold"><?= number_format($f_unidade->consumo, 0, '', '.'); ?> L/ <?php echo number_format($f_unidade->consumo / 1000, 1, ',', ''); ?>m³</td>
										</tr>
										<tr>
											<td colspan="2" class="text-muted">Valor Rateado</td>
											<td colspan="2" class="bg-w text-center text-dark font-weight-semibold"><small>R$</small> <?php echo number_format($fechamento->v_acomum, 2, ',', '.'); ?></td>
											<td colspan="2" class="separator"></td>
											<td colspan="2" class="text-muted">Valor</td>
											<?php foreach ($details as $u) { ?>
												<td colspan="<?= 6 - count($details); ?>" class="bg-w text-center text-dark font-weight-semibold"><small>R$</small> <?php echo number_format($u->consumo * $f_unidade->v_litro, 2, ',', '.'); ?></td>
											<?php } ?>
										</tr>
										<tr></tr>
										<tr>
											<td colspan="13" class="separator">&nbsp;</td>
										</tr>
										<?php /* ?>
										<tr>
											<td colspan="13" class="text-center font-weight-semibold text-muted">CONSUMO DA UNIDADE <?php echo (count($fechamentos) < 4) ? 'NO PERÍODO' : 'NOS ÚLTIMOS '.count($fechamentos).' MESES'; ?></td>
										</tr>

                                        <tr class="chart">
											<td colspan="13" class="bg-w">
                                                <canvas id="bar-chart" height="500"></canvas>
											</td>
										</tr>

                                        <tr>
											<td colspan="13" class="bg-w">
                                                <img class="d-none" id="img-chart" />
											</td>
										</tr>

                                        <tr>
											<td colspan="13" class="separator">&nbsp;</td>
										</tr>
<?php */ ?>
										<tr></tr>
										<tr>
											<!--											<td colspan="4"class="text-center font-weight-semibold text-muted">META DE ECONOMIA</td>
                                            <td colspan="2" class="separator"></td>
-->
											<td colspan="13" class="text-center font-weight-semibold text-muted">

												<?php if ($isTrc) : ?>
													A ECONOMIA DE ÁGUA DESTA UNIDADE ESTE MÊS EQUIVALE A
												<?php else : ?>
													O CONSUMO DA UNIDADE ESTE MÊS EQUIVALE A
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<!--                                            
											<td colspan="2" class="bg-w">
												<p class="text-1 text-muted mb-0">Valor</p>
                                                <div class="text-dark font-weight-bold mb-0 text-center"><small>R$</small> <?php echo number_format($f_unidade->v_total, 2, ',', '.'); ?></div>
											</td>
											<td colspan="2" rowspan="3" class="text-center align-middle bg-w">
											<div class="liquid-meter-wrapper liquid-meter-sm1">
												<div class="liquid-meter">
													<meter class="p-3" min="0" max="100" value="<?php echo 100 + number_format($meta, 0); ?>" id="meter"></meter>
												</div>
											</div>
											</td>
                                            <td colspan="2" class="separator"></td>
-->
											<td colspan="13" class="text-dark text-center font-weight-semibold align-middle bg-w">
												<div class="row">
													<?php if ($equivalencia[0]) : ?>
														<div class="col">
															<img class="comp-img-fluid" src="<?= base_url('assets/img/c0_' . $equivalencia[0]); ?>.png">
															<p class="text-1 text-muted my-0"><?= $equivalencia[0]; ?> <?= ($equivalencia[0] == 1) ? 'CAMINHÃO' : 'CAMINHÕES'; ?> DE 10.000 L</p>
														</div>
													<?php endif; ?>
													<?php if ($equivalencia[1]) : ?>
														<div class="col">
															<img class="comp-img-fluid" src="<?= base_url('assets/img/c1_' . $equivalencia[1]); ?>.png">
															<p class="text-1 text-muted my-0"><?= $equivalencia[1]; ?> PISCINAS DE 1.000 L</p>
														</div>
													<?php endif; ?>
													<?php if ($equivalencia[2]) : ?>
														<div class="col">
															<img class="comp-img-fluid" src="<?= base_url('assets/img/c2_' . $equivalencia[2]); ?>.png">
															<p class="text-1 text-muted my-0"><?= $equivalencia[2]; ?> BANHEIRAS DE 100 L</p>
														</div>
													<?php endif; ?>
													<?php if ($equivalencia[3]) : ?>
														<div class="col">
															<img class="comp-img-fluid" src="<?= base_url('assets/img/c3_' . $equivalencia[3]); ?>.png">
															<p class="text-1 text-muted my-0"><?= $equivalencia[3]; ?> <?= ($equivalencia[3] == 1) ? 'GALÃO' : 'GALÕES'; ?> DE 10 L</p>
														</div>
													<?php endif; ?>
												</div>
											</td>
										</tr>
										<!--                                        
										<tr>
											<td colspan="2" class="bg-w">
												<p class="text-1 text-muted mb-0">Meta</p>
                                                <div class="text-dark font-weight-bold mb-0 text-center"><small>R$</small> <?php echo number_format($f_unidade->meta, 2, ',', '.'); ?> </div>
											</td>
										</tr>
										<tr>
											<td colspan="2" class="bg-w">
												<p class="text-1 text-muted mb-0">Diferença</p>
                                                <div class="text-<?= ($meta <= 0) ? 'success' : 'danger'; ?> text-4 font-weight-bold mb-0 text-center"><?= (($meta > 0) ? '+' : '') . number_format($meta, 0, ',', ''); ?>%</div>
											</td>
                                        </tr>
-->
									</tbody>
								</table>
							</div>

							<div class="text-right mr-4">
								<a href="#" onclick="print();" class="btn btn-primary ml-3"><i class="fas fa-print"></i> Imprimir</a>
							</div>
						</div>
					</section>
					<!-- end: page -->
				</section>
				<script>
					function print() {
						$('#img-chart').css('padding-right', '20px');
						$('#print').print({
							deferred: $.Deferred().done(function() {
								$('#img-chart').css('padding-right', '0px');
							})
						});
					}
				</script>