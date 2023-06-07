<div id="md-edit-cliente" class="modal-block modal-block-primary">
    <section class="card card-easymeter">
        <header class="card-header">
            <div class="card-actions"></div>
            <h2 class="card-title">Cliente</h2>
        </header>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table-bordered w-100">
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
                                <th width="20%">Último Fechamento</th>
                                <td width="80%"><?= $fechamento ?></td>
                            </tr>
                        </tbody>
                    </table>
<!--                    <br/>TODO: quais infos trazer nesse modal?<br/>-->
<!--                    total blocos<br/>-->
<!--                    total unidades<br/>-->
<!--                    quantos vazamentos<br/>-->
<!--                    quantas valvulas<br/>-->
<!--                    link do ultimo fechamento<br/>-->
<!--               -->
<!--                    <table class="table-bordered w-100">-->
<!--                        <thead>-->
<!--                            <tr class="text-center">-->
<!--                                <th colspan="2" width="100%">Ranking Consumo</th>-->
<!--                            </tr>-->
<!--                            <tr class="text-center">-->
<!--                                <td width="50%">Unidade</td>-->
<!--                                <td width="50%">Consumo</td>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                            <tr>-->
<!--                                <td width="50%"><span class="float-start ms-1">0</span><span class="float-end me-1">m³</span></td>-->
<!--                                <td width="50%"><span class="float-start ms-1">0</span><span class="float-end me-1">m³</span></td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <td width="50%"><span class="float-start ms-1">0</span><span class="float-end me-1">m³</span></td>-->
<!--                                <td width="50%"><span class="float-start ms-1">0</span><span class="float-end me-1">m³</span></td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <td width="50%"><span class="float-start ms-1">0</span><span class="float-end me-1">m³</span></td>-->
<!--                                <td width="50%"><span class="float-start ms-1">0</span><span class="float-end me-1">m³</span></td>-->
<!--                            </tr>-->
<!--                        </tbody>-->
<!--                    </table>-->
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