<?php
error_reporting(0);
if(!defined('DOING_AJAX') || !DOING_AJAX){
	header('HTTP/1.0 400 Bad Request', true, 400);
	print"you cannot call this script directly";
  exit; //just for good measure
}
/**testing variables ****************************/
//unset($_SESSION[$this->pluginSession]);
//sleep(1);//when testing jquery fadeins etc
/******************************************/
/**get ingredients and options of master plugin***/
$options=$this->pluginOptions;
$optionsMaster=$this->masterOptions;
/**make labels somewhat more manageable**/
$labels=$this->wppizza_ingredients_labels($this->pluginOptions['localization']);


/*****************************************************************************
*
*
*	[get ingredients for this pricetier and initialize new session item]
*
*
*****************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='getingredients' && (int)$_POST['vars']['item']>=0 && (int)$_POST['vars']['tier']>=0 && (int)$_POST['vars']['size']>=0){
header('Content-type: text/html');

	/*just for the hell of it**/
	$itemId=(int)$_POST['vars']['item'];
	$tierId=(int)$_POST['vars']['tier'];
	$sizeId=(int)$_POST['vars']['size'];
	/**custom groups**/
	$customGroups=$options['ingredients_custom_groups'];
	/**make an array with all preselected ingredienst we can compare against later to display "no 'something'" if it has been deselected**/
	$preSelDefaultSelected=array();
	
	if(isset($_POST['vars']['multi'])){
		$multi=(int)$_POST['vars']['multi'];
	}

	/**sticky added ingredients. popup only**/
	if($options['options']['ingredients_in_popup'] && $options['options']['ingredients_added_sticky']){
		$addedIngrSticky=1;
	}


	/**multiple allowed*/
	$meta_values_multi = get_post_meta($itemId,$this->pluginMetaMulti,true);
	$multiIngredients=$meta_values_multi['add_ingredients_multi'];
	/**make sure we have a value/array for ingredients that were set in previous versions of the plugin that had no multioption yet*/
	if(!isset($multiIngredients) || !is_array($multiIngredients)){
		$multiIngredients=array(1=>1);/*whole only**/
	}


	/**get the title to add to thickbox header***/
	if($options['options']['ingredients_in_popup']){
		$output['title']=get_the_title($itemId);
	}
	
	/***********************************************************
	*
	*	[if more than one multi (half/quarter/whole) option
	*	render multi buttons first and - possibly - "add as is" button and exit]
	*
	***********************************************************/
	if(count($multiIngredients)>1 && !isset($multi)){
		if($options['options']['ingredients_addasis_button_enabled']){			
			$doAddAsIsButton=true;
			/**check if we can add a "add to cart without adding ingredients" / "add as is" button (should only be available if there are no required fileds)**/
			foreach($customGroups as $cGroup){
				if(isset($cGroup['item'][$itemId])){/*get group that apllies to this item**/
					/***if it's a preselect group or radio (ie must have one) or maust have at least one set to required and skip rest**/
					if($cGroup['type']==6 || $cGroup['type']==1 || $cGroup['type']==2){
						$doAddAsIsButton=false;
						break;
					}
					if(($cGroup['type']==3 || $cGroup['type']==4) && ( $cGroup['min_ing']>0|| $cGroup['min_total_ing']>0)){
						$doAddAsIsButton=false;
						break;
					}
				}
			}
		}	
		/**************************************************end check************************************************************************************/
		$output['head']='';
		$output['body']='<div class="wppizza-multiselect-main wppizza-cart-button"><span class="wppizza-multiselect-main-lbl">'.$options['localization']['multi_label_buttons']['lbl'].'</span>';
		foreach($multiIngredients as $k=>$v){
			$output['body'].='<input id="wppizza-multi-'.$itemId.'-'.$tierId.'-'.$sizeId.'-'.$k.'" class="btn wppizza-multi-button-main" type="button" value="'.$options['localization']['multi_button_'.$k.'']['lbl'].'" />';
		}
		if(isset($doAddAsIsButton) && $doAddAsIsButton){
			$output['body'].='<input id="wppizza-addasis" class="btn wppizza-addasis" type="button" value="'.$options['localization']['addasis_button']['lbl'].'" />';
		}
		$output['body'].='</div>';


	
		
		/***********************************************************************************************
		*
		*	initialize session here if required (i.e if we are displaying "add as is button" )
		*	, otherwise we'll do it in the next screen as there's no need to run stuff earlier than needed
		*	(one day we should put that in a function as we'll be running the same stuff further down)
		************************************************************************************************/
		if(isset($doAddAsIsButton) && $doAddAsIsButton){
			/**if we are using this, we must set the session here **/
			/***get base price for this item***/
			$meta_values_ingredients = get_post_meta($itemId,WPPIZZA_SLUG,true);
			$basePrice=$meta_values_ingredients['prices'][$sizeId];
			/***get name etc for this item***/
			$postDetails = get_post($itemId,ARRAY_A );	
			/**size name**/
			/**are we hiding pricetier name if only one available ?**/
 			if(count($meta_values_ingredients['prices'])<=1 && $optionsMaster['layout']['hide_single_pricetier']==1){
				$sizeName='';
 			}else{
				$sizeName = $optionsMaster['sizes'][$tierId][$sizeId];
 			}			
			/**empty and set session for this diy item**/
			unset($_SESSION[$this->pluginSession]['diy']);
			$_SESSION[$this->pluginSession]['diy']=array('name'=>$postDetails['post_title'],'item'=>$itemId,'tier'=>$tierId,'size'=>$sizeId,'sizename'=>$sizeName['lbl'],'baseprice'=>$basePrice,'ingredients'=>array());		
		}

		print"".json_encode($output)."";
	exit();
	}

	/***********************************************************
	*
	*	[if only one option available set to whatever option there is
	*	to save us rendering one - pointless - select button]
	*
	***********************************************************/
	if(count($multiIngredients)<=1 && !isset($multi)){
		reset($multiIngredients);
		$fKey = key($multiIngredients);
		$multi=(int)$multiIngredients[$fKey];
	}

	/**check that selected multingredient no we got via js is actually enabled and has not been tampered with**/
	if(isset($multi) && in_array($multi,$multiIngredients)){
		$multiSet=$multi;
	}else{
		$multiSet=1;/*whole only**/
	}

	/**price multiplier (percentage of whole price)*/
	$priceMultiply=1;/*set just in case*/
	$priceMultiplyWhole=1;/*set just in case*/
	$meta_values_devide = get_post_meta($itemId,$this->pluginMetaMultiDivide,true);
	if(isset($meta_values_devide[$this->pluginMetaMultiDivide])){
	$multiDivide=$meta_values_devide[$this->pluginMetaMultiDivide];
	if(isset($multiDivide[$multiSet])){
		$priceMultiply=$multiDivide[$multiSet]/100;
	}
	/**multiplier for ingredients set for "whole" item*/
	$priceMultiplyWhole=$multiDivide[1]/100;
	}

	/*******************************************************************************************************************
	*
	*	[custom Groups]
	*
	*******************************************************************************************************************/


	/*******************************************************
		if ingredient is in group that has "apply to whole menu" set
		make an array of these as the multiplier should be %
		set for "whole" item
	********************************************************/
	$ingrWholeOnly=array();
	foreach($customGroups as $kGroup=>$cGroup){
		if(isset($cGroup['item'][$itemId]) && count($cGroup['ingredient'])>0 && $cGroup['type']<5 && $cGroup['whole_only']==1){
			foreach($cGroup['ingredient'] as $kIng=>$mIng){
				$ingrWholeOnly[$kIng]=$kIng;
			}
		}
	}

	/*******************************************************
		if group5 (i.e exclude ingredients)
		make an array of these so we can - well -exclude them
	*******************************************************/
	$exclIngr=array();
	foreach($customGroups as $kGroup=>$cGroup){
		if(isset($cGroup['item'][$itemId]) && count($cGroup['ingredient'])>0 && $cGroup['type']==5){
			foreach($cGroup['ingredient'] as $kIng=>$mIng){
				$exclIngr[$kIng]=$kIng;
			}
		}
	}

	/*******************************************************
		if group6 (i.e preselect ingredients)
		make an array of these so we can preselect them
	*******************************************************/
	$preSelIngrSet=array();/*initialize array of actually preselected ingredients so we can calc prices and show them as being selected*/
	$preSelIngrInp=array();/*initialize array of actually preselected ingredients to be put into hidden elm*/
	$preSelIngr=array();
	$preSelIngrPriceZero=array();/*initialize array of preselected ingredients where the price is forced to be zero*/
	foreach($customGroups as $kGroup=>$cGroup){
		if(isset($cGroup['item'][$itemId]) && count($cGroup['ingredient'])>0 && $cGroup['type']==6){
			foreach($cGroup['ingredient'] as $kIng=>$mIng){
				if( !isset($exclIngr[$kIng])){/*exclude excluded ingredients**/
					if($cGroup['preselpricezero']){
						$preSelIngrPriceZero[$kIng]=$kIng;/**add to array of preselected, but forced zero prices*/
					}
					$preSelIngr[$kIng]=$kIng;
				}
			}
		}
	}

	/*******************************************************
		if group=textbox
		make an array of these so we can display them
		but maximum one before and one after
	*******************************************************/
	$textbox=array();


	foreach($customGroups as $kGroup=>$cGroup){
		if(isset($cGroup['item'][$itemId]) && $cGroup['sizes']=='textbox'){
			if(!$cGroup['position']){$a='pre';}else{$a='post';}
			$textbox[$a]='<fieldset id="wppizza-ingr-comments-'.$a.'" class="wppizza-ingr-comments">';
			$textbox[$a].='<legend>'.$cGroup['label'].'';
			if($cGroup['info']!=''){
				$textbox[$a].=' <span>'.$cGroup['info'].'</span>';
			}
			$textbox[$a].='</legend>';
			$textbox[$a].='<div><textarea class="wppizza-ingredients-textarea" name="wppizza-ingredients-textarea[]"></textarea></div>';
			$textbox[$a].='</fieldset>';
		}
	}

	/***********************************************************
	*
	*	group ingredients and only display the ones for this tier that are enabled and not excluded
	*
	***********************************************************/
	$ingredients=$options['ingredients'];
	$availableIngredients=array();
	foreach($ingredients as $k=>$v){
		if( !isset($exclIngr[$k])){
		if($v['sizes']==$tierId && $v['enabled']){
			/**preselect item where price is forced to zero**/
			if(isset($preSelIngrPriceZero[$k])){
				$price=0;
				/**also get price before preselected zero price**/
				$doMultiply=$priceMultiply;
				/**ingredients whole item only multiplier**/
				if(isset($ingrWholeOnly[$k])){
					$doMultiply=$priceMultiplyWhole;
				}
				$priceNonPreselect=round($v['prices'][$sizeId]*$doMultiply,2);/**use for grouping**/
			}else{
				$doMultiply=$priceMultiply;
				/**ingredients whole item only multiplier**/
				if(isset($ingrWholeOnly[$k])){
					$doMultiply=$priceMultiplyWhole;
				}
				$price=round($v['prices'][$sizeId]*$doMultiply,2);
				$priceNonPreselect=$price;/**use for grouping**/
			}

			/*in custom groups, we might not want to display prices for an ingredient if it's 0.00*/
			$pricegroup="".$optionsMaster['order']['currency_symbol']."".wppizza_output_format_float($price)."";
			if($price==0){
				if($options['settings']['price_localize_if_zero']!=''){
					$pricegroup=''.$options['settings']['price_localize_if_zero'].'';
				}
				if(!$options['settings']['price_show_if_zero']){
					$pricegroup='';
				}
			}
			$availableIngredients[$k]=array('pricebeforepreselect'=>wppizza_output_format_float($priceNonPreselect),'price'=>wppizza_output_format_float($price),'namesort'=>strtolower($v['item']),'name'=>$v['item'],'pricegroup'=>$pricegroup);
		}
	}}
	$noOfAvailIngr=count($availableIngredients);// -> ADDED 4.3.4 in case there are textboxes only
	asort($availableIngredients);	
	/**customise sort order**/
	$availableIngredients = apply_filters('wppizza_filter_ingredients_custom_sort', $availableIngredients);

	/*******************************************************************************************************************
	*
	*	[custom Groups - non exclude]
	*
	*******************************************************************************************************************/

	/**array that holds all ingredients that are used in custom groups and should therefore be removed from "normal" groups**/
	$remIngr=array();
	/**sort etc and put custom groups in array to display further down**/
	$arrCustomGroups=array();
	asort($customGroups);

	foreach($customGroups as $kGroup=>$cGroup){
		/**only deal with groups that are applied to this menu item and only if no of ingredients>0 and type<5 (i.e exclude(5) / preselect(6))*/
		if(isset($cGroup['item'][$itemId]) && count($cGroup['ingredient'])>0 && $cGroup['type']<5){
			$arrCustomGroups[$kGroup]['sort']=$cGroup['sort'];
			$arrCustomGroups[$kGroup]['type']=$cGroup['type'];
			$arrCustomGroups[$kGroup]['label']=$cGroup['label'];
			$arrCustomGroups[$kGroup]['info']=$cGroup['info'];
			$arrCustomGroups[$kGroup]['whole_only']=$cGroup['whole_only'];
			$arrCustomGroups[$kGroup]['position']=$cGroup['position'];
			$arrCustomGroups[$kGroup]['sort_by_price_first']=$cGroup['sort_by_price_first'];
			$arrCustomGroups[$kGroup]['hide_prices']=$cGroup['hide_prices'];
			$arrCustomGroups[$kGroup]['max_ing']=$cGroup['max_ing'];
			$cIngrGr=array();
			/**get ingredients attributes and sort by name**/
			foreach($cGroup['ingredient'] as $kIng=>$mIng){
				if(isset($availableIngredients[$kIng]) && !isset($exclIngr[$kIng])){
					if($cGroup['sort_by_price_first']){
						$cIngrGr[$kIng]=array('price'=>''.$availableIngredients[$kIng]['pricegroup'].'','namesort'=>''.strtolower($availableIngredients[$kIng]['name']).'','name'=>''.$availableIngredients[$kIng]['name'].'','id'=>$kIng, 'pricebeforepreselect'=>''.$availableIngredients[$kIng]['pricebeforepreselect'].'');
					}else{
						$cIngrGr[$kIng]=array('namesort'=>''.strtolower($availableIngredients[$kIng]['name']).'','name'=>''.$availableIngredients[$kIng]['name'].'','price'=>''.$availableIngredients[$kIng]['pricegroup'].'','id'=>$kIng, 'pricebeforepreselect'=>''.$availableIngredients[$kIng]['pricebeforepreselect'].'');
					}
					$remIngr[$kIng]=$kIng;
				}
			}
			asort($cIngrGr);
			/**customise sort order**/
			$cIngrGr = apply_filters('wppizza_filter_ingredients_custom_sort', $cIngrGr);

			$arrCustomGroups[$kGroup]['ingredient']=$cIngrGr;
			/**array of all ingredients in group**/
			$ingInGroup=implode(",",array_keys($cIngrGr));
			$arrCustomGroups[$kGroup]['ingredientsgroup']=$ingInGroup.'|'.$cGroup['max_ing'].'|'.$cGroup['max_same_ing'].'|'.$cGroup['min_ing'].'|'.$cGroup['max_total_ing'].'|'.$cGroup['min_total_ing'];
		}
	}



	for($i=1;$i<=(int)$multiSet;$i++){
		/*ini preselect ingredients**/
		$preSelIngrSet[$i]=array();
		$preSelIngrInp[$i]=array();
		/**make pre and post output**/
		$groupOutput['pre'][$i]=array();
		$groupOutput['post'][$i]=array();
		foreach($arrCustomGroups as $k=>$v){
			if(count($v['ingredient'])>0){
				if(!$v['position']){$a='pre';}else{$a='post';}
				/******************************************************************************************
					if set to whole_only, save first in array to display before/after half/quarter selections
					and unset group output in half/qarters
				******************************************************************************************/
				$multiIdent=$i;
				$skipGroup=0;
				if($multiSet>1 && $v['whole_only']==1){
					$multiIdent=0;
					if($i>1){
						$skipGroup=1;
					}
				}
				if($skipGroup!=1){
					$groupOutput[$a][$multiIdent][$k]='<fieldset id="wppizza-ingredients-req-'.$multiIdent.'-'.$k.'" class="wppizza-list-ingredients">';
					$groupOutput[$a][$multiIdent][$k].='<legend>';
					$groupOutput[$a][$multiIdent][$k].=''.$v['label'].'';
					if($v['info']!=''){
					$groupOutput[$a][$multiIdent][$k].=' <span>'.$v['info'].'</span>';
					}
					$groupOutput[$a][$multiIdent][$k].='</legend>';
						$groupOutput[$a][$multiIdent][$k].='<ul>';
						/**output ingredient*/
						foreach($v['ingredient'] as $kIngrGr=>$vIngrGr){

							
							/********************************
								add labels after
								preselected ingredients
								as required
							********************************/
							$preSelCountLbl='';/**no label if not preselected*/
							/**preselected label - i.e first one free or something. only if preselected prices are zero is checked AND the regular price is not zero to start off with**/
							if(isset($preSelIngrPriceZero[$kIngrGr]) && (float)$vIngrGr['pricebeforepreselect']>0){
								$preSelCountLbl='<span>'.$options['localization']['preselect_prices_zero_custom_0']['lbl'].'</span>';
							}
							
							/**show price ? */
							if(((float)$vIngrGr['pricebeforepreselect']>0 || $options['settings']['price_show_if_zero']) && !$v['hide_prices']){
								$doPrintPrice=$vIngrGr['pricebeforepreselect'].'';
							}else{
								$doPrintPrice='';
							}
							/**construct label***/
							$printPrice='';
							if($doPrintPrice!='' || $preSelCountLbl!=''){
								$printPrice=' <span class="wppizza-doingredient-price">';
								$printPrice.=''.wppizza_output_format_price($doPrintPrice,$optionsMaster['layout']['hide_decimals']).'';
								if($doPrintPrice!='' && $preSelCountLbl!=''){$printPrice.=' ';}/*add space if needed*/
								$printPrice.=''.$preSelCountLbl.'';
								$printPrice.='</span>';
							}
							
							
							
							/*********
								if we have enabled to show counts on ing items as well, add a span to hold the count next to ingr button
							****/
							$ingredients_show_count='';

							if($v['type']==0){/*normal selection*/
								/**preselect**/
								$preSelClass='';
								$preSelCount='';
								if(isset($preSelIngr[$kIngrGr])){
									$preSelIngrSet[$multiIdent][$kIngrGr]=$kIngrGr;	
									$preSelClass='-selected'; 
									$preSelCount='1x';
									$preSelDefaultSelected[$multiIdent][$kIngrGr]=$vIngrGr['name'];
								}
								if($options['options']['ingredients_show_count']){
									$ingredients_show_count='<span id="wppizza-ingredient-count-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'" class="wppizza-ingredient-count">'.$preSelCount.'</span>';
								}
								$groupOutput[$a][$multiIdent][$k].='<li id="wppizza-ingredient-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'-'.$k.'" class="wppizza-ingredient-'.$kIngrGr.'"><span class="wppizza-doingredient wppizza-ingr-'.$v['type'].''.$preSelClass.'" title="'.$options['localization']['add']['lbl'].'"><b>+</b></span>'.$ingredients_show_count.''.$vIngrGr['name'].''.$printPrice.'</li>';
							}
							
							
							if($v['type']==1){/*one - and only one - required ->radio*/
								/**preselect**/
								$preSelClass='';
								$preSelCount='';
								$checked=false;
								/*make sure we only select max one in this group*/
								if(isset($preSelIngr[$kIngrGr]) && !isset($typeOneSel[$multiIdent])){
									$preSelIngrSet[$multiIdent][$kIngrGr]=$kIngrGr;	
									$preSelClass='-selected';
									$typeOneSel[$multiIdent]=1;
									$checked=true; 
									$preSelCount='1x';
									$preSelDefaultSelected[$multiIdent][$kIngrGr]=$vIngrGr['name'];
								}
								
								if($options['options']['ingredients_show_count']){
									$ingredients_show_count='<span id="wppizza-ingredient-count-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'" class="wppizza-ingredient-count">'.$preSelCount.'</span>';
								}
								$groupOutput[$a][$multiIdent][$k].='<li id="wppizza-ingredient-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'-'.$k.'" class="wppizza-ingrli-'.$kIngrGr.'"><span class="wppizza-doingredient wppizza-ingr-'.$v['type'].''.$preSelClass.' wppizza-input-native" title="'.$options['localization']['add']['lbl'].'"><b><input type="radio" '.checked($checked,true,false).' id="wppizza-ingredient-req-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'" name="wppizza-ingredient-req-'.$multiIdent.'-'.$k.'" value="'.$kIngrGr.'" /></b></span>'.$ingredients_show_count.''.$vIngrGr['name'].' '.$printPrice.'</li>';
							}
							
							
							if($v['type']==2){/*only one required and allowed - but can be selected more than one times */
								/**preselect**/
								$preSelClass='';
								$preSelCount='';
								/*make sure we only select max one in this group*/
								if(isset($preSelIngr[$kIngrGr]) && !isset($typeTwoSel[$multiIdent])){
									$preSelIngrSet[$multiIdent][$kIngrGr]=$kIngrGr;	
									$preSelClass='-selected';
									$typeTwoSel[$multiIdent]=1; 
									$preSelCount='1x';
									$preSelDefaultSelected[$multiIdent][$kIngrGr]=$vIngrGr['name'];
								}
								if($options['options']['ingredients_show_count']){
									$ingredients_show_count='<span id="wppizza-ingredient-count-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'" class="wppizza-ingredient-count">'.$preSelCount.'</span>';
								}
								$groupOutput[$a][$multiIdent][$k].='<li id="wppizza-ingredient-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'-'.$k.'" class="wppizza-ingrli-'.$kIngrGr.'"><span class="wppizza-doingredient wppizza-ingr-'.$v['type'].''.$preSelClass.'" title="'.$options['localization']['add']['lbl'].'"><b>+</b></span>'.$ingredients_show_count.''.$vIngrGr['name'].' '.$printPrice.'</li>';
							}
							if($v['type']==3){/*at least one required but multiple allowed only 1x per ingredient though*/
								/**preselect**/
								$preSelClass='';
								$preSelCount='';
								$checked=false;
								/*only allow preselect up to max number**/
								if(isset($preSelIngr[$kIngrGr]) && (	!isset($typeThreeSel[$multiIdent]) || count($typeThreeSel[$multiIdent])<$v['max_ing']  || $v['max_ing']==0 )){
									$preSelIngrSet[$multiIdent][$kIngrGr]=$kIngrGr;	
									$preSelClass='-selected';
									$typeThreeSel[$multiIdent][]=1;
									$checked=true; 
									$preSelCount='1x';
									$preSelDefaultSelected[$multiIdent][$kIngrGr]=$vIngrGr['name'];
								}
								
								if($options['options']['ingredients_show_count']){
									$ingredients_show_count='<span id="wppizza-ingredient-count-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'" class="wppizza-ingredient-count">'.$preSelCount.'</span>';
								}
								$groupOutput[$a][$multiIdent][$k].='<li id="wppizza-ingredient-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'-'.$k.'" class="wppizza-ingrli-'.$kIngrGr.'"><span class="wppizza-doingredient wppizza-ingr-'.$v['type'].''.$preSelClass.' wppizza-input-native" title="'.$options['localization']['add']['lbl'].'"><b><input type="checkbox" '.checked($checked,true,false).' id="wppizza-ingredient-req-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'" value="'.$kIngrGr.'" /></b></span>'.$ingredients_show_count.''.$vIngrGr['name'].' '.$printPrice.'</li>';
							}
							if($v['type']==4){/*at least one required but multiple allowed */
								/**preselect**/
								$preSelClass='';
								$preSelCount='';
								/*only allow preselect up to max number**/
								if(isset($preSelIngr[$kIngrGr]) && (	!isset($typeFourSel[$multiIdent]) || count($typeFourSel[$multiIdent])<$v['max_ing']   || $v['max_ing']==0 )  ){
									$preSelIngrSet[$multiIdent][$kIngrGr]=$kIngrGr;	
									$preSelClass='-selected'; 
									$typeFourSel[$multiIdent][]=1;
									$preSelCount='1x';
									$preSelDefaultSelected[$multiIdent][$kIngrGr]=$vIngrGr['name'];
								}
								
								if($options['options']['ingredients_show_count']){
									$ingredients_show_count='<span id="wppizza-ingredient-count-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'" class="wppizza-ingredient-count">'.$preSelCount.'</span>';
								}
								$groupOutput[$a][$multiIdent][$k].='<li id="wppizza-ingredient-'.$multiIdent.'-'.$kIngrGr.'-'.$multiSet.'-'.$k.'" class="wppizza-ingrli-'.$kIngrGr.'"><span class="wppizza-doingredient wppizza-ingr-'.$v['type'].''.$preSelClass.'" title="'.$options['localization']['add']['lbl'].'"><b>+</b></span>'.$ingredients_show_count.''.$vIngrGr['name'].' '.$printPrice.'</li>';
							}
						}
						$groupOutput[$a][$multiIdent][$k].='</ul>';

						if($v['type']>=1){
							$groupOutput[$a][$multiIdent][$k].='<input type="hidden" id="wppizza-ireq-'.$v['type'].'-'.$multiIdent.'-'.$k.'" class="wppizza-ireq" value="'.$v['ingredientsgroup'].'" />';//text to check
						}

					$groupOutput[$a][$multiIdent][$k].='</fieldset>';
					/******************************************************************************************
						if set to whole_only, save first in array to display before/after half/quarter selections
						and unset group output in half/qarters
					******************************************************************************************/
					if($multiSet>1 && $v['whole_only']==1){
						$groupWholeOnly[$a][$k]=$groupOutput[$a][$multiIdent][$k];
					}
				}
			}
		}
	}

	/*******************************************************************************************************************
	*
	*	[group "standard" ingredients]
	*
	*******************************************************************************************************************/
	$groupedIngredients=array();
	foreach($availableIngredients as $k=>$v){
		if(!in_array($k,$remIngr)){/*remove ingredients from array if added to custom ingredients*/
			//$groupedIngredients[$v['price']][]=array('name'=>$v['name'],'price'=>$v['price'],'id'=>$k); 
			$groupedIngredients[$v['pricebeforepreselect']][]=array('name'=>$v['name'],'price'=>$v['price'],'id'=>$k); 
			/**preselected ingredients $i-> 1=whole, 2=half etc*/
			for($i=1;$i<=(int)$multiSet;$i++){
				if(isset($preSelIngr[$k])){$preSelIngrSet[$i][$k]=$k;}
			}

		}
	}

	/***************************************************************************
	*
	*	initialize session if we havent done so in the previous screen
	*	when checking for and adding "add as is" button
	*	and it's still required, (one day we should put that in a function)
	*
	****************************************************************************/
	if(!isset($doAddAsIsButton) || !$doAddAsIsButton ){	
		/***get base price for this item***/
		$meta_values_ingredients = get_post_meta($itemId,WPPIZZA_SLUG,true);
		$basePrice=$meta_values_ingredients['prices'][$sizeId];
		/***get name etc for this item***/
		$postDetails = get_post($itemId,ARRAY_A );
		/**size name**/
		/**are we hiding pricetier name if only one available ?**/
	 	if(count($meta_values_ingredients['prices'])<=1 && $optionsMaster['layout']['hide_single_pricetier']==1){
			$sizeName='';
	 	}else{
			$sizeName = $optionsMaster['sizes'][$tierId][$sizeId];
	 	}
	
		/**empty and set session for this diy item**/
		unset($_SESSION[$this->pluginSession]['diy']);
		$_SESSION[$this->pluginSession]['diy']=array('name'=>$postDetails['post_title'],'item'=>$itemId,'tier'=>$tierId,'size'=>$sizeId,'sizename'=>$sizeName['lbl'],'baseprice'=>$basePrice,'ingredients'=>array());
	}


	/************************************************************************************
	*
	*	add any preselected ingredients and respective prices
	*
	*************************************************************************************/
	$preselectPrice=0;
	for($i=1;$i<=(int)$multiSet;$i++){
		foreach($preSelIngrSet[$i] as $ingredientId=>$iVal){
			$iPrice=$availableIngredients[$ingredientId]['price'];
			$iName=$availableIngredients[$ingredientId]['name'];
			$multiId=$i;
			$preSelIngrInp[$i][$ingredientId]=''.$ingredientId.':1';/*hidden element */

			$preselectPrice+=$iPrice;
			/**add to session**/
			if($options['options']['ingredients_added_sort_by_price']){/*sorting by price first*/
				$_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId][]=array('sort'=>strtolower($iPrice),'sortname'=>strtolower($iName),'item'=>$ingredientId,'name'=>$iName,'price'=>wppizza_output_format_float($iPrice));
			}else{
				$_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId][]=array('sort'=>strtolower($iName),'item'=>$ingredientId,'name'=>$iName,'price'=>wppizza_output_format_float($iPrice));
			}
		}
	}

	/**when  using halfs and quarters, but we have ingredients that apply to whole only, add these too to the preselect session**/
	if(isset($preSelIngrSet[0]) && is_array($preSelIngrSet[0])){
	foreach($preSelIngrSet[0] as $ingredientId=>$iVal){
		$iPrice=$availableIngredients[$ingredientId]['price'];
		$iName=$availableIngredients[$ingredientId]['name'];
		$multiId=0;/*whole only=>0*/
		$preSelIngrInp[0][$ingredientId]=''.$ingredientId.':1';/*hidden element */

		$preselectPrice+=$iPrice;
		/**add to session**/
		if($options['options']['ingredients_added_sort_by_price']){/*sorting by price first*/
			$_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId][]=array('sort'=>strtolower($iPrice),'sortname'=>strtolower($iName),'item'=>$ingredientId,'name'=>$iName,'price'=>wppizza_output_format_float($iPrice));
		}else{
			$_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId][]=array('sort'=>strtolower($iName),'item'=>$ingredientId,'name'=>$iName,'price'=>wppizza_output_format_float($iPrice));
		}
	}}


	/**count groups to hide things if required**/
	$groupCount=count($groupedIngredients)+count($arrCustomGroups);
	$noIngrClass='';
	if($groupCount==0){$noIngrClass=' class="wppizza-no-ingredients" ';};
	/***********************************************************
	*
	*		[lets create the output]
	*
	***********************************************************/
	$output['body']='';
	/*************************
	[lets identify - when adding to cart - how many parts there are (quarters/halfs]
	*************************/
	$output['body'].='<input type="hidden" id="wppizza-ingr-multitype"  value="'.$multiSet.'" />';//text to check
	/*************************
	[add button]
	*************************/
	$output['head']=array();
	if(isset($addedIngrSticky)){$output['head'][]='<div id="wppizza-ingredients-'.$itemId.'" class="wppizza-ingredients wppizza-current-ingredients-sticky">';}

	$output['head'][]='<legend '.$noIngrClass.'>';
	/**if we are using a sticky header in the thickbox, we do not need the close icon and label as its in the title already*/
	if(!isset($addedIngrSticky)){
		$output['head'][]='<a id="wppizza-cart-cancel" title="'.$options['localization']['cancel_add_ingredients']['lbl'].'">[x]</a>';
		if($groupCount>0){
			$output['head'][]=''.$options['localization']['add_ingredients']['lbl'].'';
		}
	}


	$output['head'][]='<span id="wppizza-sub-info">';
	$output['head'][]='<input id="wppizza-diy-to-cart" class="btn btn-primary" type="button" value="'.$options['localization']['add_to_cart']['lbl'].'">';
	if($groupCount>0){
	$output['head'][]='<span id="wppizza-current-sum">';
	$output['head'][]=' '.$options['localization']['total']['lbl'].' '.$optionsMaster['order']['currency_symbol'].'';
	$output['head'][]='<span id="wppizza-current-total">'.wppizza_output_format_price(wppizza_output_format_float(($basePrice+$preselectPrice)),$optionsMaster['layout']['hide_decimals']).'</span>';
	$output['head'][]='</span>';
	}
	$output['head'][]='</span>';
	$output['head'][]='</legend>';

	if($groupCount>0 && $noOfAvailIngr>0){// -> AMENDED 4.3.4 in case there are textboxes only
		$output['head'][]='<div id="wppizza-ingredients-selected">';
		/*****current ingredients for whole only, start at 0****/
		if(isset($groupWholeOnly)){
			$i=0;
			$set=1;
			$mSet=1;

			$output['head'][]='<span id="wppizza-current-ingredients-'.$i.'" class="wppizza-current-ingredients">';
			if(!isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$i]) || count($_SESSION[$this->pluginSession]['diy']['ingredients'][$i])<=0){
				if($options['localization']['multi_icon_'.$mSet.'_'.$set.'']['lbl']!=''){
					$output['head'][]='<span class="wppizza-multi-icon wppizza-multi-icon-'.$set.'-'.$mSet.'">'.$options['localization']['multi_icon_'.$mSet.'_'.$set.'']['lbl'].'</span>';
				}
			}else{
				$output['head'][]='<ul>';
				asort($_SESSION[$this->pluginSession]['diy']['ingredients'][$i]);
				/**customise sort order**/
				$_SESSION[$this->pluginSession]['diy']['ingredients'][$i] = apply_filters('wppizza_filter_ingredients_custom_sort', $_SESSION[$this->pluginSession]['diy']['ingredients'][$i]);
				
				foreach($_SESSION[$this->pluginSession]['diy']['ingredients'][$i] as $iId=>$v){
					$output['head'][]='<li id="wppizza-remove-ingredient-'.$i.'-'.$iId.'-'.$mSet.'" class="wppizza-ingrli-'.$iId.'">';//id="wppizza-remove-ingredient-'.$multiId.'-'.$iId.'-'.$multiType.'"
					$output['head'][]='<span class="wppizza-remove-ingredient" title="'.$options['localization']['remove']['lbl'].'"><b>-</b></span><span class="wppizza-addedingredient-info">'.count($v).'x '.$v[0]['name'].'</span>';//'.count($ingredientGroup[$iId]).'x '.$ingredientGroup[$iId][0]['name'].'
					/**show prices next to selected ingredients, depending on ==0 or alt txt*/
					if($options['options']['ingredients_added_show_price']){
						if($v[0]['price']>0 || ($v[0]['price']==0 && !$options['options']['ingredients_added_show_price_no_zero'])){
							if($v[0]['price']==0 && $options['options']['ingredients_added_zero_price_txt']!=''){
								$setPrice=$options['options']['ingredients_added_zero_price_txt'];
							}else{
								$setPrice=''.$optionsMaster['order']['currency_symbol'].''.$v[0]['price'].'';
							}

							$output['head'][]=' <span class="wppizza-doingredient-price">'.$setPrice.'</span>';
						}
					}
					$output['head'][]='</li>';
				}
				$output['head'][]='</ul>';
			}
			$output['head'][]='</span>';
			$output['head'][]='<input type="hidden" id="wppizza-selected-ingredients-'.$i.'" class="wppizza-selected-ingredients" value="'.implode(",",$preSelIngrInp[0]).'" />';//text to check
		}

		/**normal left half /right half etc***/
		for($i=1;$i<=(int)$multiSet;$i++){
			$output['head'][]='<span id="wppizza-current-ingredients-'.$i.'" class="wppizza-current-ingredients"><span class="wppizza-multi-icon wppizza-multi-icon-'.$i.'-'.$multiSet.'">'.$options['localization']['multi_icon_'.$multiSet.'_'.$i.'']['lbl'].'</span>';
			if(!isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$i]) || count($_SESSION[$this->pluginSession]['diy']['ingredients'][$i])<=0){
				$output['head'][]='<p>'.$options['localization']['no_extra_ingredients']['lbl'].'</p>';
			}else{
				$output['head'][]='<ul>';
				asort($_SESSION[$this->pluginSession]['diy']['ingredients'][$i]);
				/**customise sort order**/
				$_SESSION[$this->pluginSession]['diy']['ingredients'][$i] = apply_filters('wppizza_filter_ingredients_custom_sort', $_SESSION[$this->pluginSession]['diy']['ingredients'][$i]);	
							
				foreach($_SESSION[$this->pluginSession]['diy']['ingredients'][$i] as $iId=>$v){
					$output['head'][]='<li id="wppizza-remove-ingredient-'.$i.'-'.$iId.'-'.$multiSet.'" class="wppizza-ingrli-'.$iId.'">';//id="wppizza-remove-ingredient-'.$multiId.'-'.$iId.'-'.$multiType.'"
					$output['head'][]='<span class="wppizza-remove-ingredient" title="'.$options['localization']['remove']['lbl'].'"><b>-</b></span><span class="wppizza-addedingredient-info">'.count($v).'x '.$v[0]['name'].'</span>';//'.count($ingredientGroup[$iId]).'x '.$ingredientGroup[$iId][0]['name'].'
					/**show prices next to selected ingredients, depending on ==0 or alt txt*/
					if($options['options']['ingredients_added_show_price']){
						if($v[0]['price']>0 || ($v[0]['price']==0 && !$options['options']['ingredients_added_show_price_no_zero'])){
							if($v[0]['price']==0 && $options['options']['ingredients_added_zero_price_txt']!=''){
								$setPrice=$options['options']['ingredients_added_zero_price_txt'];
							}else{
								$setPrice=''.$optionsMaster['order']['currency_symbol'].''.$v[0]['price'].'';
							}
							$output['head'][]=' <span class="wppizza-doingredient-price">'.$setPrice.'</span>';
						}
					}
					$output['head'][]='</li>';
				}
				$output['head'][]='</ul>';
			}
			$output['head'][]='</span>';
				$output['head'][]='<input type="hidden" id="wppizza-selected-ingredients-'.$i.'" class="wppizza-selected-ingredients" value="'.implode(",",$preSelIngrInp[$i]).'" />';//text to check
		}
		$output['head'][]='</div>';
	}

	if(isset($addedIngrSticky)){$output['head'][]='</div>';}


	/**************************
		textbox pre if set
	*************************/
	if(isset($textbox['pre'])){
		$output['body'].=$textbox['pre'];
	}
	/**************************
		ingredients whole menu item only - pre
	*************************/
	if(isset($groupWholeOnly['pre'])){
		$output['body'].='<div class="wppizza-iwhole wppizza-iwhole-pre">';
			foreach($groupWholeOnly['pre'] as $str){
				$output['body'].=$str;
			}
		$output['body'].='</div>';
	}

	if($groupCount>0){
	/***multiselect tabs***/
	$output['body'].='<ul class="wppizza-multiselect-tabs">';
	if($multiSet>1){
		for($i=1;$i<=(int)$multiSet;$i++){
		/*make the first one selected by default*/
			$classSelected='';
			if($i==1){
			$classSelected=' wppizza-multi-tab-selected';
			}
			$output['body'].='<li id="wppizza-ingrmulti-'.$i.'" class="wppizza-multi-tab'.$classSelected.'">'.$options['localization']['multi_tab_'.$multiSet.'_'.$i.'']['lbl'].'</li>';
		}
	}
	$output['body'].='</ul>';
	}

	if($groupCount>0 && $noOfAvailIngr>0){// -> AMENDED 4.3.4 in case there are textboxes only
	for($i=1;$i<=(int)$multiSet;$i++){
		/*make the first one (whole/half/quarter) visible by default*/
		$visibility='display:none;';
		if($i==1){
		$visibility='display:block;';
		}
		$iGroups[$i]='';
		$iGroups[$i].='<div id="wppizza-imulti-'.$i.'" class="wppizza-imulti" style="'.$visibility.'">';
		/*************************
		[custom groups sorted by sort displayed BEFORE standard groups]
		*************************/
		//$iGroups[$i].=$groupOutput['pre'][$i];
		foreach($groupOutput['pre'][$i] as $str){
			$iGroups[$i].=$str;
		}
		/*************************
		["standard" items grouped by price]
		*************************/
		foreach($groupedIngredients as $price=>$v){
			$iGroups[$i].='<fieldset class="wppizza-list-ingredients">';
			$iGroups[$i].='<legend>'.$options['localization']['ingredients_for']['lbl'].' '.$optionsMaster['order']['currency_symbol'].' '.wppizza_output_format_price($price,$optionsMaster['layout']['hide_decimals']).'</legend>';
				$iGroups[$i].='<ul>';
				foreach($v as $k=>$m){
					/**preselect**/
					$preSelClass='';
					$preSelCount='';
					$preSelCountLbl='';/**no label if not preselected*/
					
					if(isset($preSelIngr[$m['id']])){
						$preSelIngrSet[$i][$m['id']]=$m['id'];	
						$preSelClass='wppizza-ingredient-selected';
						$preSelCount='1x';
						/**preselected label - i.e first one free or something. only if preselected prices are zero is checked**/
						if(isset($preSelIngrPriceZero[$m['id']]) && (float)$price>0){
							$preSelCountLbl=' <span class="wppizza-doingredient-price">'.$options['localization']['preselect_prices_zero_regular']['lbl'].'</span>';
						}
						$preSelDefaultSelected[$i][$m['id']]=$m['name'];
					}
					if($options['options']['ingredients_show_count']){$ingredients_show_count='<span id="wppizza-ingredient-count-'.$i.'-'.$m['id'].'-'.$multiSet.'" class="wppizza-ingredient-count">'.$preSelCount.'</span>';}else{$ingredients_show_count='';}

					$iGroups[$i].='<li id="wppizza-ingredient-'.$i.'-'.$m['id'].'-'.$multiSet.'" class="wppizza-ingrli-'.$m['id'].'"><span class="wppizza-doingredient wppizza-ingr-4 '.$preSelClass.'" title="'.$options['localization']['add']['lbl'].'"><b>+</b></span>'.$ingredients_show_count.''.$m['name'].''.$preSelCountLbl.'</li>';
				}
				$iGroups[$i].='</ul>';
			$iGroups[$i].='</fieldset>';
		}
		/*************************
		[custom groups sorted by sort displayed AFTER standard groups]
		*************************/
		foreach($groupOutput['post'][$i] as $str){
			$iGroups[$i].=$str;
		}
		$iGroups[$i].='</div>';
	}}

	$allIGroups='';
	if($groupCount>0){
		$allIGroups=implode("",$iGroups);
	}
	/**************************
		ingredients whole menu item only.post
	*************************/
	if(isset($groupWholeOnly['post'])){
		$allIGroups.='<div class="wppizza-iwhole wppizza-iwhole-post">';
			foreach($groupWholeOnly['post'] as $str){
				$allIGroups.=$str;
			}
		$allIGroups.='</div>';
	}
	/*textbox if set*/
	if(isset($textbox['post'])){
		$allIGroups.=$textbox['post'];
	}



	/***add a bunch of filters for people to use***/
	$output['head'] = apply_filters('wppizza_add_ingredients_filter_head', $output['head'],$itemId,$tierId,$sizeId);
	$output['body'] = apply_filters('wppizza_add_ingredients_filter_body', $output['body']);


	$beforeForm='';
	$beforeForm = apply_filters('wppizza_add_ingredients_filter_before_form', $beforeForm, $labels);
	$afterForm='';
	$afterForm = apply_filters('wppizza_add_ingredients_filter_before_form', $afterForm, $labels);
		
	$formStart='';
	$formStart = apply_filters('wppizza_add_ingredients_filter_form_start', $formStart, $labels);
	$formEnd='';
	$formEnd = apply_filters('wppizza_add_ingredients_filter_form_end', $formEnd, $labels);	
	
	$beforeGroups='';
	$beforeGroups = apply_filters('wppizza_add_ingredients_filter_before_groups', $beforeGroups, $labels);	
	$afterGroups='';
	$afterGroups = apply_filters('wppizza_add_ingredients_filter_after_groups', $afterGroups, $labels);	/**same as $formEnd really**/
	

