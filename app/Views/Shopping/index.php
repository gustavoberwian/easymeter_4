
<section role="main" class="content-body" data-entity="<?= $entity_id ?>" data-class="<?= $url ?>" data-monitoria="<?= $monitoria ?>">

    <?php if (!empty($user->entity->image_url)) : ?>
        <img src="<?php echo base_url('assets/img/' . $user->entity->image_url); ?>" alt="<?= $user->entity->nome; ?>" class="mb-2 mt-2" height="50"/>
    <?php endif; ?>

    <!-- start: page -->
    <div class="row pt-0">
        <?php if (empty($groups)) : ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true" aria-label="Close"></button>
                <h4 class="font-weight-bold text-dark">Nenhum estabelecimento cadastrado em sua conta</h4>
                <p>Não encontramos nenhum estabelecimento cadastrado em sua conta na nossa base de dados. Se você acredita que isso seja um erro, <b>entre em contato com nosso suporte clicando no botão abaixo.</b></p>
                <p>
                    <button class="btn btn-default mt-1 mb-1" type="button">Conversar com um de nossos atendentes</button>
                </p>
            </div>
        <?php endif; ?>
        <?php if ($user->inGroup('consigaz')) : ?>
            <section class="card card-easymeter mb-4">
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-click dataTable no-footer" id="dt-entidades" data-url="/shopping/get_agrupamentos_by_entidade">
                        <thead>
                        <tr role="row">
                            <th rowspan="2" class="text-center">Nome</th>
                            <th rowspan="2" class="text-center">Tipo</th>
                            <th rowspan="2" class="text-center">Monitora</th>
                            <th rowspan="2" class="text-center">Endereço</th>
                            <th rowspan="2" class="text-center">Município</th>
                            <th rowspan="2" class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < count($groups); $i++): ?>
                                <tr role="row" class="<?= ($i % 2 == 0) ? "odd" : "" ?>" data-group="<?= $groups[$i]->agrupamento_id; ?>">
                                    <td class="dt-body-center"><?= $groups[$i]->nome; ?></td>
                                    <td class="dt-body-center"><?= $groups[$i]->tipo; ?></td>
                                    <td class="dt-body-center">
                                        <?= ($groups[$i]->m_energia) ? '<i class="fas fa-bolt text-warning"></i>' : '' ?>
                                        <?= ($groups[$i]->m_agua) ? '<i class="fas fa-tint text-primary"></i>' : '' ?>
                                        <?= ($groups[$i]->m_gas) ? '<i class="fas fa-fire text-success"></i>' : '' ?>
                                        <?= ($groups[$i]->m_nivel) ? '<i class="fas fa-ruler-vertical text-info"></i>' : '' ?>
                                    </td>
                                    <td class="dt-body-center"><?= $groups[$i]->endereco; ?></td>
                                    <td class="dt-body-center"><?= $groups[$i]->municipio; ?></td>
                                    <td class="dt-body-center">
                                        <a class="action-visualiza text-center text-primary"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php else : ?>
            <?php for ($i = 0; $i < count($groups); $i++): ?>
                <section class="col-md-6 p-2 card">
                    <div class="card card-group" data-group="<?= $groups[$i]->agrupamento_id; ?>">
                        <div class="card-body card-body-nopadding">
                            <div class="mb-0 widget-twitter-profile bg-light">
                                <div class="item profile-info p-0 overflow-hidden">
                                    <?php if (is_null($groups[$i]->img)): ?>
                                        <div class="img-fluid rounded img-hover" data-group="<?= $groups[$i]->agrupamento_id; ?>" style="background-image: url('<?= site_url("assets/img/" . $groups[$i]->img); ?>'); background-repeat: no-repeat; background-size: cover; height: 500px; background-position: center;"></div>
                                    <?php else: ?>
                                        <div class="img-fluid rounded img-hover" data-group="<?= $groups[$i]->agrupamento_id; ?>"  style="background-image: url('<?= site_url("assets/img/" . $groups[$i]->img); ?>'); background-repeat: no-repeat; background-size: cover; height: 500px; background-position: center;"></div>
                                    <?php endif; ?>
                                    <div class="row m-0 fixed-card-top">
                                        <div class="h5 color-f1">
                                            <?= $groups[$i]->nome; ?>
                                            <span class="float-end">
                                                <?= ($groups[$i]->m_energia) ? '<i class="fas fa-bolt me-3"></i>' : '' ?>
                                                <?= ($groups[$i]->m_agua) ? '<i class="fas fa-tint me-3"></i>' : '' ?>
                                                <?= ($groups[$i]->m_gas) ? '<i class="fas fa-fire me-3"></i>' : '' ?>
                                                <?= ($groups[$i]->m_nivel) ? '<i class="fas fa-ruler-vertical me-3"></i>' : '' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row m-0 fixed-card-bottom">
                                        <div class="col-md-3">
                                            <h6 class="card-body-title mb-0 text-primary"><?= $area_comum[$i]; ?></br> Consumo Mês</h6>
                                            <div class="row">
                                                <div class="h5 m0 color-f1"><span class="main"><?= ($user->inGroup('demo')) ? number_format(mt_rand(10000, 100000), 0, ',', '.') : $overall_c[$i]["consum"]; ?> <small>kWh</small></span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="card-body-title mb-0 text-primary"></br>Previsão</h6>
                                            <div class="row">
                                                <div class="h5 m0 color-f1"><span class="main"><?= ($user->demo) ? number_format(mt_rand(10000, 100000), 0, ',', '.') : $overall_c[$i]["prevision"] ?> <small>kWh</small></span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="border-left: 1px solid #777;">
                                            <h6 class="card-body-title mb-0 text-primary">Unidades</br>Consumo Mês</h6>
                                            <div class="row">
                                                <div class="h5 m0 color-f1"><span class="main"><?= ($user->demo) ? number_format(mt_rand(10000, 100000), 0, ',', '.') : $overall_l[$i]["consum"]; ?> <small>kWh</small></span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="card-body-title mb-0 text-primary"></br>Previsão</h6>
                                            <div class="row">
                                                <div class="h5 m0 color-f1"><span class="main"><?= ($user->demo) ? number_format(mt_rand(10000, 100000), 0, ',', '.') : $overall_l[$i]["prevision"] ?> <small>kWh</small></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
    <!-- end: page -->
</section>