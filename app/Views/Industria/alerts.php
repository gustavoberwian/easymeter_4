			<section role="main" class="content-body">
					<header class="page-header">
						<h2>Bauducco CD Extrema/MG</h2>
					</header>

                    <img src="/assets/img/bauducco.png" height="100" alt="Bauducco" class="mb-4">

                    <div class="row">
        <div class="col-6">
            <ul class="nav nav-pills nav-pills-primary mb-3">
                <?php if (($user->entity->m_energia) == 1) : ?>
                    <li class="nav-item configs" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#energy" type="button">Energia</button>
                    </li>
                <?php endif; ?>
                <?php if (($user->entity->m_agua) == 1) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#water" type="button">Água</button>
                    </li>
                <?php endif; ?>
                <?php if (($user->entity->m_gas) == 1) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#gas" type="button">Gás</button>
                    </li>
                <?php endif; ?>
                <?php if (($user->entity->m_nivel) == 1) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link configs" data-bs-toggle="pill" data-bs-target="#nivel" type="button">Nível</button>
                    </li>
                <?php endif; ?>
            </ul>
                </div>

                <div class="tab-content" style="background-color: transparent; box-shadow: none; padding: 0;">

                    <div class="tab-pane fade show active" id="water">
            
    <section class="card card-easymeter mb-4">
        <header class="card-header">
            <div class="card-actions buttons">
                <button type="button" class="btn btn-primary btn-alert-config">Configurações</button>
            </div>
            <h2 class="card-title">Alertas</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-hover table-click dt-alerts" id="dt-alerts-water" data-url="/industria/get_alerts" data-tipo="agua">
                <thead>
                <tr role="row">
                    <th width="5%" class="text-center"><i class="fas fa-tint text-primary center"></i></th>
                    <th width="10%">Categoria</th>
                    <th width="10%">Medidor</th>
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

</div>

<div class="tab-pane fade <?= $monitoria === 'nivel' ? 'show active' : '' ?>" id="nivel">

    <section class="card card-easymeter mb-4">
        <header class="card-header">
            <div class="card-actions buttons">
                <button type="button" class="btn btn-primary btn-alert-config">Configurações</button>
            </div>
            <h2 class="card-title">Alertas</h2>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-hover table-click dt-alerts" id="dt-alerts-nivel" data-url="/industria/get_alerts" data-tipo="nivel">
                <thead>
                <tr role="row">
                    <th width="5%" class="text-center"><i class="fas fa-database text-info"></i></th>
                    <th width="10%">Categoria</th>
                    <th width="10%">Medidor</th>
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

				</section>
