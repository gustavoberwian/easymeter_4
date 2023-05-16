
				<section role="main" class="content-body">
					<!-- start: page -->
					<section class="card">
						<header class="card-header">
							<div class="card-actions buttons">
								<button class="btn btn-primary btn-incluir"><i class="fa fa-plus"></i> Novo Chamado</button>
							</div>
							<h2 class="card-title">Chamados</h2>
						</header>
						<div class="card-body">
							<table class="table table-bordered table-striped table-nowrap table-hover" id="dt-tickets" data-url="<?php echo site_url('ajax/chamados'); ?>">
								<thead>
									<tr role="row">
										<th width="7%">id</th>
										<th width="8%">Status</th>
                                        <th width="15%">Nome</th>
                                        <th width="6%">Cond.</th>
                                        <th width="6%">Unid.</th>
										<th width="34%">Mensagem</th>
										<th width="6%">Dpto</th>
										<th width="8%">Criação</th>
										<th width="8%">Ú.Movim.</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</section>
					<!-- end: page -->
				</section>