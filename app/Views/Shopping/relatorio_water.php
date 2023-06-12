<section role="main" class="content-body">
    <!-- start: page -->
    <section class="card" id="page-header" data-url="<?= $url ?>">

        <div class="card-body">
        
            <div class="report p-4" id="print">

                <table class="text-dark w-100">
                    <tr>
                        <td>
                            <h4 class="font-weight-bold mt-0"><?= $shopping->nome; ?></h4>
                            <?php echo $shopping->logradouro; ?>, <?php echo $shopping->numero; ?><br />
                            <?php echo $shopping->bairro; ?><br />
                            <?php echo $shopping->cidade; ?>/<?php echo $shopping->uf; ?><br />
                            CEP <?php echo $shopping->cep; ?>
                        </td>
                        <td class="text-end" style="vertical-align: text-top;">
                            <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" height="50" alt="<?= ""; ?>" style="margin-top: 17px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center"><span class="mt-3 h5 font-weight-bold text-uppercase">Relatório de Consumo de Água</span>
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
                                <div class="text-4 font-weight-bold mb-0 text-center"><?= $unidade->device ?></div>
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
                                <div class="text-4 font-weight-bold mb-0 text-center"><?= str_pad(round($unidade->leitura_anterior), 6 , '0' , STR_PAD_LEFT); ?></div>
                            </td>

                            <td width="33%" class="text-dark">
                                <p class="text-1 text-muted mb-0">Leitura Atual</p>
                                <div class="text-4 font-weight-bold mb-0 text-center"><?= str_pad(round($unidade->leitura_atual), 6 , '0' , STR_PAD_LEFT); ?></div>
                            </td>

                            <td width="33%" class="text-dark">
                                <p class="text-1 text-muted mb-0">Consumo</p>
                                <div class="text-4 font-weight-bold mb-0 text-center"><?= number_format($unidade->consumo, 0, ",", ".")." L"; ?></div>
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
                                    <p class="text-muted font-weight-bold mb-0">Consumo Shopping Aberto</p>
                                </td>
                                <td width="50%">
                                    <div class="text-4 text-dark font-weight-bold mb-0 text-center"><?= number_format($unidade->consumo_o, 0, ",", ".")." L"; ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" class="bg-gray">
                                    <p class="text-muted font-weight-bold mb-0">Consumo Shopping Fechado</p>
                                </td>
                                <td width="50%">
                                    <div class="text-4 text-dark font-weight-bold mb-0 text-center"><?= number_format($unidade->consumo_c, 0, ",", ".")." L"; ?></div>
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
                                                    <td><?php echo strftime('%b/%Y', strtotime($historico[$i]["competencia"])); ?></td>
                                                    <td><?= str_pad(round($historico[$i]["leitura_anterior"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                    <td><?= str_pad(round($historico[$i]["leitura_atual"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                    <td><?= number_format($historico[$i]["consumo"], 0, ",", ".")." L"; ?></td>
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
                                                        <td><?php echo strftime('%b/%Y', strtotime($historico[$i]["competencia"])); ?></td>
                                                        <td><?= str_pad(round($historico[$i]["leitura_anterior"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                        <td><?= str_pad(round($historico[$i]["leitura_atual"]), 6 , '0' , STR_PAD_LEFT); ?></td>
                                                        <td><?= number_format($historico[$i]["consumo"], 0, ",", ".")." L"; ?></td>
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

                <?php if (array_sum($equivalencia)) : ?>
                    <div class="mt-3" style="page-break-inside: avoid;">
                        <table class="relatorio comum w-100 table-bordered">
                            <tbody>
                                <tr>
                                    <td class="bg-gray">
                                        <p class="text-muted font-weight-bold text-uppercase text-center mb-0">O CONSUMO DA UNIDADE ESTE MÊS EQUIVALE A</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-dark text-center font-weight-semibold align-middle bg-w">
                                        <div class="row">
                                            <?php if ($equivalencia[0]) : ?>
                                                <div class="col">
                                                    <?php if ($equivalencia[0] > 12) : ?>
                                                        <img class="comp-img-fluid" src="<?= base_url( 'assets/img/c0_12'); ?>.png">
                                                    <?php else : ?>
                                                        <img class="comp-img-fluid" src="<?= base_url( 'assets/img/c0_'.$equivalencia[0]); ?>.png">
                                                    <?php endif; ?>
                                                    <p class="text-1 text-muted my-0"><?= $equivalencia[0]; ?> <?= ($equivalencia[0] == 1) ? 'CAMINHÃO' : 'CAMINHÕES'; ?> DE 10.000 L</p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($equivalencia[1]) : ?>
                                                <div class="col">
                                                    <img class="comp-img-fluid" src="<?= base_url( 'assets/img/c1_'.$equivalencia[1]); ?>.png">
                                                    <p class="text-1 text-muted my-0"><?= $equivalencia[1]; ?> PISCINAS DE 1.000 L</p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($equivalencia[2]) : ?>
                                                <div class="col">
                                                    <img class="comp-img-fluid" src="<?= base_url( 'assets/img/c2_'.$equivalencia[2]); ?>.png">
                                                    <p class="text-1 text-muted my-0"><?= $equivalencia[2]; ?> BANHEIRAS DE 100 L</p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($equivalencia[3]) : ?>
                                                <div class="col">
                                                    <img class="comp-img-fluid" src="<?= base_url( 'assets/img/c3_'.$equivalencia[3]); ?>.png">
                                                    <p class="text-1 text-muted my-0"><?= $equivalencia[3]; ?> <?= ($equivalencia[3] == 1) ? 'GALÃO' : 'GALÕES'; ?> DE 10 L</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

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
