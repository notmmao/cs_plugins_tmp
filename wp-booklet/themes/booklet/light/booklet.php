<div class="wp-booklet-container-light wp-booklet-<?php echo $instance_id ?>-container">
	<div class="wp-booklet-light wp-booklet wp-booklet-<?php echo $instance_id ?>">
		<?php foreach ( $pages as $key=>$page ) : ?>
			<div class="page" data-page="<?php echo $key ?>">
				
				<?php $page_link = $page->get_page_link(); ?>
					
				<?php if ( $booklet->are_popups_enabled() ) : ?>
				
					<a class="wp-booklet-popup-trigger" <?php if ( !empty( $page_link ) ): ?>data-link="<?php echo $page_link ?>"<?php endif ?> href="<?php echo $page->get_image_url("large") ?>" >
						<img src="<?php echo $page->get_image_url("large") ?>" alt="<?php echo $page->get_image_url("large") ?>"/>
					</a>
				
				<?php else: ?>
					
					<?php if ( !empty($page_link) ) : ?>
						<a href="<?php echo $page_link ?>" >
							<img src="<?php echo $page->get_image_url("large") ?>" alt="<?php echo $page->get_image_url("large") ?>"/>
						</a>
					<?php else : ?>	
						<img src="<?php echo $page->get_image_url("large") ?>" alt="<?php echo $page->get_image_url("large") ?>"/>
					<?php endif ?>	
				
				<?php endif ?>
				
			
			</div>
		<?php endforeach ?>
	</div>
	<?php if ( $booklet->are_thumbnails_enabled() ) : ?>
		<div class="wp-booklet-thumbs-light wp-booklet-thumbs wp-booklet-<?php echo $instance_id ?>-thumbs" dir="<?php echo strtolower( $booklet->get_direction() ) ?>">
			<div class="wp-booklet-carousel" data-wpbookletcarousel="true">
				<ul>
					<?php foreach ( $pages as $key=>$page ) : ?>
						<li data-page="<?php echo $key ?>">
							<a>
								<img src="<?php echo $page->get_image_url("medium") ?>" alt="<?php echo $page->get_image_url("medium") ?>"/>
							</a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
			<a class="wp-booklet-carousel-prev inactive" href="#" data-wpbookletcarouselcontrol="true"></a>
			<a class="wp-booklet-carousel-next" href="#" data-wpbookletcarouselcontrol="true"></a>
		</div>
	<?php endif ?>
</div>


<script type="text/javascript">
	jQuery(document).ready( function() {
		
		var bookletContainer = jQuery(".wp-booklet-<?php echo $instance_id ?>-container");
		 
		bookletContainer.wpBookletExtended({
			width:<?php echo $booklet->get_width() ?>,
			height:<?php echo $booklet->get_height() ?>,
			padding:<?php echo $booklet->get_padding()  ?>,
			speed:<?php echo $booklet->get_speed() ?>,
			direction:'<?php echo $booklet->get_direction() ?>',
			delay:<?php echo $booklet->get_delay() ?>,
			popupsEnabled:<?php echo $booklet->are_popups_enabled() ? "true" : "false" ?>,
			thumbnailsEnabled:<?php echo $booklet->are_thumbnails_enabled() ? "true" : "false" ?>,
			arrowsEnabled:<?php echo $booklet->are_arrows_enabled() ? "true" : "false" ?>,
			pageNumbersEnabled:<?php echo $booklet->are_page_numbers_enabled() ? "true" : "false"  ?>,
			bookletMargin:45,
			thumbnailsContainerMargin:11,
			coverBehavior:"<?php echo $booklet->get_cover_behavior() ?>"
		});

	});
</script>
