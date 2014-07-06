<?php
	/****************************************************
	*
	*	[insert default options into options table]
	*
	*****************************************************/
		$defaultOptions = array(
			'plugin_data'=>array(
				'version' => $this->wpptmVersion,
				'license' =>  !empty($this->wpptmOptions['plugin_data']['license']) ? $this->wpptmOptions['plugin_data']['license'] : array(
					'key'=>'',
					'status'=>'',
					'error'=>''
				),
				'nag_notice' => $this->wpptmNotice
			),
			'options'=>array(
				'display_as_unavailable'=>false
			),
			'timed_items'=>!empty($this->wpptmOptions['timed_items']) ? $this->wpptmOptions['timed_items'] : array()
		);

	/********************************************************
	*
	*	[set admin access to plugin pages/tabs]
	*
	********************************************************/
		/***user_caps_ini->returns array of set roles and caps, applies default caps on first install and returns current on updates***/
		$userCaps=new WPPIZZA_USER_CAPS();
		$defaultOptions['admin_access_caps']=$userCaps->user_caps_ini($this->wppizza_tm_caps(),$this->wpptmOptions['admin_access_caps']);
	/********************************************************
	*
	*	[set localization]
	*
	********************************************************/
		$localizationOptions=array(
				'currently_not_available'=>array(
					'descr'=>__('text to display on page when NO item in a category is currently available and wppizza->timed menu->options->"Display Menu Items as unavailable ?" is NOT enabled (html allowed)', $this->wpptmLocale),
					'lbl'=>__('<div style=\'text-align:center\'>Sorry, this item is currently not available</div>', $this->wpptmLocale),
					'type'=>'textarea'
				),
				'item_na'=>array(
					'descr'=>__('text on single item when it is not available and option "Display as unavailable" has been selected', $this->wpptmLocale),
					'lbl'=>__('currently not available', $this->wpptmLocale),
					'type'=>'text'
				)
		);
		/*as we only want to store the lables so we can edit the description in the future maybe loop over it**/
		$defaultOptions['localization']=array();
		foreach($localizationOptions as $lkey=>$lArr){
			$defaultOptions['localization'][$lkey]=$lArr['lbl'];
		}
?>