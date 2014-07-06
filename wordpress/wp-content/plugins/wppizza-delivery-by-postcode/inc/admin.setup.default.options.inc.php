<?php
	/**get current set options**/
	if(isset($pluginUpdate)){
		$setOptions=$this->dbpOptions;
	}
	/**always overwrite with current version**/
	$defaultOptions['plugin_data']['version'] = $this->dbpVersion;
	$defaultOptions['plugin_data']['nag_notice'] = $this->dbpNagNotice;
	$defaultOptions['plugin_data']['license'] =  !empty($this->dbpOptions['plugin_data']['license']) ? $this->dbpOptions['plugin_data']['license'] : array('key'=>'','status'=>'','error'=>'');
	
	

	/****************************************
		[frontend options]
	****************************************/
	$defaultOptions['frontend_settings']=array(
		'enabled'=> true,
		'required'=> true,
		'no_required_onpickup'=> false,
		'show_on_load'=> false,
		'orderform_priority'=>'',
		'show_on_load_global'=> false,
		'dont_show_on_load_if_closed'=> true,
		'instant_search'=> false
	);
	/*update missing**/
	if(isset($pluginUpdate)){
		foreach($defaultOptions['frontend_settings'] as $k=>$v){
			if(isset($setOptions['frontend_settings'][$k])){
				$defaultOptions['frontend_settings'][$k]=$setOptions['frontend_settings'][$k];
			}
		}
	}

	/****************************************
		[delivery areas]
	****************************************/
	if(!isset($pluginUpdate)){
		$defaultOptions['delivery_areas']= array();
	}else{
		$defaultOptions['delivery_areas']= $setOptions['delivery_areas'];
	}
	
	
	/********************************************************
	*
	*	[set admin access to plugin pages/tabs]
	*
	********************************************************/
	/***user_caps_ini->returns array of set roles and caps, applies default caps on first install and returns current on updates***/
	$userCaps=new WPPIZZA_USER_CAPS();
	/**first install ? */
	$checkAccessCaps = isset($this->dbpOptions['admin_access_caps']) ? $this->dbpOptions['admin_access_caps'] : array();
	$defaultOptions['admin_access_caps']=$userCaps->user_caps_ini($this->wppizza_dbp_caps(),$checkAccessCaps);	
	
	
	/****************************************
		[localization]
	****************************************/
	$defaultOptions['localization']= array();
	$defaultOptions['localization']['label']=array('descr'=>__('Dropdown: Label above Post/Zip-Code Selection', $this->dbpLocale),'lbl'=>__('Select your post/zipcode :', $this->dbpLocale));
	$defaultOptions['localization']['select']=array('descr'=>__('Dropdown: Label when no selection has yet been made', $this->dbpLocale),'lbl'=>__('--please select--', $this->dbpLocale));
	$defaultOptions['localization']['label_instant']=array('descr'=>__('Textbox: Label if you are using a textbox instead of dropdown', $this->dbpLocale),'lbl'=>__('Start typing your post/zipcode:', $this->dbpLocale));
	$defaultOptions['localization']['required_error']=array('descr'=>__('Alert when potst/zip-code selection has not yet been made when trying to order [only relevant when set to "required" and "enabled"]', $this->dbpLocale),'lbl'=>__('Please select your post/zipcode !', $this->dbpLocale));
	$defaultOptions['localization']['instant_search_placeholder']=array('descr'=>__('Textbox: Placeholder when using textbox instead of dropdown', $this->dbpLocale),'lbl'=>__('Type your post/zipcode :', $this->dbpLocale));
	$defaultOptions['localization']['noresults_instant']=array('descr'=>__('Textbox: Text to display when no results have been found', $this->dbpLocale),'lbl'=>__('Sorry! No results found', $this->dbpLocale));
	$defaultOptions['localization']['dbp_generic']=array('descr'=>__('Label in Order and Email', $this->dbpLocale),'lbl'=>__('Post/Zipcode :', $this->dbpLocale));
	
	/*if we are updating, we only want to insert new values if they are not already in the optiontable**/
	if(isset($pluginUpdate)){
		foreach($defaultOptions['localization'] as $k=>$v){
			if(isset($setOptions['localization'][$k])){
				$defaultOptions['localization'][$k]=$setOptions['localization'][$k];
			}
		}
	}
?>