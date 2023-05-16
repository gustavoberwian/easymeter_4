<section role="main" class="content-body" data-entidade="<?= $entidade->id ?>" data-ramal="<?= $ramal->id ?>" data-fechamento="<?= $relatorio->id ?>">
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
                        <td width="20%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Unidade</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= $unidade->nome ?></div>
                        </td>

                        <td width="20%" class="text-dark d-print-none">
                            <p class="text-1 text-muted mb-0">Tipo</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= ucfirst(str_replace("_", " ", $unidade->tipo)); ?></div>
                        </td>

                        <td width="10%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Medidor</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= $unidade->medidor ?></div>
                        </td>

                        <td width="20%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Ciclo</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo date('d/m/Y', $fechamento->inicio).' a '.date('d/m/Y', $fechamento->fim); ?></div>
                        </td>
                        <td width="10%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Dias</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo round(($fechamento->fim - $fechamento->inicio) / 86400) + 1; ?></div>
                        </td>
                        <td width="10%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Fechamento</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo date('d/m/Y', strtotime($fechamento->cadastro)); ?></div>
                        </td>
                        <td width="10%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Competência</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?php echo competencia_nice($fechamento->competencia); ?></div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="relatorio bg-gray w-50 mt-3 table-bordered">
                    <tbody>
                    <tr>
                        <td width="33%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Leitura Anterior</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= str_pad(round($fechamento->leitura_anterior), 6 , '0' , STR_PAD_LEFT); ?></div>
                        </td>

                        <td width="33%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Leitura Atual</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= str_pad(round($fechamento->leitura_atual), 6 , '0' , STR_PAD_LEFT); ?></div>
                        </td>

                        <td width="33%" class="text-dark">
                            <p class="text-1 text-muted mb-0">Consumo</p>
                            <div class="text-4 font-weight-bold mb-0 text-center"><?= number_format($fechamento->leitura_atual - $fechamento->leitura_anterior, 0, ",", ".")." m³"; ?></div>
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
                                <div class="text-4 text-dark font-weight-bold mb-0 text-center"><?= number_format($fechamento->leitura_atual - $fechamento->leitura_anterior, 0, ",", ".")." m³"; ?></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <table class="relatorio comum w-100 table-bordered">
                        <tbody>
                        <tr>
                            <td class="bg-gray">
                                <p class="text-muted font-weight-bold text-uppercase text-center mb-0">Histórico</p>
                            </td>
                        </tr>
                        <tr>
                            <?php if ($historico) { ?>

                                <td class="history text-center" style="height:100px; vertical-align: top;">

                                    <table class="no-border p-2 w-50 float-start">
                                        <tr>
                                            <td class="font-weight-bold">Competência</td>
                                            <td class="font-weight-bold">Leitura Anterior</td>
                                            <td class="font-weight-bold">Leitura Atual</td>
                                            <td class="font-weight-bold">Consumo</td>
                                        </tr>
                                        <?php for ($i = 0; $i < 6, $i < count($historico); $i++) { ?>
                                            <tr>
                                                <td><?php echo competencia_nice($historico[$i]["competencia"]); ?></td>
                                                <td><?= str_pad(round($historico[$i]["leitura_anterior"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                <td><?= str_pad(round($historico[$i]["leitura_atual"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                <td><?= number_format($historico[$i]["consumo"], 0, ",", ".")." m³"; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>

                                    <?php if (count($historico) > 5) { ?>
                                        <table class="no-border p-2 w-50 float-start">
                                            <tr>
                                                <td class="font-weight-bold">Competência</td>
                                                <td class="font-weight-bold">Leitura Anterior</td>
                                                <td class="font-weight-bold">Leitura Atual</td>
                                                <td class="font-weight-bold">Consumo</td>
                                            </tr>
                                            <?php for ($i = 6; $i < 12, $i < count($historico); $i++) { ?>
                                                <tr>
                                                    <td><?php echo competencia_nice($historico[$i]["competencia"]); ?></td>
                                                    <td><?= str_pad(round($historico[$i]["leitura_anterior"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                    <td><?= str_pad(round($historico[$i]["leitura_atual"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                    <td><?= number_format($historico[$i]["consumo"], 0, ",", ".")." m³"; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    <?php } ?>
                                </td>

                            <?php } else { ?>

                                <td class="text-center" style="height:100px;">
                                    Nenhum Histórico Disponível
                                </td>
                            <?php } ?>
                        </tr>
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
