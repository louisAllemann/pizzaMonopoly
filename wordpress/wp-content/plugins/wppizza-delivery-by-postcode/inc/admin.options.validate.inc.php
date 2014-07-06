<?php
/***************************************************************************************************************

		[validate dbp settings]

**************************************************************************************************************/
	if(isset($_POST[''.$this->dbpSlug.''])){
		$newOptions = $this->dbpOptions;


		/*************************************************
		*
		*	[plugin data- version and nag->always update]
		*
		*************************************************/
		$newOptions['plugin_data']['version'] = $this->dbpVersion;
		$newOptions['plugin_data']['nag_notice'] = isset($input['plugin_data']['nag_notice']) ? $input['plugin_data']['nag_notice'] : $this->dbpOptions['plugin_data']['nag_notice'];


		/*******************************
		*
		*	[edd settings]
		*
		*******************************/
		if(isset($input['license'])){
			if(defined('WPPIZZA_DBP_EDD_NAME')){
				$currentLicense=$newOptions['plugin_data']['license'];/**array of key,status,error**/
				$newKey=wppizza_validate_string($input['license']['key']);
				$action	=!empty($input['license']['action']) ? $input['license']['action'] : '';
				/**de/re/activate/update***/
				$newOptions['plugin_data']['license']=$this->wppdbpEdd->edd_toggle($currentLicense,$newKey,$action,WPPIZZA_DBP_EDD_NAME,WPPIZZA_DBP_EDD_URL);
			}
		}

		/*******************************
		*
		*	[access settings]
		*
		*******************************/
		if(isset($input['admin_access_caps'])){
			$newOptions['admin_access_caps']=$this->wppdbpUserCaps->user_validate_admin_caps($this->wppizza_dbp_caps(),$this->dbpOptions['admin_access_caps'],$input['admin_access_caps']);
		}

		/*******************************
		*
		*	[frontend settings]
		*
		*******************************/
		if(isset($input['frontend_settings'])){
			$newOptions['frontend_settings']['enabled']=!empty($input['frontend_settings']['enabled']) ? true : false;
			$newOptions['frontend_settings']['required']=!empty($input['frontend_settings']['required']) ? true : false;
			$newOptions['frontend_settings']['no_required_onpickup']=!empty($input['frontend_settings']['no_required_onpickup']) ? true : false;
			$newOptions['frontend_settings']['show_on_load']=!empty($input['frontend_settings']['show_on_load']) ? true : false;
			$newOptions['frontend_settings']['orderform_priority']=wppizza_validate_string($input['frontend_settings']['orderform_priority']);
			$newOptions['frontend_settings']['show_on_load_global']=!empty($input['frontend_settings']['show_on_load_global']) ? true : false;
			$newOptions['frontend_settings']['dont_show_on_load_if_closed']=!empty($input['frontend_settings']['dont_show_on_load_if_closed']) ? true : false;
			$newOptions['frontend_settings']['instant_search']=!empty($input['frontend_settings']['instant_search']) ? true : false;
		}
		/*******************************
		*
		*	[delivery_areas]
		*
		*******************************/
		if(isset($input['delivery_areas_ctrl'])){
		$newOptions['delivery_areas'] = array();//initialize array
		$uniqueLbl=array();
		foreach($input['delivery_areas'] as $k=>$v){
			$lbl=wppizza_validate_string($v['label']);
			$type=wppizza_validate_string($v['type']);
			/*make sure we only have one email address to send to */
			$email=wppizza_validate_email_array($v['email']);
			if(isset($email[0])){$email=$email;}else{$email=array();}
			if(!isset($uniqueLbl[$type]) || !in_array($lbl,$uniqueLbl[$type])){/*make sure its unique for this type*/
				$uniqueLbl[$type][]=$lbl;
				$newOptions['delivery_areas'][$k]['type']=$type;				
				$newOptions['delivery_areas'][$k]['label']=$lbl;
				$newOptions['delivery_areas'][$k]['enabled'] = !empty($v['enabled']) ? true : false;				
				$newOptions['delivery_areas'][$k]['email']=$email;
				$newOptions['delivery_areas'][$k]['charge']=!empty($v['charge']) ? wppizza_validate_float_only($v['charge']) : 0;
				$newOptions['delivery_areas'][$k]['charge_below_free']=!empty($v['charge_below_free']) ? wppizza_validate_float_only($v['charge_below_free']) : 0;
				$newOptions['delivery_areas'][$k]['min_order_value']=!empty($v['min_order_value']) ? wppizza_validate_float_only($v['min_order_value']) : 0;
				//$newOptions['delivery_areas'][$k]['charge_type']=wppizza_validate_string($v['charge_type']);
			}
		}}

		/****************************
		*
		*	[localization]
		*
		****************************/
		if(isset($input['localization'])){
		$newOptions['localization'] = array();
		foreach($input['localization'] as $a=>$b){
			/*add new value , but keep desciption (as its not editable on frontend)*/
			$newOptions['localization'][$a]=array('lbl'=>wppizza_validate_string($b));
		}}

	}
?>