<?php
error_reporting(0);
if(!defined('DOING_AJAX') || !DOING_AJAX){
	header('HTTP/1.0 400 Bad Request', true, 400);
	print"you cannot call this script directly";
  exit; //just for good measure
}
/**testing variables ****************************/
//unset($_SESSION[$this->pluginSession]);
//sleep(5);//when testing jquery fadeins etc
/******************************************/
$options=$this->pluginOptions;
/***************************************************************
*
*
*	[add / remove item from cart session]
*
*
***************************************************************/
if(isset($_POST['vars']['type']) && (($_POST['vars']['type']=='add' || $_POST['vars']['type']=='remove' || $_POST['vars']['type']=='removeall' || $_POST['vars']['type']=='increment') && $_POST['vars']['id']!='')||  $_POST['vars']['type']=='refresh'){

	/**set count as int*********/
	$itemCount=1;
	if(isset($_POST['vars']['itemCount'])){
	$itemCount=(int)$_POST['vars']['itemCount'];
	}
	/**category id*********/
	$catIdSelected='';
	if(isset($_POST['vars']['catId']) && $_POST['vars']['catId']!=''){
		$catIdSelected=(int)$_POST['vars']['catId'];
	}

	/**initialize price array***/
	$itemprice=array();
	$itempricefordelivery=array();/*if we have excluded item to count towards free delivery */
	/**********set header********************/
	header('Content-type: application/json');
	/**add to cart**/
	if($_POST['vars']['type']=='add'){
		/*explode into item id and selected size***/
		$itemVars=explode("-",$_POST['vars']['id']);
		//$meta=get_post_meta($itemVars[1], $this->pluginSlug, true );
		$itemName=get_the_title($itemVars[1]);
		$groupId=$itemVars[1].'.'.$itemVars[3];//group items by id and size . ensure there's a seperator between (as 8 and 31 would otherwise be the same as 83 and 1. furthermore , dont use "-" as the js splits by this
		/**add category to group id (distinct cat id will only be passed if catdisplay enabled in layout)**/
		if($catIdSelected!='' && $this->pluginOptions['layout']['items_group_sort_print_by_category']){/*if we dont need to or want to split by category, do not add another distinction to the group*/
			$groupId.='.'.$catIdSelected;
		}

		/*get item set meta values to get price for this size**/
		$meta_values = get_post_meta($itemVars[1],$this->pluginSlug,true);
		$itemSizePrice=$meta_values['prices'][$itemVars[3]];
		/**are we hiding pricetier name if only one available ?**/
		if(count($meta_values['prices'])<=1 && $options['layout']['hide_single_pricetier']==1){
			$itemSizeName='';
		}else{
			$itemSizeName=$options['sizes'][$itemVars[2]][$itemVars[3]]['lbl'];
		}

		/*add item to session array. adding lowercase name first to simplify sorting with asort**/
		$_SESSION[$this->pluginSession]['items'][$groupId][]=array('sortname'=>strtolower($itemName),'size'=>$itemVars[3],'price'=>$itemSizePrice,'sizename'=>$itemSizeName,'printname'=>$itemName,'id'=>$itemVars[1],'catIdSelected'=>$catIdSelected);
	}

	/**increment when using textbox**/
	if($_POST['vars']['type']=='increment'){
		$groupSel=explode("-",$_POST['vars']['id']);
		$groupId=$groupSel[2];
		$setGroup=$_SESSION[$this->pluginSession]['items'][$groupId][0];
		unset($_SESSION[$this->pluginSession]['items'][$groupId]);
		/**reset from scratch**/
		for($i=0;$i<$itemCount;$i++){
			$_SESSION[$this->pluginSession]['items'][$groupId][]=$setGroup;
		}
	}


	/**remove from cart -> just unset**/
	if($_POST['vars']['type']=='remove'){
		/**explode and get last in array (the id)**/
		$groupId=end(explode("-",$_POST['vars']['id']));
		end($_SESSION[$this->pluginSession]['items'][$groupId]);
		$last=key($_SESSION[$this->pluginSession]['items'][$groupId]);
		unset($_SESSION[$this->pluginSession]['items'][$groupId][$last]);
		/*if there are 0x this ingredient, unset completely**/
		if(count($_SESSION[$this->pluginSession]['items'][$groupId])==0 || $itemCount==0){
			unset($_SESSION[$this->pluginSession]['items'][$groupId]);
		}
	}
	/**empty  cart -> just unset**/
	if($_POST['vars']['type']=='removeall'){
		unset($_SESSION[$this->pluginSession]['items']);
		$_SESSION[$this->pluginSession]['items']=array();
	}


	/*total price*/
	foreach($_SESSION[$this->pluginSession]['items'] as $k=>$group){
		foreach($group as $v){
			$itemprice[]=$v['price'];
			/**exclude items that are set to be excluded from calculating whether or not free delivery applies**/
			if(!isset($options['order']['delivery_calculation_exclude_item']) || !in_array($group[0]['id'],$options['order']['delivery_calculation_exclude_item'])){
				$itempricefordelivery[]=$v['price'];
			}
		}
	}

	$totalitemprice=array_sum($itemprice);
	$totalitempricefordelivery=array_sum($itempricefordelivery);

	/**total tax on all items -> currently not used as we will be calculating tax AFTER any discounts**/
	$_SESSION[$this->pluginSession]['total_items_tax']=0;
	if($options['order']['item_tax']>0){
		$_SESSION[$this->pluginSession]['total_items_tax']=$totalitemprice/100*$options['order']['item_tax'];
	}


	$_SESSION[$this->pluginSession]['total_price_items']=$totalitemprice;
	$_SESSION[$this->pluginSession]['total_price_calc_delivery']=$totalitempricefordelivery;


	print"".json_encode(wppizza_order_summary($_SESSION[$this->pluginSession],$options, 'cartajax', true))."";
	exit();
}
/***************************************************************
*
*
*	[set self pickup]
*
*
***************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='order-pickup'){


	/*****************************************
		[set session variable]
	*****************************************/
	if($_POST['vars']['value']=='true'){
		$_SESSION[$this->pluginSession]['selfPickup']=1;
	}else{
		if(isset($_SESSION[$this->pluginSession]['selfPickup'])){
			unset($_SESSION[$this->pluginSession]['selfPickup']);
		}
	}
	/*****************************************
		set default location -> to be overwritten below if required
	*****************************************/
	$location=$_POST['vars']['locHref'];

	/*****************************************
		[get and parse all post variables
		provided we are actually on the order
		page, otherwise there's nothing to do
	*****************************************/
	if(count($_POST['vars']['data'])>0){

		/***************************************************************
			[get and parse all user post variables and save in session
		***************************************************************/
		$this->wppizza_sessionise_userdata($_POST['vars']['data'],$options['order_form']);

		/*****************************************
			[parse and add all get variables
		*****************************************/
		$getParameters = array();
		if($_POST['vars']['urlGetVars']!=''){
			parse_str(substr($_POST['vars']['urlGetVars'],1), $getParameters);/*loose the '?'  */
		}

		/*********build the location url making sure permalinks are taken care of too**/
		$location=$this->wppizza_set_redirect_url($_POST['vars']['locHref'],$getParameters);

	}


	$vars['location']=$location;

	print"".json_encode($vars)."";
