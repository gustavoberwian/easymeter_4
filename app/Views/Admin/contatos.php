<section role="main" class="content-body">
    <header class="page-header">
        <h2>Contatos</h2>
        <div class="right-wrapper text-end">
            <ol class="breadcrumbs pe-4">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><span>Contatos</span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <section class="card">
        <header class="card-header">
            <div class="card-actions buttons">
                <div class="dropdown">
                    <button class="btn btn-primary btn-incluir dropdown-toggle ml-3" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-config">
                        <li><a href="#" class="monitor" data-filter=""><i class="far fa-check-square"></i>Todos</a></li>
                        <li><a href="#" class="monitor" data-filter="0"><i class="far fa-square"></i>Responder</a></li>
                        <li><a href="#" class="monitor" data-filter="1"><i class="far fa-square"></i>Respondidos</a></li>
                    </ul>
                </div>
            </div>
            <h2 class="card-title">Contatos</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-striped table-nowrap" id="dt-contatos"
                data-url="<?php echo site_url('admin/get_contatos'); ?>" data-total="<?php echo $total; ?>">
                <thead>
                    <tr role="row">
                        <th width="18%">Nome</th>
                        <th width="18%">E-mail</th>
                        <th width="8%">Telefone</th>
                        <th width="21%">Entidade</th>
                        <th width="12%">Cidade</th>
                        <th width="10%">Data</th>
                        <th width="8%">Status</th>
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