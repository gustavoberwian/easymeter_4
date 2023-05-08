<section role="main" class="content-body" data-entidade="<?= $entidade->id ?>" data-ramal="<?= $ramal->id ?>">
    <!-- start: page -->
    <header class="page-header">
        <h2><?= $entidade->nome ?> - Alertas</h2>
    </header>

    <section class="card card-easymeter mb-4">
        <header class="card-header">
            <div class="card-actions buttons">
                <button type="button" class="btn btn-primary btn-alert-config">Configurações</button>
            </div>
            <h2 class="card-title">Alertas</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-hover table-click dt-alerts" id="dt-alertas" data-url="/consigaz/get_alertas" data-tipo="gas">
                <thead>
                <tr role="row">
                    <th width="5%"></th>
                    <th width="10%">Categoria</th>
                    <th width="10%">Medidor</th>
                    <th width="15%">Unidade</th>
                    <th width="55%">Mensagem</th>
                    <th width="10%">Enviada Em</th>
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