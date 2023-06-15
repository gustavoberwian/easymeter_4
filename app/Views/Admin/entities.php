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
                <button class="btn btn-primary btn-incluir"><i class="fa fa-plus"></i> Incluir Entidade</button>
                <button class="btn btn-primary btn-ops" data-bs-toggle="dropdown" id="dropdown-filtros"><i
                        class="fa fa-cog"></i></button>
                <ul class="dropdown-menu dropdown-menu-config" aria-labelledby="dropdown-filtros" role="menu">
                    <li><a href="#" class="inativos"><i class="fas fa-none ps-1"></i> Mostrar Inativos</a></li>
                    <div class="dropdown-divider"></div>
                    <li><a href="#" class="monitor all" data-mode="0"><i class="fas fa-check ps-1"></i> Todos</a></li>
                    <li><a href="#" class="monitor agua" data-mode="1"><i class="fas fa-none ps-1"></i> Água</a></li>
                    <li><a href="#" class="monitor gas" data-mode="2"><i class="fas fa-none ps-1"></i> Gás</a></li>
                    <li><a href="#" class="monitor energia" data-mode="3"><i class="fas fa-none ps-1"></i> Energia</a>
                    </li>
                    <li><a href="#" class="monitor nivel" data-mode="4"><i class="fas fa-none ps-1"></i> Nível</a></li>
                </ul>
            </div>
            <h2 class="card-title">Listagem</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-click table-pointer" id="dt-entities"
                data-url="<?php echo site_url('admin/get_entities'); ?>">
                <thead>
                    <tr role="row">
                        <th width="5%" class="d-none">id</th>
                        <th width="15%">Nome</th>
                        <th width="5%">Tipo</th>
                        <th width="10%">Classificação</th>
                        <th width="5%" title="Monitoramento">Monitora</th>
                        <th width="13%">Local</th>
                        <th width="13%">Administradora</th>
                        <th width="19%">Gestor</th>
                        <th width="5%" class="d-none">status</th>
                        <th width="10%">Ações</th>
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
$data['modal_id'] = 'modalExclui';
$data['modal_title'] = 'Você tem certeza?';
$data['modal_message'] = 'Deseja realmente excluir este Condomínio?';
$data['button'] = array('Excluir', 'Cancelar');
echo view('Admin/modals/confirm', $data);
?>