/***header and body, depending on js popup sticky**/
if(isset($addedIngrSticky)){
	$output['body']="".$beforeForm."<form id='wppizza-ingr-form'>".$formStart.$output['body'].$beforeGroups.$allIGroups.$afterGroups.$formEnd."</form>".$afterForm."";/*set to sticky, omit head*/
}else{
	$output['body']="".$beforeForm."<form id='wppizza-ingr-form'>".$formStart.implode('',$output['head']).$output['body'].$beforeGroups.$allIGroups.$afterGroups.$formEnd."</form>".$afterForm."";
}

if(isset($addedIngrSticky)){
	$output['head']=implode('',$output['head']);
}else{
	$output['head']="";
}


/**add all preselected to session so we can show them later as "NO something" (if enabled)**/
if($options['options']['ingredients_show_depreselected']){
	$_SESSION[$this->pluginSession]['diy']['preselected']=$preSelDefaultSelected;
}

print"".json_encode($output)."";
exit();
}
/*****************************************************************************
*
*
*	[add/remove ingredients to item]
*
*
*****************************************************************************/
if(isset($_POST['vars']['type']) && ($_POST['vars']['type']=='addingredient' || $_POST['vars']['type']=='removeingredient') && (int)$_POST['vars']['item']>=0){
header('Content-type: application/json');
	/**menue item id**/
	$postId=(int)$_POST['vars']['postId'];
	/**id of ingredient*/
	$ingredientId=(int)$_POST['vars']['item'];
	/**are we adding or removing ?*/
	$addOrRemove=$_POST['vars']['type'];
	/**groupId*/
	$groupId=$_POST['vars']['groupId']; /*either >=0 if it's a custom group or '' if not*/
	/**multiId*/
	$multiId=(int)$_POST['vars']['multiId'];
	/**multiType*/
	$multiType=(int)$_POST['vars']['multiType']; /*1-whole; 1 or 2 ->halfs, 1 or 2 or 3 or 4 ->quarters*/

	/**price multiplier (percentage of whole price)*/
	$priceMultiply=1;
	$priceMultiplyWhole=1;
	$meta_values_devide = get_post_meta($postId,$this->pluginMetaMultiDivide,true);
	if(isset($meta_values_devide[$this->pluginMetaMultiDivide])){
		$multiDivide=$meta_values_devide[$this->pluginMetaMultiDivide];
			if(isset($multiDivide[$multiType])){
				$priceMultiply=$multiDivide[$multiType]/100;
			}
	/**multiplier for ingredients set for "whole" item*/
	$priceMultiplyWhole=$multiDivide[1]/100;
	}



	/**get group type -> returns
			0/'' = default
			1 = only one required and allowed - and only one times ->radio
			2 = only one required and allowed - but can be selected more than one times
			3 = at least one required but multiple allowed but only 1x per ingredient (checkbox)
			4 = at least one required but multiple allowed and multiple per ingredient
			5 = exclude ingredient(s)
			6 = preselect ingredient(s)
	**/
	/**get multiId -> returns (zero indexed)
			0 = if ingredients can only be selected for a whole menu items
			1 = if ingredients can only be selected individually for 2 halfs
			2 = if ingredients can only be selected individually for 3 thirds
			3 = if ingredients can only be selected individually for 4 quarters
	**/

	/**********************************************
	*
	*	[select classes on add]
	*
	**********************************************/

	/**********************************************
		[Normal Non-Customised Groups when adding]
	**********************************************/
	if($groupId=='' && 	$addOrRemove=='addingredient'){
		$output['cssDefaultSelect']=''.$multiId.'-'.$ingredientId.'-'.$multiType.'';
	}
	/**********************************************
		[Customised Groups when adding]
	**********************************************/
	if($groupId!='' && 	$addOrRemove=='addingredient'){

		$groupType=$options['ingredients_custom_groups'][$groupId]['type'];
		$groupVars=$options['ingredients_custom_groups'][$groupId];
		$groupIngredients=$options['ingredients_custom_groups'][$groupId]['ingredient'];
		/*0 default*/
		if($groupType==0){
			$output['cssSelect'][$ingredientId]=array('groupId'=>$groupId,'groupType'=>$groupType);
		}
		/*1 = only one required and allowed - and only one times ->radio*/
		if($groupType==1){
			foreach($groupIngredients as $k=>$iId){
				/**UNSET ALL PREVIOUSLY SELECTED INGREDIENTS OF THAT GROUP*/
				if(isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$iId])){
					unset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$iId]);
				}
				$output['cssDeselect'][$iId]=array('groupId'=>$groupId,'groupType'=>$groupType);
			}
				$output['cssSelect'][$ingredientId]=array('groupId'=>$groupId,'groupType'=>$groupType);
		}
		/*2 = only one required and allowed - but can be selected more than one times	*/
		if($groupType==2){
			foreach($groupIngredients as $k=>$iId){
				/**unset all previously selected ingredients of that group that OR NOT THIS ingredient*/
				if($iId!=$ingredientId){
					if(isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$iId])){
						unset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$iId]);
					}
					$output['cssDeselect'][$iId]=array('groupId'=>$groupId,'groupType'=>$groupType);
				}
			}
			$output['cssSelect'][$ingredientId]=array('groupId'=>$groupId,'groupType'=>$groupType);
		}
		/*3 = at least one required but multiple allowed but only 1x per ingredient (checkbox)*/
		if($groupType==3){
			/**as we only want one and it gets added below, unset all of this ingredient first**/
			if(isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId])){
				unset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId]);
			}
			$output['cssSelect'][$ingredientId]=array('groupId'=>$groupId,'groupType'=>$groupType);
			$output['cssCheckSelect'][$ingredientId]=array('groupId'=>$groupId,'groupType'=>$groupType);
		}
		/*4 = at least one required but multiple allowed and multiple per ingredient*/
		if($groupType==4){
		/**no checking necesssary here. will be checked when adding to cart via js**/
			$output['cssSelect'][$ingredientId]=array('groupId'=>$groupId,'groupType'=>$groupType);
		}
	}
	/**********************************************
	*
	*	[deselect classes on remove]
	*
	**********************************************/
	if($addOrRemove=='removeingredient'){

		/**********************************************
			[Customised Groups when removing]
		**********************************************/

		/**get the custom groups associated with this menu item*/
		if(isset($options['ingredients_custom_groups']) && is_array($options['ingredients_custom_groups'])){
			$groupType=array();
			foreach($options['ingredients_custom_groups'] as $k=>$v){
				if(is_array($v['item']) && isset($v['item'][$postId]) && is_array($v['ingredient']) && in_array($ingredientId,$v['ingredient']) && $v['type']!=6){/**do not use exclude group (id 6) here or the js wont  deselect via css class**/
					$groupType=array('groupId'=>$k,'groupType'=>$v['type']);
					break;
				}
			}
		}
		if(isset($groupType['groupId'])){
			/*1 = only one required and allowed - and only one times ->radio*/
			/*3 = at least one required but multiple allowed but only 1x per ingredient (checkbox)*/
			if($groupType['groupType']==1 || $groupType['groupType']==3){
				$output['cssDeselect'][$ingredientId]=array('groupId'=>$groupType['groupId'],'groupType'=>$groupType['groupType']);
				$output['cssCheckDeselect'][$ingredientId]=array('groupId'=>$groupType['groupId'],'groupType'=>$groupType['groupType']);
			}
			/*2 = only one required and allowed - but can be selected more than one times	*/
			/*4 = at least one required but multiple allowed and multiple per ingredient*/
			/*note:as we will be removing this ingredient further down, we'll check for 1 instead of zero as in this place, it still exists*/
			/*lets also use isset, incase of double clicks on the frontend to avoid php notices*/
			if(($groupType['groupType']==0 || $groupType['groupType']==2 || $groupType['groupType']==4) && isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId]) && count($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId])==1 ){
				$output['cssDeselect'][$ingredientId]=array('groupId'=>$groupType['groupId'],'groupType'=>$groupType['groupType']);
			}
		}
		/*note:as we will be removing this ingredient further down, we'll check for 1 instead of zero as in this place, it still exists*/
		if($groupId=='' && isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId]) && count($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId])==1){
			$output['cssDefaultRestore']=''.$multiId.'-'.$ingredientId.'-'.$multiType.'';
		}
	}

	/**name of ingredient**/
	$iName=$options['ingredients'][$ingredientId]['item'];
	/**get price again depending on size**/
	$selectedSize=$_SESSION[$this->pluginSession]['diy']['size'];

	/*******************************************************
		if ingredient is in group that has "apply to whole menu" set
		make an array of these as the multiplier should be %
		set for "whole" item
	********************************************************/
	$doMultiply=$priceMultiply;	/*ini*/
	if(isset($options['ingredients_custom_groups']) && is_array($options['ingredients_custom_groups'])){
		foreach($options['ingredients_custom_groups'] as $kGroup=>$cGroup){
			if(isset($cGroup['item'][$postId]) && count($cGroup['ingredient'])>0 && $cGroup['type']<5 && $cGroup['whole_only']==1 && isset($cGroup['ingredient'][$ingredientId])){
				$doMultiply=$priceMultiplyWhole;
				break;
			}
		}
		
		/*********
			if ingredient is in preselect group and initial prices are set to 0 
			Any *first* one of this ingredient should have a price of 0 (can happen if user is allowed
			to also totally deselect a preselected ingredient and then wants to reselect it 
		****/
		foreach($options['ingredients_custom_groups'] as $kGroup=>$cGroup){
			if(isset($cGroup['item'][$postId]) && count($cGroup['ingredient'])>0 && $cGroup['type']==6 && $cGroup['preselpricezero'] && isset($cGroup['ingredient'][$ingredientId])){
				/**check how many have been added already**/
				$numberOfThisIngrSelected=count($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId]);
				if($numberOfThisIngrSelected==0){/**if its the first one (again) the price should be 0 (again)*/
					$options['ingredients'][$ingredientId]['prices'][$selectedSize]=0;
				}
			}
		}
	}

	$iPrice=round($options['ingredients'][$ingredientId]['prices'][$selectedSize]*$doMultiply,2);

	/**add ingredient to session item if addingredient**/
	if($addOrRemove=='addingredient'){
		if($options['options']['ingredients_added_sort_by_price']){/*sorting by price first*/
			$_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId][]=array('sort'=>$iPrice,'sortname'=>strtolower($iName),'item'=>$ingredientId,'name'=>$iName,'price'=>wppizza_output_format_float($iPrice));//,'multiId'=>$multiId
		}else{
			$_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId][]=array('sort'=>strtolower($iName),'item'=>$ingredientId,'name'=>$iName,'price'=>wppizza_output_format_float($iPrice));//,'multiId'=>$multiId
		}
	}

	asort($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId]);
	/**customise sort order**/
	$_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId] = apply_filters('wppizza_filter_ingredients_custom_sort', $_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId]);
	
	
	

	/**remove one ingredient (the last one added if more that one of tha same) from session item if removeingredient**/
	if($addOrRemove=='removeingredient'){
		if(isset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId])){
			end($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId]);
			$last_key = key($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId]);
			unset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId][$last_key]);
			/*if there are 0x this ingredient, unset completely**/
			if(count($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId])==0){
				unset($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$ingredientId]);
			}
		}
	}

	/****calculate price (baseprice+ingredient) and group to output********/
	$output['total']=$_SESSION[$this->pluginSession]['diy']['baseprice'];
	/**current ingredients group**/
	$ingredientGroup=array();
	foreach($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId] as $iId=>$v){
		$ingredientGroup[$iId]=array();
		foreach($v as $k=>$m){
			$ingredientGroup[$iId][]=array('name'=>$m['name'],'price'=>$m['price']);
		}
	}
	/**calculate total for all ingredients groups**/
	foreach($_SESSION[$this->pluginSession]['diy']['ingredients'] as $mId=>$g){
		foreach($g as $iId=>$v){
			foreach($v as $k=>$m){
				$output['total']+=$m['price'];
			}
		}
	}
	/**make sure we are never negative, even if negative priced ingredients have been added**/
	if($output['total']<0){
		$output['total']=0;
	}


	/*format price*/
	$output['total']=wppizza_output_format_price(wppizza_output_format_float($output['total']),$optionsMaster['layout']['hide_decimals']);


	/*group items**/
	$output['ingredients']='';
	$selectedIngredients=array();
	if(count($ingredientGroup)>0){
		/***if it can only be applied to whole menu item, set icon ident to 1**/
		$iconIdentType=$multiType;
		$iconIdentId=$multiId;
		if($multiId==0){
			$iconIdentType=1;
			$iconIdentId=1;
		}

		/*label in front of group*/
		$output['ingredients'].='<span class="wppizza-multi-icon wppizza-multi-icon-'.$multiType.'-'.$multiId.'">'.$options['localization']['multi_icon_'.$iconIdentType.'_'.$iconIdentId.'']['lbl'].'</span>';
		$output['ingredients'].='<ul>';
			$i=0;
			foreach($ingredientGroup as $iId=>$v){
				$output['ingredients'].='<li id="wppizza-remove-ingredient-'.$multiId.'-'.$iId.'-'.$multiType.'" class="wppizza-ingrli-'.$iId.'">';
				$output['ingredients'].='<span class="wppizza-remove-ingredient" title="'.$options['localization']['remove']['lbl'].'"><b>-</b></span><span class="wppizza-addedingredient-info">'.count($ingredientGroup[$iId]).'x '.$ingredientGroup[$iId][0]['name'].'</span>';
				/**show prices next to selected ingredients, depending on ==0 or alt txt*/
				if($options['options']['ingredients_added_show_price']){
					if($ingredientGroup[$iId][0]['price']>0 || ($ingredientGroup[$iId][0]['price']==0 && !$options['options']['ingredients_added_show_price_no_zero'])){
						if($ingredientGroup[$iId][0]['price']==0 && $options['options']['ingredients_added_zero_price_txt']!=''){
							$setPrice=$options['options']['ingredients_added_zero_price_txt'];
						}else{
							$setPrice=''.$optionsMaster['order']['currency_symbol'].''.$ingredientGroup[$iId][0]['price'].'';
						}
						$output['ingredients'].=' <span class="wppizza-doingredient-price">'.$setPrice.'</span>';
					}
				}
				$output['ingredients'].='</li>';
				$selectedIngredients[]=''.(int)$iId.':'.count($_SESSION[$this->pluginSession]['diy']['ingredients'][$multiId][$iId]).'';
			$i++;
			}
		$output['ingredients'].='</ul>';
	}else{
		/**all ingredients have been removed (or none have been selected yet) for this half/quarter etc*/
		$output['ingredients'].=''.$options['localization']['multi_icon_'.$iconIdentType.'_'.$iconIdentId.'']['lbl'].''.$options['localization']['no_extra_ingredients']['lbl'].'';
	}

	$output['selectedingredients']=''.implode(",",$selectedIngredients).'';

