<?php
	$options = $this->pluginOptions;
	$optionsSizes =wppizza_sizes_available($options['sizes']);
	$optionsCurrency =$options['order']['currency_symbol'];
	$optionsDecimals =$options['layout']['hide_decimals'];
	$meta_values = get_post_meta($meta_options->ID, $this->pluginSlug);
	$meta_values = $meta_values[0];

	$str='';

	/****  pricetiers and prices ***/
	$str.="<div class='".$this->pluginSlug."_option'>";
	$str.="<div class='wppizza-meta-label'>".__('price tier and prices', $this->pluginLocale).":</div> ";
	$str.="<select name='".$this->pluginSlug."[sizes]' class='wppizza_pricetier_select'>";
	foreach($optionsSizes as $l=>$m){
		if($l==$meta_values['sizes']){$sel=" selected='selected'";}else{$sel='';}
		$ident=!empty($options['sizes'][$l][0]['lbladmin']) && $options['sizes'][$l][0]['lbladmin']!='' ? $options['sizes'][$l][0]['lbladmin'] :'ID:'.$l.'';		
		$str.="<option value='".$l."'".$sel.">".implode(", ",$m['lbl'])." [".$ident."]</option>";
	}
	$str.="</select>";
	$str.="<span class='wppizza_pricetiers'>";
		foreach($meta_values['prices'] as $k=>$v){
			$str.="<input name='".$this->pluginSlug."[prices][]' size='5' type='text' value='".wppizza_output_format_price($v,$optionsDecimals)."' />".$optionsCurrency."";
	}
	$str.="</span>";
	$str.="</div>";
		
	if(isset($options['additives']) && is_array($options['additives']) && count($options['additives'])>0){
		/*->*** which additives in item ***/
		$str.="<div class='".$this->pluginSlug."_option'>";
		$str.="<div class='wppizza-meta-label'>".__('contains additives', $this->pluginLocale).":</div> ";
		asort($options['additives']);//sort but keep index
		foreach($options['additives']  as $s=>$o){
			if(!is_array($o)){$lbl=$o;}else{$lbl=''.$o['name'];}/*legacy*/
			$str.="<label class='button'>";
			$str.="<input name='".$this->pluginSlug."[additives][".$s."]' size='5' type='checkbox' ". checked(in_array($s,$meta_values['additives']),true,false)." value='".$s."' /> ".$lbl."";
			$str.="</label>";			
		}
		$str.="</div>";
	}

	print"".$str;
?>