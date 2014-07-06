<?php
	/*no need to run this when no wpml or on wppizza first install*/
	if(function_exists('icl_translate') && $this->pluginOptions!=0) {
		/**localization**/
		foreach($this->pluginOptions['localization'] as $k=>$arr){
			$this->pluginOptions['localization'][$k]['lbl'] = icl_translate(WPPIZZA_SLUG,''. $k.'', $arr['lbl']);
		}
		/**additives**/
		foreach($this->pluginOptions['additives'] as $k=>$str){
			$this->pluginOptions['additives'][$k] = icl_translate(WPPIZZA_SLUG,'additives_'. $k.'', $str);
		}
		/**sizes**/
		foreach($this->pluginOptions['sizes'] as $k=>$arr){
			foreach($arr as $sKey=>$sArr){
				$this->pluginOptions['sizes'][$k][$sKey]['lbl'] = icl_translate(WPPIZZA_SLUG,'sizes_'. $k.'_'.$sKey.'', $sArr['lbl']);
			}
		}		
		/**order_form**/
		foreach($this->pluginOptions['order_form'] as $k=>$arr){
			$this->pluginOptions['order_form'][$k]['lbl'] = icl_translate(WPPIZZA_SLUG,'order_form_'. $k.'', $arr['lbl']);
		}
		/**confirmation_form**/
		foreach($this->pluginOptions['confirmation_form'] as $k=>$arr){
			$this->pluginOptions['confirmation_form'][$k]['lbl'] = icl_translate(WPPIZZA_SLUG,'confirmation_form_'. $k.'', $arr['lbl']);
		}			
		/**order**/
		$this->pluginOptions['order']['order_email_from'] = icl_translate(WPPIZZA_SLUG,'order_email_from', $this->pluginOptions['order']['order_email_from']);
		$this->pluginOptions['order']['order_email_from_name'] = icl_translate(WPPIZZA_SLUG,'order_email_from_name', $this->pluginOptions['order']['order_email_from_name']);
		/**order email to **/
		foreach($this->pluginOptions['order']['order_email_to'] as $k=>$arr){
			$this->pluginOptions['order']['order_email_to'][$k] = icl_translate(WPPIZZA_SLUG,'order_email_to_'.$k.'', $arr);
		}
		/**order email bcc **/
		foreach($this->pluginOptions['order']['order_email_bcc'] as $k=>$arr){
			$this->pluginOptions['order']['order_email_bcc'][$k] = icl_translate(WPPIZZA_SLUG,'order_email_bcc_'. $k.'', $arr);
		}
		/**order email attachments **/
		if(isset($this->pluginOptions['order']['order_email_attachments']) && is_array($this->pluginOptions['order']['order_email_attachments'])){
		foreach($this->pluginOptions['order']['order_email_attachments'] as $k=>$arr){
			$this->pluginOptions['order']['order_email_attachments'][$k] = icl_translate(WPPIZZA_SLUG,'order_email_attachments_'. $k.'', $arr);
		}}		
	
		/**gateways select label**/
		$this->pluginOptions['gateways']['gateway_select_label'] = icl_translate(WPPIZZA_SLUG,'gateway_select_label', $this->pluginOptions['gateways']['gateway_select_label']);
		/*per gateway*/
		if(isset($this->pluginGateways)){
			foreach($this->pluginGateways as $key=>$regGw){
				if(isset($regGw->gatewayOptions) && is_array($regGw->gatewayOptions)){
					foreach($regGw->gatewayOptions as $g=>$gwOption){
						if(is_string($gwOption) && $gwOption!=''){
							$regGwSet =__($gwOption, $this->pluginLocale);
							$regGw->gatewayOptions[$g] = icl_translate(WPPIZZA_SLUG.'_gateways',''.strtolower($key).'_'. $g.'', $regGwSet);
						}
					}
				}
			}
		}	
	
	
	
	}
?>