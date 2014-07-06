<?php
	/**get previously saved options**/
	$options = $this->pluginOptionsNoWpml;

		/**lets not forget static, uneditable options **/
		$options['plugin_data']['version'] = $this->pluginVersion;
		$options['plugin_data']['nag_notice'] = isset($input['plugin_data']['nag_notice']) ? $input['plugin_data']['nag_notice'] : $options['plugin_data']['nag_notice'];
		$options['plugin_data']['empty_category_and_items'] = false;/*we dont really want to save these settings, just execute when true*/
		$options['plugin_data']['db_order_status_options'] = !empty($input['plugin_data']['db_order_status_options']) ? $input['plugin_data']['db_order_status_options'] : wppizza_order_status_default();

		if(isset($input['plugin_data']['empty_category_and_items']) && $input['plugin_data']['empty_category_and_items']==1){
			$this->wppizza_empty_taxonomy(!empty($input['plugin_data']['delete_attachments']) ? true : false, !empty($input['plugin_data']['truncate_orders']) ? true : false);
		}

		/**maybe in the future**/
//		if(isset($input['plugin_data']['install_sample_data'])){
			//include(WPPIZZA_PATH .'inc/admin.sample.data.inc.php');
			//$this->wppizza_empty_taxonomy(!empty($input['plugin_data']['delete_attachments']) ? true : false, !empty($input['plugin_data']['truncate_orders']) ? true : false);
