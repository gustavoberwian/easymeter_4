<!-- Modal Form -->
<div id="modalBloco" class="modal-block modal-block-primary">
    <form class="form-bloco">
        <section class="card">
            <header class="card-header">
                <h2 class="card-title"><?= $modal_title; ?></h2>
            </header>

			<?php if (isset($bloco->id)) { ?>
				<input name="id" type="hidden" value="<?= $bloco->id;?>">
			<?php } ?>
			<div class="card-body">

				<div class="alert alert-danger notification" style="display:none;"></div>

				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Identificador <span class="required">*</span></label>
					<div class="col-lg-9">
						<input id="id-bloco" name="id-bloco" class="form-control" value="<?php if (isset($bloco->nome)) echo $bloco->nome;?>" placeholder="Identificador do Bloco" required>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-lg-3 control-label text-lg-right pt-2">Ramal <span class="required">*</span></label>
					<div class="col-lg-9">
						<div class="select-wrap">
							<select id="sel-ramal" name="sel-ramal" class="form-control populate" multiple size=undefined required >
                                <?php $ramal = 0; if (isset($bloco->ramal_id)) $ramal = $bloco->ramal_id; ?>
                                <?php foreach ($ramais as $r) : ?>
									<option <?php if ($r->id == $ramal || count($ramais) == 1) echo 'selected '; ?> value="<?= $r->id; ?>"><?= $r->nome; ?></option>
                                <?php endforeach; ?>
							</select>
							<a class="ramal_modal">Adicionar Ramal</a>
						</div>
					</div>
                </div>

			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-end">
						<button class="btn btn-primary modal-confirm overlay-small" data-loading-overlay>Salvar</button>
						<button class="btn btn-default modal-dismiss">Cancelar</button>
					</div>
				</div>
			</footer>
	    </section>
    </form>
</div>

<script>

	var $entity_ramal = $('#sel-entity').select2( {
		ajax: {
			url: $('#sel-entity').data('url'),
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					page: params.page
				};
			}
		},
		theme: 'bootstrap', language: 'pt-BR', placeholder: "Selecione a entidade",
		width: '',
		escapeMarkup: function (markup) { return markup; },
		templateResult: function (data) { return data.nome || data.text; },
		templateSelection: function (data) { return data.nome || data.text; }
	})

</script>
