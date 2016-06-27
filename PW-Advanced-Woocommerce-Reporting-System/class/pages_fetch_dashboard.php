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
    
    
    <div class="awr-action awr-action--open"></div><!--monile-btn -->
	<nav id="ml-menu" class="awr-menu"  style="visibility:hidden">
		<img class="awr-menu-logo" src="<?php echo __PW_REPORT_WCREPORT_URL__; ?>/assets/images/logo.png" />
		<div class="awr-toggle-menu"></div>
		<div class="menu__wrap">
				
				<ul data-menu="main" class="menu__level">
					<li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_dashboard&parent=dashboard" id="dashboard"><i class="fa fa-bookmark"></i><?php echo __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
                    <li class="menu__item"><a class="menu__link" href="admin.php?page=wcx_wcreport_plugin_active_report&parent=active_plugin" id="active_plugin"><i class="fa fa-cogs"></i><?php echo __('Active Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__); ?></a></li>
				</ul>
				
				
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