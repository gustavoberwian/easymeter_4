<section role="main" class="content-body">
    <header class="page-header">
        <h2>Condomínios</h2>
        <div class="right-wrapper text-right">
            <ol class="breadcrumbs">
                <li><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                <li><span>Condomínios</span></li>
            </ol>
        </div>
    </header>
    <!-- start: page -->
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Cron Tasks Manager</h2>
        </header>
        <div class="card-body">

            <table class="table table-responsive-md table-bordered">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select_all">
                        </th>
                        <th>Horário</th>
                        <th>Comando</th>
                        <th>Status</th>
                        <th>Criado</th>
                        <th>Atualizado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $t): ?>
                        <tr>
                            <td>
                                <input type="checkbox" value="<?= $t->task_id ?>" class="task_checkbox">
                            </td>
                            <td>
                                <?= $t->time ?>
                            </td>
                            <td>
                                <?= $t->command ?>
                            </td>
                            <td
                                class="<?php echo (\mult1mate\crontab\TaskInterface::TASK_STATUS_ACTIVE == $t->status) ? '' : 'text-danger' ?>">
                                <?= $t->status ?>
                            </td>
                            <td>
                                <?= $t->ts ?>
                            </td>
                            <td>
                                <?= $t->ts_updated ?>
                            </td>
                            <td>
                                <a href="#" class="task-edit" data-id="<?= $t->task_id ?>">Editar</a>
                                <a
                                    href="<?php echo site_url('admin/configuracoes/log') ?>?task_id=<?= $t->task_id ?>">Log</a>
                                <a href="#" class="run_task" data-task-id="<?php echo $t->task_id ?>"
                                    data-controller="<?php echo site_url('TasksController'); ?>">Run</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form class="form-inline">
                <div class="form-group">
                    <label for="action" class="mr-2">Com os selecionados</label>
                    <select class="form-control" id="action">
                        <option value="Enable">Habilitar</option>
                        <option value="Disable">Desabilitar</option>
                        <option value="Delete">Apagar</option>
                        <option value="Run">Executar</option>
                    </select>
                </div>
                <div class="form-group ml-2">
                    <input type="submit" value="Aplicar" class="btn btn-primary" id="execute_action">
                </div>
            </form>
        </div>
    </section>
    <section class="card">
        <header class="card-header">
            <h2 class="card-title">Run custom task</h2>
        </header>
        <div class="card-body">
            <form class="form-inline">
                <div class="form-group">
                    <label for="method" class="mr-sm-2">Métodos:</label>
                    <select class="form-control mb-2 mr-sm-3 mb-sm-0" id="method">
                        <option></option>
                        <?php foreach ($methods as $class => $class_methods): ?>
                            <optgroup label="<?= $class ?>">
                                <?php foreach ($class_methods as $m): ?>
                                    <option value="<?= $class . '::' . $m . '()' ?>"><?= $m ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="command" class="mr-sm-2">Comando</label>
                    <input type="text" class="form-control mb-2 mr-sm-3 mb-sm-0" id="command" name="command"
                        placeholder="Controller::method" style="width: 300px;">
                    <input type="hidden" id="controller" name="controller"
                        value="<?php echo site_url('TasksController'); ?>" />
                </div>
                <input type="submit" value="Executar" class="btn btn-primary" id="run_custom_task">
            </form>
            <div id="output_section" style="display: none;">
                <h3>Task output</h3>
                <pre id="task_output_container"></pre>
            </div>
    </section>
    <!-- end: page -->
</section>