<?php
/***************************************************************************************************************

		[validate ingredients settings]

**************************************************************************************************************/
	if(isset($_POST[''.$this->pluginSlug.'_ingredients'])){
		$newOptions = $this->pluginOptionsNoWpml;

		/*************************************************
		*
		*	[plugin data- version and nag->always update]
		*
		*************************************************/
		$newOptions['plugin_data']['version'] = $this->pluginVersion;
		$newOptions['plugin_data']['nag_notice'] = isset($input['plugin_data']['nag_notice']) ? $input['plugin_data']['nag_notice'] : $this->pluginOptionsNoWpml['plugin_data']['nag_notice'];

		/*******************************
		*
		*	[ingredients]
		*
		*******************************/
		if(isset($input['ingredients'])){
			$tosortOptions['ingredients'] = array();//initialize array
			foreach($input['ingredients'] as $a=>$b){
				foreach($b as $c=>$d){
					if($c=='item'){
						$tosortOptions['ingredients'][$a][$c]=wppizza_validate_string($d);
					}
					if($c=='sizes'){
						$tosortOptions['ingredients'][$a][$c]=wppizza_validate_int_only($d);
					}
					if($c=='prices'){
						foreach($d as $e=>$f){
							/**allow negative prices**/
							$neg='';if(substr($f,0,1)=='-'){$neg='-';}
							$tosortOptions['ingredients'][$a][$c][$e]=$neg.wppizza_validate_float_only($f,2);
						}
					}
				}
				$tosortOptions['ingredients'][$a]['enabled'] = !empty($input['ingredients'][$a]['enabled']) ? true : false;
			}
			/*set order for ksort*/
			$newOptions['ingredients'] = array();//initialize array
			foreach($tosortOptions['ingredients'] as $k=>$v){
				$newOptions['ingredients'][$k]['sizes']=$v['sizes'];
				$newOptions['ingredients'][$k]['item']=$v['item'];
				$newOptions['ingredients'][$k]['prices']=$v['prices'];
				$newOptions['ingredients'][$k]['enabled']=$v['enabled'];
			}

		}

		/*******************************
		*
		*	[access level]
		*
		*******************************/
		if(isset($input['admin_access_caps'])){
			$access=$this->wppizza_ingredients_capabilities_tabs();
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

		/************************************
		*
		*	[options]
		*
		********************************************/
		if(isset($input['options'])){
			$newOptions['options']['ingredients_in_popup'] =  !empty($input['options']['ingredients_in_popup']) ? true : false;
			$newOptions['options']['ingredients_in_popup_wpc'] = (int)$input['options']['ingredients_in_popup_wpc']>0 ? (int)$input['options']['ingredients_in_popup_wpc'] : 100;
			$newOptions['options']['ingredients_in_popup_anim'] = (int)$input['options']['ingredients_in_popup_anim'];
			$newOptions['options']['ingredients_added_sticky'] =  !empty($input['options']['ingredients_added_sticky']) ? true : false;
			$newOptions['options']['ingredients_addasis_button_enabled'] =  !empty($input['options']['ingredients_addasis_button_enabled']) ? true : false;
			$newOptions['options']['ingredients_show_count'] =  !empty($input['options']['ingredients_show_count']) ? true : false;
			$newOptions['options']['ingredients_omit_single_count'] =  !empty($input['options']['ingredients_omit_single_count']) ? true : false;
			$newOptions['options']['ingredients_added_sort_by_price'] =  !empty($input['options']['ingredients_added_sort_by_price']) ? true : false;
			$newOptions['options']['ingredients_added_show_price'] =  !empty($input['options']['ingredients_added_show_price']) ? true : false;
			$newOptions['options']['ingredients_added_show_price_no_zero'] =  !empty($input['options']['ingredients_added_show_price_no_zero']) ? true : false;
			$newOptions['options']['ingredients_added_zero_price_txt'] = wppizza_validate_string($input['options']['ingredients_added_zero_price_txt']);
			$newOptions['options']['ingredients_show_depreselected'] =  !empty($input['options']['ingredients_show_depreselected']) ? true : false;
			$newOptions['options']['ingredients_show_depreselected_after'] =  !empty($input['options']['ingredients_show_depreselected_after']) ? true : false;
			$newOptions['options']['ingredients_show_depreselected_prefix'] = wppizza_validate_string($input['options']['ingredients_show_depreselected_prefix']);
		}
		/************************************
		*
		*	[license]
		*
		********************************************/
		if(isset($input['license'])){
			$licenseKey=wppizza_validate_string($input['license']['key']);
			$newOptions['license']['key'] = trim($licenseKey);

			/**default response**/
			$response=array('action'=>false,'value'=>'');
			/**are we activating or de-activating**/
			if(isset($input['license']['activate'])){
				$licenseToggle=$this->wppizza_addingredients_activate_license($licenseKey);
				$response['action']='activate';
				$response['value']=$licenseToggle;
			}
			if(isset($input['license']['deactivate'])){
				$licenseToggle=$this->wppizza_addingredients_deactivate_license($licenseKey);
				$response['action']='deactivate';
				$response['value']=$licenseToggle;
			}
			/***catch response (as this might be connection error***/
			$newOptions['license']['response'] =  $response;

			/**only change thinsg if we are actually changing things and we had no connection errors**/
			if(isset($licenseToggle) && $licenseToggle!='connection-error'){
				$newOptions['license']['enabled'] =  $licenseToggle;
			}



//			$newOptions['license']['enabled'] =  !empty($licenseToggle) ? $licenseToggle : $this->wppizza_addingredients_deactivate_license($licenseKey);
			$licenseCheck =  !empty($input['license']['check']) ? $this->wppizza_addingredients_check_license($licenseKey) : false;
//			print_r($licenseCheck);
//			exit();

		}
		/************************************
		*
		*	[ingredients custom groups]
		*	[custom groups settings]
		*
		********************************************/
		if(isset($input['settings'])){
			$newOptions['settings']['price_show_if_zero'] =  !empty($input['settings']['price_show_if_zero']) ? true : false;
			$newOptions['settings']['price_localize_if_zero'] = wppizza_validate_string($input['settings']['price_localize_if_zero']);

			$newOptions['ingredients_custom_groups'] = array();//initialize array
			if(isset($input['ingredients_custom_groups']) && is_array($input['ingredients_custom_groups'])){
				foreach($input['ingredients_custom_groups'] as $k=>$v){
					/*only insert if  a group size/pricetier has been selected*/
					if($v['sizes']!=''){
						/**save in sort order so we can use a simple asort when displaying*/
						$newOptions['ingredients_custom_groups'][$k]['sizes']=wppizza_validate_alpha_only($v['sizes']);
						/**set exclude and preselect to post position to disply last*/
						if($v['type']>=5){
							$newOptions['ingredients_custom_groups'][$k]['position']=true;
						}else{
							$newOptions['ingredients_custom_groups'][$k]['position']=!empty($v['position']) ? true : false;
						}

						/**set exclude and preselect to 9999 to display last*/
						if($v['type']>=5){
							$newOptions['ingredients_custom_groups'][$k]['sort']=9999;
						}else{
							$newOptions['ingredients_custom_groups'][$k]['sort']=wppizza_validate_int_only($v['sort']);
						}

						$newOptions['ingredients_custom_groups'][$k]['label']=wppizza_validate_string($v['label']);
						$newOptions['ingredients_custom_groups'][$k]['info']=wppizza_validate_string($v['info']);
						$newOptions['ingredients_custom_groups'][$k]['type']=wppizza_validate_int_only($v['type']);

						/**group only applies to whole item (i.e taken out of multi toppings loop. not apploicable to types>=5*/
						if($v['type']>=5){
							$newOptions['ingredients_custom_groups'][$k]['whole_only']=false;
						}else{
							$newOptions['ingredients_custom_groups'][$k]['whole_only']=!empty($v['whole_only']) ? true : false;
						}

						/***preselect price is zero***/
						if($v['type']!=6){
								$newOptions['ingredients_custom_groups'][$k]['preselpricezero']=false;
						}else{
								$newOptions['ingredients_custom_groups'][$k]['preselpricezero']=!empty($v['preselpricezero']) ? true : false;
						}



						/*maximum number of *different* ingredients->only relevant when set to type 3 or 4**/
						if($newOptions['ingredients_custom_groups'][$k]['type']==3 || $newOptions['ingredients_custom_groups'][$k]['type']==4){
							$newOptions['ingredients_custom_groups'][$k]['max_ing']=wppizza_validate_int_only($v['max_ing']);
							/*min number of different ingredients->minimum=1, maximum: if max_ing>0: no more than max_ing , if max_ing==0 at least 1, but can be more*/
							$selMinIng=wppizza_validate_int_only($v['min_ing']);
							if($selMinIng==0){
								$selMinIng=0;/*set to a minimum of 1 if set to 0*/
								$newOptions['ingredients_custom_groups'][$k]['min_ing']=0;
							}
							/*if max not set (0), set min to whatever has been set (automatically >=1)*/
							if($newOptions['ingredients_custom_groups'][$k]['max_ing']==0 && $selMinIng>=1){
								$newOptions['ingredients_custom_groups'][$k]['min_ing']=$selMinIng;
							}

							/*if max set (>0), set min_ing to whatever has been set (will always be at least >=1) but at a maximum to max_ing*/
							if($newOptions['ingredients_custom_groups'][$k]['max_ing']>0){
								if($selMinIng > $newOptions['ingredients_custom_groups'][$k]['max_ing']){
									$newOptions['ingredients_custom_groups'][$k]['min_ing']=$newOptions['ingredients_custom_groups'][$k]['max_ing'];
								}else{
									$newOptions['ingredients_custom_groups'][$k]['min_ing']=$selMinIng;
								}
							}
						}else{
							$newOptions['ingredients_custom_groups'][$k]['max_ing']=0;
							$newOptions['ingredients_custom_groups'][$k]['min_ing']=1;
						}

						/*maximum number of *the same* ingredient->only relevant when set to type 2 or 4**/
						if($newOptions['ingredients_custom_groups'][$k]['type']==2 || $newOptions['ingredients_custom_groups'][$k]['type']==4){
							$newOptions['ingredients_custom_groups'][$k]['max_same_ing']=wppizza_validate_int_only($v['max_same_ing']);
							$newOptions['ingredients_custom_groups'][$k]['max_total_ing']=wppizza_validate_int_only($v['max_total_ing']);
							
							/**min_total_ing cannot be > max_total_ing*/
							$min_total_ing_set=wppizza_validate_int_only($v['min_total_ing']);
							if($min_total_ing_set>$newOptions['ingredients_custom_groups'][$k]['max_total_ing']){
								$min_total_ing_set=$newOptions['ingredients_custom_groups'][$k]['max_total_ing'];
							}
							$newOptions['ingredients_custom_groups'][$k]['min_total_ing']=$min_total_ing_set;
						}else{
							$newOptions['ingredients_custom_groups'][$k]['max_same_ing']=0;
							$newOptions['ingredients_custom_groups'][$k]['max_total_ing']=0;
							$newOptions['ingredients_custom_groups'][$k]['min_total_ing']=0;
						}

						$newOptions['ingredients_custom_groups'][$k]['ingredient']=array();
						/**ingredients in group**/
						if(isset($v['ingredient']) && is_array($v['ingredient'])){
							foreach($v['ingredient'] as $l=>$m){
								$mVal=wppizza_validate_int_only($m);
								$newOptions['ingredients_custom_groups'][$k]['ingredient'][$mVal]=$mVal;
							}
						}
						/**menu item to apply group to  **/
						$newOptions['ingredients_custom_groups'][$k]['item']=array();
						if(isset($v['item']) && is_array($v['item'])){
							foreach($v['item'] as $l=>$m){
								$mVal=wppizza_validate_int_only($m);
								$newOptions['ingredients_custom_groups'][$k]['item'][$mVal]=$mVal;
							}
						}

						/**add any new options to the end as otherwsie the order stuff gets displayed in the backend might change on save and people get confused*/
						$newOptions['ingredients_custom_groups'][$k]['hide_prices']=!empty($v['hide_prices']) ? true : false;
						$newOptions['ingredients_custom_groups'][$k]['sort_by_price_first']=!empty($v['sort_by_price_first']) ? true : false;

				}}}
		}

		/****************************
		*
		*	[localization]
		*
		****************************/
		if(isset($input['localization'])){
		$newOptions['localization'] = array();
		foreach($input['localization'] as $a=>$b){
			/*add new value , but keep desciption (as its not editable on frontend)*/
			//$newOptions['localization'][$a]=array('lbl'=>wppizza_validate_string($b),'descr'=>$oldOptions['localization'][$a]['descr']);
			$newOptions['localization'][$a]=array('lbl'=>wppizza_validate_string($b));
		}}

	/***************************************************************************************************************
	*	check selected ingredients
	*	if some of these do not exist anymore make sur ethey are also removed from custom groups
	*
	**************************************************************************************************************/
	if($newOptions['ingredients']!=$this->pluginOptionsNoWpml['ingredients']){
		foreach($newOptions['ingredients_custom_groups'] as $k=>$v){
			foreach($v['ingredient'] as $iId=>$iIdVal){
				/**unset deleted ingredients**/
				if(!in_array($iIdVal,array_keys($newOptions['ingredients']))){
					unset($newOptions['ingredients_custom_groups']['ingredient'][$iIdVal]);
				}
				/*unset disabled ingredients**/
				if(!isset($newOptions['ingredients'][$iIdVal]['enabled'])){
					unset($newOptions['ingredients_custom_groups']['ingredient'][$iIdVal]);
				}
			}
		}
	}
	/*************check for text boxes*******************/
	foreach($newOptions['ingredients_custom_groups'] as $k=>$v){
		/**set if post (i.e menu item ) has a textbox as custom group**/
		if($v['sizes']=='textbox' && count($v['item'])>0){
			foreach($v['item'] as $pId){
				$postHasTextbox[$pId]=true;	
			}
		}
	}
	/***************************************************************************************************************
		check selected postsizes and ingredients enabled of all menu items
		if we have deleted/or disabled all ingredients of a pricetier that is enabled on a menu item
		update menu item to NOT have add ingredients enabled, as there wont be any to choose from
	**************************************************************************************************************/
			$enabledIngredientTiers=array();
			foreach($newOptions['ingredients'] as $k=>$v){
				if($v['enabled']){
					$enabledIngredientTiers[$v['sizes']]=$v['sizes'];
				}
			}

			/**get all posts of this posttype**/
			$posts = get_posts(array(
    			'post_type'   => WPPIZZA_POST_TYPE,
    			'post_status' => 'publish',
    			'posts_per_page' => -1,
    			'fields' => 'ids'
    		));
			if(isset($posts) && is_array($posts)){
			foreach($posts as $k=>$p){
    		//get the meta we need from post
    			$post_meta_values = get_post_meta($p,WPPIZZA_SLUG,true);
    			$selectedSize=$post_meta_values['sizes'];
    			$post_meta_ingredients_enabled = get_post_meta($p,$this->pluginMetaValue,true);
				$ingredientsDisabled[$p]	= !empty($post_meta_ingredients_enabled[$this->pluginMetaValue]) ? true : false;
				/**************************************************************************
					[if the size of this posttype has no corresponding (enabled)ingredients
					AND no textbox  enabled 
					AND the ingredients box was checked, force update meta value
					to false for this item]
				**************************************************************************/
				if(!in_array($selectedSize,$enabledIngredientTiers) && !isset($postHasTextbox[$p]) && $ingredientsDisabled[$p]){
					update_post_meta($p,''.$this->pluginMetaValue.'',false);
				}
			}}
	}
?>