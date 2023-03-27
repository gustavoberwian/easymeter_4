<section role="main" class="content-body">

    <header class="page-header" data-group="<?= $group_id; ?>" <?= $user->inGroup('shopping', 'unity') ? 'data-device="'.$unidade->device.'"' : '' ?> >
        <?php if ($user->inGroup('shopping', 'unity')): ?>
            <h2><?= $unidade->nome; ?></h2>
        <?php else: ?>
            <h2><?= $group->group_name; ?></h2>
        <?php endif; ?>
    </header>

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true" aria-label="Close"></button>
        <h4 class="font-weight-bold text-dark">Está página se encontra indisponível no momento</h4>
        <p>Está página se encontra indisponível no momento. Se você acredita que isso seja um erro, <b>entre em contato com nosso suporte clicando no botão abaixo.</b></p>
        <p>
            <button class="btn btn-default mt-1 mb-1" type="button">Conversar com um de nossos atendentes</button>
        </p>
    </div>

</section>