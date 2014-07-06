<?php
	function wppizza_output_format_float($str,$type='price'){
		if($type=='price'){
			//$str=number_format_i18n($str,2);
			$str=sprintf('%01.2f',$str);
		}
		/**when hiding decimals $type==true**/
		if($type=='hidedecimals'){
			$str=sprintf('%01.0f',$str);
		}
		
		return $str;
	}

	function wppizza_output_format_price($str,$hideDecimals=false){
		if(trim($str)!=''){

			/*lagacy reasons when already using wppizza_output_format_price in customised cart template**/
			$sstr=substr($str,-3,1);
			if($sstr==','){
				$str=str_replace(array(',','.'),'',$str);
				$str = substr_replace($str, '.', -2, 0);
			}
			/***************************************************/

			if($hideDecimals){
				$str=number_format_i18n($str,0);
			}else{
				$str=number_format_i18n($str,2);
			}
		}
		return $str;
	}
	function wppizza_wordwrap_indent($str){
		$str=str_replace(PHP_EOL,"".PHP_EOL."     ",$str);
		return $str;
	}

	/**default order status*/
	function wppizza_order_status_default(){
		$orderStatus=array('NEW','ACKNOWLEDGED','ON_HOLD','PROCESSED','DELIVERED','REJECTED','REFUNDED','OTHER');
		return $orderStatus;
	}
	/**allow filtering of default**/
	function wppizza_custom_order_status(){
		$orderStatus=wppizza_order_status_default();
		$orderStatus= apply_filters('wppizza_filter_order_status', $orderStatus);
		$setOrderStatus=array();
		foreach($orderStatus as $oStatus){
		$setOrderStatus[]=wppizza_validate_alpha_only(str_replace(" ","_",strtoupper($oStatus)));
		}
		$newOrderStatus=array_unique($setOrderStatus);

		return $newOrderStatus;
	}

	function wppizza_currencies($selected='',$returnValue=null){
		$items['---none---']='';
		$items['USD']='$';
		$items['GBP']='£';
		$items['EUR']='€';
		$items['CAD']='$';
		$items['CHF']='CHF';
		$items['ALL']='Lek';
		$items['AFN']='&#1547;';
		$items['ARS']='$';
		$items['AWG']='ƒ';
		$items['AUD']='$';
		$items['AZN']='&#1084;';
		$items['BSD']='$';
		$items['BBD']='$';
		$items['BYR']='p.';
		$items['BZD']='BZ$';
		$items['BMD']='$';
		$items['BOB']='$b';
		$items['BAM']='KM';
		$items['BWP']='P';
		$items['BGN']='&#1083;&#1074;';
		$items['BRL']='R$';
		$items['BND']='$';
		$items['KHR']='&#6107;';
		$items['KYD']='$';
		$items['CLP']='$';
		$items['CNY']='¥';
		$items['RMB']='¥';
		$items['COP']='$';
		$items['CRC']='¢';
		$items['HRK']='kn';
		$items['CUP']='&#8369;';
		$items['CZK']='Kc';
		$items['DKK']='kr';
		$items['DOP']='RD$';
		$items['XCD']='$';
		$items['EGP']='£';
		$items['SVC']='$';
		$items['EEK']='kr';
		$items['FKP']='£';
		$items['FJD']='$';
		$items['GHC']='¢';
		$items['GIP']='£';
		$items['GTQ']='Q';
		$items['GGP']='£';
		$items['GYD']='$';
		$items['HNL']='L';
		$items['HKD']='$';
		$items['HUF']='Ft';
		$items['ISK']='kr';
		$items['IDR']='Rp';
		$items['IRR']='&#65020;';
		$items['IMP']='£';
		$items['ILS']='&#8362;';
		$items['JMD']='J$';
		$items['JPY']='¥';
		$items['JEP']='£';
		$items['KZT']='&#1083;';
		$items['KPW']='&#8361;';
		$items['KRW']='&#8361;';
		$items['KGS']='&#1083;';
		$items['LAK']='&#8365;';
		$items['LVL']='Ls';
		$items['LBP']='£';
		$items['LRD']='$';
		$items['LTL']='Lt';
		$items['MKD']='&#1076;';
		$items['MYR']='&#82;';
		$items['MUR']='&#8360;';
		$items['MXN']='$';
		$items['MNT']='&#8366;';
		$items['MZN']='MT';
		$items['NAD']='$';
		$items['NPR']='&#8360;';
		$items['ANG']='ƒ';
		$items['NZD']='$';
		$items['NIO']='C$';
		$items['NGN']='&#8358;';
		$items['KPW']='&#8361;';
		$items['NOK']='kr';
		$items['OMR']='&#65020;';
		$items['PKR']='&#8360;';
		$items['PAB']='B/.';
		$items['PYG']='Gs';
		$items['PEN']='S/.';
		$items['PHP']='&#8369;';
		$items['PLN']='&#122;&#322;';
		$items['QAR']='&#65020;';
		$items['RON']='lei';
		$items['RUB']='&#1088;';
		$items['SHP']='£';
		$items['SAR']='&#65020;';
		$items['RSD']='&#1056;&#1057;&#1044;';
		$items['RSD-ALT']='RSD';//hyphens and anything thereafter will be stripped to get the right ISO in frontend
		$items['SCR']='&#8360;';
		$items['SGD']='$';
		$items['SBD']='$';
		$items['SOS']='S';
		$items['ZAR']='R';
		$items['KRW']='&#8361;';
		$items['LKR']='&#8360;';
		$items['SEK']='kr';
		$items['SRD']='$';
		$items['SYP']='£';
		$items['TWD']='NT$';
		$items['THB']='&#3647;';
		$items['TTD']='TT$';
		$items['TRL']='£';
		$items['TVD']='$';
		$items['UAH']='&#8372;';
		$items['UYU']='$U';
		$items['UZS']='&#1083;';
		$items['VEF']='Bs';
		$items['VND']='&#8363;';
		$items['YER']='&#65020;';
		$items['ZWD']='Z$';
		$items['TRY']='&#8378;';
		

		if(!$returnValue){
		ksort($items);
	    foreach($items as $key=>$val){
	    	if($key==$selected){$d=' selected="selected"';}else{$d='';}
			$options[]=array('selected'=>''.$d.'','value'=>''.$val.'','id'=>''.$key.'');
	    }}
	    if($selected!='' && $returnValue){
	    	$options=array('key'=>$selected,'val'=>$items[$selected]);
	    }
		return $options;
	}
	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		a bit hackish mind you, but thank's any way to the website
		that provided the original if i find it again, i insert the address here...
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * **/
	function wppizza_frontendOpeningTimes($options){
		$str='';
		/**group identical opening times**/
		foreach($options['opening_times_standard'] as $k=>$v){
			if($k==0){$k=7;}/*for sorting reasons , set sunday temporarily to 7 here**/
			if(!isset($times[''.$v['open'].'|'.$v['close'].''])){
				$times[''.$v['open'].'|'.$v['close'].'']=array();
				$times[''.$v['open'].'|'.$v['close'].''][]=$k;
			}else{
				$times[''.$v['open'].'|'.$v['close'].''][]=$k;
			}
		}
		foreach($times as $k=>$arr){
			/*to have sundays last when sorting, set it to 7*/
			asort($arr);
			$grouped[$k]=array('firstday'=>$arr[0],'days'=>$arr,'consecutivedays'=>wpizza_days_concat($arr));
		}
		/**sort by first day in array so we start with a monday regardless**/
		asort($grouped);
		foreach($grouped as $k=>$v){
			foreach(explode(",",$v['consecutivedays']) as $b=>$c){
				$str.='<span>';				
					$consec=explode("-",$c);
					$open=explode("|",$k);
					if(count($consec)==2){
						if(($consec[0]+1)==$consec[1]){$seperator=', ';}else{$seperator='-';}
						$str.=''.wpizza_format_weekday($consec[0],'D').''.$seperator.''.wpizza_format_weekday($consec[1],'D').'';
					}
					if(count($consec)==1){
						$str.=''.wpizza_format_weekday($consec[0],'D').'';
					}
					if($open[0]==$open[1]){
						$str.=' '.$options['localization']['openinghours_closed']['lbl'].'';
					}else{
						$str.=' '.wpizza_format_time($open[0],$options['opening_times_format']).'-'.wpizza_format_time($open[1],$options['opening_times_format']).'';//loose leading zeros
					}
				$str.='</span> ';
			}
		}
		return trim($str);
	}
	/**format time output**/
	function wpizza_format_time($time,$timeFormat){
		if(!isset($timeFormat) || !is_array($timeFormat)){
			$fHour='G';
			$fSeparator=':';
			$fMinute='i';
			$fAMPM='';
		}else{
			$fHour=$timeFormat['hour'];
			$fSeparator=$timeFormat['separator'];
			$fMinute=$timeFormat['minute'];
			$fAMPM=$timeFormat['ampm'];
		}
		$hm=explode(":",$time);
		$t=mktime($hm[0],$hm[1],0,0,0,0);
		$time=date(''.$fHour.''.$fSeparator.''.$fMinute.''.$fAMPM.'',$t);

	return $time;
	}

	/**makes a text representation out of int**/
	function wpizza_format_weekday($int,$format ){
		/*let's use static timestamps, no need to use the overhead of a function to generate really **/
		$day[1]=946900800;//mon (3rd jan 2000 12:00)
		$day[2]=946987200;//tue (4th jan 2000 12:00)
		$day[3]=947073600;//wed (5th jan 2000 12:00)
		$day[4]=947160000;//thu (6th jan 2000 12:00)
		$day[5]=947246400;//fri (7th jan 2000 12:00)
		$day[6]=947332800;//sat (8th jan 2000 12:00)
		$day[7]=947419200;//sun (9th jan 2000 12:00) if using 7 as sunday
		$day[0]=947419200;//sun (9th jan 2000 12:00) if using 0 as sunday

		//$wDayFormatted=strftime($format,$day[$int]);
		$wDayFormatted=date_i18n($format,$day[$int]);

		return $wDayFormatted;
	}