print"".json_encode($output)."";
}
/*****************************************************************************
*
*
*	[we are done adding and removing - add item to main session]
*
*
*****************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='diy-to-cart'){
header('Content-type: application/json');
	/**get / set some vars**/
	$diyItem=$_SESSION[$this->pluginSession]['diy'];
	
	/** show de-preselected**/
	if($options['options']['ingredients_show_depreselected']){
		$diyItemPreselected=$diyItem['preselected'];
		$preDeSelectLoc='pre';
		if($options['options']['ingredients_show_depreselected_after']){
			$preDeSelectLoc='post';
		}
	}
	
	$output['hasingredients']=1;//by default we assume ingredients have been added
	$output['id']=$diyItem['item'];/*nedded to make trigger in js*/
	$output['tier']=$diyItem['tier'];/*nedded to make trigger in js*/
	$output['size']=$diyItem['size'];/*nedded to make trigger in js*/
	/***/

	/**
		multiType - how many parts are there (halfs,quarters ?)
		lets also make sure this is actually enabled otherwise set to 1
		(in case somone wants to mess with the hidden input field
		if they do mess around, they still get charged the right/same amount
		just do not end up with what they wanted. their own fault really)
	**/
	$multiType=(int)$_POST['vars']['multiType'];
	$meta_values_multi = get_post_meta($diyItem['item'],$this->pluginMetaMulti,true);
	$multiIngredients=$meta_values_multi['add_ingredients_multi'];
	if(!in_array($multiType,$multiIngredients)){
		$multiType=1;
	}


