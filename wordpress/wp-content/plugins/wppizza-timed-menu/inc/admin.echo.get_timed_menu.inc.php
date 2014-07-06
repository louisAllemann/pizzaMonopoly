<?php
		$str='';
		$str.="<tr class='wppizza_".$field."_option wppizza-row-remove wppizza-tm-display-".$display."'>";


				$str.="<td>";
					/*Label*/
					$value=!empty($options[$field][$k]['label']) ? $options[$field][$k]['label'] : '';
					$str.="<input id='wppizza_".$field."_label_".$k."' name='".$this->wpptmSlug."[".$field."][".$k."][label]' size='20' class='wppizza-getkey wppizza_".$field."_label' type='text' value='".$value."' placeholder='".__('Label', $this->wpptmLocale)."'/> ";
					/**display type**/
					$displayValue=!empty($options[$field][$k]['display']) ? $options[$field][$k]['display'] : $display;
					if($displayValue=='internal'){
						$postPagesVis='none';
						$internalVis='table-cell';
					}else{
						$postPagesVis='table-cell';
						$internalVis='none';
					}
					$str.="<input id='wppizza_".$field."_display_".$k."' class='wppizza_".$field."_display'  name='".$this->wpptmSlug."[".$field."][".$k."][display]'  type='hidden' value='".$displayValue."' /> ";
				$str.="</td>";

				/**Select menu items**/
				if(count($itemsCatsPagesPosts['items'])>0){
				$str.="<td>";
					$str.= "<span id='wppizza_".$field."_menu_item_".$k."' class=' wppizza_".$field."_menu_item' style='margin:20px 0'>";
							$str.="<select name='".$this->wpptmSlug."[".$field."][".$k."][menu_item][]' multiple='multiple' id='wppizza_".$field."_".$k."_menu_item' class='wppizza_tm_menu_item'>";
							foreach($itemsCatsPagesPosts['items'] as $pKey=>$pVal){
								$str.="<option value='".$pVal['id']."' ";
									if(isset($options[$field][$k]['menu_item']) && in_array($pVal['id'],$options[$field][$k]['menu_item'])){
										$str.=" selected='selected'";
									}
								$str.=">".$pVal['title']."</option>";
							}
							$str.="</select>";
							$str.='<br/>'.__('Ctrl+Click to select multiple', $this->wpptmLocale).'';
					$str.= "</span>";
				$str.="</td>";
				}

				/***categories**/
				if(count($itemsCatsPagesPosts['categories'])>0){
				$str.="<td style='display:".$internalVis."' class='wppizza-tm-int'>";
					$str.= "<span id='wppizza_".$field."_categories_".$k."' class=' wppizza_".$field."_categories' style='margin:20px 0'>";
							$str.="<select name='".$this->wpptmSlug."[".$field."][".$k."][categories][]' multiple='multiple' id='wppizza_".$field."_".$k."_categories' class='wppizza_tm_categories'>";
							foreach($itemsCatsPagesPosts['categories'] as $pKey=>$pVal){
								$str.="<option value='".$pVal['id']."' ";
									if(isset($options[$field][$k]['categories'][$pVal['id']])){
										$str.=" selected='selected'";
									}
								$str.=">".str_repeat("-", $pVal['depth'])."".$pVal['title']."</option>";
							}
							$str.="</select>";
							$str.='<br/>'.__('Ctrl+Click to select multiple', $this->wpptmLocale).'';
					$str.= "</span>";
				$str.="</td>";
				}


				/***pages**/
				if(count($itemsCatsPagesPosts['pages'])>0){
				$str.="<td style='display:".$postPagesVis."' class='wppizza-tm-pp'>";
					$str.= "<span id='wppizza_".$field."_pages_".$k."' class=' wppizza_".$field."_pages' style='margin:20px 0'>";
							$str.="<select name='".$this->wpptmSlug."[".$field."][".$k."][pages][]' multiple='multiple' id='wppizza_".$field."_".$k."_pages' class='wppizza_tm_pages'>";
							foreach($itemsCatsPagesPosts['pages'] as $pKey=>$pVal){
								$str.="<option value='".$pVal['id']."' ";
									if(isset($options[$field][$k]['pages'][$pVal['id']])){
										$str.=" selected='selected'";
									}
								$str.=">".$pVal['title']."</option>";
							}
							$str.="</select>";
							$str.='<br/>'.__('Ctrl+Click to select multiple', $this->wpptmLocale).'';
					$str.= "</span>";
				$str.="</td>";
				}

