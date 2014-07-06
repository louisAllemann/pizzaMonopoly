<?php
error_reporting(0);
if(!defined('DOING_AJAX') || !DOING_AJAX){
	header('HTTP/1.0 400 Bad Request', true, 400);
	print"you cannot call this script directly";
  exit; //just for good measure
}
$options=$this->masterOptions;
$optionSizes=wppizza_sizes_available($options['sizes']);//outputs an array $arr=array(['lbl']=>array(),['prices']=>array());

$output='';
/**adding new ingredients**/
if($_POST['vars']['field']=='ingredients' && $_POST['vars']['id']>=0){
	/**********set header********************/
	header('Content-type: text/html');

	/*add to o*ptions array to avoid !isset */
	$options[$_POST['vars']['field']][$_POST['vars']['id']]['item'] = '';
	$options[$_POST['vars']['field']][$_POST['vars']['id']]['sizes'] = '';
	$options[$_POST['vars']['field']][$_POST['vars']['id']]['enabled'] = true;
	/**copy**/
	if(isset($_POST['vars']['copyId'])){
		$v=$this->pluginOptionsNoWpml['ingredients'][$_POST['vars']['copyId']];
		$options[$_POST['vars']['field']][$_POST['vars']['id']]['item'] = $v['item'].' ('.__('copy',$this->pluginLocale).')';
		$options[$_POST['vars']['field']][$_POST['vars']['id']]['sizes'] = $v['sizes'];
		$options[$_POST['vars']['field']][$_POST['vars']['id']]['prices'] = $v['prices'];
		$options[$_POST['vars']['field']][$_POST['vars']['id']]['enabled'] = false;
	}else{
		$v='';
	}

	$output=$this->wppizza_admin_section_ingredients($_POST['vars']['field'],$_POST['vars']['id'],$v,$options,$optionSizes,false);
}
/**adding new ingredients group**/
if($_POST['vars']['field']=='ingredients_group' && $_POST['vars']['id']>=0){
	/**********set header********************/
	header('Content-type: text/html');

	$output=$this->wppizza_admin_section_ingredients_groups($_POST['vars']['id'],$optionSizes);
}

/**copy ingredients to group**/
if($_POST['vars']['field']=='ingredients_group_copy' && $_POST['vars']['sourceId']>=0 && $_POST['vars']['destId']>=0){
	/**********set header********************/
	header('Content-type: text/html');
	$sourceId=(int)$_POST['vars']['sourceId'];
	$destId=(int)$_POST['vars']['destId'];
	$setOptions=$this->pluginOptionsNoWpml;
	
	$destinationSizes=count($optionSizes[$destId]['price']);
	$allCurrentIngredients=$this->pluginOptionsNoWpml['ingredients'];
	
	$ingrfromCopy=array();
	$ingrToCopy=array();
	foreach($allCurrentIngredients as $k=>$v){
		if($v['sizes']==$sourceId){
			$ingrfromCopy[]=$v;
		}
		if($v['sizes']==$destId){
			$ingrToCopy[]=$v;
		}		
	}
	/*sort destination by key in reverse so we can get the highest key*/
	$fKey=0;
	if(count($allCurrentIngredients)>0){
		krsort($allCurrentIngredients);
		reset($allCurrentIngredients);
		$fKey = key($allCurrentIngredients)+1;
	}
	/**now add to options ingredients array and set prices iv available**/
	foreach($ingrfromCopy as $k=>$v){
		$setOptions['ingredients'][$fKey]['sizes']=$destId;
		$setOptions['ingredients'][$fKey]['item']=$v['item'];
		for($i=0;$i<(int)$destinationSizes;$i++){
			if(isset($v['prices'][$i])){
				$setOptions['ingredients'][$fKey]['prices'][$i]=$v['prices'][$i];
			}else{
				$setOptions['ingredients'][$fKey]['prices'][$i]=0;	
			}
		}
		/*disable by default*/
		$setOptions['ingredients'][$fKey]['enabled']=false;
		$fKey++;
	}
	$ingredientsAdded=count($setOptions['ingredients'])-count($allCurrentIngredients);
	if($ingredientsAdded>0){
		update_option($this->pluginSlug, $setOptions );	
	}
$output=''.$ingredientsAdded.' '.__('ingredients added',$this->pluginLocale).'';
}

/**select new ingredients group**/
if($_POST['vars']['field']=='ingredients_group_select' && $_POST['vars']['id']>=0 && $_POST['vars']['selKey']>=0){
	/**********set header********************/
	header('Content-type: text/html');

	$output=$this->wppizza_admin_section_ingredients_groups($_POST['vars']['id'],$optionSizes,$_POST['vars']['selKey']);
}
/**verify/validate ingredients groups before save**/
if($_POST['vars']['field']=='ingredients_group_validate'){
	/**********set header********************/
	header('Content-type: application/json');

	/*****************************************
		[get and parse all post variables
	*****************************************/
	$params = array();
	parse_str($_POST['vars']['data'], $params);


	/****************************************
		[set default return vars]
	****************************************/
	$output=array();
	$output['valid']=1;//0->invalid / 1->valid
	$output['msg']=''.__('Sorry, you cannot have the same ingredient for the same menu item in more than one custom group. Duplicate Ingredient for',$this->pluginLocale).': '.PHP_EOL;
	/****************************************
		[get custom groups to validate]
	****************************************/

	if(isset($params['wppizza_addingredients']['ingredients_custom_groups']) && is_array($params['wppizza_addingredients']['ingredients_custom_groups'])){
		$customGroups=$params['wppizza_addingredients']['ingredients_custom_groups'];
		$chkArr=array();
		foreach($customGroups as $k=>$v){
			/*exclude preselect and exclude from check*/
			if($v['type']<5){
			if(isset($v['ingredient'])){
			foreach($v['ingredient'] as $ingrID){
				if(isset($v['item'])){
				foreach($v['item'] as $pageID){
					/***if we already have this set before, set valid=0 and exit**/
					if(isset($chkArr[''.$v['sizes'].'-'.$pageID.'-'.$ingrID.''])){
						$output['valid']=0;
						$postTitle = get_post($pageID);
						$output['msg'].=''.__('Group',$this->pluginLocale).': '.implode(",",$optionSizes[$v['sizes']]['lbl']) . PHP_EOL ;
						$output['msg'].=''.__('Ingredient',$this->pluginLocale).': '. $this->pluginOptionsNoWpml['ingredients'][$ingrID]['item'] . PHP_EOL ;
						$output['msg'].=''.__('Menu Item',$this->pluginLocale).': '. $postTitle->post_title . PHP_EOL ;
						//$output['msg'].=''.implode(",",$optionSizes[$v['sizes']]['lbl']) . PHP_EOL . $this->pluginOptionsNoWpml['ingredients'][$ingrID]['item']. PHP_EOL . $postTitle->post_title . PHP_EOL;
						$output['grpId_1']=$chkArr[''.$v['sizes'].'-'.$pageID.'-'.$ingrID.''];
						$output['grpId_2']=$k;
						$output['ingrID']=$ingrID;
						print"".json_encode($output)."";
						exit();
					}
					$chkArr[''.$v['sizes'].'-'.$pageID.'-'.$ingrID.'']=$k;
				}}
			}}}
		}
	}

	/**return validation vars as json and exit **/
	print"".json_encode($output)."";
	exit();
}
print"".$output."";
exit();
?>