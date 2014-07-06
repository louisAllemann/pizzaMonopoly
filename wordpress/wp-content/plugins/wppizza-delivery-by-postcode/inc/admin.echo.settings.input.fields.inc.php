<?php
			switch ( $tab ) :
				case 'deliveries' :

					if($field=='version'){
						echo "".$options['plugin_data'][$field]."";
					}
					if($field=='delivery_areas'){
						
						
						echo"<input name='".$this->dbpSlug."[".$field."_ctrl]' type='hidden'  value='1' />";/**make sure we are also submiting something (i.e empty array)if no timed item was defined**/
						
						
						echo"<div id='wppizza_".$field."'>";
						echo"<div id='wppizza_".$field."_options'>";
						if(is_array($options[$field])){
						asort($options[$field]);
						
						echo"<table id='wppizza_dbp_areas'>";
						$type=false;
						foreach($options[$field] as $k=>$v){
							/**labels**/
							if(!$type || $v['type']!=$type){
								$type=$v['type'];
								echo"<tr><th>";
									if($type=='per_item'){
										echo"".__('delivery charges per item', $this->dbpLocale);
									}
									if($type=='standard'){
										echo"".__('fixed delivery charges', $this->dbpLocale);
									}
									if($type=='no_delivery'){
										echo"".__('no delivery offered', $this->dbpLocale);
									}
									if($type=='minimum_total'){
										echo"".__('(free) delivery depending on conditions below', $this->dbpLocale);
									}				
									if($this->pluginOptions['order']['delivery_selected']!=$type){
										echo" <span class='description'>".__('invalid with your current "WPPizza->Order Settings->Delivery Charges" Settings', $this->dbpLocale)."</span>";
									}
								echo"</th></tr>";
							}
							/**area**/
							echo"<tr><td>".$this->wppizza_dbp_admin_get_delivery_areas($field,$k,$v,$options)."</td></tr>";
						}}
						echo"</table>";
						
						
						echo"</div>";
						echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('add new delivery area', $this->dbpLocale)."</a>";
						echo"</div>";
					}

				break;

				case 'frontend-settings' :
					if($field=='enabled'){
					echo"<div id='wppizza_".$field."'>";
						echo"<input name='".$this->dbpSlug."[frontend_settings][".$field."]' type='checkbox' ". checked($options['frontend_settings'][$field],true,false)." value='1' />";
						echo" ".__('Uncheck to disable/hide Post/Zip Code selection in frontend.', $this->dbpLocale)."<br/>";
					echo"</div>";
					}
					if($field=='required'){
					echo"<div id='wppizza_".$field."'>";
						echo"<input name='".$this->dbpSlug."[frontend_settings][".$field."]' type='checkbox' ". checked($options['frontend_settings'][$field],true,false)." value='1' />";
						echo" ".__('require customer to select a post/zipcode [you probably want this on]', $this->dbpLocale)."<br/>";
						
						echo"<input name='".$this->dbpSlug."[frontend_settings][no_required_onpickup]' type='checkbox' ". checked($options['frontend_settings']['no_required_onpickup'],true,false)." value='1' />";
						echo" ".__('do NOT require the customer to make a selection if he/she has selected "self-pickup" from your store', $this->dbpLocale)."<br/>";						
					echo"</div>";
					}
					if($field=='orderform_priority'){
					echo"<div id='wppizza_".$field."'>";
						echo"<select name='".$this->dbpSlug."[frontend_settings][".$field."]' >";
						echo"<option value='' >".__('after all formfields', $this->dbpLocale)."</option>";
						echo"<option value='0' ".selected($options['frontend_settings'][$field],0,false).">".__('before 1st formfield', $this->dbpLocale)."</option>";
						echo"<option value='1' ".selected($options['frontend_settings'][$field],1,false).">".__('before 2nd formfield', $this->dbpLocale)."</option>";
						echo"<option value='2' ".selected($options['frontend_settings'][$field],2,false).">".__('before 3rd formfield', $this->dbpLocale)."</option>";
						echo"<option value='3' ".selected($options['frontend_settings'][$field],3,false).">".__('before 4th formfield', $this->dbpLocale)."</option>";
						echo"<option value='4' ".selected($options['frontend_settings'][$field],4,false).">".__('before 5th formfield', $this->dbpLocale)."</option>";
						echo"<option value='5' ".selected($options['frontend_settings'][$field],5,false).">".__('before 6th formfield', $this->dbpLocale)."</option>";
						echo"<option value='6' ".selected($options['frontend_settings'][$field],6,false).">".__('before 7th formfield', $this->dbpLocale)."</option>";
						echo"<option value='7' ".selected($options['frontend_settings'][$field],7,false).">".__('before 8th formfield', $this->dbpLocale)."</option>";
						echo"<option value='8' ".selected($options['frontend_settings'][$field],8,false).">".__('before 9th formfield', $this->dbpLocale)."</option>";
						echo"<option value='9' ".selected($options['frontend_settings'][$field],9,false).">".__('before 10th formfield', $this->dbpLocale)."</option>";
						echo"<option value='10' ".selected($options['frontend_settings'][$field],10,false).">".__('before 11th formfield', $this->dbpLocale)."</option>";
						echo"</select>";
						echo" ".__('select where you would like to display the dropdown on the orderpage. Please note: it is irrelevant if any of the wppizza->settings->order form settings feilds are enabled or not. However, if any of the fields are not being displayed, the priority selected here might not correspond to the number of fields visible.', $this->dbpLocale)."<br/>";
					echo"</div>";
					}
					if($field=='show_on_load'){
					echo"<div id='wppizza_".$field."'>";
						echo"<input name='".$this->dbpSlug."[frontend_settings][".$field."]' type='checkbox' ". checked($options['frontend_settings'][$field],true,false)." value='1' />";
						echo" ".__('if enabled, a popupbox will appear on wppizza cart, wppizza menu items and wppizza order page pages. (but only if no selection has been made yet)', $this->dbpLocale)."";
						echo"<br/>";
						echo"<input name='".$this->dbpSlug."[frontend_settings][show_on_load_global]' type='checkbox' ". checked($options['frontend_settings']['show_on_load_global'],true,false)." value='1' />";
						echo" ".__('Popup on EVERY page regardless of whether or not it has any menu items, cart or orderpage ? (provided it is generally enabled above)', $this->dbpLocale)."";
						echo"<br/>";
						echo"<input name='".$this->dbpSlug."[frontend_settings][dont_show_on_load_if_closed]' type='checkbox' ". checked($options['frontend_settings']['dont_show_on_load_if_closed'],true,false)." value='1' />";
						echo" ".__('Do NOT show popup if shop is closed', $this->dbpLocale)."";

						echo"</div>";
					}
					if($field=='instant_search'){
					echo"<div id='wppizza_".$field."'>";
						echo"<input name='".$this->dbpSlug."[frontend_settings][".$field."]' type='checkbox' ". checked($options['frontend_settings'][$field],true,false)." value='1' />";
						echo" ".__('this will display an autocomplete textbox instead of a dropdown. Useful if you have A LOT of different locations. Experimental. Please let me know if you experience problems with this.', $this->dbpLocale)."<br/>";
					echo"</div>";

					echo"<br/><br/><b>".__('If you wish to display the delivery by post/zip-code selection in a different location use shortode: [wppizza_dbp]', $this->dbpLocale)."</b>";
					}

				break;

				case 'localization' :
						if($field=='localization'){
							/**to get descriptions include default options**/
							require_once('admin.setup.default.options.inc.php');
							echo"<div id='wppizza_".$field."'>";
								echo"<div id='wppizza_".$field."_options'>";
								$localize=array();
								foreach($options[$field] as $k=>$v){
									$localize[]=array('descr'=>$defaultOptions[$field][$k]['descr'],'name'=>''.$this->dbpSlug.'['.$field.']['.$k.']','value'=>$v['lbl']);
								}
								asort($localize);//sort but keep index
								foreach($localize as $k=>$v){
									echo "<input name='".$v['name']."' size='30' type='text' value='".$v['value']."' />";
									echo "".$v['descr']."<br/>";
								}
								echo"</div>";
							echo"</div>";
						}
				break;

				case 'access' :
					if($field=='access'){
						$this->wppdbpUserCaps->user_echo_admin_caps($this->wppizza_dbp_caps(),$this->dbpSlug,'admin_access_caps');
					}

				break;

				case 'license' :
					if($field=='license'){
						$slug		= $this->dbpSlug;
						$fieldName	= $this->dbpSlug."[license][key]";
						$license 	= $options['plugin_data']['license']['key'];
						$status 	= $options['plugin_data']['license']['status'];
						$this->wppdbpEdd->echo_edd_settings($slug,$fieldName,$license,$status);
					}
				break;



			endswitch;
?>