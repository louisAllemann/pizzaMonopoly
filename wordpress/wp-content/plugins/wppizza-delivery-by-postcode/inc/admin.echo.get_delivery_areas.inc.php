<?php
		$deliveryType=!empty($options[$field][$k]['type']) ? $options[$field][$k]['type'] : $this->pluginOptions['order']['delivery_selected'];
		$optionsDecimals=$this->pluginOptions['layout']['hide_decimals'];		
		$str='';
		$str.="<span class='wppizza_option'>";
		$str.="".__('Label [post/zipcode]', $this->dbpLocale)." ";
		$value=!empty($options[$field][$k]['label']) ? $options[$field][$k]['label'] : '';
		$str.="<input id='wppizza_".$field."_".$k."' name='".$this->dbpSlug."[".$field."][".$k."][label]' size='20' class='wppizza-getkey' type='text' value='".$value."' /> ";//".$options[$field][$k]['item']."

		$str.="".__('Email', $this->dbpLocale)." ";
		$value=!empty($options[$field][$k]['email']) && is_array($options[$field][$k]['email']) ? implode(",",$options[$field][$k]['email']) : '';
		$str.="<input name='".$this->dbpSlug."[".$field."][".$k."][email]' size='20' type='text' value='".$value."' /> ";
		
		if($deliveryType!='no_delivery'){
			if($deliveryType=='minimum_total'){
				$str.="".__('Delivery Free above', $this->dbpLocale)." ";
			}else{
				$str.="".__('Delivery Charges', $this->dbpLocale)." ";	
			}
		
			$value=!empty($options[$field][$k]['charge']) ? wppizza_output_format_price($options[$field][$k]['charge'],$optionsDecimals) : '';
			$str.="<input name='".$this->dbpSlug."[".$field."][".$k."][charge]' size='3' type='text' value='".$value."' /> ";
		}
		/**minimum total / free delivery has an additional field regarding fixed delivery charges below minimum total*/
		if($deliveryType=='minimum_total'){
			$str.="".__('Fixed Delivery charges if free total not reached', $this->dbpLocale)." ";	
			$value=!empty($options[$field][$k]['charge_below_free']) ? wppizza_output_format_price($options[$field][$k]['charge_below_free'],$optionsDecimals) : wppizza_output_format_price(0,$optionsDecimals);
			$str.="<input name='".$this->dbpSlug."[".$field."][".$k."][charge_below_free]' size='3' type='text' value='".$value."' /> ";
		}
		
		/** minimum order value**/
		$str.="".__('minimum order', $this->dbpLocale)." ";
		$value=!empty($options[$field][$k]['min_order_value']) ? wppizza_output_format_price($options[$field][$k]['min_order_value'],$optionsDecimals) : wppizza_output_format_price(0,$optionsDecimals);	
		$str.="<input name='".$this->dbpSlug."[".$field."][".$k."][min_order_value]' size='3' type='text' value='".$value."' /> ";
		
		
		$str.="<input type='hidden' name='".$this->dbpSlug."[".$field."][".$k."][type]' value='".$deliveryType."' /> ";

		$str.="<span class='button'>".__('on/off ', $this->dbpLocale)."<input name='".$this->dbpSlug."[".$field."][".$k."][enabled]' title='".__('enabled', $this->dbpLocale)."' type='checkbox'  ". checked($options[$field][$k]['enabled'],true,false)." value='1' /></span>";
		$str.="<a href='#' class='wppizza-delete ".$field." button' title='".__('delete', $this->dbpLocale)."'> [X] </a>";

		$str.="</span>";
?>