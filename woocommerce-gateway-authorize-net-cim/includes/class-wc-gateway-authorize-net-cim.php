<?php
/**
 * WooCommerce Authorize.net CIM Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.net CIM Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.net CIM Gateway for your
 * needs please refer to http://docs.woothemes.com/document/authorize-net-cim/
 *
 * @package   WC-Gateway-Authorize-Net-CIM/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2015, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Authorize.net CIM Payment Gateway Parent Class
 *
 * Functionality which is shared between the credit card and echeck gateways
 *
 * @since 2.0.0
 */
class WC_Gateway_Authorize_Net_CIM extends SV_WC_Payment_Gateway_Direct {


	/** @var string authorize.net API login ID */
	public $api_login_id;

	/** @var string authorize.net API transaction key */
	public $api_transaction_key;

	/** @var string authorize.net test API login ID */
	public $test_api_login_id;

	/** @var string authorize.net test API transaction key */
	public $test_api_transaction_key;

	/** @var string determines how to process transactions, auth & capture or auth only */
	public $transaction_type;

	/** @var string require the card security code during checkout */
	public $require_cvv;

	/** @var string the location of the merchant's payment processor, determines what fields are required at checkout */
	public $payment_processor_location;

	/** @var WC_Authorize_Net_CIM_API instance */
	protected $api;

	/** @var array shared settings names */
	protected $shared_settings_names = array( 'api_login_id', 'api_transaction_key', 'test_api_login_id', 'test_api_transaction_key' );


