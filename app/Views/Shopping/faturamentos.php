<section role="main" class="content-body">
    <!-- start: page -->
    <header class="page-header" data-url="<?= $url ?>">
        <h2><?= $group->group_name; ?></h2>
    </header>

    <div class="row">
        <div class="col-6">
            <ul class="nav nav-pills nav-pills-primary mb-3">
                <?php if (!is_null($group->m_energia)) : ?>
                    <li class="nav-item configs" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#energy" type="button">Energia</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_agua)) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#water" type="button">Água</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_gas)) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#gas" type="button">Gás</button>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($group->m_nivel)) : ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#nivel" type="button">Nível</button>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="col-6 text-end">
            <img src="<?php echo base_url('assets/img/' . $user->condo->image_url); ?>" alt="<?= ""; ?>" class="mb-3" height="50"/>
        </div>
    </div>

    <div class="tab-content" style="background-color: transparent; box-shadow: none; padding: 0;">

        <div class="tab-pane fade show active" id="energy">

            <div class="row pt-0">
                <section class="col-md-12 card card-easymeter h-auto mt-0 mb-3">
                    <header class="card-header">
                        <div class="card-actions buttons">
                            <button class="btn btn-primary btn-download" data-group="<?= $group_id; ?>" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
                        </div>
                        <h2 class="card-title">Lançamentos</h2>
                    </header>

                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <button class="ml-3 btn btn-success btn-incluir mb-3">Incluir</button>
                        </div>
                        <div class="tab-form faturamento">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer" id="dt-faturamentos" data-url="/energia/GetFaturamentos" data-group="<?=$group_id ?>">
                                    <thead>
                                        <tr role="row">
                                            <th rowspan="3">Competência</th>
                                            <th rowspan="3">Data inicial</th>
                                            <th rowspan="3">Data final</th>
                                            <th colspan="4" class="text-center"><?= $area_comum; ?></th>
                                            <th colspan="4" class="text-center">Unidades</th>
                                        </tr>
                                        <tr role="row">
                                            <th colspan="3" class="text-center">Consumo - kWh</th>
                                            <th rowspan="2" class="text-center">Demanda - Kw</th>
                                            <th colspan="3" class="text-center">Consumo - kWh</th>
                                            <th rowspan="2" class="text-center">Demanda - Kw</th>
                                            <th rowspan="2">Emissão</th>
                                            <th rowspan="2">Ações</th>
                                        </tr>
                                        <tr role="row">
                                            <th>Total</th>
                                            <th>Ponta</th>
                                            <th>Fora Ponta</th>
                                            <th>Total</th>
                                            <th>Ponta</th>
                                            <th>Fora Ponta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="tab-pane fade" id="water">

            <div class="row pt-0">
                <section class="col-md-12 card card-easymeter h-auto mt-0 mb-3">
                    <header class="card-header">
                        <div class="card-actions buttons">
                            <button class="btn btn-primary btn-water-download" data-group="<?= $group_id; ?>" data-loading-overlay><i class="fas fa-file-download mr-3"></i> Baixar Planilha</button>
                        </div>
                        <h2 class="card-title">Lançamentos</h2>
                    </header>

                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <button class="ml-3 btn btn-success btn-water-incluir mb-3">Incluir</button>
                        </div>
                        <div class="tab-form faturamento">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped dataTable table-hover table-click no-footer" id="dt-water" data-url="/water/GetLancamentosAgua" data-group="<?=$group_id ?>">
                                    <thead>
                                        <tr role="row">
                                            <th rowspan="2">Competência</th>
                                            <th rowspan="2">Data inicial</th>
                                            <th rowspan="2">Data final</th>
                                            <th colspan="3" class="text-center">Consumo - L</th>
                                            <th rowspan="2">Emissão</th>
                                            <th rowspan="2">Ações</th>
                                        </tr>
                                        <tr role="row">
                                            <th>Total</th>
                                            <th>Aberto</th>
                                            <th>Fechado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div>
        <table class="text-dark w-100">
            <tbody><tr>
                <td class="text-end">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?= "Easymeter"; ?>" class="mb-4" height="30"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Faturamento -->
    <div id="md-include" class="modal-block modal-block-primary mfp-hide">
        <section class="card card-easymeter">
            <header class="card-header">
                <div class="card-actions"></div>
                <h2 class="card-title">Cadastrar Lançamento - Energia</h2>
            </header>

            <div class="card-body">

                <div class="alert alert-danger fade show d-none" role="alert">
                    
                </div>

                <form class="form-fechamento">
                    
                    <input type="hidden" id="tar-group" name="tar-group" value="<?= $group_id; ?>">
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Competência<span class="required">*</span></label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control vcompetencia" id="tar-competencia" name="tar-competencia" value="" placeholder="__/____" required tabIndex="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Período <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control vdate" id="tar-data-ini" name="tar-data-ini" value="" placeholder="Data inicial" required tabIndex="2">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control vdate" id="tar-data-fim" name="tar-data-fim" value="" placeholder="Data final" required tabIndex="3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Mensagem</label>
                        <div class="col-lg-9">
                            <textarea class="form-control" id="tar-msg" name="tar-msg" value="" rows="5" tabIndex="4"></textarea>
                        </div>
                    </div>

    <!--
                    <div class="form-group row">
                        <div class="col-lg-3 text-lg-end">
                            <div class="switch switch-sm switch-primary">
                                <input type="checkbox" id="tar-demandas" name="tar-demandas" data-plugin-ios-switch checked/>
                            </div>
                        </div>
                        <label class="col-lg-9 control-label pt-2">Calcular Demandas</label>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-3 text-lg-end">
                            <div class="switch switch-sm switch-primary">
                                <input type="checkbox" id="tar-station" name="tar-station" data-plugin-ios-switch checked/>
                            </div>
                        </div>
                        <label class="col-lg-9 control-label pt-2">Calcular Ponta/Fora Ponta</label>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-3 text-lg-end">
                            <div class="switch switch-sm switch-primary disabled">
                                <input type="checkbox" id="tar-values" name="tar-values" data-plugin-ios-switch/>
                            </div>
                        </div>
                        <label class="col-lg-9 control-label pt-2">Calcular valores</label>
                    </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right" style="padding-top: 30px !important;">TUSD Unitário <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Ponta</label>
                                <input type="text" id="tar-tusd-p" name="tar-tusd-p" class="form-control vnumber" placeholder="Valor da conta em reais" required tabIndex="4">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Fora Ponta</label>
                                <input type="text" id="tar-tusd-f" name="tar-tusd-f" class="form-control vnumber" placeholder="Valor da conta em reais" required tabIndex="5">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right" style="padding-top: 30px !important;">Demanda Unitária <span class="required">*</span></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Ponta</label>
                                <input id="tar-demanda-p" name="tar-demanda-p" class="form-control vnumber" value="" placeholder="Valor em reais" required tabIndex="6">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Fora Ponta</label>
                                <input id="tar-demanda-f" name="tar-demanda-f" class="form-control vnumber" value="" placeholder="Valor em reais" required tabIndex="7">
                            </div>
                        </div>
                    </div>
                </div>
    -->                        
                </form>
            </div>

            <footer class="card-footer">
                <div class="row">
                <div class="col-md-6">
                        <button class="btn btn-primary btn-cfg" href="<?= site_url('shopping/configuracoes/'.$group_id.'#unidades'); ?>" tabIndex="8">Configurar Unidades</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay tabIndex="8">Incluir</button>
                        <button class="btn btn-default modal-dismiss" tabIndex="9">Cancelar</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

    <div id="md-water-include" class="modal-block modal-block-primary mfp-hide">
        <section class="card card-easymeter">
            <header class="card-header">
                <div class="card-actions"></div>
                <h2 class="card-title">Cadastrar Lançamento - Água</h2>
            </header>

            <div class="card-body">

                <div class="alert alert-danger fade show d-none" role="alert">
                    
                </div>

                <form class="form-water-fechamento">
                    
                    <input type="hidden" id="tar-water-group" name="tar-water-group" value="<?= $group_id; ?>">
                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Competência<span class="required">*</span></label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control vcompetencia" id="tar-water-competencia" name="tar-water-competencia" value="" placeholder="__/____" required tabIndex="1">
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Período <span class="required">*</span></label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control vdate" id="tar-water-data-ini" name="tar-water-data-ini" value="" placeholder="Data inicial" required tabIndex="2">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control vdate" id="tar-water-data-fim" name="tar-water-data-fim" value="" placeholder="Data final" required tabIndex="3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 control-label text-lg-right pt-2">Mensagem</label>
                        <div class="col-lg-9">
                            <textarea class="form-control" id="tar-water-msg" name="tar-water-msg" value="" rows="5" tabIndex="4"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <footer class="card-footer">
                <div class="row">
                <div class="col-md-6">
                        <button class="btn btn-primary btn-cfg" href="<?= site_url('shopping/configuracoes/'.$group_id.'#unidades'); ?>" tabIndex="8">Configurar Unidades</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-primary modal-water-confirm overlay-small" data-loading-overlay tabIndex="8">Incluir</button>
                        <button class="btn btn-default modal-dismiss" tabIndex="9">Cancelar</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

    <div id="modalExclui" class="modal-block modal-header-color modal-block-danger mfp-hide">
        <input type="hidden" class="id" value="0">
        <input type="hidden" class="type" value="">
        <section class="card">
            <header class="card-header">
                <div class="card-actions"></div>
                <h2 class="card-title">Você tem certeza?</h2>
            </header>
            <div class="card-body">
                <div class="modal-wrapper">
                    <div class="modal-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="modal-text">
                        <h4>Deseja realmente excluir este Lançamento?</h4>
                        <p>Ao excluir o lançamento, todas as informações de consumo das unidades também serão excluídos.</p>
                        <p><strong>Atenção:</strong> A exclusão é definitiva e não poderá ser desfeita.</p>
                    </div>
                </div>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-danger modal-confirm overlay-small mr-3" style="min-width:69px;" data-timer="10" data-loading-overlay disabled>10</button>
                        <button class="btn btn-default modal-dismiss">Cancelar</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>
    <!-- end: page -->
</section>