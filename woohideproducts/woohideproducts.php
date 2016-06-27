<?php
/*
 * Plugin Name: WooCommerce Hide Products
 * Plugin URI: http://codecanyon.net/user/codewoogeek
 * Description: WooCommerce Hide Shop Products can able to hide shop products based on User Role
 * Version: 3.3
 * Author: codewoogeek
 * Author URI: http://codecanyon.net/user/codewoogeek
 */

if (!defined('ABSPATH')) {
    exit;
}

function check_woocommerce_is_active() {
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        return false;
    }
}

add_action('init', 'cwg_avoid_header_sent_problem');

function cwg_avoid_header_sent_problem() {
    ob_start();
}

function cwg_hide_woo_products($where, $query) {
    global $wpdb;
    if (!is_admin()) {
        //var_dump($query);
        $product = $query->query_vars['post_type'];
        $product_category_check = isset($query->query_vars['taxonomy']) ? $query->query_vars['taxonomy'] : null;
        //var_dump($product_category_check);
        if ($product == 'product' || isset($product_category_check) || $product_category_check == 'product_cat') {
            if (is_user_logged_in()) {
                $get_role = cwg_get_user_role_from_id(get_current_user_id());
                $get_settings = cwg_get_options_value_from_role($get_role);
            } else {
                $get_role = "guest";
                $get_settings = cwg_get_options_value_from_role($get_role);
            }
            if ($get_settings['type'] == 'products') {
                $arrayofids = array_filter((array) $get_settings['productids']);
               
                if (!empty($arrayofids)) {
                    $arrayofids = join(',', $arrayofids);
                    $visibility = $get_settings['visibility'];
                    if ($visibility == 'include') {
                        $where .= " AND `post_type`='product' AND `ID` IN ($arrayofids)";
                    } else {
                        $where .= " AND `post_type`='product' AND `ID` NOT IN ($arrayofids)";
                    }
                }
            } else {

                $array_of_category_ids = array_filter((array) $get_settings['categoryids']);

                if (!empty($array_of_category_ids)) {
                    $array_of_category_ids = join(',', $array_of_category_ids);
                    $visibility = $get_settings['visibility'];

                    if ($visibility == 'include') {
                        $where .= " AND ID IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ($array_of_category_ids) )";
                    } else {
                        $where .= " AND ID NOT IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ($array_of_category_ids) )";
                    }
                }
            }
        }
    }
    return $where;
}

add_filter('posts_where_paged', 'cwg_hide_woo_products', 10, 2);

function cwg_get_user_role_from_id($user_id) {
    $get_user_data = get_userdata($user_id);
    return $get_user_data->roles[0];
}

function cwg_get_options_value_from_role($role) {
    $array_structure = array(
        'type' => get_option('woo_toggle_products_by_type_' . $role) == '1' ? 'products' : 'categories',
        'visibility' => get_option('woo_include_exclude_products_' . $role) == '1' ? 'include' : 'exclude',
        'productids' => get_option('woo_select_products_' . $role),
        'categoryids' => get_option('woo_toggle_type_category_' . $role),
    );
    return $array_structure;
}

function cwg_common_function_to_show_hide($data) {
    ob_start();
    ?>
    var alter_data = jQuery('#woo_toggle_products_by_type_<?php
    echo $data;
    ?>').val();
    if(alter_data==='1') {
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().show();

    }else {

    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().show();
    }
    jQuery('#woo_toggle_products_by_type_<?php
    echo $data;
    ?>').change(function(){
    var current_data = jQuery(this).val();
    if(current_data==='1') {
    // Hide the Category alone here
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().show();

    }else {
    // Hide the Products and Category Checkbox  (for category it is useless)
    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().show();

    }
    });
    <?php
    return ob_get_clean();
}

function cwg_load_script_to_admin() {
    global $woocommerce;
//var_dump($woocommerce->version);
    if (isset($_GET['tab'])) {
        if ($_GET['tab'] == 'woocommerce_hide_products') {
            ?>
            <script type="text/javascript">
                jQuery(function () {
            <?php
            global $wp_roles;
            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }
            $getdata = $wp_roles->get_names();
            if ((float) $woocommerce->version > "2.2.0") {

                $k = 0;
                foreach ($getdata as $data => $key) {

                    if ($k == 0) {
                        ?>
                                //                                jQuery('#woo_select_products_guest').select2();
                                jQuery('#woo_toggle_type_category_guest').select2();

                        <?php
                        echo cwg_common_function_to_show_hide('guest');
                    }

                    echo cwg_common_function_to_show_hide($data);
                    ?>


                            //                            jQuery('#woo_select_products_<?php
                    echo $data;
                    ?>//').select2();
                            jQuery('#woo_toggle_type_category_<?php
                    echo $data;
                    ?>').select2();
                    <?php
                    $k++;
                }
            } else {
                $k = 0;
                foreach ($getdata as $data => $key) {
                    if ($k == 0) {
                        ?>
                                //jQuery('#woo_select_products_guest').chosen();
                                jQuery('#woo_toggle_type_category_guest').chosen();

                        <?php
                        echo cwg_common_function_to_show_hide('guest');
                    }
                    echo cwg_common_function_to_show_hide($data);
                    ?>
                            //                            jQuery('#woo_select_products_<?php
                    echo $data;
                    ?>//').chosen();
                            jQuery('#woo_toggle_type_category_<?php
                    echo $data;
                    ?>').chosen();
                    <?php
                    $k++;
                }
            }
            ?>
                });
            </script>
            <?php
        }
    }
}

add_action('admin_head', 'cwg_load_script_to_admin');

include_once('inc/class-admin-settings.php');