exit();
}
/************************************************************************************************
*
*
*	[get the confirm order page]
*
*
************************************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='confirmorder'){
	header('Content-type: text/html');
		/***************************************************************
			[get and parse all user post variables and save in session
		***************************************************************/
		if(count($_POST['vars']['data'])>0){
			$param=$this->wppizza_sessionise_userdata($_POST['vars']['data'],$options['order_form']);
			/**add hash**/
			$atts['hash']=!empty($param['wppizza_hash']) ? wppizza_validate_string($param['wppizza_hash']) : '';
			/**add used gateway*/
			$atts['gateway']=!empty($param['wppizza-gateway']) ? wppizza_validate_string($param['wppizza-gateway']) : '';
		}		
		ob_start();
		$this->wppizza_include_shortcode_template('confirmationpage',$atts);
		$markup = ob_get_clean();
		
	print"".$markup;
	exit();
}
/************************************************************************************************
*
*
*	[create a nonce]
*
*
************************************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='nonce' && isset($_POST['vars']['val'])){
	$nonceType=$_POST['vars']['val'];
	$nonce=''.wp_nonce_field( 'wppizza_nonce_'.$nonceType.'','wppizza_nonce_'.$nonceType.'',false, false).'';
print"".$nonce;
exit();
}
/************************************************************************************************
*
*	[(try to) add new account, registering email as username]
*	[if it already exists, just send the username and password again]
*	[if it fails. just ignore]
*
************************************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='new-account'){

	/*****************************************
		[get and parse all post variables
	*****************************************/
	$postedVars = array();
	parse_str($_POST['vars']['data'], $postedVars);
	$postedVars = apply_filters('wppizza_filter_sanitize_post_vars', $postedVars);
	$output['pVar']=$postedVars;
	/****************************************
		verify nonce first
	******************************************/
	if (isset($postedVars['wppizza_nonce_register']) && wp_verify_nonce($postedVars['wppizza_nonce_register'],'wppizza_nonce_register') ){

		/************************************************
			check if email exists already
			if it does not carry on adding account
		************************************************/
		$user_id = username_exists( $postedVars['cemail'] );
		$email_id = email_exists( $postedVars['cemail'] );

		/**new user**/
		if(!$user_id && !$email_id){
			/************************************************************************************
				we do NOT only want to save form fields here that are set to "use for registering"
				but update / add all enabled ones, so let's change the action/method
				and set distinct POST vars
			************************************************************************************/
			remove_action('user_register', array( $this, 'wppizza_user_register_form_save_fields' ),100 );
			add_action('user_register', array( $this, 'wppizza_user_register_order_page' ),100 );
			$_POST=array();
			foreach($postedVars as $k=>$v){
				$_POST[$k]=$v;
			}
			/*generate a pw**/
			$user_password = wp_generate_password( $length=10, $include_standard_special_chars=true );
			/*create the user**/
			$user_id_new = wp_create_user( $postedVars['cemail'], $user_password, $postedVars['cemail'] );
			/**this should never happen really**/
			if(is_wp_error($user_id_new)){
				$output['error']="<div class='wppizza-login-error'>Error: ".$user_id_new->get_error_message()."</div>";
			}
			/*send un/pw to user*/
			if($user_id_new && $user_password!=''){/*bit of overkill*/
				wp_new_user_notification( $user_id_new, $user_password );
				wp_set_auth_cookie( $user_id_new );/**login too*/
			}
		}else{
			$output['error']="<div class='wppizza-login-error'>".$options['localization']['register_option_create_account_error']['lbl']."</div>";
		}
	}
	print"".json_encode($output);/*not outputted but may one day come in handy for debug purposes*/
	exit();
}

