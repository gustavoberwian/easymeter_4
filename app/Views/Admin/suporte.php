<section role="main" class="content-body">
<header class="page-header">
		<h2>Chamados</h2>
		<div class="right-wrapper text-end">
			<ol class="breadcrumbs">
				<li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
				<li><span>Chamados</span></li>
			</ol>
		</div>
	</header>
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
						<th width="15%">Nome</th>
						<th width="5%">Status</th>
						<th width="30%">Mensagem</th>
						<th width="10%">Email</th>
						<th width="5%">Cadastro</th>
						<th width="5%">Departamento</th>
					</tr>
				</thead>

			</table>
		</div>
	</section>
	<!-- end: page -->
</section>