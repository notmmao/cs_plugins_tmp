<?php
class wf_fedex_woocommerce_shipping_admin{
	
	public function __construct(){
		$this->settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		$this->weight_dimensions_manual = 'no';
		$this->custom_services = $this->settings['services'];
		$this->image_type = $this->settings['image_type'];
        $this->debug = ( $bool = $this->settings[ 'debug' ] ) && $bool == 'yes' ? true : false;
		
		
		if (is_admin()) {
			add_action('add_meta_boxes', array($this, 'wf_add_fedex_metabox'));
		}
		if (isset($_GET['wf_fedex_createshipment'])) {
			add_action('init', array($this, 'wf_fedex_createshipment'));
		}
		
		if (isset($_GET['wf_fedex_additional_label'])) {
			add_action('init', array($this, 'wf_fedex_additional_label'));
		}
		
		if (isset($_GET['wf_fedex_viewlabel'])) {
			add_action('init', array($this, 'wf_fedex_viewlabel'));
		}
		
		if (isset($_GET['wf_fedex_void_shipment'])) {
			add_action('init', array($this, 'wf_fedex_void_shipment'));
		}
		
		if (isset($_GET['wf_clear_history'])) {
			add_action('init', array($this, 'wf_clear_history'));
		}		
	}
	
	public function wf_clear_history(){
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) 			
			return;
			
		$order_id = base64_decode($_GET['wf_clear_history']);
		
		if(empty($order_id))
			return;
		
		$void_shipments = get_post_meta($order_id, 'wf_woo_fedex_shipment_void',false);
		if(empty($void_shipments))
			return;
		
		foreach($void_shipments as $void_shipment_id){
			delete_post_meta($order_id, 'wf_woo_fedex_packageDetails_'.$void_shipment_id);
			delete_post_meta($order_id, 'wf_woo_fedex_shippingLabel_'.$void_shipment_id);
			delete_post_meta($order_id, 'wf_woo_fedex_shipmentId',$void_shipment_id);		
			delete_post_meta($order_id, 'wf_woo_fedex_shipment_void',$void_shipment_id);	
			delete_post_meta($order_id, 'wf_fedex_additional_label_',$void_shipment_id);			
		}
		
		delete_post_meta($order_id, 'wf_woo_fedex_shipment_void_errormessage');		
		delete_post_meta($order_id, 'wf_woo_fedex_service_code');
		delete_post_meta($order_id, 'wf_woo_fedex_shipmentErrorMessage');		
					
