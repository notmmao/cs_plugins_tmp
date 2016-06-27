<div class="wp-booklet-sortable" data-pdf-limit="<?php echo get_option('wp-booklet2-pdf-limit-status') ?>" data-post-id="<?php echo $post->ID ?>">
	<?php if ( $pages ) : ?>
		<?php foreach( $pages as $key => $page ) : ?>
				<div class="wp-booklet-portlet">
					<div class="wp-booklet-portlet-header">
						Page
						<span class="wp-booklet-portlet-header-buttons">
							<span class="wp-booklet-header-visibility"></span>
							<span class="wp-booklet-header-remove"></span>
						</span>
					</div>
					<div class="wp-booklet-portlet-content">
						<div class="wp-booklet-portlet-content-left">
							<img data-width="<?php echo $page->get_actual_width() ?>" data-height="<?php echo $page->get_actual_height() ?>" src="<?php echo $page->get_image_url("medium") ?>" class="wp-booklet-img"/>
							<input class="wp-booklet-attachment-id" value="<?php echo $page->get_id() ?>" name="wp-booklet-attachment[]" type="hidden"/>
						</div>
						<div class="wp-booklet-portlet-content-right">
							<p>
								<label>Page Link</label><br/>
								<input class="widefat" type="text" value="<?php echo $page->get_page_link() ?>" name="wp-booklet-attachment-properties[wp-booklet-page-link][]"/>
							</p>
						</div>
						<div class="clearfix"></div>
						<input class="button-secondary wp-booklet-image-upload" type="button" value="Replace image"/>
					</div>
				</div>
		<?php endforeach ?>
	<?php endif ?>
</div>
<a class="button wp-booklet-sortable-add-pages">Add pages</a>
<?php if ( $pdf_capable ) : ?>
<a class="button wp-booklet-sortable-upload-pdf">Upload PDF</a>
<?php endif ?>
<div class="wp-booklet-preloader-overlay"><div class="wp-booklet-preloader-note"></div></div>	