function wpizza_days_concat( Array $days ){

    // Define all days of the week, st sun(0) to 7
    static $all_days = array('1', '2', '3', '4', '5', '6','7');

    // prepare our output
    $output = array();

    // loop through all days of the week
    foreach ( $all_days as $i => $day ){
        // if it is included,
        if ( in_array( $day, $days ) ){
            $output[] = $day;
        }else{/*if not*/
            $output[] = '#';
        }
    }

    // clean everything up
    $output = implode( '#', $output );
    $output = trim( $output, '#' );

    // two or more consecutive hashes = days that are two or more apart
    $output = preg_split( '/##+/', $output, NULL, PREG_SPLIT_NO_EMPTY );

    // turn consecutive days into dashed days
    foreach ( $output as $i => $value ){
    	$output[ $i ] = preg_replace( '/#(\w+#)*/', '-', $value );
    }
    // format with commas
    $output = implode( ',', $output );

return $output;
}

/**are we currntly open ?*/
function wpizza_are_we_open($standard,$custom,$breaks){
	$serverTime=current_time('timestamp');
	$currentlyOpen=0;//initialize as closed
	$todayWday=date("w",$serverTime);
	$d=date("d",$serverTime);
	$m=date("m",$serverTime);
	$Y=date("Y",$serverTime);
	/**make sunday 7 instead of 0 to aid sorting**/
	if($todayWday==0){$yesterdayWday=6;}else{$yesterdayWday=($todayWday-1);}
	/**get the opening times today, as well as the spillover from yesterday
	in case its very early in the morning and we dont close until after midnight on the previous day**/
	$todayTimes=$standard[$todayWday];
	$yesterdayTimes=$standard[$yesterdayWday];
	$todayStartTime	= mktime(0, 0, 0, $m , $d, $Y);
	$todayEndTime	= mktime(23, 59, 59, $m , $d, $Y);
	$todayDate	= ''.$Y.'-'.$m.'-'.$d.'';
	$yesterdayDate	= date("Y-m-d",mktime(12, 0, 0, $m , $d-1, $Y));

	/**now we first check if these dates have custom dates opening times****/
	if(count($custom)>0){
		$yesterdayCustom=array_search($yesterdayDate,$custom['date']);
		$todayCustom=array_search($todayDate,$custom['date']);
	}
	/*if we have found dates in custom dates array,make start and end and use these**/
	if(isset($yesterdayCustom) && $yesterdayCustom!==false){
		$t=wpizza_get_opening_times($custom['open'][$yesterdayCustom],$custom['close'][$yesterdayCustom],$d,$m,$Y,'yesterday');
		if($t){
			$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
		}
	}else{//use times from standard opening times
		$t=wpizza_get_opening_times($standard[$yesterdayWday]['open'],$standard[$yesterdayWday]['close'],$d,$m,$Y,'yesterday');
		if($t){
			$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
		}
	}
	if(isset($todayCustom) && $todayCustom!==false){
		$t=wpizza_get_opening_times($custom['open'][$todayCustom],$custom['close'][$todayCustom],$d,$m,$Y,'today');
			$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
	}else{//use times from standard opening times
		$t=wpizza_get_opening_times($standard[$todayWday]['open'],$standard[$todayWday]['close'],$d,$m,$Y,'today');
		if($t){
			$openToday[]=array('start'=>$t['start'],'end'=>$t['end']);
		}
	}

	/*********check if we have added some breaks/siestas whatever you want to call it***/
	if(count($breaks)>0){
		/**first check if today is a custom day and if we've set break times for it**/
		if(isset($custom['date']) && in_array($todayDate,$custom['date']) ){
			foreach($breaks as $k=>$v){
				if($v['day']=='-1'){
					$t=wpizza_get_opening_times($v['close_start'],$v['close_end'],$d,$m,$Y,'today');
					if($t['start']<=$serverTime && $t['end']>=$serverTime){
						$currentlyOpen=0;
						return $currentlyOpen;
					}
				}
			}
		}else{
			/**its not a custom day, so check if we havea break set for this weekday**/
			foreach($breaks as $k=>$v){
				if($todayWday==$v['day']){
					$t=wpizza_get_opening_times($v['close_start'],$v['close_end'],$d,$m,$Y,'today');
					if($t['start']<=$serverTime && $t['end']>=$serverTime){
						$currentlyOpen=0;
						return $currentlyOpen;
					}
				}
			}

		}
	}
	/********we've done the siesta/break check, now check if current time is in the $openToday array between start and end***/
	foreach($openToday as $k=>$times){
		if( $serverTime >= $times['start'] && $serverTime <= $times['end']){
			$currentlyOpen=1;
		return $currentlyOpen;
		}
	}

	return $currentlyOpen;

}

