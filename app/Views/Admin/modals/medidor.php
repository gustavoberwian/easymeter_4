<!-- Modal Form -->
<div id="modalMedidor" class="modal-block modal-block-primary">
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Detalhes do Medidor</h2>
        </header>
        <form class="form-medidor">
            <div class="card-body">
                <div class="alert alert-danger notification" style="display:none;"></div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-end pt-2">Identificador</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="<?php echo $medidor->nome;?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
										<span class="input-group-text">
                                            <i class="fas fa-microchip"></i>
										</span>
                                    <input type="text" class="form-control" style="width: 70px;" value="<?php echo $medidor->central;?>"  title="Central" readonly disabled>
                                    <span class="input-group-btn" style="width:0px;"></span>
                                    <input type="text" class="form-control text-center" style="margin-left:-1px" value="<?php echo $medidor->posicao;?>"  title="Posição" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-end pt-2">Sensor</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="<?= $medidor->sensor_id; ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
										<span class="input-group-text" title="Fator">
                                            <i class="fas fa-divide"></i>
										</span>
                                    <input type="text" class="form-control" value="<?= $medidor->fator; ?>" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-end pt-2">Calibrado em</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="<?= date("d/m/Y H:i", strtotime($medidor->calibracao)); ?>" readonly disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-end pt-2">Utilizado em</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="<?= is_null($medidor->utilizacao) ? '' : date("d/m/Y H:i", strtotime($medidor->utilizacao)); ?>" readonly disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-end pt-2">Instalado em</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="<?= is_null($medidor->instalacao) ? '' : date("d/m/Y H:i", strtotime($medidor->instalacao)); ?>" readonly disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-end pt-2">Leitura Atual</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="0" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text" title="Central">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                    <input type="text" class="form-control" value="<?php echo date("d/m/Y H:i", time()); ?>" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-end pt-2">Status</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="-" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-car-battery"></i>
                                    </span>
                                    <input type="text" class="form-control" style="width: 70px;" value="0v" readonly disabled>
                                    <span class="input-group-btn" style="width:0px;"></span>
                                    <input type="text" class="form-control" style="margin-left:-1px" value="0%" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-default modal-dismiss">Fechar</button>
                    </div>
                </div>
            </footer>
        </form>
    </section>
</div>
