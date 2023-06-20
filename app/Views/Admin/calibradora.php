
				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Calibradora</h2>
						<div class="right-wrapper text-end">
							<ol class="breadcrumbs">
								<li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
								<li><span>Calibradora</span></li>
							</ol>
						</div>
					</header>
                    <!-- start: page -->
                    <section class="card">
                        <div class="card-body">
                            <div class="scrollable scrollable-terminal visible-slider" data-plugin-scrollable style="height: 100px;">
                                <div class="terminal scrollable-content">
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <section class="card">
                        <header class="card-header">
							<div class="card-actions buttons">
                                <div class="checkbox-custom checkbox-default float-start mt-2 mr-3">
                                    <input type="checkbox" id="auto" name="auto">
                                    <label for="auto">Continuar automaticamente</label>
                                </div>
                                <button class="btn btn-primary btn-iniciar" disabled>Iniciar Calibragem</button>
                                <button class="btn btn-primary btn-continuar collapse">Continuar</button>
                                <button class="btn btn-primary btn-salvar collapse">Salvar</button>
                                <button class="btn btn-warning btn-play ml-4 collapse">Pausar</button>
                            </div>
                            <h2 class="card-title">Calibração</h2>
                        </header>
                        <div class="card-body">
                            <table class="table table-responsive-md table-bordered table-sm table-hover dt-all-center" id="dt-sensores">
                                <thead class="center">
                                    <tr role="row">
                                        <th>Porta</th>
                                        <th>Serial</th>
                                        <th>F 1 A</th>
                                        <th>F 1 B</th>
                                        <th>F 1 C</th>
                                        <th>F 2 A</th>
                                        <th>F 2 B</th>
                                        <th>F 2 C</th>
                                        <th>F 3 A</th>
                                        <th>F 3 B</th>
                                        <th>F 3 C</th>
                                        <th>F 4 A</th>
                                        <th>F 4 B</th>
                                        <th>F 4 C</th>
                                        <th>F 5 A</th>
                                        <th>F 5 B</th>
                                        <th>F 5 C</th>
                                        <th>Fator</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
					</section>

                    <!-- end: page -->
                </section>

                <script>
    var processo = <?php echo isset($processo->processo) ? $processo->processo : 0; ?>;
    var data_processo = '<?= isset($processo->cadastro) ? $processo->cadastro : 0; ?>';
</script>    
                
                <?php
                    $data['modal_id'] = 'modalCalcular';
                    $data['modal_title'] = 'Processo Finalizado';
                    $data['modal_message'] = 'Deseja calcular o fator agora?';
                    $data['button'] = array('Sim', 'Não');
                    echo view('Admin/modals/confirm', $data);
                    ?>