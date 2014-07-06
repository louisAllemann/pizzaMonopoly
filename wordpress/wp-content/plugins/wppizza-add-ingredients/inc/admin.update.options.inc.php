<?php
foreach($defaultOptionsThisPlugin['localization'] as $k=>$v){
	if(!isset($options['localization'][$k]['lbl'])){
		$options['localization'][$k]['lbl']=$defaultOptionsThisPlugin['localization'][$k]['lbl'];
	}
}
if(!isset($options['ingredients_custom_groups'])){
	$options['ingredients_custom_groups']=$defaultOptionsThisPlugin['ingredients_custom_groups'];
}



/*********settings*****************/
if(!isset($options['settings'])){
	$options['settings']=$defaultOptionsThisPlugin['settings'];
}else{
	/**update with newly added options*/
	foreach($defaultOptionsThisPlugin['settings'] as $settings=>$s){
		if(!array_key_exists($settings,$options['settings'])){
			$options['settings'][$settings]=$s;	
		}
	}
}

/*********license*****************/
if(!isset($options['license'])){
	$options['license']=$defaultOptionsThisPlugin['license'];
}
	
	

/*********options*****************/
if(!isset($options['options'])){
	$options['options']=$defaultOptionsThisPlugin['options'];
}else{
	/**update with newly added options*/
	foreach($defaultOptionsThisPlugin['options'] as $settings=>$s){
		if(!array_key_exists($settings,$options['options'])){
			$options['options'][$settings]=$s;	
		}
	}
}




/****access capabilities update****/
if(isset($options['admin_access_caps']) || isset($resetCaps)){
	global $wp_roles;
	
	/**available caps**/
	$wppizzaAddIngrCaps=$this->wppizza_ingredients_capabilities_tabs();

	$wppizzaCapsAvailable=array();
	foreach($wppizzaAddIngrCaps as $role=>$caps){
		$wppizzaCapsAvailable[$caps['cap']]=$caps['cap'];
	}	

	/**previously already set caps (mk unique)**/
	$wppizzaCapsSet=array();
	foreach($defaultOptionsThisPlugin['admin_access_caps'] as $role=>$caps){
		foreach($caps as $cap){
		$wppizzaCapsSet[$cap]=$cap;
		}
	}
	/**reset it something got really screwed up*/
	if(isset($resetCaps)){
		$capsToDb=array();
		foreach($wp_roles->roles as $rName=>$rVal){
			$userRole = get_role($rName);
			/***first remove ALL wppizza caps**/
			foreach($wppizzaAddIngrCaps as $role=>$caps){
				$userRole->remove_cap( ''.$caps['cap'].'' );
			}
			/**now lets add all caps for everyone that can manage options**/
			$userRole = get_role($rName);/**get role again which should now have none of the wppizza caps anymore*/
			foreach($wppizzaAddIngrCaps as $role=>$caps){
				if(isset($rVal['capabilities']['manage_options']) && !isset($userRole->capabilities[$caps['cap']])){
					$userRole->add_cap( ''.$caps['cap'].'' );
					$capsToDb[$rName][]=$caps['cap'];
				}			
			}			
		}
	}else{

		/**newly available caps**/
		$newCaps=array_diff($wppizzaCapsAvailable,$wppizzaCapsSet);

		/**wppizza caps options set to save in db**/
		$capsToDb=array();
		foreach($wp_roles->roles as $rName=>$rVal){
			$userRole = get_role($rName);
			foreach($wppizzaCapsAvailable as $avCap){
				if(isset($userRole->capabilities[$avCap])){
					$capsToDb[$rName][]=$avCap;
				}
			}
		}
		/**add newly added caps if we are updating the plugin (as opposed to new install)**/
		if(is_array($newCaps) && count($newCaps)>0){
			foreach($newCaps as $newCap){
				foreach($wp_roles->roles as $rName=>$rVal){
					$userRole = get_role($rName);
					/**if this user has manage option caps and this cap has not already been set , add it**/
					if(isset($rVal['capabilities']['manage_options']) && !isset($userRole->capabilities[$newCap])){
						$userRole->add_cap( ''.$newCap.'' );
						$capsToDb[$rName][]=$newCap;
					}
				}
			}
		}
	}
	/**save array of array options**/
	$options['admin_access_caps']=$capsToDb;	
}
?>