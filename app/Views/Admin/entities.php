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
                <button class="btn btn-primary btn-incluir"><i class="fa fa-plus"></i> Incluir Condomínio</button>
                <button class="btn btn-primary btn-ops" data-toggle="dropdown"><i class="fa fa-cog"></i></button>
                <ul class="dropdown-menu dropdown-menu-config" role="menu">
                    <li><a href="#" class="inativos"><i class="fas fa-none"></i> Mostrar Inativos</a></li>
                    <div class="dropdown-divider"></div>
                    <li><a href="#" class="monitor agua_gas" data-mode="0"><i class="fas fa-check"></i> Água e Gás</a></li>
                    <li><a href="#" class="monitor agua" data-mode="1"><i class="fas fa-none"></i> Água</a></li>
                    <li><a href="#" class="monitor gas" data-mode="2"><i class="fas fa-none"></i> Gás</a></li>
                </ul>
            </div>
            <h2 class="card-title">Listagem</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-pointer" id="dt-condos" data-url="<?php echo site_url('admin/get_condos'); ?>">
                <thead>
                <tr role="row">
                    <th width="5%">id</th>
                    <th width="15%">Nome</th>
                    <th width="5%">Tipo</th>
                    <th width="5%" title="Monitoramento">Monitora</th>
                    <th width="13%">Municipio</th>
                    <th width="13%">Administradora</th>
                    <th width="19%">Síndico/Gestor</th>
                    <th width="5%">Início</th>
                    <th width="5%">Fim</th>
                    <th width="5%">status</th>
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