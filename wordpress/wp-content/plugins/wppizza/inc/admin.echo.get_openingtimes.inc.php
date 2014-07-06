<?php
	if(!isset($k)){
		$val=array('date'=>'','open'=>'','close'=>'');
	}else{
		$val=array('date'=>date("d M Y",strtotime($options['date'][$k])),'open'=>$options['open'][$k],'close'=>$options['close'][$k]);
	}
	$str='';
	$str.="<span class='wppizza_option'>";
	$str.="<input name='".$this->pluginSlug."[".$field."][date][]' size='10' type='text' class='wppizza-date-select' value='".$val['date']."' />";
	$str.="".__('open from', $this->pluginLocale).":";
	$str.="<input name='".$this->pluginSlug."[".$field."][open][]' size='3' type='text' class='wppizza-time-select' value='".$val['open']."' />";
	$str.="".__('to', $this->pluginLocale).":";
	$str.="<input name='".$this->pluginSlug."[".$field."][close][]' size='3' type='text' class='wppizza-time-select' value='".$val['close']."' />";
	$str.="<a href='#' class='wppizza-delete ".$field." button' title='".__('delete', $this->pluginLocale)."'> [X] </a>";
	$str.="</span>";
?>