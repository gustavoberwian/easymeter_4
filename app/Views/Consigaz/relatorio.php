<section role="main" class="content-body" data-entidade="<?= $relatorio->entidade_id ?>" data-ramal="<?= $relatorio->ramal_id ?>" data-fechamento="<?= $fechamento->id ?>">
    <!-- start: page -->
    <section class="card" id="page-header">

        <div class="card-body">

            <div class="report p-4" id="print">

                <table class="text-dark w-100">
                    <tr>
                        <td colspan="2" class="text-center"><span class="mt-3 h5 font-weight-bold text-uppercase">Relatório de Consumo de Gás</span>
                    </tr>
                </table>
                <table class="relatorio w-100 mt-3 table-bordered">
									<tbody>
										<tr>
											<td width="35%" class="text-dark align-middle p-2">
												<address class="mb-0">
													<h4 class="mt-0">CONDOMÍNIO <?php echo strtoupper($unidade_info->condo); ?></h4>
													<strong><?= is_null($unidade_info->username) ? "Não Cadastrado" : $unidade_info->username; ?></strong><br/>
													<?php echo $unidade_info->logradouro; ?>, <?php echo $unidade_info->numero; ?><br/>
													<?php echo $unidade_info->bairro; ?> - <?php echo $unidade_info->cidade; ?>/<?php echo $unidade_info->uf; ?><br/>
													CEP <?php echo $unidade_info->cep; ?>
												</address>
                                            </td>
											<td width="35%" class="text-dark p-2">
                                                <p class="text-1 text-muted mb-0">Consumo Comparado</p>
                                                <div class="progress-bar-title">Você: <?php echo number_format($relatorio->consumo, 0, '', '.'); ?> m³</div>
												<div class="progress light mb-2">
													<div class="progress-bar progress-bar-<?= format_pb_comparativo($relatorio->consumo, array($relatorio->consumo, $relatorio->media)); ?>" role="progressbar" aria-valuenow="<?php echo $relatorio->consumo; ?>" aria-valuemin="0" aria-valuemax="<?php echo $relatorio->soma; ?>" style="width: <?php echo $relatorio->consumo / $relatorio->soma * 100 ; ?>%;"></div>		
												</div>
                                                <div class="progress-bar-title">Vizinhos: <?php echo number_format($relatorio->media, 0, '', '.'); ?> m³</div>
												<div class="progress light mb-2">
													<div class="progress-bar progress-bar-<?= format_pb_comparativo($relatorio->media, array($relatorio->consumo, $relatorio->media)); ?>" role="progressbar" aria-valuenow="<?php echo $relatorio->media; ?>" aria-valuemin="0" aria-valuemax="<?php echo $relatorio->soma; ?>" style="width: <?php echo $relatorio->media / $relatorio->soma * 100 ; ?>%;"></div>
												</div>                                
											</td>
										</tr>
									</tbody>
								</table>
                <table class="relatorio w-100 mt-3 table-bordered">
                    <tbody>
                    <tr>
                        <?php if (!intval($unidade_info->bloco)): ?>
                        <td width="20%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Apto</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= $unidade_info->apto ?></div>
                        </td>
                        <?php else: ?>
                        <td width="20%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Bloco/Apto</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= $unidade_info->bloco . '/' . $unidade_info->apto ?></div>
                        </td>
                        <?php endif; ?>

                        <td width="20%" class="text-dark d-print-none">
                            <p class="text-1 text-muted mb-0">Tipo</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= ucfirst(str_replace("_", " ", $relatorio->tipo)); ?></div>
                        </td>

                        <td width="20%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Ciclo</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo date('d/m/Y', strtotime($fechamento->inicio)).' a '.date('d/m/Y', strtotime($fechamento->fim)); ?></div>
                        </td>
                        <td width="10%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Dias</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo round((strtotime($fechamento->fim) - strtotime($fechamento->inicio)) / 86400) + 1; ?></div>
                        </td>
                        <td width="10%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Fechamento</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo date('d/m/Y', strtotime($fechamento->cadastro)); ?></div>
                        </td>
                        <td width="10%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Competência</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo strftime('%b/%Y', strtotime($fechamento->competencia)); ?></div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="relatorio bg-gray w-50 mt-3 table-bordered">
                    <tbody>
                    <tr>
                        <td width="33%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Leitura Anterior</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= str_pad(round($relatorio->leitura_anterior), 6 , '0' , STR_PAD_LEFT); ?></div>
                        </td>

                        <td width="33%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Leitura Atual</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= str_pad(round($relatorio->leitura_atual), 6 , '0' , STR_PAD_LEFT); ?></div>
                        </td>

                        <td width="33%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Consumo</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= number_format($relatorio->leitura_atual - $relatorio->leitura_anterior, 0, ",", ".")." m³"; ?></div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="mt-3">

                    <table class="relatorio comum w-100 mb-3 table-bordered">
                        <tbody>
                        <tr>
                            <td colspan="2" class="bg-gray">
                                <p class="text-muted font-weight-bold text-uppercase text-center mb-0">Consumo</p>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%" class="bg-gray">
                                <p class="text-muted font-weight-bold mb-0">Consumo m³</p>
                            </td>
                            <td width="50%">
                                <div class="text-4 text-dark font-weight-bold mb-0 text-center"><?= number_format($relatorio->leitura_atual - $relatorio->leitura_anterior, 0, ",", ".")." m³"; ?></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3" style="page-break-inside: avoid;">
                    <table class="relatorio comum w-100 table-bordered">
                        <tbody>
                        <tr>
                            <td class="bg-gray">
                                <p class="text-muted font-weight-bold text-uppercase text-center mb-0">Mensagem</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="height:100px;">
                                <?= $fechamento->mensagem; ?>
                            </td>
                        </tr>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <table class="text-dark w-100">
                        <tr>
                            <td class="text-end">
                                <img src="<?php echo base_url('assets/img/logo.png'); ?>" height="30" alt="Easymeter"/>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
</section>
