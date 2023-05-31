				<section role="main" class="content-body">
                    <!-- start: page -->
					<div class="inner-toolbar clearfix">
						<ul>
							<li class="right">
								<ul class="nav nav-pills nav-pills-primary">
									<li class="nav-item">
										<label>Filtro</label>
									</li>
									<li class="nav-item">
										<a class="nav-link active" href="#access-log" data-toggle="tab">Access Log</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#error-log" data-toggle="tab">Error Log</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#custom-log" data-toggle="tab">Custom Log</a>
									</li>
								</ul>
							</li>
						</ul>
                    </div>
					<section class="card">
						<div class="card-body tab-content">
							<div id="access-log" class="tab-pane active">
                                <table class="table" id="dt-log" data-url="<?php echo site_url('admin/get_log'); ?>">
                                    <thead>
                                        <tr role="row">
                                            <th class="d-none"></th>
                                            <th width="5%">Tipo</th>
                                            <th width="5%">Por</th>
                                            <th width="70%">Mensagem</th>
                                            <th width="15%">Data</th>
                                            <th width="5%" class="d-none d-lg-table-cell">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
					<section class="card">
                        <header class="card-header">
                            <h2 class="card-title">Acessos</h2>
                        </header>
						<div class="card-body">
                            <table class="table" id="dt-access" data-url="<?php echo site_url('admin/get_access'); ?>">
                                <thead>
                                    <tr role="row">
                                        <th width="35%">Usuário</th>
                                        <th width="15%">Data</th>
                                        <th width="35%">Entidade</th>
                                        <th width="10%">Unidade</th>
                                        <th width="5%">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </section>
					<!-- end: page -->
				</section>