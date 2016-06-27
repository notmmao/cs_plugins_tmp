<?php
	global $pw_rpt_main_class;
	
	global $wpdb;
			
	$order_date="SELECT pw_posts.ID AS order_id, pw_posts.post_date AS order_date, pw_posts.post_status AS order_status FROM {$wpdb->prefix}posts as pw_posts WHERE pw_posts.post_type='shop_order' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_posts.ID Order By pw_posts.post_date ASC LIMIT 5";
	$results= $wpdb->get_results($order_date);
	
	$first_date='';
	$i=0;
	while($i<5){
		
		if($results[$i]->order_date!=0)
		{
			if(isset($results[$i]))
				$first_date=$results[$i]->order_date;
			break;	
		}
		$i++;
	}
	
	$order_date="SELECT pw_posts.ID AS order_id, pw_posts.post_date AS order_date, pw_posts.post_status AS order_status FROM {$wpdb->prefix}posts as pw_posts WHERE pw_posts.post_type='shop_order' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_posts.ID Order By pw_posts.post_date DESC LIMIT 1";
	$results= $wpdb->get_results($order_date);
	
	$pw_to_date='';
	if(isset($results[0]))
		$pw_to_date=$results[0]->order_date;

	if($first_date==''){
		$first_date= date("Y-m-d");
		$this->pw_from_date_dashboard=$first_date;
		$first_date=substr($first_date,0,4);
	}else{
		
		$pw_from_date_dashboard=explode(" ",$first_date);
		$this->pw_from_date_dashboard=$pw_from_date_dashboard[0];
		
		$first_date=substr($first_date,0,4);
	}
	
	if($pw_to_date==''){
		$pw_to_date= date("Y-m-d");
		$this->pw_to_date_dashboard=$pw_to_date;
		$pw_to_date=substr($pw_to_date,0,4);
	}else{
		$pw_to_date_dashboard=explode(" ",$pw_to_date);
		$this->pw_to_date_dashboard=$pw_to_date_dashboard[0];
		
		$pw_to_date=substr($pw_to_date,0,4);
	}
	
	
?>

