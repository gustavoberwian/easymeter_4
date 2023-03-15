
<section role="main" class="content-body" data-entity="<?= $entity_id ?>">

    <img src="<?php echo base_url('assets/img/logo-ancar.png'); ?>" alt="<?= "Ancar"; ?>" class="mb-2 mt-2" height="50"/>

    <!-- start: page -->
    <div class="row pt-0">
        <?php foreach ($groups as $group): ?>
            <section class="col-md-6 p-2">
                <div class="card" data-group="<?= $group->bloco_id; ?>">
                    <div class="card-body card-body-nopadding">
                        <div class="mb-0 widget-twitter-profile bg-light">
                            <div class="item profile-info p-0 overflow-hidden">
                                <?php if (is_null($group->img)): ?>
                                    <div class="img-fluid rounded img-hover" data-group="<?= $group->bloco_id; ?>" style="background-image: url('<?= site_url("assets/img/" . $group->img); ?>'); background-repeat: no-repeat; background-size: cover; height: 500px; background-position: center;"></div>
                                <?php else: ?>
                                    <div class="img-fluid rounded img-hover" data-group="<?= $group->bloco_id; ?>"  style="background-image: url('<?= site_url("assets/img/" . $group->img); ?>'); background-repeat: no-repeat; background-size: cover; height: 500px; background-position: center;"></div>
                                <?php endif; ?>
                                <div class="row m-0 fixed-card-top">
                                    <div class="h5 color-f1"><?= $group->nome; ?><span class="float-end"><i class="fas fa-bolt me-3"></i><i class="fas fa-tint me-2"></i></span></div>
                                </div>
                                <div class="row m-0 fixed-card-bottom">
                                    <div class="col-md-3">
                                        <h6 class="card-body-title mb-0 text-primary"><?= $area_comum; ?></br> Consumo Mês</h6>
                                        <div class="row">
                                            <div class="h5 m0 color-f1"><span class="main"><?= $overall_c["consum"]; ?> <small>kWh</small></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="card-body-title mb-0 text-primary"></br>Previsão</h6>
                                        <div class="row">
                                            <div class="h5 m0 color-f1"><span class="main"><?= $overall_c["prevision"] ?> <small>kWh</small></span></div>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="col-md-4">
                                        <h6 class="card-body-title mb-0 text-primary"><?= $area_comum; ?>: Média Diária</h6>
                                        <div class="row">
                                            <div class="h5 m0 color-f1"><span class="main"><?= $overall_c["average"]; ?> <small>kWh</small></span></div>
                                        </div>
                                    </div>
                                    -->
                                    <div class="col-md-3" style="border-left: 1px solid #777;">
                                        <h6 class="card-body-title mb-0 text-primary">Unidades</br>Consumo Mês</h6>
                                        <div class="row">
                                            <div class="h5 m0 color-f1"><span class="main"><?= $overall_l["consum"]; ?> <small>kWh</small></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="card-body-title mb-0 text-primary"></br>Previsão</h6>
                                        <div class="row">
                                            <div class="h5 m0 color-f1"><span class="main"><?= $overall_l["prevision"] ?> <small>kWh</small></span></div>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="col-md-4">
                                        <h6 class="card-body-title mb-0 text-primary">Unidades: Média Diária</h6>
                                        <div class="row">
                                            <div class="h5 m0 color-f1"><span class="main"><?= $overall_l["average"]; ?> <small>kWh</small></span></div>
                                        </div>
                                    </div>
                                    -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <!-- end: page -->
</section>