/**************************serverside check**************************************************
*	[js/clientside ckecking is fine for immediate feedback / usability, but...........:
*
*	the following will only ever happen if someone has tampered with the frontend
*	hidden input html fields to override/bypass the js checking.
*	if so we just fail silently, and output a
*	"get lost" error to browser console.
*
******************************************************************************************/
$serverSideCheck='Y';/**if we want to bypass the server side check, comment out this line**/

if(isset($serverSideCheck)){
	$ingrExcluded=array();
	$cGroups=$options['ingredients_custom_groups'];
	/**ini error/cheating message to be displayed in console (if any)*/
	$errorIngr=array();
	/*get all set custom groups for this menu item **/
	$ingrCustomGroups=array();
	foreach($cGroups as $k=>$v){
		/**lets also add the custom group id to the extend data of any given ingredient. might be useful somewhere*/
		foreach($v['ingredient'] as $iId){
			$ingrCustomGroupId[$iId]=$k;
		}

		/**make sure this goup applies to this menu item. only group types>=1 and <=5 (i.e not default group type or preselect, but DO include exclude groups) **/
		if(isset($v['item'][$diyItem['item']]) && $v['type']>0 && $v['type']<=5){
				$ingrCustomGroups[$k]=$v;
				/******
					nonsensically, a group may have been created with certain ingredients, only to have all of them excluded again in another group
					if that is the case exclude this group entirely from validation as it doesnt exist on the frontend to start off with
				*******/
				if($v['type']==5){
					$ingrExcluded+=$v['ingredient'];	
				}				
		}		
	}

	/****get all the ingredients selected in each group***/
	/*creates array[set][ingrId]=no_of_times_selected*/
	$selIngrSet=array();
	foreach($_SESSION[$this->pluginSession]['diy']['ingredients'] as $multiSet=>$ingrSet){
		$selIngrSet[$multiSet]=array();
		foreach($ingrSet as $ingrId=>$ingr){
			$selIngrSet[$multiSet][$ingrId]=count($ingr);
		}
	}


	/**check ingredients per group **/
	$grpId=0;
	foreach($ingrCustomGroups as $k=>$v){
		$groupCheck[$k]=array();
		/*if it's whole only we do not need to loop over all halfs, quarters etc but only one single time**/
		$loopCount=$multiType;
		if($v['whole_only']){$loopCount=1;}

		/*initially set group selection as invalid*/
		$groupInvalid[$grpId]=true;


		/******************************************************
			if - nonsensically but possible - all items of this group have been excluded again, set initially as valid 
			as nothing needs to be checked that is not selectable to start off with 
			however, we DO also check further down again if an additional ingredient has been (fraudulently) selected that was excluded
		******************************************************/
		$ingrDiff=array_diff($v['ingredient'],$ingrExcluded);
		if(count($ingrDiff)==0){
			$groupInvalid[$grpId]=false;	
		}

		/**loop over all halfs, quarters (or just one single time if whole only)*/
		for($i=1;$i<=(int)$loopCount;$i++){

			/**************************************************************************************
			*
			*	[only if a GROUP has been set to 'whole only' AND  half or quarter has been selected, it has an ident of 0]
			*	[if GROUP has been set to whole only AND  ingredients on everything has been selected, it has a normal ident of 1 ]
			*	[ingredients on everything has ident of 1; ingredients on halfs 1 and 2; on  quarters 1,2,3,4]
			*
			*************************************************************************************/
			if($v['whole_only'] && $multiType>1){$multiIdent=0;}else{$multiIdent=$i;}

				/***check coustom group types 1 - 4 *******/
				if($v['type']>=1 && $v['type']<5){
					/*any groups (types >0 and <5) that  MUST have at least one ingredient selected, check here*/
					foreach($v['ingredient'] as $l=>$m){
						if(isset($selIngrSet[$multiIdent][$l])){
							$groupInvalid[$grpId]=false;/*group has at least one ingredient selected, good*/
							$groupCheck[$k][$multiIdent][$l]=$selIngrSet[$multiIdent][$l];/*group has at least one selected*/

							/*check that we do not have an ingredient more times than allowed*/
							if($v['max_same_ing']>0 && $groupCheck[$k][$multiIdent][$l]>$v['max_same_ing']){
								$groupInvalid[$grpId]=true;/*an ingredient has been selected more than the allowed max number of times. not good*/
								/*error msg*/
								$errorIngr[]='error 1 ['.$v['type'].'|'.$v['max_same_ing'].']';
								break;
							}
						}
					}
					/*some groups (type 3 and 4) allow to set min_ing to 0 ,if so they will be valid regarding min select**/
					/*therefore , if no ingredient has been selected its definitely valid overriding whatever we check for above*/
					if($v['min_ing']==0 && count($groupCheck[$k][$multiIdent])==0){
						$groupInvalid[$grpId]=false;
					}

					/*check that we do not have more than the allowed number  but at least the required of different ingredients in this group**/
					if($v['max_ing']>0 && (count($groupCheck[$k][$multiIdent])>$v['max_ing'] || count($groupCheck[$k][$multiIdent])<$v['min_ing'] )){
						$groupInvalid[$grpId]=true;/*more than the allowed number of different ingredients has been selected or the minimum required has not been reached. not good*/
						/*error msg*/
						$errorIngr[]='error 2 ['.$v['type'].'|'.$v['max_ing'].']';
					}

					/*check that we do not have more than the allowed total SUM  of all selected different ingredients in this group**/
					if($v['max_total_ing']>0 && array_sum($groupCheck[$k][$multiIdent])>$v['max_total_ing']){
						$groupInvalid[$grpId]=true;/*more than the allowed total SUM  of all selected different ingredients in this group. not good*/

						/*error msg*/
						$errorIngr[]='error 2.1 ['.$v['type'].'|'.$v['max_ing'].'|'.$v['max_total_ing'].' '.print_r($groupCheck,true).' | '.array_sum($groupCheck[$k][$multiIdent]).']';
					}

					/*check that have the min total SUM  of all selected different ingredients in this group**/
					if($v['min_total_ing']>0 && array_sum($groupCheck[$k][$multiIdent])<$v['min_total_ing']){
						$groupInvalid[$grpId]=true;/*more than the allowed total SUM  of all selected different ingredients in this group. not good*/

						/*error msg*/
						$errorIngr[]='error 2.2 ['.$v['type'].'|'.$v['max_ing'].'|'.$v['min_total_ing'].' '.print_r($groupCheck,true).' | '.array_sum($groupCheck[$k][$multiIdent]).']';
					}

				}
				/***check coustom group type 5 (exclude) *******/
				if($v['type']==5){
					/**if it's an exclude group, set it to valid initially**/
					$groupInvalid[$grpId]=false;
					foreach($v['ingredient'] as $l=>$m){
						if(isset($selIngrSet[$multiIdent][$l])){
							$groupInvalid[$grpId]=true;/*it appears an ingredient has been selected that was excluded from selection . not good*/
							/*error msg*/
							$errorIngr[]='error 3 ['.$v['type'].']';
							break;
						}
					}
				}
				/*if the group selection is (still) invalid, stop right here and just fail silently outputting message to console as this can/should only happen if someone tampered with the html*/
				if($groupInvalid[$grpId]){
					$output['groupInvalid']=__('cheating , are we ?', $this->pluginLocale).PHP_EOL.implode(PHP_EOL,$errorIngr);
					print"".json_encode($output)."";
					exit();
				}
		}
	$grpId++;
	}
}
/**************************serverside check end********************************************
*
*	[all is well, let's carry on:]
*
******************************************************************************************/

	/**if we have not added any ingredients, we just add it to the plain master session by triggering*/
	if(count($diyItem['ingredients'])==0){
		$output['hasingredients']=0;
	}

	/*make id**/
	/*as with all others, make it groupable by creating a unique key for all items that have the same id and size and ingredients*/
	$groupId=$diyItem['item'].'.'.$diyItem['size'].'';
	/**now extend the groupkey by adding the keys and counts of added ingredients**/
	foreach($diyItem['ingredients'] as $mId=>$g){
		foreach($g as $iId=>$v){
			$groupId.='|'.$mId.'.'.$iId.'.'.count($v).'';
		}
	}

	/****calculate price (baseprice+ingredient) and group to output********/
	$totalPrice=$diyItem['baseprice'];
	/**calculate total for all ingredients groups**/
	$ingredientGroup=array();
	foreach($diyItem['ingredients'] as $mId=>$g){
		foreach($g as $iId=>$v){
			foreach($v as $k=>$m){
				$ingredientGroup[$mId][$iId][]=array('name'=>$m['name'],'id'=>$m['item'],'price'=>$m['price']);
				$totalPrice+=$m['price'];
			}
		}
	}
	/**make sure we are never negative, even if negative priced ingredients have been added**/
	if($totalPrice<0){
		$totalPrice=0;
	}

	/*group items**/
	$additionalInfo=array();
	$iGroupCount=count($ingredientGroup);
	$groupIngredients=array();
	if($iGroupCount>0){
		$i=1;
		foreach($ingredientGroup as $gId=>$iArr){
			$additionalInfo[$gId]='';/*initialize string*/			
			$additionalInfoData[$gId]=array();/*initialize array*/
			/**get ingredients in group***/
			if(is_array($iArr)){
				foreach($iArr as $k=>$v){
					$thisIngCount=count($v);
					$thisIngDisplay=''.$thisIngCount.'x ';
					/*omit "1x" if single ingredient and enabled**/
					if($thisIngCount==1 && $options['options']['ingredients_omit_single_count']){
						$thisIngDisplay='';
					}
					$groupIngredients[$gId][]=''.$thisIngDisplay.''.$v[0]['name'].'';
					/*store the data of ingredient*/
					$additionalInfoData[$gId][$k]['count']=''.$thisIngCount.'';
					$additionalInfoData[$gId][$k]['id']=''.$v[0]['id'].'';
					$additionalInfoData[$gId][$k]['name']=''.$v[0]['name'].'';
					$additionalInfoData[$gId][$k]['price']=''.$v[0]['price'].'';
					$additionalInfoData[$gId][$k]['pricetotal']=$v[0]['price']*$thisIngCount;
					$additionalInfoData[$gId][$k]['customgroupid']=!isset($ingrCustomGroupId[$k]) ? '' : $ingrCustomGroupId[$k] ;
				}
				$additionalInfo[$gId].=implode(", ",$groupIngredients[$gId]);
			}
			
		$i++;
		}
	}

	/**********************************************************************************************************
	*
	*	[add de-preselected if enabled]
	*	lets put this de preselected thing in its own loop, rather then potentially breaking things elswehere
	*
	**********************************************************************************************************/
	if($options['options']['ingredients_show_depreselected'] && is_array($diyItemPreselected) && count($diyItemPreselected)>0){
		$additionalInfoPreDeselect=array();/*initialize array*/
		$additionalInfo=array();
		foreach($diyItemPreselected as $gId=>$iArr){		
			$groupedIngredients=array();
			$additionalInfoPreDeselect[$gId]=array();/*initialize array*/
			$additionalInfo[$gId]='';/*initialize new string*/
				if(is_array($iArr)){
					foreach($diyItemPreselected[$gId] as $deSelId=>$deSelName){
						if(!isset($additionalInfoData[$gId][$deSelId])){
							$additionalInfoPreDeselect[$gId][]=''.$options['options']['ingredients_show_depreselected_prefix'].' '.$deSelName.'';
						}
					}
					//if(count($groupIngredients[$gId])>0 || count($additionalInfoPreDeselect[$gId])>0 ){
					/**just to make array_merge happy**/
					if(!isset($groupIngredients[$gId]) || !is_array($groupIngredients[$gId])){$groupIngredients[$gId]=array();}
					if(!isset($additionalInfoPreDeselect[$gId]) || !is_array($additionalInfoPreDeselect[$gId])){$additionalInfoPreDeselect[$gId]=array();}
						asort($additionalInfoPreDeselect[$gId]);
						if($preDeSelectLoc=='pre'){/**pre all others**/
							$groupedIngredients=array_merge($additionalInfoPreDeselect[$gId],$groupIngredients[$gId]);
						}else{/**after all others**/
							$groupedIngredients=array_merge($groupIngredients[$gId],$additionalInfoPreDeselect[$gId]);	
						}
					//}
				$additionalInfo[$gId].=implode(", ",$groupedIngredients);
			}		
		}
	}

	/**make tha additional info array depending on how many parts there should be (1,2 or 4), adding '--' for empty vals */
	$multiInfoGrp=array();
	$multiInfoGrpDetails=array();
	if(count($additionalInfo)>0){/*no point of printing anything if no ingredients have been selected anywhere*/
	
		/**if there's a group 0 (i.e applied to whole item only, add it but set icon type to 1**/
		$setI=1;
		if(isset($additionalInfo[0])){
			$setI=0;
		}
	
		for($i=$setI;$i<=(int)$multiType;$i++){
			if($i==0 && isset($additionalInfo[$i])){
				$multiInfoGrp['wholeonly']=$additionalInfo[$i];
				$multiInfoGrpDetails['wholeonly']=$additionalInfoData[$i];
			}else{
				/**add icon for first half, second half etc if more than one*/
				$multiInfoGrp['multi'][$i]='';
				$multiInfoGrpDetails['multi'][$i]='';
				if(isset($additionalInfo[$i])){
					$multiInfoGrp['multi'][$i].=$additionalInfo[$i];
					$multiInfoGrpDetails['multi'][$i][]=$additionalInfoData[$i];
				}
			}
		}
	}

	/**add any textarea input**/
	/*****************************************
		[get and parse all post variables]
	*****************************************/
	$limitTxt=128;/*ought to be enough really*/
	$data = array();
	parse_str($_POST['vars']['data'], $data);
	/***now get the comment boxes post data (anything put into additional info will automatically be sanitized)****/
	$txtBox='';
	if(isset($data['wppizza-ingredients-textarea']) && count($data['wppizza-ingredients-textarea'])>0){
		$customTxtBox=$data['wppizza-ingredients-textarea'];
		$txtBox=implode(" ",$customTxtBox);
		$txtBox=trim(str_replace(PHP_EOL,' ',$txtBox));

	}

	/*****************************************
		[add comments to additional info]
	*****************************************/
	if(!isset($i)){$i=1;}
	if($txtBox!=''){
			$multiInfoGrp['textbox']=''.trim($txtBox).'';
			$multiInfoGrpDetails['textbox']=''.trim($txtBox).'';
	}
	/**add md5(txt) as groupid so different comments get stored as different groups**/
	$addGroupId='';
	if($txtBox!=''){
		$addGroupId=".".md5($txtBox);
	}

	/**********************************************************
		[if no add ingr where added, set extend to empty array]
		[mainly used when a "must have at least..." group was set to
		"Minimum number of different ingredients..."=0
		as we will otherwise just get an empty array displayed as "array"]
	**********************************************************/
	$setExtend=array();
	$setExtendData=array();
	$setExtend['addingredients']=$multiInfoGrp;
	$setExtendData['addingredients']=$multiInfoGrpDetails;/**detailed array of ingredients (id, count etc) that have been added */
	if(count($multiInfoGrp)==0){
		$setExtend=array();
		$setExtendData=array();
	}

	/***************************************
		[now we add this to the master session]
	*****************************************/
	$_SESSION[$this->pluginSession]['items'][$groupId.$addGroupId][]=array('sortname'=>strtolower($diyItem['name']),'size'=>$diyItem['size'],'price'=>$totalPrice,'sizename'=>$diyItem['sizename'],'printname'=>$diyItem['name'],'id'=>$diyItem['item'],'extend'=>$setExtend,'extenddata'=>$setExtendData);//changed $multiInfoGroup to $multiInfoGrp


	/******unset this temporary session var******/
	unset($_SESSION[$this->pluginSession]['diy']);

print"".json_encode($output)."";
}
?>