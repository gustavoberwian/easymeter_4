<section role="main" class="content-body">
    <!-- start: page -->
    <div class="row">
        <?php
        $first = 'my-3 mt-md-0';
        $atual = "";
        $primeiro = "";
        foreach ($centrais as $central) {
            if ($atual == $central->condo) {
                $primeiro = "";
            } else {
                $primeiro = "card-featured-left card-featured-primary";
                $atual = $central->condo;
            }

            ?>
            <div class="col-md-3 col-sm-6 <?= $first; ?>">
                <section class="card <?= $primeiro; ?> card-central <?= ($central->auto_ok == 0) ? 'disabled' : ''; ?>" id="<?= $central->nome; ?>" data-parent="<?= $central->parent; ?>">
                    <div class="card-body">
                        <a style="text-decoration: none;" href="<?= site_url('admin/centrais/'.$central->nome); ?>">
                            <h4 class="my-0 text-primary">
                                <?= $central->nome; ?>
                                <i class="card-body-online float-right fas fa-circle <?= format_online_status($central->ultimo_envio, $central->nome);?>" title="<?= ($central->auto_ok > 0) ? '' : 'Processamento das leituras desabilitado'; ?>"></i>
                                <?php if (!is_null($central->tensao)) { ?>
                                    <i class="card-body-online float-right mr-2 fas <?= ($central->fonte == "R") ? 'fa-bolt text-success' : 'fa-car-battery text-danger'; ?>" title="<?= number_format($central->tensao / 10, 1, ",", "").'V'; ?>"></i>
                                <?php } ?>
                                <?php if (!is_null($central->fraude_hi) && ($central->fraude_hi != '000.000.000.000' || $central->fraude_low != '000.000.000.000')) { ?>
                                    <i class="card-body-online float-right mr-2 fas fa-user-secret text-danger" title="Fraude Detectada"></i>
                                <?php } ?>
                                <?php if (!is_null($central->tamanho) && $central->tamanho > 0) { ?>
                                    <i class="card-body-online float-right mr-2 fas fa-sort-amount-up text-<?= ($central->tamanho > 1000) ? "danger" : 'warning' ; ?>" title="<?= number_format($central->tamanho, 0, ",", ".").' B'; ?>"></i>
                                <?php } ?>
                                </h6>
                                <p class="text-3 text-muted my-0"><span title="Último envio"><?= ($central->ultimo_envio) ? date('d/m/y H:i:s', $central->ultimo_envio) : '-' ; ?></span><span class="float-right" title="Localizador"><?= $central->localizador; ?></span></p>
                                <div class="row">
                                    <div class="col-8">
                                        <p class="text-2 text-muted my-0"><?= substr($central->condo, 0, 20); ?></p>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span class="badge badge-default"><?= $central->modo; ?></span>
                                    </div>
                                </div>
                        </a>
                    </div>
                </section>
            </div>
            <?php
            $first = 'mb-3';
        }
        ?>
    </div>
    <!-- end: page -->
</section>