/***************************************************************
*
*
*	[profile update]
*
*
***************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='profile_update'){
	/*****************************************
		[get and parse all post variables to get hash
	*****************************************/
	$params = array();
	parse_str($_POST['vars']['data'], $params);
	/*****************************************
		[get the order]
	*****************************************/
	global $wpdb;
	$order = $wpdb->get_row("SELECT id,order_ini FROM ".$wpdb->prefix . $this->pluginOrderTable." WHERE hash='".$params['wppizza_hash']."' ");
	$oDetails=maybe_unserialize($order->order_ini);
	$oDetails['update_profile']=1;
	/*update order to say we want to update profile when done**/
	$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET order_ini='".maybe_serialize($oDetails)."'WHERE id='".$order->id."' ");
exit();
}

/***************************************************************
*
*
*	[tip added ]
*
*
***************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='add_tips'){

	/***************************************************************
		[get and parse all user post variables and save in session and return parsed $params
	***************************************************************/
	$params = $this->wppizza_sessionise_userdata($_POST['vars']['data'],$options['order_form']);

	/*****************************************
		[sanitize gratuity]
	*****************************************/
	$tips=wppizza_validate_float_only($params['ctips'],2);
	global $wpdb;
	/*might as well delete the previously initialized order. So we do not delete arbitrary stuff when messing with the hash, restrict to INITIALIZED and orders of 3 minutes or less. Ought to be reasonably safe**/
	$res=$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->prefix . $this->pluginOrderTable." WHERE hash=%s AND payment_status='INITIALIZED' AND order_date > TIMESTAMPADD(MINUTE,-3,NOW()) ",$params['wppizza_hash']));

	/**add tips distincly to session*/
	$_SESSION[$this->pluginSession]['tips']=$tips;

	/*****************************************
		[parse and add all get variables
	*****************************************/
	$getParameters = array();
	if($_POST['vars']['urlGetVars']!=''){
		parse_str(substr($_POST['vars']['urlGetVars'],1), $getParameters);/*loose the '?'  */
	}

	/*********build the location url making sure permalinks are taken care of too**/
	$location=$this->wppizza_set_redirect_url($_POST['vars']['locHref'],$getParameters);


	$vars['location']=$location;

	print"".json_encode($vars)."";
