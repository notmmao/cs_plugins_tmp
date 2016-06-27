	<div class="container my_content">	
    	<div id="loader-wrapper">
            <div id="loader"></div>
    
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
   			
        </div>
    <!--<div  id="awr_fullscreen_loading" style="height:100%;	background-color: rgb(28, 29, 34);    position: absolute;    top: 0;    right: 0;    left: -20px;    z-index: 9999999;    background: rgb(28, 29, 34);   padding: 10px;">
        <div class="awr-loding-gif-cnt" >
			<div class="awr-loading-css">
			 <div class="rect1"></div>
			 <div class="rect2"></div>
			 <div class="rect3"></div>
			 <div class="rect4"></div>
			 <div class="rect5"></div>
		   </div>
		</div>
    </div>-->
    
	<div class="awr-allmenu-cnt" style="visibility:hidden">
		<div class="awr-allmenu-close"><i class="fa fa-times"></i></div>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-check"></i><?php echo __('Basics',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_dashboard&parent=dashboard" id="dashboard"><i class="fa fa-bookmark"></i><?php echo __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_details&parent=details" id="details"><i class="fa fa-file-text"></i><?php echo __('All Orders',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-files-o"></i><?php echo __('More Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_product&parent=all_details&product" id="product"><i class="fa fa-cog"></i><?php echo __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_category&parent=all_details&category" id="category"><i class="fa fa-tags"></i><?php echo __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_customer&parent=all_details&customer" id="customer"><i class="fa fa-user"></i><?php echo __('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_billingcountry&parent=all_details&billing_countery" id="billing_countery"><i class="fa fa-globe"></i><?php echo __('Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_billingstate&parent=all_details&billing_state" id="billing_state"><i class="fa fa-map"></i><?php echo __('Billing State',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_paymentgateway&parent=all_details&Payment_gateway" id="Payment_gateway"><i class="fa fa-credit-card"></i><?php echo __('Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_orderstatus&parent=all_details&order_status" id="order_status"><i class="fa fa-check"></i><?php echo __('Order Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_recentorder&parent=all_details&recent_order" id="recent_order"><i class="fa fa-shopping-cart"></i><?php echo __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_taxreport&parent=all_details&tax_report" id="tax_report"><i class="fa fa-pie-chart"></i><?php echo __('Tax Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_customrebuyproducts&parent=all_details&customer_buy_prod" id="customer_buy_prod"><i class="fa fa-users"></i><?php echo __('Customer Buy Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_refunddetails&parent=all_details&refund_detail" id="refund_detail"><i class="fa fa-eye-slash"></i><?php echo __('Refund Detail',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_coupon&parent=all_details&coupon" id="coupon"><i class="fa fa-hashtag"></i><?php echo __('Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
				</div>
			</div>
			<!--<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-random"></i><?php echo __('CrossTab',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_prod_per_month&parent=cross_tab&prod_month" id="prod_month"><i class="fa fa-cog"></i><?php echo __('Product/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_variation_per_month&parent=cross_tab&variation_month" id="variation_month"><i class="fa fa-line-chart"></i><?php echo __('Variation/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_prod_per_country&parent=cross_tab&prod_country" id="prod_country"><i class="fa fa-globe"></i><?php echo __('Product/Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_prod_per_state&parent=cross_tab&prod_state" id="prod_state"><i class="fa fa-map"></i><?php echo __('Product/State',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_country_per_month&parent=cross_tab&country_month" id="country_month"><i class="fa fa-globe"></i><?php echo __('Country/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_payment_per_month&parent=cross_tab&payment_month" id="payment_month"><i class="fa fa-credit-card"></i><?php echo __('Payment Gateway/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_ord_status_per_month&parent=cross_tab&order_status_month" id="order_status_month"><i class="fa fa-check"></i><?php echo __('Order Status/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
				</div>
			</div>-->
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="awr-allmenu-box">
					<div class="awr-menu-title"><i class="fa fa-check"></i><?php echo __('Other',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></div>
					<!--<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_variation&parent=variation" id="variation"><i class="fa fa-line-chart"></i><?php echo __('Variation',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>-->
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_stock_list&parent=stock_list" id="stock_list"><i class="fa fa-cart-arrow-down"></i><?php echo __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<!--<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_variation_stock&parent=variation_stock" id="variation_stock"><i class="fa fa-rocket"></i><?php echo __('Variation Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>-->
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_projected_actual_sale&parent=proj_actual_sale" id="proj_actual_sale"><i class="fa fa-calendar-check-o"></i><?php echo __('Projected vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_tax_reports&parent=tax_reports" id="tax_reports"><i class="fa fa-pie-chart"></i><?php echo __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
					<a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_setting_report&parent=setting" id="setting"><i class="fa fa-cogs"></i><?php echo __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
                    <a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_active_report&parent=active_plugin" id="active_plugin"><i class="fa fa-cogs"></i><?php echo __('Active Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 awr-allmenu-footer">
				<h3><?php echo __('WOOCommerce Advance Reporting System',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></h3>
				<span>Powered By <a href="http://codecanyon.net/user/proword/portfolio">Proword</a></span>
			</div>
		</div><!--row -->
	</div>
    
    <div class="awr-action awr-action--open"></div><!--monile-btn -->
	<nav id="ml-menu" class="awr-menu"  style="visibility:hidden">
		<img class="awr-menu-logo" src="<?php echo __PW_REPORT_WCREPORT_URL__; ?>/assets/images/logo.png" />
		<div class="awr-toggle-menu"></div>
		<div class="menu__wrap">
				
				<ul data-menu="main" class="menu__level">
					<li class="menu__item"><a class="menu__link awr-allmenu" href="#" ><i class="fa fa-bars"></i><?php echo __('All Menus',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_dashboard&parent=dashboard" id="dashboard"><i class="fa fa-bookmark"></i><?php echo __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_details&parent=details" id="details"><i class="fa fa-file-text"></i><?php echo __('All Orders',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" data-submenu="all_details" href="#"><i class="fa fa-files-o"></i><?php echo __('More Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<!--<li class="menu__item"><a class="menu__link" data-submenu="cross_tab" href="#"><i class="fa fa-random"></i><?php echo __('CrossTab',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>-->
					<!--<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_variation&parent=variation" id="variation"><i class="fa fa-line-chart"></i><?php echo __('Variation',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>-->
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_stock_list&parent=stock_list" id="stock_list"><i class="fa fa-cart-arrow-down"></i><?php echo __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<!--<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_variation_stock&parent=variation_stock" id="variation_stock"><i class="fa fa-rocket"></i><?php echo __('Variation Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>-->
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_projected_actual_sale&parent=proj_actual_sale" id="proj_actual_sale"><i class="fa fa-calendar-check-o"></i><?php echo __('Projected vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_tax_reports&parent=tax_reports" id="tax_reports"><i class="fa fa-pie-chart"></i><?php echo __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_setting_report&parent=setting" id="setting"><i class="fa fa-cogs"></i><?php echo __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
                    <li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_active_report&parent=active_plugin" id="active_plugin"><i class="fa fa-cogs"></i><?php echo __('Active Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
				</ul>
				
				<!-- all_details Submenu -->
				<ul data-menu="all_details" class="menu__level">
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_product&parent=all_details&product" id="product"><i class="fa fa-cog"></i><?php echo __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_category&parent=all_details&category" id="category"><i class="fa fa-tags"></i><?php echo __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_customer&parent=all_details&customer" id="customer"><i class="fa fa-user"></i><?php echo __('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_billingcountry&parent=all_details&billing_countery" id="billing_countery"><i class="fa fa-globe"></i><?php echo __('Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_billingstate&parent=all_details&billing_state" id="billing_state"><i class="fa fa-map"></i><?php echo __('Billing State',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_paymentgateway&parent=all_details&Payment_gateway" id="Payment_gateway"><i class="fa fa-credit-card"></i><?php echo __('Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_orderstatus&parent=all_details&order_status" id="order_status"><i class="fa fa-check"></i><?php echo __('Order Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_recentorder&parent=all_details&recent_order" id="recent_order"><i class="fa fa-shopping-cart"></i><?php echo __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_taxreport&parent=all_details&tax_report" id="tax_report"><i class="fa fa-pie-chart"></i><?php echo __('Tax Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_customrebuyproducts&parent=all_details&customer_buy_prod" id="customer_buy_prod"><i class="fa fa-users"></i><?php echo __('Customer Buy Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_refunddetails&parent=all_details&refund_detail" id="refund_detail"><i class="fa fa-eye-slash"></i><?php echo __('Refund Detail',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_coupon&parent=all_details&coupon" id="coupon"><i class="fa fa-hashtag"></i><?php echo __('Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
				</ul>
				
				<!-- cross_tab Submenu -->
				<!--<ul data-menu="cross_tab" class="menu__level">
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_prod_per_month&parent=cross_tab&prod_month" id="prod_month"><i class="fa fa-cog"></i><?php echo __('Product/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_variation_per_month&parent=cross_tab&variation_month" id="variation_month"><i class="fa fa-line-chart"></i><?php echo __('Variation/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_prod_per_country&parent=cross_tab&prod_country" id="prod_country"><i class="fa fa-globe"></i><?php echo __('Product/Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_prod_per_state&parent=cross_tab&prod_state" id="prod_state"><i class="fa fa-map"></i><?php echo __('Product/State',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_country_per_month&parent=cross_tab&country_month" id="country_month"><i class="fa fa-globe"></i><?php echo __('Country/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_payment_per_month&parent=cross_tab&payment_month" id="payment_month"><i class="fa fa-credit-card"></i><?php echo __('Payment Gateway/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_ord_status_per_month&parent=cross_tab&order_status_month" id="order_status_month"><i class="fa fa-check"></i><?php echo __('Order Status/Month',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
				</ul>-->
				
			</div>
	</nav>
	
    <!-- Main container -->
    
        <div class="awr-content" style="visibility:hidden">
            <?php
            	include($page);
			?>
            
            <!-- Ajax loaded content here -->
        </div>
    </div>