<?php
/*
 * Purpose of Ttis file is for writing common API requests related
 * functions which will be used across the plugin code. 
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wfFedexRequest{
	public function __construct() {
		$this->id                               = WF_Fedex_ID;
		$this->rateservice_version              = 16;
		$this->ship_service_version              = 15;
		$this->addressvalidationservice_version = 2;
		$this->init();		
	}
	
	private function init() {		
		$this->settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		
		
		$this->account_number  				 = isset ( $this->settings[ 'account_number' ] ) ? $this->settings[ 'account_number' ] : '';
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
	}
	
	public function get_shipping_charges_payment(){
		$return	=	array();
		
		$acc_no=$this->shipping_charges_payment_type=='SENDER'?$this->account_number:$this->shipping_payor_acc_no;
		$return['PaymentType']					=	$this->shipping_charges_payment_type;
		if($this->shipping_charges_payment_type=='SENDER'){
			$return['Payor']['ResponsibleParty']=array(
				'AccountNumber'	=>	$this->account_number,
				'Address'		=>	array(
					'CountryCode'	=>	WC()->countries->get_base_country()
				)
			);
		}else{
			$return['Payor']['ResponsibleParty']=array(
				'AccountNumber'	=>	$acc_no,
				'Contact'		=>	array(
					'PersonName'	=> $this->shipping_payor_cname,
					'CompanyName'	=> $this->shipping_payor_company,
					'PhoneNumber'	=> $this->shipping_payor_phone,
					'EMailAddress'	=> $this->shipping_payor_email,
				),
				'Address'		=>	array(
					'StreetLines'			=>	array($this->shipping_payor_address1,$this->shipping_payor_address2),
					//'StreetLines'			=>	$this->shipping_payor_address2,
					'City'					=>	$this->shipping_payor_city,
					'StateOrProvinceCode'	=>	$this->shipping_payor_state,
					'PostalCode'			=>	$this->shipping_payor_postal_code,
					'CountryCode'			=>	$this->shipping_payor_country,
				)
			);
		}
		
		return $return;
	}
}