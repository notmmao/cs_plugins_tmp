<?php
/*
 * create settings page for hide products woocommerce
 */
if (!defined('ABSPATH')) {
    exit;
}

function cwg_hide_product_settings_tab($settings_tab) {
    $settings_tab['woocommerce_hide_products'] = __('WooCommerce Hide Products', 'woohideproducts');
    return $settings_tab;
}

function cwg_hide_product_admin_options() {
    return apply_filters('woocommerce_toggle_product_settings', array(
        array(
            'name' => __('WooCommerce Hide Shop Products', 'woocommerce'),
            'type' => 'title',
            'desc' => '',
            'id' => '_woo_hide_products'
        ),
        array('type' => 'sectionend', 'id' => '_woo_hide_products'),
        array('type' => 'sectionend', 'id' => '_woo_hide_products_maincontent'),
    ));
}

function cwg_hide_product_default_values() {
    global $woocommerce;
    foreach (cwg_hide_product_admin_options() as $settings) {
        if (isset($settings['defaultvalue']) && ($settings['std'])) {
            add_option($settings['defaultvalue'], $settings['std']);
        }
    }
}

function cwg_hide_product_register_admin_fields() {
    woocommerce_admin_fields(cwg_hide_product_admin_options());
}

function cwg_hide_product_update_options() {
    woocommerce_update_options(cwg_hide_product_admin_options());
}

function cwg_hide_product_comprehensive_settings($settings) {

    $get_terms_product = get_terms('product_cat', array('hide_empty' => 'false'));
//var_dump($get_terms_product);
    $current_category_products = array();
    if (!empty($get_terms_product) && is_array($get_terms_product)) {
        foreach ($get_terms_product as $each_term) {
            $current_category_products[$each_term->term_id] = $each_term->name;
        }
    }

    $updated_settings = array();
    $mainvariable = array();
    foreach ($settings as $section) {
        if (isset($section['id']) && '_woo_hide_products_maincontent' == $section['id'] &&
                isset($section['type']) && 'sectionend' == $section['type']) {
            global $wp_roles;
            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }
            $getdata = $wp_roles->get_names();

            foreach ($getdata as $data => $key) {
                $updated_settings[] = array(
                    'name' => __('Hide Products for ' . $key, 'woohideproducts'),
                    'type' => 'title',
                    'id' => '_woo_hide_products_' . $data,
                );
                $updated_settings[] = array(
                    'name' => __('Hide by', 'woohideproducts'),
                    'desc' => __('Toggle Products by category or certain products', 'woohideproducts'),
                    'tip' => '',
                    'id' => 'woo_toggle_products_by_type_' . $data,
                    'css' => '',
                    'std' => '1',
                    'defaultvalue' => 'woo_toggle_products_by_type_' . $data,
                    'type' => 'select',
                    'options' => array('1' => __('Products', 'woohideproducts'), '2' => __('Categories', 'woohideproducts')),
                    'desc_tip' => true,
                );
                $updated_settings[] = array(
                    'name' => __('Choose Category', 'woohideproducts'),
                    'desc' => __('Want to hide products in a bulk way then using this option to hide the products', 'woohideproducts'),
                    'tip' => '',
                    'id' => 'woo_toggle_type_category_' . $data,
                    'css' => '',
                    'std' => '',
                    'defaultvalue' => 'woo_toggle_type_category_' . $data,
                    'type' => 'multiselect',
                    'options' => $current_category_products,
                );

                $updated_settings[] = array(
                    'name' => __('Select Product(s) to Hide', 'woohideproducts'),
                    'desc' => __('Select Product which will be hide it in site wide', 'woohideproducts'),
                    'tip' => '',
                    'id' => 'woo_select_products_' . $data,
                    'css' => '',
                    'std' => '',
                    'placeholder' => __('Select Products to Hide', 'woohideproducts'),
                    'type' => 'cwg_product_search',
                );

                $updated_settings[] = array(
                    'name' => __('Include/Exclude Product(s) or Categories', 'woohideproducts'),
                    'desc' => __('Toggle Include Products/Exclude Products in your site', 'woohideproducts'),
                    'tip' => '',
                    'id' => 'woo_include_exclude_products_' . $data,
                    'css' => '',
                    'std' => '2',
                    'defaultvalue' => 'woo_include_exclude_products_' . $data,
                    'type' => 'radio',
                    'options' => array('1' => __('Include', 'woohideproducts'), '2' => __('Exclude', 'woohideproducts')),
                    'desc_tip' => true,
                );



                $updated_settings[] = array(
                    'type' => 'sectionend', 'id' => '_woo_hide_products_' . $data,
                );
            }

            $updated_settings[] = array(
                'name' => __('Hide Products for Guest', 'woohideproducts'),
                'type' => 'title',
                'id' => '_woo_hide_products_guest',
            );
            $updated_settings[] = array(
                'name' => __('Hide by', 'woohideproducts'),
                'desc' => __('Toggle or Hide Products by category or certain products', 'woohideproducts'),
                'tip' => '',
                'id' => 'woo_toggle_products_by_type_guest',
                'css' => '',
                'std' => '1',
                'defaultvalue' => 'woo_toggle_products_by_type_guest',
                'type' => 'select',
                'options' => array('1' => __('Products', 'woohideproducts'), '2' => __('Categories', 'woohideproducts')),
                'desc_tip' => true,
            );

            $updated_settings[] = array(
                'name' => __('Choose Category', 'woohideproducts'),
                'desc' => __('Want to hide products in a bulk way then using this option to hide the products', 'woohideproducts'),
                'tip' => '',
                'id' => 'woo_toggle_type_category_guest',
                'css' => '',
                'std' => '',
                'defaultvalue' => 'woo_toggle_type_category_guest',
                'type' => 'multiselect',
                'options' => $current_category_products,
            );

            $updated_settings[] = array(
                'name' => __('Select Product(s) to Hide', 'woohideproducts'),
                'desc' => __('Select Product which will be Hide it in site wide', 'woohideproducts'),
                'tip' => '',
                'id' => 'woo_select_products_guest',
                'css' => '',
                'std' => '',
                'type' => 'cwg_product_search',
                'placeholder' => __('Select Product(s) to Hide', 'woohideproducts'),
            );
            $updated_settings[] = array(
                'name' => __('Include/Exclude Product(s) or Categories', 'woohideproducts'),
                'desc' => __('Toggle Include Products/Exclude Products in WooCommerce', 'woohideproducts'),
                'tip' => '',
                'id' => 'woo_include_exclude_products_guest',
                'css' => '',
                'std' => '2',
                'defaultvalue' => 'woo_include_exclude_products_guest',
                'type' => 'radio',
                'options' => array('1' => __('Include', 'woohideproducts'), '2' => __('Exclude', 'woohideproducts')),
                'desc_tip' => true,
            );


            $updated_settings[] = array(
                'type' => 'sectionend', 'id' => '_woo_hide_products_guest',
            );
        }

        $updated_settings[] = $section;
    }

    return $updated_settings;
}