/* takes 01:45, 3:45 format, no seconds as currently not needed**/
function wpizza_get_opening_times($starttime,$endtime,$d,$m,$Y,$day='today'){
 $openingtime=false;//initilize
 $start=explode(':',$starttime);
 $end=explode(':',$endtime);
 /***if both times are the same , we are closed**/
  if($starttime==$endtime) {
	$openingtime=false;
 }

 /*for easier comparison, change 00 in endtime hour to 24 if thats the case*/
 if((int)$end[0]==0){$end24=24;}else{$end24=$end[0];}
 /*compare. if start hour>end hour OR starthour==endhour AND startminute>endminute , opening times are crossing midnight**/
 if((int)$start[0]>$end24 ||  ($start[0]==$end[0] && $start[1]>$end[1])) {
 	$openingTimesCrossMidnight=1;
 }
 if(isset($openingTimesCrossMidnight)){
 	if($day=='today'){
 		$openingtime['start']=mktime($start[0],$start[1],0,$m,$d,$Y);
 		$openingtime['end']=mktime(23,59,59,$m,$d,$Y);
 	}
 	if($day=='yesterday'){
 		$openingtime['start']=mktime(0,0,0,$m,$d,$Y);
 		$openingtime['end']=mktime($end[0],$end[1],0,$m,$d,$Y);
 	}
 }else{
  	if($day=='today'){
 		$openingtime['start']=mktime($start[0],$start[1],0,$m,$d,$Y);
 		$openingtime['end']=mktime($end[0],$end[1],0,$m,$d,$Y);
 	}
/*won't happen...well, shouldn't**/
// 	if($day=='yesterday'){
// 		$openingtime['start']=mktime(23,0,0,$m,$d,$Y);
// 		$openingtime['end']=mktime(23,0,0,$m,$d,$Y);
// 	}
 }

	return $openingtime;
}

/*********************************************************
	[which mealsizes are available]
*********************************************************/
function wppizza_sizes_available($options){
	$availableSizes=array();
	if(is_array($options)){
	foreach($options as $l=>$m){
		foreach($m as $r=>$s){
			$availableSizes[$l]['lbl'][$r]=$options[$l][$r]['lbl'];
			$availableSizes[$l]['price'][$r]=$options[$l][$r]['price'];
		}
	}}
	return $availableSizes;
}
/*********************************************************
	[days]
*********************************************************/
function wppizza_days(){
	$items['1']=__('Mondays', WPPIZZA_LOCALE);
	$items['2']=__('Tuesdays', WPPIZZA_LOCALE);
	$items['3']=__('Wednesdays', WPPIZZA_LOCALE);
	$items['4']=__('Thursdays', WPPIZZA_LOCALE);
	$items['5']=__('Fridays', WPPIZZA_LOCALE);
	$items['6']=__('Saturdays', WPPIZZA_LOCALE);
	$items['0']=__('Sundays', WPPIZZA_LOCALE);

	return $items;
}
/*********************************************************
	[chosen style options]
*********************************************************/
function wppizza_public_styles($selected=''){
	$items['default']=__('Default', WPPIZZA_LOCALE);
	$items['responsive']=__('Responsive [Experimental]', WPPIZZA_LOCALE);
    foreach($items as $key=>$val){
    	if($key==$selected){$d=' selected="selected"';}else{$d='';}
		$options[]=array('selected'=>''.$d.'','value'=>''.$val.'','id'=>''.$key.'');
    }
    return $options;
}

/*********************************************************
	[which metabox (sizes,additives) options are being used]
*********************************************************/
function wppizza_options_in_use(){
	$usedAdditives=array();
	$usedSizes=array();
	//get your custom posts ids as an array
	$posts = get_posts(array(
	    'post_type'   => WPPIZZA_SLUG,
	    'fields' => 'ids',
	    'posts_per_page'=>-1
	    )
	);
	//loop over each post
	foreach($posts as $k=>$p){
		//get the meta you need form each post
		$itemMeta=get_post_meta($p,WPPIZZA_SLUG);
		if(isset($itemMeta[0]['additives']) && is_array($itemMeta[0]['additives']) && count($itemMeta[0]['additives'])>0){
			$usedAdditives[] =implode(",",$itemMeta[0]['additives']);
		}
		if(isset($itemMeta[0]['sizes'])){
			$usedSizes[] =$itemMeta[0]['sizes'];
		}
	}
	$optionsInUse['sizes']=array_unique($usedSizes);
	$optionsInUse['additives']=array_filter(array_unique(explode(",",implode(",",$usedAdditives))), 'strlen');

	return $optionsInUse;
}

