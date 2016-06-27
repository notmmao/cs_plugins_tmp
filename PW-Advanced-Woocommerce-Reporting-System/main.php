<?php
/*
Plugin Name: PW Advanced Woo Reporting
Plugin URI: http://proword.net/Advanced_Reporting/
Description: WooCommerce Advance Reporting plugin is a comprehensive and the most complete reporting system.
Version: 2.2
Author: Proword.net
Author URI: http://proword.net/
Text Domain: pw_report_wcreport_textdomain
Domain Path: /languages/
*/

if(!class_exists('pw_report_wcreport_class')){

	//USE IN INCLUDE
	define( '__PW_REPORT_WCREPORT_ROOT_DIR__', dirname(__FILE__));
	
	//USE IN ENQUEUE AND IMAGE
	define( '__PW_REPORT_WCREPORT_CSS_URL__', plugins_url('assets/css/',__FILE__));
	define( '__PW_REPORT_WCREPORT_JS_URL__', plugins_url('assets/js/',__FILE__));
	define ('__PW_REPORT_WCREPORT_URL__',plugins_url('', __FILE__));
	
	//PERFIX
	define ('__PW_REPORT_WCREPORT_FIELDS_PERFIX__', 'custom_report_' );
	
	//TEXT DOMAIN FOR MULTI LANGUAGE
	define ('__PW_REPORT_WCREPORT_TEXTDOMAIN__', 'pw_report_wcreport_textdomain' );
	
	include('includes/datatable_generator.php');
	
	//CLASS FOR ENQUEUE SCRIPTS AND STYLES
	class pw_report_wcreport_class extends pw_rpt_datatable_generate{
		
		public $pw_plugin_status='';
				
		function __construct(){
			include('includes/actions.php');
			
			add_action('admin_head',array($this,'pw_report_backend_enqueue'));
			add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ) );
			add_action('admin_menu', array( $this,'pw_report_setup_menus'));
			
			$field=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code';
			$this->pw_plugin_status=get_option($field);
			
		} 

		
		function pw_report_backend_enqueue(){
			if(isset($_GET['parent']))
			{
				include ("includes/admin-embed.php");
			}
		}	
		
		function loadTextDomain() {
			load_plugin_textdomain( 'pw_report_wcreport_textdomain' , false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
		}
		
		function pw_report_setup_menus() {
			
			global $submenu;
			add_menu_page(__('Woo Reporting',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Woo Reporting',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_dashboard&parent=dashboard',  array($this,'wcx_plugin_dashboard'),'dashicons-chart-pie' );
			
			add_submenu_page(null, __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_dashboard',  array($this,'wcx_plugin_dashboard' ));
			
			add_submenu_page(null, __('My Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('My Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_plugin_menu_my_dashboard',  array($this,'wcx_plugin_menu_my_dashboard' ));
			
			add_submenu_page(null, __('Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_details',   array($this,'wcx_plugin_menu_details' ) );		
			//ALL DETAILS
			add_submenu_page(null, __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_product',   array($this,'wcx_plugin_menu_product' ) );		
			add_submenu_page(null, __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_category',   array($this,'wcx_plugin_menu_category' ) );
			add_submenu_page(null, __('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_customer',   array($this,'wcx_plugin_menu_customer' ) );	
			add_submenu_page(null, __('Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_billingcountry',   array($this,'wcx_plugin_menu_billingcountry' ) );	
			add_submenu_page(null, __('Billing State',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Billing State',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_billingstate',   array($this,'wcx_plugin_menu_billingstate' ) );
			add_submenu_page(null, __('Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_paymentgateway',   array($this,'wcx_plugin_menu_paymentgateway' ) );
			add_submenu_page(null, __('Order Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Order Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_orderstatus',   array($this,'wcx_plugin_menu_orderstatus' ) );
			add_submenu_page(null, __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_recentorder',   array($this,'wcx_plugin_menu_recentorder' ) );
			add_submenu_page(null, __('Tax Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Tax Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_taxreport',   array($this,'wcx_plugin_menu_taxreport' ) );
			add_submenu_page(null, __('Customer Buy Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Customer Buy Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_customrebuyproducts',   array($this,'wcx_plugin_menu_customrebuyproducts' ) );
			add_submenu_page(null, __('Refund Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Refund Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_refunddetails',   array($this,'wcx_plugin_menu_refunddetails' ) );
			add_submenu_page(null, __('Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_coupon',   array($this,'wcx_plugin_menu_coupon' ) );
			//////////////////////////////////////////////
			//////////////////////
			//////////////////////////////////////////////
			//CROSS TAB
			
			//////////////////////////////////////////////
			//////////////////////
			//////////////////////////////////////////////
			//VARIATION
			add_submenu_page(null, __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_stock_list',   array($this,'wcx_plugin_menu_stock_list' ) );
			//STOCK VARIATION
			add_submenu_page(null, __('Target Sale vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Target Sale vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_projected_actual_sale',   array($this,'wcx_plugin_menu_projected_actual_sale' ) );
			add_submenu_page(null, __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_tax_reports',   array($this,'wcx_plugin_menu_tax_reports' ) );	
			
			/////////////////////////////
			//SETTINGS
			/////////////////////////////////
			add_submenu_page(null, __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Report Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_setting_report',   array($this,'wcx_plugin_menu_setting_report' ) );
			
			add_submenu_page(null, __('Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Report Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_addons_report',   array($this,'wcx_plugin_menu_addons_report' ) );
			
			add_submenu_page(null, __('Proword',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Other Useful Plugins',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_proword_report',   array($this,'wcx_plugin_menu_proword_report' ) );	
			
			add_submenu_page(null, __('Activate Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Active Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__), 'manage_options', 'wcx_wcreport_plugin_active_report',   array($this,'wcx_plugin_menu_active_report' ) );	
			
			//CUSTOMIZE MENUS
			do_action( 'pw_report_wcreport_admin_menu' );
			
		}
		
		function wcx_plugin_dashboard($display="all"){
			$this->pages_fetch("dashboard_report.php",$display);
		}
		
		function wcx_plugin_menu_my_dashboard(){
			$this->pages_fetch("reports.php");
		}
		
		//Details
		function wcx_plugin_menu_details(){
			$this->pages_fetch("details.php");
		}
		
		//////////////////////ALL DETAILS//////////////////////
		
		function pages_fetch($page,$display="all"){
			
			$visible_menu=array(
				
				array(
					"parent" => "main",
					"childs" => array(
						array(
							"label" => __('All Menus',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "all_menu",
							"link" => "#",
							"icon" => "fa-bars",
						),
						array(
							"label" => __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "dashboard",
							"link" => "admin.php?page=wcx_wcreport_plugin_dashboard&parent=dashboard",
							"icon" => "fa-bookmark",
						),
						array(
							"label" => __('All Orders',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "all_orders",
							"link" => "admin.php?page=wcx_wcreport_plugin_details&parent=all_orders",
							"icon" => "fa-file-text",
						),
						array(
							"label" => __('More Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "more_reports",
							"link" => "#",
							"icon" => "fa-files-o",
							"submenu_id" => "more_reports",
						),
						//CROSSTAB
						//VARIATION
						array(
							"label" => __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "stock_list",
							"link" => "admin.php?page=wcx_wcreport_plugin_stock_list&parent=stock_list",
							"icon" => "fa-cart-arrow-down",
						),
						//VARIATION STOCK 
						array(
							"label" => __('Target Sale vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "proj_actual_sale",
							"link" => "admin.php?page=wcx_wcreport_plugin_projected_actual_sale&parent=proj_actual_sale",
							"icon" => "fa-calendar-check-o",
						),
						array(
							"label" => __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "tax_reports",
							"link" => "admin.php?page=wcx_wcreport_plugin_tax_reports&parent=tax_reports",
							"icon" => "fa-pie-chart",
						),
						array(
							"label" => __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "setting",
							"link" => "admin.php?page=wcx_wcreport_plugin_setting_report&parent=setting",
							"icon" => "fa-cogs",
						),
						array(
							"label" => __('Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "addons",
							"link" => "admin.php?page=wcx_wcreport_plugin_addons_report&parent=addons",
							"icon" => "fa-plug",
						),
						array(
							"label" => __('Proword',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "proword",
							"link" => "admin.php?page=wcx_wcreport_plugin_proword_report&parent=proword",
							"icon" => "fa-product-hunt",
						),
					)
 				),
				array(
					"parent" => "more_reports",
					"childs" => array(
						array(
							"label" => __("Product" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "product",
							"link" => "admin.php?page=wcx_wcreport_plugin_product&parent=more_reports&product",
							"icon" => "fa-cog",
						),
						array(
							"label" => __("Category" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "category",
							"link" => "admin.php?page=wcx_wcreport_plugin_category&parent=more_reports&category",
							"icon" => "fa-tags",
						),
						array(
							"label" => __("Customer" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "customer",
							"link" => "admin.php?page=wcx_wcreport_plugin_customer&parent=more_reports&customer",
							"icon" => "fa-user",
						),
						array(
							"label" => __("Billing Country" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "billing_country",
							"link" => "admin.php?page=wcx_wcreport_plugin_billingcountry&parent=more_reports&billing_country",
							"icon" => "fa-globe",
						),
						array(
							"label" => __("Billing State" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "billing_state",
							"link" => "admin.php?page=wcx_wcreport_plugin_billingstate&parent=more_reports&billing_state",
							"icon" => "fa-map",
						),
						array(
							"label" => __("Payment Gateway" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "payment_gateway",
							"link" => "admin.php?page=wcx_wcreport_plugin_paymentgateway&parent=more_reports&payment_gateway",
							"icon" => "fa-credit-card",
						),
						array(
							"label" => __("Order Status" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "order_status",
							"link" => "admin.php?page=wcx_wcreport_plugin_orderstatus&parent=all_details&order_status",
							"icon" => "fa-check",
						),
						array(
							"label" => __("Recent Order" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "recent_order",
							"link" => "admin.php?page=wcx_wcreport_plugin_recentorder&parent=more_reports&recent_order",
							"icon" => "fa-shopping-cart",
						),
						array(
							"label" => __("Tax Report" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "tax_report",
							"link" => "admin.php?page=wcx_wcreport_plugin_taxreport&parent=more_reports&tax_report",
							"icon" => "fa-pie-chart",
						),
						array(
							"label" => __("Customer Buy Product" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "customer_buy_prod",
							"link" => "admin.php?page=wcx_wcreport_plugin_customrebuyproducts&parent=more_reports&customer_buy_prod",
							"icon" => "fa-users",
						),
						array(
							"label" => __("Refund Detail" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "product",
							"link" => "admin.php?page=wcx_wcreport_plugin_product&parent=more_reports&product",
							"icon" => "fa-eye-slash",
						),
						array(
							"label" => __("Coupon" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "coupon",
							"link" => "admin.php?page=wcx_wcreport_plugin_coupon&parent=more_reports&coupon",
							"icon" => "fa-hashtag",
						),
					)
 				),
			);
						
			include("class/pages_fetch_dashboards.php");
		}
		
		function dashboard($item_id){
			
			$username = 'proword'; 
			$api_key = 't0kbg3ez6pl5yo1ojhhoja9d64swh6wi';
			
			$item_valid_id='12042129'; //8218941
		
			//CHECK IF THE CALL FOR THE FUNCTION WAS EMPTY
			if ( $item_id != '' ):
				
				$api_url='http://marketplace.envato.com/api/edge/'.$username.'/'.$api_key.'/verify-purchase:'.$item_id.'.json';
				
				
				$response = wp_remote_get(  $api_url );
				
				/* Check for errors, if there are some errors return false */
				if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
					return false;
				}
				
				/* Transform the JSON string into a PHP array */
				$result = json_decode( wp_remote_retrieve_body( $response ), true );
				
				//print_r($result);
				if (isset($result['verify-purchase']['item_id']) && $result['verify-purchase']['item_id']==$item_valid_id && isset($result['verify-purchase']['item_name']) &&  $result['verify-purchase']['item_name'] ) :
					return $result;
					//return true;
				else:
					return false;
				endif;
			endif; 
			
		}
		
		
		//1-PRODUCTS
		function wcx_plugin_menu_product(){
			$this->pages_fetch("product.php");
		}
		//2-CATEGORY
		function wcx_plugin_menu_category(){
			$this->pages_fetch("category.php");
		}
		//3-CUSTOMER
		function wcx_plugin_menu_customer(){
			$this->pages_fetch("customer.php");
		}
		//4-BILLING COUNTRY
		function wcx_plugin_menu_billingcountry(){
			$this->pages_fetch("billingcountry.php");
		}
		//5-BILLING STATE
		function wcx_plugin_menu_billingstate(){
			$this->pages_fetch("billingstate.php");
		}
		//6-PAYMENT GATEWAY
		function wcx_plugin_menu_paymentgateway(){
			$this->pages_fetch("paymentgateway.php");
		}
		//7-ORDER STATUS
		function wcx_plugin_menu_orderstatus(){
			$this->pages_fetch("orderstatus.php");
		}
		//8-RECENT ORDER
		function wcx_plugin_menu_recentorder(){
			$this->pages_fetch("recentorder.php");
		}
		//9-TAX REPORT
		function wcx_plugin_menu_taxreport(){
			$this->pages_fetch("taxreport.php");
		}
		//10-CUSTOMER BUY PRODUCT
		function wcx_plugin_menu_customrebuyproducts(){
			$this->pages_fetch("customerbuyproducts.php");
		}
		//11-REFUND DETAILS
		function wcx_plugin_menu_refunddetails(){
			$this->pages_fetch("refunddetails.php");
		}
		//12-COUPON
		function wcx_plugin_menu_coupon(){
			$this->pages_fetch("coupon.php");
		}
		
		//////////////////////CROSS TABS//////////////////////
		
		//VARIATION		
		function wcx_plugin_menu_variation(){
			$this->pages_fetch("variation.php");
		}
		//STOCK LIST
		function wcx_plugin_menu_stock_list(){
			$this->pages_fetch("stock_list.php");
		}
		//VARIATION STOCK
		function wcx_plugin_menu_variation_stock(){
			$this->pages_fetch("variation_stock.php");
		}
		//PROJECTED VS ACTUAL SALE
		function wcx_plugin_menu_projected_actual_sale(){
			$this->pages_fetch("projected_actual_sale.php");
		}
		//TAX REPORT
		function wcx_plugin_menu_tax_reports(){
			$this->pages_fetch("tax_reports.php");
		}
		
		//SETTING
		function wcx_plugin_menu_setting_report(){
			$this->pages_fetch("setting_report.php");
		}
		
		//ADD-ONS
		function wcx_plugin_menu_addons_report(){
			$this->pages_fetch("addons_report.php");
		}
		
		//ADD-ONS
		function wcx_plugin_menu_proword_report(){
			$this->pages_fetch("advertise_other_plugins.php");
		}
		
		//ACTIVE
		function wcx_plugin_menu_active_report(){
			$this->pages_fetch("plugin_active.php");
		}
	}
	
	$GLOBALS['pw_rpt_main_class'] = new pw_report_wcreport_class;
	
	
	//THE PLUGIN PAGES IS CREATED IN THIS FILE
	//include('class/custommenu.php');
}
?>