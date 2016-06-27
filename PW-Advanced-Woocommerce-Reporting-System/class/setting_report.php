<?php
	
	$pw_livesearch_options_part=array(
		array(
			'id' => 'pw_report_metaboxname_fields_options_dashboard_setting',
			'title' => __('Dashboard Setting',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'icon' => '<i class="fa fa-clipboard"></i>',
			'variable' => 'pw_report_metaboxname_fields_options_dashboard_setting'
		),
		array(
			'id' => 'pw_report_metaboxname_fields_options_projected',
			'title' => __('Target Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'icon' => '<i class="fa fa-clipboard"></i>',
			'variable' => 'pw_report_metaboxname_fields_options_projected'
		),
		array(
			'id' => 'pw_report_metaboxname_fields_options_license',
			'title' => __('License info',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'icon' => '<i class="fa fa-info"></i>',
			'variable' => 'pw_report_metaboxname_fields_options_license'
		)
	);
	
	
	//GENERAL SETTING
	$pw_report_metaboxname_fields_options_dashboard_setting = array(
		array(  
			'label'	=> "",
			'desc'	=> "",
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search',
			'type'	=> 'notype',		
		),
		array(
			'label'	=> __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Recent Order table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'recent_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'recent_post_per_page',
			'type'	=>'numeric',
		),
		array(
			'label'	=> __('Top Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Top Product table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_product_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_product_post_per_page',
			'type'	=>'numeric',
		),
		array(
			'label'	=> __('Top Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Top Category table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_category_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_category_post_per_page',
			'type'	=>'numeric',
		),
		array(
			'label'	=> __('Top Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Top Customer table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_customer_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_customer_post_per_page',
			'type'	=>'numeric',
		),
		array(
			'label'	=> __('Top Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Top Billing Country table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_country_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_country_post_per_page',
			'type'	=>'numeric',
		),
		array(
			'label'	=> __('Top State Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Top State Country table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_state_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_state_post_per_page',
			'type'	=>'numeric',
		),
		array(
			'label'	=> __('Top Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Top Payment Gateway table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_gateway_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_gateway_post_per_page',
			'type'	=>'numeric',
		),
		array(
			'label'	=> __('Top Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Set Minimum page number for Top Coupon table',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_coupon_post_per_page',
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_coupon_post_per_page',
			'type'	=>'numeric',
		),
		
	);
	
	//FETCH YEARS
	global $wpdb;
		
	$order_date="SELECT pw_posts.ID AS order_id, pw_posts.post_date AS order_date, pw_posts.post_status AS order_status FROM {$wpdb->prefix}posts as pw_posts WHERE pw_posts.post_type='shop_order' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_posts.ID Order By pw_posts.post_date ASC LIMIT 1";
	$results= $wpdb->get_results($order_date);
	
	$first_date='';
	if(isset($results[0]))
		$first_date=$results[0]->order_date;
	
	if($first_date==''){
		$first_date= date("Y-m-d");
		$first_date=substr($first_date,0,4);
	}else{
		$first_date=substr($first_date,0,4);
	}
	
	$order_date="SELECT pw_posts.ID AS order_id, pw_posts.post_date AS order_date, pw_posts.post_status AS order_status FROM {$wpdb->prefix}posts as pw_posts WHERE pw_posts.post_type='shop_order' AND pw_posts.post_status IN ('wc-completed', 'wc-on-hold', 'wc-processing') AND pw_posts.post_status NOT IN ('trash') GROUP BY pw_posts.ID Order By pw_posts.post_date DESC LIMIT 1";
	$results= $wpdb->get_results($order_date);
	
	$pw_to_date='';
	if(isset($results[0]))
		$pw_to_date=$results[0]->order_date;

	if($pw_to_date==''){
		$pw_to_date= date("Y-m-d");
		$pw_to_date=substr($pw_to_date,0,4);
	}else{
		$pw_to_date=substr($pw_to_date,0,4);
	}
	
	
	
	
	$cur_year=date("Y-m-d");
	$cur_year=substr($cur_year,0,4);
	
	$option="";
	for($year=($first_date-5);$year<($pw_to_date+10);$year++)
	{
		$year_arr[$year]=array (
						'label'	=> $year,  
						'value'	=> $year  
					);
	}
	
	
	//SEARCH OPTION
	$pw_report_metaboxname_fields_options_projected= array(
		array(  
			'label' => __('Projected Sales Year',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'  => __('Choose Year',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'id'    => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'projected_year', 
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'projected_year', 
			'type'  => 'select_year' ,
			'options'	=> $year_arr,
		),
		array(  
			'label'	=> "",
			'desc'	=> __("Set Sales of monthes",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_year_sale',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_year_sale',
			'type'	=> 'notype',		
		),
		array(  
			'label'	=> __("Set Sales of monthes",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> "",
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes',
			'type'	=> 'monthes',		
		),
		
	);
		
	//LOCALIZAITION
	$pw_report_metaboxname_fields_options_license= array(
		array(  
			'label'	=> "",
			'desc'	=> __("Plugin Info",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'plugin_info',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'plugin_info',
			'type'	=> 'notype',		
		),
		array(  
			'label'	=> __("",__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> "",
			'name'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'license',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'license',
			'type'	=> 'text_info',		
		),
	);
	
	
	
	if (isset($_POST["update_settings"])) {
		// Do the saving
			
		foreach($_POST as $key=>$value){
			if(!isset($_POST[$key])){
				delete_option($key);  
				continue;
			}
			
			$old = get_option($key);  
			$new = $value; 
			if(!is_array($new)) 
			{
				if ($new && $new != $old) {  
					update_option($key, $new);  
				} elseif ('' == $new && $old) {  
					delete_option($key);  
				}
			}else{
				
				//die(print_r($new));
				
				$get_year=array_keys($value);
				$get_year=$get_year[0];
				
				foreach($value[$get_year] as $keys=>$vals){
					
					$old = get_option($key."_".$get_year."_".$keys);  
					$new = $vals; 
					
					if ($new && $new != $old) {  
						update_option($key."_".$get_year."_".$keys, $new);  
					} elseif ('' == $new && $old) {  
						delete_option($key."_".$get_year."_".$keys);  
					}
					
				}
			}
		}
			
		/*		die("d");
		foreach($pw_livesearch_options_part as $option_part){
			$this_part_variable=$$option_part['variable'];
			foreach ($this_part_variable as $field) { 
				
				if(!isset($_POST[$field['id']])){
					delete_option($field['id']);  
					continue;
				}
					
				$old = get_option($field['id']);  
				$new = $_POST[$field['id']];  
				if ($new && $new != $old) {  
					update_option($field['id'], $new);  
				} elseif ('' == $new && $old) {  
					delete_option($field['id']);  
				}
	
			} // end foreach  
		}*/
		?>
			<div id="setting-error-settings_updated" class="updated settings-error">
				<p><strong><?php echo __('Settings saved',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>.</strong></p>
            </div>
		<?php
	}	
	
	$html= '<div class="wrap">
			<h2>'.__('Mega Search Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h2>
			<br />
			<form method="POST" action="">
				<input type="hidden" name="update_settings" value="Y" />
				<input type="hidden" name="update_setting" value="NN" />
				<div class="tabs tabsA tabs-style-underline"> 
					<nav>
						<ul>';
							foreach($pw_livesearch_options_part as $option_part){
								$html.='<li><a href="#'.$option_part['id'].'" class="">'.$option_part['icon'].' <span>'.$option_part['title'].'</span></a></li>';
							}
					$html.='
						</ul>
					</nav>
					<div class="content-wrap">';		
						
	
	foreach($pw_livesearch_options_part as $option_part){
		//TAB TITLE
		
		
		$html.= '<section id="'.$option_part['id'].'">';
			$html.= '<table class="form-table">'; 
			$this_part_variable=$$option_part['variable'];
			foreach ($this_part_variable as $field) {  
				if(isset($field['dependency']))  
				{
					//$html.= pw_livesearch_dependency($field['id'],$field['dependency']);	
				}
				// get value of this field if it exists for this post  
				$meta = get_option($field['id']);  
				// begin a table row with  
				$style='';
				if($field['type']=='notype')
					$style='style="border-bottom:solid 1px #ccc"';
				$html.= '<tr class="'.$field['id'].'_field" '.$style.'> 
		
					<th><label for="'.$field['id'].'">'.$field['label'].'</label></th> 
					<td>';  
					switch($field['type']) {  
		
						case 'notype':
							$html.= '<span class="description">'.$field['desc'].'</span>';
						break;
						
						case 'text_info':
						
							if ($this->dashboard($this->pw_plugin_status)){
								$html.= '<h3>Plugin is Licensed !</h3>';
								
								$result=$this->dashboard($this->pw_plugin_status);
								
								$html.='<div style="border-left:5px solid #eee;padding:5px;line-height:20px;letter-spacing: 1px;"><strong>Plugin Name : </strong>'.$result['verify-purchase']['item_name'].'';
								$html.='<br /><strong>Buyer Id : </strong>'.$result['verify-purchase']['buyer'].'';
								$html.='<br /><strong>Purchase Date : </strong>'.$result['verify-purchase']['created_at'].'';
								$html.='<br /><strong>License Type : </strong>'.$result['verify-purchase']['licence'].'';
								$html.='<br /><strong>Supported Until : </strong>'.$result['verify-purchase']['supported_until'].'</div>';
							}
						break;
						
						case 'text':
							$html.= '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" value="'.$meta.'" />
							<br /><span class="description">'.$field['desc'].'</span>	';  
						break; 
						
						case 'radio':  
							foreach ( $field['options'] as $option ) {
								$html.= '<input type="radio" name="'.$field['id'].'" class="'.$field['id'].'" value="'.$option['value'].'" '.checked( $meta, $option['value'] ,0).' '.$option['checked'].' /> 
										<label for="'.$option['value'].'">'.$option['label'].'</label><br><br>';  
							}  
						break;
						
						case 'checkbox':  
								$html.= '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" '.checked( $meta, "on" ,0).'"/> 
									<br /><span class="description">'.$field['desc'].'</span>';  
							break;
						
						case 'select':  
							$html.= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">';  
							foreach ($field['options'] as $option) {  
								$html.= '<option '. selected( $meta , $option['value'],0 ).' value="'.$option['value'].'">'.$option['label'].'</option>';  
							}  
							$html.= '</select><br /><span class="description">'.__($field['desc'],__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>';  
						break;
						
						case 'select_year':  
							$html.= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">';  
							foreach ($field['options'] as $option) {  
								$html.= '<option '. selected( $meta , $option['value'],0 ).' value="'.$option['value'].'">'.$option['label'].'</option>';  
							}  
							$html.= '</select><br /><span class="description">'.__($field['desc'],__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</span>';  
							
							$all_monthes='';
							$months = array("January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December");
  
  						//	$html.=$first_date;$year<$pw_to_date;
  
							for($year=2010;$year<2025;$year++){
								
								foreach($months as $month){
									$all_monthes[$year][$month]=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes_'.$year.'_'.$month);	
								}
								
							}
							
							$html.='
								<script>
								
									var all_month='.json_encode($all_monthes).';
									
									var mS = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"];
									
									
									jQuery(document).ready(function($){
										var cur_year="";
										cur_year=$("#custom_report_projected_year").val();
										
										$("#custom_report_projected_year").change(function(){
											
											chg_year=$(this).val();
											var i=0
											$(".pwr_year_months").each(function(){
												input_name=$(this).attr("name");
												input_name=input_name.replace(cur_year,chg_year);
												$(this).attr("name",input_name);
												
												your_val="0";
												your_month=mS[i];
												if(all_month[chg_year][your_month])
													your_val=all_month[chg_year][your_month];
													
												$(this).val(your_val);
												i=i+1;
											});
										});
									});
								</script>
							';
							
						break;
								
						case 'monthes':
							
							$first_date=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'projected_year',$first_date);
							
							foreach($months as $month){
								$value=get_option($field['id'].'_'.$first_date.'_'.$month,0);
								
								$html.= '
							<span><label><strong>'.$month.'</strong></label></span><input type="text" name="'.$field['id'].'['.$first_date.']['.$month.']" id="'.$field['id'].'" class="'.$field['id'].' pwr_year_months" value="'.$value.'"/><br />';
							}
							
							$html.='
							<br /><span class="description">'.$field['desc'].'</span>	';  
						break; 		
												
						case 'numeric':  
							$html.= '
							<input type="number" name="'.$field['id'].'"  id="'.$field['id'].'" value="'.($meta=='' ? "":$meta).'" size="30" class="width_170 '.$field['id'].'" min="0" pattern="[-+]?[0-9]*[.,]?[0-9]+" title="Only Digits!" class="input-text qty text" />
		';
							$html.= '
								<br /><span class="description">'.$field['desc'].'</span>';  
						break;
						
						case 'html_editor':
						{
							ob_start();

								$html.= '
								<p><span class="description">'.$field['desc'].'</span></p>
								<p class="form-field product_field_type" >';
								$editor_id =$field['id'];
								 wp_editor(stripslashes($meta), $editor_id );
								$html.= ob_get_clean();
								$html.='</p>';
						}
						break; 
						
						case "pw_pages":
						{
							$args = array(
								'depth'                 => 0,
								'child_of'              => 0,
								'selected'              => $meta,
								'echo'                  => 0,
								'name'                  => $field['id'],
								'id'                    => null, // string
								'show_option_none'      => __('Choose a Page',__PW_REPORT_WCREPORT_TEXTDOMAIN__), // string
								'show_option_no_change' => null, // string
								'option_none_value'     => null, // string
							);
							$html.=wp_dropdown_pages($args);
							$html.= '<br /><span class="description">'.$field['desc'].'</span>'; 
						}
						break;
						
						case 'posttype_seletc':  
						{
							$output = 'objects';
							$args = array(
								'public' => true
							);
							$post_types = get_post_types( $args , $output);
															
							$html.='<select name="'.$field['id'].'[]" id="'.$field['id'].'" class="chosen-select-build-posttype" multiple="multiple"> ';
							$html.='<option value="" >'.__('Choose Post Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';
							foreach ( $post_types  as $post_type ) {
								
								if ( $post_type->name != 'attachment' ) {
									$post_value=$post_type->name;
									$post_lbl=$post_type->labels->name;
									
									$selected='';
									if(is_array($meta) && in_array($post_value,$meta))
										$selected='SELECTED';
									
									$html.='<option value="'.$post_value.'" '.$selected.'>'.$post_lbl.' ('.$post_value.')</option>';
								}
							}
							
							$html.= '<br /><span class="description">'.$field['desc'].'</span>'; 
							$html.='</select>
							<script type="text/javascript">
								jQuery(document).ready(function(){
									var visible = true;
									setInterval(
									function()
									{
										if(visible)
											if(jQuery(".chosen-select-build-posttype").is(":visible"))
											{
												jQuery(".chosen-select-build-posttype").chosen();
											}
									}, 100);
								});
							</script>';
						}
						break; 
						
						case 'all_search':
						{
							$html.='
							<select name="'.$field['name'].'" >
								<option value="">'.__('Choose Live Search',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';
								global $pw_woo_ad_main_class,$wpdb;
								
								$args=array('post_type' => 'pw_livesearch',
								'post_status'=>'publish',
								);
								
								$my_query_archive = new WP_Query($args);
								
								if( $my_query_archive->have_posts()):
									while ( $my_query_archive->have_posts() ) : $my_query_archive->the_post(); 	
										$id=get_the_ID();
										$html.= '<option value="'.$id.'" '.selected($id,$meta,0).'>'.get_the_title().'</option>';
									endwhile;
									wp_reset_query();
								endif;	
								$html.='</select>';
								$html.= '<br /><span class="description">'.$field['desc'].'</span>'; 
						}
						break;
						
						
						case "colorpicker":
							
							$html.= '<div class="medium-lbl-cnt">
											<label for="'.$field['id'].'" class="full-label">'.$field['label'].'</label>
											<input name="'.$field['id'].'" id="'.$field['id'].'" type="text" class="wp_ad_picker_color" value="'.$meta.'" data-default-color="#'.$meta.'">
										  </div>
									';	
							$html.= '
							
							<br />';
							$html.= '<br /><span class="description">'.$field['desc'].'</span>'; 
						break;
						
						case 'icon_type':  
							$html.= $meta;
							$html.= '<input type="hidden" id="'.$field['id'].'font_icon" name="'.$field['id'].'" value="'.$meta.'"/>';
							$html.= '<div class="'.$field['id'].' pw_iconpicker_grid" id="benefit_image_icon">';
							$html.= include(__PW_LIVESEARCH_ROOT_DIR__ .'/includes/font-awesome.php');
							$html.= '</div>';
							$html.= '<br /><span class="description">'.$field['desc'].'</span><br />'; 
							$output = '
							<script type="text/javascript"> 
								jQuery(document).ready(function(jQuery){';
									if ($meta == '') $meta ="fa-none";
									$output .= 'jQuery( ".'.$field['id'].' .'.$meta.'" ).siblings( ".active" ).removeClass( "active" );
									jQuery( ".'.$field['id'].' .'.$meta.'" ).addClass("active");';
							$output.='
									jQuery(".'.$field['id'].' i").click(function(){
										var val=(jQuery(this).attr("class").split(" ")[0]!="fa-none" ? jQuery(this).attr("class").split(" ")[0]:"");
										jQuery("#'.$field['id'].'font_icon").val(val);
										jQuery(this).siblings( ".active" ).removeClass( "active" );
										jQuery(this).addClass("active");
									});
								});
							</script>';
							$html.= $output;
						break; 	
						
						case 'upload':
							//wp_enqueue_media();
							$image = __PW_LIVESEARCH_ROOT_DIR__.'/assets/images/pw-transparent.gif';
							if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium'); $image = $image[0]; }
						
							$html.= '<input name="'.$field['id'].'" id="'.$field['id'].'" type="hidden" class="custom_upload_image '.$field['id'].'" value="'.(isset($meta) ? $meta:'').'" /> 
							<img src="'.(isset($image) ? $image:'').'" class="custom_preview_image" alt="" />
							<input name="btn" class="pw_woo_search_upload_image_button button" type="button" value="'.__('Choose Image',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'" /> 
							<button type="button" class="pw_woo_ad_search_remove_image_button button">Remove image</button>';  
						break;
						
						case 'loading_type':
							$html.= '<input type="hidden" id="'.$field['id'].'_font_icon" name="'.$field['id'].'" value="'.$meta.'"/>';
							$html.= '<div class="'.$field['id'].' pw_iconpicker_grid pw_iconpicker_loading" id="benefit_image_icon">';
							include(__PW_LIVESEARCH_ROOT_DIR__ .'/includes/loading-icon.php');
							$html.= '</div>';
							$output = '
							<script type="text/javascript"> 
								jQuery(document).ready(function(jQuery){';
									if ($meta == '') $meta ="fa-none";
									$output .= 'jQuery( ".'.$meta.'" ).siblings( ".active" ).removeClass( "active" );
									jQuery( ".'.$meta.'" ).addClass("active");';
							$output.='
									jQuery(".'.$field['id'].' i").click(function(){
										var val=(jQuery(this).attr("class").split(" ")[0]!="fa-none" ? jQuery(this).attr("class").split(" ")[0]:"");
										jQuery("#'.$field['id'].'_font_icon").val(val);
										jQuery(this).siblings( ".active" ).removeClass( "active" );
										jQuery(this).addClass("active");
									});
								});
							</script>';
							$html.= $output;
						break;
						
						case "default_archive_grid":
						{
							global $pw_woo_ad_main_class,$wpdb;
			
							$query_meta_query=array('relation' => 'AND');
							$query_meta_query[] = array(
														'key' => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'shortcode_type',
														'value' => "search_archive_page",
														'compare' => '=',
													);
							
							$args=array('post_type' => 'ad_woo_search_grid',
										'post_status'=>'publish',
										'meta_query' => $query_meta_query,
									 );
							
							$html.= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="'.$field['id'].'" style="width: 170px;">
									<option>'.__('Choose Shorcode',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';  
							
							$my_query_archive = new WP_Query($args);
							if( $my_query_archive->have_posts()):
								while ( $my_query_archive->have_posts() ) : $my_query_archive->the_post(); 							
									$html.= '<option value="'.get_the_ID().'" '.selected($meta,get_the_ID(),0).'>'.get_the_title().'</option>';
								endwhile;	
							endif;	
							
							$html.= '</select>';
						}
						break;
						
						case "pw_sendto_form_fields":
						{
							$html.= '
							<label class="pw_showhide" for="displayProduct-price"><input name="'.$field['id'].'[name_from]" type="checkbox" '.(is_array($meta) && in_array("name_from",$meta) ? "CHECKED": "").' value="name_from" class="displayProduct-eneble">'.__('Name (From) Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>
							
							<label class="pw_showhide" for="displayProduct-price"><input name="'.$field['id'].'[name_to]" type="checkbox" '.(is_array($meta) && in_array("name_to",$meta) ? "CHECKED": "").' value="name_to" class="displayProduct-eneble">'.__('Name (To) Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>                            
											
							<label class="pw_showhide" for="displayProduct-star"><input name="'.$field['id'].'[email]" type="checkbox" '.(is_array($meta) && in_array("email",$meta) ? "CHECKED": "").' value="email" class="displayProduct-eneble">'.__('Email (To) Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>                                    
														
							<label class="pw_showhide" for="displayProduct-metatag"><input name="'.$field['id'].'[description]" type="checkbox" '.(is_array($meta) && in_array("description",$meta) ? "CHECKED": "").' value="description">'.__('Description Field',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' </label>
							';
						}
						break;
						
						case 'multi_select': 
						{ 
							
							$html.= '<select name="'.$field['id'].'[]" id="'.$field['id'].'" style="width: 170px;" class="chosen-select-build" multiple="multiple">';  
							foreach ($field['options'] as $option) {  
								$selected='';
								if(is_array($meta) && in_array($option['value'],$meta))
									$selected='SELECTED';
								$html.= '<option '. $selected.' value="'.$option['value'].'">'.$option['label'].'</option>';  
							}  
							$html.= '</select><br /><span class="description">'.$field['desc'].'</span>';  
							
							$html.= '			
							<script type="text/javascript"> 
								jQuery(document).ready(function(){
									var visible = true;
									setInterval(
										function()
										{
											if(visible)
												if(jQuery(".chosen-select-build").is(":visible"))
												{
													visible = false;
													jQuery(".chosen-select-build").chosen();
												}
									}, 100);
									
								});
							</script>
							';
						}
						break;
						
						case 'multi_side':
						{
							global $wpdb;
							$options='';
							$selected_options='';
							
							if(is_array($meta)){
								foreach($meta as $opt){
									$selected_options.= '<option value="'.$opt.'" SELECTED>'.$opt.'</option>';
								}
							}
							
							$types = $wpdb->get_results("SELECT meta_key FROM ".$wpdb->postmeta." GROUP BY meta_key", ARRAY_A);
							if ($types!=null && is_array($types)) {
								foreach($types as $k=>$v) {
//								  if ($this->selected==null || !in_array($v['meta_key'], $this->selected)) {
									$options.= '<option value="'.$v['meta_key'].'">'.$v['meta_key'].'</option>';
	//							  }
								}
							  }
							$html.='
							<div class="row">
								
							</div>
							<div class="col-xs-4">
									<select name="from" id="undo_redo" class="form-control" size="11" multiple="multiple">
										'.$options.'
									</select>
								</div>
								
								<div class="col-xs-2">
									<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>
									<button type="button" id="undo_redo_rightAll" class="btn btn-default btn-block"><i class="fa fa-forward"></i></button>
									<button type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-right"></i></button>
									<button type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block"><i class="fa fa-chevron-left"></i></button>
									<button type="button" id="undo_redo_leftAll" class="btn btn-default btn-block"><i class="fa fa-backward"></i></button>
									<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>
								</div>
								
								<div class="col-xs-4">
									<select name="'.$field['id'].'[]"  id="undo_redo_to" class="form-control" size="11" multiple="multiple">'.$selected_options.'</select>
								</div>
							
							';
						}
						break;
						
						
					}
			}
			$html.= '</table>';
		$html.= '</section>';	
	}
	
	$html.= '</nav><!--END TAB-->';
	
	$html.= ' <div class="awr-setting-submit">
				<input type="submit" value="Save settings" class="button-primary"/>
			</div>
		</form>
	</div>
	
	<script type="text/javascript">
		function strpos(haystack, needle, offset) {
			var i = (haystack + "").indexOf(needle, (offset || 0));
			return i === -1 ? false : i;
		}
		
		jQuery(document).ready(function(){
			[].slice.call( document.querySelectorAll( ".tabsA" ) ).forEach( function( el ) {
				new CBPFWTabs( el );
			});
			
			////////////SHOW/HIDE CUSTOM FIELD SELECTION/////////////
			
			
			////////////END SHOW/HIDE CUSTOM FIELD SELECTION/////////////
			
		});	
	</script>
	';
	
	echo $html;
?>