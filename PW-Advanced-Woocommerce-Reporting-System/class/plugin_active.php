<?php
	
	$pw_active_plugin = array(
		array(  
			'label'	=> __('Domain Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Domain Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_domain_name',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_domain_name',
			'type'	=> 'text',		
		),
		array(  
			'label'	=> __('Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
			'desc'	=> __('Enter Your Purchase Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),		
			'name'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code',
			'id'	=> __PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code',
			'type'	=> 'text',		
		)
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
			
		global $pw_rpt_main_class;
		$field=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code';
		$pw_rpt_main_class->pw_plugin_status=get_option($field);
		
		$text='';
		if ($pw_rpt_main_class->dashboard($pw_rpt_main_class->pw_plugin_status)){
			$text=__('Congratulation, The Plugin has been Activated Successfully !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			?>
                <div id="setting-error-settings_updated" class="updated settings-error">
                    <p><strong><?php echo $text;?></strong></p>
                </div>
            <?php
		}else{
			$text=__('Unfortunately, The Purchase code is Wrong, Please try Again !',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
			?>
                <div id="setting-error-settings_updated" class="error">
                    <p><strong><?php echo $text;?></strong></p>
                </div>
            <?php
		}
	}	
	
	$field_1=$pw_active_plugin[0];
	$field_2=$pw_active_plugin[1];
	
	$meta_1 = get_option($field_1['id']);  
	$meta_2 = get_option($field_2['id']);  
	
	$html= '<div class="wrap">
			<h2>'.__('Plugin Activate',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</h2>
			<br />
			<form method="POST" action="">
				<input type="hidden" name="update_settings" value="Y" />
				<table class="form-table">
					<tr > 
						<th><label for="'.$field_1['id'].'">'.$field_1['label'].'</label></th> 
						<td>
							<input type="text" name="'.$field_1['id'].'" id="'.$field_1['id'].'" class="'.$field_1['id'].'" value="'.$meta_1.'"/>
						</td>
					</tr>	
					<tr > 
						<th><label for="'.$field_2['id'].'">'.$field_2['label'].'</label></th> 
						<td>
							<input type="text" name="'.$field_2['id'].'" id="'.$field_2['id'].'" class="'.$field_2['id'].'" value="'.$meta_2.'"/>
						</td>
					</tr>	
				</table>
			<div class="awr-setting-submit">
				<input type="submit" value="Save settings" class="button-primary"/>
			</div>
		</form>
	</div>
	
	<script type="text/javascript">
		
	</script>
	';
	
	echo $html;
?>