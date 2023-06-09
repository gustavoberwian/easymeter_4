
				<section role="main" class="content-body">
				<header class="page-header">
        <h2>Entidades</h2>
        <div class="right-wrapper text-end">
            <ol class="breadcrumbs pe-4">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><span>Entidades</span></li>
            </ol>
        </div>
    </header>
					<!-- start: page -->
					<section class="card">
						<header class="card-header">
							<div class="card-actions buttons">
								<div class="text-end buttons-alerts alerts">
									<span class="text-end vis-group text-3 text-muted d-none d-lg-inline">Filtrar por:</span>
									<div class="text-end btn-group btn-group-toggle ml-3 vis-group box-group" data-toggle="buttons">
										<label class=" filt btn btn-primary read active" data-filter="">
											<input type="radio" style="display:none;" name="read" id="option7" autocomplete="off" checked> Todos
										</label>
										<label class="filt btn btn-primary read" data-filter="active">
											<input type="radio" name="read" style="display:none;" id="option8" autocomplete="off"> Ativos
										</label>
										<label class=" filt btn btn-primary read" data-filter="ended">
											<input type="radio" name="read" style="display:none;" id="option9" autocomplete="off"> Finalizados
										</label>
									</div>
								</div>
							</div>
							<h2 class="card-title">Avisos</h2>
						</header>
						<div class="card-body">
							<table class="table table-hover" id="dt-alertas" style="cursor: auto;" data-url="<?php echo site_url('admin/get_alertas'); ?>">
								<thead>
									<tr role="row">
                                        <th width="0%" class="d-none"></th>
                                        <th width="0%" class="d-none"></th>
                                        <th width="0%" class="d-none"></th>
										<th width="6%">Tipo</th>
                                        <th width="25%">Titulo</th>
										<th width="44%" class="d-none d-lg-table-cell">Mensagem</th>
										<th width="15%">Enviada</th>
                                        <th width="5%" class="d-none d-lg-table-cell">Por</th>
                                        <th width="5%" class="d-none d-lg-table-cell">Ações</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</section>
					<!-- end: page -->
				</section>
<?php
	// $data['modal_id'] = 'modalExclui';
	// $data['modal_title'] = 'Deseja realmente excluir?';
    // $data['modal_icon'] = 'fa-question-circle';
    // $data['modal_header_color'] = "danger";
    // $data['modal_style'] = 'danger';
    // $data['modal_message'] = 'Esta ação é definitiva e não pode ser desfeita.';
	// $data['button'] = array('Exluir', 'Cancelar');
    // $this->load->view('modals/confirma', $data);
?>