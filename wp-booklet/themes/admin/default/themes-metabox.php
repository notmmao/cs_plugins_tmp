<p>
	<label>Booklet theme</label>
	<select class="widefat" name="wp-booklet-metas[wp-booklet-theme]">
		
		<?php if ( $theme_manager->theme_exists( $booklet->get_theme() ) ) : ?>
			
			<?php foreach( $themes as $theme ) : ?>
				<option <?php echo $theme->get_name() == $booklet->get_theme() ? "selected" : "" ?> value="<?php echo $theme->get_name() ?>"><?php echo ucwords( $theme->get_name() ) ?></option>
			<?php endforeach ?>
			
		<?php else: ?>
			
			<?php foreach( $themes as $theme ) : ?>
				<option <?php echo $default_theme_name == $theme->get_name() ? "selected" : "" ?> value="<?php echo $theme->get_name() ?>"><?php echo ucwords( $theme->get_name() ) ?></option>
			<?php endforeach ?>
			
		<?php endif ?>
		
	</select>
</p>