//				/***posts -> actually disabled as you open a can of worms**/
//				if(count($itemsCatsPagesPosts['posts'])>0){
//				$str.="<td style='display:".$postPagesVis."' class='wppizza-tm-pp'>";
//					$str.= "<span id='wppizza_".$field."_posts_".$k."' class=' wppizza_".$field."_posts' style='margin:20px 0'>";
//							$str.="<select name='".$this->wpptmSlug."[".$field."][".$k."][posts][]' multiple='multiple' id='wppizza_".$field."_".$k."_posts' class='wppizza_tm_posts'>";
//							foreach($itemsCatsPagesPosts['posts'] as $pKey=>$pVal){
//								$str.="<option value='".$pVal['id']."' ";
//									if(isset($options[$field][$k]['posts']) && in_array($pVal['id'],$options[$field][$k]['posts'])){
//										$str.=" selected='selected'";
//									}
//								$str.=">".$pVal['title']."</option>";
//							}
//							$str.="</select>";
//							$str.='<br/>'.__('Ctrl+Click to select multiple', $this->wpptmLocale).'';
//					$str.= "</span>";
//				$str.="</td>";
//				}



				$str.="<td>";
					/*start date*/
					$value=!empty($options[$field][$k]['start_date']) ? date("d M Y",strtotime($options[$field][$k]['start_date'])) : '';
					$str.="<input id='wppizza_".$field."_start_date_".$k."' name='".$this->wpptmSlug."[".$field."][".$k."][start_date]' size='20' class='wppizza-date-select wppizza_".$field."_start_date' type='text' value='".$value."' placeholder='".__('Start Date', $this->wpptmLocale)."'/> ";
				$str.="<br />";
					/*end date*/
					$value=!empty($options[$field][$k]['end_date']) ? date("d M Y",strtotime($options[$field][$k]['end_date'])) : '';
					$str.="<input id='wppizza_".$field."_end_date_".$k."' name='".$this->wpptmSlug."[".$field."][".$k."][end_date]' size='20'  class='wppizza-date-select wppizza_".$field."_end_date' type='text' value='".$value."' placeholder='".__('End Date', $this->wpptmLocale)."' /> ";
				$str.="</td>";



				$str.="<td>";
					/*start time*/
					$value=!empty($options[$field][$k]['start_time']) ? $options[$field][$k]['start_time'] : '';
					$str.="<input id='wppizza_".$field."_start_time_".$k."' name='".$this->wpptmSlug."[".$field."][".$k."][start_time]' class='wppizza-time-select wppizza_".$field."_start_time' size='20' type='text' value='".$value."' placeholder='".__('Start Time [HH:MM]', $this->wpptmLocale)."'/> ";
				$str.="<br />";
					/*end time*/
					$value=!empty($options[$field][$k]['end_time']) ? $options[$field][$k]['end_time'] : '';
					$str.="<input id='wppizza_".$field."_end_time_".$k."' name='".$this->wpptmSlug."[".$field."][".$k."][end_time]' class='wppizza-time-select wppizza_".$field."_end_time' size='20'  type='text' value='".$value."' placeholder='".__('End Time [HH:MM]', $this->wpptmLocale)."' /> ";
				$str.="</td>";


				/*days sorted by general->settings -> week start on*/
				$str.="<td>";
					$wStart=get_option('start_of_week');
					for($i=$wStart;$i<($wStart+7);$i++){
						$day=$i;
						if($i>6){$day=$i-7;}
						$str.="<label class='button'>".wpizza_format_weekday($day,'D')." <input id='wppizza_".$field."_day_".$k."_".$day."' class='wppizza_".$field."_day' name='".$this->wpptmSlug."[".$field."][".$k."][day][".$day."]'  type='checkbox' ".checked(isset($options[$field][$k]['day'][$day]),true,false)." value='".$day."' /></label>";
					}
				$str.="</td>";



				/****buttons**/
				$str.="<td>";
					$str.="<span class='button'>".__('on/off ', $this->wpptmLocale)."<input name='".$this->wpptmSlug."[".$field."][".$k."][enabled]' title='".__('enabled', $this->wpptmLocale)."' type='checkbox'  ". checked($options[$field][$k]['enabled'],true,false)." value='1' /></span>";
					$str.="<a href='#' class='wppizza-delete ".$field." button' title='".__('delete', $this->wpptmLocale)."'> [X] </a>";
				$str.="</td>";

		$str.="</tr>";
?>