/*********************************************************************************
*
*	[returns an array containing all order data (prices, discounts, currency etc]
*
*********************************************************************************/
function wppizza_order_summary($session,$options,$module=null,$ajax=null){
	/**allow filtering of options and session**/
	$session = apply_filters('wppizza_filter_order_summary_session', $session);
	$options = apply_filters('wppizza_filter_order_summary_options', $options);
	
	/***************************************************
		[in i some vars if undefined]
	***************************************************/
	if(!isset($session['total_price_calc_delivery'])){
		$session['total_price_calc_delivery']=0;
	}
	/****************************************************
		[set the original free delivery min_total value as a var
		to be able to compare against further down and deisplay appropriate
		text if min order value has also been set
	******************************************************/
	$orderMinTotalSet=$options['order']['delivery']['minimum_total']['min_total'];

	/****************************************************
		[get currency]
	****************************************************/
	$summary['currency']=''.$options['order']['currency_symbol'].'';/*do not add any spans or anything else to this as it gets stored in the db */
	$summary['currencyiso']=''.wppizza_validate_letters_only($options['order']['currency'],3).'';//strip any -alt identifiers (serbian currency for example has 2 display options but the same ISO code)

	/****************************************************
		[set currency positions]
	****************************************************/
	$summary['currency_left']=$summary['currency'].' ';
	$summary['currency_right']='';
	if($options['layout']['currency_symbol_position']=='right'){/*right aligned*/
		$summary['currency_left']='';
		$summary['currency_right']=' '.$summary['currency'];
	}

	/****************************************************
		[hide decimals?]
	****************************************************/
	$optionsDecimals=$options['layout']['hide_decimals'];
	/****************************************************
		[get cart items as grouped array]
	****************************************************/
	$cartItems=array();//ini array
	$cartItemsCount=0;//count all items
	$groupedItems=array();//ini array
	$summary['items']=array();//ini array
	/**lets group items by id and sizes***/
	foreach($session['items'] as $groupid=>$groupitems){
		foreach($groupitems as $v){
			$cartItemsCount++;
			/**really only for legacy reasons, future versions will only have extend key**/
			if(!isset($v['additionalinfo'])){$v['additionalinfo']=array();}
			if(!isset($v['extend'])){$v['extend']=array();}
			if(!isset($v['extenddata'])){$v['extenddata']=array();}
			$cartItems[''.$groupid.''][]=array('sortname'=>$v['sortname'],'size'=>$v['size'],'sizename'=>$v['sizename'],'printname'=>$v['printname'],'price'=>$v['price'],'additionalinfo'=>$v['additionalinfo'],'extend'=>$v['extend'],'extenddata'=>$v['extenddata'],'postId'=>$v['id']);
			/**conditional just used to not break other extensions/plugins that have not been updated yet to add selected category id.*/
			if(isset($v['catIdSelected'])){
				$catIdSelected[''.$groupid.'']=$v['catIdSelected'];
			}else{
				$catIdSelected[''.$groupid.'']='';
			}
		}
	}
	foreach($cartItems as $k=>$v){
		$groupedItems[$k]=array(
			'sortname'=>$cartItems[$k][0]['sortname'],
			'size'=>$cartItems[$k][0]['size'],
			'sizename'=>$cartItems[$k][0]['sizename'],
			'printname'=>$cartItems[$k][0]['printname'],
			'price'=>$cartItems[$k][0]['price'],
			'count'=>count($cartItems[$k]),
			'total'=>(count($cartItems[$k])*$cartItems[$k][0]['price']),
			'additionalinfo'=>$cartItems[$k][0]['additionalinfo'],
			'extend'=>$cartItems[$k][0]['extend'],
			'extenddata'=>$cartItems[$k][0]['extenddata'],
			'postId'=>$cartItems[$k][0]['postId'],
			'catIdSelected'=>$catIdSelected[$k]
		);
	}
	asort($groupedItems);
	/**output items sorted by name and size**/
	foreach($groupedItems as $k=>$v){
		/*get categories**/
		$catObj = get_the_terms($v['postId'], WPPIZZA_TAXONOMY);
		$catArray=json_decode(json_encode($catObj), true);
		/*******************************************************************************************
			if other extensions have yet to add selcatid, just add the first one
			the item is categorised in.
			90% of the time there will only be one anyway, so this would be correct.
			worst case scenario, an enexpected (although not wrong) category will be displayed
			{all of this is only relevant anyway if "show category for emails etc" is enabled in layout
		*******************************************************************************************/
		if($v['catIdSelected']==''){
			$firstCat=reset($catArray);
			$v['catIdSelected']=$firstCat['term_id'];
		}		
		$summary['items'][$k]=array('name'=>$v['printname'],'count'=>$v['count'],'size'=>$v['sizename'],'price'=>wppizza_output_format_price($v['price'],$optionsDecimals),'pricetotal'=>wppizza_output_format_price($v['total'],$optionsDecimals),'categories'=>$catArray,'additionalinfo'=>$v['additionalinfo'],'extend'=>$v['extend'],'extenddata'=>$v['extenddata'],'postId'=>$v['postId'],'catIdSelected'=>$v['catIdSelected']);
	}


	/****************************************************
		[if ajax request get items from template to keep formatting consistent]
	****************************************************/
	if(($ajax)){
		$summary['itemsajax'] = do_shortcode('[wppizza type="cart" request="ajax"]');
	}
	/**********************************
		[discounts]
	**********************************/
	/** no discount**/
		$discountLabel='';
		$discountValue='';
		$discountValuePrint='';
		if($options['order']['discount_selected']=='none'){
				$discountApply=0;
		}
		/** percentage discount**/
		if($options['order']['discount_selected']=='percentage'){
			/**sort highest to lowest and check if it aplies, if it does, apply and stop loop (only want to appply one!**/
			$discountApply=0;
			/**get most relevant discount to apply to price***/
			rsort($options['order']['discounts']['percentage']['discounts']);
			foreach($options['order']['discounts']['percentage']['discounts'] as $k=>$v){
				if($session['total_price_items']>=$v['min_total']){
					$discountApply=round($session['total_price_items']/100*$v['discount'],2);
				break;
				}
			}
			/**get all available discounts to display***/
			sort($options['order']['discounts']['percentage']['discounts']);
			foreach($options['order']['discounts']['percentage']['discounts'] as $k=>$v){
				if($v['discount']>0){// && $v['min_total']>0
				$summary['pricing_discounts'][]="".$options['localization']['spend']['lbl']." <span>".$options['order']['currency_symbol']."".wppizza_output_format_price($v['min_total'],$optionsDecimals)."</span> ".$options['localization']['save']['lbl']." <span>".($v['discount'])."%</span>";
				}
			}

		}
		/** value discount**/
		if($options['order']['discount_selected']=='standard'){
			/**sort highest to lowest and check if it aplies, if it does, apply and stop loop (only want to appply one!**/
			$discountApply=0;
			/**get most relevant discount to apply to price***/
			rsort($options['order']['discounts']['standard']['discounts']);
			foreach($options['order']['discounts']['standard']['discounts'] as $k=>$v){
				if($session['total_price_items']>=$v['min_total']){
					$discountApply=$v['discount'];
				break;
				}
			}
			/**get all available discounts to display***/
			sort($options['order']['discounts']['standard']['discounts']);
			foreach($options['order']['discounts']['standard']['discounts'] as $k=>$v){
				if($v['discount']>0){//&& $v['min_total']>0
					$summary['pricing_discounts'][]="".$options['localization']['spend']['lbl']." <span>".$options['order']['currency_symbol']." ".wppizza_output_format_price($v['min_total'],$optionsDecimals)."</span> ".$options['localization']['save']['lbl']." <span>".wppizza_output_format_float($v['discount'])." ".$options['order']['currency_symbol']."</span>";
				}
			}
		}
		/***self pickup discount added to other discounts (if any)**/
		if($options['order']['order_pickup_discount']>0 && isset($session['selfPickup']) ){
			$discountApply=$discountApply+($session['total_price_items']/100*$options['order']['order_pickup_discount']);
		}


		if(isset($discountApply) && $discountApply>0){
			$discountLabel=$options['localization']['discount']['lbl'];
			$discountValue=(wppizza_output_format_float($discountApply));
			$discountValuePrint=wppizza_output_format_price($discountValue,$optionsDecimals);
		}

			/**********************************
				[delivery]
			**********************************/
			$deliveryLabel=$options['localization']['free_delivery']['lbl'];//initialize var
			$deliveryCharges='';

			if($options['order']['delivery_selected']=='no_delivery'){/*delivery disabled*/
				$deliveryCharges='';/*set to empty to hide*/
				$deliveryLabel='';/*set to empty to hide*/
				/*set flag for 'no delivery'*/
				$summary['no_delivery']=1;
				$summary['self_pickup_enabled']=1;
				$summary['selfPickup']=2;/**set to >1 to not display self pickup note/text in emails and order page*/
				/**disable self pickup checkboxes*/
				$options['order']['order_pickup']=false;
			}


			if($options['order']['delivery_selected']=='standard'){//standard (i.e. fixed delivery charges)
				/**delivery settings to display with discount options somewhere*/
				if($options['order']['delivery']['standard']['delivery_charge']>0){
					$deliveryLabel=$options['localization']['delivery_charges']['lbl'];
					$deliveryCharges=wppizza_output_format_float($options['order']['delivery']['standard']['delivery_charge']);
				}
			}
			if($options['order']['delivery_selected']=='minimum_total'){//minimum total
				if($options['order']['delivery']['minimum_total']['deliver_below_total']){
					if($session['total_price_calc_delivery']<$options['order']['delivery']['minimum_total']['min_total']){
						$deliveryLabel=$options['localization']['delivery_charges']['lbl'];
						$deliveryCharges=wppizza_output_format_float($options['order']['delivery']['minimum_total']['min_total']-$session['total_price_calc_delivery']);
					}
				}

				/**fixed price set if below free delivery: overrides "deliver_below_total" **/
				if($options['order']['delivery']['minimum_total']['deliverycharges_below_total']>0){
					if($session['total_price_calc_delivery']<$options['order']['delivery']['minimum_total']['min_total']){
						$deliveryLabel=$options['localization']['delivery_charges']['lbl'];
						$deliveryCharges=wppizza_output_format_float($options['order']['delivery']['minimum_total']['deliverycharges_below_total']);
					}
				}

				/**if we are hiding decimals recalc/round delivery charges or we might get rounding errors**/
				if($deliveryCharges>0 && $optionsDecimals){
					$recalc=wppizza_output_format_float($deliveryCharges,'hidedecimals');
					if($recalc<=0){
						$deliveryLabel=$options['localization']['free_delivery']['lbl'];
						$deliveryCharges='';
					}else{
						$deliveryCharges=$recalc;;
					}
				}

				/**delivery settings to display with discount options somewhere*/
				if($options['order']['delivery']['minimum_total']['min_total']>0){
					$summary['pricing_delivery']="".$options['localization']['free_delivery_for_orders_of']['lbl']." <span>".$options['order']['currency_symbol']." ".wppizza_output_format_price($options['order']['delivery']['minimum_total']['min_total'],$optionsDecimals)."</span>";
				}else{
					$summary['pricing_delivery']="".$options['localization']['free_delivery']['lbl']."";
				}
			}



			if($options['order']['delivery_selected']=='per_item'){/*delivery charges on a per item basis*/
				/**free delivery isset>0**/
				if($options['order']['delivery']['per_item']['delivery_per_item_free']>0){
					/*value not reached for free delivery*/
					if($session['total_price_calc_delivery']<$options['order']['delivery']['per_item']['delivery_per_item_free']){
						/*number of items*deliverycharges per item*/
						if($cartItemsCount>0 && $options['order']['delivery']['per_item']['delivery_charge_per_item']>0){
							$deliveryCharges=wppizza_output_format_float($cartItemsCount*$options['order']['delivery']['per_item']['delivery_charge_per_item']);
						}
					}
				}else{/*no free delivery set (i.e set to 0)*/
					/*number of items*deliverycharges per item*/
					if($cartItemsCount>0){
						$deliveryCharges=wppizza_output_format_float($cartItemsCount*$options['order']['delivery']['per_item']['delivery_charge_per_item']);
					}
				}
				/*label next to delivery charges if>0 otherwise default above->free*/
				if($deliveryCharges>0){
					$deliveryLabel=$options['localization']['delivery_charges']['lbl'];
				}

				/**delivery settings to display with discount options somewhere*/
				if($options['order']['delivery']['per_item']['delivery_per_item_free']>0){
					$summary['pricing_delivery']="".$options['localization']['delivery_charges_per_item']['lbl']." <span>".$options['order']['currency_symbol']." ".wppizza_output_format_price($options['order']['delivery']['per_item']['delivery_charge_per_item'],$optionsDecimals)."</span>";
					$summary['pricing_delivery_per_item_free']="".$options['localization']['free_delivery_for_orders_of']['lbl']." <span>".$options['order']['currency_symbol']." ".wppizza_output_format_price($options['order']['delivery']['per_item']['delivery_per_item_free'],$optionsDecimals)."</span>";
				}else{
					$summary['pricing_delivery']="".$options['localization']['delivery_charges_per_item']['lbl']." <span>".$options['order']['currency_symbol']." ".wppizza_output_format_price($options['order']['delivery']['per_item']['delivery_charge_per_item'],$optionsDecimals)."</span>";
				}

			}

			/*******************************************
			*	admin enabled self pickup on the frontend
			******************************************/
			if($options['order']['order_pickup']){
				$summary['self_pickup_enabled']=1;
				$summary['order_self_pickup']=$options['localization']['order_self_pickup']['lbl'];
				$summary['order_self_pickup_cart']=$options['localization']['order_self_pickup_cart']['lbl'];
				$summary['order_page_self_pickup']=$options['localization']['order_page_self_pickup']['lbl'];

				/*check where we want to display self pickup checkbox*/
				if($options['order']['order_pickup_display_location']==1 || $options['order']['order_pickup_display_location']==3){
					$summary['self_pickup_cart']=1;
				}
				if($options['order']['order_pickup_display_location']==2 || $options['order']['order_pickup_display_location']==3){
					$summary['self_pickup_order_page']=1;
				}
				$summary['selfPickup']=0;/*default off*/
				/*customer chose self pickup. set appropriate values in cart*/
				if(isset($session['selfPickup'])){
					$summary['selfPickup']=1;/*indicate that self pcikup chosen*/
					$deliveryCharges=0;/*set delivery charges to 0*/
				}
				/**set id for checkbox to see if we have enabled js alerts**/
				if($options['order']['order_pickup_alert']){
					$summary['selfPickupId']='wppizza-order-pickup-js';
				}else{
					$summary['selfPickupId']='wppizza-order-pickup-sel';
				}
			}


			/**minimum order value set but not reached***/
			if($options['order']['order_min_for_delivery']>0 && $options['order']['order_min_for_delivery']>$session['total_price_calc_delivery'] ){//&& (!isset($summary['selfPickup']) ||  isset($summary['no_delivery']))
				$placeOrderDisabled=true;

				/**set min_total value to be min order for delivery**/
				$options['order']['delivery']['minimum_total']['min_total']=$options['order']['order_min_for_delivery'];

				$options['localization']['minimum_order']['lbl']=$options['localization']['minimum_order']['lbl'];
			}

			/*************************************************************************
			*
			*
			*	[Taxes]
			*
			*
			*************************************************************************/

				/****************************************************
					[tax on sum of all items BEFORE discounts. currently not in use]
				****************************************************/
				//	$summary['total_items_tax']=wppizza_round_up($session['total_items_tax'],2);

				$itemTax=0;/**ini as 0**/
				$taxesIncluded=0;/**ini as 0**/
				$summary['taxrate']=$options['order']['item_tax'];/*capture/set taxrate**/
				/**********************************************************
				*
				*	[tax NOT included in set prices]
				*
				**********************************************************/
				if(!$options['order']['taxes_included']){
					/****************************************************
						[item tax AFTER discounts]
					****************************************************/
					$summary['tax_applied']='items_only';
					if($options['order']['item_tax']>0){
						$summary['tax_enabled']=1;
						$totalSales=$session['total_price_items']-(float)$discountValue;
						/*round up decimals**/
						$itemTax=wppizza_round_up($totalSales/100*$options['order']['item_tax'],2);

						/****************************************************
							[add tax to shipping too]
						****************************************************/
						if($options['order']['shipping_tax']){
							$summary['tax_applied']='items_and_shipping';/*set location*/
						}
						if($options['order']['shipping_tax'] && $deliveryCharges!='' && (int)$deliveryCharges>0){
							$itemTax=wppizza_round_up(($totalSales+$deliveryCharges)/100*$options['order']['item_tax'],2);
						}
					}
				}
				/**********************************************************
				*
				*	[tax IS included in prices !!!]
				*
				**********************************************************/
				if($options['order']['taxes_included']){
					$summary['tax_applied']='taxes_included';
					/****************************************************
						[add tax to items only]
					****************************************************/
					if($options['order']['item_tax']>0){
						$summary['tax_enabled']=1;
						$totalSales=$session['total_price_items']-(float)$discountValue;
						$taxesIncluded=wppizza_round_up($totalSales/(100+$options['order']['item_tax'])*$options['order']['item_tax'],2);
					}
					/****************************************************
						[add tax to shipping too]
					****************************************************/
					if($options['order']['shipping_tax'] && $deliveryCharges!='' && (int)$deliveryCharges>0){
						$taxesIncluded=wppizza_round_up(($totalSales+$deliveryCharges)/(100+$options['order']['item_tax'])*$options['order']['item_tax'],2);
					}
				}
				
			/*********************************************************************
			*
			*
			*	[surcharges handling charges]
			*
			*
			**********************************************************************/				
				/****************************************************
					[total order before tips]
				****************************************************/
				$totalOrderBeforeTips=$session['total_price_items']-(float)$discountValue+(float)$deliveryCharges+(float)$itemTax;
				
				/****************************************************
					[surcharges]
				****************************************************/
				$surcharges=0;
				$surchargePcVal=$session['gateway-selected']['surchargePc'];
				$surchargeFixedVal=$session['gateway-selected']['surchargeFixed'];
				
				/*charges percent*/
				if($surchargePcVal>0){
					$surcharges+=$totalOrderBeforeTips/100*abs($surchargePcVal);
				}
				/*charges fixed*/
				if($surchargeFixedVal>0){
					$surcharges+=$surchargeFixedVal;
				}
				/**round*/
				if($surcharges>0){
					$surcharges=wppizza_round_up($surcharges,2);
				}
			/*********************************************************************
			*
			*
			*	[gratuities]
			*
			*
			**********************************************************************/
			$gratuities=0;
			if(isset($session['tips']) && $session['tips']>0){
				$gratuities=wppizza_output_format_price($session['tips'],$optionsDecimals);
				$summary['tips']=array('lbl'=>$options['localization']['tips']['lbl'],'val'=>$gratuities);
			}

	/****************************************************
		[get total order value]
	****************************************************/
	$totalOrder=$session['total_price_items']-(float)$discountValue+(float)$deliveryCharges+(float)$itemTax+(float)$gratuities;
	/**if customer chose self pickup, display only label that states self pickup . no need for value**/
	$deliveryValue=wppizza_output_format_price($deliveryCharges,$optionsDecimals);
	$summary['order_value']=array(
		'item_tax'=>array('lbl'=>$options['localization']['item_tax_total']['lbl'],'val'=>wppizza_output_format_price($itemTax,$optionsDecimals)),
		'taxes_included'=>array('lbl'=>sprintf(''.$options['localization']['taxes_included']['lbl'].'',$options['order']['item_tax']),'val'=>wppizza_output_format_price($taxesIncluded,$optionsDecimals)),
		'total_price_items'=>array('lbl'=>$options['localization']['order_items']['lbl'],'val'=>wppizza_output_format_price(wppizza_output_format_float($session['total_price_items']),$optionsDecimals)),
		'delivery_charges'=>array('lbl'=>$deliveryLabel,'val'=>$deliveryValue),
		'discount'=>array('lbl'=>$discountLabel,'val'=>$discountValuePrint),
		'total'=>array('lbl'=>$options['localization']['order_total']['lbl'],'val'=>wppizza_output_format_price(wppizza_output_format_float($totalOrder),$optionsDecimals))
	);
	
	/*******************************************************
		[gateways must handle surcharges themselves and
		update the db accordingly. However, to display 
		surcharges on gateway change in the orderpage
		we overwrite the total here for display before
		actually processing and without adding them to the db]
	*******************************************************/
	if($surcharges>0 && ($module=='orderpage' || $module=='confirmationpage' )){
		$summary['order_value']['total']=array('lbl'=>$options['localization']['order_total']['lbl'],'val'=>wppizza_output_format_price(wppizza_output_format_float($totalOrder+$surcharges),$optionsDecimals));
		$summary['order_value']['handling_charge']=array('lbl'=>$options['localization']['order_page_handling']['lbl'],'val'=>wppizza_output_format_price(wppizza_output_format_float($surcharges),$optionsDecimals));
	}	
	if($session['gateway-selected']['surchargeAtCheckout'] && ($module=='orderpage' || $module=='confirmationpage' )){
		$summary['order_value']['handling_charge']=array('lbl'=>$options['localization']['order_page_handling']['lbl'],'str'=>$options['localization']['order_page_handling_oncheckout']['lbl']);
	}
	

	/****************************************************
		[check if we are open]
	****************************************************/
	if(!isset($options['opening_times_custom'])){$options['opening_times_custom']=array();}/*get rid of some php notices*/
	if(!isset($options['times_closed_standard'])){$options['times_closed_standard']=array();}/*get rid of some php notices*/

	$isOpen=wpizza_are_we_open($options['opening_times_standard'],$options['opening_times_custom'],$options['times_closed_standard']);
	
	/***allow filtering of is_open**/
	$options = apply_filters('wppizza_filter_order_summary_options_open', $options, $isOpen);
	$session = apply_filters('wppizza_filter_order_summary_session_open', $session, $isOpen);
	$isOpen = apply_filters('wppizza_filter_is_open', $isOpen);



	$summary['shopopen']=$isOpen;
	$summary['button']='';
	$summary['nocheckout']='';
	if($isOpen==0){//closed -> display closed in cart element
		$summary['innercartinfo']=$options['localization']['closed']['lbl'];
	}
	if($isOpen==1){//open
		if(count($summary['items'])<=0){//open but nothing in cart -> display 'cart is empty' in cart element
			$summary['innercartinfo']=$options['localization']['cart_is_empty']['lbl'];
		}
		if(count($summary['items'])>0){//open and stuff in cart -> check min value reached in do/dont display button and info
			if(
					(
						(
						$options['order']['delivery_selected']=='minimum_total' &&
							(
								$options['order']['delivery']['minimum_total']['deliver_below_total'] ||
								(!$options['order']['delivery']['minimum_total']['deliver_below_total'] &&  $session['total_price_calc_delivery']>=$options['order']['delivery']['minimum_total']['min_total'])
							)

						) ||
						$options['order']['delivery_selected']=='standard' ||
						$options['order']['delivery_selected']=='no_delivery' ||
						$options['order']['delivery_selected']=='per_item'
					) && !isset($placeOrderDisabled)

			){
				if($options['order']['orderpage']){//go to order page
					/**wpml select of order page**/
					if(function_exists('icl_object_id')) {
						$options['order']['orderpage']=icl_object_id($options['order']['orderpage'],'page');
						/*confirmation page -> amend order link**/
						if($options['confirmation_form_amend_order_link']>0){
							$options['confirmation_form_amend_order_link']=icl_object_id($options['confirmation_form_amend_order_link'],'page');
						}
					}
					
					
					$summary['orderpagelink']=get_page_link($options['order']['orderpage']);
					/*confirmation page -> amend order link**/
					if($options['confirmation_form_amend_order_link']>0){
						$summary['amendorderlink']=get_page_link($options['confirmation_form_amend_order_link']);
					}else{
						$summary['amendorderlink']='';	
					}
					
					
					$summary['button']='<a href="'.$summary['orderpagelink'].'">';
					$summary['button'].='<input class="btn btn-primary" type="button" value="'.$options['localization']['place_your_order']['lbl'].'" />';
					$summary['button'].='</a>';

				}
			}else{
					/**free delivery and charges > min order value*/
					if($options['order']['delivery_selected']=='minimum_total' && $orderMinTotalSet==$options['order']['delivery']['minimum_total']['min_total']){//&& $orderMinTotalSet>=$options['order']['delivery']['minimum_total']['min_total']
						$summary['nocheckout']=''.$options['localization']['minimum_order_delivery']['lbl'].' ';
					}else{
						$summary['nocheckout']=''.$options['localization']['minimum_order']['lbl'].' ';
					}
						$summary['nocheckout'].=''.$options['order']['currency_symbol'].' '.wppizza_output_format_price($options['order']['delivery']['minimum_total']['min_total'],$optionsDecimals).'';
			}
		}

		/****************************************************
			[empty cart button, show/hide depending if enabled or no of items]
		*****************************************************/
		if(!empty($options['layout']['empty_cart_button']) && count($summary['items'])>0 ){
			$summary['button'].='<input class="wppizza-empty-cart-button btn btn-primary" type="button" value="'.$options['localization']['empty_cart']['lbl'].'" />';
		}

	}
	/**enable increase/decrease in cart**/
	if($options['layout']['cart_increase']){
		$summary['increase_decrease']=1;
	}

	$summary = apply_filters('wppizza_filter_summary', $summary);

return $summary;
}
/***************************************************
	[sort multidimensional array
	[example: wppizza_array_multisort($array, array('sizes'=>SORT_ASC, 'item'=>SORT_ASC)); ]
****************************************************/
function wppizza_array_multisort($array, $cols){
	    $colarr = array();
	    foreach ($cols as $col => $order) {
	        $colarr[$col] = array();
	        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
	    }
	    $eval = 'array_multisort(';
	    foreach ($cols as $col => $order) {
	        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
	    }
	    $eval = substr($eval,0,-1).');';
	    eval($eval);
	    $ret = array();
	    foreach ($colarr as $col => $arr) {
	        foreach ($arr as $k => $v) {
	            $k = substr($k,1);
	            if (!isset($ret[$k])) $ret[$k] = $array[$k];
	            $ret[$k][$col] = $array[$k][$col];
	        }
	    }
	    return $ret;
}
/***********************************************************************************
	[ helper to return single dimension localization variables]
	[ could also be used of course to return any 2 dimensional array as single dimension arr]

	localization variables tend to be organized like this:
	'some_key'=>array(
			'descr'=>__('some description', $this->pluginLocale),
			'lbl'=>__('some label', $this->pluginLocale)
	),

	so to access the lbl, one has to write something like
	$txt=$this->pluginOptions

	echo $txt['localization']['some_key']['lbl']
	or
	echo $txt['localization']['some_key']['descr']

	or some such
	with this helper you can write:
	$txt=wppizza_output_localization_vars($this->pluginOptions['localization'],'lbl')

	echo $txt['some_key'];
	a lot cleaner
@ arr: two dimensional array
@ key: which key to use as single dimention key value
****************************************************************************/
function wppizza_return_single_dimension_array($arr, $key='lbl'){
	$array=array();
	if(is_array($arr)){
	foreach($arr as $k=>$v)
		$array[$k]=$v[$key];
	}
	return $array;
}

