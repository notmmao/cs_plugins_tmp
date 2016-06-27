<?php

/**
 * Plugin Name: Woocommerce Shipping Gateway per Product
 * Plugin URI: http://www.dreamfoxmedia.com/project/woocommerce-shipping-gateway-per-product-premium/ 
 * Version: 1.2.2	
 * Author: Dreamfox Media
 * Author URI: www.dreamfoxmedia.com 
 * Description: Extend Woocommerce plugin to add shipping methods to a product
 * Requires at least: 3.7
 * Tested up to: 4.3.1
 * @Developer : Hoang Xuan Hao / Marco van Loghum Slaterus ( Dreamfoxmedia )
 */

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
{
    add_action( 'admin_enqueue_scripts', 'softsdev_product_shippings_enqueue' );
    add_action('admin_menu','softdev_product_shippings_submenu_page');

    function softsdev_product_shippings_enqueue() {
        wp_enqueue_style( 'softsdev_pd_shippings_enqueue', plugin_dir_url( __FILE__ ) . '/css/style.css' );
    }

    function softdev_product_shippings_submenu_page() {
        add_submenu_page('woocommerce', __('Product Shippings', 'softsdev'), __('Product Shippings', 'softsdev'), 'manage_options', 'softsdev-product-shippings', 'softsdev_product_shippings_settings');
    }

    function softsdev_product_shippings_footer_text( $text ) {
        if(! empty( $_GET['page'] ) && strpos( $_GET['page'], 'softsdev-product-shippings' ) === 0 ) {
            $text = sprintf( 'If you enjoy using <strong>Woocommerce Shipping Gateway per Product</strong>, please <a href="%s" target="_blank">leave us a ★★★★★ rating</a>. A <strong style="text-decoration: underline;">huge</strong> thank you in advance!', 'https://wordpress.org/support/view/plugin-reviews/woocommerce-shipping-gateway-per-product' );
        }
        return $text;
    }

    function softdev_product_shippings_update_footer($text) {
        if(! empty( $_GET['page'] ) && strpos( $_GET['page'], 'softsdev-product-shippings' ) === 0 ) {
            $text = 'Version 1.2.2';
        }
        return $text;   
    }

    function softsdev_product_shippings_settings()
    {   
       add_filter( 'admin_footer_text', 'softsdev_product_shippings_footer_text' );
       add_filter( 'update_footer', 'softdev_product_shippings_update_footer' );
        ?>

        <?php 
        echo '<div class="wrap "><div id="icon-tools" class="icon32"></div>';
        echo '<h2 style="padding-bottom:15px; margin-bottom:20px; border-bottom:1px solid #ccc">' . __('Woocommerce Product Shippings per Product', 'softsdev') . '</h2>';
         ?>
        <div class="left-mc-setting">
            <p>This plugin for woocommerce lets you select the available shipping gateways for each individual product.
			You can select for eacht individual product the shipping gateway that will be used by checkout.
			If no selection is made, then the default shipping gateways are displayed.
			If you for example only select local delivery then only local delivery will available for that product by checking out.
			Works on latest Woocommerce version.
			This plugin allows you to improve your customer service by giving the best shipping service for your customers.</p>
		<p>This version is limited in features (you can only select gateways for 10 products). For a small fee you can get the Premium version with <a href="http://www.dreamfoxmedia.com/shop/woocommerce-shipping-gateway-per-product-premium/#utm_source=plugin&utm_medium=premium_left&utm_campaign=wcsgpp">no limitations</a>!</p>
       </div>
        <div class="right-mc-setting">
            <div style="border: 5px dashed #B0E0E6; padding: 0 20px; background: white;">
            <h3>Woocommerce Shipping Gateway per Product</h3>
                <p>This plugin has a Premium version with no limitations. <a href="http://www.dreamfoxmedia.com/shop/woocommerce-shipping-gateway-per-product-premium/#utm_source=plugin&utm_medium=premium_right&utm_campaign=wcpgpp">Have a look at its benefits</a>!</p>
            </div>

            <?php $user = wp_get_current_user(); ?>
            <!-- Begin paypal donation Form -->
            
             <form style="text-align:center; border-bottom:1px solid #ccc; border-top:1px solid #ccc; padding:20px 0; margin:20px 0;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <center>Like this product we’ve made and want to contribute to its future development? Donate however much you’d like with the below donate button.<br><br></center>	
			    <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="UNTLWQSLRH85U">
                <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
                <img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
            </form>

            
            <!-- Begin MailChimp Signup Form -->
            <link href="//cdn-images.mailchimp.com/embedcode/slim-081711.css" rel="stylesheet" type="text/css">
            <style type="text/css">
                #mc_embed_signup{/* background:#fff;  */clear:left; font:14px Helvetica,Arial,sans-serif; }
                /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
                   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
            </style>
            <div id="mc_embed_signup" style="border-bottom:1px solid #ccc">
            <form action="//dreamfoxmedia.us3.list-manage.com/subscribe/post?u=a0293a6a24c69115bd080594e&amp;id=131c5e0c11" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                <div id="mc_embed_signup_scroll">
                <label for="mce-EMAIL">Subscribe to our mailinglist</label>
                <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a0293a6a24c69115bd080594e_131c5e0c11" tabindex="-1" value=""></div>
                <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                </div>
            </form>
            </div>

            <div class="wcsgpp-box" >
                <h4 class="wcsgpp-title"><?php echo __( 'Looking for help?', 'woocommerce shipping per product' ); ?></h4>
                <p><?php echo __( 'We have some resources available to help you in the right direction.', 'woocommerce shipping per product' ); ?></p>
                <ul class="ul-square">
                    <li><a href="http://www.dreamfoxmedia.com/ufaq-category/wcsgpp-f/#utm_source=plugin&utm_medium=faq&utm_campaign=wcsgpp"><?php echo __( 'Knowledge Base', 'woocommerce shipping gateway per product' ); ?></a></li>
                    <li><a href="https://nl.wordpress.org/plugins/woocommerce-shipping-gateway-per-product/faq/"><?php echo __( 'Frequently Asked Questions', 'woocommerce shipping gateway per product' ); ?></a></li>
                </ul>
                <p><?php echo sprintf( __( 'If your answer can not be found in the resources listed above, please use the <a href="%s">support forums on WordPress.org</a>.' ), 'https://wordpress.org/support/plugin/woocommerce-shipping-gateway-per-product' ); ?></p>
                <p><?php echo sprintf( __( 'Found a bug? Please <a href="%s">open an issue on GitHub</a>.' ), 'https://github.com/dreamfoxnl/WC-Shipping-Gateway-per-Product-Free/issues' ); ?></p>
            </div>

        </div>
        
        <?php
    }



    add_action('add_meta_boxes', 'wps_ship_meta_box_add', 50);
    function wps_ship_meta_box_add()
    {
        add_meta_box('shippings', 'Shippings', 'wps_shipping_form', 'product', 'side', 'core');
    }

    function wps_shipping_form()
    {
        global $post, $woocommerce;
        $productIds = get_option('woocommerce_product_apply_ship');
        $postShippings = count(get_post_meta($post->ID, 'shippings', true)) ? get_post_meta($post->ID, 'shippings', true) : array();
        if (is_array($productIds))
        {
            foreach ($productIds as $key => $product) {
                if (!get_post($product) || !count(get_post_meta($product, 'shippings', true)))
                    unset($productIds[$key]);
            }
        }
        update_option('woocommerce_product_apply_ship', $productIds);
        $productIds = get_option('woocommerce_product_apply_ship');

        if (count($productIds) >= 10 && !count(get_post_meta($post->ID, 'shippings', true)))
        {
            echo 'Limit reached Please download full version package at www.dreamfoxmedia.com!';
            return;
        }

        if ($woocommerce->shipping->load_shipping_methods())
            foreach ($woocommerce->shipping->load_shipping_methods() as $key => $method) {
                if ($method->enabled == 'yes')
                    $shippings[$key] = $method;
            }

        foreach ($shippings as $ship) {
            $checked = '';
            if (is_array($postShippings) && in_array($ship->id, $postShippings))
                $checked = ' checked="checked" ';
            ?>  
            <input type="checkbox" <?php echo $checked; ?> value="<?php echo $ship->id; ?>" name="ship[]" id="ship_<?php echo $ship->id ?>" />
            <label for="ship_<?php echo $ship->id ?>"><?php echo $ship->title; ?></label>  
            <br />  
            <?php
        }
    }

    add_action('save_post', 'wps_ship_meta_box_save', 10, 2);
    function wps_ship_meta_box_save($post_id, $post)
    {
        // Restrict to save for autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        // Restrict to save for revisions
        if (isset($post->post_type) && $post->post_type == 'revision')
            return $post_id;
        if (isset( $_POST['post_type']) && $_POST['post_type'] == 'product' && isset( $_POST['ship'] ))
        {
            $productIds = get_option('woocommerce_product_apply_ship');
            if ( is_array( $productIds ) && !in_array( $post_id, $productIds ) )
            {
                $productIds[] = $post_id;
                update_option('woocommerce_product_apply_ship', $productIds);
            }
            //delete_post_meta($post_id, 'shippings');
            $shippings = array();
            if ($_POST['ship'])
            {
                foreach ($_POST['ship'] as $ship)
                    $shippings[] = $ship;
            }
            if (count($shippings))
                update_post_meta($post_id, 'shippings', $shippings);
            else
                delete_post_meta($post_id, 'shippings');
        }

    }

    function wps_shipping_method_disable_country($available_methods)
    {
        global $woocommerce;
        $_available_methods = $available_methods;
        $temp = array();
        $arrayKeys = array_keys($available_methods);
        if (count($woocommerce->cart))
        {
            $items = $woocommerce->cart->cart_contents;
            $itemsShips = '';
            if ( is_array( $items ) )
            {
                foreach ( $items as $item ) {
                    $itemsShips = get_post_meta( $item['product_id'], 'shippings', true );
                    if ( !empty( $itemsShips ) )
                    {
                        foreach ( $arrayKeys as $key ) {
                            if( array_key_exists( $key, $available_methods ) ){
                                $method_id = $available_methods[$key]->method_id;
                                if ( !empty( $method_id ) && !in_array( $method_id, $itemsShips ) )
                                {
                                    unset( $available_methods[$key] );
                                }
                            }
                        }
                        $temp = array_merge( $temp, $itemsShips );
                    }
                }
            }
        }
        // Calculatting max shipping
        $maxcost_shipping = array();
        $max_cost = -1;
        foreach ( $_available_methods as $key => $available_method ) {
            if( array_key_exists( $key, $_available_methods ) ){
                $method_id = $available_method->method_id;
                if ( $available_method->cost > $max_cost && in_array( $method_id, $temp ) )
                {
                    $max_cost = $available_method->cost;
                    $maxcost_shipping = array($key => $available_method);
                }
            }
        }    
        // Showing Max value shipping
        if (count($available_methods))
        {
            return $available_methods;
        } else
        {
            return count($maxcost_shipping) ? $maxcost_shipping : $available_methods;
        }
    }

    // update new filter as depricated woocommerce_available_shipping_methods
    // 
    add_filter('woocommerce_package_rates', 'wps_shipping_method_disable_country', 99);
    function update_user_database()
    {
        $is_shipping_updated = get_option('is_shipping_updated');
        if (!$is_shipping_updated)
        {
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'product',
                'fields' => 'ids'
            );
            $products = get_posts($args);
            foreach ($products as $pro_id) {
                $itemsShips = get_post_meta($pro_id, 'shippings', true);
                if (empty($itemsShips))
                {
                    delete_post_meta($pro_id, 'shippings');
                }
            }
            update_option('is_shipping_updated', true);
        }
    }
    add_action('wp_head', 'update_user_database');
}

?>