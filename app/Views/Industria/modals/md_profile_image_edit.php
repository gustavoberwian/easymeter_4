<!-- Modal Form -->
<div id="modalProfile" class="modal-block modal-block-primary">
<section class="card">
		<header class="card-header">
			<div class="card-actions">
				<a tabindex="0" class="card-action card-action-help input-help" data-id="2" data-toggle="popover"></a>
			</div>
			<h2 class="card-title">Editar imagem</h2>
		</header>
			<div class="card-body">                                        
    <div id="image" class="tab-pane">
        <form id="avatar" class="avatar p-3" action="<?php echo site_url('/shopping/profile'); ?>" method="post">
            <input type="hidden" name="crop-image" id="crop-image" value="">
            <input type="hidden" name="facebook" value=""> 
            <div class="form-group row">
                <div class="col-md-12 text-center">
                <div class="form-group row">
                <div class="col-md-12">
                    <div class="img-preview"></div>
                </div>
            </div>
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="input-append">
                            <div class="uneditable-input">
                                <i class="fas fa-file fileupload-exists"></i>
                                <span class="fileupload-preview"></span>
                            </div>
                            <label for="upload_image" class="btn btn-default btn-file">
                                <span class="fileupload-exists">Mudar</span>
                                <span class="fileupload-new">Escolher</span>
                                <input type="file" name="upload_image" id="upload_image" accept="image/*"/>
                            </label>
                            <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remover</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6 text-right mt-3">
                    <button class="d-none" type="reset">Clear</button>
                </div>
            </div>
            <footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-right">
                        <button class="btn btn-primary btn-update">Atualizar</button>
                        <button class="btn btn-default modal-dismiss">Cancelar</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>
<?php if (isset($error) && !$error) { ?>
                    <script>
                        document.getElementById('profile').reset();
                        document.getElementById('avatar').reset();
                    </script>
                <?php } ?>

<script>
    var $image_crop = $('.img-preview').croppie({
        enableExif: true,
        viewport: {
            width:300,
            height:300,
            type:'square'
        },
        boundary:{
            width:400,
            height:400
        }
    });
    
    $('#upload_image').on('change', function() {
        var reader = new FileReader();
        
        reader.onload = function (event) {
            $('.croppie-container .cr-boundary').css('background', 'none');
            $image_crop.croppie('bind', {
                url: event.target.result
            });
        };

        if (this.files.length > 0)
            reader.readAsDataURL(this.files[0]);
            
    });

   $('.btn-update').on('click', function(e) {
        e.preventDefault();
        // crop image
        $image_crop.croppie('result', { type: 'canvas', size: 'viewport' } )
        .then( function(img) {
            // atualiza field
            $("#crop-image").val(img);
            // envia form
            $('.avatar').submit();
        });
    });

    $(document).on('click', '.fileupload-exists[data-dismiss="fileupload"]', function (e) {
        $("#crop-image").val('');
        $('.cr-image, .cr-overlay').removeAttr('style src');
        $('.croppie-container .cr-boundary').css('background', 'url(/assets/img/default_avatar.jpg)');
    });
   
      

   


</script>