<div class="wrap">
	<div class="row">
		<div class="awr-box-content-tab">
			<?php 
				$table_name='dashboard_report';
				$pw_rpt_main_class->search_form_html($table_name);
			?>	 
			
			<div id="target">
				<?php
					$table_name='dashboard_report';
					$pw_rpt_main_class->table_html($table_name);
				?>	
			</div>
		</div>
		
        		
		<?php
        if ($this->dashboard($this->pw_plugin_status)){
		?>
        
		<!--CHARTS/TABS-->
		<div class="awr-box-content-tab">
			<div class="tabs tabsA tabs-style-underline"> 
				<nav>
					<ul class="tab-uls">
                                            
                        <li><a href="#section-bar-1" class="" data-target="pwr_chartdiv_month"> <div><i class="fa fa-cogs"></i><?php echo __('Sales By Months',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div></a></li>
                        
                        <li><a href="#section-bar-2" class="" data-target="pwr_chartdiv_day"> <div><i class="fa fa-cogs"></i><?php echo __('Sales By Days',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div></a></li>
                        
                        <li><a href="#section-bar-3" class="" data-target="pwr_chartdiv_multiple"> <div><i class="fa fa-cogs"></i><?php echo __('Multiple Charts',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div></a></li>
                        
						<li><a href="#section-bar-4" class="" data-target="pwr_chartdiv_pie"> <div><i class="fa fa-columns"></i><?php echo __('Top Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__) ?></div></a></li>
                        
					</ul>
				</nav>
                
                <div class="awr-theme-chart">
                	<ul>
                        <li  class="awr-theme-chart-title">
                        	<span class="">Click to change theme:&nbsp;&nbsp;</span>
                        </li>
                        
                        <li class="pw_switch_chart_theme " data-theme="light">
                        	<img width="36" height="35" src="https://www.amcharts.com/wp-content/uploads/2013/11/theme_light2.png" alt="theme_light">
                        </li>
                        
                        <li class="pw_switch_chart_theme " data-theme="dark">
                        	<img width="36" height="35" src="https://www.amcharts.com/wp-content/uploads/2013/11/theme_dark2.png" alt="theme_dark">
                        </li>
                        
                        <li class="pw_switch_chart_theme " data-theme="patterns">
                        	<img width="36" height="35" src="https://www.amcharts.com/wp-content/uploads/2013/11/theme_pattern2.png" alt="theme_patterns">
                        </li>
                        
                        <li class="pw_switch_chart_theme " data-theme="none">
                        	<img width="36" height="35" src="https://www.amcharts.com/wp-content/uploads/2013/11/theme_none.png" alt="theme_none">
                        </li>
                        
                	</ul>
                </div>
                
				<div class="content-wrap">
                    <section id="section-bar-1">
						<div id="pwr_chartdiv_month" style="width: 100%; height: 450px;"></div>
					</section>
                    
                    <section id="section-bar-2">
						<div id="pwr_chartdiv_day" style="width: 100%; height: 450px;"></div>
					</section>
                    
                    <section id="section-bar-3">
						<div id="pwr_chartdiv_multiple" style="width: 100%; height: 550px;"></div>
					</section>
                    
					<section id="section-bar-4">
						<div id="pwr_chartdiv_pie" style="width: 100%; height: 450px;"></div>
					</section>
					
				</div>
			</div>
			
		</div>
		
        <!--MAP--><!---->
		<div class="col-xs-12">
        	<div class="awr-box">
            	<div class="awr-title">
                	<h3><i class="fa fa-desktop"></i><?php _e('Map',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></h3>
                    <div class="awr-title-icons">
                    	<div class="awr-title-icon"><i class="fa fa-arrow-up"></i></div>
                        <div class="awr-title-icon"><i class="fa fa-cog"></i></div>
                        <div class="awr-title-icon"><i class="fa fa-times"></i></div>
                    </div>
                </div>
                
                <div class="awr-box-content container5">
					<div class="map">
						<span>Alternative content for the map</span>
					</div>
					

					<div class="rightPanel">
						<h2>Select a year</h2>
						<div class="knobContainer">
							<input class="knob" data-width="80" data-height="80" data-min="<?php echo $first_date;?>" data-max="<?php echo $pw_to_date; ?>" data-cursor=true data-fgColor="#454545" data-thickness=.45 value="<?php echo $first_date;?>" data-bgColor="#c7e8ff" /> 
						</div>
						<div class="areaLegend">
							<span>Alternative content for the legend</span>
						</div>
						<div class="plotLegend"></div>
					</div>  					
				</div>
			</div>
		</div>
		
		<!--DATA GRID-->

		<div class="col-md-12">
		<?php
			$table_name='monthly_summary';
			$pw_rpt_main_class->table_html($table_name);
		?>
		</div>
		
        
		<div class="col-md-6">
			<?php
				$table_name='order_summary';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
			
		<div class="col-md-6">
			<?php
				$table_name='sale_order_status';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
        
        
		<div class="col-md-6">
			<?php
				$table_name='top_5_products';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
		
		<div class="col-md-6">
			<?php
				$table_name='top_5_category';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
        
        
		<div class="col-md-6">
			<?php
				$table_name='top_5_country';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
		
		<div class="col-md-6">
			<?php
				$table_name='top_5_state';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
            
        
		<div class="col-md-12">
			<?php
				$table_name='recent_5_order';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
         
        
		<div class="col-md-12">
			<?php
				$table_name='top_5_customer';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
		
		<div class="col-md-12">
			<?php
				$table_name='top_5_coupon';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
		
		<div class="col-md-12">
			<?php
				$table_name='top_5_gateway';
				$pw_rpt_main_class->table_html($table_name);
			?>
		</div>
        
        <?php
		}//END DASHBOARD FUNCITON CHECK
		?>
		
     </div><!--row --> 
</div>

    <?php
		
		//echo $first_date.' - '.$pw_to_date;
	
		$country_values='';
		$areas='';
		
		$all_country='';
		for($year=$first_date;$year<=$pw_to_date;$year++){
			$Country_sql="SELECT SUM(pw_postmeta1.meta_value) AS 'Total' ,pw_postmeta2.meta_value AS 'BillingCountry' ,Count(*) AS 'OrderCount' FROM {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID WHERE pw_posts.post_type	=	'shop_order' AND pw_postmeta1.meta_key =	'_order_total' AND pw_postmeta2.meta_key	=	'_billing_country' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND DATE(pw_posts.post_date) BETWEEN '".$year."-01-01' AND '".$year."-12-30' AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_postmeta2.meta_value Order By Total DESC";
			
			//echo($Country_sql);
			
			$results= $wpdb->get_results($Country_sql);
			
			foreach($results as $items){
				
				if($items->BillingCountry=='')
					continue;
					
				$all_country[]=$items->BillingCountry;
			}
		}
		
		for($year=$first_date;$year<=$pw_to_date;$year++){
			$Country_sql="SELECT SUM(pw_postmeta1.meta_value) AS 'Total' ,pw_postmeta2.meta_value AS 'BillingCountry' ,Count(*) AS 'OrderCount' FROM {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID WHERE pw_posts.post_type	=	'shop_order' AND pw_postmeta1.meta_key =	'_order_total' AND pw_postmeta2.meta_key	=	'_billing_country' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND DATE(pw_posts.post_date) BETWEEN '".$year."-01-01' AND '".$year."-12-30' AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_postmeta2.meta_value Order By Total DESC";
			
			$results= $wpdb->get_results($Country_sql);
			
			$this_year_country='';
			
			foreach($results as $items){
				
				if($items->BillingCountry=='')
					continue;
				
				$country      	= $this->pw_get_woo_countries();//Added 20150225														
				$pw_table_value = isset($country->countries[$items->BillingCountry]) ? $country->countries[$items->BillingCountry]: $items->BillingCountry;
				
			
				$country_values[]=round($items->Total,0);
				$areas[$year][$items->BillingCountry]= array(
								"value" => $items->Total,
								"href" => "http://en.wikipedia.org/w/index.php?search=".$pw_table_value,
								"tooltip" => array(
									"content" => "<span style=\"font-weight:bold;\">".$pw_table_value."</span><br /><span style=\"font-weight:bold;\">".  $this->price($items->Total) . " # " .$items->OrderCount."</span><br />Total : ".$items->Total
								));
				$this_year_country[]=$items->BillingCountry;
			}
			
			
			
			if(is_array($this_year_country) && is_array($all_country))
			{
				$diff_array=array_diff($all_country,$this_year_country);
				
				foreach($diff_array as $diff_country){
					$country      	= $this->pw_get_woo_countries();														
					$pw_table_value = isset($country->countries[$diff_country]) ? $country->countries[$diff_country]: $diff_country;
					
					
				
					//$country_values[]=0;
					$areas[$year][$diff_country]= array(
									"value" => "0",
									"href" => "http://en.wikipedia.org/w/index.php?search=".$pw_table_value,
									"tooltip" => array(
										"content" => "<span style=\"font-weight:bold;\">".$pw_table_value."</span><br /><span style=\"font-weight:bold;\">".  $this->price(0) . " #0</span><br />Total : 0"
									));
				}
			}
		}
		
		$plots='';
		$state_values='';
		$state='';
		
		$all_states='';
		
		//GET ALL STATES
		for($year=$first_date;$year<=$pw_to_date;$year++){
			$State_sql="SELECT SUM(pw_postmeta1.meta_value) AS 'Total' ,pw_postmeta2.meta_value AS 'billing_state' ,pw_postmeta3.meta_value AS 'billing_country' ,Count(*) AS 'OrderCount' FROM {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta3 ON pw_postmeta3.post_id=pw_posts.ID WHERE pw_posts.post_type =	'shop_order' AND pw_postmeta1.meta_key =	'_order_total' AND pw_postmeta2.meta_key	=	'_billing_state' AND pw_postmeta3.meta_key	= '_billing_country' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND DATE(pw_posts.post_date) BETWEEN '".$year."-01-01' AND '".$year."-12-01' AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_postmeta2.meta_value Order By Total DESC";
	
			$results= $wpdb->get_results($State_sql);
			
			foreach($results as $items){
				
				if($items->billing_state=='' || $items->billing_country=='')
					continue;
				
				$pw_table_value=$this->pw_get_woo_bsn($items->billing_country,$items->billing_state);
				
				//REMOVE ( FROM STATE NAME : EXMP : Spain(Madrid) => Spain
				$this_state=explode("(",$pw_table_value);
				$pw_table_value=$this_state[0];
				
				$all_states[]=$pw_table_value;			
			}
		}
		
		//print_r($all_states);
	
		for($year=$first_date;$year<=$pw_to_date;$year++){
			$State_sql="SELECT SUM(pw_postmeta1.meta_value) AS 'Total' ,pw_postmeta2.meta_value AS 'billing_state' ,pw_postmeta3.meta_value AS 'billing_country' ,Count(*) AS 'OrderCount' FROM {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID LEFT JOIN {$wpdb->prefix}postmeta as pw_postmeta3 ON pw_postmeta3.post_id=pw_posts.ID WHERE pw_posts.post_type =	'shop_order' AND pw_postmeta1.meta_key =	'_order_total' AND pw_postmeta2.meta_key	=	'_billing_state' AND pw_postmeta3.meta_key	= '_billing_country' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND DATE(pw_posts.post_date) BETWEEN '".$year."-01-01' AND '".$year."-12-01' AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_postmeta2.meta_value Order By Total DESC";
	
			$results= $wpdb->get_results($State_sql);
			
			$this_year_states='';
						
			foreach($results as $items){
				
				if($items->billing_state=='' || $items->billing_country=='')
					continue;
				
				
				
				$pw_table_value=$this->pw_get_woo_bsn($items->billing_country,$items->billing_state);
				$pw_table_values=strtolower(str_replace(" ","",$pw_table_value));
				
				//REMOVE ( FROM STATE NAME : EXMP : Spain(Madrid) => Spain
				$this_state=explode("(",$pw_table_values);
				$pw_table_values=$this_state[0];
				
				$state[]=$pw_table_values;
				$state_values[]=round($items->Total,0);
				$plots[$year][$pw_table_values]= array(
								"value" => $items->Total,
								"tooltip" => array(
									"content" => "<span style=\"font-weight:bold;\">".$pw_table_value."</span><br /><span style=\"font-weight:bold;\">".  $this->price($items->Total) . " # " .$items->OrderCount."</span><br />Total : ".$items->Total
								));
				
				//REMOVE ( FROM STATE NAME : EXMP : Spain(Madrid) => Spain				
				$this_state=explode("(",$pw_table_value);
				$pw_table_value=$this_state[0];				
								
				$this_year_states[]=$pw_table_value;			
			}
			
			if(is_array($this_year_states) && is_array($all_states))
			{
				$diff_array=array_diff($all_states,$this_year_states);
				foreach($diff_array as $diff_state){
					
					//$state_values[]=0;
					
					$pw_table_values=strtolower(str_replace(" ","",$diff_state));
					
					
					//REMOVE ( FROM STATE NAME : EXMP : Spain(Madrid) => Spain
					$this_state=explode("(",$pw_table_values);
					$pw_table_values=$this_state[0];	
					
					$state[]=$pw_table_values;
					$plots[$year][$pw_table_values]= array(
									"value" => "0",
									"tooltip" => array(
										"content" => "<span style=\"font-weight:bold;\">".$diff_state."</span><br /><span style=\"font-weight:bold;\">".  $this->price(0) . " # 0</span><br />Total : 0"
									));
				}
			}
		}
		
		
		
		//print_r($plots);
		$map_date='';
		if($first_date!=$pw_to_date){
			for($year=$first_date;$year<=$pw_to_date;$year++){
				$map_date[$year]=array("areas" =>$areas[$year],"plots" =>$plots[$year]);	
			}
		}else{
			$year=$first_date;
			$map_date[$year]=array("areas" =>$areas[$year],"plots" =>$plots[$year]);
		}
		
		//print_r($map_date);
		
		
		/////////SESARCH RANGES//////////
		$first_limit_country=$two_limit_country=$first_limit_state=$two_limit_state=false;
		if(is_array($country_values))
		{
			sort($country_values);	
			$max_counrty= max($country_values);
			$math=round($max_counrty/3,0);
			$first_limit_country=$math;
			$two_limit_country=$math+$math;
		}
		
		if(is_array($state_values))
		{
			sort($state_values);
			$max_state= max($state_values);
			$math=round($max_state/3,0);
			$first_limit_state=$math;
			$two_limit_state=$math+$math;
		}
		
		/*
		
		//--------------
		
		*/
		

		//////////////////
		
		
		//print_r($map_date);
		
    	$arr=$map_date;
	?>
    

<script type="text/javascript">

	var data = <?php echo (json_encode($arr)=='' ? "''":json_encode($arr)) ; ?>;
		
	jQuery( document ).ready(function( $ ) {
		
		var myarray = [];
		var myJSON = new Object();
		<?php
			//die(print_r($state));
			foreach((array)$state as $state_name){
				if($state_name=='' || is_numeric($state_name))	continue;	
		?>
		
			var geocoder = new google.maps.Geocoder();
			var address = "<?php echo $state_name?>";
			
			geocoder.geocode( { 'address': address}, function(results, status) {
			
				if (status == google.maps.GeocoderStatus.OK) {
					var latitude = results[0].geometry.location.lat();
					var longitude = results[0].geometry.location.lng();
					
					//confirm("<?php echo $state_name?>"+latitude+" - "+longitude);

					var item = {
							"latitude": latitude,
							"longitude": longitude,
							"text": {
								"position": "left",
								"content": "",
							}
						};
	
					myJSON.<?php echo strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $state_name)));?>=item;
				}
				
			}); 
			
		<?php
			}
		?>
		
		setTimeout(function(){
	
			// Default plots params
			var plots = myJSON;	
			
		
			$(".knob").knob({
				release : function (value) {
					$(".container5").trigger('update', [data[value], {}, {}, {animDuration : 300}]);
				}
			});
		
			// Mapael initialisation
			$world = $(".container5");
			$world.mapael({
				map : {
					name : "world_countries",
					defaultArea: {
						attrs : {
							fill: "#eee",
							stroke : "#aaa", 
							"stroke-width" : 0.3
						}
					},
					defaultPlot: {
						text : {
							attrs: {
								fill:"#333"
							},
							attrsHover: {
								fill:"#fff",
								"font-weight":"bold"
							}
						}
					}
					, zoom : {
						enabled : true
						, step : 0.25
						, maxLevel : 20
					}
				},
				legend : {
					<?php 
					global  $woocommerce;
					
					if($first_limit_country){
					?>
					area : {
						display : true,
						title :"Country Orders Amount", 
						marginBottom : 7,
						slices : [
							{
								max :<?php echo $first_limit_country; ?>, 
								attrs : {
									fill : "#6ECBD4"
								},
								label :'Less than <?php echo  ($first_limit_country).' '.get_woocommerce_currency(); ?>'
							},
							{
								min :<?php echo $first_limit_country; ?>, 
								max :<?php echo $two_limit_country; ?>, 
								attrs : {
									fill : "#3EC7D4"
								},
								label :'> <?php echo ($first_limit_country).' '.get_woocommerce_currency(); ?> and < <?php echo ($two_limit_country).' '.get_woocommerce_currency(); ?>'
							},
							{
								min :<?php echo $two_limit_country; ?>, 
								attrs : {
									fill : "#01565E"
								},
								label :'More than <?php echo ($two_limit_country).' '.get_woocommerce_currency(); ?>'
							}
						]
					},
					<?php
					}
					if($first_limit_state){
					?>
					plot :{
						display : true,
						title: "State Orders Amount",
						marginBottom : 6,
						slices : [
							{
								type :"circle",
								max :<?php echo $first_limit_state; ?>, 
								attrs : {
									fill : "#FD4851",
									"stroke-width" : 1
								},
								attrsHover :{
									transform : "s1.5",
									"stroke-width" : 1
								}, 
								label :"Less than <?php echo $first_limit_state.' '.get_woocommerce_currency();?>", 
								size : 10
							},
							{
								type :"circle",
								min :<?php echo $first_limit_state; ?>, 
								max :<?php echo $two_limit_state; ?>, 
								attrs : {
									fill : "#FD4851",
									"stroke-width" : 1
								},
								attrsHover :{
									transform : "s1.5",
									"stroke-width" : 1
								}, 
								label :"> <?php echo $first_limit_state.' '.get_woocommerce_currency(); ?> and < <?php echo $two_limit_state.' '.get_woocommerce_currency(); ?>", 
								size : 20
							},
							{
								type :"circle",
								min :<?php echo $two_limit_state; ?>,
								attrs : {
									fill : "#FD4851",
									"stroke-width" : 1
								},
								attrsHover :{
									transform : "s1.5",
									"stroke-width" : 1
								}, 
								label :"More than <?php echo $two_limit_state.' '.get_woocommerce_currency(); ?>", 
								size : 30
							}
						]
					}
					<?php
					}
					?>
				},
				plots : $.extend(true, {}, data[<?php echo $first_date;?>]['plots'], plots),
				areas: data[<?php echo $first_date;?>]['areas']
			});
		
		},2000);
		
		
		[].slice.call( document.querySelectorAll( ".tabsA" ) ).forEach( function( el ) {
			new CBPFWTabs( el );
		});
		
		[].slice.call( document.querySelectorAll( ".tabsB" ) ).forEach( function( el ) {
			new CBPFWTabs( el );
		})
	});
</script>