		if ( $this->debug ) {
            //dont redirect when debug is printed
            die();
		}
        else{
            wp_redirect(admin_url('/post.php?post='.$order_id.'&action=edit'));
		    exit;    
        }        
	}	
	
	public function wf_fedex_void_shipment(){
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) 			
			return;
			
		$void_params = explode('||', base64_decode($_GET['wf_fedex_void_shipment']));
		
		if(empty($void_params) || !is_array($void_params) || count($void_params) != 2)
			return;
		
		$shipment_id = $void_params[0]; 
		$order_id =  $void_params[1];			
			
			
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
			include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
		
		$woofedexwrapper = new wf_fedex_woocommerce_shipping_admin_helper();
		$tracking_completedata = get_post_meta($order_id, 'wf_woo_fedex_tracking_full_details_'.$shipment_id, true);
		if(!empty($tracking_completedata)){
			$woofedexwrapper->void_shipment($order_id,$shipment_id,$tracking_completedata);
		}
		
        if ( $this->debug ) {
            //dont redirect when debug is printed
            die();
		}
        else{
		  wp_redirect(admin_url('/post.php?post='.$order_id.'&action=edit'));
		  exit;
        }
	}
	
	private function wf_load_order($orderId){
		if (!class_exists('WC_Order')) {
			return false;
		}
		return new WC_Order($orderId);      
	}
	
	private function wf_user_permission(){
		// Check if user has rights to generate invoices
		$current_user = wp_get_current_user();
		$user_ok = false;
		if ($current_user instanceof WP_User) {
			if (in_array('administrator', $current_user->roles) || in_array('shop_manager', $current_user->roles)) {
				$user_ok = true;
			}
		}
		return $user_ok;
	}
	
	public function wf_fedex_createshipment(){
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) 			
			return;
		
		$order = $this->wf_load_order($_GET['wf_fedex_createshipment']);
		if (!$order) 
			return;
		
		$this->wf_create_shipment($order);

        if ( $this->debug ) {
            //dont redirect when debug is printed
            die();
		}
        else{
		  wp_redirect(admin_url('/post.php?post='.$_GET['wf_fedex_createshipment'].'&action=edit'));
		  exit;
        }
	}
	
	public function wf_fedex_viewlabel(){
		$shipmentDetails = explode('|', base64_decode($_GET['wf_fedex_viewlabel']));

		if (count($shipmentDetails) != 2) {
			exit;
		}
		
		$shipmentId = $shipmentDetails[0]; 
		$post_id = $shipmentDetails[1]; 
		$shipping_label = get_post_meta($post_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
		header('Content-Type: application/'.$this->image_type);
		header('Content-disposition: attachment; filename="ShipmentArtifact-' . $shipmentId . '.'.$this->image_type.'"');
		print(base64_decode($shipping_label)); 
		exit;
	}
	
	public function wf_fedex_additional_label(){
		$shipmentDetails = explode('|', base64_decode($_GET['wf_fedex_additional_label']));

		if (count($shipmentDetails) != 3) {
			exit;
		}
		
		$shipmentId = $shipmentDetails[0]; 
		$post_id = $shipmentDetails[1];
		$add_key = $shipmentDetails[2];		
		$additional_labels = get_post_meta($post_id, 'wf_fedex_additional_label_'.$shipmentId, true);
		if(!empty($additional_labels) && isset($additional_labels[$add_key])){
			header('Content-Type: application/'.$this->image_type);
			header('Content-disposition: attachment; filename="Addition-doc-'. $add_key . '-' . $shipmentId . '.'.$this->image_type.'"');
			print(base64_decode($additional_labels[$add_key])); 
		}
		exit;		
	}
	
	private function wf_is_service_valid_for_country($order,$service_code){
		$exception_list = array('FEDEX_GROUND','FEDEX_FREIGHT_ECONOMY','FEDEX_FREIGHT_PRIORITY');
		$exception_country = array('US','CA');
		if(in_array($order->shipping_country,$exception_country) && in_array($service_code,$exception_list)){
			return true;
		}
		
		if($order->shipping_country == WC()->countries->get_base_country()){
			return strpos($service_code, 'INTERNATIONAL_') === false;
		}
		else{
			return strpos($service_code, 'INTERNATIONAL_') !== false;
		}
		return false; 
	}

	private function wf_get_shipping_service($order,$retrive_from_order = false){
		
		if($retrive_from_order == true){
			$service_code = get_post_meta($order->id, 'wf_woo_fedex_service_code', true);
			if(!empty($service_code)) return $service_code;
		}
		
		if(!empty($_GET['fedex_shipping_service'])){			
			return $_GET['fedex_shipping_service'];
		}
			
		//TODO: Take the first shipping method. It doesnt work if you have item wise shipping method
		$shipping_methods = $order->get_shipping_methods();
		
		if ( ! $shipping_methods ) {
			return '';
		}
	
		$shipping_method = array_shift($shipping_methods);

		return str_replace(WF_Fedex_ID.':', '', $shipping_method['method_id']);
	}
	
	public function wf_create_shipment($order){		
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
			include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
		
		$woofedexwrapper = new wf_fedex_woocommerce_shipping_admin_helper();
		$serviceCode = $this->wf_get_shipping_service($order,false);
		
		$woofedexwrapper->print_label($order,$serviceCode,$order->id);		
	}
	
	public function wf_add_fedex_metabox(){
		global $post;
		if (!$post) {
			return;
		}
		
		$order = $this->wf_load_order($post->ID);
		if (!$order) 
			return;
		
		add_meta_box('wf_fedex_metabox', __('Fedex', 'wf-shipping-fedex'), array($this, 'wf_fedex_metabox_content'), 'shop_order', 'side', 'default');
	}

	public function wf_fedex_metabox_content(){
		global $post;
		
		if (!$post) {
			return;
		}

		$order = $this->wf_load_order($post->ID);
		if (!$order) 
			return;			

		$shipmentIds = get_post_meta($order->id, 'wf_woo_fedex_shipmentId', false);
		$shipment_void_ids = get_post_meta($order->id, 'wf_woo_fedex_shipment_void', false);
		
		$shipmentErrorMessage = get_post_meta($order->id, 'wf_woo_fedex_shipmentErrorMessage',true);
		$shipment_void_error_message = get_post_meta($order->id, 'wf_woo_fedex_shipment_void_errormessage',true);
		
		//Only Display error message if the process is not complete. If the Invoice link available then Error Message is unnecessary
		if(!empty($shipmentErrorMessage))
		{
			echo '<div class="error"><p>' . sprintf( __( 'Fedex Create Shipment Error:%s', 'wf-shipping-fedex' ), $shipmentErrorMessage) . '</p></div>';
		}

		if(!empty($shipment_void_error_message)){
			echo '<div class="error"><p>' . sprintf( __( 'Void Shipment Error:%s', 'wf-shipping-fedex' ), $shipment_void_error_message) . '</p></div>';
		}			
		echo '<ul>';
		$selected_sevice = $this->wf_get_shipping_service($order,true);	
		if (!empty($shipmentIds)) {
			if(!empty($selected_sevice))
				echo "<li>Shipping Service: <strong>$selected_sevice</strong></li>";		
			
			foreach($shipmentIds as $shipmentId) {
				echo '<li><strong>Shipment #:</strong> '.$shipmentId;
				$usps_trackingid = get_post_meta($order->id, 'wf_woo_fedex_usps_trackingid_'.$shipmentId, true);
				if(!empty($usps_trackingid)){
					echo "<br><strong>USPS Tracking #:</strong> ".$usps_trackingid;
				}
				if((is_array($shipment_void_ids) && in_array($shipmentId,$shipment_void_ids))){
					echo "<br> This shipment $shipmentId is terminated.";
				}
				echo '<hr>';
				$packageDetailForTheshipment = get_post_meta($order->id, 'wf_woo_fedex_packageDetails_'.$shipmentId, true);
				if(!empty($packageDetailForTheshipment)){
					foreach($packageDetailForTheshipment as $dimentionKey => $dimentionValue){
						if($dimentionValue){
							echo '<strong>' . $dimentionKey . ': ' . '</strong>' . $dimentionValue ;
							echo '<br />';
						}						
					}
					echo '<hr>';
				}
				$shipping_label = get_post_meta($post->ID, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
				if(!empty($shipping_label)){
					$download_url = admin_url('/post.php?wf_fedex_viewlabel='.base64_encode($shipmentId.'|'.$post->ID));?>
					<a class="button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Print Label', 'wf-shipping-fedex'); ?>"><?php _e('Print Label', 'wf-shipping-fedex'); ?></a>
					<?php 
				}				
				$additional_labels = get_post_meta($post->ID, 'wf_fedex_additional_label_'.$shipmentId, true);
				if(!empty($additional_labels) && is_array($additional_labels)){
					foreach($additional_labels as $additional_key => $additional_label){
						$download_add_label_url = admin_url('/post.php?wf_fedex_additional_label='.base64_encode($shipmentId.'|'.$post->ID.'|'.$additional_key));?>
						<a class="button tips" href="<?php echo $download_add_label_url; ?>" data-tip="<?php _e('Additional Label', 'wf-shipping-fedex'); ?>"><?php _e('Additional Label', 'wf-shipping-fedex'); ?></a>
						<?php
					}		
				}
				if((!is_array($shipment_void_ids) || !in_array($shipmentId,$shipment_void_ids))){
					$void_shipment_link = admin_url('/post.php?wf_fedex_void_shipment=' . base64_encode($shipmentId.'||'.$post->ID));?>				
					<a class="button tips" href="<?php echo $void_shipment_link; ?>" data-tip="<?php _e('Void Shipment', 'wf-shipping-fedex'); ?>"><?php _e('Void Shipment', 'wf-shipping-fedex'); ?></a>
					<?php 
				}				
				echo '<hr style="border-color:#0074a2"></li>';
			} ?>		
			<?php 
			if(count($shipmentIds) == count($shipment_void_ids)){
				$clear_history_link = admin_url('/post.php?wf_clear_history=' . base64_encode($post->ID));?>				
					<a class="button button-primary tips" href="<?php echo $clear_history_link; ?>" data-tip="<?php _e('Clear History', 'wf-shipping-fedex'); ?>"><?php _e('Clear History', 'wf-shipping-fedex'); ?></a>
					<?php 
			}					
		}
		else {			 
			$generate_url = admin_url('/post.php?wf_fedex_createshipment='.$post->ID);
			echo '<li>'.__('Choose Service:').'<select class="select" id="fedex_manual_service">';
			foreach($this->custom_services as $service_code => $service){
				if($service['enabled'] == true && $this->wf_is_service_valid_for_country($order,$service_code) == true){
					echo '<option value="'.$service_code.'" ' . selected($selected_sevice,$service_code) . ' >'.$service_code.'</option>';
				}	
			}
			echo '</select></li>';
			echo '<li><label for="wf_fedex_cod"><input type="checkbox" style="" id="wf_fedex_cod" name="wf_fedex_cod" class="">' . __('Cash On Delivery', 'wf-shipping-fedex') . '</label></li>';
			if($this->weight_dimensions_manual == 'yes'){
				$dimension_unit = strtolower( get_option( 'woocommerce_dimension_unit' ));
				$weight_unit = strtolower(strtolower( get_option('woocommerce_weight_unit') ));?>
				<li><strong>Weight:&nbsp;</strong><input type="text" id="fedex_manual_weight" size="3" />&nbsp;<?=$weight_unit;?><br>								
				<strong>&nbsp;Height:&nbsp;</strong><input type="text" id="fedex_manual_height" size="3" />&nbsp;<?=$dimension_unit;?><br>
				<strong>&nbsp;&nbsp;Width:&nbsp;</strong><input type="text" id="fedex_manual_width" size="3" />&nbsp;<?=$dimension_unit;?><br>
				<strong>Length:&nbsp;</strong><input type="text" id="fedex_manual_length" size="3" />&nbsp;<?=$dimension_unit;?>	
				</li>				
			<?php }?>
			
				<li><a class="button tips onclickdisable create_shipment" href="<?php echo $generate_url; ?>" data-tip="<?php _e('Create Shipment', 'wf-shipping-fedex'); ?>"><?php _e('Create Shipment', 'wf-shipping-fedex'); ?></a></li>
			<?php
		}
		echo '</ul>';?>
		<script>
		jQuery("a.create_shipment").one("click", function() {
			
			jQuery(this).click(function () { return false; });
			
			location.href = this.href + '&weight=' + jQuery('#fedex_manual_weight').val() + 
			'&length=' + jQuery('#fedex_manual_length').val()
			+ '&width=' + jQuery('#fedex_manual_width').val()
			+ '&height=' + jQuery('#fedex_manual_height').val()
			+ '&cod=' + jQuery('#wf_fedex_cod').is(':checked')
			+ '&fedex_shipping_service=' + jQuery('#fedex_manual_service').val();
			return false;			
		});
		</script>		
		<?php
	}	
}
new wf_fedex_woocommerce_shipping_admin();
?>
