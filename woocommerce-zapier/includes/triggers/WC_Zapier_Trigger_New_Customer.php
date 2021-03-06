<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Zapier_Trigger_New_Customer extends WC_Zapier_Trigger {

	public function __construct() {

		// Prefix the trigger key with wc. to denote that this is a trigger that relates to a WooCommerce order
		$this->trigger_key         = 'wc.new_customer';

		$this->trigger_title       = __( 'New Customer', 'wc_zapier' );

		$checkout_signup_enabled = get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) == 'yes' ? true : false;
		$my_account_signup_enabled = get_option( 'woocommerce_enable_myaccount_registration' ) == 'yes' ? true : false;

		$this->trigger_description = __( 'Triggers if a customer chooses to register for an account.', 'wc_zapier' );
		if ( $checkout_signup_enabled ) {
			$this->trigger_description .= __( '<br />Occurs if a customer registers during the checkout process when placing an order.', 'wc_zapier' );
		}

		if ( $my_account_signup_enabled ) {
			$this->trigger_description .= __( '<br />Occurs if a customer registers via the my account page.', 'wc_zapier' );
		}

		// Registration is completely disabled, so show a warning message
		if ( !$checkout_signup_enabled && !$my_account_signup_enabled ) {
			$this->trigger_description .= sprintf( __( '<br />Warning: this trigger can only occur if your <a href="%s">WooCommerce settings</a> have the <em>Enable registration on the "Checkout" page</em> and/or <em>Enable registration on the "My Account" page</em> setting(s) enabled.', 'wc_zapier' ), admin_url( 'admin.php?page=wc-settings&tab=account' ) ) . '</span>';
		}

		$this->sort_order = 2;

		// WooCommerce action(s)
		$this->actions['woocommerce_created_customer'] = 1;

		parent::__construct();
	}


	public function assemble_data( $args, $action_name ) {

		global $woocommerce;

		$customer_id = null;
		if ( $this->is_sample() ) {
			// Use the currently logged in user's details for testing
			$current_user = wp_get_current_user();
			$customer_id = empty( $current_user ) ? 1 : $current_user->ID;
		} else {
			$customer_id = intval( $args[0] );
		}

		$customer_data = get_user_by( 'id', $customer_id );

		if ( ! $customer_data ) {
			// No user/customer information found
			return false;
		}

		$customer = array();

		// Gather customer's data so it can be sent to Zapier
		$customer['id']              = $customer_data->ID;
		$customer['first_name']      = $customer_data->first_name;
		$customer['last_name']       = $customer_data->last_name;
		$customer['email_address']   = $customer_data->user_email;
		$customer['username']        = $customer_data->user_login;
		$customer['paying_customer'] = (bool) $customer_data->paying_customer;

		// Important: the following fields WILL be empty if this customer hasn't placed an order yet, or hasn't added address details to their account
		$woocommerce_usermeta_fields = array(
				'billing_first_name',
				'billing_last_name',
				'billing_company',
			// 'billing_address', Only available for orders via WC_Orders::get_billing_address()
				'billing_email',
				'billing_phone',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_postcode',
				'billing_country', // Two letter country code
				'billing_country_name', // Country Name
				'billing_state',
				'shipping_first_name',
				'shipping_last_name',
				'shipping_company',
			// 'shipping_address', Only available for orders via WC_Orders::get_shipping_address()
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_postcode',
				'shipping_country', // Two letter country code
				'shipping_country_name', // Country Name
				'shipping_state'
		);

		$customer_meta = get_user_meta( $customer_id );

		foreach ( $woocommerce_usermeta_fields as $woocommerce_usermeta_field ) {
			$customer[ $woocommerce_usermeta_field ] = isset( $customer_meta[$woocommerce_usermeta_field][0] ) ? $customer_meta[$woocommerce_usermeta_field][0] : '';
		}

		// Country name conversions
		if ( !empty( $customer['billing_country'] ) ) {
			$customer['billing_country_name'] = $woocommerce->countries->countries[$customer['billing_country']];
		}
		if ( !empty( $customer['shipping_country'] ) ) {
			$customer['shipping_country_name'] = $woocommerce->countries->countries[$customer['shipping_country']];
		}

		WC_Zapier()->log( "Assembled customer data.", $customer['id'], 'Customer' );

		return $customer;

	}

}