<?php
	$str='';
	$str.="<span class='wppizza_option'>";
	
	if(!is_array($v)){/**for legacy reasons as it really should have been an array to start off with**/
		$additiveVal['name']=$v;
		$additiveVal['sort']='';
	}else{
		$additiveVal=$v;	
	}

	$str.="".__('sort', $this->pluginLocale).": <input name='".$this->pluginSlug."[".$field."][".$k."][sort]' size='3' type='text' value='". $additiveVal['sort'] ."' placeholder=''/>";
	$str.="".__('name', $this->pluginLocale).": <input id='wppizza_".$field."_".$k."' name='".$this->pluginSlug."[".$field."][".$k."][name]' size='30' class='wppizza-getkey' type='text' value='".$additiveVal['name']."' />";	
	/**if not in use or just added via js***/
	if(!isset($optionInUse) || (isset($optionInUse) && !in_array($k,$optionInUse['additives']))){
		$str.="<a href='#' class='wppizza-delete button' title='".__('delete', $this->pluginLocale)."'> [X] </a>";
	}else{
		$str.="".__('in use', $this->pluginLocale)."";
	}
	$str.="</span>";
?>