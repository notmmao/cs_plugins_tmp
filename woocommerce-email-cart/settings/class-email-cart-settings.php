<?php
/**
 * WooCommerce Email Cart Settings
 *
 * @author 		cxThemes
 * @category 	Settings
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email_Cart_Settings' ) ) :

/**
 * WC_Email_Cart_Settings
 */
class WC_Email_Cart_Settings {
	
	protected $id    = '';
	protected $label = '';
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'email_cart_settings';
		$this->label = __( 'Email Cart Settings', 'email-cart' );
		
		// add the menu itme
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'woocommerce_settings_email_cart_settings', array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_email_cart_settings', array( $this, 'save' ) );

	}
	
	/**
	 * Add a submenu item to the WooCommerce menu
	 */
	public function admin_menu() {

		add_submenu_page( 'options-general.php', 
						  __("Email Cart",'email-cart'),
						  __("Email Cart",'email-cart'),
						  'manage_woocommerce', 
						  $this->id,
						  array( $this, 'admin_page' ) );
		
	}
	
	public function admin_page() {
		
		global $woocommerce;
		
		// Save settings if data has been posted
		if ( ! empty( $_POST ) )
			$this->save();

		// Add any posted messages
		if ( ! empty( $_GET['wc_error'] ) )
			//self::add_error( stripslashes( $_GET['wc_error'] ) );

		 if ( ! empty( $_GET['wc_message'] ) )
			//self::add_message( stripslashes( $_GET['wc_message'] ) );

		//self::show_messages();
		?>
		
		<form method="post" id="mainform" action="" enctype="multipart/form-data">
			<div id="poststuff" class="woocommerce-email-cart-wrap">
				<div class="wrap woocommerce woocommerce-email-cart">
				
					<!-- <div class="icon32" id="icon-woocommerce-email-cart"><br></div> -->
					
					<a class="email-cart-back" href="admin.php?page=woocommerce_email_cart"><span class="dashicons dashicons-arrow-left"></span> Back</a>
					
					<h2><?php _e( 'Email Cart', 'email-cart' ); ?><span class="dashicons dashicons-arrow-right"></span><?php _e( 'Settings', 'email-cart' ); ?></h2>
					
					<?php
					$settings = $this->get_settings();
					
					if (class_exists('WC_Admin_Settings') ){
						// > 2.1 and above
						WC_Admin_Settings::output_fields( $settings );
					}
					else{
						// below 2.1
						include( $woocommerce->plugin_path() . '/admin/woocommerce-admin-settings.php' );
						woocommerce_admin_fields( $settings );
					}
					?>
					
					<p class="submit">
						<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'email-cart' ); ?>" />
						<?php wp_nonce_field( 'woocommerce-settings' ); ?>
					</p>
				
				</div>
			</div>
		</form>
		
		<?php
	}
	
	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public static function get_settings() {
		
		$settings = array();
		
		$settings[] =array(

			'type' => 'title',

			// 'desc' => __('Complete the following fields to setup your Email Cart', 'email-cart'),

			'id' => 'email_cart_title'

		);
		
		$email_content_unedited = __("Hi There,

Please find a link below to your cart on our online store where your items have already been added.

Here is what you will find in your cart:
[product_items]

You can now easily checkout and pay, or, continue browsing and adding other items to your cart

If you are a new customer you may need to register your details to create a new account. If you are returning, please just log in to finish your checkout.

[link]

Thanks!

The %s Team", 'email-cart');
		$email_content = sprintf( $email_content_unedited, get_bloginfo("name") );
		$settings[] = array(
			
			'name' => __( 'Default Backend Email Template', 'email-cart' ),
			
			'desc' 		=> __( 'This is the default text to be editable in each Email Cart created on the backend.', 'email-cart' ) . '<br />' . __('Please do no remove any text containing [square brackets] as this will be populated with the cart information.', 'email-cart' ),
			
			'id' 		=> 'email_cart_default_backend_text',
			
			'type' 		=> 'textarea',
			
			'default'	=> $email_content,
			
			'css' 		=> 'height: 300px;',
			
			'autoload'      => false
			
		);
		
		$email_content_unedited = __("Hi,

I’ve created a cart for you on %s. Click the link below to view it. 
Here is what it contains:
[product_items]

You can now easily edit it, checkout and pay, or, browse around the store and continue adding other items to your cart. 

If you are a new customer you may need to register your details to create a new account to checkout. If you are returning, just log in to finish your checkout.

[link]

Thanks!

The %s Team", 'email-cart');
					
		$email_content = sprintf( $email_content_unedited, get_bloginfo("name"), get_bloginfo("name") );
		$settings[] = array(
			
			'name' => __( 'Default Frontend Email Template', 'email-cart' ),
			
			'desc' 		=> __( 'This is the default text to be editable in each Email Cart on the Frontend Checkout page.', 'email-cart') . '<br />' . __('Please do no remove any text containing [square brackets] as this will be populated with the cart information.', 'email-cart' ),
			
			'id' 		=> 'email_cart_default_frontend_text',
			
			'default'	=> $email_content,
			
			'type' 		=> 'textarea',
			
			'css' 		=> 'height: 300px;',
			
			'autoload'      => false
			
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
		
		$settings[] = array(
			
			'name' => __( 'Send From', 'email-cart' ),
			
			'desc' 		=> __( 'The Default From Address. Default is WooCommerce From Address', 'email-cart' ),
			
			'id' 		=> 'email_cart_default_from',
			
			'default'	=> get_option("woocommerce_email_from_address"),
			
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
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();

		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'woocommerce-settings' ) )
			die( __( 'Action failed. Please refresh the page and retry.', 'email-cart' ) );

		$this->save_fields( $settings );

		
	}
	
	/**
	 * Save admin fields.
	 *
	 * Loops though the woocommerce options array and outputs each field.
	 *
	 * @access public
	 * @param array $options Opens array to output
	 * @return bool
	 */
	public static function save_fields( $options ) {
		if ( empty( $_POST ) )
			return false;
		// Options to update will be stored here
		$update_options = array();

		// Loop options and get values to save
		foreach ( $options as $value ) {

			if ( ! isset( $value['id'] ) )
				continue;

			$type = isset( $value['type'] ) ? sanitize_title( $value['type'] ) : '';

			// Get the option name
			$option_value = null;

			switch ( $type ) {

				// Standard types
				case "checkbox" :

					if ( isset( $_POST[ $value['id'] ] ) ) {
						$option_value = 'yes';
					} else {
						$option_value = 'no';
					}

				break;

				case "textarea" :

					if ( isset( $_POST[$value['id']] ) ) {
						$option_value = wp_kses_post( trim( stripslashes( $_POST[ $value['id'] ] ) ) );
					} else {
						$option_value = '';
					}

				break;

				case "text" :
				case 'email':
				case 'number':
				case "select" :
				case "color" :
				case 'password' :
				case "single_select_page" :
				case "single_select_country" :
				case 'radio' :

					if ( $value['id'] == 'woocommerce_price_thousand_sep' || $value['id'] == 'woocommerce_price_decimal_sep' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $value['id'] ] )  ) {
							$option_value = wp_kses_post( stripslashes( $_POST[ $value['id'] ] ) );
						} else {
							$option_value = '';
						}

					} elseif ( $value['id'] == 'woocommerce_price_num_decimals' ) {

						// price separators get a special treatment as they should allow a spaces (don't trim)
						if ( isset( $_POST[ $value['id'] ] )  ) {
							$option_value = absint( $_POST[ $value['id'] ] );
						} else {
						   $option_value = 2;
						}

					} elseif ( $value['id'] == 'woocommerce_hold_stock_minutes' ) {

						// Allow > 0 or set to ''
						if ( ! empty( $_POST[ $value['id'] ] )  ) {
							$option_value = absint( $_POST[ $value['id'] ] );
						} else {
							$option_value = '';
						}

						wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );

						if ( $option_value != '' )
							wp_schedule_single_event( time() + ( absint( $option_value ) * 60 ), 'woocommerce_cancel_unpaid_orders' );

					} else {

					   if ( isset( $_POST[$value['id']] ) ) {
							$option_value = woocommerce_clean( stripslashes( $_POST[ $value['id'] ] ) );
						} else {
							$option_value = '';
						}

					}

				break;

				// Special types
				case "multiselect" :
				case "multi_select_countries" :

					// Get countries array
					if ( isset( $_POST[ $value['id'] ] ) )
						$selected_countries = array_map( 'wc_clean', array_map( 'stripslashes', (array) $_POST[ $value['id'] ] ) );
					else
						$selected_countries = array();

					$option_value = $selected_countries;

				break;

				case "image_width" :

					if ( isset( $_POST[$value['id'] ]['width'] ) ) {

						$update_options[ $value['id'] ]['width']  = woocommerce_clean( stripslashes( $_POST[ $value['id'] ]['width'] ) );
						$update_options[ $value['id'] ]['height'] = woocommerce_clean( stripslashes( $_POST[ $value['id'] ]['height'] ) );

						if ( isset( $_POST[ $value['id'] ]['crop'] ) )
							$update_options[ $value['id'] ]['crop'] = 1;
						else
							$update_options[ $value['id'] ]['crop'] = 0;

					} else {
						$update_options[ $value['id'] ]['width'] 	= $value['default']['width'];
						$update_options[ $value['id'] ]['height'] 	= $value['default']['height'];
						$update_options[ $value['id'] ]['crop'] 	= $value['default']['crop'];
					}

				break;

				// Custom handling
				default :

					do_action( 'woocommerce_update_option_' . $type, $value );

				break;

			}

			if ( ! is_null( $option_value ) ) {
				// Check if option is an array
				if ( strstr( $value['id'], '[' ) ) {

					parse_str( $value['id'], $option_array );

					// Option name is first key
					$option_name = current( array_keys( $option_array ) );

					// Get old option value
					if ( ! isset( $update_options[ $option_name ] ) )
						 $update_options[ $option_name ] = get_option( $option_name, array() );

					if ( ! is_array( $update_options[ $option_name ] ) )
						$update_options[ $option_name ] = array();

					// Set keys and value
					$key = key( $option_array[ $option_name ] );

					$update_options[ $option_name ][ $key ] = $option_value;

				// Single value
				} else {
					$update_options[ $value['id'] ] = $option_value;
				}
			}

			// Custom handling
			do_action( 'woocommerce_update_option', $value );
		}

		// Now save the options
		foreach( $update_options as $name => $value )
			update_option( $name, $value );

		return true;
	}

}

endif;

return new WC_Email_Cart_Settings();
