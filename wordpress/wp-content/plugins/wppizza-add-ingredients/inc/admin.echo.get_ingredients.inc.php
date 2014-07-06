<?php
		if($v==''){
			/*added via ajax. so lets get  and use the first available price tiers**/
			reset($optionSizes);
			$first_key = key($optionSizes);
			$v['ajaxprices']=$optionSizes[$first_key]['price'];
		}

		$str='';
		$str.="<span class='wppizza_option'>";

		$str.="<input id='wppizza_".$field."_".$k."' name='".$this->pluginSlug."[".$field."][".$k."][item]' size='30' class='wppizza-getkey' type='text' value='".$options[$field][$k]['item']."' />";
		$str.="<select name='".$this->pluginSlug."[".$field."][".$k."][sizes]' class='wppizza_pricetier_select ".$field."'>";
		foreach($optionSizes as $l=>$m){
			$ident=empty($this->masterOptions['sizes'][$l][0]['lbladmin']) ? 'ID:'.$l.'' : '"'.$this->masterOptions['sizes'][$l][0]['lbladmin'].'"' ;
			$str.="<option value='".$l."' ".selected($l,$options[$field][$k]['sizes'],false).">".implode(", ",$m['lbl'])." [".$ident."]</option>";//".implode(", ",$m['lbl'])."
		}
		$str.="</select>";
		$str.="<span class='wppizza_pricetiers'>";
		if(isset($v['prices'])){
		foreach($v['prices']  as $l=>$m){
			$str.="<input name='".$this->pluginSlug."[".$field."][".$k."][prices][]' size='5' type='text' value='".wppizza_output_format_price(wppizza_output_format_float($options[$field][$k]['prices'][$l]),$options['layout']['hide_decimals'])."' />";
		}}
		if(isset($v['ajaxprices'])){
		foreach($v['ajaxprices']  as $l=>$m){
			/* as this is a (new) ingredient, we probably dont want the default prices  here as they will be too big .*/
			$str.="<input name='".$this->pluginSlug."[".$field."][".$k."][prices][]' size='5' type='text' value='' />";
		}}

		$str.="</span>";

		$str.="<span class='button'>".__('on/off', $this->pluginLocale)." <input name='".$this->pluginSlug."[".$field."][".$k."][enabled]' title='".__('enabled', $this->pluginLocale)."' type='checkbox'  ". checked($options[$field][$k]['enabled'],true,false)." value='1' /></span>";
		$str.="<a href='#' class='wppizza-delete ".$field." button' title='".__('delete', $this->pluginLocale)."'> [X] </a>";

		if($copy){
			$str.="<span id='wppizza_ingr_copy_".$k."' class='wppizza_ingr_copy ".$field." ' title='".__('copy', $this->pluginLocale)."'> &#10064; </span>";
		}

		$str.="</span>";
?>