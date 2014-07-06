<?php
	if(!isset($k)){
		$val=array('day'=>'0','close_start'=>'12:00','close_end'=>'13:00');
	}else{
		$val=array('day'=>$options['day'],'close_start'=>$options['close_start'],'close_end'=>$options['close_end']);
	}
	$str='';
	$str.="<span class='wppizza_option'>";
	
	$str.="<select name='".$this->pluginSlug."[".$field."][day][]'>";
		if($k=='-1'){$sel=" selected='selected'";}else{$sel="";}
		$str.="<option value='-1'".$sel.">--".__('Custom Dates Above (if any)', $this->pluginLocale)."--</option>";	
	foreach(wppizza_days() as $k=>$v){
		if($k==$val['day']){$sel=" selected='selected'";}else{$sel="";}
		$str.="<option value='".$k."'".$sel.">".$v."</option>";	
	}
	$str.="</select>";
	$str.="".__('closed from', $this->pluginLocale).":";
	$str.="<input name='".$this->pluginSlug."[".$field."][close_start][]' size='2' type='text' class='wppizza-time-select' value='".$val['close_start']."' />";
	$str.="".__('to', $this->pluginLocale).":";
	$str.="<input name='".$this->pluginSlug."[".$field."][close_end][]' size='2' type='text' class='wppizza-time-select' value='".$val['close_end']."' />";
	$str.="<a href='#' class='wppizza-delete ".$field." button' title='".__('delete', $this->pluginLocale)."'> [X] </a>";
	$str.="</span>";
?>