<?php $first = true; foreach ( $addon['options'] as $i => $option ) :

	$percentage  = '';

	if (strpos($option['price'], '%')) {
		$percentage = 'data-price-percentage="true"';
		str_replace('%', '', $option['price']);
		$price  = get_product_addon_price_for_display( $option['price'] );
		$price = str_replace('$', '', $price);

		if ($price > 0) {
			$price = ' (+' . $price . '%)';
		} else {
			$price = ' (' . $price . '%)';
		}
	} else {
		$price = $option['price'] > 0 ? ' (' . woocommerce_price( get_product_addon_price_for_display( $option['price'] ) ) . ')' : '';
	}

	$price = $option['price'] > 0 ? '(' . woocommerce_price( get_product_addon_price_for_display( $option['price'] ) ) . ')' : '';

	if ( isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] ) ) {
		$current_value = (
				isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] ) &&
				in_array( sanitize_title( $option['label'] ), $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] )
				) ? 1 : 0;
	} else {
		$current_value = $first ? 1 : 0;
		$first         = false;
	}
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ) . '-' . $i; ?>">
		<label><input <?php echo $percentage ?> type="radio" class="addon addon-radio" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>[]" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" value="<?php echo sanitize_title( $option['label'] ); ?>" <?php checked( $current_value, 1 ); ?> /> <?php echo wptexturize( $option['label'] ); ?></label>
	</p>

<?php endforeach; ?>