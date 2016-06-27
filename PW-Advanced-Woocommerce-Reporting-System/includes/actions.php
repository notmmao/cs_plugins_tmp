<?php
	//FETCH REPORT DATAGRID
	add_action('wp_ajax_pw_rpt_fetch_data', 'pw_rpt_fetch_data');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_data', 'pw_rpt_fetch_data');
	function pw_rpt_fetch_data() {
		global $wpdb;
		
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		//print_r($my_array_of_vars);
		
		//echo $sql;
		
		//$products = $wpdb->get_results($sql);
		
		global $pw_rpt_main_class;
		
		$table_name=$my_array_of_vars['table_name'];
        $pw_rpt_main_class->table_html($table_name,$my_array_of_vars);
		
		die();
	}
	
	
	//FETCH REPORT DATAGRID
	add_action('wp_ajax_pw_rpt_fetch_data_dashborad', 'pw_rpt_fetch_data_dashborad');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_data_dashborad', 'pw_rpt_fetch_data_dashborad');
	function pw_rpt_fetch_data_dashborad() {
		global $wpdb;
		
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		//print_r($my_array_of_vars);
		
		//echo $sql;
		
		//$products = $wpdb->get_results($sql);
		
		global $pw_rpt_main_class;
        
		echo '
		<div class="awr-box">
			<div class="awr-title">
				<h3>
					<i class="fa fa-filter"></i>
					
				</h3>
			</div><!--awr-title -->
			<div class="awr-box-content">
				<div class="col-xs-12">
					<div class="awr-box">
						<div class="awr-box-content">					
							<div id="target">'.
									$pw_rpt_main_class->table_html("dashboard_report",$my_array_of_vars).'
							</div>
						</div>
					</div>
				</div>    
			</div>
		</div>		
        
        <div class="col-md-12">'.
            $pw_rpt_main_class->table_html("monthly_summary",$my_array_of_vars).'
        </div>
		';
		
		die();
	}
	
	
	//FETCH CHART DATA
	add_action('wp_ajax_pw_rpt_fetch_chart', 'pw_rpt_fetch_chart');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_chart', 'pw_rpt_fetch_chart');
	function pw_rpt_fetch_chart() {
		
		global $wpdb;
		global $pw_rpt_main_class;
		
		parse_str($_POST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		$type = $_POST['type'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		$pw_from_date=$my_array_of_vars['pw_from_date'];
		$pw_to_date=$my_array_of_vars['pw_to_date'];
		$cur_year=substr($pw_from_date,0,4);
		
		$pw_hide_os=array('trash');
		$pw_shop_order_status="wc-completed,wc-on-hold,wc-processing";
		if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
			$pw_shop_order_status = explode(",",$pw_shop_order_status); 
		else $pw_shop_order_status = array();
		
		
			
		/////////////////////////////
		//TOP PRODUCTS PIE CHART
		////////////////////////////
		$order_items_top_product=$pw_rpt_main_class->pw_get_dashboard_top_products_chart_pie($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		/////////////////////////////
		//SALE BY MONTHS
		////////////////////////////
		
		$order_items_months_multiple=$pw_rpt_main_class->pw_get_dashboard_sale_months_multiple_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_months=$pw_rpt_main_class->pw_get_dashboard_sale_months_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_days=$pw_rpt_main_class->pw_get_dashboard_sale_days_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		//die($order_items_days);
		
		$order_items_week=$pw_rpt_main_class->pw_get_dashboard_sale_weeks_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$final_json='';
		/////////////////////
		//SALE BY MONTH MULTIPLE CHART
		////////////////////
		
		$pw_fetchs_data='';
		$i=0;
		foreach ($order_items_months_multiple as $key => $order_item) {
			$value  = number_format($order_item->TotalAmount,2);
			
			$pw_fetchs_data[$i]["date"]=substr($order_item->Month,0,10);		
			$pw_fetchs_data[$i]["value"] = $value;
			$pw_fetchs_data[$i]["volume"] = $value;
			
			$i++;
			
		}
		$final_json[]=($pw_fetchs_data);
		
		//////////////////
		//SALE BY DAYS
		//////////////////
		$item_dates = array();
		$item_data  = array();
		$pw_fetchs_data = '';
		$i=0;
		foreach ($order_items_days as $item) {
			$item_dates[]           = trim($item->Date);
			$item_data[$item->Date] = $item->TotalAmount;
			
			$value= number_format($item->TotalAmount,2);
			$pw_fetchs_data[$i]["date"] = trim($item->Date);
			$pw_fetchs_data[$i]["value"] = $value;
			$pw_fetchs_data[$i]["volume"] = $value;
			$i++;
		}
		$final_json[]=$pw_fetchs_data;
		
		////////////////////////////
		//SALE BY WEEK
		/////////////////////////////
		$item_dates = array();
		$item_data  = array();
		
		$weekarray = array();
		$timestamp = time();
		for ($i = 0; $i < 7; $i++) {
			$weekarray[] = date('Y-m-d', $timestamp);
			$timestamp -= 24 * 3600;
		}
		
		foreach ($order_items_week as $item) {
			$item_dates[]           = trim($item->Date);
			$item_data[$item->Date] =  number_format($item->TotalAmount,2);
		}
		
		$new_data = array();
		foreach ($weekarray as $date) {
			if (in_array($date, $item_dates)) {
				
				$new_data[$date] = $item_data[$date];
			} else {
				$new_data[$date] = 0;
			}
		}
		
		$pw_fetchs_data = array();
		$i         = 0;
		foreach ($new_data as $key => $value) {
			$pw_fetchs_data[$i]["date"] = $key;
			$pw_fetchs_data[$i]["value"] =  number_format($value,2);
			$pw_fetchs_data[$i]["volume"] =  number_format($value,2);
			$i++;			
		}
		$final_json[]=array_reverse($pw_fetchs_data);
		
		///////////////////////
		//MONTH FOR CHART
		////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_months as $key => $order_item) {

			$value            =  number_format($order_item->TotalAmount,2);
					
			$pw_fetchs_data[$i]["date"]=$order_item->Month;		
			$pw_fetchs_data[$i]["value"] = $value;
			$pw_fetchs_data[$i]["volume"] = $value;
			
			$i++;			
		}
		$final_json[]=($pw_fetchs_data);		
		//die(print_r($final_json));
		
		///////////////////////////
		//	PIE CHART TOP PRODUCTS
		//////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_top_product as $items) {
			$pw_fetchs_data[$i]['label']=$items->Label;
			$pw_fetchs_data[$i]['value']= number_format($items->Value,2);
			
			$i++;
		}
		$final_json[]=($pw_fetchs_data);				
				
		echo json_encode($final_json);	
		die();
		
		//return $pw_fetchs_data;
		$data1=$data2=$data3=$data4='';
		$int= mt_rand(1262055681,1262055681);
		
		$min_epoch = strtotime("1 September 2014");
		$max_epoch = strtotime("30 October 2014");
	
		$rand_epoch = rand($min_epoch, $max_epoch);
		
		$fdate='';
			
			//die($fdate.' , '.$new_date.' : '.$diff->format('%R'));
		
		for($i=0;$i<12;$i++){
			$rand_epoch = rand($min_epoch, $max_epoch);
			$new_date=date('Y-m-d', $rand_epoch);
			
			if($fdate=='')
				$fdate=$new_date;
			
			$fdates = date_create($fdate);
			$new_dates = date_create($new_date);	
			$diff=date_diff($fdates,$new_dates);
			
			
			/*while($diff->format('%0')=='0')
			{
				$fdates = date_create($fdate);
				$rand_epoch = rand($min_epoch, $max_epoch);
				$new_date=date('Y-m-d', $rand_epoch);
				
				$new_dates = date_create($new_date);	
				$diff=date_diff($fdates,$new_dates);
				
				echo $fdate." , ".$new_date." : " . $diff->format('%a')." <br />";
			}*/
			
			if($i>=9)
				$data1[$i]['date']=$data2[$i]['date']=$data3[$i]['date']=$data4[$i]['date']='2015-'.($i+1).'-01';
			else
				$data1[$i]['date']=$data2[$i]['date']=$data3[$i]['date']=$data4[$i]['date']='2015-0'.($i+1).'-20';
			
			//$data1[$i]['date']=$data2[$i]['date']=$data3[$i]['date']=$data4[$i]['date']='2015-09-'.($i+1);
			
			
			$data1[$i]['value']=$data1[$i]['volume']=rand(0,500);
			
					
			$data2[$i]['value']=rand(0,500);
			
		
			$data3[$i]['value']=rand(0,500);
			
		
			$data4[$i]['value']=rand(0,500);
			$fdate=$new_date;
		}
		
		//print_r($data1);
		//echo date('Y-m-d', $rand_epoch);
		//header('Content-Type: application/json');
		echo json_encode($data1);	
		die();
		
		
		
		
		
	}
?>