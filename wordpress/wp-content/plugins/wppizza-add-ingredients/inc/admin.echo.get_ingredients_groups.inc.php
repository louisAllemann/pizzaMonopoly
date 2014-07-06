<?php
	$str='';

	/*********************************************************************************
	*
	*	[get all available group options to either be retrived by ajax when adding new
	*	or from DB for already entered groups]
	*
	**********************************************************************************/
	$groupOptions='';
	$groupOptions.="<div class='wppizza-custom-groups-details'>";


			$infoVisibility='block';
			if(isset($dbVal['type']) && $dbVal['type']>=5){$infoVisibility='none';}
			/*set to textbox**/
			$txtBoxVisibility='block';$isTextbox=false;

			if((isset($dbVal['sizes']) && is_string($dbVal['sizes']) && $dbVal['sizes']=='textbox') || (is_string($tierSelected) && $tierSelected=='textbox') ){$txtBoxVisibility='none';$isTextbox=true;}

			$groupOptions.="<div class='wppizza-custom-group-info-".$id."' style='display:".$infoVisibility."'>";

				$groupOptions.="<span class='wppizza-custom-group-sort-".$id."' style='display:".$txtBoxVisibility.";float:left'>";
				$groupOptions.="".__('Sort', $this->pluginLocale).": ";
					$value=($dbVal!='-1') ? ''.$dbVal['sort'].'' : '0' ;
				$groupOptions.="<input name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][sort]' size='2' type='text' value='".$value."' />";
				$groupOptions.="</span>";


				if($isTextbox){
					$groupOptions.="".__('Label [i.e "comments" etc]', $this->pluginLocale)." ";
				}else{
					$groupOptions.=' '.__('Group Label', $this->pluginLocale).': ';
				}
					$value=($dbVal!='-1')  ? $dbVal['label'] : '' ;
				$groupOptions.='<input name="'.$this->pluginSlug.'[ingredients_custom_groups]['.$id.'][label]" size="30" type="text" value="'.$value.'">';
			$groupOptions.="</div>";

			$groupOptions.="<div class='wppizza-custom-group-info-".$id."' style='display:".$infoVisibility."'>";
				if($isTextbox){
					$groupOptions.=' '.__('Additional Info (after label)', $this->pluginLocale).': ';
				}else{
					$groupOptions.=' '.__('Additional Info (after group label)', $this->pluginLocale).': ';
				}
					$value=($dbVal!='-1')  ? $dbVal['info'] : '' ;
					$groupOptions.='<input name="'.$this->pluginSlug.'[ingredients_custom_groups]['.$id.'][info]" size="40" type="text" value="'.$value.'">';
			$groupOptions.="</div>";

			$groupOptions.="<div class='wppizza-custom-group-info-".$id."'  style='display:".$infoVisibility."'>";
				$groupOptions.="<input name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][position]' type='checkbox'  ".checked($dbVal['position'],true,false)."  value='1' />";
				if($isTextbox){
					$groupOptions.=" ".__('Display after all Groups ?', $this->pluginLocale)." ";
				}else{
					$groupOptions.=" ".__('Display After Regular Groups (if any) ?', $this->pluginLocale)." ";
				}
				if(!$isTextbox){
					$groupOptions.=" (".__('By default custom groups will be displayed BEFORE any regular groups', $this->pluginLocale).") ";
				}
			$groupOptions.="</div>";

			if(!$isTextbox){/*no need to display these when textbox group*/
				/**hide prices**/
				$groupOptions.="<div class='wppizza-custom-group-info-".$id."'  style='display:".$infoVisibility."'>";
					if(isset($dbVal['hide_prices']) && $dbVal['hide_prices']==1){$chk=' checked="checked"';}else{$chk='';}/**to not throw php notices when upgrading plugin from previous versions, let's not use checked();*/
					$groupOptions.="<input name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][hide_prices]' type='checkbox'  ".$chk."  value='1' />";
					$groupOptions.=" ".__('Do not display prices after individual ingredients', $this->pluginLocale)." ";
				$groupOptions.="</div>";

				/**sort by price**/
				$groupOptions.="<div class='wppizza-custom-group-info-".$id."'  style='display:".$infoVisibility."'>";
					if(isset($dbVal['sort_by_price_first']) && $dbVal['sort_by_price_first']==1){$chk=' checked="checked"';}else{$chk='';}/**to not throw php notices when upgrading plugin from previous versions, let's not use checked();*/
					$groupOptions.="<input name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][sort_by_price_first]' type='checkbox'  ".$chk."  value='1' />";
					$groupOptions.=" ".__('Sort by price first', $this->pluginLocale)." ";
				$groupOptions.="</div>";
			}


			$groupOptions.="<div style='display:".$txtBoxVisibility."'>";
			$groupOptions.="<div class='wppizza-custom-group-info-".$id."'  style='display:".$infoVisibility."'>";
				/**whole only**/
				if(isset($dbVal['whole_only']) && $dbVal['whole_only']==1){$chk=' checked="checked"';}else{$chk='';}/**to not throw php notices when upgrading plugin from previous versions, let's not use checked();*/
				$groupOptions.="<input name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][whole_only]' type='checkbox'  ".$chk."  value='1' />";
				$groupOptions.=" ".__('Ingredients can only be applied to whole menu item even if half and/or quarters are enabled (has no effect when only whole is enabled)', $this->pluginLocale)."";
			$groupOptions.="</div>";
			$groupOptions.="</div>";


		$groupOptions.="<div style='display:".$txtBoxVisibility."'>";
			$groupOptions.='<b>'.__('Custom Group Type', $this->pluginLocale).'</b><br/>';
			$groupOptions.="<select id='wppizza_custom_groups_type_".$id."' name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][type]' class='wppizza-custom-groups-type'>";
				$groupOptions.="<option value='0' ".selected($dbVal['type'],0,false).">".__('Default - [just like regular groups]', $this->pluginLocale)."</option>";
				$groupOptions.="<option value='1' ".selected($dbVal['type'],1,false).">".__('Group must have one - *and only one ingredient one time* - selected (radio input)', $this->pluginLocale)."</option>";
				$groupOptions.="<option value='2' ".selected($dbVal['type'],2,false).">".__('Group must have one - *and only one* ingredient - selected, but can be select multiple times ', $this->pluginLocale)."</option>";
				$groupOptions.="<option value='3' ".selected($dbVal['type'],3,false).">".__('Group must have *AT LEAST* minimum number of ingredient selected below (multiple allowed - but no more than 1x per ingredient)', $this->pluginLocale)."</option>";
				$groupOptions.="<option value='4' ".selected($dbVal['type'],4,false).">".__('Group must have *AT LEAST* minimum number of ingredient selected below (multiple allowed and multiple per ingredient)', $this->pluginLocale)."</option>";
				$groupOptions.="<option value='5' ".selected($dbVal['type'],5,false).">".__('EXCLUDE ingredient (any one selected here will be EXCLUDED from this group and for this/these item(s) - regardless of any other settings)', $this->pluginLocale)."</option>";
				$groupOptions.="<option value='6' ".selected($dbVal['type'],6,false).">".__('PRESELECT (preselect ingredient with price added to the base price. If another custom group allows no more than one, first will be selected)', $this->pluginLocale)."</option>";
			$groupOptions.="</select>";

			/**checkbox preselected prices are 0**/
			$preSelZeroPriceVisibility='none';
			if(isset($dbVal['type']) && $dbVal['type']==6 ){$preSelZeroPriceVisibility='block';}
			$groupOptions.="<div id='wppizza-custom-group-presel-".$id."' class='wppizza-custom-group-presel-ing' style='display:".$preSelZeroPriceVisibility."'>";
			if(isset($dbVal['preselpricezero']) && $dbVal['preselpricezero']==1){$chk=' checked="checked"';}else{$chk='';}/**to not throw php notices when upgrading plugin from previous versions, let's not use checked();*/
			$groupOptions.=" <input name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][preselpricezero]' type='checkbox'  ".$chk."  value='1' />";
			$groupOptions.=' '.__('Preselected Ingredients Prices are 0 [any *additional* selection of the same, pre-selected ingredient will be added by price set]', $this->pluginLocale).'';
			$groupOptions.="</div>";


			$maxIngVisibility='none';
			if(isset($dbVal['type']) && ($dbVal['type']==3 || $dbVal['type']==4 )){$maxIngVisibility='block';}
			$groupOptions.="<div id='wppizza-custom-group-min-ing-".$id."' class='wppizza-custom-group-min-ing' style='display:".$maxIngVisibility."'>";
				$value=($dbVal!='-1' && isset($dbVal['min_ing']))  ? $dbVal['min_ing'] : '1' ;
				$groupOptions.='<input name="'.$this->pluginSlug.'[ingredients_custom_groups]['.$id.'][min_ing]" size="2" type="text" value="'.$value.'">';
				$groupOptions.=' '.__('<b>Minimum</b> number of <b>different</b> ingredients to select in this group (0 or more, but cannot be > *Maximum* number of *different* ingredients below [unless this is set to 0])', $this->pluginLocale).'';
			$groupOptions.="</div>";


			$minIngVisibility='none';
			if(isset($dbVal['type']) && ($dbVal['type']==3 || $dbVal['type']==4 )){$minIngVisibility='block';}
			$groupOptions.="<div id='wppizza-custom-group-max-ing-".$id."' class='wppizza-custom-group-max-ing' style='display:".$minIngVisibility."'>";
				$value=($dbVal!='-1' && isset($dbVal['max_ing']))  ? $dbVal['max_ing'] : '0' ;
				$groupOptions.='<input name="'.$this->pluginSlug.'[ingredients_custom_groups]['.$id.'][max_ing]" size="2" type="text" value="'.$value.'">';
				$groupOptions.=' '.__('<b>Maximum</b> number of <b>different</b> ingredients to select in this group [0 for unlimited]', $this->pluginLocale).'';
			$groupOptions.="</div>";

			$maxSameIngVisibility='none';
			if(isset($dbVal['type']) && ($dbVal['type']==2 || $dbVal['type']==4 )){$maxSameIngVisibility='block';}
			$groupOptions.="<div id='wppizza-custom-group-max-same-ing-".$id."' class='wppizza-custom-group-max-same-ing' style='display:".$maxSameIngVisibility."'>";

				$value=($dbVal!='-1' && isset($dbVal['max_same_ing']))  ? $dbVal['max_same_ing'] : '0' ;
				$groupOptions.='<input name="'.$this->pluginSlug.'[ingredients_custom_groups]['.$id.'][max_same_ing]" size="2" type="text" value="'.$value.'">';
				$groupOptions.=' '.__('Maximum number of <b>the same</b> ingredient selectable [0 for unlimited]', $this->pluginLocale).'';

				$value=($dbVal!='-1' && isset($dbVal['max_total_ing']))  ? $dbVal['max_total_ing'] : '0' ;
				$groupOptions.='<br />';
				$groupOptions.='<input name="'.$this->pluginSlug.'[ingredients_custom_groups]['.$id.'][max_total_ing]" size="2" type="text" value="'.$value.'">';
				$groupOptions.=' '.__('Maximum <b>total sum of all</b> ingredients selected in this group [0 to ignore]', $this->pluginLocale).'';


				$value=($dbVal!='-1' && isset($dbVal['min_total_ing']))  ? $dbVal['min_total_ing'] : '0' ;
				$groupOptions.='<br />';
				$groupOptions.='<input name="'.$this->pluginSlug.'[ingredients_custom_groups]['.$id.'][min_total_ing]" size="2" type="text" value="'.$value.'">';
				$groupOptions.=' '.__('Minimum <b>total sum of all</b> ingredients that have to be selected in this group [0 to ignore. must be less or equal to Maximum <b>total sum</b>]', $this->pluginLocale).'';


			$groupOptions.="</div>";

		$groupOptions.="</div>";
		
		/**get selectable ingredients**/
		$selectableIngredients='';
		if(isset($this->pluginOptions['ingredients']) && is_array($this->pluginOptions['ingredients'])){
			foreach($this->pluginOptions['ingredients'] as $iid=>$iv){
			if($tierSelected==$iv['sizes'] && $iv['enabled']){/*set by sizes above*/
				if(isset($dbVal['ingredient'][$iid]) && $dbVal['ingredient'][$iid]==$iid){$chk=' checked="checked"';}else{$chk='';}/**to not throw php notices when upgrading plugin from previous versions, let's not use checked();*/
				$selectableIngredients.="<span class='button' id='".$this->pluginSlug."-ingredients_custom_group-".$id."-ingredient-".$iid."'>".$iv['item']." <input ".$chk." name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][ingredient][".$iid."]' title='".__('enabled', $this->pluginLocale)."' type='checkbox'  value='".$iid."' /></span> ";
			}
		}}



		$groupOptions.="<div style='padding:0;display:".$txtBoxVisibility."'>";
			$groupOptions.="<div style='padding:5px; background-color:#FCFCFC;display:".$infoVisibility."' class='wppizza-custom-group-info-".$id."'>";
			$groupOptions.='<b>'.__('Available Ingredients for this group', $this->pluginLocale).':</b>';
			$groupOptions.="<br/>";
			if($selectableIngredients!=''){
				$groupOptions.="<br/>";
				$groupOptions.='<b>'.__('IMPORTANT: Any Ingredients selected here will be taken out of any "Standard" Groups and must be enabled.', $this->pluginLocale).'</b>';
			}else{
				$groupOptions.='<p style="color:red;padding:10px">'.__('Sorry, there are no ingredients available and/or enabled for this group', $this->pluginLocale).'</p>';	
			}
			$groupOptions.="</div>";
			$groupOptions.="<div style='padding:5px;'>";
			$groupOptions.=$selectableIngredients;
			$groupOptions.="</div>";
		$groupOptions.="</div>";


		$groupOptions.="<div>";
			$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1, 'orderby'=>'title' ,'order' => 'ASC');
			$query = new WP_Query( $args );
			$groupOptions.=''.__('Assign to the following menu item(s)', $this->pluginLocale).': ';
			$groupOptions.="<br/>";
			$groupOptions.="<select class='".$this->pluginSlug."_custom_groups_item' name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][item][]' multiple='multiple'>";
				if($isTextbox){
					foreach($query->posts as $pKey=>$pVal){
						$pageName[$pVal->ID]=$pVal->post_title;
						$groupOptions.="<option value='".$pVal->ID."' ";
						if(isset($dbVal['item'][$pVal->ID])){
							$groupOptions.=" selected='selected'";
						}
						$groupOptions.=">".$pVal->post_title."</option>";
					}
				}else{
					foreach($query->posts as $pKey=>$pVal){
						$pageName[$pVal->ID]=$pVal->post_title;
						$meta=get_post_meta($pVal->ID, WPPIZZA_POST_TYPE, true );
						if($meta['sizes']==$tierSelected){
							$groupOptions.="<option value='".$pVal->ID."' ";
							if(isset($dbVal['item'][$pVal->ID])){
								$groupOptions.=" selected='selected'";
							}
							$groupOptions.=">".$pVal->post_title."</option>";
						}
					}
				}
			$groupOptions.="</select>";
			$groupOptions.='<br/>'.__('Ctrl+Click to select more than one', $this->pluginLocale).'';
		$groupOptions.="</div>";
	$groupOptions.="</div>";


	/************************************************************************
	*
	*	[we are adding a new group or get settings of a group from db ]
	*
	*
	*************************************************************************/
	if($tierSelected=='-1' || $dbVal!='-1'){
		$str.=PHP_EOL.PHP_EOL."<span class='wppizza_option'>";

			$str.="<div id='wppizza-custom-groups-header-".$id."' class='wppizza-custom-groups-header button'>";
				$str.="".__('Select Group to Customise', $this->pluginLocale)."";
				$str.="<select id='".$this->pluginSlug."_group_select_".$id."' class='".$this->pluginSlug."_group_select wppizza-getkey' name='".$this->pluginSlug."[ingredients_custom_groups][".$id."][sizes]'>";
					$str.="<option value=''>--".__('Select Group',$this->pluginLocale)."--</option>";
					foreach($optionSizes as $l=>$m){
						$ident=empty($this->masterOptions['sizes'][$l][0]['lbladmin']) ? 'ID:'.$l.'' : '"'.$this->masterOptions['sizes'][$l][0]['lbladmin'].'"' ;
						$str.="<option value='".$l."' ".selected($dbVal['sizes'],$l,false).">".implode(", ",$m['lbl'])." [".$ident."]</option>";
					}
					$str.="<option value='textbox' ".selected($dbVal['sizes'],'textbox',false).">--".__('Add Textbox to Item',$this->pluginLocale)."--</option>";
				$str.="</select>";
				$str.="<a href='#' class='wppizza-delete button' style='float:right' title='".__('delete', $this->pluginLocale)."'> [X] </a>";
				$str.="<span class='wppizza-custom-groups-visibility button' style='float:right'>".__('show/hide details', $this->pluginLocale)."</span>";



				/***show some info for easier identification**/
				if($tierSelected>=0){
					$str.="<div class='wppizza-custom-groups-header-info'>";
						$str.="".__('Current:')." ";

						if($dbVal['type']<5){$str.=" <b>";}else{$str.=" <span>";}

							/**sort*/

							/**label*/
							$str.="<b style='color:black'>";
							if($dbVal['type']==0){$str.="".$dbVal['label']."";}
							if($dbVal['type']==1){$str.="".$dbVal['label']."";}
							if($dbVal['type']==2){$str.="".$dbVal['label']."";}
							if($dbVal['type']==3){$str.="".$dbVal['label']."";}
							if($dbVal['type']==4){$str.="".$dbVal['label']."";}
							if($dbVal['type']==5){$str.="".__('Exclude Ingredients', $this->pluginLocale)."";}
							if($dbVal['type']==6){$str.="".__('Preselect Ingredients', $this->pluginLocale)."";}
							$str.="</b>";
							if($dbVal['sizes']=='textbox'){$str.=" (".__('Textbox', $this->pluginLocale).") ";}

							$str.=" | ";



							if($dbVal['type']<5){
								$str.="".__('Position:')." ";
								$str.=($dbVal!='-1') ? ''.$dbVal['sort'].'' : '0' ;
								/*pre post regular*/
								if(isset($dbVal['position']) && $dbVal['position']==1 ){
									$str.="-".__('post', $this->pluginLocale)."";
								}else{
									$str.="-".__('pre', $this->pluginLocale)."";
								}
								$str.=" | ";
							}

							//if($dbVal['sizes']!='textbox'){
								/**menu items**/
								$str.=" ".__('Items:')." ";
								if(count($dbVal['item'])==0){
									$str.=" ".__('--', $this->pluginLocale)." ";
								}
								if(count($dbVal['item'])>=1 && count($dbVal['item'])<=2){
									$mI=array();
									foreach($dbVal['item'] as $k=>$v){
										$mI[$k]=$pageName[$k];
									}
									$str.=implode(", ",$mI);
								}
								if(count($dbVal['item'])>2){
									$str.="".count($dbVal['item'])." ".__('Menu Items', $this->pluginLocale)."";
								}
							//}


						if($dbVal['type']<5){$str.=" </b>";}else{$str.="</span>";}
					$str.="</div>";
				}


			$str.="</div>";

			/**holds all options fetched via ajax or from db**/
			$str.="<div id='".$this->pluginSlug."_group_edit_".$id."' class='".$this->pluginSlug."_group_edit' style='display:none'>";
				/*if a current option fetched from db insert them directly*/
				if($tierSelected>=0 && $dbVal!='-1'){$str.=$groupOptions;}
			$str.="</div>";

		$str.="</span>";
	}
	/************************************************************************
	*
	*	[if options fetched via ajax ( for new groups, just return those,
	*	as they will be inserted in the right place via js]
	*
	*************************************************************************/
	if($tierSelected>=0 && $dbVal=='-1'){
		$str.=$groupOptions;
	}

?>