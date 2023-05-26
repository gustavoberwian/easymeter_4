<section role="main" class="content-body">
    <header class="page-header">
        <h2>Central <?= $central->nome; ?></h2>
        <div class="right-wrapper text-right">
            <ol class="breadcrumbs">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><a href="<?php echo site_url('admin/centrais'); ?>"><span>Centrais</span></a></li>
                <li><span id="central"><?= $central->nome; ?></span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <div class="row">
        <div class="col">
            <table class="table table-responsive-md table-sm table-bordered mb-0">
                <tr>
                    <?php
                    for ($i = 0; $i < count($leituras); $i++) {
                        $l = isset($x[$leituras[$i]]) ? $x[$leituras[$i]] : 0;
                        echo '<td style="background-color:' . numberToColor($l < 25 ? $l : 0, 0, 24, ['#CC0000', '#EEEE00', '#4CAF50']) . '!important;" title="' . $leituras[$i] . ': ' . $l . '"></td>';
                    }
                    ?>
                </tr>
            </table>
        </div>
    </div>


    <div class="row pt-3">
        <div class="col-md-3">
            <section class="card card-central h-100 <?= ($central->auto_ok == 0) ? 'disabled' : ''; ?>">
                <div class="card-body">
                    <h6 class="card-body-title mb-2 mt-0 text-primary">Central <i class="card-body-online float-right fas fa-circle <?= format_online_status($central->ultimo_envio, $central->nome); ?>"></i></h6>
                    <h2 class="mt-0 mb-2"><?= $central->nome; ?></h2>
                </div>
            </section>
        </div>
        <div class="col-md-3">
            <section class="card card-leitura h-100">
                <div class="card-body">
                    <h6 class="card-body-title mb-2 mt-0 text-primary"><i class="fas fa-sim-card mr-2"></i> Informações</h6>
                    <h5 class="mt-0 mb-1"><b>Codomínio: </b><?= $central->entidade_nome; ?></h5>
                    <h5 class="mt-0 mb-0"><b>Localizador: </b> <?= $central->localizador; ?> <span class="badge badge-default"><?= $central->modo; ?></span></h5>
                    <?php if ($data) : ?>
                        <h5 class="mt-0 mt-1"><b>Hardware/Software: </b> <?= number_format($data->hardware / 100, 2); ?> / <?= number_format($data->software / 100, 2); ?></h5>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <div class="col-md-3">
            <section class="card card-leitura h-100">
                <div class="card-body">
                    <h6 class="card-body-title mb-2 mt-0 text-primary"><i class="fas fa-exclamation-triangle mr-2"></i> Taxa de Erros <?php echo (gettype($erros) == 'boolean' ? ' - ' : (gettype($erros) == 'integer' ? $erros : ($erros->total > 0 ? round(100 - ($erros->realizadas / $erros->total * 100)) . '%' : ''))); ?></span></h6>
                    <h5 class="mt-0 mb-1"><b>Enviado:</b> <?php echo (gettype($erros) == 'boolean' ? 'Sem' : (gettype($erros) == 'integer' ? $erros : ($erros->total > 0 ? intval($erros->total) : 0))); ?> registros</h5>
                    <h5 class="mt-0 mb-0"><b>Recebido:</b> <?php echo (gettype($erros) == 'boolean' ? 'Sem' : (gettype($erros) == 'integer' ? $erros : ($erros->realizadas > 0 ? intval($erros->realizadas) : 0))); ?> registros</h5>
                </div>
            </section>
        </div>
        <div class="col-md-3">
            <section class="card card-auto h-100">
                <div class="card-body text-center">
                    <h6 class="card-body-title mb-2 mt-0 text-primary text-left"><i class="fas fa-magic mr-2"></i> Processa Leituras <i class="card-body-online float-right fas fa-cog btn-central-conf" <?= ($central->auto_ok == 1) ? 'style="display: none;"' : ''; ?>></i></h6>
                    <div class="switch switch-sm switch-success">
                        <input type="checkbox" name="auto" id="auto" data-plugin-ios-switch <?= ($central->auto_ok == 1) ? 'checked' : ''; ?> />
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="row row-eq-height">
        <div class="col-md-9">
            <section class="card h-100">
                <header class="card-header">
                    <h2 class="card-title">Conexões</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="dt-portas" data-url="<?php echo site_url('admin/get_central_detail/' . $central->nome); ?>">
                        <thead>
                            <tr role="row">
                                <th width="10%">Posição</th>
                                <th width="10%">Medidor</th>
                                <?php if (substr($central->nome, 0, 2) == "53") : ?>
                                    <th width="10%">Bateria</th>
                                <?php else : ?>
                                    <th width="10%">Sensor</th>
                                <?php endif; ?>
                                <th width="10%">Monitoramento</th>
                                <th width="10%">Fator</th>
                                <th width="10%">Entrada</th>
                                <th width="10%">Unidade</th>
                                <th width="5%">Tipo</th>
                                <th width="10%">Leitura</th>
                                <th width="10%">Consumo</th>
                                <th width="5%">Fraude</th>
                                <th width="5%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-md-3">
            <section class="card h-100">
                <header class="card-header">
                    <h2 class="card-title">Envios</h2>
                </header>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-pointer" id="dt-envios" data-url="<?php echo site_url('admin/get_central_envios/' . $central->nome); ?>">
                        <thead>
                            <tr role="row">
                                <th width="20%">ID</th>
                                <th width="30%">Tamanho</th>
                                <th width="50%">Data</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    <!-- end: page -->
</section>
<script>
    var auto_ok = <?= $central->auto_ok; ?>;
</script>