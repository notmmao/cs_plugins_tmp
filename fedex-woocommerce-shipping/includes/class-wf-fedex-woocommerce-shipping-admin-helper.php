<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wf_fedex_woocommerce_shipping_admin_helper  {
	private $service_code;

	public function __construct() {
		$this->id                                   = WF_Fedex_ID;
		$this->rateservice_version                  = 16;
		$this->ship_service_version                 = 15;
		$this->addressvalidationservice_version     = 2;
        $this->tracking_ids                         = '';
		$this->init();
	}

	private function init() {		
		$this->settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		
		
		$this->fed_req	=	new	wfFedexRequest();
		
		//TODO:
		$this->weight_dimensions_manual = 'no';
		
		$this->add_trackingpin_shipmentid = $this->settings['tracking_shipmentid'];
		
		
		$this->origin          = str_replace( ' ', '', strtoupper( $this->settings[ 'origin' ] ) );
		$this->origin_country  = WC()->countries->get_base_country();
		$this->account_number  = $this->settings[ 'account_number' ];
		$this->meter_number    = $this->settings[ 'meter_number' ];
		$this->smartpost_hub   = $this->settings[ 'smartpost_hub' ];
		
		$this->indicia = isset($this->settings[ 'indicia']) ? $this->settings[ 'indicia'] : 'PARCEL_SELECT';

		$this->api_key         = $this->settings[ 'api_key' ];
		$this->api_pass        = $this->settings[ 'api_pass' ];
		$this->production      = ( $bool = $this->settings[ 'production' ] ) && $bool == 'yes' ? true : false;
		$this->debug           = ( $bool = $this->settings[ 'debug' ] ) && $bool == 'yes' ? true : false;
		$this->insure_contents = ( $bool = $this->settings[ 'insure_contents' ] ) && $bool == 'yes' ? true : false;
		$this->request_type    = $this->settings[ 'request_type'];
		$this->packing_method  = $this->settings[ 'packing_method'];
		$this->boxes           = $this->settings[ 'boxes'];
		$this->custom_services = $this->settings[ 'services'];
		$this->offer_rates     = $this->settings[ 'offer_rates'];
		$this->residential     = ( $bool = $this->settings[ 'residential'] ) && $bool == 'yes' ? true : false;
		$this->freight_enabled = ( $bool = $this->settings[ 'freight_enabled'] ) && $bool == 'yes' ? true : false;
		$this->fedex_one_rate  = ( $bool = $this->settings[ 'fedex_one_rate'] ) && $bool == 'yes' ? true : false;
		$this->fedex_one_rate_package_ids = array(
			'FEDEX_SMALL_BOX',
			'FEDEX_MEDIUM_BOX',
			'FEDEX_LARGE_BOX',
			'FEDEX_EXTRA_LARGE_BOX',
			'FEDEX_PAK',
			'FEDEX_ENVELOPE',
		);
		
		$this->box_max_weight			=	$this->settings[ 'box_max_weight'];
		$this->weight_packing_process	=	$this->settings[ 'weight_packing_process'];
		
		if(isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'LBS_IN'){
			$this->dimension_unit 			= 	'in';
			$this->weight_unit 				= 	'lbs';
			$this->labelapi_dimension_unit	=	'IN';
			$this->labelapi_weight_unit 	=	'LB';
			$this->default_boxes                    = include( 'data-wf-box-sizes.php' );
		}else{
			$this->dimension_unit 			= 	'cm';
			$this->weight_unit 				= 	'kg';
			$this->labelapi_dimension_unit	=	'CM';
			$this->labelapi_weight_unit 	=	'KG';
			$this->default_boxes                    = include( 'data-wf-box-sizes-cm.php' );
		}
		
		
		
		$this->freight_class               = $this->settings[ 'freight_class' ];
		$this->freight_number              = $this->settings[ 'freight_number'];
		$this->freight_billing_street      = $this->settings[ 'freight_billing_street' ];
		$this->freight_billing_street_2    = $this->settings[ 'billing_street_2' ];
		$this->freight_billing_city        = $this->settings[ 'freight_billing_city' ];
		$this->freight_billing_state       = $this->settings[ 'freight_billing_state' ];
		$this->freight_billing_postcode    = $this->settings[ 'billing_postcode' ];
		$this->freight_billing_country     = $this->settings[ 'billing_country' ];
		
		$this->freight_shipper_person_name      = $this->settings[ 'shipper_person_name' ];
		$this->freight_shipper_company_name      = $this->settings[ 'shipper_company_name' ];
		$this->freight_shipper_phone_number      = $this->settings[ 'shipper_phone_number' ];
		
		$this->freight_shipper_street      = $this->settings[ 'freight_shipper_street' ];
		$this->freight_shipper_street_2    = $this->settings[ 'shipper_street_2'];
		$this->freight_shipper_city        = $this->settings[ 'freight_shipper_city' ];
		$this->freight_shipper_state       = $this->settings[ 'freight_shipper_state' ];
		$this->freight_shipper_residential = ( $bool = $this->settings[ 'shipper_residential'] ) && $bool == 'yes' ? true : false;
		$this->freight_class               = str_replace( array( 'CLASS_', '.' ), array( '', '_' ), $this->freight_class );
	
		$this->output_format = $this->settings['output_format'];
		$this->image_type = $this->settings['image_type'];
        
        $this->cod_collection_type = isset($this->settings[ 'cod_collection_type']) ? $this->settings[ 'cod_collection_type'] : 'ANY';

		
		$this->shipping_charges_payment_type = isset ( $this->settings['shipping_charges_payment_type'] ) ? $this->settings['shipping_charges_payment_type'] : '';
		$this->shipping_payor_acc_no		 = isset ( $this->settings['shipping_payor_acc_no'] ) ? $this->settings['shipping_payor_acc_no'] : '';
		$this->shipping_payor_cname			 = isset ( $this->settings['shipping_payor_cname'] ) ? $this->settings['shipping_payor_cname'] : '';
		$this->shipping_payor_company		 = isset ( $this->settings['shipping_payor_company'] ) ? $this->settings['shipping_payor_company'] : '';
		$this->shipping_payor_phone			 = isset ( $this->settings['shipping_payor_phone'] ) ? $this->settings['shipping_payor_phone'] : '';
		$this->shipping_payor_email			 = isset ( $this->settings['shipping_payor_email'] ) ? $this->settings['shipping_payor_email'] : '';
		$this->shipping_payor_address1		 = isset ( $this->settings['shipping_payor_address1'] ) ? $this->settings['shipping_payor_address1'] : '';
		$this->shipping_payor_address2		 = isset ( $this->settings['shipping_payor_address2'] ) ? $this->settings['shipping_payor_address2'] : '';
		$this->shipping_payor_city			 = isset ( $this->settings['shipping_payor_city'] ) ? $this->settings['shipping_payor_city'] : '';
		$this->shipping_payor_state			 = isset ( $this->settings['shipping_payor_state'] ) ? $this->settings['shipping_payor_state'] : '';
		$this->shipping_payor_postal_code	 = isset ( $this->settings['shipping_payor_postal_code'] ) ? $this->settings['shipping_payor_postal_code'] : '';
		$this->shipping_payor_country		 = isset ( $this->settings['shipping_payor_country'] ) ? $this->settings['shipping_payor_country'] : '';
		
		$this->shipping_customs_duties_payer 		= isset ( $this->settings['shipping_customs_duties_payer'] ) ? $this->settings['shipping_customs_duties_payer'] : '';
		$this->shipping_customs_shipment_purpose 	= isset ( $this->settings['shipping_customs_shipment_purpose'] ) ? $this->settings['shipping_customs_shipment_purpose'] : '';
        
		

		// Insure contents requires matching currency to country
		switch ( WC()->countries->get_base_country() ) {
			case 'US' :
				if ( 'USD' !== get_woocommerce_currency() ) {
					$this->insure_contents = false;
				}
			break;
			case 'CA' :
				if ( 'CAD' !== get_woocommerce_currency() ) {
					$this->insure_contents = false;
				}
			break;
		}	
	}

	public function debug( $message, $type = 'notice' ) {
		if ( $this->debug ) {
			echo( $message);
		}
	}

	public function get_fedex_packages( $package ) {
		switch ( $this->packing_method ) {
			case 'box_packing' :
				return $this->box_shipping( $package );
			break;
			case 'weight_based' :
				return $this->weight_based_shipping( $package );
			break;
			case 'per_item' :
			default :
				return $this->per_item_shipping( $package );
			break;
		}
	}

	public function get_freight_class( $shipping_class_id ) {
		$class = get_woocommerce_term_meta( $shipping_class_id, 'fedex_freight_class', true );
		return $class ? $class : '';
	}

	private function per_item_shipping( $package ) {
		$to_ship  = array();
		$group_id = 1;
		// Get weight of order
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( __( 'Product # is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );
				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				$this->debug( sprintf( __( 'Product # is missing weight. Aborting.', 'wf-shipping-fedex' ), $item_id ), 'error' );
				return;
			}

			$group = array();

			$group = array(
				'GroupNumber'       => $group_id,
				'GroupPackageCount' => 1,
				'Weight' => array(
					'Value' => round( woocommerce_get_weight( $values['data']->get_weight(), $this->weight_unit ), 2 ) ,
					'Units' => $this->labelapi_weight_unit
				),
				'packed_products' => array( $values['data'] )
			);

			if ( $values['data']->length && $values['data']->height && $values['data']->width ) {

				$dimensions = array( $values['data']->length, $values['data']->width, $values['data']->height );

				sort( $dimensions );

				$group['Dimensions'] = array(
					'Length' => max( 1, round( woocommerce_get_dimension( $dimensions[2], $this->dimension_unit ), 2 ) ),
					'Width'  => max( 1, round( woocommerce_get_dimension( $dimensions[1], $this->dimension_unit ), 2 ) ),
					'Height' => max( 1, round( woocommerce_get_dimension( $dimensions[0], $this->dimension_unit ), 2 ) ),
					'Units'  => $this->labelapi_dimension_unit
				);
			}

			$group['InsuredValue'] = array(
				'Amount'   => sizeof($package['contents'])==1?$this->order->get_total():round( $values['data']->get_price() ),
				'Currency' => $this->wf_get_fedex_currency()
			);
			
			for($loop = 0; $loop < $values['quantity'];$loop++){
				$to_ship[] = $group;
			}
			$group_id++;
		}
		return $to_ship;
	}

	private function box_shipping( $package ) {
		if ( ! class_exists( 'WF_Boxpack' ) ) {
			include_once 'class-wf-packing.php';
		}

		$boxpack = new WF_Boxpack();

		// Merge default boxes
		foreach ( $this->default_boxes as $key => $box ) {
			$box['enabled'] = isset( $this->boxes[ $box['id'] ]['enabled'] ) ? $this->boxes[ $box['id'] ]['enabled'] : true;
			$this->boxes[] = $box;
		} 
		
		// Define boxes
		foreach ( $this->boxes as $key => $box ) {
			if ( ! is_numeric( $key ) ) {
				continue;
			}

			if ( ! $box['enabled'] ) {
				continue;
			}

			$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'], $box['box_weight'] );

			if ( isset( $box['id'] ) ) {
				$newbox->set_id( current( explode( ':', $box['id'] ) ) );
			}

			if ( $box['max_weight'] ) {
				$newbox->set_max_weight( $box['max_weight'] );
			}
		}

		// Add items
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( __( 'Product # is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );
				continue;
			}

			if ( $values['data']->length && $values['data']->height && $values['data']->width && $values['data']->weight ) {

				$dimensions = array( $values['data']->length, $values['data']->height, $values['data']->width );

				for ( $i = 0; $i < $values['quantity']; $i ++ ) {
					$boxpack->add_item(
						woocommerce_get_dimension( $dimensions[2], $this->dimension_unit ),
						woocommerce_get_dimension( $dimensions[1], $this->dimension_unit ),
						woocommerce_get_dimension( $dimensions[0], $this->dimension_unit ),
						round(woocommerce_get_weight( $values['data']->get_weight(), $this->weight_unit ),2),
						$values['data']->get_price(),
						array(
							'data' => $values['data']
						)
					);
				}

			} else {
				$this->debug( sprintf( __( 'Product #%s is missing dimensions. Aborting.', 'wf-shipping-fedex' ), $item_id ), 'error' );
				return;
			}
		}

		// Pack it
		$boxpack->pack();
		$packages = $boxpack->get_packages();
		$to_ship  = array();
		$group_id = 1;
		foreach ( $packages as $package ) {
			if ( $package->unpacked === true ) {
				$this->debug( 'Unpacked Item' );
			} else {
				$this->debug( 'Packed ' . $package->id );
			}

			$dimensions = array( $package->length, $package->width, $package->height );

			sort( $dimensions );

			$group = array(
				'GroupNumber'       => $group_id,
				'GroupPackageCount' => 1,
				'Weight' => array(
					'Value' => round( $package->weight, 2 ) ,
					'Units' => $this->labelapi_weight_unit
				),
				'Dimensions'        => array(
					'Length' => max( 1, round( $dimensions[2], 2 ) ),
					'Width'  => max( 1, round( $dimensions[1], 2 ) ),
					'Height' => max( 1, round( $dimensions[0], 2 ) ),
					'Units'  => $this->labelapi_dimension_unit
				),
				'InsuredValue'      => array(
					'Amount'   => sizeof($packages)==1?$this->order->get_total():round( $package->value ),
					'Currency' => $this->wf_get_fedex_currency()
				),
				'packed_products' => array(),
				'package_id'      => $package->id
			);
			if ( ! empty( $package->packed ) && is_array( $package->packed ) ) {
				foreach ( $package->packed as $packed ) {
					$group['packed_products'][] = $packed->get_meta( 'data' );
				}
			}

			if ( $this->freight_enabled ) {
				$highest_freight_class = '';

				if ( ! empty( $package->packed ) && is_array( $package->packed ) ) {
					foreach( $package->packed as $item ) {
						if ( $item->get_meta( 'data' )->get_shipping_class_id() ) {
							$freight_class = $this->get_freight_class( $item->get_meta( 'data' )->get_shipping_class_id() );

							if ( $freight_class > $highest_freight_class ) {
								$highest_freight_class = $freight_class;
							}
						}
					}
				}

				$group['freight_class'] = $highest_freight_class ? $highest_freight_class : '';
			}

			$to_ship[] = $group;

			$group_id++;
		}

		return $to_ship;
	}
	
	private function weight_based_shipping( $package ){
		if ( ! class_exists( 'WeightPack' ) ) {
			include_once 'weight_pack/class-wf-weight-packing.php';
		}
		
		$weight_pack=new WeightPack($this->weight_packing_process);
		$weight_pack->set_max_weight($this->box_max_weight);
		$cart_items	=	$package['contents'];
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( __( 'Product # is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );
				continue;
			}			
			
			if ( ! $values['data']->get_weight() ) {
				$this->debug( sprintf( __( 'Product # is missing weight. Aborting.', 'wf-shipping-fedex' ), $item_id ), 'error' );
				return;
			}
			
			$weight_pack->add_item(woocommerce_get_weight( $values['data']->get_weight(), $this->weight_unit ), $values['data'], $values['quantity']);
		}		
		
		$pack	=	$weight_pack->pack_items();
		
		$errors	=	$pack->get_errors();
		if( !empty($errors) ){
			//do nothing
			return;
		} else {
			$boxes		=	$pack->get_packed_boxes();
			$unpacked_items	=	$pack->get_unpacked_items();
			
			$to_ship  				= 	array();
			$group_id 				= 	1;		
			$group					= 	array();
			$packed_products		=	array();
			$insured_value			=	0;
			
			if(isset($this->order)){
				$order_total	=	$this->order->get_total();
			}
			
			
			$packages		=	array_merge( $boxes,	$unpacked_items ); // merge items if unpacked are allowed
			$package_count	=	sizeof($packages);
			
			// get all items to pass if item info in box is not distinguished
			$packable_items	=	$weight_pack->get_packable_items();
			$all_items		=	array();
			if(is_array($packable_items)){
				foreach($packable_items as $packable_item){
					$all_items[]	=	$packable_item['data'];
				}
			}
			
			foreach($packages as $package){
				if(($package_count	==	1) && isset($order_total)){
					$insured_value	=	$order_total;
				}else{
					$insured_value	=	0;
					if(!empty($package['items'])){
						foreach($package['items'] as $item){						
							$insured_value			=	$insured_value+$item->get_price();
						}
					}else{
						if($order_total && $package_count){
							$insured_value	=	$order_total/$package_count;
						}
					}
				}
				
				$group = array(
					'GroupNumber'       => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => round($package['weight'],2),
						'Units' => $this->labelapi_weight_unit
					),
					'packed_products' => $package['items']?$package['items']:$all_items
				);
				$group['InsuredValue'] = array(
					'Amount'   => $insured_value,
					'Currency' => $this->wf_get_fedex_currency()
				);

				$to_ship[] = $group;
			}
		}
		return $to_ship;
	}

	public function residential_address_validation( $package ) {
		$residential = $this->residential;

		// Address Validation API only available for production
		if ( $this->production ) {

			// Check if address is residential or commerical
			try {

				$client = new SoapClient( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/production/AddressValidationService_v' . $this->addressvalidationservice_version. '.wsdl', array( 'trace' => 1 ) );

				$request = array();

				$request['WebAuthenticationDetail'] = array(
					'UserCredential' => array(
						'Key'      => $this->api_key,
						'Password' => $this->api_pass
					)
				);
				$request['ClientDetail'] = array(
					'AccountNumber' => $this->account_number,
					'MeterNumber'   => $this->meter_number
				);
				$request['TransactionDetail'] = array( 'CustomerTransactionId' => ' *** Address Validation Request v2 from WooCommerce ***' );
				$request['Version'] = array( 'ServiceId' => 'aval', 'Major' => $this->addressvalidationservice_version, 'Intermediate' => '0', 'Minor' => '0' );
				$request['RequestTimestamp'] = date( 'c' );
				$request['Options'] = array(
					'CheckResidentialStatus' => 1,
					'MaximumNumberOfMatches' => 1,
					'StreetAccuracy' => 'LOOSE',
					'DirectionalAccuracy' => 'LOOSE',
					'CompanyNameAccuracy' => 'LOOSE',
					'ConvertToUpperCase' => 1,
					'RecognizeAlternateCityNames' => 1,
					'ReturnParsedElements' => 1
				);
				$request['AddressesToValidate'] = array(
					0 => array(
						'AddressId' => 'WTC',
						'Address' => array(
							'StreetLines' => array( $package['destination']['address'], $package['destination']['address_2'] ),
							'PostalCode'  => $package['destination']['postcode'],
						)
					)
				);

				$response = $client->addressValidation( $request );

				if ( $response->HighestSeverity == 'SUCCESS' ) {
					if ( is_array( $response->AddressResults ) )
						$addressResult = $response->AddressResults[0];
					else
						$addressResult = $response->AddressResults;

					if ( $addressResult->ProposedAddressDetails->ResidentialStatus == 'BUSINESS' )
						$residential = false;
					elseif ( $addressResult->ProposedAddressDetails->ResidentialStatus == 'RESIDENTIAL' )
						$residential = true;
				}

			} catch (Exception $e) {
                $this->debug( __( 'SoapFault while residential_address_validation.', 'wf-shipping-fedex' ) );
            }

		}

		$this->residential = apply_filters( 'woocommerce_fedex_address_type', $residential, $package );

		if ( $this->residential == false ) {
			$this->debug( __( 'Business Address', 'wf-shipping-fedex' ) );
		}
	}

	private function get_fedex_common_api_request( $request ) {
		// Prepare Shipping Request for FedEx
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key'      => $this->api_key,
				'Password' => $this->api_pass
			)
		);
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->account_number,
			'MeterNumber'   => $this->meter_number
		);
		$request['TransactionDetail'] = array(
			'CustomerTransactionId'     => '*** Express Domestic Shipping Request using PHP ***'
		);
		$request['Version'] = array(
			'ServiceId'              => 'ship',
			'Major'                  => $this->ship_service_version,
			'Intermediate'           => '0',
			'Minor'                  => '0'
		);		
		return $request;
	}
	private function get_fedex_api_request( $package ,$request_type) {
		$request = array();

		$request = $this->get_fedex_common_api_request($request);
		
		//$request['ReturnTransitAndCommit'] = false;
		$request['RequestedShipment']['PreferredCurrency'] = $this->wf_get_fedex_currency();
		$request['RequestedShipment']['DropoffType']       = 'REGULAR_PICKUP';
		$request['RequestedShipment']['ServiceType'] = $this->service_code;
		$request['RequestedShipment']['ShipTimestamp']     = date( 'c');
		$request['RequestedShipment']['PackagingType']     = 'YOUR_PACKAGING';
		
		$request['RequestedShipment']['Shipper'] = array(
						'Contact'=>array(
							'PersonName' => $this->freight_shipper_person_name,
							'CompanyName' => $this->freight_shipper_company_name,
							'PhoneNumber' => $this->freight_shipper_phone_number
						),
						'Address'               => array(
							'StreetLines'         => array( strtoupper( $this->freight_shipper_street ), strtoupper( $this->freight_shipper_street_2 ) ),
							'City'                => strtoupper( $this->freight_shipper_city ),
							'StateOrProvinceCode' => strtoupper( $this->freight_shipper_state ),
							'PostalCode'          => strtoupper( $this->origin ),
							'CountryCode'         => strtoupper( $this->origin_country ),
							'Residential'         => $this->freight_shipper_residential
						)
					);

		
		$shipping_charges_payment	=	$this->fed_req->get_shipping_charges_payment();		
		$request['RequestedShipment']['ShippingChargesPayment']=$shipping_charges_payment;
		  
		$request['RequestedShipment']['RateRequestTypes'] = $this->request_type === 'LIST' ? 'LIST' : 'NONE';
		$request['RequestedShipment']['Recipient'] = array(
			'Contact' => array(
				'PersonName' => $package['destination']['first_name'] . ' ' . $package['destination']['last_name'],
				'CompanyName' => $package['destination']['company'],
				'PhoneNumber' => $this->order->billing_phone
			),
			'Address' => array(
				'StreetLines' =>  array( $package['destination']['address_1'],$package['destination']['address_2']),
				'Residential'         => $this->residential,
				'PostalCode'          => str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) ),
				'City'                => strtoupper( $package['destination']['city'] ),
				'StateOrProvinceCode' => strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '',
				'CountryCode'         => $package['destination']['country']
			)
		);		
		if ( 'freight' === $request_type ){
			$request['RequestedShipment']['LabelSpecification'] = array(
				'LabelFormatType' => 'VICS_BILL_OF_LADING',
				'ImageType' => strtoupper($this->image_type),  // valid values DPL, EPL2, PDF, ZPLII and PNG
				'LabelStockType' => $this->output_format
			);
		}
		else{
			$request['RequestedShipment']['LabelSpecification'] = array(
				'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
				'ImageType' => strtoupper($this->image_type),  // valid values DPL, EPL2, PDF, ZPLII and PNG
				'LabelStockType' => $this->output_format
			);
		}		
		return $request;
	}

	private function get_fedex_requests( $fedex_packages, $package, $request_type = '' ) {
		$requests = array();
		
		// All reguests for this package get this data
		$package_request = $this->get_fedex_api_request( $package,$request_type );
		if ( $fedex_packages ) {
			$total_packages = 0;
			$total_weight   = 0;
			foreach ( $fedex_packages as $key => $parcel ) {
				$total_packages += $parcel['GroupPackageCount'];
				$total_weight   += $parcel['Weight']['Value'] * $parcel['GroupPackageCount'];
			}
			foreach ( $fedex_packages as $key => $parcel ) {
				
				$single_package_weight = $parcel['Weight']['Value'];
				
				$request        = $package_request;
				$parcel_value = $parcel['InsuredValue']['Amount'] * $parcel['GroupPackageCount'];
				
				$request['RequestedShipment']['TotalWeight'] = array(
					'Value' => $total_weight, 
					'Units' => $this->labelapi_weight_unit // valid values LB and KG
				);
				
				$commodoties    = array();
				$freight_class  = '';

				// Store parcels as line items
				$request['RequestedShipment']['RequestedPackageLineItems'] = array();
				
				$parcel_request = $parcel;
				if ( $parcel_request['packed_products'] ) {
					foreach ( $parcel_request['packed_products'] as $product ) {
						
						if ( isset( $commodoties[ $product->id ] ) ) {
							$commodoties[ $product->id ]['Quantity'] ++;
							$commodoties[ $product->id ]['CustomsValue']['Amount'] += round( $product->get_price() );
							continue;
						}
						$commodoties[ $product->id ] = array(
							'Name'                 => sanitize_title( $product->get_title() ),
							'NumberOfPieces'       => 1,
							'Description'          => sanitize_title( $product->get_title()),
							'CountryOfManufacture' => ( $country = get_post_meta( $product->id, 'CountryOfManufacture', true ) ) ? $country : WC()->countries->get_base_country(),
							'Weight'               => array(
								'Units'            => $this->labelapi_weight_unit,
								'Value'            => round( woocommerce_get_weight( $product->get_weight(), $this->weight_unit ), 2 ) ,
							),
							'Quantity'             => $parcel['GroupPackageCount'],
							'UnitPrice'            => array(
								'Amount'           => round( $product->get_price() ),
								'Currency'         => $this->wf_get_fedex_currency()
							),
							'CustomsValue'         => array(
								'Amount'           => round( $product->get_price() ),
								'Currency'         => $this->wf_get_fedex_currency()
							),
							'QuantityUnits' => 'EA'
						);
					}
				}
				
				if ( 'freight' === $request_type ) {
					// Get the highest freight class for shipment
					if ( isset( $parcel['freight_class'] ) && $parcel['freight_class'] > $freight_class ) {
						$freight_class = $parcel['freight_class'];
					}
				} else {
					// Work out the commodoties for CA shipments
					

					$special_servicetypes = array();
					// Is this valid for a ONE rate? Smart post does not support it
					if ( $this->fedex_one_rate && '' === $request_type && isset($parcel_request['package_id']) && in_array( $parcel_request['package_id'], $this->fedex_one_rate_package_ids )) {
						$request['RequestedShipment']['PackagingType']                                   = $parcel_request['package_id'];
						if('US' === $package['destination']['country'] && 'US' === $this->origin_country ){
							$special_servicetypes[] = 'FEDEX_ONE_RATE';
							
						}
					}
					
					if(isset($_GET['cod']) && $_GET['cod'] === 'true'){
						if(isset($this->order)){
							$order_total=$this->order->get_total();
						}
						$special_servicetypes[] = 'COD';
						$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CodCollectionAmount']['Currency'] = $this->wf_get_fedex_currency();	
						$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CodCollectionAmount']['Amount'] = $order_total?$order_total:$parcel_value;	
						$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CollectionType'] =$this->cod_collection_type;							
					}
					if(!empty($special_servicetypes)){
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] = $special_servicetypes;
					}
				}

				// Remove temp elements
				unset( $parcel_request['freight_class'] );
				unset( $parcel_request['packed_products'] );
				unset( $parcel_request['package_id'] );

				if ( ! $this->insure_contents || 'smartpost' === $request_type ) {
					unset( $parcel_request['InsuredValue'] );
				}				
			
				if ( 'smartpost' === $request_type ) {
					$request['RequestedShipment']['PackageCount'] = 1;
					$parcel_request = array_merge( array( 'SequenceNumber' => 1 ), $parcel_request );
				
				}else{
					$request['RequestedShipment']['PackageCount'] = $total_packages;
					$parcel_request = array_merge( array( 'SequenceNumber' => $key + 1 ), $parcel_request );
				}

				$request['RequestedShipment']['RequestedPackageLineItems'][] = $parcel_request;				
				
				$indicia = $this->indicia;
				
				if($indicia == 'AUTOMATIC' && $single_package_weight >= 1)
					$indicia = 'PARCEL_SELECT';
				elseif($indicia == 'AUTOMATIC' && $single_package_weight < 1)
					$indicia = 'PRESORTED_STANDARD';				
				
				// Smart post
				if ( 'smartpost' === $request_type ) {
					$request['RequestedShipment']['SmartPostDetail'] = array(
						'Indicia'              => $indicia,
						'HubId'                => $this->smartpost_hub,
						'AncillaryEndorsement' => 'ADDRESS_CORRECTION',
						'SpecialServices'      => ''
					);
					
					// Smart post does not support insurance, but is insured up to $100
					if ( $this->insure_contents && round( $parcel_value ) > 100 ) {
						return false;
					}
				} elseif ( $this->insure_contents ) {
					$request['RequestedShipment']['TotalInsuredValue'] = array(
						'Amount'   => round( $parcel_value ),
						'Currency' => $this->wf_get_fedex_currency()
					);
				}
				
				if ( 'freight' === $request_type ) {
					$request['CarrierCodes'] = 'FXFR';
					$request['RequestedShipment']['FreightShipmentDetail'] = array(
						'FedExFreightAccountNumber'            => strtoupper( $this->freight_number ),
						'FedExFreightBillingContactAndAddress' => array(
							'Address'                             => array(
								'StreetLines'                        => array( strtoupper( $this->freight_billing_street ), strtoupper( $this->freight_billing_street_2 ) ),
								'City'                               => strtoupper( $this->freight_billing_city ),
								'StateOrProvinceCode'                => strtoupper( $this->freight_billing_state ),
								'PostalCode'                         => strtoupper( $this->freight_billing_postcode ),
								'CountryCode'                        => strtoupper( $this->freight_billing_country )
							)
						),
						'Role'                                 => 'SHIPPER',
						'PaymentType'                          => 'PREPAID',
						'TotalHandlingUnits' 					=> 1
					);

					// Format freight class
					$freight_class = $freight_class ? $freight_class : $this->freight_class;
					$freight_class = $freight_class < 100 ?  '0' . $freight_class : $freight_class;
					$freight_class = 'CLASS_' . str_replace( '.', '_', $freight_class );

					$request['RequestedShipment']['FreightShipmentDetail']['LineItems'] = array(
						'FreightClass' => $freight_class,
						'Packaging'    => 'SKID',
						'Weight'       => array(
							'Units'    => $this->labelapi_weight_unit,
							'Value'    => round( $total_weight, 2 )
						),
						'Pieces' => 1,
						'Description' => 'Heavy Stuff'
					);
					
					// If account id is set by third party then don't overwrite with frieght account id.
					if(isset($request['RequestedShipment']['ShippingChargesPayment'])){
						if($request['RequestedShipment']['ShippingChargesPayment']['PaymentType']=='SENDER'){
							$request['RequestedShipment']['ShippingChargesPayment'] = array(
								'PaymentType' => 'SENDER',
								'Payor' => array(
									'ResponsibleParty' => array(
										'AccountNumber'           => strtoupper( $this->freight_number ),
										'CountryCode'             => WC()->countries->get_base_country()
									)
								)
							);
						}
					}
					
					
				}
				
				$core_countries = array('US','CA');
				if (WC()->countries->get_base_country() !== $package['destination']['country'] || !in_array(WC()->countries->get_base_country(),array('US'))) {
					
					$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment'] = array(
						'PaymentType' => $this->shipping_customs_duties_payer
					);
					// If payor is not a recipient then account details is not needed
					if($this->shipping_customs_duties_payer!='RECIPIENT'){
						$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty']=array(
							'AccountNumber'           => strtoupper( $this->account_number ),
							'CountryCode'             => WC()->countries->get_base_country()
						);
					}

					$request['RequestedShipment']['CustomsClearanceDetail']['CustomsValue'] = array('Amount' => $parcel_value, 'Currency' => $this->wf_get_fedex_currency());	
					$request['RequestedShipment']['CustomsClearanceDetail']['Commodities'] = array_values( $commodoties );
					
					if( !in_array(WC()->countries->get_base_country(),$core_countries)){
						$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice'] = array(
							'Purpose' => $this->shipping_customs_shipment_purpose
						);
					}

					if(WC()->countries->get_base_country()=='CA'){ //International shipment from CA other than US
						if(isset($this->order->id)&&$this->order->shipping_country!='US'){
							//$request['RequestedShipment']['CustomsClearanceDetail']['ExportDetail']['ExportComplianceStatement']='NOT_REQUIRED';//'AESX20160102123456';
							$request['RequestedShipment']['CustomsClearanceDetail']['ExportDetail']['B13AFilingOption']='NOT_REQUIRED';
						}
					}
				}
				// Add request				
				$request=apply_filters('wf_fedex_request',$request,$this->order);
				$requests[] = $request;
			}			
		}
		return $requests;
	}

	private function wf_get_package_from_order($order){
		$orderItems = $order->get_items();
		foreach($orderItems as $orderItem)
		{
			$product_data   = wc_get_product( $orderItem['variation_id'] ? $orderItem['variation_id'] : $orderItem['product_id'] );
			$items[] = array('data' => $product_data , 'quantity' => $orderItem['qty']);
		}
		$package['contents'] = $items;
		$package['destination']['country'] = $order->shipping_country;
		$package['destination']['first_name'] = $order->shipping_first_name;
		$package['destination']['last_name'] = $order->shipping_last_name;
		$package['destination']['company'] = $order->shipping_company;
		$package['destination']['address_1'] = $order->shipping_address_1;
		$package['destination']['address_2'] = $order->shipping_address_2;
		$package['destination']['city'] = $order->shipping_city;
		$package['destination']['state'] = $order->shipping_state;
		$package['destination']['postcode'] = $order->shipping_postcode;
		
		$packages=apply_filters('wf_fx_shipping_packages',array($package),$order->id);		
		return $packages;
	}
	
	public function print_label( $order,$service_code,$order_id ){
		$this->order = $order;
		$this->order_id = $order_id;
		$this->service_code = $service_code;
		$packages=$this->wf_get_package_from_order($order);
		foreach($packages as $package){
			$this->print_label_processor($package);
		}		
	}
	
	public function void_shipment( $order_id , $shipment_id, $tracking_completedata){
		$request = array();
		$this->order_id = $order_id;
		$request = $this->get_fedex_common_api_request($request);
		$request['ShipTimestamp'] = date('c');
		$request['TrackingId'] = $tracking_completedata;
		$request['DeletionControl'] = 'DELETE_ONE_PACKAGE'; // Package/Shipment

		$this->debug( 'FedEx REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $request, true ) . '</pre>' );
		
        try {
		  $client = new SoapClient( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/ShipService_v' . $this->ship_service_version. '.wsdl', array( 'trace' => 1 ) );
		  $result = $client->deleteShipment( $request );
        } catch (Exception $e) {
            $this->debug( __( 'SoapFault while void_shipment.', 'wf-shipping-fedex' ) );
        }

		$this->debug( 'FedEx RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $result, true ) . '</pre>' );

		if ( $result->HighestSeverity != 'FAILURE' && $result->HighestSeverity != 'ERROR') {
			add_post_meta($order_id, 'wf_woo_fedex_shipment_void', $shipment_id, false);					 
		}else{
			$shipment_void_errormessage =  $this->result_notifications($result->Notifications, $error_message='');
			update_post_meta($order_id, 'wf_woo_fedex_shipment_void_errormessage', $shipment_void_errormessage);
		}		
	}
	
	public function print_label_processor( $package ) {
		
		$this->shipmentErrorMessage = '';
		$this->master_tracking_id = '';
		
		// Debugging
		$this->debug( __( 'FEDEX debug mode is on - to hide these messages, turn debug mode off in the settings.', 'wf-shipping-fedex' ) );

		// See if address is residential
		$this->residential_address_validation( $package );

		$request_type= '';
		if(! empty( $this->smartpost_hub ) && $package['destination']['country'] == 'US' && $this->service_code == 'SMART_POST')
			$request_type = 'smartpost';
		elseif(strpos($this->service_code, 'FREIGHT') !== false){
			$request_type = 'freight';
		}
			
		// Get requests
		$fedex_packages   = $this->get_fedex_packages( $package );
		if($this->validate_package($fedex_packages)){
			$fedex_requests   = $this->get_fedex_requests( $fedex_packages, $package, $request_type);
			if ( $fedex_requests ) {
				$this->run_package_request( $fedex_requests );
			}
			$packages_to_quote_count = sizeof( $fedex_requests );
		}
		update_post_meta($this->order_id, 'wf_woo_fedex_shipmentErrorMessage', $this->shipmentErrorMessage);	

	}

	public function run_package_request( $requests ) {
		/* try {		 	
		 */	
			//$this->tracking_ids = '';
			foreach ( $requests as $key => $request ) {
				$this->process_result( $this->get_result( $request ) , $request);
			}
			if(!empty($this->tracking_ids)){
				// Auto fill tracking info.
				$shipment_id_cs = $this->tracking_ids;
				WfTrackingUtil::update_tracking_data( $this->order_id, $shipment_id_cs, 'fedex', WF_Tracking_Admin_FedEx::SHIPMENT_SOURCE_KEY, WF_Tracking_Admin_FedEx::SHIPMENT_RESULT_KEY );
			}
			
		/*  } catch ( Exception $e ) {
			$this->debug( print_r( $e, true ), 'error' );
			return false;
		} */ 
	}
	
	private function wf_get_fedex_currency(){
		if(get_woocommerce_currency() == 'GBP') 
			return 'UKL';
		else if(get_woocommerce_currency() == 'CHF') 
			return 'SFR';
		else if(get_woocommerce_currency() == 'MXN') 
			return 'NMP';
		return get_woocommerce_currency();		
	}

	private function get_result( $request ) {
		if(!empty($this->master_tracking_id))
			$request['RequestedShipment']['MasterTrackingId'] = $this->master_tracking_id;		

		$this->debug( 'FedEx REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $request, true ) . '</pre>' );
		
        $result = '';
        try {
		  $client = new SoapClient( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/ShipService_v' . $this->ship_service_version. '.wsdl', array( 'trace' => 1 ) );

		  $result = $client->processShipment( $request );
        } catch (Exception $e) {
            $this->debug( __( 'SoapFault while run_package_request.', 'wf-shipping-fedex' ) );
        }

		$this->debug( 'FedEx RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $result, true ) . '</pre>' );
		return $result;
	}

	private function process_result( $result = '' , $request) {
		if ( $result->HighestSeverity != 'FAILURE' && $result->HighestSeverity != 'ERROR' && ! empty ($result->CompletedShipmentDetail) ) {
			
			if(property_exists($result->CompletedShipmentDetail,'CompletedPackageDetails')){
				if(is_array($result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds)){
					foreach($result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds as $track_ids){
						if($track_ids->TrackingIdType != 'USPS'){
							$shipmentId = $track_ids->TrackingNumber;	
							$tracking_completedata = $track_ids; 		
						}else{
							$usps_shipmentId = $track_ids->TrackingNumber;
							
						}
					}
				}
				else{
					$shipmentId = $result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;		
					$tracking_completedata = $result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds;
				}	
			}
			elseif(property_exists($result->CompletedShipmentDetail,'MasterTrackingId')){
				$shipmentId = $result->CompletedShipmentDetail->MasterTrackingId->TrackingNumber;		
				$tracking_completedata = $result->CompletedShipmentDetail->MasterTrackingId;				
			}			
			
			if(!empty($result->CompletedShipmentDetail->MasterTrackingId) && empty($this->master_tracking_id))
				$this->master_tracking_id = $result->CompletedShipmentDetail->MasterTrackingId;
			
			$addittional_label = array();
			if(property_exists($result->CompletedShipmentDetail,'ShipmentDocuments')){
				$shippingLabel = base64_encode($result->CompletedShipmentDetail->ShipmentDocuments->Parts->Image);
			}else{
				$shippingLabel = base64_encode($result->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image);
				if(property_exists($result->CompletedShipmentDetail->CompletedPackageDetails,'PackageDocuments')){
					$package_documents = $result->CompletedShipmentDetail->CompletedPackageDetails->PackageDocuments;
					if(is_array($package_documents)){
						foreach($package_documents as $document_key=>$package_document){
							$addittional_label[$document_key] = base64_encode($package_document->Parts->Image);
						}
					}
				}		
			}
            if(!empty($shippingLabel) && property_exists($result->CompletedShipmentDetail,'AssociatedShipments')){
                $associated_documents = $result->CompletedShipmentDetail->AssociatedShipments->Label;
                if(!empty($associated_documents)){
                        $addittional_label['AssociatedLabel'] = base64_encode($associated_documents->Parts->Image);
                }
            }
			
			 if(!empty($shipmentId) && !empty($shippingLabel)){
				add_post_meta($this->order_id, 'wf_woo_fedex_shipmentId', $shipmentId, false);
				add_post_meta($this->order_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, $shippingLabel, true);
				add_post_meta($this->order_id, 'wf_woo_fedex_packageDetails_'.$shipmentId, $this->wf_get_parcel_details($request) , true);	
				
				if(isset($tracking_completedata)){
					add_post_meta($this->order_id, 'wf_woo_fedex_tracking_full_details_'.$shipmentId, $tracking_completedata, true);
				}			
					
				if(!empty($this->service_code)){
					add_post_meta($this->order_id, 'wf_woo_fedex_service_code', $this->service_code, true);
				}
				
				if(!empty($usps_shipmentId)){
					add_post_meta($this->order_id, 'wf_woo_fedex_usps_trackingid_'.$shipmentId, $usps_shipmentId, true);
				}

				if($this->add_trackingpin_shipmentid == 'yes' && !empty($shipmentId)){
					//$this->order->add_order_note( sprintf( __( 'Fedex Tracking-pin #: %s.', 'wf-shipping-fedex' ), $shipmentId) , true);
					$this->tracking_ids = $this->tracking_ids . $shipmentId . ',';			
				}
				
				if($this->add_trackingpin_shipmentid == 'yes' && !empty($usps_shipmentId)){
					//$this->order->add_order_note( sprintf( __( 'Fedex Smart Post USPS Tracking-pin #: %s.', 'wf-shipping-fedex' ), $usps_shipmentId) , true);
				}
				
				if(!empty($addittional_label)){
					add_post_meta($this->order_id, 'wf_fedex_additional_label_'.$shipmentId, $addittional_label, true);				
				}							
			} 
		}else{
			$this->shipmentErrorMessage .=  $this->result_notifications($result->Notifications, $error_message='');
		}		
	}
	
	private function wf_get_parcel_details($request){
		 $weight = '';
		 $height = '';
		 $width = '';
		 $length = '';
		 if(isset($request['RequestedShipment']['RequestedPackageLineItems'][0])){
			$line = $request['RequestedShipment']['RequestedPackageLineItems'][0];
			if(isset($line['Weight'])){
				$weight = $line['Weight']['Value'] . ' ' . $line['Weight']['Units'];			
			}
			if(isset($line['Dimensions'])){
				$height = $line['Dimensions']['Height'] . ' ' . $line['Dimensions']['Units'];	
				$width = $line['Dimensions']['Width'] . ' ' . $line['Dimensions']['Units'];	
				$length = $line['Dimensions']['Length'] . ' ' . $line['Dimensions']['Units'];					
			}			
		 }		 
		 return array('Weight' => $weight, 'Height' => $height, 'Width' => $width, 'Length' => $length);
	}
	
	private function result_notifications($notes, $error_message=''){
		foreach($notes as $noteKey => $note){
			if(is_string($note)){    
				$error_message .=  $noteKey . ': ' . $note . "<br />";
			}
			else{
				$error_message .=  $this->result_notifications($note,$error_message);
			}
		}
		return $error_message;
	}
	
	private function validate_package($packages){
		$package_valid=true;
		$unpacked_items=array();
		$msg='';
		if($this->packing_method	!=	'box_packing'){// Only box packing is needed to check products now 
			return true;
		}
		foreach($packages as $package){
			if(!isset($package['packed_products'])||empty($package['packed_products'])){
				$package_valid=false;
				$unpacked_items[]=$package;
			}			
		}
		if(!$package_valid&&!empty($unpacked_items)){
			$msg=' Following product dimensions cannot be packed. Please configure correct box dimenions, Or set Individually/Weight based  as parcel packing method.</br>';
			foreach($unpacked_items as $unpacked_item){
				$dim=$unpacked_item['Dimensions']['Length'].'X'.$unpacked_item['Dimensions']['Width'].'X'.$unpacked_item['Dimensions']['Height'].' '.$unpacked_item['Dimensions']['Units'];
				$weight=$unpacked_item['Weight']['Value'].' '.$unpacked_item['Weight']['Units'];
				$msg.=sprintf('Dimenstions: %1$s Weight: %2$s</br>',$dim,$weight);				
			}
		}
		if($msg){
			$this->shipmentErrorMessage=__($msg);
		}		
		return $package_valid;
	}
}