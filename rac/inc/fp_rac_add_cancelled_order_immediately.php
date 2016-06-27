<?php

class FPRacCancelledOrder{
    
    
    public function __construct(){
        add_action('woocommerce_order_status_cancelled',array($this,'add_cancelled_order_immediately_to_cart_list_as_abandoned'));
    }
    
    public static function add_cancelled_order_immediately_to_cart_list_as_abandoned($order_id){
        global $wpdb;
        $table_name = $wpdb->prefix . 'rac_abandoncart';
        $order = new WC_Order($order_id);
        $cart_details = maybe_serialize($order);
        $user_id = 'old_order';
        if ($order->user_id != '') {
            $user_email = $order->billing_email;
        } else {
            $user_email = $order->billing_email;
        }
        $order_modified_time = strtotime($order->modified_date);
        $wpdb->insert($table_name, array('cart_details' => $cart_details, 'user_id' => $user_id, 'email_id' => $user_email, 'cart_abandon_time' => $order_modified_time, 'cart_status' => 'ABANDON'), array('%s', '%s', '%d', '%s'));
    }
}

new FPRacCancelledOrder();