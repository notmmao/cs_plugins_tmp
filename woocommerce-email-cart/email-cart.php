<?php
/**
 * Plugin Name: WooCommerce Email Cart
 * Description: Create a WooCommerce cart and send it to any email address.
 * Author: cxThemes
 * Author URI: http://codecanyon.net/user/cxThemes/portfolio
 * Plugin URI: http://codecanyon.net/item/email-cart-for-woocommerce/5568059
 * Version: 1.12
 * Text Domain: email-cart
 * Domain Path: /languages/
 *
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC_Email_Cart
 * @author    cxThemes
 * @category  WooCommerce
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Required functions
 **/
if ( ! function_exists( 'is_woocommerce_active' ) ) require_once( 'woo-includes/woo-functions.php' );

require 'plugin-updates/plugin-update-checker.php';
$EmailCartUpdateChecker = new PluginUpdateChecker(
	'http://cxthemes.com/plugins/woocommerce-email-cart/email-cart.json',
	__FILE__,
	'email-cart'
);

if ( ! is_woocommerce_active() ) return;

/**
 * The WC_Email_Cart global object
 * @name $WC_Email_Cart
 * @global WC_Email_Cart $GLOBALS['WC_Email_Cart']
 */
$GLOBALS['WC_Email_Cart'] = new WC_Email_Cart();

/**
 * Email Cart Main Class.  This class is responsible
 * for setting up the admin start page/menu
 * items.
 *
 */
class WC_Email_Cart {
	
	
	/* Plugin id */
	private $id			= 'woocommerce_email_cart';
	
	
	/* Plugin text domain */
	const TEXT_DOMAIN	= 'email-cart';
	
	
	/* Plugin text domain */
	const VERSION		= '1.12';
	
	
	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {
		
		include_once( 'settings/class-email-cart-settings.php' );
		
		register_activation_hook( __FILE__, array( $this, 'set_email_cart_settings' ) );
		
		add_action( 'init',    array( $this, 'load_translation' ) );
		
		add_action( 'init',  array( $this, 'add_product_to_cart' ) );
		
		// add the menu itme
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
		//add_filter( 'woocommerce_general_settings', array( $this, 'add_order_email_cart_setting' ) );
		
		add_action('wp_ajax_woocommerce_add_email_cart_item', array( $this, 'woocommerce_ajax_add_email_cart_item' ));
			
		if (
				( ! empty($_GET["page"]) )
				&&
				(
					( $_GET["page"] == "woocommerce_email_cart" )
					||
					( $_GET["page"] == "email_cart_settings" )
				)
			){
			
			add_action( 'admin_print_styles', array( $this, 'admin_scripts' ) );
	
		}
		
		
		if (get_option( 'email_cart_show_on_cart_page' ) == 'yes' ) {
			
			//Only load the Email Cart -> button if the option is ticked in the settings
		
			add_action( "woocommerce_cart_collaterals", array( $this, 'cart_page_call_to_action' ) );
			
		}
		
		//Load the email cart on the frontend anyway in anticipation of the user deeplinking
		
		add_action( "woocommerce_after_cart", array( $this, 'cart_page_load_form' ) );
			
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		
		
		add_action( 'woocommerce_init', array( $this, 'add_cart_functionality_to_admin' ), 80 );
		
		// send cart
		add_action( 'init', array( $this, 'send_email_cart' ), 80 );
	}
	
	public function add_cart_functionality_to_admin() {
		global $woocommerce;
		
		if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
			include_once( $woocommerce->plugin_path . '/woocommerce-functions.php' );
		} else {
			include_once( $woocommerce->plugin_path . '/includes/wc-template-functions.php' );
		}
		
