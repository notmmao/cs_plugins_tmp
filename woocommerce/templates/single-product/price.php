<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// IMPORTANT(stas): This is a hack to get role-plugi prie on the product page

$role_price = '';

$current_user = new WP_User(wp_get_current_user()->ID);
$user_roles = $current_user->roles;
$user_role = array_shift($user_roles);
$rp_percentage = get_option( $user_role );
$rp_percentage = (empty($rp_percentage) ? 0 : $rp_percentage);
$target_product_types = array('variable');
$rp_apply_round = get_option( 'rp_round_price' );
$reg_price = $product->price;

global $post;

$my_terms = get_option('myCategories');
$terms = get_the_terms( $post->ID, 'product_cat');
if ($terms) {
    if (!empty($my_terms)){
        foreach ($terms as $term) {
            $product_cat_name = $term->name;
            if ( in_array($product_cat_name, $my_terms )) {
                $rp_percentage = 0;
            }
        }
    }
}

if (isset($rp_percentage) && $rp_percentage > 0) {
    $role_price = "<div style='display:none' id='role-discount' data-value='" . $rp_percentage . "'></div>";
}
?>

<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<p class="price"><?php echo $product->get_price_html(); ?></p>
    <?php echo($role_price); ?>

	<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>