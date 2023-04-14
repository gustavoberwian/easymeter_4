
                <section role="main" class="content-body">

                    <header class="page-header">
                        <h2>Bauducco CD Extrema/MG</h2>
					</header>
<!--
                    <section class="card card-easymeter">
                        <header class="card-header">
                            <div class="card-actions buttons">
                                <button class="btn btn-primary btn-download" data-id="<?= $id; ?>" data-loading-overlay><i class="fas fa-file-download mr-2"></i> Baixar Planilha</button>
                            </div>
                            <h2 class="card-title"><?= $report->tipo == 1 ? $competencia : (week_day(date_create_from_format('d/m/Y', $competencia)->format("N"), 0).", ".$competencia); ?></h2>
                        </header>

                        <div class="card-body">
                            <table class="table table-bordered table-striped mb-0" id="dt-report">
                                <thead>
                                <tr>
                                    <th width="40%">Medidor</th>
                                    <th width="10%">Tipo</th>
                                    <th width="15%">Leitura Inicial</th>
                                    <th width="15%">Leitura Final</th>
                                    <th width="20%">Consumo - L</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data as $r): ?>
                                        <tr>
                                            <td width="40%"><?= $r->nome; ?></th>
                                            <td width="10%" class="center"><?= $r->nome != "Cozinha (gás)" ? "<span class=\"badge badge-agua\">Água</span>" : "<span class=\"badge badge-gas\">Gás</span>"; ?></th>
                                            <td width="15%" class="center"><?= $r->leitura_anterior; ?></th>
                                            <td width="15%" class="center"><?= $r->leitura_atual; ?></th>
                                            <td width="20%" class="center"><?= number_format($r->consumo, 0, ",", "."); ?></th>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
-->