	/**
	 * Returns an array of form fields specific for this method
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_method_form_fields()
	 * @return array of form fields
	 */
	protected function get_method_form_fields() {

		return array(

			'api_login_id' => array(
				'title'    => __( 'API Login ID', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Authorize.net API Login ID', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
			),

			'api_transaction_key' => array(
				'title'    => __( 'API Transaction Key', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Authorize.net API Transaction Key', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
			),

			'test_api_login_id' => array(
				'title'    => __( 'Test API Login ID', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your test Authorize.net API Login ID', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
			),

			'test_api_transaction_key' => array(
				'title'    => __( 'Test API Transaction Key', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'     => 'password',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your test Authorize.net API Transaction Key', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
			),
		);
	}


	/**
	 * Override the standard transaction processing to cover these situations:
	 *
	 * 1) For a tokenized transaction where the billing information entered does
	 * not match the billing information stored on the token -> update the token
	 * prior to processing the transaction
	 *
	 * 2) For a tokenized transaction when the order has no shipping address ID
	 * -> create one and use it for the transaction
	 *
	 * 3) For a tokenized transaction when the shipping address for the order does
	 * not match the shipping address stored for the customer in CIM -> update
	 * the shipping address stored in CIM to the address for the order
	 *
	 * @TODO: this method is not invoked for subscription renewal or pre-order
	 * release transactions and it probably should be @MR 2015-07-24
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway_Direct::do_transaction()
	 * @param \WC_Order $order
	 * @return bool
	 */
	public function do_transaction( $order ) {

		// bail if not profile transaction
		if ( empty( $order->payment->token ) ) {
			return parent::do_transaction( $order );
		}

		$token = $this->get_payment_token( $order->get_user_id(), $order->payment->token );

		// compare the billing information saved on the token with the billing information for the order
		if ( ! $token->billing_matches_order( $order ) ) {

			// does not match, update the existing payment profile
			$this->get_api()->update_tokenized_payment_method( $order );

			// update the token billing hash with the entered info
			$token->update_billing_hash( $order );

			// persist the token to user meta
			$this->update_payment_token( $order->get_user_id(), $token );
		}

		$shipping_address = $this->get_shipping_address( $order->get_user_id() );

		// no shipping profile, create one
		if ( empty( $order->payment->shipping_address_id ) ) {

			$response = $this->get_api()->create_shipping_address( $order );

			// create_shipping_address() can return an ID if a duplicate exists in CIM, as it
			// will simply return the ID instead of creating the address
			$order->payment->shipping_address_id = is_numeric( $response ) ? $response : $response->get_shipping_address_id();

			// persist profile ID to user meta
			$shipping_address->update_id( $order->payment->shipping_address_id );

			// update the hash and persist to user meta
			$shipping_address->update_hash( $order );

		} elseif ( ! $shipping_address->matches_order( $order ) ) {

			// saved shipping profile does not match shipping info used for order,
			// update the existing shipping profile in CIM
			$this->get_api()->update_shipping_address( $order );

			// update the hash and persist to user meta
			$shipping_address->update_hash( $order );
		}

		// continue processing the transaction
		return parent::do_transaction( $order );
	}


	/**
	 * Add any Authorize.net CIM specific transaction information as
	 * class members of WC_Order instance.  Added members can include:
	 *
	 * + po_number - PO Number to be included with the transaction via the legacy filter below
	 *
	 * @since 2.0.0
	 * @see WC_Gateway_Authorize_Net_CIM::get_order()
	 * @param int $order_id order ID being processed
	 * @return WC_Order object with payment and transaction information attached
	 */
	public function get_order( $order_id ) {

		// add common order members
		$order = parent::get_order( $order_id );

		// backwards compat for transaction/PO number filters introduced in v1.x
		// @deprecated in 2.0.0
		$order->description = apply_filters( 'wc_authorize_net_cim_transaction_description', $order->description, $order->id, $this );

		// remove any weirdness in the description
		$order->description = SV_WC_Helper::str_to_sane_utf8( $order->description );

		// @deprecated in 2.0.0
		$po_number = apply_filters( 'wc_authorize_net_cim_transaction_po_number', false, $order_id, $this );
		if ( $po_number ) {
			$order->po_number =  $po_number;
		}

		// add shipping address ID for profile transactions (using existing payment method or adding a new one)
		if ( $order->get_user_id() && ( ! empty( $order->payment->token ) || $this->should_tokenize_payment_method() ) ) {

			$shipping_address = $this->get_shipping_address( $order->get_user_id() );

			$order->payment->shipping_address_id = $shipping_address->get_id();
		}

		return $order;
	}


	/**
	 * Override the default get_order_meta() to provide backwards compatibility
	 * for Subscription/Pre-Order tokens added in v1.x, as the meta keys were
	 * not scoped per gateway. A bulk upgrade of all meta keys while trying to
	 * account for the gateway used for the original order (only defined by the
	 * payment type saved) is too risky given the potential for timeouts.
	 *
	 * Eventually this method can be removed and an upgrade routine can be added
	 * to catch any straggling meta keys that haven't been updated.
	 *
	 * @TODO: Remove me in July 2016 @MR
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_order_meta()
	 * @param int|string $order_id ID for order to get meta for
	 * @param string $key meta key
	 * @return mixed
	 */
	public function get_order_meta( $order_id, $key ) {

		// v2.0.0-v2.0.3 fix due to copypasta
		if ( 'payment_token' === $key && metadata_exists( 'post', $order_id, $this->get_order_meta_prefix() . $key ) && metadata_exists( 'post', $order_id, '_wc_authorize_net_cim_payment_profile_id' ) ) {

			$legacy_payment_token = get_post_meta( $order_id, '_wc_authorize_net_cim_payment_profile_id', true );
			$payment_token        = get_post_meta( $order_id, $this->get_order_meta_prefix() . 'payment_token', true );
			$customer_id          = get_post_meta( $order_id, '_wc_authorize_net_cim_customer_profile_id', true );

			// oops, payment token was previously overwritten due to copypasta
			// fix by correctly setting the payment token to the value provided in the old meta value ('_wc_authorize_net_cim_payment_profile_id')
			if ( $payment_token === $customer_id ) {

				$this->update_order_meta( $order_id, 'payment_token', $legacy_payment_token );

				return $legacy_payment_token;
			}
		}

		// v1.x token order meta handling
		if ( 'payment_token' === $key && ! metadata_exists( 'post', $order_id, $this->get_order_meta_prefix() . $key ) &&
			 metadata_exists( 'post', $order_id, '_wc_authorize_net_cim_payment_profile_id' ) ) {

			$token = get_post_meta( $order_id, '_wc_authorize_net_cim_payment_profile_id', true );

			$this->update_order_meta( $order_id, 'payment_token', $token );

			return $token;
		}

		// v1.x customer ID order meta handling
		if ( 'customer_id' === $key && ! metadata_exists( 'post', $order_id, $this->get_order_meta_prefix() . $key ) &&
			 metadata_exists( 'post', $order_id, '_wc_authorize_net_cim_customer_profile_id' ) ) {

			$customer_id = get_post_meta( $order_id, '_wc_authorize_net_cim_customer_profile_id', true );

			$this->update_order_meta( $order_id, 'customer_id', $customer_id );

			return $customer_id;
		}

		return get_post_meta( $order_id, $this->get_order_meta_prefix() . $key, true );
	}


	/**
	 * Auth.net tokenizes payment methods prior to sale
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway_Direct::tokenize_before_sale()
	 * @return bool
	 */
	public function tokenize_before_sale() {
		return true;
	}


	/**
	 * Return a custom payment token class instance
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway_Direct::build_payment_token()
	 * @return bool
	 */
	public function build_payment_token( $token, $data ) {

		return new WC_Authorize_Net_CIM_Payment_Profile( $token, $data );
	}


	/**
	 * Add additional attributes to be used when merging local token data into
	 * remote tokens, as Authorize.net doesn't include certain things like
	 * expiration dates or card types when fetching tokens from the API, but that
	 * info is saved with the token locally when it's first created
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway_Direct::get_payment_token_merge_attributes()
	 * @return array merge attributes
	 */
	protected function get_payment_token_merge_attributes() {

		return array_merge( parent::get_payment_token_merge_attributes(), array( 'billing_hash', 'payment_hash' ) );
	}


	/**
	 * When retrieving payment profiles via the Auth.net API, it returns both
	 * credit/debit card *and* eCheck payment profiles from a single call. Overriding
	 * the core framework update method ensures that eChecks aren't saved to
	 * the credit card token meta entry, and vice versa.
	 *
	 * @since 2.0.0
	 * @param int $user_id WP user ID
	 * @param array $tokens array of tokens
	 * @param string $environment_id optional environment id, defaults to plugin current environment
	 * @return string updated user meta id
	 */
	protected function update_payment_tokens( $user_id, $tokens, $environment_id = null ) {

		foreach ( $tokens as $token_id => $token ) {

			if ( ( $this->is_credit_card_gateway() && ! $token->is_credit_card() ) || ( $this->is_echeck_gateway() && ! $token->is_echeck() ) ) {
				unset( $tokens[ $token_id ] );
			}
		}

		return parent::update_payment_tokens( $user_id, $tokens, $environment_id );
	}


	/** Customer ID methods ***************************************************/


	/**
	 * CIM generates it's own customer IDs (customer profile IDs) after creating
	 * the customer profile via the API
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_customer_id()
	 * @param int $user_id WP user ID
	 * @param array $args optional additional arguments which can include: environment_id, autocreate (true/false), and order
	 * @return string payment gateway customer id
	 */
	public function get_customer_id( $user_id, $args = array() ) {

		$defaults = array(
			'environment_id' => $this->get_environment(),
			'autocreate'     => true,
			'order'          => null,
		);

		$args = array_merge( $defaults, $args );

		return get_user_meta( $user_id, $this->get_customer_id_user_meta_name( $args['environment_id'] ), true );
	}


	/**
	 * Change the meta name for the customer ID to:
	 *
	 * `wc_authorize_net_cim_customer_profile_id{_non-production environment ID}`
	 *
	 * @since 2.0.0
	 * @param string|null $environment_id
	 * @return string
	 */
	public function get_customer_id_user_meta_name( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		// no leading underscore since this is meant to be visible to the admin
		return 'wc_' . $this->get_plugin()->get_id() . '_customer_profile_id' . ( ! $this->is_production_environment( $environment_id ) ? '_' . $environment_id : '' );
	}


	/**
	 * Return an instance of the shipping address class for the given user
	 * @since 2.0.0
	 * @param string|int $user_id WP user ID
	 * @return \WC_Authorize_Net_CIM_Shipping_Address
	 */
	public function get_shipping_address( $user_id ) {

		return new WC_Authorize_Net_CIM_Shipping_Address( $user_id, $this );
	}


	/**
	 * Helper method to remove all local customer/payment profile data
	 *
	 * 1) removes local tokens
	 * 2) removes local customer ID
	 *
	 * @since 2.0.0
	 */
	public function remove_local_profile() {

		$user_id = get_current_user_id();

		$env = $this->is_test_environment() ? '_test' : '';

		// remove local tokens, hardcoded key names because this method could be invoked
		// from either the credit card or eCheck gateway, and we want to remove *both*
		// sets of tokens, not simply the tokens from the gateway that invoked the method.
		delete_user_meta( $user_id, '_wc_authorize_net_cim_credit_card_payment_tokens' . $env );
		delete_user_meta( $user_id, '_wc_authorize_net_cim_echeck_payment_tokens' . $env );

		// remove shipping address ID
		delete_user_meta( $user_id, 'wc_authorize_net_cim_shipping_address_id' . $env );

		// remove customer ID
		$this->remove_customer_id( $user_id );
	}


	/** Subscriptions *********************************************************/


	/**
	 * Tweak the labels shown when editing the payment method for a Subscription,
	 * hooked from SV_WC_Payment_Gateway_Integration_Subscriptions
	 *
	 *
	 * @since 2.0.3
	 * @see SV_WC_Payment_Gateway_Integration_Subscriptions::admin_add_payment_meta()
	 * @param array $meta payment meta
	 * @param \WC_Subscription $subscription subscription being edited
	 * @return array
	 */
	public function subscriptions_admin_add_payment_meta( $meta, $subscription ) {

		if ( isset( $meta[ $this->get_id() ] ) ) {
			$meta[ $this->get_id() ]['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['label'] = __( 'Payment Profile ID', WC_Authorize_Net_CIM::TEXT_DOMAIN );
			$meta[ $this->get_id() ]['post_meta'][ $this->get_order_meta_prefix() . 'customer_id' ]['label']   = __( 'Customer Profile ID', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}

		return $meta;
	}


	/**
	 * Validate the CIM payment meta for a Subscription by ensuring the payment
	 * profile ID and customer profile ID are both numeric
	 *
	 *
	 * @since 2.0.3
	 * @see SV_WC_Payment_Gateway_Integration_Subscriptions::admin_validate_payment_meta()
	 * @param array $meta payment meta
	 * @throws \Exception if payment profile/customer profile IDs are not numeric
	 */
	public function subscriptions_admin_validate_payment_meta( $meta ) {

		// payment profile ID (payment_token) must be numeric
		if ( ! ctype_digit( (string) $meta['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['value'] ) ) {
			throw new Exception( __( 'Payment Profile ID must be numeric.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
		}

		// customer profile ID (customer_id) must be numeric
		if ( ! ctype_digit( (string) $meta['post_meta'][ $this->get_order_meta_prefix() . 'customer_id' ]['value'] ) ) {
			throw new Exception( __( 'Customer Profile ID must be numeric.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
		}
	}


	/** Utility methods ********************************************************/


	/**
	 * Returns true if the gateway is properly configured to perform transactions.
	 * Authorize.net CIM requires: API Login ID & API Transaction Key
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::is_configured()
	 * @return boolean true if the gateway is properly configured
	 */
	protected function is_configured() {

		$is_configured = parent::is_configured();

		// missing configuration
		if ( ! $this->get_api_login_id() || ! $this->get_api_transaction_key() ) {
			$is_configured = false;
		}

		return $is_configured;
	}


	/**
	 * Checks if the CIM add-on is enabled for the provided authorize.net account
	 * by requesting a token for the hosted profile page using dummy data. The
	 * 'getHostedProfilePageRequest' method was chosen as it's lightweight
	 * and multiple calls to it have no effect on the provided authorize.net account
	 *
	 * @since 1.0.4
	 * @return bool true if CIM feature is enabled on provided authorize.net account, false otherwise
	 */
	public function is_cim_feature_enabled() {

		try {

			$customer_id =  $this->get_customer_id( get_current_user_id() );

			$this->get_api()->get_hosted_profile_page_token( $customer_id ? $customer_id : 0 );

		} catch ( SV_WC_Plugin_Exception $e ) {

			// E00044 is 'Customer Information Manager is not enabled.' error
			if ( 44 == $e->getCode() ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Get the API object
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_api()
	 * @return WC_Authorize_Net_CIM_API API instance
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		$includes_path = $this->get_plugin()->get_plugin_path() . '/includes';

		// main API class
		require_once( $includes_path . '/api/class-wc-authorize-net-cim-api.php' );

		// abstracts
		require_once( $includes_path . '/api/abstract-wc-authorize-net-cim-api-request.php' );
		require_once( $includes_path . '/api/abstract-wc-authorize-net-cim-api-response.php' );
		require_once( $includes_path . '/api/transaction/abstract-wc-authorize-net-cim-api-transaction-request.php' );
		require_once( $includes_path . '/api/transaction/abstract-wc-authorize-net-cim-api-transaction-response.php' );

		// profiles
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-profile-request.php' );
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-profile-response.php' );
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-customer-profile-request.php' );
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-customer-profile-response.php' );
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-payment-profile-request.php' );
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-payment-profile-response.php' );
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-shipping-address-request.php' );
		require_once( $includes_path . '/api/profile/class-wc-authorize-net-cim-api-shipping-address-response.php' );

		// hosted profile page
		require_once( $includes_path . '/api/profile-page/class-wc-authorize-net-cim-api-hosted-profile-page-request.php' );
		require_once( $includes_path . '/api/profile-page/class-wc-authorize-net-cim-api-hosted-profile-page-response.php' );

		// transactions
		require_once( $includes_path . '/api/transaction/class-wc-authorize-net-cim-api-non-profile-transaction-request.php' );
		require_once( $includes_path . '/api/transaction/class-wc-authorize-net-cim-api-non-profile-transaction-response.php' );
		require_once( $includes_path . '/api/transaction/class-wc-authorize-net-cim-api-profile-transaction-request.php' );
		require_once( $includes_path . '/api/transaction/class-wc-authorize-net-cim-api-profile-transaction-response.php' );

		// response message helper
		require_once( $includes_path . '/api/class-wc-authorize-net-cim-api-response-message-helper.php' );

		return $this->api = new WC_Authorize_Net_CIM_API( $this->get_id(), $this->get_environment(), $this->get_api_login_id(), $this->get_api_transaction_key() );
	}


	/**
	 * Returns the API Login ID based on the current environment
	 *
	 * @since 2.0.0
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string the API login ID to use
	 */
	public function get_api_login_id( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return 'production' == $environment_id ? $this->api_login_id : $this->test_api_login_id;
	}


	/**
	 * Returns the API Transaction Key based on the current environment
	 *
	 * @since 2.0.0
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string the API transaction key to use
	 */
	public function get_api_transaction_key( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return 'production' == $environment_id ? $this->api_transaction_key : $this->test_api_transaction_key;
	}


}
