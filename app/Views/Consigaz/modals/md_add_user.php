<!-- Modal Form -->
<div id="md-add-user" class="modal-block modal-block-primary">
	<section class="card">
		<header class="card-header">
			<div class="card-actions">
				<a tabindex="0" class="card-action card-action-help input-help" data-id="2" data-toggle="popover"></a>
			</div>
			<h2 class="card-title">Cadastrar Usuário - <?= $name ?></h2>
		</header>
		<form class="form-add">
            <input type="hidden" name="eid" value="<?= $eid; ?>">
			<div class="card-body">
                <div class="form-group row relation-user-group" data-url="get_groups_for_select/" data-id=<?= $eid ?>>
                    <label for="group-user" class="col-lg-3 control-label text-lg-right pt-2">Agrupamento do
                        usuário <span class="required">*</span></label>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="select-wrap">
                                <select id="group-user" name="group-user" class="form-control">
                                    <option selected disabled value="">Carregando...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row relation-user-unity" data-url="get_unity_for_select">
                    <label for="unity-user" class="col-lg-3 control-label text-lg-right pt-2">Unidade do
                        usuário <span class="required">*</span></label>
                    <div class="col-lg-6 text">
                        <div class="row">
                            <div class="select-wrap">
                                <select id="unity-user" name="unity-user" class="form-control">
                                    <option selected disabled value="">Carregando...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Nome <span class="required">*</span></label>
					<div class="col-lg-6">
                        <input type="text" id="con-nome" name="con-nome" class="form-control vnome" value="" placeholder="Nome do usuário" required>
					</div>
				</div>

                <div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">E-mail <span class="required">*</span></label>
					<div class="col-lg-6">
						<input type="email" id="con-email" name="con-email" class="form-control" value="" placeholder="E-mail do usuário" required>
					</div>
				</div>
                <div class="form-group row">
                    <label for="senha-user" class="col-lg-3 control-label text-lg-right pt-2">Senha <span class="required">*</span></label>
                    <div class="col-lg-6">
                        <input id="senha-user" name="senha-user" type="password" value=""
                            class="form-control vsenha" placeholder="Senha" autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="confirm-user" class="col-lg-3 control-label text-lg-right pt-2">Confirmação de Senha <span class="required">*</span></label>
                    <div class="col-lg-6">
                        <input id="confirm-user" name="confirm-user" type="password" value=""
                            class="form-control vconfirma" placeholder="Confirme">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 control-label text-lg-right"></label>
                    <div class="col-lg-9">
                        <div class="checkbox-custom checkbox-default">
                            <input type="checkbox" id="con-prop" name="con-prop" checked >
                            <label for="con-prop">Proprietário da Unidade</label>
                        </div>
                    </div>
                </div>
              
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-end">
						<button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Cadastrar</button>
						<button class="btn btn-default modal-dismiss">Cancelar</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>
<script>
    $.ajax({
        method:'POST',
        url: '/consigaz/get_groups_for_select/',
        data: { eid: $('.relation-user-group').data('id')},
        success: function (result) {
            $('#group-user').html(result);
            $.ajax({
                method:'POST',
                url: '/consigaz/get_unity_for_select/',
                data: { gid: $('.select-group:selected').data('val')},
                success: function (result) {
                    $('#unity-user').html(result);
                    $('#unity-user').data('val', $('.select-unity:selected').data('val'));
                },
            })
        },
    })
        
    
</script>