function cwg_optimize_product_search($value) {
    global $woocommerce;
    // For the WooCommerce Latest Version
    ?>
    <tr valign="top">
        <th class="titledesc" scope="row">
            <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
        </th>
        <td class="forminp forminp-select">
            <?php if ((float) $woocommerce->version > (float) ('2.2.0')) { ?>
                <input type="hidden" class="wc-product-search" id="<?php echo $value['id']; ?>"  style="width:330px;" name="<?php echo $value['id']; ?>" data-placeholder="<?php echo $value['placeholder']; ?>" data-action="woocommerce_json_search_products" data-multiple="true" data-selected="<?php
                $get_datas = array();
                $available_data = get_option($value['id']);
                if ($available_data) {
                    $ids = is_array($available_data) ? array_unique(array_filter(array_map('absint', (array) $available_data))) : array_unique(array_filter(array_map('absint', (array) explode(',', $available_data))));
                    foreach ($ids as $eachid) {
                        $getproductdata = wc_get_product($eachid);
                        $get_datas[$eachid] = wp_kses_post($getproductdata->get_formatted_name());
                    } echo esc_attr(json_encode($get_datas));
                }
                ?>" value="<?php echo implode(',', array_keys($get_datas)); ?>"/>
                       <?php
                   } else {
                       ?>

                <select multiple="multiple" name="<?php echo $value['id']; ?>[]" id="<?php echo $value['id']; ?>" class="<?php echo $value['id']; ?>" style="width:330px;" >
                    <?php
                    $get_datas = array();
                    $available_data = get_option($value['id']);
                    if ($available_data) {
                        $ids = is_array($available_data) ? array_unique(array_filter(array_map('absint', (array) $available_data))) : array_unique(array_filter(array_map('absint', (array) explode(',', $available_data))));
                        foreach ($ids as $eachid) {
                            ?>
                            <option value="<?php echo $eachid; ?>" selected="selected"><?php echo '#' . $eachid . ' &ndash; ' . get_the_title($eachid); ?></option>
                            <?php
                        }
                    } else {
                        ?>
                        <option value=""></option>
                        <?php
                    }
                    ?>
                </select>

                <script type="text/javascript">

                    jQuery(function () {
                        jQuery("select.<?php echo $value['id']; ?>").ajaxChosen({
                            method: 'GET',
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            dataType: 'json',
                            afterTypeDelay: 100,
                            data: {
                                action: 'woocommerce_json_search_products',
                                security: '<?php echo wp_create_nonce("search-products"); ?>'
                            }
                        }, function (data) {
                            var terms = {};
                            jQuery.each(data, function (i, val) {
                                terms[i] = val;
                            });
                            return terms;
                        });
                    });
                </script>
                <?php
            }
            ?>
        </td>
    </tr>
    <?php
}

add_action('woocommerce_settings_tabs_woocommerce_hide_products', 'cwg_hide_product_register_admin_fields');
add_filter('woocommerce_settings_tabs_array', 'cwg_hide_product_settings_tab', 999);
add_action('admin_init', 'cwg_hide_product_default_values');
add_action('woocommerce_admin_field_cwg_product_search', 'cwg_optimize_product_search');
add_filter('woocommerce_toggle_product_settings', 'cwg_hide_product_comprehensive_settings');
add_action('woocommerce_update_options_woocommerce_hide_products', 'cwg_hide_product_update_options');
