<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header">
        <h2 data-id="<?= $entity->id; ?>"><?= $entity->nome; ?></h2>
    </header>

    <div class="row">
        <div class="col">
            <section class="card">
                <form class="form-horizontal form-bordered form-condo" novalidate="novalidate">
                    <input id="id-condo" name="id-condo" type="hidden" value="1" readonly="">
                    <header class="card-header">
                        <h2 class="card-title">Visualizar Condomínio</h2>
                        <div class="card-actions buttons">
                            <div class="btn-group">
                                <button class="btn btn-primary btn-unidades active">Unidades</button>
                                <button class="btn btn-primary btn-configuracoes">Configurações</button>
                                <button class="btn btn-primary btn-consumo">Fechamentos</button>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <!-- tab-form unidades -->
                        <div class="tab-form unidades">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="unidades-container">
                                        <div class="mb-2 text-end">
                                            <button class="btn btn-success btn-inclui-unidade mr-2" type="button" disabled="">
                                                <i class="fa fa-plus"></i> Incluir Unidade
                                            </button>
                                            <button class="btn btn-primary btn-filter" type="button" disabled="">
                                                <i class="fa fa-filter"></i>
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped dataTable no-footer" id="dt-unidades" role="grid">
                                                <thead>
                                                    <tr role="row">
                                                        <th class="dt-body-center">Apto</th>
                                                        <th class="dt-body-center">Andar</th>
                                                        <th class="dt-body-center">PIN</th>
                                                        <th class="dt-body-center">Tipo</th>
                                                        <th class="">Proprietário</th>
                                                        <th class="">Email</th>
                                                        <th class="dt-body-center">Telefone</th>
                                                        <th class="">Entradas</th>
                                                        <th class="actions dt-body-center">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab-form unidades -->

                        <!-- tab-form configurações -->
                        <div class="tab-form configuracoes d-none">
                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Config 1 <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input id="conf1-condo" name="conf1-condo" type="text" value="" class="form-control" placeholder="Explicação" readonly="" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 control-label text-lg-right pt-2">Tipo de Tarifagem</label>
                                <div class="col-lg-6">
                                    <div class="radio-custom radio-primary">
                                        <input type="radio" id="tarifa-tipo1" name="tarifa-tipo" value="1" checked="" disabled="">
                                        <label for="tarifa-tipo1">Rateio linear</label>
                                    </div>
                                    <div class="radio-custom radio-primary">
                                        <input type="radio" id="tarifa-tipo2" name="tarifa-tipo" value="2" disabled="">
                                        <label for="tarifa-tipo2">Rateio pelo consumo</label>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <!-- tab-form configurações -->

                        <!-- tab-form fechamentos -->
                        <div class="tab-form consumo d-none">

                            <ul class="nav nav-pills nav-pills-primary">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#pill-agua" data-toggle="tab"><i class="fas fa-tint"></i> Água</a>
                                </li>
                            </ul>

                            <div class="tab-content my-4" style="background-color: transparent;box-shadow: none;padding: 0; border: 0;">
                                <div id="pill-agua" class="tab-pane active">
                                    <div class="row mb-3">
                                        <div class="col text-end">
                                            <button class="btn btn-success btn-tarifar text-end">Tarifar</button>
                                        </div>
                                    </div>
                                    <div id="dt-fechamentos_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive"><table class="table table-bordered table-striped dataTable no-footer" id="dt-fechamentos" data-url="http://easymeter.com.br/ajax/get_fechamentos" role="grid" aria-describedby="dt-fechamentos_info">
                                                <thead>
                                                <tr role="row"><th class="filter dt-body-center sorting" width="10%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Ramal: Ordenar colunas de forma ascendente">Ramal</th><th class="filter dt-body-center sorting" width="10%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Competência: Ordenar colunas de forma ascendente">Competência</th><th class="filter dt-body-center sorting" width="10%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Data Inicial: Ordenar colunas de forma ascendente">Data Inicial</th><th class="filter dt-body-center sorting" width="10%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Data Final: Ordenar colunas de forma ascendente">Data Final</th><th class="filter dt-body-center sorting" width="10%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Consumo: Ordenar colunas de forma ascendente">Consumo</th><th class="filter dt-body-right sorting" width="11%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Valor da Conta: Ordenar colunas de forma ascendente">Valor da Conta</th><th class="filter dt-body-center sorting" width="10%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Emissão: Ordenar colunas de forma ascendente">Emissão</th><th class="filter dt-body-center sorting" width="10%" tabindex="0" aria-controls="dt-fechamentos" rowspan="1" colspan="1" aria-label="Relatórios: Ordenar colunas de forma ascendente">Relatórios</th><th width="9%" class="actions sorting_disabled" rowspan="1" colspan="1" aria-label="Ações">Ações</th></tr>
                                                </thead>
                                                <tbody><tr id="489" role="row" class="odd"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Março/2023</td><td class=" dt-body-center">15/02/2023</td><td class=" dt-body-center">17/03/2023</td><td class=" dt-body-center">3846 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 63.297,54</td><td class=" dt-body-center">22/03/2023</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="489" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="489"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="476" role="row" class="even"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Fevereiro/2023</td><td class=" dt-body-center">18/01/2023</td><td class=" dt-body-center">15/02/2023</td><td class=" dt-body-center">3552 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 59.755,70</td><td class=" dt-body-center">22/02/2023</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="476" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="476"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="465" role="row" class="odd"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Janeiro/2023</td><td class=" dt-body-center">19/12/2022</td><td class=" dt-body-center">18/01/2023</td><td class=" dt-body-center">3506 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 59.215,66</td><td class=" dt-body-center">18/01/2023</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="465" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="465"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="458" role="row" class="even"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Dezembro/2022</td><td class=" dt-body-center">19/11/2022</td><td class=" dt-body-center">19/12/2022</td><td class=" dt-body-center">4130 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 66.779,75</td><td class=" dt-body-center">21/12/2022</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="458" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="458"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="451" role="row" class="odd"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Novembro/2022</td><td class=" dt-body-center">19/10/2022</td><td class=" dt-body-center">19/11/2022</td><td class=" dt-body-center">4042 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 65.699,67</td><td class=" dt-body-center">22/11/2022</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="451" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="451"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="434" role="row" class="even"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Outubro/2022</td><td class=" dt-body-center">19/09/2022</td><td class=" dt-body-center">19/10/2022</td><td class=" dt-body-center">3786 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 62.578,64</td><td class=" dt-body-center">24/10/2022</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="434" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="434"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="427" role="row" class="odd"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Setembro/2022</td><td class=" dt-body-center">17/08/2022</td><td class=" dt-body-center">19/09/2022</td><td class=" dt-body-center">4127 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 66.720,14</td><td class=" dt-body-center">22/09/2022</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="427" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="427"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="414" role="row" class="even"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Agosto/2022</td><td class=" dt-body-center">19/07/2022</td><td class=" dt-body-center">17/08/2022</td><td class=" dt-body-center">3539 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 59.636,46</td><td class=" dt-body-center">18/08/2022</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="414" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="414"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="398" role="row" class="odd"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Julho/2022</td><td class=" dt-body-center">20/06/2022</td><td class=" dt-body-center">19/07/2022</td><td class=" dt-body-center">3621 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 60.597,32</td><td class=" dt-body-center">20/07/2022</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="398" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="398"><i class="fas fa-trash" title="Excluir"></i></a></td></tr><tr id="385" role="row" class="even"><td class=" dt-body-center">E14S005483</td><td class=" dt-body-center">Junho/2022</td><td class=" dt-body-center">09/05/2022</td><td class=" dt-body-center">20/06/2022</td><td class=" dt-body-center">3879 m<sup>3</sup></td><td class=" dt-body-right"><span class="float-left">R$</span> 55.863,82</td><td class=" dt-body-center">21/06/2022</td><td class=" dt-body-center"><span class="badge badge-warning">Não Enviados</span></td><td class=" actions"><a href="#" class="action-download-agua " data-id="385" title="Baixar Planilha"><i class="fas fa-file-download"></i></a>
                                                        <a href="#" class="action-delete " data-id="385"><i class="fas fa-trash" title="Excluir"></i></a></td></tr></tbody></table></div><div id="dt-fechamentos_processing" class="dataTables_processing card" style="display: none;">Processando...</div><div class="row"><div class="col-6"><div class="dataTables_info" id="dt-fechamentos_info" role="status" aria-live="polite">Mostrando de 1 até 10 de 46 registros</div></div><div class="col-6"><div class="dataTables_paginate paging_numbers" id="dt-fechamentos_paginate"><ul class="pagination"><li class="paginate_button page-item active"><a href="#" aria-controls="dt-fechamentos" data-dt-idx="0" tabindex="0" class="page-link">1</a></li><li class="paginate_button page-item "><a href="#" aria-controls="dt-fechamentos" data-dt-idx="1" tabindex="0" class="page-link">2</a></li><li class="paginate_button page-item "><a href="#" aria-controls="dt-fechamentos" data-dt-idx="2" tabindex="0" class="page-link">3</a></li><li class="paginate_button page-item "><a href="#" aria-controls="dt-fechamentos" data-dt-idx="3" tabindex="0" class="page-link">4</a></li><li class="paginate_button page-item "><a href="#" aria-controls="dt-fechamentos" data-dt-idx="4" tabindex="0" class="page-link">5</a></li></ul></div></div></div></div>
                                </div>


                            </div>
                        </div>
                        <!-- tab-form fechamentos -->
                    </div>
                </form>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>