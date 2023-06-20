<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Relatórios Viver Canoas</h2>
						<div class="right-wrapper text-right">
                            <ol class="breadcrumbs">
								<li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                                <li><span>Relatórios</span></li>
								<li><span>Viver Canoas</span></li>
							</ol>
						</div>
					</header>
					<!-- start: page -->
					<div class="form-group text-lg-right row">
                        <div class="col-lg-12">
                            <label class="control-label pt-2 pr-2">Competência:</label>
                            <div class="btn-group">
                                <select class="form-control competencia">
                                    <?php if ($competencias) : ?>
                                        <?php foreach ($competencias as $c) { ?>
                                            <option value="<?php echo $c->competencia; ?>" data-comp="<?php echo $c->id; ?>"><?= competencia_nice($c->competencia); ?></option>
                                        <?php } ?>
                                    <?php endif; ?>
                                </select>
                                <div class="btn-group-append">
                                    <button class="btn btn-primary ml-2 btn-download" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row pt-0">
                        <div class="col-lg-4">
                            <section class="card">
                                <header class="card-header">
                                    <h2 class="card-title">Água x Gás</h2>
                                    <p class="card-subtitle">Unidades com consumo de água, porém sem consumo de gás ou sem medidor</p>
                                </header>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped table-hover table-clickable" id="dt-agua-gas-viver" data-url="<?php echo site_url('ajax/v_ag'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="25%">Bloco</th>
                                                <th width="25%">Unidade</th>
                                                <th width="25%">Água</th>
                                                <th width="25%">Gás</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-lg-4">
                            <section class="card h-100">
                                <header class="card-header">
                                    <h2 class="card-title">Gás x Água</h2>
                                    <p class="card-subtitle">Unidades com consumo de gás, porém com consumo de água menor que 100L</p>
                                </header>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped table-hover table-clickable" id="dt-gas-agua-viver" data-url="<?php echo site_url('ajax/v_ga'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="25%">Bloco</th>
                                                <th width="25%">Unidade</th>
                                                <th width="25%">Água</th>
                                                <th width="25%">Gás</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-lg-4">
                            <section class="card">
                                <header class="card-header">
                                    <h2 class="card-title">Menos de 100L</h2>
                                    <p class="card-subtitle">Unidades com consumo de água inferior a 100L</p>
                                </header>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped table-hover table-clickable" id="dt-agua-inf-viver" data-url="<?php echo site_url('ajax/v_ai'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="33%">Bloco</th>
                                                <th width="33%">Unidade</th>
                                                <th width="33%">Água</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <section class="card">
                                <header class="card-header">
                                    <h2 class="card-title">Sem Consumo de Água</h2>
                                    <p class="card-subtitle">Unidades com consumo de água zerado</p>
                                </header>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped table-hover table-clickable" id="dt-agua-z-viver" data-url="<?php echo site_url('ajax/v_az'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="33%">Bloco</th>
                                                <th width="33%">Unidade</th>
                                                <th width="33%">Água</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-lg-4 vazamento">
                            <section class="card">
                                <header class="card-header">
                                    <h2 class="card-title">Vazamento de Água</h2>
                                    <p class="card-subtitle">Unidades com vazamento de água há pelo menos 24 horas</p>
                                </header>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped table-hover table-clickable" id="dt-agua-v-viver" data-url="<?php echo site_url('ajax/v_av'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="20%">Bloco</th>
                                                <th width="20%">Unidade</th>
                                                <th width="20%">Horas</th>
                                                <th width="20%">Consumo</th>
                                                <th width="20%">Entrada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="col-lg-4">
                            <section class="card">
                                <header class="card-header">
                                    <h2 class="card-title">Vazamento de Gás</h2>
                                    <p class="card-subtitle">Unidades com consumo inferior ao necessário para 1 hora uso do fogão </p>
                                </header>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped table-hover table-clickable" id="dt-gas-v-viver" data-url="<?php echo site_url('ajax/v_gv'); ?>">
                                        <thead>
                                            <tr role="row">
                                                <th width="33%">Bloco</th>
                                                <th width="33%">Unidade</th>
                                                <th width="33%">Consumo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
					<!-- end: page -->
				</section>