exit();
}
/************************************************************************************************
*
*
*	[choose and set gateway to calculate charges]
*
*
************************************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='wppizza-select-gateway'){
	
	if(count($_POST['vars']['data'])>0){
		$this->wppizza_sessionise_userdata($_POST['vars']['data'],$options['order_form']);
	}
	
	if(isset($_SESSION[$this->pluginSessionGlobal]['userdata']['gateway'])){
		unset($_SESSION[$this->pluginSessionGlobal]['userdata']['gateway']);
	}
	$_SESSION[$this->pluginSessionGlobal]['userdata']['gateway']=strtoupper(wppizza_validate_string($_POST['vars']['selgw']));
	print"".json_encode($_SESSION[$this->pluginSessionGlobal]['userdata'])."";/*not being output anywhere though*/
	exit();
}
/************************************************************************************************
*
*
*	[send the order by email and update db]
*
*
************************************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='sendorder'){
	/**********set header********************/
	header('Content-type: text/plain');

	/*****************************************
		[get and parse all post variables
	*****************************************/
	$params = array();
	parse_str($_POST['vars']['data'], $params);
	/*****************************************
		[get the order]
	*****************************************/
	global $wpdb;
	$orderId=false;
	$order = $wpdb->get_row("SELECT id,payment_status FROM ".$wpdb->prefix . $this->pluginOrderTable." WHERE hash='".$params['wppizza_hash']."' ");
	if($order){
		$orderId=$order->id;
		$orderStatus=$order->payment_status;
	}

	/********************************************
		[new send order email class]
	********************************************/
	$sendEmail=new WPPIZZA_SEND_ORDER_EMAILS;

	/************************************************************
	*	[provided we have a valid order id AND its set
	*	to INITIALIZE send the emails and update db]
	*	returns array
	*	['status']->true/false
	*	['error']->''/msg
	*	['mailer']->mail function name
	*
	************************************************************/
	/*default errors**/
	$mailResults['error']=__('Sorry, we could not find this order.',$this->pluginLocale);
	$mailResults['mailer']='Error'; /**just so we dont have an abandoned colon**/
	if($orderId){
		if($orderStatus=='INITIALIZED'){

			/**update the db**/
			$now=time();
			//$thisOrderTransactionId='COD'.$now.$orderId.'';
			$thisOrderPostVars = apply_filters('wppizza_filter_sanitize_post_vars', $params);
			$gatewayUsed=strtoupper($thisOrderPostVars['wppizza-gateway']);
			$thisOrderPostVars=esc_sql(serialize($thisOrderPostVars));
			$thisOrderTransactionId=$gatewayUsed.$now.$orderId.'';


			$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET
			transaction_id='".$thisOrderTransactionId."',
			initiator='".$gatewayUsed."',
			customer_ini='".$thisOrderPostVars."'
			WHERE id='".$orderId."' ");


			/**send the email***/
			$mailResults=$sendEmail->wppizza_order_send_email($orderId);

			/**update again to see if mail was sent successfully**/
			$updateDb=true;

		}else{
			$mailResults['error']=__('This order has already been processed',$this->pluginLocale);
		}
	}
	/***update order******/
	if(isset($updateDb)){
		if($mailResults['status']){
			$mailSent='Y';
			$mailError='';
			$paymentStatus='COMPLETED';
			$transactionDetails=__('SUCCESS',$this->pluginLocale);
		}else{
			$mailSent='N';
			$mailError=serialize($mailResults['error']);
			$paymentStatus='FAILED';
			$transactionDetails=__('FAILED: Sending of Mail Failed',$this->pluginLocale);
		}
		/*update order**/
		$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET
		transaction_details='".$transactionDetails."',
		payment_status='".$paymentStatus."',
		customer_details='".$sendEmail->customerDetails."',
		order_details='".$sendEmail->orderDetails."',
		mail_sent='".$mailSent."',
		mail_error='".$mailError."'
		WHERE id='".$orderId."' ");

		/**do additional stuff when order has been executed*/
		do_action('wppizza_on_order_executed', $orderId , $this->pluginOrderTable);

	}


	/********************************************************************
	*
	*	[depending on $mailResults['status'],
	*	will insert order into db and output "thankyou"
	*	or justs displays error]
	*
	********************************************************************/
	$output=$sendEmail->wppizza_order_results($mailResults,$orderId);

	print"".$output."";
exit();
}
/************************************************************************************************
*
*	[using a cache plugin, load full cart dynamically]
*
************************************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='hasCachePlugin'){
		ob_start();
		$attributes=json_decode(stripslashes($_POST['vars']['attributes']),true);
		$this->wppizza_include_shortcode_template('cart',$attributes);
		$markup = ob_get_clean();
	print"".$markup."";
exit();
}
exit();
?>