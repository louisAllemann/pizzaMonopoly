<?php
	$customGroups =$this->pluginOptions['ingredients_custom_groups'];
	$ingredients =$this->pluginOptions['ingredients'];
	$post_meta_values = get_post_meta($meta_options->ID,WPPIZZA_SLUG,true);
	$selectedSize=$post_meta_values['sizes'];
	$meta_values_ingredients = get_post_meta($meta_options->ID,$this->pluginMetaValue,true);
	$canEnableIngredients=$this->wppizza_check_ingredient_sizes($selectedSize);
	if(!$canEnableIngredients || !isset($meta_values_ingredients[$this->pluginMetaValue])){
		$meta_values_ingredients[$this->pluginMetaValue]=false;
	}

	$meta_values_multi = get_post_meta($meta_options->ID,$this->pluginMetaMulti,true);
	if(!isset($meta_values_multi[$this->pluginMetaMulti])){
		$meta_values_multi[$this->pluginMetaMulti]=array(1=>true);
	}

	$meta_values_multi_divide = get_post_meta($meta_options->ID,$this->pluginMetaMultiDivide,true);

	/**do we allow ingredients per quarter/half etc ?*/
	$multiOptions=array(
		1=>array('lbl'=>__('whole', $this->pluginLocale)),
		2=>array('lbl'=>__('halfs', $this->pluginLocale)),
		4=>array('lbl'=>__('quarters', $this->pluginLocale))
	);

	/*******************************************************************************
	if we cannot enable "add ingredients" due to the fact that there are no
	corresponsing ingredients setup and enabled, display notification
	unless pricetier is changed or ingredient added, this box will not save checked
	********************************************************************************/
	$ingredientsNote='';
	if(!$canEnableIngredients){
		$ingredientsNote.="<p class='wppizza-red' style='font-size:110%'>";
		$ingredientsNote.="".__('Sorry, there are no ingredients associated with the price tier chosen for this item yet,', $this->pluginLocale)."<br/>";
		$ingredientsNote.="".__('If you wish to enable this option for this item, either create (and enable) at least one ingredient for this pricetier or select(and save) a different tier for this item.', $this->pluginLocale)."";
		$ingredientsNote.="</p> ";
	}

	$str='';

	/*->***  enable addition of ingredients by customer on/off***/
	$str.="<div class='".$this->pluginSlug."_option' style='overflow:auto'>";
	if($ingredientsNote!=''){
		$str.=$ingredientsNote;
	}
	$str.="<label>";
	$str.="".__('allow customers to add additional ingredients to this item ?', $this->pluginLocale).": ";
	$str.="<span class='button'>";
	$str.="".__('yes/no ', $this->pluginLocale)."";
	$str.="<input name='".$this->pluginSlug."[".$this->pluginMetaValue."]' type='checkbox' ". checked($meta_values_ingredients[$this->pluginMetaValue],true,false)." value='1' />";
	$str.="</span>";
	$str.="</label>";
	/**whole/half/quarter**/
	$str.="<div>";
		$str.="<table>";
			$str.="<tr>";
				$str.="<td>";
					$str.=" ".__('Allow Ingredients to be selected for', $this->pluginLocale).": ";
				$str.="</td>";
				foreach($multiOptions as $k=>$v){
				$str.="<td>";
					$str.="<label>";
						$str.="<span class='button'>";
						$str.="".$v['lbl']."";
						$str.="<input name='".$this->pluginSlug."[".$this->pluginMetaMulti."][".$k."]' type='checkbox' ". checked(isset($meta_values_multi[$this->pluginMetaMulti][$k]),true,false)." value='".$k."' />";
						$str.="</span>";
					$str.="</label>";
				$str.="</td>";
				}
			$str.="</tr>";

			$str.="<tr>";
				$str.="<td>";
					$str.=" ".__('Ingredient price (in %)', $this->pluginLocale).": ";
				$str.="</td>";
				foreach($multiOptions as $k=>$v){
					$val=!empty($meta_values_multi_divide[$this->pluginMetaMultiDivide][$k]) ? $meta_values_multi_divide[$this->pluginMetaMultiDivide][$k]: (100/$k);
					$str.="<td>";
						$str.="<input name='".$this->pluginSlug."[".$this->pluginMetaMultiDivide."][".$k."]' type='text'  size='5' value='".$val ."' />";
					$str.="</td>";
				}
			$str.="</tr>";
		$str.="</table>";
	$str.="</div>";
	/**info custom groups**/
	$str.="<div>";
		$str.=' '.__('Custom Groups applied', $this->pluginLocale).': ';
		$cGroupSummary=array();
		asort($customGroups);
		foreach($customGroups as $k=>$cGroup){
			if(in_array($meta_options->ID,$cGroup['item'])){
				$cGroupSummary[$k]['lbl']='';
					if($cGroup['type']>=0 && $cGroup['type']<5 ){$cGroupSummary[$k]['lbl'].="".$cGroup['label']."";}
					if($cGroup['type']==5){$cGroupSummary[$k]['lbl'].="".__('Exclude Ingredients', $this->pluginLocale)."";}
					if($cGroup['type']==6){$cGroupSummary[$k]['lbl'].="".__('Preselect Ingredients', $this->pluginLocale)."";}


				$cGroupSummary[$k]['ingr']='';
					if($cGroup['type']=='textbox'){
						$cGroupSummary[$k]['ingr'].="".__('Textbox', $this->pluginLocale)."";
					}else{
					if(count($cGroup['ingredient']>0)){
						$selIngr=array();
						foreach($cGroup['ingredient'] as $iId){
							$selIngr[]=$ingredients[$iId]['item'];
						}
						$cGroupSummary[$k]['ingr'].="".implode(", ",$selIngr)."";
					}}
			}
		}
		if(count($cGroupSummary)==0){$str.="".__('none', $this->pluginLocale)."";}else{
			$str.="<span class='button wppizza_ingr_summary_toggle'>".__('show/hide', $this->pluginLocale)."</span>";
			$str.="<table class='wppizza_ingr_summary' style='display:none'>";
				$str.="<thead>";
				$str.="<tr>";
					$str.="<td>".__('Group Label', $this->pluginLocale)."</td>";
					$str.="<td>".__('Ingredients', $this->pluginLocale)."</td>";
				$str.="</tr>";
				$str.="</thead>";
				$str.="<tbody>";
			foreach($cGroupSummary as $k=>$v){
				$str.="<tr>";
					$str.="<td>".$v['lbl']."</td>";
					$str.="<td>".$v['ingr']."</td>";
				$str.="</tr>";
			}
			$str.="</tbody>";
			$str.="</table>";

		}

	$str.="</div>";
	$str.="</div>";

	print"".$str;
?>