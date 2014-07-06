<?php
/********************************************************************************************

	Class to allow to deal more easily with user capabilties .
	Currently only used in dependend extensions, but will (should) - one day - also
	really be utilized in this main WPPizza plugin (that's why it's here...:)


********************************************************************************************/
if (!class_exists( 'WPPizza' ) ) {return ;}
class WPPIZZA_USER_CAPS{
	function __construct() {
		//parent::__construct();
		add_filter('editable_roles', array($this,'user_caps_roles_remove_higher_levels'));
	}

	/****************************************************************************************
	*
	*	[add caps on install or - on update - remove old / add new caps]
	*	$caps=array();
	*	$caps['timed_items']=array('name'=>__('Item name','locale'),'cap'=>'my_unique_cap');
	*	$options =$this->myPluginOptions['my_option_key']
	*
	*****************************************************************************************/
	function user_caps_ini($caps=array(),$options){
		global $wp_roles;
		/*get all roles that have manage_options capabilities**/
		$defaultAdmins=array();
		foreach($wp_roles->roles as $rName=>$rVal){
			if(isset($rVal['capabilities']['manage_options'])){
				$defaultAdmins[$rName]=$rName;
			}
		}


		/**first install, options are empty**/
		if($options==0){
			/**foreach of these, add all capabilities**/
			$setCaps=array();
			foreach($defaultAdmins as $k=>$roleName){
				$userRole = get_role($roleName);
				if(is_array($caps)){
				foreach($caps as $akey=>$aVal){
					$setCaps[$k][]=$aVal['cap'];
					/**no point in adding it twice**/
					if(!$userRole->has_cap( ''.$aVal['cap'].'' )){
						$userRole->add_cap( ''.$aVal['cap'].'' );
					}
				}}
			}
		}else{
			/** not first install, get currently set, compare*/
			$setCaps=$options;/**current set caps**/

			$previousCaps=array();
			foreach($setCaps as $roleName=>$roleCaps){
				$flipCaps[$roleName]=array_flip($roleCaps);
				foreach($roleCaps as $prevCaps){
					$previousCaps[$prevCaps]=$prevCaps;
				}
			}
			$currentCaps=array();
			foreach($caps as $roleCaps){
				$currentCaps[$roleCaps['cap']]=$roleCaps['cap'];
			}

			/**remove caps not in use anymore***/
			$removedCaps=array_diff($previousCaps,$currentCaps);
			foreach($removedCaps as $rCap){
				foreach($wp_roles->roles as $rName=>$rVal){
					$userRole = get_role($rName);
					if($userRole->has_cap( ''.$rCap.'' )){
						unset($setCaps[$rName][$flipCaps[$rName][$rCap]]);/*remove from array*/
						$userRole->remove_cap( $rCap );
					}
				}
			}

			/**add newly added caps ***/
			$addedCaps=array_diff($currentCaps,$previousCaps);
			foreach($defaultAdmins as $k=>$roleName){
				$userRole = get_role($roleName);
				foreach($addedCaps as  $aCap){
					/**no point in adding it twice**/
					if(!$userRole->has_cap( ''.$aCap.'' )){
						$setCaps[$roleName][]=$aCap;
						$userRole->add_cap( ''.$aCap.'' );
					}
				}
			}
		}
	return $setCaps;
	}
	/****************************************************************************************
	*
	*	[get caps of current user]
	*
	*****************************************************************************************/
	function current_user_caps($caps){
		global $current_user;
		$usercaps=array();
		$capUnique=array();/*dont need to have the same thing multiple times*/
		/*user can have more than one role**/
		foreach($current_user->roles as $roleName){
			$userRole = get_role($roleName);
			foreach($caps as $tab=>$v){
				if(isset($userRole->capabilities[$v['cap']]) && !isset($capUnique[$v['cap']])){
					//$usercaps[]=array('tab'=>$tab,'cap'=>$v['cap'],'name'=>$v['name']);
					$usercaps['caps'][]=$v['cap'];
					$usercaps['tabs'][]=$tab;
					$usercaps['name'][]=$v['name'];
					$capUnique[$v['cap']]=1;
				}
			}
		}
		return $usercaps;
	}
	/****************************************************************************************
	*
	*	[return ul of roles and relevant caps]
	*
	*****************************************************************************************/
	function user_echo_admin_caps($caps,$slug,$key,$showSelf=false){
		global $current_user,$user_level,$wp_roles;
		$roles=get_editable_roles();/*only get roles user is allowed to edit**/		
		/****add hidden element, so we can update/validate even when ALL checkboxes are unchecked**/
		echo"<input name='".$slug."[".$key."]' type='hidden' value='1' />";
		foreach($roles as $roleName=>$v){
			$userRole = get_role($roleName);
			/*do not display current users role (otherwise he can screw his own access)*/
			if(!in_array($roleName,$current_user->roles) || $showSelf){
			echo"<ul class='wppizza_".$slug."_".$key."'>";
				echo"<li style='width:150px'><b>".$roleName.":</b></li>";
				foreach($caps as $aKey=>$aArray){
					echo"<li><input name='".$slug."[".$key."][".$roleName."][".$aArray['cap']."]' type='checkbox'  ". checked(isset($userRole->capabilities[$aArray['cap']]),true,false)." value='".$aArray['cap']."' /> ".$aArray['name']."</li>";//". checked($options['plugin_data']['access_level'],true,false)."
				}
			echo"</ul>";
			}
		}
	}
	/****************************************************************************************
	*
	*	[validate and set / revoke caps as required]
	*
	*****************************************************************************************/
	function user_validate_admin_caps($caps,$capsCurrent,$capsPost){
		$newCaps=$capsCurrent;

		foreach($capsPost as $roleName=>$v){
			$userRole = get_role($roleName);
			$capsFlip[$roleName]=!empty($capsCurrent[$roleName]) ? $capsCurrent[$roleName] : array();

			foreach($caps as $akey=>$aVal){
				/**not checked, but previously selected->remove capability**/
				if(isset($userRole->capabilities[$aVal['cap']]) && ( !is_array($capsPost[$roleName]) || !isset($capsPost[$roleName][$aVal['cap']]))){
					unset($newCaps[$roleName][$capsFlip[$roleName][$aVal['cap']]]);/*remove from array*/
					$userRole->remove_cap( ''.$aVal['cap'].'' );
				}
				/**checked and NOT previously selected->add capability*/
				if(is_array($capsPost[$roleName]) && isset($capsPost[$roleName][$aVal['cap']]) && !isset($userRole->capabilities[$aVal['cap']])){
					$newCaps[$roleName][]=$aVal['cap'];/*add to array*/
					$userRole->add_cap( ''.$aVal['cap'].'' );
				}
			}
		}
		return $newCaps;
	}
	/****************************************************************************************
	*
	*	[filter editable roles]
	*
	*****************************************************************************************/	
	function user_caps_roles_remove_higher_levels($all_roles) {
    	$user = wp_get_current_user();
    	$next_level = 'level_' . ($user->user_level + 1);
    	foreach ( $all_roles as $name => $role ) {
	        if (isset($role['capabilities'][$next_level])) {
            	unset($all_roles[$name]);
        	}
    	}
	    return $all_roles;
	}
}
?>