		$woocommerce->frontend_includes();
		
		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
		$woocommerce->session  = new $session_class();
		$woocommerce->cart     = new WC_Cart();  
		$woocommerce->customer = new WC_Customer();
	}
	
	public function set_email_cart_settings() {
		$email_cart_settings = WC_Email_Cart_Settings::get_settings();
		
		foreach ( $email_cart_settings as $option ) {
			$existing_value = get_option( $option["id"] );
			if ( ! $existing_value ) {
				update_option( $option["id"], $option["default"]);
			}
		}
	}
	
	/**
	 * Add products to the cart - When GET parameter email_cart_products is in url add products to the cart
	 */
	public function add_product_to_cart() {
		if ( ! is_admin() ) {
			if ( ! empty( $_GET['email_cart_products'] ) ) {
				global $woocommerce;
				
				$landing_page = $_GET["landing_page"];
				
				$get_id = $_GET['unId'];
				
				$woocommerce->cart->empty_cart();
				$product_ids = $_GET['email_cart_products'];
				if (strpos($product_ids, ',') !== FALSE) {
					$product_ids = explode(",", $product_ids);
				} else {
					$product_ids = array( $product_ids );
				}
				
				foreach ($product_ids as $product_id) {
					if (strpos($product_id, '_') !== FALSE) {
						// Split product parent id from variation data
						$product_variation_ids = preg_split("/_/",$product_id,2);
						$product_id = $product_variation_ids[0];
						
						// Check if quantity is bigger than 1
						$product_qty = 1;
						if (strpos($product_id, '.') !== FALSE) {
							$product_info = explode(".", $product_id);
							$product_qty = $product_info[0];
							$product_id = $product_info[1];
						}
						
						$product_variation_info = explode("(", $product_variation_ids[1]);
						
						$variation_id = $product_variation_info[0];
						
						// Get variation attributes
						$variation_attributes = substr($product_variation_info[1], 0, -1);
						$variation_attributes = explode(" ", $variation_attributes);
						$request_variation_attributes = array();
						foreach ($variation_attributes as $attr) {
							$attr_key_val = explode("=", $attr);
							$request_variation_attributes[$attr_key_val[0]] = $attr_key_val[1];
						}
						$adding_to_cart = get_product( $product_id );
						$attributes = $adding_to_cart->get_attributes();
						$variation  = get_product( $variation_id );
						$all_variations_set = true;
						
						// Verify all attributes
						foreach ( $attributes as $attribute ) {
							
							if ( ! $attribute['is_variation'] )
								continue;
				
							$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );
							
							if ( ! empty( $request_variation_attributes[ $taxonomy ] ) ) {
								// Don't use woocommerce_clean as it destroys sanitized characters
								$value = sanitize_title( trim( stripslashes( $request_variation_attributes[ $taxonomy ] ) ) );
								// Get valid value from variation
								$valid_value = $variation->variation_data[ $taxonomy ];
								// Allow if valid
								if ( $valid_value == '' || $valid_value == $value ) {
									if ( $attribute['is_taxonomy'] )
										$variations[ esc_html( $attribute['name'] ) ] = $value;
									else {
										// For custom attributes, get the name from the slug
										$options = array_map( 'trim', explode( '|', $attribute['value'] ) );
										foreach ( $options as $option ) {
											if ( sanitize_title( $option ) == $value ) {
												$value = $option;
												break;
											}
										}
										$variations[ esc_html( $attribute['name'] ) ] = $value;
									}
									continue;
								}
				
							}
				
							$all_variations_set = false;
						}
						
						if ( $all_variations_set ) {
							$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $product_qty, $variation_id, $variations );
	
							if ( $passed_validation ) {
								$woocommerce->cart->add_to_cart( $product_id, $product_qty, $variation_id, $variations );
							}
						}
					} else {
						// Check if quantity is bigger than 1
						$product_qty = 1;
						if (strpos($product_id, '.') !== FALSE) {
							$product_info = explode(".", $product_id);
							$product_qty = $product_info[0];
							$product_id = $product_info[1];
						}
						$woocommerce->cart->add_to_cart( $product_id, $product_qty );
					}
				}
				if (isset($landing_page)) {
					if ($landing_page == "checkout") {
						header('Location: '.$woocommerce->cart->get_checkout_url());
					} else {
						header('Location: '.$woocommerce->cart->get_cart_url().'?unId='.$get_id);
						//header('Location: '.$woocommerce->cart->get_cart_url());
					}
				} else {
					header('Location: '.$woocommerce->cart->get_cart_url());
				}
				exit;
			}
		}
	}


	/**
	 * Localization
	 */
	public function load_translation() {
		
		// localisation
		load_plugin_textdomain( 'email-cart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Send Email Cart - Send email to customer with link to cart
	 */
	public function send_email_cart() {
		
		if ( ( ! empty( $_GET["cart_sent"] ) ) && ( $_GET["cart_sent"] == 1 ) ) {
			
			global $woocommerce, $wpdb;
			
			( function_exists('wp_verify_nonce') ) ? wp_verify_nonce( 'email-cart-sent' ) : $woocommerce->verify_nonce( 'email-cart-sent' );
		

			$email_address = ( isset( $_POST["email_address"] ) ) ? $_POST["email_address"] : null;
			$cc_email_address = ( isset( $_POST["cc_email_address"] ) ) ? $_POST["cc_email_address"] : null;
			$bcc_email_address = ( isset( $_POST["bcc_email_address"] ) ) ? $_POST["bcc_email_address"] : null;
			$email_content = ( isset( $_POST["email_content"] ) ) ? $_POST["email_content"] : null;
			$email_subject = ( isset( $_POST["email_subject"] ) ) ? $_POST["email_subject"] : null;
			$landing_page = ( isset( $_POST["landing_page"] ) ) ? $_POST["landing_page"] : null;
			$share_link = ( isset( $_POST["share_link"] ) ) ? $_POST["share_link"] : null;
			
			$share_parts = explode("?", $share_link);
			$share_link_query_string = $share_parts[1];
			parse_str($share_link_query_string, $share_params);

			if (strpos($email_address, ',') !== FALSE) {
				$test_email_address = explode(",", $email_address);
				$email_address = array();
				foreach ($test_email_address as $email) {
					$email_address[] = sanitize_email( $email );
				}
			} else {
				$email_address = sanitize_email( $email_address );
			}
			$to = $email_address;

			$totals_colspan = 3; //Qty, Price
			
			ob_start();
			
			$header_style	= "font-size:12px; background-color:#eee; font-weight:normal; color:#555;";
			$total_style	= "font-size:14px; ";
			$cell_style		= "border:1px solid #eee; padding:7px 9px; color:#222; font-size:14px;";
			$table_style	= "border:1px solid #eee;";
			?>
			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="<?php echo $table_style ?> font-family:Arial, sans-serif;">
				<tr>
					<th style='text-align:left; <?php echo $header_style ?> <?php echo $cell_style ?>' width="60%"><?php echo __('Product', 'email-cart'); ?></th>
					<th style='text-align:right; <?php echo $header_style ?> <?php echo $cell_style ?>' width="15%"><?php echo __('Price', 'email-cart'); ?></th>
					<th style='text-align:right; <?php echo $header_style ?> <?php echo $cell_style ?>' width="10%"><?php echo __('Qty', 'email-cart'); ?></th>
					<th style='text-align:right; <?php echo $header_style ?> <?php echo $cell_style ?>' width="15%"><?php echo __('Total', 'email-cart') ?></th>
				</tr>
				<?php
				// Constants
				if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
					define( 'WOOCOMMERCE_CART', true );
				}
				$woocommerce->cart->calculate_totals();
				$var_cart_contents = $woocommerce->cart->get_cart();
				
				$final_cart_content = serialize($var_cart_contents);
			
				foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
					 
					$product_price = apply_filters( 'woocommerce_cart_item_product', $cart_item['pricing_item_meta_data'], $cart_item, $cart_item_key );

					$pro_total = $cart_item['line_total']; 
					$pro_quantity = $product_price['_quantity']; 
					if (!$pro_quantity) {
						$pro_quantity = 1;
					}
					$pro_price = $pro_total/$pro_quantity;
					
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						?>
						<tr>

							<td class="product-name" style="<?php echo $cell_style ?>">
								<?php
									if ( ! $_product->is_visible() ) {
										echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
									} else {
										if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
											printf('<a href="%s">%s</a>', esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $cart_item['product_id'] ) ) ), apply_filters('woocommerce_in_cart_product_title', $_product->get_title(), $cart_item, $cart_item_key ) );
										} else {
											echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );
										}
									}
									
									// Meta data
									echo $woocommerce->cart->get_item_data( $cart_item );

		               				// Backorder notification
		               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
		               					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
								?>
							</td>

							<td class="product-price" style="<?php echo $cell_style ?>">								
							<?php $ayy = $cart_item["pricing_item_meta_data"];
								if(is_array($ayy)){
									echo apply_filters( 'woocommerce_cart_price', $pro_price, $cart_item_key );
								}else{
									echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
								}
						
							/*if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
								$product_price = get_option('woocommerce_tax_display_cart') == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

									echo apply_filters('woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
								} else {
									echo apply_filters( 'woocommerce_cart_item_price', $woocommerce->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
								} */?>
							</td>

							<td class="product-quantity" style="<?php echo $cell_style ?>">
								<?php
								if ( $_product->is_sold_individually() ) {
									echo '1';
								} /*else {
									
									$product_qty = array_values($cart_item['pricing_item_meta_data']);
									echo $product_qty[3];
									//echo $cart_item['quantity'];
									
								} */ 
								
								else if ( $cart_item['pricing_item_meta_data'] == '' ) {
								echo $cart_item['quantity'];
								} else {
									
									$product_qty = array_values($cart_item['pricing_item_meta_data']);
									echo $product_qty[3];
								
								} 
								?>
							</td>

							<td class="product-subtotal" style="<?php echo $cell_style ?>">
								<?php
									echo apply_filters( 'woocommerce_cart_item_subtotal', $woocommerce->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
								?>
							</td>
						</tr>
						<?php
					}
				} ?>
		
					
				<tr>
					<th style="text-align:left; <?php echo $cell_style; ?> <?php echo $total_style ?>"><?php echo __('Cart Subtotal', 'email-cart') ?></th>
					<td colspan="<?php echo $totals_colspan; ?>" style="text-align:right; <?php echo $cell_style; ?>">
						<span>
						<?php 
						if ( function_exists('wc_cart_totals_subtotal_html') ) {
							wc_cart_totals_subtotal_html();
						} else {
							echo $woocommerce->cart->get_cart_subtotal();
						} ?>
						</span>
					</td>
				</tr>
				<?php 
				if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
					// Show the tax row if showing prices exclusive of tax only
					if ( $woocommerce->cart->tax_display_cart == 'excl' ) {
						foreach ( $woocommerce->cart->get_tax_totals() as $code => $tax ) {
							echo '<tr class="tax-rate tax-rate-' . $code . '">
								<th>' . $tax->label . '</th>
								<td>' . $tax->formatted_amount . '</td>
							</tr>';
						}
					}
				} else {
					if ( $woocommerce->cart->tax_display_cart == 'excl' ) : ?>
					<?php if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) : ?>
						<?php foreach ( $woocommerce->cart->get_tax_totals() as $code => $tax ) : ?>
							<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
								<th style="text-align:left; <?php echo $cell_style; ?> <?php echo $total_style ?>"><?php echo esc_html( $tax->label ); ?></th>
								<td colspan="<?php echo $totals_colspan; ?>" style="text-align:right; <?php echo $cell_style; ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr class="tax-total">
							<th style="text-align:left; <?php echo $cell_style; ?> <?php echo $total_style ?>"><?php echo esc_html( $woocommerce->countries->tax_or_vat() ); ?></th>
							<td colspan="<?php echo $totals_colspan; ?>" style="text-align:right; <?php echo $cell_style; ?>"><?php echo wc_cart_totals_taxes_total_html(); ?></td>
						</tr>
					<?php endif; ?>
				<?php endif;
				} ?>
				
				<tr>
					<th style="text-align:left; <?php echo $cell_style; ?> <?php echo $total_style ?>"><?php echo __('Order Total', 'email-cart'); ?></th>
					<td colspan="<?php echo $totals_colspan; ?>" style="text-align:right; <?php echo $cell_style; ?>">
						<?php 
						if ( function_exists('wc_cart_totals_order_total_html') ) {
							echo wc_cart_totals_order_total_html();
						} else { ?>
							<strong><?php echo $woocommerce->cart->get_total(); ?></strong>
							<?php
							// If prices are tax inclusive, show taxes here
							if (  $woocommerce->cart->tax_display_cart == 'incl' ) {
								$tax_string_array = array();

								foreach ( $woocommerce->cart->get_tax_totals() as $code => $tax ) {
									$tax_string_array[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
								}

								if ( ! empty( $tax_string_array ) ) {
									echo '<small class="includes_tax">' . sprintf( __( '(Includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) ) . '</small>';
								}
							}
						} ?>
					</td>
				</tr>
					
				<!-- </tbody> -->
			</table>
			<?php
			$uniqId = date("ymd")."_".rand(0,99999);
			$product_items = ob_get_clean();			
			
			$checkout_url = get_home_url()."?email_cart_products=".$share_params["email_cart_products"];
			$checkout_url .= "&landing_page=".$landing_page."&unId=".$uniqId;
			$email_content = trim(nl2br($email_content));
			
			$message = str_replace(array("[product_items]", "[link]"), array($product_items, "<a href='".$checkout_url."' title='".__('Click here to view your cart.', 'email-cart')."'>".__('Click here to view your cart.', 'email-cart')."</a>"), $email_content);
			
			$subject = $email_subject;
			
			$from_address = get_option("email_cart_default_from");
			
			if ($from_address == "") {
				$from_address = get_option("woocommerce_email_from_address");
			}
			
			$headers[] = 'From: '.get_bloginfo("name").' <'.$from_address.'>';
			
			if ( isset($cc_email_address) && ( $cc_email_address != "" ) ) {
				$headers[] = 'Cc: '.$cc_email_address;
			}
			
			if ( isset($bcc_email_address) && ( $bcc_email_address != "" ) ) {
				$header_bcc = $bcc_email_address;
				$headers[] = 'Bcc: '.$header_bcc;
			}
			
			// elseif ( isset($send_a_copy) && ( $send_a_copy != '' ) ) {
			// 	$headers[] = 'Bcc: '.$send_a_copy;
			// }
			
			$headers = apply_filters( 'email_cart_headers', $headers );
			
			add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
			
			$email_body = $this->get_email_header_html();
			$email_body .= $message;
			$email_body .= $this->get_email_footer_html();
			
			$status = wp_mail( $to, $subject, $email_body, $headers );
			
			//insert cart data into database table			
			
			
			$product_variation_table_name = $wpdb->prefix . 'product_variations';
			
			$wpdb->insert( $product_variation_table_name, array( 'product_id' => $uniqId, 'user_email_id' => $to, 'data' => $final_cart_content, 'product_total' => '','created_on' => date('Y-m-d h:i:s') ) );
			// Send a Copy to Admin
			$send_a_copy = get_option( 'email_cart_send_a_copy' );
			if ( isset($send_a_copy) && ( $send_a_copy != '' ) ) {
				
				if (is_admin())	$email_source = __("the backend", 'email-cart');
				else			$email_source = __("the cart page", 'email-cart');
				
				$your_site_link = "<a href='" . get_home_url() . "'>" . get_bloginfo("name") . "</a>";
				
$copy_message_vsprintf = array();
$copy_message = __("
The following email was sent using Email Cart for WooCommerce from %s of your site %s:
<br>
", 'email-cart' );
$copy_message_vsprintf[] = $email_source;
$copy_message_vsprintf[] = $your_site_link;

$copy_message .= __("
To:		%s
<br>
", 'email-cart' );
$copy_message_vsprintf[] = ( is_array($to) ) ? implode($to,', ') : $to ;

$copy_message .= __("
From:	%s
<br>
", 'email-cart' );
$copy_message_vsprintf[] = $from_address;

if ( isset($cc_email_address) && ( $cc_email_address != "" ) ) {
$copy_message .= __("
Cc:		%s
<br>
", 'email-cart' );
$copy_message_vsprintf[] = $cc_email_address;
}

if ( isset($bcc_email_address) && ( $bcc_email_address != "" ) ) {
$copy_message .= __("
Bcc:	%s
<br>
", 'email-cart' );
$copy_message_vsprintf[] = $bcc_email_address;
}

$copy_message .= __("
<br>
-----------------------------------------
<br>
<br>
", 'email-cart' );
				
				$copy_message = vsprintf( $copy_message, $copy_message_vsprintf );
				$message = $copy_message . $message;
				
				$email_body = $this->get_email_header_html();
				$email_body .= $message;
				$email_body .= $this->get_email_footer_html();
				
				$status_admin = wp_mail( $send_a_copy, $subject, $email_body, $headers );
				
			}
			
			remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
			
			if ( isset( $_POST["is_cart_page"] ) && $_POST["is_cart_page"] == 1 ) {
				$woocommerce->add_message( __( 'Your cart was sent successfully.', 'email-cart' ) );
			}
			
		}
	}
	
	function get_email_header_html($email_heading="") {
		ob_start();
		
		( function_exists('wc_get_template') ) ? wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) ) : woocommerce_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );

		return ob_get_clean();
	}
	
	function get_email_footer_html() {
		ob_start();
		
		( function_exists('wc_get_template') ) ? wc_get_template( 'emails/email-footer.php' ) : woocommerce_get_template( 'emails/email-footer.php' );

		return ob_get_clean();
	}

	/**
	 * WP Mail Filter - Set email body as HTML
	 */
	public function set_html_content_type() {
		return 'text/html';
	}


	/**
	 * Add a submenu item to the WooCommerce menu
	 */
	public function admin_menu() {

		add_submenu_page( 'woocommerce',
						  __( 'Email Cart', 'email-cart' ),
						  __( 'Email Cart', 'email-cart' ),
						  'manage_woocommerce',
						  $this->id,
						  array( $this, 'admin_page' ) );

	}


	/**
	 * Include admin scripts
	 */
	public function admin_scripts() {
		global $woocommerce, $wp_scripts;
		 
		if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
			$this->load_legacy_admin_scrips();
		} else {
			$this->load_admin_scrips();
		}
	}
	
	public function load_admin_scrips() {
		global $woocommerce, $wp_query, $post;

		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		wp_enqueue_script( 'wc-admin-meta-boxes', $woocommerce->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'ajax-chosen', 'chosen', 'plupload-all' ), WC_VERSION );

    	wp_enqueue_script( 'woocommerce_admin' );
		wp_enqueue_script( 'iris' );
		wp_enqueue_script( 'ajax-chosen' );
		wp_enqueue_script( 'chosen' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		
		wp_enqueue_script( 'wc-admin-order-meta-boxes', $woocommerce->plugin_url() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js', array( 'wc-admin-meta-boxes' ), WC_VERSION );
		wp_enqueue_script( 'wc-admin-order-meta-boxes-modal', $woocommerce->plugin_url() . '/assets/js/admin/order-backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wc-admin-order-meta-boxes' ), WC_VERSION );

		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		
		$params = array(
			'plugin_url' 					=> $woocommerce->plugin_url(),
			'ajax_url' 						=> admin_url('admin-ajax.php'),
			'search_products_nonce' 		=> wp_create_nonce("search-products")
		);

		wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $params );
		
		wp_register_style( 'woocommerce-email_cart', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/css/ec-style.css', basename( __FILE__ ) ), '', self::VERSION, 'screen' );
		wp_enqueue_style( 'woocommerce-email_cart' );
		
		wp_register_script( 'woocommerce-email_cart', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/js/email-cart.js', basename( __FILE__ ) ), array('jquery'), self::VERSION );
		wp_enqueue_script( 'woocommerce-email_cart' );
		
		$woocommerce_email_cart_params = array(
			'remove_item_notice' 			=> __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'email-cart' ),
			'plugin_url' 					=> plugins_url( basename( plugin_dir_path( __FILE__ ) ) ),
			'ajax_url' 						=> admin_url('admin-ajax.php'),
			'order_item_nonce' 				=> wp_create_nonce("order-item"),
			'home_url'						=> get_home_url(),
			'version'						=> $woocommerce->version,
			'calc_totals_nonce' 			=> wp_create_nonce("calc-totals")
		);
		
		wp_localize_script( 'woocommerce-email_cart', 'woocommerce_email_cart_params', $woocommerce_email_cart_params );

	}
	
	public function load_legacy_admin_scrips() {
		global $woocommerce, $wp_scripts;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	
		// Register scripts
		wp_register_script( 'woocommerce_admin', $woocommerce->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-placeholder', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $woocommerce->version );
		wp_register_script( 'jquery-blockui', $woocommerce->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.60', true );
		wp_register_script( 'jquery-placeholder', $woocommerce->plugin_url() . '/assets/js/jquery-placeholder/jquery.placeholder' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
		wp_register_script( 'jquery-tiptip', $woocommerce->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
		wp_register_script( 'woocommerce_writepanel', $woocommerce->plugin_url() . '/assets/js/admin/write-panels'.$suffix.'.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'), $woocommerce->version );
		wp_register_script( 'ajax-chosen', $woocommerce->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery'.$suffix.'.js', array('jquery', 'chosen'), $woocommerce->version );
		wp_register_script( 'chosen', $woocommerce->plugin_url() . '/assets/js/chosen/chosen.jquery'.$suffix.'.js', array('jquery'), $woocommerce->version );
		
		
		wp_enqueue_script( 'woocommerce_writepanel' );
		wp_enqueue_script( 'woocommerce_admin' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_script( 'ajax-chosen' );
		wp_enqueue_script( 'chosen' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		
		$woocommerce_witepanel_params = array(
			'plugin_url' 					=> $woocommerce->plugin_url(),
			'ajax_url' 						=> admin_url('admin-ajax.php'),
			'search_products_nonce' 		=> wp_create_nonce("search-products")
		);
		wp_localize_script( 'woocommerce_writepanel', 'woocommerce_writepanel_params', $woocommerce_witepanel_params );
			
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		
		wp_register_style( 'woocommerce-email_cart', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/css/ec-style.css', basename( __FILE__ ) ), '', self::VERSION, 'screen' );
		wp_enqueue_style( 'woocommerce-email_cart' );
		
		wp_register_script( 'woocommerce-email_cart', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/js/email-cart.js', basename( __FILE__ ) ), array('jquery'), self::VERSION );
		wp_enqueue_script( 'woocommerce-email_cart' );
		
		$woocommerce_email_cart_params = array(
			'remove_item_notice' 			=> __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'email-cart' ),
			'plugin_url' 					=> plugins_url( basename( plugin_dir_path( __FILE__ ) ) ),
			'ajax_url' 						=> admin_url('admin-ajax.php'),
			'order_item_nonce' 				=> wp_create_nonce("order-item"),
			'home_url'						=> get_home_url(),
			'version'						=> $woocommerce->version,
			'calc_totals_nonce' 			=> wp_create_nonce("calc-totals")
		);
		
		wp_localize_script( 'woocommerce-email_cart', 'woocommerce_email_cart_params', $woocommerce_email_cart_params );
	}
	
	
	public function frontend_scripts() {
		global $woocommerce;
		
		if ( is_cart() ) {
			$woocommerce_email_cart_params = array(
				'plugin_url' 					=> $woocommerce->plugin_url(),
				'ajax_url' 						=> admin_url('admin-ajax.php'),
				'order_item_nonce' 				=> wp_create_nonce("order-item"),
				'home_url'						=> get_home_url()."/",
				'cart_url'						=> $woocommerce->cart->get_cart_url()
			);
			
			wp_enqueue_script( 'woocommerce-email_cart_frontent', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/js/email-cart-frontend.js', basename( __FILE__ ) ), array('jquery'), self::VERSION);
			wp_localize_script( 'woocommerce-email_cart_frontent', 'woocommerce_email_cart_params', $woocommerce_email_cart_params );
			
			wp_register_style( 'woocommerce-email_cart', plugins_url( basename( plugin_dir_path( __FILE__ ) ) . '/css/ec-style.css', basename( __FILE__ ) ), '', self::VERSION, 'screen' );
			wp_enqueue_style( 'woocommerce-email_cart' );
		
		}
		
	}


	/**
	 * Render the admin page
	 */
	public function admin_page() {
		
		global $woocommerce;
	
		$woocommerce->cart->empty_cart();
		
		$action = 'admin.php?page=woocommerce_email_cart&amp;cart_sent=1'; ?>
		<form enctype="multipart/form-data" id="cart-sent-form" method="post" action="<?php echo esc_attr( $action ); ?>">
		<div id="poststuff" class="woocommerce-email-cart-wrap">
			<div class="wrap woocommerce woocommerce-email-cart">
				
				<!-- <div class="icon32" id="icon-woocommerce-email-cart"><br></div> -->
				
				<a class="email-cart-settings" href="admin.php?page=email_cart_settings">
					<span class="dashicons dashicons-admin-generic"></span> Settings
				</a>
				
				<h2><?php _e( 'Email Cart', 'email-cart' ); ?></h2>
				<?php
				if ( ! empty( $_GET['cart_sent'] ) ) {
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your cart was sent successfully.', 'email-cart' ) . '</strong></p></div>';
				}
				
				$this->admin_cart_page(); ?>
			</div>
		</div>
		<?php 
		( function_exists('wp_verify_nonce') ) ? wp_verify_nonce( 'email-cart-sent' ) : $woocommerce->nonce_field('email-cart-sent'); ?>
		</form>
		<?php
		
	}


	/**
	 * Render the body of the admin starting page
	 */
	private function admin_cart_page() {
		global $woocommerce;
		/*
		$email_content_unedited = __("Hi There,

Please find a link below to your cart on our online store where your items have already been added.

Here is what you will find in your cart:
[product_items]

You can now easily checkout and pay, or, continue browsing and adding other items to your cart

If you are a new customer you may need to register your details to create a new account. If you are returning, please just log in to finish your checkout.

[link]

Thanks!

The %s Team", 'email-cart');
		$email_content = sprintf( $email_content_unedited, get_bloginfo("name") );*/
		$email_content = get_option("email_cart_default_backend_text");
		?>
		
		<div id="woocommerce-order-items" class="postbox " >
			
			<div class="handlediv" title="Click to toggle">
				<br />
			</div>
			
			<h3 class='hndle'>
				<span><strong><?php _e( 'Step 1:', 'email-cart' ); ?></strong> Cart</span>
			</h3>
			
			<div class="inside">
				
				<table class="email-cart-table">
					<tbody>
						
						<tr>
							<td class="label">
								<label for="post_type">
									<?php _e( 'Add Products to your Cart', 'email-cart' ); ?>
								</label>
								<p class="description">
									<?php _e( 'Search and add products to email the cart to a customer. They can then log-in or register to checkout and pay via the existing store settings.', 'email-cart' ); ?>
								</p>
							</td>
							<td>
									
								<div class="email-cart-toggle">
									
									<div class="woocommerce_order_items_wrapper">
										<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
											<thead>
												<tr>
													<th>&nbsp;</th>
													<th class="item" colspan="2"><?php _e( 'Item', 'email-cart' ); ?></th>
								
													<?php do_action( 'woocommerce_admin_order_item_headers' ); ?>
								
													<th class="price"><?php _e( 'Price', 'email-cart' ); ?></th>
								
													<th class="quantity"><?php _e( 'Qty', 'email-cart' ); ?></th>
								
													<th class="line_cost"><?php _e( 'Total', 'email-cart' ); ?>&nbsp;<a class="tips" data-tip="<?php _e( 'Line subtotals are before pre-tax discounts, totals are after.', 'email-cart' ); ?>" href="#">[?]</a></th>
								
												</tr>
											</thead>
											<tbody id="order_items_list"></tbody>
										</table>
									</div>
								
									<p class="update_action">
										<button type="button" class="button" name="update_cart" id="update_cart">Update Cart</button>
									</p>
								
									<p class="add_items">
										<select id="add_item_id" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'email-cart' ); ?>" style="width: 400px"></select>								
										<button type="button" class="button add_cart_item"><?php _e( 'Add item(s)', 'email-cart' ); ?></button>
									</p>
									<div class="clear"></div>
								
								</div>
									
							</td>
						</tr>
						
						<tr>
							<td class="label">
								<label><?php _e( 'Landing Page', 'email-cart' ); ?></label>
								<p class="description"><?php _e( 'Select whether to direct users to the Cart or the Checkout Page', 'email-cart' ); ?></p>
							</td>
							<td>
								<div class="form-field landing_page">
									<select name="landing_page" id="landing_page" class="email-cart-select">
										<option value="cart"><?php _e( 'Cart', 'email-cart' ); ?></option>
										<option value="checkout"><?php _e( 'Checkout', 'email-cart' ); ?></option>
									</select>
								</div>
							</td>
						</tr>
						
					</tbody>
				</table>
				
			</div>
		</div>
		
		
		
		<div id="woocommerce-order-items" class="postbox " >
			
			<div class="handlediv" title="Click to toggle">
				<br />
			</div>
			
			<h3 class='hndle'>
				<span><strong><?php _e( 'Step 2:', 'email-cart' ); ?></strong> <?php _e( 'Email', 'email-cart' ); ?></span>
			</h3>
			
			<div class="inside">
				
				<table class="email-cart-table">
					<tbody>
						
						<tr>
							<td class="label">
								<label><?php _e( 'Email Address', 'email-cart' ); ?></label>
								<p class="description"><?php _e( 'Enter the email address you would like to send this cart to. (Separate multiple addresses with a comma)', 'email-cart' ); ?></p>
							</td>
							<td>
								<div class="form-field email_address">
									<input type="text" name="email_address" id="email_address" value="" />
								</div>
							</td>
						</tr>
						<?php
						if (get_option( 'email_cart_show_cc_field_back' ) == 'yes' ) { ?>
						<tr>
							<td class="label">
								<label><?php _e( 'CC Email Address', 'email-cart' ); ?></label>
								<p class="description"><?php _e( 'Enter the email address you would like to CC on this cart email. (Separate multiple addresses with a comma)', 'email-cart' ); ?></p>
							</td>
							<td>
								<div class="form-field email_address">
									<input type="text" name="cc_email_address" id="cc_email_address" value="" />
								</div>
							</td>
						</tr>
						<?php
						}
						if (get_option( 'email_cart_show_bcc_field_back' ) == 'yes' ) {
							?>
							<tr>
								<td class="label">
									<label><?php _e( 'BCC Email Address', 'email-cart' ); ?></label>
									<p class="description"><?php _e( 'Enter the email address you would like to BCC on this cart email. (Separate multiple addresses with a comma)', 'email-cart' ); ?></p>
								</td>
								<td>
									<div class="form-field email_address">
										<input type="text" name="bcc_email_address" id="bcc_email_address" value="" />
									</div>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td class="label">
								<label><?php _e( 'Email Subject', 'email-cart' ); ?></label>
							</td>
							<td>
								<div class="form-field email_subject">
									<input type="text" name="email_subject" id="email_subject" value="<?php printf( __( 'Shopping Cart from %s', 'email-cart' ), get_bloginfo("name") ); ?>" />
								</div>
							</td>
						</tr>
						
						<tr>
							<td class="label">
								<label><?php _e( 'Email Content', 'email-cart' ); ?></label>
								<p class="description"><?php _e( 'Edit the email message. [Shortcodes] will replaced will relevant content.', 'email-cart' ); ?></p>
							</td>
							<td>
								<div class="form-field">
									<textarea name="email_content" id="email_content" rows="18" cols="20"><?php echo $email_content; ?></textarea>
								</div>
							</td>
						</tr>
						
						<tr>
							<td class="label">
								<p class="description"><?php _e("When you've finished adding products and preparing your email, then send your cart.", 'email-cart' ); ?></p>
							</td>
							<td>
								<input type="submit" class="button button-primary submit-button" name="submit" id="submit" value="<?php _e( 'Send Cart', 'email-cart' ); ?>" />
							</td>
						</tr>
						
					</tbody>
				</table>
				
			</div>
		</div>
		
		
		<div id="woocommerce-order-items" class="postbox " >
			
			<div class="handlediv" title="Click to toggle">
				<br />
			</div>
			
			<h3 class='hndle'>
				<span><?php _e( 'Cart URL', 'email-cart' ); ?></span>
			</h3>
			
			<div class="inside">
				
				<table class="email-cart-table">
					<tbody>
						
						<tr>
							<td class="label">
								<p class="description"><?php _e( 'Alternatively copy this link to share via any other app (Email Newsletter, Social Network, etc).', 'email-cart' ); ?></p>
							</td>
							<td>
								<div class="form-field">
									<input type="text" name="share_link" id="share_link" value="" readonly />
								</div>
							</td>
						</tr>
						
					</tbody>
				</table>
				
			</div>
		</div>
		
		<?php
	}
	

	function add_order_email_cart_setting( $settings ) {
		
		$settings[] =array(

			'name' => __( 'Email Cart', 'email-cart' ),

			'type' => 'title',

			'desc' => __('Complete the following fields to setup your Email Cart', 'email-cart'),

			'id' => 'email_cart_title'

		);
		
		$settings[] = array(

			'name'    => __( 'Show on Front-End Cart', 'email-cart' ),

			'desc'    => __( 'Show a button to allow users to email their cart from the front-end cart page', 'email-cart' ),

			'id'      => 'email_cart_show_on_cart_page',

			'std'     => 'no', // WooCommerce < 2.0

			'default' => 'no', // WooCommerce >= 2.0

			'type'    => 'checkbox'

		);
		
		$settings[] = array(

			'name'    => __( 'Show CC Email Field ', 'email-cart' ),

			'desc'    => __( 'Back-end', 'email-cart' ),

			'id'      => 'email_cart_show_cc_field_back',

			'std'     => 'no', // WooCommerce < 2.0

			'default' => 'no', // WooCommerce >= 2.0
					
			'checkboxgroup'		=> 'start',

			'type'    => 'checkbox'

		);
		
		$settings[] = array(

			'desc'    => __( 'Front-end', 'email-cart' ),
			
			'desc_tip'	=> __( 'Display a field for users to add CC email addresses to the email', 'email-cart' ),

			'id'      => 'email_cart_show_cc_field_front',

			'std'     => 'no', // WooCommerce < 2.0

			'default' => 'no', // WooCommerce >= 2.0
					
			'checkboxgroup'		=> 'end',

			'type'    => 'checkbox'

		);
		
		$settings[] = array(

			'name'    => __( 'Show BCC Email Field', 'email-cart' ),

			'id'      => 'email_cart_show_bcc_field_back',

			'desc'    => __( 'Back-end', 'email-cart' ),

			'std'     => 'no', // WooCommerce < 2.0

			'default' => 'no', // WooCommerce >= 2.0
					
			'checkboxgroup'		=> 'start',

			'type'    => 'checkbox'

		);
		
		$settings[] = array(

			'desc'    => __( 'Front-end', 'email-cart' ),

			'desc_tip'    => __( 'Display a field for users to add BCC email addresses to the email', 'email-cart' ),

			'id'      => 'email_cart_show_bcc_field_front',

			'std'     => 'no', // WooCommerce < 2.0

			'default' => 'no', // WooCommerce >= 2.0
					
			'checkboxgroup'		=> 'end',

			'type'    => 'checkbox'

		);
		
		$settings[] = array(
			
			'name' => __( 'Always Send a Copy', 'email-cart' ),
			
			'desc' 		=> __( 'Add addresses that will receive a copy of the emailed cart on each send. Add multiple separated by comma', 'email-cart' ),
			
			'id' 		=> 'email_cart_send_a_copy',
			
			'type' 		=> 'text',
			
			'css' 		=> 'min-width:300px;',
			
			'autoload'      => false
			
		);
		
		$settings[] =array(

			'type' => 'sectionend',

			'id' => 'email_cart_title'

		);
		
		return $settings;
		
	}

	
	/**
	* Add order item via ajax
	*
	* @access public
	* @return void
	*/
	function woocommerce_ajax_add_email_cart_item() {
		global $woocommerce, $wpdb;

		check_ajax_referer( 'order-item', 'security' );

		$empty_cart = ( isset( $_POST['empty_cart'] ) ) ? $_POST['empty_cart'] : false;
		if ( $empty_cart ) {
			$woocommerce->cart->empty_cart();
		}

		$items_to_add = ( isset( $_POST['items_to_add'] ) ) ? $_POST['items_to_add'] : null;
		
		if ( is_array( $items_to_add ) ) {
			
			foreach ($items_to_add as $item_to_add) {
				
				// Find the item
				if ( ! is_numeric( $item_to_add ) )
				   continue;

				$post = get_post( $item_to_add );

				if ( ! $post || ( $post->post_type !== 'product' && $post->post_type !== 'product_variation' ) )
				   die();

				$_product = get_product( $post->ID );

				$this->add_ajax_product_to_cart($_product->id, $post->ID);
				
			}
			
			$item_count = 0;
			
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$legacy_product_data = $cart_item['data'];
					$item = array();

					$item['product_id'] 			= $legacy_product_data->id;
					$item['variation_id'] 			= isset( $legacy_product_data->variation_id ) ? $legacy_product_data->variation_id : '';
					$item['name'] 					= $legacy_product_data->get_title();
					$item['tax_class']				= $legacy_product_data->get_tax_class();
					$item['qty'] 					= $product_qty[3];
					$item['line_subtotal'] 			= number_format( (double) $legacy_product_data->get_price_excluding_tax(), 2, '.', '' );
					$item['line_subtotal_tax'] 		= '';
					$item['line_total'] 			= number_format( (double) $legacy_product_data->get_price_excluding_tax(), 2, '.', '' );
					$item['line_tax'] 				= '';
					
					if ($item['qty'] > 1) {
						$link_products += $item['qty'] + ".";
					}
					
					?>
					<tr class="item">
						
						<td class="product-remove">
							<?php
								echo sprintf( '<a href="javascript:;" class="remove dashicons dashicons-no" title="%s"></a>', __( 'Remove this item', 'woocommerce' ) );
							?>
						</td>
						
						<td class="product-thumbnail">
							<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

								if ( ! $_product->is_visible() ) {
									echo $thumbnail;
								} else {
									if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
										printf('<a href="%s">%s</a>', esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $cart_item['product_id'] ) ) ), $thumbnail );
									} else {
										printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
									}
								}
							?>
						</td>
						
						<td class="product-name">
							<input type="hidden" class="order_item_id" name="order_item_id[<?php echo absint( $item_count  ); ?>]" value="<?php echo esc_attr( $item['product_id']  ); ?>" />
							<input type="hidden" class="order_item_variation_id" name="order_item_variation_id[<?php echo absint( $item_count  ); ?>]" value="<?php echo esc_attr( $item['variation_id']  ); ?>" />
							<?php
							if ( isset( $cart_item["variation"] ) && ( is_array($cart_item["variation"]) ) ) {
								$attribute_count = 0;
								$attributes = "(";
									
								foreach ( $cart_item["variation"] as $key => $value ) {
									if ( substr(sanitize_title( $key ), 0, 10) != "attribute_" ) {
										$key = 'attribute_' . sanitize_title( $key );
									}
									$value = sanitize_title( trim( stripslashes( $value ) ) );
									
									if ($attribute_count == 0) {
										$attributes .= $key . "=" . $value;
									} else {
										$attributes .= "+" . $key . "=" . $value;
									}
									?>
									<input type="hidden" class="order_item_variation_attribute_name" name="order_item_variation_attribute_name[<?php echo absint( $item_count  ); ?>][<?php echo $attribute_count; ?>]" value="<?php echo esc_attr( $key ); ?>" />
									<input type="hidden" class="order_item_variation_attribute_value" name="order_item_variation_attribute_value[<?php echo absint( $item_count  ); ?>][<?php echo $attribute_count; ?>]" value="<?php echo esc_attr( $value ); ?>" />
									<?php
									$attribute_count++;
								}
								$attributes .= ")";
								$link_products .= $item['product_id'] . "_" . $item['variation_id'] . $attributes . ","; ?>
								<input type="hidden" class="order_item_variation_text" name="order_item_variation_text[<?php echo absint( $item_count  ); ?>]" value="<?php echo woocommerce_get_formatted_variation( $_product->variation_data, true ); ?>" />
							<?php
							} else {
								$link_products .= $item['product_id'] . "_" . $item['variation_id'] . ",";
							} ?>
							<input type="hidden" class="order_item_name" name="order_item_name[<?php echo absint( $item_count  ); ?>]" value="<?php echo $item['name']; ?>" />
							<input type="hidden" class="order_item_count" name="order_item_count[]" value="<?php echo $item_count; ?>" />
							<input type="hidden" class="item-count" name="item-count" value="<?php echo $item_count; ?>" /> 
							<input type="hidden" name="order_item_qty[<?php echo absint( $item_count  ); ?>]" placeholder="0" value="<?php echo esc_attr( $item['qty'] ); ?>" size="4" class="quantity item_quantity" />
							<input type="hidden" name="line_total[<?php echo absint( $item_count  ); ?>]" value="<?php if ( isset( $item['line_total'] ) ) echo esc_attr( $item['line_total'] ); ?>" class="line_total" readonly />
							
							<?php
								if ( ! $_product->is_visible() ) {
									echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
								} else {
									if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
										printf('<a href="%s">%s</a>', esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $cart_item['product_id'] ) ) ), apply_filters('woocommerce_in_cart_product_title', $_product->get_title(), $cart_item, $cart_item_key ) );
									} else {
										echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );
									}
								}
								// Meta data
								echo $woocommerce->cart->get_item_data( $cart_item );

	               				// Backorder notification
	               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
	               					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
							?>
						</td>

						<td class="product-price">
							<?php
							if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
								$product_price = get_option('woocommerce_tax_display_cart') == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

								echo apply_filters('woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
							} else {
								echo apply_filters( 'woocommerce_cart_item_price', $woocommerce->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							} ?>
						</td>

						<td class="product-quantity">
							<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input( array(
										'input_name'  => "cart[{$cart_item_key}][qty]",
										'input_value' => $product_qty[3],
										'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
										'min_value'   => '0'
									), $_product, false );
								}

								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
							?>
						</td>

						<td class="product-subtotal">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', $woocommerce->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
							?>
						</td>
					</tr>
					<?php
					$item_count++;
				}
			} 
			
		}
		wc_clear_notices();
		// Quit out
		die();
	}
	
	
	public function add_ajax_product_to_cart( $product_id, $variation_id='' ) {
		
		global $woocommerce;
		
		$was_added_to_cart   = false;
		$added_to_cart       = array();
		$adding_to_cart      = ( function_exists('wc_get_product') ) ? wc_get_product( $product_id ) : get_product( $product_id );
		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->product_type, $adding_to_cart );

		// Variable product handling
		if ( 'variable' === $add_to_cart_handler ) {

			$quantity           = 1;
			$all_variations_set = true;
			$variations         = array();

			// Only allow integer variation ID - if its not set, redirect to the product page
			if ( empty( $variation_id ) ) {
				wc_add_notice( __( 'Please choose product options&hellip;', 'woocommerce' ), 'error' );
				return;
			}

			$attributes = $adding_to_cart->get_attributes();
			$variation  = ( function_exists('wc_get_product') ) ? wc_get_product( $variation_id ) : get_product( $variation_id );

			// Verify all attributes
			foreach ( $attributes as $attribute ) {
				if ( ! $attribute['is_variation'] ) {
					continue;
				}

				$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

				if ( ! empty( $variation->variation_data[ $taxonomy ] ) ) {

					// Don't use woocommerce_clean as it destroys sanitized characters
					$value = sanitize_title( trim( stripslashes( $variation->variation_data[ $taxonomy ] ) ) );

					// Get valid value from variation
					$valid_value = $variation->variation_data[ $taxonomy ];

					// Allow if valid
					if ( $valid_value == '' || $valid_value == $value ) {
						if ( $attribute['is_taxonomy'] ) {
							$variations[ $taxonomy ] = $value;
						}
						else {
							// For custom attributes, get the name from the slug
							$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							foreach ( $options as $option ) {
								if ( sanitize_title( $option ) == $value ) {
									$value = $option;
									break;
								}
							}
							 $variations[ $taxonomy ] = $value;
						}
						continue;
					}

				}

				$all_variations_set = false;
			}
			if ( $all_variations_set ) {
				// Add to cart validation
				$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

				if ( $passed_validation ) {
					if ( $woocommerce->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
						( function_exists('wc_add_to_cart_message') ) ? wc_add_to_cart_message( $product_id ) : woocommerce_add_to_cart_message( $product_id );
						$was_added_to_cart = true;
						$added_to_cart[] = $product_id;
					}
				}
			} else {
				wc_add_notice( __( 'Please choose product options&hellip;', 'woocommerce' ), 'error' );
				return;
			}

		// Grouped Products
		} elseif ( 'grouped' === $add_to_cart_handler ) {

			if ( ! empty( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ) {

				$quantity_set = false;

				foreach ( $_REQUEST['quantity'] as $item => $quantity ) {
					if ( $quantity <= 0 ) {
						continue;
					}

					$quantity_set = true;

					// Add to cart validation
					$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

					if ( $passed_validation ) {
						if ( $woocommerce->cart->add_to_cart( $item, $quantity ) ) {
							$was_added_to_cart = true;
							$added_to_cart[] = $item;
						}
					}
				}

				if ( $was_added_to_cart ) {
					( function_exists('wc_add_to_cart_message') ) ? wc_add_to_cart_message( $added_to_cart ) : woocommerce_add_to_cart_message( $added_to_cart );
				}

				if ( ! $was_added_to_cart && ! $quantity_set ) {
					wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'woocommerce' ), 'error' );
					return;
				}

			} elseif ( $product_id ) {

				/* Link on product archives */
				wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'woocommerce' ), 'error' );
				return;

			}

		// Simple Products
		} else {

			$quantity 			= empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );

			// Add to cart validation
			$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

			if ( $passed_validation ) {
				// Add the product to the cart
				if ( $woocommerce->cart->add_to_cart( $product_id, $quantity ) ) {
					( function_exists('wc_add_to_cart_message') ) ? wc_add_to_cart_message( $product_id ) : woocommerce_add_to_cart_message( $product_id );
					$was_added_to_cart = true;
					$added_to_cart[] = $product_id;
				}
			}

		}
		
	}

	public function cart_page_call_to_action() {
		?>
		<div class="email-cart-button-holder">
			<a class="email-cart-button email-cart-dropdown-btn button" href="javascript:;" id="email_cart_dropdown_btn"><?php _e( 'Email this Cart', 'email-cart' ); ?> →</a>
		</div>
		<?php
	}

	public function cart_page_load_form() {
		
		global $woocommerce;
		
		if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
			$item_count = 0;
			?>
			
			<div class="email-cart">
				
				<form class="email-cart-frontend-form" action="<?php echo esc_url( add_query_arg( array( 'cart_sent' => 1 ),  $woocommerce->cart->get_cart_url() ) ); ?>" method="post">
					<div class="email-cart-frontend">
						
						<div class="email-cart-close"><?php _e( 'Close', 'email-cart' ); ?></div>
						
						<input type="hidden" name="is_cart_page" value="1" />
									
						<?php
						$link_products = "";
						//echo '<pre>';print_R($woocommerce->cart->get_cart());
						//echo '</pre>';
						foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
							$_all_product_data = $values;
							$_product = $values['data'];
							if ( $_product->exists() && $values['quantity'] > 0 ) {
								
								$item = array();

								$item['product_id'] 			= $_product->id;
								$item['variation_id'] 			= isset( $_product->variation_id ) ? $_product->variation_id : '';
								$item['name'] 					= $_product->get_title();
								$item['tax_class']				= $_product->get_tax_class();
								$item['qty'] 					= $values['quantity'];
								$item['line_subtotal'] 			= number_format( (double) $_product->get_price_excluding_tax(), 2, '.', '' );
								$item['line_subtotal_tax'] 		= '';
								$item['line_total'] 			= number_format( (double) $_product->get_price_excluding_tax(), 2, '.', '' );
								$item['line_tax'] 				= '';
								
								if ($item['qty'] > 1) {
									$link_products += $item['qty'] + ".";
								} ?>
								
								<div class="item" data-order_item_id="<?php echo $item['product_id'] ; ?>">
									<input type="hidden" class="order_item_id" name="order_item_id[<?php echo absint( $item_count  ); ?>]" value="<?php echo esc_attr( $item['product_id']  ); ?>" />
									<input type="hidden" class="order_item_variation_id" name="order_item_variation_id[<?php echo absint( $item_count  ); ?>]" value="<?php echo esc_attr( $item['variation_id']  ); ?>" />
									<?php
									if ( isset( $_all_product_data["variation"] ) && ( is_array($_all_product_data["variation"]) ) ) {
										$attribute_count = 0;
										$attributes = "(";
											
										foreach ( $_all_product_data["variation"] as $key => $value ) {
											if ( substr(sanitize_title( $key ), 0, 10) != "attribute_" ) {
												$key = 'attribute_' . sanitize_title( $key );
											}
											$value = sanitize_title( trim( stripslashes( $value ) ) );
											
											if ($attribute_count == 0) {
												$attributes .= $key . "=" . $value;
											} else {
												$attributes .= "+" . $key . "=" . $value;
											}
											?>
											<input type="hidden" class="order_item_variation_attribute_name" name="order_item_variation_attribute_name[<?php echo absint( $item_count  ); ?>][<?php echo $attribute_count; ?>]" value="<?php echo esc_attr( $key ); ?>" />
											<input type="hidden" class="order_item_variation_attribute_value" name="order_item_variation_attribute_value[<?php echo absint( $item_count  ); ?>][<?php echo $attribute_count; ?>]" value="<?php echo esc_attr( $value ); ?>" />
											<?php
											$attribute_count++;
										}
										$attributes .= ")";
										$link_products .= $item['product_id'] . "_" . $item['variation_id'] . $attributes . ","; ?>
										<input type="hidden" class="order_item_variation_text" name="order_item_variation_text[<?php echo absint( $item_count  ); ?>]" value="<?php echo woocommerce_get_formatted_variation( $_product->variation_data, true ); ?>" />
									<?php
									} else {
										$link_products .= $item['product_id'] . "_" . $item['variation_id'] . ",";
									} ?>
									<input type="hidden" class="order_item_name" name="order_item_name[<?php echo absint( $item_count  ); ?>]" value="<?php echo $item['name']; ?>" />
									<input type="hidden" class="order_item_count" name="order_item_count[]" value="<?php echo $item_count; ?>" />
									<input type="hidden" class="item-count" name="item-count" value="<?php echo $item_count; ?>" /> 
									<input type="hidden" name="order_item_qty[<?php echo absint( $item_count  ); ?>]" placeholder="0" value="<?php echo esc_attr( $item['qty'] ); ?>" size="4" class="quantity item_quantity" />
									<input type="hidden" name="line_total[<?php echo absint( $item_count  ); ?>]" value="<?php if ( isset( $item['line_total'] ) ) echo esc_attr( $item['line_total'] ); ?>" class="line_total" readonly />
								</div>
								<?php
								$item_count++;
							}
						} ?>
						
					</div>
					<?php
					( function_exists('wp_verify_nonce') ) ? wp_verify_nonce( 'email-cart-sent' ) : $woocommerce->nonce_field('email-cart-sent'); ?>
				
					<?php
					/*
					$email_content_unedited = __("Hi,

I’ve created a cart for you on %s. Click the link below to view it.
Here is what it contains:
[product_items]

You can now easily edit it, checkout and pay, or, browse around the store and continue adding other items to your cart.

If you are a new customer you may need to register your details to create a new account to checkout. If you are returning, just log in to finish your checkout.

[link]

Thanks!

The %s Team", 'email-cart');
					
					$email_content = sprintf( $email_content_unedited, get_bloginfo("name"), get_bloginfo("name") );*/
					$email_content = get_option("email_cart_default_frontend_text");
					?>
				
							

					<div class="email-cart-img email-cart-img-top"></div>
				
					<h3 class='email-cart-heading'><?php _e( 'Email this Cart', 'email-cart' ); ?></h3>
					
					<div class="email-cart-row email-cart-row-email_address">
						<input class="email-cart-input-text" type="text" name="email_address" id="email_address" placeholder="<?php _e( "Email Address(es)", 'email-cart' ); ?>" value="" />
						<div class="email-cart-description"><?php _e( 'Separate more than one with a comma (,)', 'email-cart' ); ?></div>
					</div>
					
					<?php
					if (get_option( 'email_cart_show_cc_field_front' ) == 'yes' ) { ?>
					<div class="email-cart-row email-cart-row-email_address">
						<input class="email-cart-input-text" type="text" name="cc_email_address" id="cc_email_address" placeholder="<?php _e( "CC Email Address(es)", 'email-cart' ); ?>" value="" />
						<div class="email-cart-description"><?php _e( 'Separate more than one with a comma (,)', 'email-cart' ); ?></div>
					</div>
					<?php
					}
					
					if (get_option( 'email_cart_show_bcc_field_front' ) == 'yes' ) { ?>
					<div class="email-cart-row email-cart-row-email_address">
						<input class="email-cart-input-text" type="text" name="bcc_email_address" id="bcc_email_address" placeholder="<?php _e( "BCC Email Address(es)", 'email-cart' ); ?>" value="" />
						<div class="email-cart-description"><?php _e( 'Separate more than one with a comma (,)', 'email-cart' ); ?></div>
					</div>
					<?php
					} ?>
					
					<div class="email-cart-row">
						<input class="email-cart-input-text" type="text" name="email_subject" id="email_subject" value="<?php printf( __( 'View my Cart on Creation Station Printing and Design %s', 'email-cart' ), get_bloginfo("name") ); ?>" />
						<div class="email-cart-description"><?php _e( 'Edit the email subject line above if you like', 'email-cart' ); ?></div>
					</div>
					
					<?php
					if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
					?>
						<div class="email-cart-row">
							<select name="landing_page" id="landing_page" >
								<option value="cart"><?php _e( 'Cart', 'email-cart' ); ?></option>
								<option value="checkout"><?php _e( 'Checkout', 'email-cart' ); ?></option>
							</select>
							<div class="email-cart-description"><?php _e( 'Choose which page the user will land. Only visible to Admin level user\'s. That\'s you!', 'email-cart' ); ?></div>
						</div>
					<?php
					} else {
					?>
						<input type="hidden" name="landing_page" id="landing_page" value="cart" />
					<?php
					} ?> 
					
					<div class="email-cart-row">
						<textarea name="email_content" id="email_content" rows="18" cols="20"><?php echo $email_content; ?></textarea>
						<div class="email-cart-description"><?php _e( 'Edit the email content above as you wish. The [placeholder] items will add the appropriate content when the email is sent to your recipients, so make sure you keep them!', 'email-cart' ); ?></div>
					</div>
					
					<div class="email-cart-row email-cart-submit-block">
						<input type="submit" class="checkout-button button alt" name="email_cart_submit" id="email_cart_submit" value="<?php _e( 'Email Cart', 'email-cart' ); ?>" />
					</div>
					
					
					<hr/>
								
					
					<h4 class='email-cart-heading'><?php _e( 'or Share the Link Anywhere!', 'email-cart' ); ?></h4>
									
					<div class="email-cart-row">
						<input class="email-cart-input-text" type="text" name="share_link" id="share_link" value="" readonly />
						<div class="email-cart-description"><?php _e( 'Just copy the link below and paste into any other place you like!', 'email-cart' ); ?></div>
					</div>
					
					<div class="email-cart-img email-cart-img-bottom"></div>
						   
				</form>
				
			</div>
		<?php
		
		}
	}
} // class WC_Email_Cart