//		}

		/**validate global settings***/
		if(isset($_POST[''.$this->pluginSlug.'_global'])){
			/**submitted options -> validate***/
			$options['plugin_data']['js_in_footer'] = !empty($input['plugin_data']['js_in_footer']) ? true : false;
			$options['plugin_data']['wp_multisite_session_per_site'] = !empty($input['plugin_data']['wp_multisite_session_per_site']) ? true : false;
			$options['plugin_data']['using_cache_plugin'] = !empty($input['plugin_data']['using_cache_plugin']) ? true : false;
			$options['plugin_data']['mail_type'] = wppizza_validate_alpha_only($input['plugin_data']['mail_type']);
		}

		/**sets custom menu as child of a parent page**/
		if(isset($input['plugin_data']['category_parent_page'])){
			$options['plugin_data']['category_parent_page'] = !empty($input['plugin_data']['category_parent_page']) ? (int)$input['plugin_data']['category_parent_page'] : '';
			//$options['plugin_data']['category_parent_page'] = wppizza_validate_alpha_only($input['plugin_data']['category_parent_page']);


		}
		if(isset($input['layout']['category_sort'])){
			$options['layout']['category_sort']=$input['layout']['category_sort'];
		}
		
		if(isset($input['layout']['category_sort_hierarchy'])){
			$options['layout']['category_sort_hierarchy']=$input['layout']['category_sort_hierarchy'];
		}		
		
		/*set number of items per loop. must be >= get_option('posts_per_page ')*/
		if(isset($input['layout']['items_per_loop'])){
			/*if minus=>set to -1**/
			if(substr($input['layout']['items_per_loop'],0,1)=='-'){
				$set='-1';
			}else{/*else mk int**/
				if((int)$input['layout']['items_per_loop']>=get_option('posts_per_page ')){
					$set=(int)$input['layout']['items_per_loop'];
				}else{
					$set=get_option('posts_per_page ');
				}
			}

			$options['layout']['items_per_loop']=$set;
		}


		if(isset($_POST[''.$this->pluginSlug.'_layout'])){
			$options['layout']['include_css'] = !empty($input['layout']['include_css']) ? true : false;
			$options['layout']['css_priority'] = wppizza_validate_int_only($input['layout']['css_priority']);
			$options['layout']['hide_decimals'] = !empty($input['layout']['hide_decimals']) ? true : false;
			$options['layout']['style'] = wppizza_validate_alpha_only($input['layout']['style']);
			$options['layout']['placeholder_img'] = !empty($input['layout']['placeholder_img']) ? true : false;
			$options['layout']['suppress_loop_headers'] = !empty($input['layout']['suppress_loop_headers']) ? true : false;
			$options['layout']['hide_cart_icon'] = !empty($input['layout']['hide_cart_icon']) ? true : false;
			$options['layout']['hide_item_currency_symbol'] = !empty($input['layout']['hide_item_currency_symbol']) ? true : false;
			$options['layout']['hide_single_pricetier'] = !empty($input['layout']['hide_single_pricetier']) ? true : false;
			$options['layout']['hide_prices'] = !empty($input['layout']['hide_prices']) ? true : false;
			$options['layout']['disable_online_order'] = !empty($input['layout']['disable_online_order']) ? true : false;
			$options['layout']['add_to_cart_on_title_click'] = !empty($input['layout']['add_to_cart_on_title_click']) ? true : false;
			$options['layout']['currency_symbol_left'] = !empty($input['layout']['currency_symbol_left']) ? true : false;
			$options['layout']['currency_symbol_position'] = preg_replace("/[^a-z]/","",$input['layout']['currency_symbol_position']);
			$options['layout']['show_currency_with_price'] = wppizza_validate_int_only($input['layout']['show_currency_with_price']);
			$options['layout']['cart_increase'] = !empty($input['layout']['cart_increase']) ? true : false;
			$options['layout']['prettyPhoto'] = !empty($input['layout']['prettyPhoto']) ? true : false;
			$options['layout']['prettyPhotoStyle']=wppizza_validate_string($input['layout']['prettyPhotoStyle']);
			$options['layout']['empty_cart_button'] = !empty($input['layout']['empty_cart_button']) ? true : false;
			$options['layout']['items_group_sort_print_by_category'] = !empty($input['layout']['items_group_sort_print_by_category']) ? true : false;
			$options['layout']['items_category_hierarchy'] = preg_replace("/[^a-z]/","",$input['layout']['items_category_hierarchy']);
			$options['layout']['items_category_hierarchy_cart'] = preg_replace("/[^a-z]/","",$input['layout']['items_category_hierarchy_cart']);
			$options['layout']['items_category_separator']=wppizza_validate_string($input['layout']['items_category_separator']);


			$options['layout']['sticky_cart_animation']=absint($input['layout']['sticky_cart_animation']);
			$options['layout']['sticky_cart_animation_style']=wppizza_validate_string($input['layout']['sticky_cart_animation_style']);
			$options['layout']['sticky_cart_margin_top']=absint($input['layout']['sticky_cart_margin_top']);
			$options['layout']['sticky_cart_background']=preg_replace("/[^a-zA-Z0-9#]/","",$input['layout']['sticky_cart_background']);
			$options['layout']['sticky_cart_limit_bottom_elm_id']=preg_replace("/[^a-zA-Z0-9_-]/","",$input['layout']['sticky_cart_limit_bottom_elm_id']);
			$options['layout']['jquery_fb_add_to_cart'] = !empty($input['layout']['jquery_fb_add_to_cart']) ? true : false;
			$options['layout']['jquery_fb_add_to_cart_ms']=absint($input['layout']['jquery_fb_add_to_cart_ms']);
			$options['opening_times_format']['hour']=wppizza_validate_string($input['opening_times_format']['hour']);
			$options['opening_times_format']['separator']=wppizza_validate_string($input['opening_times_format']['separator']);
			$options['opening_times_format']['minute']=wppizza_validate_string($input['opening_times_format']['minute']);
			$options['opening_times_format']['ampm']=wppizza_validate_string($input['opening_times_format']['ampm']);

		}
		/**validate opening_times settings***/
		if(isset($_POST[''.$this->pluginSlug.'_opening_times'])){
			$options['opening_times_standard'] = array();//initialize array
			ksort($input['opening_times_standard']);//just for consistency. not really necessary though
			foreach($input['opening_times_standard'] as $k=>$v){
				foreach($v as $l=>$m){
				$options['opening_times_standard'][$k][$l]=wppizza_validate_24hourtime($m);
				}
			}

			$options['opening_times_custom'] = array();//initialize array
			if(isset($input['opening_times_custom'])){
			foreach($input['opening_times_custom'] as $k=>$v){
				foreach($v as $l=>$m){
					if($k=='date'){
						$options['opening_times_custom'][$k][$l]=wppizza_validate_date($m,'Y-m-d');
					}else{
						$options['opening_times_custom'][$k][$l]=wppizza_validate_24hourtime($m);
					}
				}
			}}

			$options['times_closed_standard'] = array();//initialize array
			if(isset($input['times_closed_standard'])){
				foreach($input['times_closed_standard'] as $k=>$v){
					foreach($v as $l=>$m){
						if($k=='day'){
							$options['times_closed_standard'][$l][$k]=(int)$m;
						}else{
							$options['times_closed_standard'][$l][$k]=wppizza_validate_24hourtime($m);
						}
					}
				}
			}
		}
		/**validate order settings***/
		if(isset($_POST[''.$this->pluginSlug.'_order'])){
			$options['order'] = array();//initialize array
			$options['order']['currency'] = strtoupper($input['order']['currency']);//validation a bit overkill, but then again, why not
				$displayCurrency=wppizza_currencies($input['order']['currency'],true);
			$options['order']['currency_symbol'] = $displayCurrency['val'];
			$options['order']['orderpage'] = !empty($input['order']['orderpage']) ? (int)$input['order']['orderpage'] : false;
			$options['order']['orderpage_exclude']=!empty($input['order']['orderpage_exclude']) ? true : false;
			$options['order']['order_pickup']=!empty($input['order']['order_pickup']) ? true : false;
			$options['order']['order_pickup_alert']=!empty($input['order']['order_pickup_alert']) ? true : false;
			$options['order']['order_pickup_discount']=wppizza_validate_float_pc($input['order']['order_pickup_discount']);
			$options['order']['order_min_for_delivery']=wppizza_validate_float_only($input['order']['order_min_for_delivery']);

			$options['order']['order_pickup_display_location'] = wppizza_validate_int_only($input['order']['order_pickup_display_location']);

			$options['order']['delivery_selected'] = wppizza_validate_alpha_only($input['order']['delivery_selected']);
			$options['order']['discount_selected'] = wppizza_validate_alpha_only($input['order']['discount_selected']);

			$options['order']['delivery'] = array();
			foreach($input['order']['delivery'] as $k=>$v){
				foreach($v as $l=>$m){
					if($l!='deliver_below_total'){
						$options['order']['delivery'][$k][$l]=wppizza_validate_float_only($m,2);
					}
				}
				if($k=='minimum_total'){
					$options['order']['delivery'][$k]['deliver_below_total']=!empty($input['order']['delivery'][$k]['deliver_below_total']) ? true : false;
					$options['order']['delivery'][$k]['deliverycharges_below_total']=wppizza_validate_float_only($input['order']['delivery'][$k]['deliverycharges_below_total']);
				}
			}
			/**hardcode no_delivery (as there are  no submitted input values)*/
					$options['order']['delivery']['no_delivery']='';


			$options['order']['discounts'] = array();//initialize array
			$options['order']['discounts']['none'] = array();//add distinctly as it has no array associated with it
			foreach($input['order']['discounts'] as $a=>$b){
				foreach($b as $c=>$d){
					foreach($d as $e=>$f){
						foreach($f as $g=>$h){
							if($a=='percentage' && $g=='discount'){
								$options['order']['discounts'][$a][$c][$e][$g]=wppizza_validate_float_pc($h);
							}else{
								$options['order']['discounts'][$a][$c][$e][$g]=wppizza_validate_float_only($h,2);
							}
						}
					}
				}
			}

			$options['order']['delivery_calculation_exclude_item'] = !empty($input['order']['delivery_calculation_exclude_item']) ? $input['order']['delivery_calculation_exclude_item'] : array();
			$options['order']['item_tax']=wppizza_validate_float_pc($input['order']['item_tax'],5);//5 decimals should really be enough i would have thought
			$options['order']['taxes_included'] = !empty($input['order']['taxes_included']) ? true : false;
			$options['order']['shipping_tax'] = !empty($input['order']['shipping_tax']) ? true : false;
			$options['order']['append_internal_id_to_transaction_id'] = !empty($input['order']['append_internal_id_to_transaction_id']) ? true : false;


			$options['order']['order_email_to'] = wppizza_validate_email_array($input['order']['order_email_to']);
			$options['order']['order_email_bcc'] = wppizza_validate_email_array($input['order']['order_email_bcc']);
			$options['order']['order_email_attachments'] = wppizza_strtoarray($input['order']['order_email_attachments']);
			$emailFrom=wppizza_validate_email_array($input['order']['order_email_from']);/*validated as array but we only store the first value as string*/
			$options['order']['order_email_from'] = !empty($emailFrom[0]) ? ''.$emailFrom[0].'' : '' ;
			$options['order']['order_email_from_name'] = wppizza_validate_string($input['order']['order_email_from_name']);
		}
		/**validate order form***/
		if(isset($_POST[''.$this->pluginSlug.'_order_form'])){
			foreach($input['order_form'] as $a=>$b){
				$options['order_form'][$a]['sort'] = (int)($input['order_form'][$a]['sort']);
				$options['order_form'][$a]['key'] = $options['order_form'][$a]['key'];
				$options['order_form'][$a]['lbl'] = wppizza_validate_string($input['order_form'][$a]['lbl']);
				$options['order_form'][$a]['type'] = wppizza_validate_letters_only($input['order_form'][$a]['type']);
				$options['order_form'][$a]['enabled'] = !empty($input['order_form'][$a]['enabled']) ? true : false;
				$options['order_form'][$a]['required'] = !empty($input['order_form'][$a]['required']) ? true : false;
				$options['order_form'][$a]['required_on_pickup'] = !empty($input['order_form'][$a]['required_on_pickup']) ? true : false;
				$options['order_form'][$a]['prefill'] = !empty($input['order_form'][$a]['prefill']) ? true : false;
				$options['order_form'][$a]['onregister'] = !empty($input['order_form'][$a]['onregister']) ? true : false;
				$options['order_form'][$a]['value'] = wppizza_strtoarray($input['order_form'][$a]['value']);
			}
		
			
			$options['confirmation_form_enabled'] = !empty($input['confirmation_form_enabled']) ? true : false;
			if(isset($input['confirmation_form']) && is_array($input['confirmation_form'])){
			$options['confirmation_form_amend_order_link'] = (int)$input['confirmation_form_amend_order_link'];
			foreach($input['confirmation_form'] as $a=>$b){
				$options['confirmation_form'][$a]['sort'] = (int)($input['confirmation_form'][$a]['sort']);
				$options['confirmation_form'][$a]['key'] = $options['confirmation_form'][$a]['key'];
				$options['confirmation_form'][$a]['lbl'] = wppizza_validate_string($input['confirmation_form'][$a]['lbl'],true);
				$options['confirmation_form'][$a]['type'] = wppizza_validate_letters_only($input['confirmation_form'][$a]['type']);
				$options['confirmation_form'][$a]['enabled'] = !empty($input['confirmation_form'][$a]['enabled']) ? true : false;
				$options['confirmation_form'][$a]['required'] = !empty($input['confirmation_form'][$a]['required']) ? true : false;			
			}}else{			
				$input['confirmation_form']=array();
				$options['confirmation_form_amend_order_link'] = '';
			}
			
			
			if(isset($input['confirmation_form']) && is_array($input['confirmation_form'])){
				if(isset($input['localization_confirmation_form'])){
				//$allowHtml=array('thank_you_p','jquery_fb_add_to_cart_info');/*array of items to allow html (such as tinymce textareas) */
				foreach($input['localization_confirmation_form'] as $a=>$b){
					/*add new value , but keep desciption (as its not editable on frontend)*/
					$html=false;
					//if(in_array($a,$allowHtml)){$html=1;}
					$options['localization_confirmation_form'][$a]=array('lbl'=>wppizza_validate_string($b,$html));
				}}			
			}		
		}

		/**validate sizes settings***/
		if(isset($_POST[''.$this->pluginSlug.'_sizes'])){
			$options['sizes'] = array();//initialize array
			if(isset($input['sizes'])){

			foreach($input['sizes'] as $a=>$b){
				$i=0;
				foreach($b as $c=>$d){
					if($i==0){
					$options['sizes'][$a][$c]['lbladmin']=wppizza_validate_string($d['lbladmin']);
					}
					$options['sizes'][$a][$c]['lbl']=wppizza_validate_string($d['lbl']);
					$options['sizes'][$a][$c]['price']=wppizza_validate_float_only($d['price'],2);
				$i++;
				}


			}}
		}
		/**validate additives ***/
		if(isset($_POST[''.$this->pluginSlug.'_additives'])){
			$options['additives'] = array();//initialize array
			if(isset($input['additives'])){
			foreach($input['additives'] as $a=>$b){
				if(trim($b['name'])!=''){
					$sort= ($b['sort']!='') ? wppizza_validate_int_only($b['sort']) : '';
					$options['additives'][$a]=array('sort'=>$sort,'name'=>wppizza_validate_string($b['name']));
				}
			}}
		}
	
		/**validate localization ***/
		if(isset($_POST[''.$this->pluginSlug.'_localization'])){
			if(isset($input['localization'])){
			$allowHtml=array('thank_you_p','jquery_fb_add_to_cart_info','register_option_create_account_info','register_option_create_account_error');/*array of items to allow html (such as tinymce textareas) */
			foreach($input['localization'] as $a=>$b){
				/*add new value , but keep desciption (as its not editable on frontend)*/
				if(in_array($a,$allowHtml)){$html=1;}else{$html=false;}
				$options['localization'][$a]=array('lbl'=>wppizza_validate_string($b,$html));
			}}
		}

		/*******************************
		*
		*	[access level]
		*
		*******************************/
		if(isset($_POST['wppizza_access'])){
			$access=$this->wppizza_set_capabilities();
			//$roles=get_editable_roles();/*only get roles user is allowed to edit**/
			foreach($input['admin_access_caps'] as $roleName=>$v){
				$userRole = get_role($roleName);

				foreach($access as $akey=>$aVal){
					/**not checked, but previously selected->remove capability**/
					if(isset($userRole->capabilities[$aVal['cap']]) && ( !is_array($input['admin_access_caps'][$roleName]) || !isset($input['admin_access_caps'][$roleName][$aVal['cap']]))){
						$userRole->remove_cap( ''.$aVal['cap'].'' );
					}
					/**checked and NOT previously selected->add capability*/
					if(is_array($input['admin_access_caps'][$roleName]) && isset($input['admin_access_caps'][$roleName][$aVal['cap']]) && !isset($userRole->capabilities[$aVal['cap']])){
						$userRole->add_cap( ''.$aVal['cap'].'' );
					}
				}
			}
		}

		/**update gateways**/
		if(isset($_POST[''.$this->pluginSlug.'_gateways'])){

			$options['gateways']['gateway_select_as_dropdown']=!empty($input['gateways']['gateway_select_as_dropdown'])? true : false;
			$options['gateways']['gateway_showorder_on_thankyou']=!empty($input['gateways']['gateway_showorder_on_thankyou'])? true : false;
			$options['gateways']['gateway_select_label']=wppizza_validate_string($input['gateways']['gateway_select_label']);

			/**sort selected gateway*/
			asort($input['gateways']['gateway_order']);

			$options['gateways']['gateway_selected']=array();
			foreach($input['gateways']['gateway_order'] as $gw=>$sort){
				$options['gateways']['gateway_selected'][$gw]=!empty($input['gateways']['gateway_selected'][$gw])? true : false;
			}

			/**selected gateway*/
			$gateways=$this->wppizza_get_registered_gateways();

			foreach($gateways as $k=>$v){
				$updateGatewayOptions=array();
				foreach($v['gatewaySettings'] as $l=>$m){
					/*validate value according to callback*/
					if(isset($input['gateways'][$v['gatewayOptionsName']][$m['key']])){
						if($m['validateCallback']!=''){
							if(is_array($m['validateCallback'])){
								$val=$m['validateCallback'][0]($input['gateways'][$v['gatewayOptionsName']][$m['key']],$m['validateCallback'][1]);
							}else{
								$val=$m['validateCallback']($input['gateways'][$v['gatewayOptionsName']][$m['key']]);
							}
						}else{
							/*no callback defined*/
							$val=$input['gateways'][$v['gatewayOptionsName']][$m['key']];
						}
					}else{
						$val='';
					}
					$updateGatewayOptions[$m['key']]=$val;
				}

				/****add label and info*****/
					$lbl=wppizza_validate_string($input['gateways'][$v['gatewayOptionsName']]['gateway_label']);
				$updateGatewayOptions['gateway_label']=!empty($lbl) ? $lbl : $v['gatewayName'];
				$updateGatewayOptions['gateway_info']=wppizza_validate_string($input['gateways'][$v['gatewayOptionsName']]['gateway_info']);

				/****add any non-user-editable gateway specific options (version numbers for example)*****/
				if(isset($v['gatewaySettingsNonEditable']) && is_array($v['gatewaySettingsNonEditable'])){
					foreach($v['gatewaySettingsNonEditable'] as $neKey=>$neVal){
						$updateGatewayOptions[$neKey]=$neVal;
					}
				}
				update_option($v['gatewayOptionsName'],$updateGatewayOptions);
			}
		}
?>