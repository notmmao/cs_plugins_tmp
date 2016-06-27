<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$freight_classes = include( 'data-wf-freight-classes.php' );
$smartpost_hubs  = include( 'data-wf-smartpost-hubs.php' );
$smartpost_hubs  = array( '' => __( 'N/A', 'wf-shipping-fedex' ) ) + $smartpost_hubs;

$wc_countries   = new WC_Countries();
// This function will not support prior to WC 2.2
$country_list   = $wc_countries->get_countries();
array_unshift( $country_list, "" );

/**
 * Array of settings
 */
return array(
	'enabled'          => array(
		'title'           => __( 'Realtime rates', 'wf-shipping-fedex' ),
		'type'            => 'checkbox',
		'label'           => __( 'Enable', 'wf-shipping-fedex' ),
		'default'         => 'no'
	),
	'title'            => array(
		'title'           => __( 'Method Title', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'description'     => __( 'This controls the title which the user sees during checkout.', 'wf-shipping-fedex' ),
		'default'         => __( 'FedEx', 'wf-shipping-fedex' ),
		'desc_tip'        => true
	),
    'debug'      => array(
		'title'           => __( 'Debug Mode', 'wf-shipping-fedex' ),
		'label'           => __( 'Enable', 'wf-shipping-fedex' ),
		'type'            => 'checkbox',
		'default'         => 'no',
		'desc_tip'    => true,
		'description'     => __( 'Enable debug mode to show debugging information on the cart/checkout.', 'wf-shipping-fedex' )
	),	
	'api'                    => array(
		'title'              => __( 'Generic API Settings', 'wf-shipping-fedex' ),
		'type'               => 'title',
		'description'        => __( 'After signup, get a <a href="https://www.fedex.com/wpor/web/jsp/drclinks.jsp?links=wss/develop.html">developer key here</a>. After testing you can get a <a href="https://www.fedex.com/wpor/web/jsp/drclinks.jsp?links=wss/production.html">production key here</a>.', 'wf-shipping-fedex' )
	),
	'account_number'           => array(
		'title'           => __( 'FedEx Account Number', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'description'     => '',
		'default'         => ''
    ),
    'meter_number'           => array(
		'title'           => __( 'Fedex Meter Number', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'description'     => '',
		'default'         => ''
    ),
    'api_key'           => array(
		'title'           => __( 'Web Services Key', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'description'     => '',
		'default'         => '',
		'custom_attributes' => array(
			'autocomplete' => 'off'
		)
    ),
    'api_pass'           => array(
		'title'           => __( 'Web Services Password', 'wf-shipping-fedex' ),
		'type'            => 'password',
		'description'     => '',
		'default'         => '',
		'custom_attributes' => array(
			'autocomplete' => 'off'
		)
    ),
    'production'      => array(
		'title'           => __( 'Production Key', 'wf-shipping-fedex' ),
		'label'           => __( 'This is a production key', 'wf-shipping-fedex' ),
		'type'            => 'checkbox',
		'default'         => 'no',
		'desc_tip'    => true,
		'description'     => __( 'If this is a production API key and not a developer key, check this box.', 'wf-shipping-fedex' )
	),
	'dimension_weight_unit' => array(
			'title'           => __( 'Dimension/Weight Unit', 'wwf-shipping-fedex' ),
			'label'           => __( 'This unit will be passed to FedEx.', 'wf-shipping-fedex' ),
			'type'            => 'select',
			'default'         => 'LBS_IN',
			'desc_tip'    => true,
			'description'     => 'Product dimensions and weight will be converted to the selected unit and will be passed to FedEx.',
			'options'         => array(
				'LBS_IN'	=> __( 'Pounds & Inches', 'wf-shipping-fedex'),
				'KG_CM' 	=> __( 'Kilograms & Centimeters', 'wf-shipping-fedex')			
			)
	),
    'residential'      => array(
		'title'           => __( 'Residential', 'wf-shipping-fedex' ),
		'label'           => __( 'Default to residential delivery', 'wf-shipping-fedex' ),
		'type'            => 'checkbox',
		'default'         => 'no',
		'desc_tip'    => true,
		'description'     => __( 'Enables residential flag. If you account has Address Validation enabled, this will be turned off/on automatically.', 'wf-shipping-fedex' ),
	),
    'insure_contents'      => array(
		'title'       => __( 'Insurance', 'wf-shipping-fedex' ),
		'label'       => __( 'Enable Insurance', 'wf-shipping-fedex' ),
		'type'        => 'checkbox',
		'default'     => 'yes',
		'desc_tip'    => true,
		'description' => __( 'Sends the package value to FedEx for insurance.', 'wf-shipping-fedex' ),
	),
	'fedex_one_rate'      => array(
		'title'       => __( 'Fedex One', 'wf-shipping-fedex' ),
		'label'       => sprintf( __( 'Enable %sFedex One Rates%s', 'wf-shipping-fedex' ), '<a href="https://www.fedex.com/us/onerate/" target="_blank">', '</a>' ),
		'type'        => 'checkbox',
		'default'     => 'yes',
		'desc_tip'    => true,
		'description' => __( 'Fedex One Rates will be offered if the items are packed into a valid Fedex One box, and the origin and destination is the US. For other countries this option will enable FedEx packing. Note: All FedEx boxes are not available for all countries, disable this option or disable different boxes if you are not receiving any shipping services.', 'wf-shipping-fedex' ),
	),	
	'request_type'     => array(
		'title'           => __( 'Request Type', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'default'         => 'LIST',
		'class'           => '',
		'desc_tip'        => true,
		'options'         => array(
			'LIST'        => __( 'List rates', 'wf-shipping-fedex' ),
			'ACCOUNT'     => __( 'Account rates', 'wf-shipping-fedex' ),
		),
		'description'     => __( 'Choose whether to return List or Account (discounted) rates from the API.', 'wf-shipping-fedex' )
	),
	'smartpost_hub'           => array(
		'title'           => __( 'Fedex SmartPost Hub', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'description'     => __( 'Only required if using SmartPost.', 'wf-shipping-fedex' ),
		'desc_tip'        => true,
		'default'         => '',
		'options'         => $smartpost_hubs
    ),
	'indicia'   => array(
		'title'           => __( 'Indicia', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'desc_tip'    => true,
		'description'     => 'Applicable only for SmartPost. Ex: Parcel Select option requires weight of at-least 1LB. Automatic will choose PRESORTED STANDARD if the weight is less than 1lb and PARCEL SELECT if the weight is more than 1lb',
		'default'         => 'PARCEL_SELECT',
		'options'         => array(
		    'MEDIA_MAIL'         => __( 'MEDIA MAIL', 'wf-shipping-fedex' ),
		    'PARCEL_RETURN'    => __( 'PARCEL RETURN', 'wf-shipping-fedex' ),
		    'PARCEL_SELECT'    => __( 'PARCEL SELECT', 'wf-shipping-fedex' ),
		    'PRESORTED_BOUND_PRINTED_MATTER' => __( 'PRESORTED BOUND PRINTED MATTER', 'wf-shipping-fedex' ),
		    'PRESORTED_STANDARD' => __( 'PRESORTED STANDARD', 'wf-shipping-fedex' ),
			'AUTOMATIC' => __( 'AUTOMATIC', 'wf-shipping-fedex' )
		),
    ),
	'offer_rates'   => array(
		'title'           => __( 'Offer Rates', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'description'     => '',
		'default'         => 'all',
		'options'         => array(
		    'all'         => __( 'Offer the customer all returned rates', 'wf-shipping-fedex' ),
		    'cheapest'    => __( 'Offer the customer the cheapest rate only, anonymously', 'wf-shipping-fedex' ),
		),
    ),
	'shipping_customs_duties_payer'  => array(
		'title'           => __( 'Customs Duties Payer', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'desc_tip'    => true,
		'description'     => 'Select customs duties payer',
		'default'         => 'SENDER',
		'options'         => array(
			'SENDER' 						      	=> __( 'Sender', 						'woocommerce-shipping-fedex'),
			'RECIPIENT' 						      	=> __( 'Recipient', 						'woocommerce-shipping-fedex'),
		)				
	),
	'shipping_customs_shipment_purpose'   => array(
		'title'           => __( 'Purpose Of Shipment', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'desc_tip'    => true,
		'description'     => 'Select purpose of shipment',
		'default'         => 'SOLD',
		'options'         => array(
			'GIFT' 				=> __( 'Gift', 				'woocommerce-shipping-fedex'),
			'NOT_SOLD' 			=> __( 'Not Sold', 			'woocommerce-shipping-fedex'),
			'PERSONAL_EFFECTS' 	=> __( 'Personal effects', 	'woocommerce-shipping-fedex'),
			'REPAIR_AND_RETURN' => __( 'Repair and return', 'woocommerce-shipping-fedex'),
			'SENDER' 			=> __( 'Sample', 			'woocommerce-shipping-fedex'),
			'SOLD' 				=> __( 'Sold', 	 			'woocommerce-shipping-fedex'),
		)				
	),
	'services_packaging'  => array(
		'title'           => __( 'Services and Packaging', 'ups-woocommerce-shipping' ),
		'type'            => 'title',
		'description'     => '',
	),
    'packing_method'   => array(
		'title'           => __( 'Parcel Packing Method', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'default'         => '',
		'class'           => 'packing_method',
		'options'         => array(
			'per_item'       => __( 'Default: Pack items individually', 'wf-shipping-fedex' ),
			'box_packing'    => __( 'Recommended: Pack into boxes with weights and dimensions', 'wf-shipping-fedex' ),
			'weight_based'   => __( 'Weight based: Calculate shipping based on weight', 'wf-shipping-fedex' ),
		),
		'desc_tip'    => true,
		'description'     => __( 'Determine how items are packed before being sent to FedEx.', 'wf-shipping-fedex' ),
	),
	'box_max_weight'           => array(
		'title'           => __( 'Box Maximum Weight', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '10',
		'class'           => 'weight_based_option',
		'desc_tip'    => true,
		'description'     => __( 'Maximum weight allowed for single box.', 'wf-shipping-fedex' ),
    ),
	'weight_packing_process'   => array(
		'title'           => __( 'Packing Process', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'default'         => '',
		'class'           => 'weight_based_option',
		'options'         => array(
			'pack_descending'       => __( 'Pack heavier items first', 'wf-shipping-fedex' ),
			'pack_ascending'    	=> __( 'Pack lighter items first.', 'wf-shipping-fedex' ),
			'pack_simple'    		=> __( 'Pack purely divided by weight.', 'wf-shipping-fedex' ),
		),
		'desc_tip'    => true,
		'description'     => __( 'Select your packing order.', 'wf-shipping-fedex' ),
	),
	'boxes'  => array(
		'type'            => 'box_packing'
	),
	'services'  => array(
		'type'            => 'services'
	),
	'label-settings'                    => array(
		'title'              => __( 'Label Printing API Settings', 'ups-woocommerce-shipping' ),
		'type'               => 'title',
	),
	'origin'           => array(
		'title'           => __( 'Origin Postcode', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'desc_tip'    => true,
		'description'     => __( 'Enter postcode for the <strong>Shipper</strong>.', 'wf-shipping-fedex' ),
		'default'         => ''
    ),
	'shipper_person_name'           => array(
			'title'           => __( 'Shipper Person Name', 'wf-shipping-fedex' ),
			'type'            => 'text',
			'default'         => '',
			'desc_tip'    => true,
			'description'     => 'Required for label Printing'			
	),	
	'shipper_company_name'           => array(
			'title'           => __( 'Shipper Company Name', 'wf-shipping-fedex' ),
			'type'            => 'text',
			'default'         => ''	,
			'desc_tip'    => true,
			'description'     => 'Required for label Printing'
	),	
	'shipper_phone_number'           => array(
			'title'           => __( 'Shipper Phone Number', 'wf-shipping-fedex' ),
			'type'            => 'text',
			'default'         => ''	,
			'desc_tip'    => true,
			'description'     => 'Required for label Printing'
    ),
    'freight_shipper_street'           => array(
		'title'           => __( 'Shipper Street Address', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
		'desc_tip'    => true,
		'description'     => 'Required for label Printing. And should be filled if LTL Freight is enabled.'
    ),
    'shipper_street_2'           => array(
		'title'           => __( 'Shipper Street Address 2', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
		'desc_tip'    => true,
		'description'     => 'Required for label Printing. And should be filled if LTL Freight is enabled.'
    ),
    'freight_shipper_city'           => array(
		'title'           => __( 'Shipper City', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
		'desc_tip'    => true,
		'description'     => 'Required for label Printing. And should be filled if LTL Freight is enabled.'
    ),
    'freight_shipper_state'           => array(
		'title'           => __( 'Shipper State Code', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
		'desc_tip'    => true,
		'description'     => 'Required for label Printing. And should be filled if LTL Freight is enabled.'
    ),
    'shipper_residential'           => array(
    	'title'           => __( 'Residential', 'wf-shipping-fedex' ),
		'label'           => __( 'Shipper Address is Residential?', 'wf-shipping-fedex' ),
		'type'            => 'checkbox',
		'default'         => 'no'
    ),	
	'output_format'   => array(
		'title'           => __( 'Label print size', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'desc_tip'    => true,
		'description'     => '8.5x11 indicates paper and 4x6 indicates thermal size.',
		'default'         => 'PAPER_7X4.75',
		'options'         => array(
			'PAPER_4X6' 						      	=> __( 'PAPER_4X6', 						'woocommerce-shipping-fedex'),
			'PAPER_4X8' 						      	=> __( 'PAPER_4X8', 						'woocommerce-shipping-fedex'),
			'PAPER_4X9' 						      	=> __( 'PAPER_4X9', 						'woocommerce-shipping-fedex'),
			'PAPER_7X4.75' 					      		=> __( 'PAPER_7X4.75', 					'woocommerce-shipping-fedex'),
			'PAPER_8.5X11_BOTTOM_HALF_LABEL' 	      	=> __( 'PAPER_8.5X11_BOTTOM_HALF_LABEL', 	'woocommerce-shipping-fedex'),
			'PAPER_8.5X11_TOP_HALF_LABEL'			  	=> __( 'PAPER_8.5X11_TOP_HALF_LABEL', 	'woocommerce-shipping-fedex'),
			'PAPER_LETTER' 					      		=> __( 'PAPER_LETTER', 					'woocommerce-shipping-fedex'),
			)				
		)	,
	'image_type'   => array(
		'title'           => __( 'Image Type', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'desc_tip'    => true,
		'description'     => '4x6 output format best fit with type PNG',
		'default'         => 'pdf',
		'options'         => array(
			'pdf' 						      	=> __( 'PDF', 						'woocommerce-shipping-fedex'),
			'png' 						      	=> __( 'PNG', 						'woocommerce-shipping-fedex')
			)				
		)	,
	'tracking_shipmentid' => array(
			'title'           => __( 'Tracking PIN', 'wf-shipping-canada-post' ),
			'label'           => __( 'Add Tracking PIN to customer order notes', 'wf-shipping-canada-post' ),
			'type'            => 'checkbox',
			'default'         => 'no',
			'description'     => ''
		),	
	'convert_currency' => array(
		'title'           => __( 'Rates in base currency', 'wf-shipping-canada-post' ),
		'label'           => __( 'Convert FedEx returned rates to base currency', 'wf-shipping-canada-post' ),
		'type'            => 'checkbox',
		'default'         => 'no',
		'desc_tip'    => true,
		'description'     => 'Ex: FedEx returned rates in USD and would like to convert to the base currency EUR. Convertion happens only FedEx API provide the exchange rate.'
	),
    'cod_collection_type'   => array(
		'title'           => __( 'COD Collection Type', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'desc_tip'    => true,
		'description'     => 'Identifies the type of funds FedEx should collect upon shipment delivery.',
		'default'         => 'ANY',
		'options'         => array(
			'ANY' 						      	=> __( 'ANY', 						'woocommerce-shipping-fedex'),
			'CASH' 						      	=> __( 'CASH', 						'woocommerce-shipping-fedex'),
			'GUARANTEED_FUNDS'   		      	=> __( 'GUARANTEED_FUNDS',			'woocommerce-shipping-fedex')
			)				
		)	,
	'shipping_charges_payment_type'   => array(
		'title'           => __( 'Shipping Charges Payment Type', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'desc_tip'    => true,
		'description'     => 'Select the payer for shipping and customs charges',
		'default'         => 'SENDER',
		'options'         => array(
			'SENDER' 						      	=> __( 'Sender', 						'woocommerce-shipping-fedex'),
			//'RECIPIENT' 						      	=> __( 'Recipient', 						'woocommerce-shipping-fedex'),
			'THIRD_PARTY' 						      	=> __( 'Third Party', 						'woocommerce-shipping-fedex'),
		)				
	),
    'payer_settings'           => array(
		'title'           => __( 'Third Party Payer Settings', 'wf-shipping-fedex' ),
		'type'            => 'title',
		'description'     => __( 'Payer Settings fields are mandatory only if third party billing is enabled.', 'wf-shipping-fedex' ),
    ),
	'shipping_payor_acc_no'    => array(
		'title'           => __( 'Third Party Account Number', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
		'desc_tip'    => true,
		'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_cname'     => array(
		'title'           => __( 'Third Party Payer Contact Person', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_company'   => array(
		'title'           => __( 'Third Party Payer Company', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_phone'     => array(
		'title'           => __( 'Third Party Payer Contact Number', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_email'     => array(
		'title'           => __( 'Third Party Payer Contact Email', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_address1'   => array(
		'title'           => __( 'Third Party Payer Address Line 1', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_address2'   => array(
		'title'           => __( 'Third Party Payer Address Line 2', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_city'       => array(
		'title'           => __( 'Third Party Payer City', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_state'       => array(
		'title'           => __( 'Third Party Payer State Code', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_postal_code' => array(
		'title'           => __( 'Third Party Payer Postal Code', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
        'description'     => 'Required if third party payment selected',
    ),
	'shipping_payor_country'    => array(
		'title'           => __( 'Third Party Payer Country', 'wf-shipping-fedex' ),
		'type'            => 'select',
		'default'         => '',
		'options'		  => $country_list,
        'description'     => 'Required if third party payment selected',
    ),
	'freight'           => array(
		'title'           => __( 'FedEx LTL Freight', 'wf-shipping-fedex' ),
		'type'            => 'title',
		'description'     => __( 'If your account supports Freight, we need some additional details to get LTL rates. Note: These rates require the customers CITY so won\'t display until checkout.', 'wf-shipping-fedex' ),
    ),
    'freight_enabled'      => array(
		'title'           => __( 'Enable', 'wf-shipping-fedex' ),
		'label'           => __( 'Enable Freight', 'wf-shipping-fedex' ),
		'type'            => 'checkbox',
		'default'         => 'no'
	),
	'freight_number' => array(
		'title'       => __( 'FedEx Freight Account Number', 'wf-shipping-fedex' ),
		'type'        => 'text',
		'description' => '',
		'default'     => '',
		'placeholder' => __( 'Defaults to your main account number', 'wf-shipping-fedex' )
	),
	'freight_billing_street'           => array(
		'title'           => __( 'Billing Street Address', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => ''
    ),
    'billing_street_2'           => array(
		'title'           => __( 'Billing Street Address 2', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => ''
    ),
    'freight_billing_city'           => array(
		'title'           => __( 'Billing City', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => ''
    ),
    'freight_billing_state'           => array(
		'title'           => __( 'Billing State Code', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
    ),
    'billing_postcode'           => array(
		'title'           => __( 'Billing ZIP / Postcode', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
    ),
    'billing_country'           => array(
		'title'           => __( 'Billing Country Code', 'wf-shipping-fedex' ),
		'type'            => 'text',
		'default'         => '',
    ),
	
    'freight_class'           => array(
		'title'           => __( 'Default Freight Class', 'wf-shipping-fedex' ),
		'desc_tip'    => true,
		'description'     => sprintf( __( 'This is the default freight class for shipments. This can be overridden using <a href="%s">shipping classes</a>', 'wf-shipping-fedex' ), admin_url( 'edit-tags.php?taxonomy=product_shipping_class&post_type=product' ) ),
		'type'            => 'select',
		'default'         => '50',
		'options'         => $freight_classes
    ),
);