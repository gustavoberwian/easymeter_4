<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header">
        <h2>Alertas</h2>
    </header>

    <div class="row pt-0 mb-4">
        <div class="col-md-4">
            <section class="card card-comparativo h-100">
                <div class="card-body" style="background-color: #03aeef;">
                    <h6 class="card-body-title mb-3 mt-0 text-light">Cliente <i class="float-end fas fa-microchip"></i></h6>
                    <div class="row">
                        <div class="col-lg-12 pl-1">
                            <select id="sel-entity" name="sel-entity" class="form-control" required>
                                <option disabled value="">Selecione o cliente</option>
                                <?php foreach ($clientes as $i => $cliente) : ?>
                                    <option <?= (array_key_first($clientes) == $i) ? 'selected' : '' ?> value="<?= $cliente->id ?>"><?= $cliente->nome ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

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