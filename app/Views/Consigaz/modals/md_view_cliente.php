<div id="md-edit-cliente" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Cliente</h2>
        </header>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table-bordered w-100 border-color-light-grey-3">
                        <thead>
                            <tr class="text-center">
                                <th colspan="2" width="100%">Informações Gerais</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="text-center">
                                <th width="20%">Nome</th>
                                <td width="80%"><?= $entidade->nome; ?></td>
                            </tr>
                            <tr class="text-center">
                                <th width="20%">Endereço</th>
                                <td width="80%"><?= $entidade->logradouro . ", " . $entidade->numero . " - " . $entidade->bairro . ", " . $entidade->cidade . " - " . $entidade->uf . ", " . $entidade->cep; ?></td>
                            </tr>
                            <tr class="text-center">
                                <th width="20%">Fechamento</th>
                                <td width="80%"><?= $fechamento ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <table class="table-bordered w-100 border-color-light-grey-3">
                        <thead>
                        <tr class="text-center">
                            <th colspan="2" width="100%">Ranking Consumo</th>
                        </tr>
                        <tr class="text-center">
                            <td width="50%">Unidade</td>
                            <td width="50%">Consumo</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($unidades_ranking)): ?>
                            <?php foreach ($unidades_ranking as $unidade): ?>
                                <tr>
                                    <td width="50%"><span class="float-start ms-1"><?= $unidade->unidade ?></span></td>
                                    <td width="50%"><span class="float-start ms-1"><?= number_format($unidade->value, 2, ',', '.') ?></span><span class="float-end me-1">m³</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <th rowspan="3">
                                Nenhum registro foi encontrado
                            </th>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table-bordered w-100 border-color-light-grey-3">
                        <thead>
                        <tr class="text-center">
                            <th colspan="2" width="100%">Contagem</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center">
                            <td width="50%">Blocos</td>
                            <td width="50%"><?= $blocos ?></td>
                        </tr>
                        <tr class="text-center">
                            <td width="50%">Unidades</td>
                            <td width="50%"><?= $unidades ?></td>
                        </tr>
                        <tr class="text-center">
                            <td width="50%">Vazamentos</td>
                            <td width="50%"><?= $vazamentos ?></td>
                        </tr>
                        <tr class="text-center">
                            <td width="50%">Válvulas</td>
                            <td width="50%"><?= $valvulas ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="card-footer">
            <div class="text-end">
                <button class="btn btn-default modal-dismiss" tabIndex="9">Fechar</button>
            </div>
        </footer>
    </section>
</div>