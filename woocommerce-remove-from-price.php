<?php
/**
 * Plugin Name: WooCommerce Remove Variation "From: $XX" Price
 * Plugin URI: https://gist.github.com/BFTrick/7643587
 * Description: Disable the WooCommerce variable product "From: $X" price.
 * Author: Patrick Rauland
 * Author URI: http://patrickrauland.com/
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    	Patrick Rauland
 * @since		1.0
 */

function patricks_custom_variation_price( $price, $product ) {

	$target_product_types = array( 
		'variable' 
	);

	if ( in_array ( $product->product_type, $target_product_types ) ) {
		// if variable product return and empty string
		return '';
	}

	// return normal price
	return $price;
}
add_filter('woocommerce_get_price_html', 'patricks_custom_variation_price', 10, 2);


// that's all folks!