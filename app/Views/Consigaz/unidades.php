
<section role="main" class="content-body" data-class="<?= $url ?>" data-monitoria="<?= $monitoria ?>" data-entidade="<?= $entidade->id ?>">

    <style>
        .ios-switch .state-background {
            background: none;
            border: none;
        }
        .ios-switch .on-background {
            opacity: 100;
        }
        .switch.disabled {
            opacity: 50%;
            pointer-events: none;
        }
        .ios-switch.on .handle {
            background-color: #47a447;
            border: #fff
        }
        .ios-switch .handle {
            background-color: #47a447;
            border: #fff
        }
        .switch.warning .ios-switch .handle {
            background-color: #f1d163;
            border: #fff
        }
        .switch.danger .ios-switch .handle {
            background-color: #d2322d;
            border: #fff
        }
    </style>

    <!-- start: page -->
    <header class="page-header">
        <h2><?= $entidade->nome ?> - Unidades</h2>
    </header>

    <div class="row pt-0">
        <section class="card card-easymeter mb-4">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover dataTable no-footer" id="dt-unidades" data-url="/consigaz/get_unidades">
                    <thead>
                    <tr role="row">
                        <th width="25%" class="text-center">Medidor</th>
                        <th width="25%" class="text-center">Bloco</th>
                        <th width="25%" class="text-center">Apto</th>
                        <th width="15%" class="text-center">Válvula</th>
                        <th width="10%" class="text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <!-- end: page -->
</section>

<?php
echo view('Consigaz/modals/sync');
?>