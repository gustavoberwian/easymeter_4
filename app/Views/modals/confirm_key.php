<!-- Modal Form -->
<div id="<?php echo $modal_id; ?>" class="modal-block <?php echo isset($modal_style) ? $modal_style : 'modal-block-primary'; ?> mfp-hide">
	<input type="hidden" class="id" value="0">
	<section class="card">
		<header class="card-header">
            <div class="card-actions buttons"></div>
			<h2 class="card-title"><?php echo $modal_title; ?></h2>
		</header>
		<div class="card-body">
			<div class="modal-wrapper">
				<?php if (isset($modal_icon)) : ?>
					<div class="modal-icon">
						<i class="fas <?php echo $modal_icon; ?>"></i>
					</div>
				<?php endif; ?>
				<div class="modal-text">
					<p class="mb-0"><?php echo $modal_message; ?></p>
				</div>
			</div>
		</div>
		<footer class="card-footer">
			<div class="row">
				<div class="col-md-12 text-end">
					<button class="btn btn-default modal-dismiss"><?php echo $button[1]; ?></button>
					<button class="btn btn-danger modal-confirm-key overlay-small" data-loading-overlay><?php echo $button[0]; ?></button>
				</div>
			</div>
		</footer>
	</section>
</div>