/****************************************************************************
	[decode entities in send order email plaintext]
****************************************************************************/
function wppizza_email_decode_entities($str,$charset,$decodeNCRs=true){

		$supportedCharsets=array('iso-8859-1','iso-8859-5','iso-8859-15','utf-8','cp866','cp1251','cp1252','koi8-r','big5','gb2312','big5-hkscs','shift_jis','euc-jp','macroman');
		if(in_array(strtolower($charset),$supportedCharsets)){
			$charset=$charset;
		}else{
			$charset='UTF-8';
		}
		if($decodeNCRs){
   			$str= html_entity_decode($str,ENT_QUOTES,"".$charset."");////".get_bloginfo('charset')."
    		$str= preg_replace('/&#(\d+);/m',"chr(\\1)",$str); //#decimal notation /*php 5.5 e modifier deleted*/
	    	$str= preg_replace('/&#x([a-f0-9]+);/mi',"chr(0x\\1)",$str);  //#hex notation/*php 5.5 e modifier deleted*/
	    	/**the below is - i think - an error as to how &amp; is stored in the db in the first place. ought to check that at some point*/
	    	$str= str_replace('&amp;','&',$str);/*let's deal with &amp too quotes have already been dealt with in html_entity_decode. not using htmlspecialchars_decode as that would also convert back &lt; and &gt; which we (probably) dont want. lt's be safe.*/
		}

	return $str;
}
/****************************************************************************
	[convert entities in send order email when sending html]
****************************************************************************/
function wppizza_email_html_entities($str){
	$str = htmlentities($str, ENT_QUOTES, mb_internal_encoding());
	return $str;
}
/****************************************************************************
	[output formfields depending on type]
****************************************************************************/
function wppizza_echo_formfield($type='text',$id='',$name='',$value='',$placeholder='',$options='',$selected=''){

	if($type=='text' || $type=='email'){
		echo'<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'"  size="40" placeholder="'.$placeholder.'" />';
	}
	if($type=='checkbox' || $type=='radio'){/**lets keep this for legacy reasons*/
		echo'<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="1" '.$selected.'/>';
	}

	if($type=='check'){
		echo'<input type="checkbox" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$selected.'/>';
	}
	if($type=='rdo'){
		if(is_array($options)){
			$i=0;
			foreach($options as $key=>$val){
				echo'<input type="radio" id="'.$key.'_'.$i.'" name="'.$key.'" value="'.$val.'" '.checked(is_array($selected) && in_array($val,$selected),true,false).'/>';
			$i++;
			}
		}else{
			echo'<input type="rdo" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$selected.'/>';
		}
	}

	if($type=='checkboxmulti'){
		if(isset($options) && is_array($options)){
		foreach($options as $k=>$v){
			echo'<input type="checkbox" id="'.$id.'_'.$v.'" name="'.$name.'['.$v.']" value="'.$v.'" '.checked(in_array($v,$value),true,false).'/> '.$v.'&nbsp;&nbsp;&nbsp;';
		}}
	}
	if($type=='textarea'){
		echo'<textarea id="'.$id.'" name="'.$name.'">'.$value.'</textarea>';
	}
	if($type=='texteditor'){
		$id=strtolower(str_replace(array('[',']'),'_',$name));/* WP 3.9 doesnt like brackets in id's*/
		echo'<div style="width:550px">';
		wp_editor( $value, $id , array('teeny'=>1,'wpautop'=>false,'media_buttons'=>false,'textarea_name'=>$name) );
		echo'</div>';
	}
	if($type=='select'){
		echo''.$options;
	}
}
?>