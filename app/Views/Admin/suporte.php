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
			<table class="table table-bordered table-striped table-nowrap" id="dt-suporte"
				data-url="<?php echo site_url('admin/get_chamados_novo'); ?>">
				<thead>
					<tr role="row">
						<th width="10%">Unidade</th>
						<th width="25%">Nome</th>
						<th width="10%">Email</th>
						<th width="15%">Mensagem</th>
						<th width="15%">Status</th>
						<th width="15%">Cadastro</th>
						<th width="15%">Departamento</th>
						<th width="15%">Agrupamento</th>
						<th width="15%">Classificação</th>
					</tr>
				</thead>

			</table>
		</div>
	</section>
	<!-- end: page -->
</section>