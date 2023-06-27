
<!-- Modal Form -->
<div id="modalUnidade" class="modal-block modal-block-primary">
	<section class="card">
		<header class="card-header">
            <?php if ($origem == "demo" ): ?>
                <h2 class="card-title">Área 01</h2>
            <?php else: ?>
			    <h2 class="card-title"> Unidade <?php echo $unidade->apto; ?></h2>
            <?php endif; ?>
		</header>
        <div class="card-body">
        <table class="table table-sm table-bordered text-center">
            <tbody>
                <tr>
                    <td class="text-right" style="width: 150px;"><b>Referencia</b></td>
                    <td><?= $unidade->competencia; ?></td>
                    <td class="text-right" style="width: 150px;"><b>Fechamento</b></td>
                    <td><?= date('d/m/Y', strtotime($unidade->cadastro)); ?></td>
                </tr>
                <tr>
                    <td class="text-right" style="width: 150px;"><b>Data Inicial</b></td>
                    <td><?= date('d/m/Y', strtotime($unidade->inicio)); ?></td>
                    <td class="text-right" style="width: 150px;"><b>Data Final</b></td>
                    <td><?= date('d/m/Y', strtotime($unidade->fim)); ?></td>
                </tr>

            </tbody>
        </table>

            <?php  ?>
                <table class="table table-sm table-bordered text-center">
                    <thead>
                        <tr>
                            <th colspan="<?php echo count($details) + 1; ?>">CONSUMO DA UNIDADE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right" style="width: 150px;"><b>Medidor</b></td>
                            <?php foreach ($details as $u) { 
                                echo "<td>".$u->medidor."</td>";
                            } ?>
                        </tr>
                        <tr>
                            <td class="text-right"><b>Entrada</b></td>
                            <?php foreach ($details as $u) { 
                                echo "<td>".(($user->inGroup('demo') && $u->entrada == "Escada") ? "Cozinha" : (($user->inGroup('representante') && $u->entrada == "Escada") ? "Copa" : $u->entrada))."</td>";
                            } ?>
                        </tr>
                        <tr>
                            <td class="text-right"><b>Leitura Anterior</b></td>
                            <?php foreach ($details as $u) { 
                                echo "<td>".$u->leitura_anterior."</td>";
                            } ?>
                        </tr>
                        <tr>
                            <td class="text-right"><b>Leitura Atual</b></td>
                            <?php foreach ($details as $u) { 
                                echo "<td>".$u->leitura_atual."</td>";
                            } ?>
                        </tr>
                        <tr>
                            <td class="text-right"><b>Consumo</b></td>
                            <?php 
                            $total = 0;
                            foreach ($details as $u) { 
                                echo "<td>".number_format($u->consumo, 0, '', '.')." m³</td>";
                                $total += $u->consumo;
                            } ?>
                        </tr>
                    </tbody>
                </table>
            <?php ; ?>
            <table class="table table-sm text-center">
				<tbody>
				    <tr>
					    <td class="text-right" style="width: 150px;"><b>Consumo Total</b></td>
                        <td><?php echo number_format($unidade->consumo, 0, '', '.'); ?> m³</td>
					    <td class="text-right" style="width: 150px;"><b>Taxa indefinida</b></td>
                        <!-- <td><?php echo number_format($unidade->consumo, 0, '', '.'); ?> m³</td> -->
					</tr>
                </tbody>
            </table>
        </div>
        <footer class="card-footer">
            <div class="row">
                <div class="col-md-6">
                        <a href="<?php echo site_url($origem."/relatorios/".$fechamento_id.'/'.$unidade->id); ?>" class="btn btn-primary">Visualizar Relatório</a>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-default modal-dismiss">Fechar</button>
                </div>
            </div>
        </footer>
	</section>
</div>
