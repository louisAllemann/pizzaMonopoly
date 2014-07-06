<?php
if (!class_exists( 'WPPizza' ) ) {return ;}
class WPPIZZA_GATEWAYS extends WPPIZZA {
	protected $pluginGateways;
	private $gatewayOrderDetails;
	private $gatewayOrderId;
	function __construct() {
    	parent::__construct();

		add_action('init', array( $this, 'wppizza_add_default_gateways'));/*add default COD gateway*/
		/************************************************************************
			[runs only for frontend]
		*************************************************************************/
		if(!is_admin()){
			add_action('init', array( $this, 'wppizza_instanciate_gateways_frontend'));
			add_action('init', array( $this, 'wppizza_do_gateways'));/**output available gateway choices on order page**/
			add_action('init', array( $this,'wppizza_gateway_initialize_order'));/*initialize oder into db */
		}

		/************************************************************************
			[load wpml from parent. used in ajax call, so must be available front and backend ]
		*************************************************************************/
		add_action('init', array( $this, 'wppizza_wpml_localization'),99);

		/************************************************************************
			[runs only in backend]
		*************************************************************************/
		add_action('admin_init', array( $this, 'wppizza_available_gateways'),1);/**check if a gateways has been (un)installed and if so, update option**/
		add_action('admin_init', array( $this, 'wppizza_load_gateways_admin'));

	}

	function wppizza_do_gateways() {
		add_action('wppizza_choose_gateway', array( $this, 'wppizza_choose_gateway'));
	}

	function wppizza_load_gateways_admin() {
		$allClasses=get_declared_classes();
		foreach ($allClasses AS $class){
			$chkStr=substr($class,0,16);
			if($chkStr=='WPPIZZA_GATEWAY_'){
				$c=new $class;
			}
		}
	}

	function wppizza_instanciate_gateways_frontend() {

		/**display surcharges pre order**/
		$this->showSurchageBeforOrder=false;

		/**get the selected gateway and associated classname*/
		$wppizzaGatewayCount=0;
		$wppizzaGateway=array();
		$wppizzaGatewayOptions=array();
		
				
		if(isset($this->pluginOptions['gateways']['gateway_selected']) && is_array($this->pluginOptions['gateways']['gateway_selected'])){
		foreach($this->pluginOptions['gateways']['gateway_selected'] as $gw=>$enbld){
			if($enbld){/**only add enabled gateways**/
				$gatewayClass="WPPIZZA_GATEWAY_".strtoupper($gw);
				$wppizzaGateway[$gw]=new $gatewayClass;
				$wppizzaGatewayOptions[$gw]=$wppizzaGateway[$gw];


				/***  to display surcharges in orderpage prior to ordering****/
				/**set first gateway**/
				if($wppizzaGatewayCount==0){
					$this->pluginGatewaySelected=$gw;
				}
				/***check if surcharges are calculated by the gateway rather than set by admin**/
				//$wppizzaGateway[$gw]->gatewaySurchargeAtCheckout=false;
				if(isset($wppizzaGateway[$gw]->gatewaySurchargeAtCheckout)){
					$this->showSurchageBeforOrder=true;
				}


				/***check (for legacy reasons) if we have the relevant vars in gateway plugin to calculate surcharges on order page **/
				if(isset($wppizzaGateway[$gw]->gatewaySurchargePercent) && isset($wppizzaGateway[$gw]->gatewaySurchargeFixed)){
					$wppizzaGateway[$gw]->surchargePc=$wppizzaGateway[$gw]->gatewayOptions[$wppizzaGateway[$gw]->gatewaySurchargePercent];
					$wppizzaGateway[$gw]->surchargeFixed=$wppizzaGateway[$gw]->gatewayOptions[$wppizzaGateway[$gw]->gatewaySurchargeFixed];
					/**check if any of the values are >0 **/
					if($wppizzaGateway[$gw]->gatewayOptions[$wppizzaGateway[$gw]->gatewaySurchargePercent]>0 || $wppizzaGateway[$gw]->gatewayOptions[$wppizzaGateway[$gw]->gatewaySurchargeFixed]>0 ){
						$this->showSurchageBeforOrder=true;/*if any gateway has surcharges>0 calculate and display**/
					}
				}else{
						$wppizzaGateway[$gw]->surchargePc=0;
						$wppizzaGateway[$gw]->surchargeFixed=0;
				}
			$wppizzaGatewayCount++;
			}
		}}
		/**all gateways have been disabled, set some defaults**/
		if(!isset($this->pluginGatewaySelected)){
			$_SESSION[$this->pluginSession]['gateway-selected']['gw']='--nogatewayavailable--';
			$_SESSION[$this->pluginSession]['gateway-selected']['surchargePc']=0;
			$_SESSION[$this->pluginSession]['gateway-selected']['surchargeFixed']=0;
			$_SESSION[$this->pluginSession]['gateway-selected']['surchargeAtCheckout']=false;		
		
		}
		/**if we have not yet set a gateway , use the first available one **/
		if(!isset($_SESSION[$this->pluginSessionGlobal]['userdata']['gateway']) && isset($this->pluginGatewaySelected)){
			$_SESSION[$this->pluginSession]['gateway-selected']['gw']=strtolower($this->pluginGatewaySelected);
			$_SESSION[$this->pluginSession]['gateway-selected']['surchargePc']=$wppizzaGateway[$this->pluginGatewaySelected]->surchargePc;
			$_SESSION[$this->pluginSession]['gateway-selected']['surchargeFixed']=$wppizzaGateway[$this->pluginGatewaySelected]->surchargeFixed;
			$_SESSION[$this->pluginSession]['gateway-selected']['surchargeAtCheckout']=!empty($wppizzaGateway[$this->pluginGatewaySelected]->gatewaySurchargeAtCheckout) ? true:false;
		}
		/***switch gw via ajax and reload page*****/
		if(isset($_SESSION[$this->pluginSessionGlobal]['userdata']['gateway'])){
			$selGw=$_SESSION[$this->pluginSessionGlobal]['userdata']['gateway'];
			/**lets just make double sure this exists in case soemone feels the need to mess around with the html values**/
			if(isset($wppizzaGateway[$selGw])){
				if(isset($_SESSION[$this->pluginSession]['gateway-selected'])){
					unset($_SESSION[$this->pluginSession]['gateway-selected']);
				}
				$_SESSION[$this->pluginSession]['gateway-selected']['gw']=strtolower($selGw);
				$_SESSION[$this->pluginSession]['gateway-selected']['surchargePc']=$wppizzaGateway[$selGw]->surchargePc;
				$_SESSION[$this->pluginSession]['gateway-selected']['surchargeFixed']=$wppizzaGateway[$selGw]->surchargeFixed;
				$_SESSION[$this->pluginSession]['gateway-selected']['surchargeAtCheckout']=!empty($wppizzaGateway[$selGw]->gatewaySurchargeAtCheckout) ? true:false;
			}
		}

		/**add a hidden flag in frontend to display surcharges on gateway change and set initial session**/
		if($this->showSurchageBeforOrder){
			add_action('wppizza_choose_gateway',array($this,'wppizza_recalculate_handling'));
		}

		$this->pluginGateways=$wppizzaGateway;

	return $wppizzaGatewayOptions;
	}

/****************************************************************************
*
*	[Gateway Recalc Handling Charge]
*	[check if we need to recalculate handling charges on order page]
****************************************************************************/
	function wppizza_recalculate_handling(){
		print"<input type='hidden' id='wppizza_calc_handling' />";
	}
/***********************************************
	[include default COD "gateway"]
***********************************************/
	function wppizza_add_default_gateways() {
		require_once(WPPIZZA_PATH .'classes/wppizza-gateway-cod.php');
	}

	/**display choices***/
	function wppizza_choose_gateway(){
		$displayAsDropdown=$this->pluginOptions['gateways']['gateway_select_as_dropdown'];
		$selectLabel=$this->pluginOptions['gateways']['gateway_select_label'];
		$enabledGateways=$this->pluginGateways;
		if(count($enabledGateways)>0){
			/**display choice of more than one**/
			if(count($enabledGateways)>1){
				print"<div class='wppizza-gateways'>";
					/**as dropdown**/
					if($displayAsDropdown){
						print"<label class='wppizza-gw-label'>".$selectLabel."</label>";
						print"<select name='wppizza-gateway' /> ";
						$i=0;
						foreach($enabledGateways as $key=>$gw){
							$key=strtolower($key);
							/*******************************************************************************
								if we want to submit directly via ajax (other than cod which does this anyway),
								without sending to any gateway (lets say bacs or something)
								check if $this->gatewaySubmit isset and set to ajax and add class as required
								so we can identify this
							********************************************************************************/
							$gwAddClass='';
							if(isset($gw->gatewayTypeSubmit) && $gw->gatewayTypeSubmit=='ajax'){
								$gwAddClass=' class="wppizzaGwAjaxSubmit"';
							}
							if(isset($gw->gatewayTypeSubmit) && $gw->gatewayTypeSubmit=='custom'){/*customised*/
								$gwAddClass=' class="wppizzaGwCustom"';
							}
							print"<option value='".$key."' ".$gwAddClass." ".selected($_SESSION[$this->pluginSession]['gateway-selected']['gw'],$key,false)." />";
								print"".empty($gw->gatewayOptions['gateway_label']) ? $gw->gatewayName : $gw->gatewayOptions['gateway_label'] ." ";
							print"</option>";

						$i++;
						}
						print"</select>";
					}else{
						print"<label class='wppizza-gw-label'>".$selectLabel."</label>";
						$i=0;
						foreach($enabledGateways as $key=>$gw){
							$key=strtolower($key);
							/*******************************************************************************
								if we want to submit directly via ajax (other than cod which does this anyway),
								without sending to any gateway (lets say bacs or something)
								check if $this->gatewaySubmit isset and set to ajax and add class as required
								so we can identify this
							********************************************************************************/
							$gwAddClass='';
							if(isset($gw->gatewayTypeSubmit) && $gw->gatewayTypeSubmit=='ajax'){
								$gwAddClass=' class="wppizzaGwAjaxSubmit"';
							}
							if(isset($gw->gatewayTypeSubmit) && $gw->gatewayTypeSubmit=='custom'){/*customised*/
								$gwAddClass=' class="wppizzaGwCustom"';
							}
							print"<div id='wppizza-gw-".$key."' class='wppizza-gw-button button'>";
									print"<label>";
									print"<input type='radio' name='wppizza-gateway' id='wppizza-gateway-".$key."' ".$gwAddClass." value='".$key."' ".checked($_SESSION[$this->pluginSession]['gateway-selected']['gw'],$key,false)."/> ";
									print"".!empty($gw->gatewayImage) ? $gw->gatewayImage : '' ." ";
									print"".empty($gw->gatewayOptions['gateway_label']) ? $gw->gatewayName : $gw->gatewayOptions['gateway_label'] ." ";
									print"</label>";
									print"".!empty($gw->gatewayOptions['gateway_info']) ? '<span class="wppizza-gateway-addinfo">'.$gw->gatewayOptions['gateway_info'].'</span>' : '' ." ";
							print"</div>";
						$i++;
						}
					}
				print"</div>";
				echo $this->wppizza_gateway_standard_button();
			}

			/**only one gateway just display button and add hidden field**/
			if(count($enabledGateways)==1){
				foreach($enabledGateways as $key=>$gw){
					$key=strtolower($key);
					/*******************************************************************************
						if we want to submit directly via ajax (other than cod which does this anyway),
						without sending to any gateway (lets say bacs or something)
						check if $this->gatewaySubmit isset and set to ajax and add class as required
						so we can identify this
					********************************************************************************/
					$gwAddClass='';
					if(isset($gw->gatewayTypeSubmit) && $gw->gatewayTypeSubmit=='ajax'){
						$gwAddClass=' class="wppizzaGwAjaxSubmit"';
					}
					if(isset($gw->gatewayTypeSubmit) && $gw->gatewayTypeSubmit=='custom'){/*customised*/
								$gwAddClass=' class="wppizzaGwCustom"';
					}
					/**add hidden value so the ajax call knows whether its cod or anything else*/
					print"<input type='hidden' name='wppizza-gateway' id='wppizza-gateway-".$key."' ".$gwAddClass." value='".$key."' /> ";
					/**if this method has not been defined in class or is empty, display standard button**/
					if(method_exists($gw,'gateway_button') && $gw->gateway_button()!=''){
						echo $gw->gateway_button();
					}else{
						echo $this->wppizza_gateway_standard_button();
					}
				}
			}
		}
	}
	/***********************************************
	[standard "send order" button if not defined or empty in class]
	 MUST have class=wppizza-ordernow]
	***********************************************/
	function wppizza_gateway_standard_button() {
		$cart=wppizza_order_summary($_SESSION[$this->pluginSession],$this->pluginOptions , 'gatewaybuttons' );
		if($cart['nocheckout']!=''){
			$standardButton='<div class="wppizza-order-nocheckout">'.$cart['nocheckout'].'</div>';
		}else{
			$standardButton='<input class="submit wppizza-ordernow" type="submit" style="display:block" value="'.$this->pluginOptions['localization']['send_order']['lbl'].'" />';
		}
		return $standardButton;
	}
	/***********************************************
	*
	*	[array of items, tax, delivery charges etc]
	*	[(can be) used when creating/storing hash etc in
	*	in db for paymant gatways]
	*
	***********************************************/
	function wppizza_gateway_order_details($addVars=array()) {
		$gatewayOrder=array();
		$cartDetails=wppizza_order_summary($_SESSION[$this->pluginSession],$this->pluginOptions, 'orderdetails');
		$gatewayOrder['currencyiso']=$cartDetails['currencyiso'];
		$gatewayOrder['currency']=$cartDetails['currency'];
		foreach($cartDetails['items'] as $k=>$v){
			$gatewayOrder['item'][$k]['postId']=''.$v['postId'].'';
			$gatewayOrder['item'][$k]['name']=''.$v['name'].'';
			$gatewayOrder['item'][$k]['size']=''.$v['size'].'';
			$gatewayOrder['item'][$k]['count']=''.$v['count'].'';
			$gatewayOrder['item'][$k]['quantity']=''.$v['count'].'';/*legacy some customised templates may use this*/
			$gatewayOrder['item'][$k]['price']=''.wppizza_validate_float_only($v['price']).'';
			$gatewayOrder['item'][$k]['pricetotal']=''.wppizza_validate_float_only($v['pricetotal']).'';
			$gatewayOrder['item'][$k]['categories']=$v['categories'];
			/**add any additional info to name*/
			$addInfo=array();
			if(is_array($v['additionalinfo']) && count($v['additionalinfo'])>0){foreach($v['additionalinfo'] as $additionalInfo){
				$addInfo[]=''.$additionalInfo.'';
			}}
			//$gatewayOrder['item'][$k]['additionalinfo']=implode("",$addInfo);
			$gatewayOrder['item'][$k]['additionalinfo']=$addInfo;
			$gatewayOrder['item'][$k]['additionalInfo']=implode(" ",$addInfo);/*legacy paypal and order thank you page (note upper case I)*/
			$gatewayOrder['item'][$k]['extend']=$v['extend'];
			$gatewayOrder['item'][$k]['extenddata']=$v['extenddata'];/**to store data (keys, id's count, prices  etc) in the db to maybe retrieve later, put it in this key**/
			$gatewayOrder['item'][$k]['catIdSelected']=wppizza_validate_int_only($v['catIdSelected']);/**store selected category.**/
		}

		$gatewayOrder['total_price_items']=wppizza_validate_float_only($cartDetails['order_value']['total_price_items']['val']);
		$gatewayOrder['discount']=wppizza_validate_float_only($cartDetails['order_value']['discount']['val']);
		$gatewayOrder['taxrate']=wppizza_validate_float_only($cartDetails['taxrate']);
		$gatewayOrder['item_tax']=wppizza_validate_float_only($cartDetails['order_value']['item_tax']['val']);
		$gatewayOrder['taxes_included']=wppizza_validate_float_only($cartDetails['order_value']['taxes_included']['val']);

		$gatewayOrder['delivery_charges']=!empty($cartDetails['order_value']['delivery_charges']['val']) ? wppizza_validate_float_only($cartDetails['order_value']['delivery_charges']['val']) : '';
		$gatewayOrder['tips']=!empty($cartDetails['tips']['val']) ? wppizza_validate_float_only($cartDetails['tips']['val']) : '';

		$gatewayOrder['selfPickup']=!empty($cartDetails['selfPickup']) ? wppizza_validate_int_only($cartDetails['selfPickup']) : 0;
		$gatewayOrder['total']=wppizza_validate_float_only($cartDetails['order_value']['total']['val']);

		/**add any additional variables are set we want to pass/hash*/
		foreach($addVars as $k=>$v){
			$gatewayOrder[$k]=$v;
		}

		/*****created and return checkable hash**/
		$cartHash=wppizza_mkHash($gatewayOrder);/*make unique hash*/
		$gatewayOrder['hash']=$cartHash['hash'];/*add hash to array*/
		$gatewayOrder['order_ini']=$cartHash['order_ini'];/*add orig hash string to array*/

		return $gatewayOrder;
	}
	/***********************************************
		[check if gateways have changed/been (de)activated
		and update option accordingly (provided its not
		first install or old version anyway]
	***********************************************/
	function wppizza_available_gateways() {
		if(isset($this->pluginOptions['gateways']['gateway_selected'])){
		/**variable to store current gateways option**/
		$currentGateways=$this->pluginOptions['gateways']['gateway_selected'];

		/*get available gateway classes, **/
		$availableGateways=array();
		foreach (get_declared_classes() AS $class){
			$chkStr=substr($class,0,16);
			$iDent=substr($class,16);
			if($chkStr=='WPPIZZA_GATEWAY_'){
					$availableGateways[$iDent]=false;
			}
		}

		/**unset gateway from option if it's not in available classes****/
		foreach($this->pluginOptions['gateways']['gateway_selected'] as $k=>$v){
			if(!in_array($k,array_keys($availableGateways))){
				unset($this->pluginOptions['gateways']['gateway_selected'][$k]);
			}
		}

		/**get all additionally enabled gateways and initialise as disabled**/
		foreach($availableGateways as $k=>$v){
			if(!in_array($k,array_keys($currentGateways))){
				$this->pluginOptions['gateways']['gateway_selected'][$k]=false;
			}
		}

		/**gateway array has changed-> update option*/
		if($currentGateways!=$this->pluginOptions['gateways']['gateway_selected']){
			$updateOptions=$this->pluginOptions;
			/**overwrite gateway selected with new array**/
			$updateOptions['gateways']['gateway_selected']=$this->pluginOptions['gateways']['gateway_selected'];
			/**update options**/
			update_option($this->pluginSlug, $updateOptions );
		}

	}}
	/************************************************************************************************
	*
	*	[initialize an order on order page with hash etc so we can later compare via ipn,
	*	provided at least one gateway is available and we are not just returning to site via cancel ]
	*
	************************************************************************************************/
	function wppizza_gateway_initialize_order(){
		if(count($this->pluginGateways)>0){
			add_action('wppizza_choose_gateway', array($this,'gateway_set_order_details'));
			add_action('wppizza_choose_gateway', array($this,'gateway_db_initialize_order'));
			add_action('wppizza_choose_gateway', array($this, 'gateway_form_fields'));
			/**add a do action hook that has order id and details which can be used elsewhere **/
			add_action('wppizza_order_form_after', array($this, 'gateway_order_details_hook'));
		}
	}
	/********************************************************************
	*
	*	[returns order details and a hash made from those details,
	*	to store and check against later.
	*	additional variables added by sending array to this function
	*	(in this case  a timestamp) to make the order unique]
	*
	********************************************************************/
	function gateway_set_order_details(){
		/*In case microtime is available use it*/
		if(function_exists('microtime')){
			$timestamp=microtime(true);
		}else{
			$timestamp=time();
		}
		$this->gatewayOrderDetails=$this->wppizza_gateway_order_details(array('time'=>$timestamp));
	}
	/******************************************************************
	*
	*	[initialize/insert order in db when going to order page ]
	*
	******************************************************************/
	function gateway_db_initialize_order() {
		global $wpdb,$current_user;
		get_currentuserinfo();
		//$wpdb->hide_errors();
		$wpdb->query( $wpdb->prepare( "INSERT INTO ".$wpdb->prefix . $this->pluginOrderTable." ( wp_user_id, hash, order_ini, payment_status )VALUES ( %s, %s, %s , %s )", array($current_user->ID, $this->gatewayOrderDetails['hash'], $this->gatewayOrderDetails['order_ini'],'INITIALIZED')));
		$this->gatewayOrderId=$wpdb->insert_id;
	}
	/*some action hooks to do somthing with the order details that have been  inserted into db**/
	function gateway_order_details_hook(){
		do_action('wppizza_gateway_do_order_details', $this->gatewayOrderDetails, $this->gatewayOrderId);
	}
	/******************************************************************
	*
	*	[add hash formfield to check against when sending to gateway]
	*
	******************************************************************/
	function gateway_form_fields(){
		$formFields='';
		$formFields='<input type="hidden" name="wppizza_hash" value="'.$this->gatewayOrderDetails['hash'].'" />';
		print $formFields;
	}
	/******************************************************************
	*
	*	[output order on thank you page]
	*
	******************************************************************/
	function gateway_order_on_thankyou($id){
		$orderEmails=new WPPIZZA_SEND_ORDER_EMAILS;
		$orderDetails=$orderEmails->gateway_order_on_thankyou($id);
		return	$orderDetails;
	}
	/******************************************************************
	*
	*	[output unset cart]
	*
	******************************************************************/
	function gateway_unset_cart() {
	 	if (!session_id()) {session_start();}
	    /*holds items in cart*/
	    $_SESSION[$this->pluginSession]['items']=array();
	    /*gross sum of all items in cart,before discounts etc*/
	    $_SESSION[$this->pluginSession]['total_price_items']=0;
	    /**gratuities**/
	    $_SESSION[$this->pluginSession]['tips']=0;
	}
/****************************************************************************************************************************
*
*
*
*	[methods - new as of v2.9.3 - to aid gateway development]
*	be aware that these - for the moment - are still subject to change !!!
*
*
*****************************************************************************************************************************/

	/**************************************************************************
	*
	*	[install/update gateway options - update runs when version number increases]
	*
	**************************************************************************/
	function wppizza_gateway_admin_init(){
		/*first install*/
		if($this->gatewayOptions==0){
			$editableOptions = $this->gateway_settings(true);
			$nonEditableOptions = $this->gateway_settings_non_editable();
			$defaultOptions=array_merge($editableOptions,$nonEditableOptions);
			/**insert options**/
			update_option($this->gatewayOptionsName, $defaultOptions );
		}else{
			/*update options if version is higher than current*/
			//$forceUpdate=1;//development only
			if(!isset($this->gatewayOptions['version']) || version_compare($this->gatewayOptions['version'],$this->gatewayVersion , '<' ) || isset($forceUpdate)){
				/**currently set options**/
				$currentOptions=$this->gatewayOptions;

				/*get and merge editable and uneditable**/
				$editableOptions = $this->gateway_settings(true);
				$nonEditableOptions = $this->gateway_settings_non_editable();
				$defaultOptions=array_merge($editableOptions,$nonEditableOptions);

				/*get and add all newly added options**/
				$addedOptions=array_diff_key($defaultOptions,$currentOptions);/**get new options**/
				$updateOptions=$currentOptions;
				foreach($addedOptions as $k=>$v){
					$updateOptions[$k]=$v;/*add new options*/
				}
				/*overwrite noneditable/fixed options**/
				foreach($nonEditableOptions as $k=>$v){
					$updateOptions[$k]=$v;
				}
				/**now update the options***/
				update_option($this->gatewayOptionsName, $updateOptions );
			}
		}

	}

	/**************************************************************************
	*
	*	[get order by hash and/or id and blogid (if set) and payment status (if set)
	*	and initiator (if set) and unserialize order_details and customer_details]
	*
	**************************************************************************/
	function wppizza_gateway_get_order_details($orderhash=false, $orderId=false, $blogid=false, $payment_status=false, $initiator=false){
		if(!$orderhash && !$orderId){
			return false;
		}
		global $wpdb;
		//$wpdb->hide_errors();
		/**sanitize hash**/
		if($orderhash){
			$orderhash=wppizza_validate_alpha_only($orderhash);
		}
		/**sanitize id**/
		if($orderId){
			$orderId=(int)$orderId;
		}
		/**select the right blog table if set **/
		if($blogid &&  (int)$blogid>1){
			$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';
		}
		/**set payment status query **/
		if($payment_status && is_array($payment_status)){
			$psQuery=" AND payment_status IN ('".implode("','",$payment_status)."') ";
		}else{
			$psQuery="";
		}
		/**initiator query **/
		$iniQuery="";
		if($initiator){
			$iniQuery=" AND initiator='".esc_sql($initiator)."' ";
		}

		/**build where query**/
		$wQuery='';
		if($orderId && !$orderhash){$wQuery=" id='".$orderId."' ";}
		if(!$orderId && $orderhash){$wQuery=" hash='".$orderhash."' ";}
		if($orderId && $orderhash){$wQuery=" hash='".$orderhash."' AND id='".$orderId."' ";}


		$getOrderDetails = $wpdb->get_row("SELECT wp_user_id, id, hash, order_ini as order_details, customer_ini as customer_details, payment_status FROM " . $wpdb->prefix . $this->pluginOrderTable . " WHERE ".$wQuery." ".$psQuery." ".$iniQuery." LIMIT 0,1 ");
		if(is_object($getOrderDetails)){
			/**unserialize order**/
			$getOrderDetails->order_details=maybe_unserialize($getOrderDetails->order_details);
			/**unserialize customer**/
			$getOrderDetails->customer_details=maybe_unserialize($getOrderDetails->customer_details);
			/**return details**/
			return	$getOrderDetails;
		}else{
			return false;
		}
	}


	/******************************************************************
	*
	*	[update db entry with order variables]
	*
	******************************************************************/
	function wppizza_gateway_update_order_details($order, $blogid=false){
		global $wpdb;
		//$wpdb->hide_errors();
		$orderId=$order->id;
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}
		
		/***check if surcharges are enabled***/
		if(isset($this->gatewaySurchargePercent) || isset($this->gatewaySurchargeFixed) ){
			/***************************
		 		[get tips (if any)]
			***************************/
			$tips=0;
			if(isset($order->order_details['tips']) && $order->order_details['tips']>0){
				$tips=$order->order_details['tips'];
			}		
			/*********************************************************
			 	[add % handling fee to total, but not on tips if percentage]
			*********************************************************/			
			$ppSurcharge=0;
			$appliedSurcharge=0;
			$order->order_details['surcharge']=0;
			/**calculate surcharge percentages**/			
			if(isset($this->gatewayOptions[$this->gatewaySurchargePercent]) && $this->gatewayOptions[$this->gatewaySurchargePercent]>0){	
				$ppSurcharge+=($order->order_details['total']-$tips)/100*abs($this->gatewayOptions[$this->gatewaySurchargePercent]);
			}
			/**calculate surcharge fixed**/	
			if(isset($this->gatewayOptions[$this->gatewaySurchargeFixed]) && $this->gatewayOptions[$this->gatewaySurchargeFixed]>0){
				$ppSurcharge+=$this->gatewayOptions[$this->gatewaySurchargeFixed];
			}
			/**add to order details**/
			if($ppSurcharge>0){
				$appliedSurcharge=wppizza_round_up($ppSurcharge,2);
				$order->order_details['total']=$order->order_details['total']+$appliedSurcharge;
				$order->order_details['handling_charge']=$appliedSurcharge;
			}
		}
		$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET customer_ini='".wppizza_filter_sanitize_post_vars($_POST)."', initiator='".esc_sql($this->gatewayName)."' , order_ini='".esc_sql(serialize($order->order_details))."'   WHERE id='".$orderId."' ");
	
		/**filter order items**/
		$order->order_details['item'] = apply_filters('wppizza_filter_order_extend', $order->order_details['item']);
	
	
		return $order;
	}
	/******************************************************************
	*
	*	[update db entry with POSTed (customer) vars]
	*
	******************************************************************/
	function wppizza_gateway_update_customer_details($orderId, $postVars){
		global $wpdb;
		//$wpdb->hide_errors();
		$orderId=(int)$orderId;
		/**sanitize post vars**/
		$thisOrderPostVars = apply_filters('wppizza_filter_sanitize_post_vars', $postVars);
		$thisOrderPostVars=esc_sql(serialize($thisOrderPostVars));
		/*update db**/
		$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET customer_ini='".$thisOrderPostVars."' WHERE id='".$orderId."' ");
	}
	/******************************************************************
	*
	*	[order has been successfully charged and captured]
	*
	******************************************************************/
	function wppizza_gateway_order_payment_captured($orderid, $blogid=false, $transaction_id, $transaction_details){
		global $wpdb;
		//$wpdb->hide_errors();
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}
		$wpdb->update(
			$wpdb->prefix  .  $this->pluginOrderTable,
			array('payment_status' => 'CAPTURED','transaction_id' => $transaction_id, 'transaction_details' => maybe_serialize($transaction_details), 'initiator' => esc_sql($this->gatewayName)),
			array('id' => $orderid ),
			array('%s','%s','%s','%s'),
			array('%d')
		);
	}
	/******************************************************************
	*
	*	[order has been refunded]
	*
	******************************************************************/
	function wppizza_gateway_order_payment_refunded($orderid, $blogid=false, $transaction_id, $transaction_details){
		global $wpdb;
		//$wpdb->hide_errors();
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}
		$wpdb->update(
			$wpdb->prefix  .  $this->pluginOrderTable,
			array('payment_status' => 'REFUNDED','order_status' => 'REFUNDED', 'transaction_id' => $transaction_id, 'transaction_details' => maybe_serialize($transaction_details), 'initiator' => esc_sql($this->gatewayName)),
			array('id' => $orderid ),
			array('%s','%s','%s','%s','%s'),
			array('%d')
		);
	}	
	/******************************************************************
	*
	*	[order set to pending]
	*
	******************************************************************/
	function wppizza_gateway_order_payment_pending($orderid, $blogid=false, $transaction_id, $transaction_details){
		global $wpdb;
		//$wpdb->hide_errors();
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}
		$wpdb->update(
			$wpdb->prefix  .  $this->pluginOrderTable,
			array('payment_status' => 'PENDING','transaction_id' => $transaction_id, 'transaction_details' => maybe_serialize($transaction_details), 'initiator' => esc_sql($this->gatewayName)),
			array('id' => $orderid ),
			array('%s','%s','%s','%s'),
			array('%d')
		);
	}	
	/******************************************************************
	*
	*	[payment invalid (mismatched amount or currency for example)]
	*
	******************************************************************/
	function wppizza_gateway_order_payment_invalid($orderid, $blogid=false, $transaction_id, $transaction_details){
		global $wpdb;
		//$wpdb->hide_errors();
		$transaction_id=wppizza_validate_string($transaction_id);/**sanitize**/
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}
		$wpdb->update(
			$wpdb->prefix . $this->pluginOrderTable,
			array('payment_status' => 'INVALID','transaction_id' => $transaction_id, 'transaction_details' => maybe_serialize($transaction_details), 'initiator' => esc_sql($this->gatewayName)),
			array('id' => $orderid ),
			array('%s','%s','%s'),
			array('%d')
		);
	}
	/******************************************************************
	*
	*	[order payment has failed->update db entry]
	*
	******************************************************************/
	function wppizza_gateway_order_payment_failed($orderid, $blogid=false, $error){
		global $wpdb;
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}
		//$wpdb->hide_errors();
		$wpdb->update(
			$wpdb->prefix . $this->pluginOrderTable,
			array('payment_status' => 'FAILED', 'initiator' => esc_sql($this->gatewayName), 'transaction_errors' => maybe_serialize($error)),
			array('id' => $orderid ),
			array('%s','%s','%s'),
			array('%d')
		);
	}
	/******************************************************************
	*
	*	[order has been cancelled using GET vars-> display txt]
	*
	******************************************************************/
	function wppizza_gateway_order_cancelled($orderhash, $blogid=false, $cancelTxt='', $delete=false){
		global $wpdb;
		//$wpdb->hide_errors();
		$orderhash=wppizza_validate_alpha_only($orderhash);/**sanitize**/
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}

		/***check if order exists**/
		$res = $this->wppizza_gateway_get_order_details($orderhash, false, $blogid, array('INITIALIZED','CANCELLED'), $this->gatewayName);
		if($res){
			/**delete cancelled order**/
			if($delete){
				$wpdb->query("DELETE FROM ".$wpdb->prefix . $this->pluginOrderTable." WHERE id=".$res->id."");
			}
			/**update order as cancelled**/
			if(!$delete){
				$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET payment_status='CANCELLED' WHERE id=".$res->id." ");
			}

			print"<div class='wppizza-gateway-success'>".$cancelTxt."</div>";

		}else{
			print"<div class='wppizza-gateway-error'>error [".$this->gatewayIdent."-1101]: ".__('nothing to do !',$this->pluginLocale)."</div>";
		}
		return;
	}

	/******************************************************************
	*
	*	[order has failed using GET vars-> display txt]
	*
	******************************************************************/
	function wppizza_gateway_payment_failed($orderhash, $blogid=false, $failTxt='', $delete=false){
		global $wpdb;
		//$wpdb->hide_errors();
		$orderhash=wppizza_validate_alpha_only($orderhash);/**sanitize**/
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}

		/***check if order exists**/
		$res = $this->wppizza_gateway_get_order_details($orderhash, false, $blogid, array('FAILED'), $this->gatewayName);
		if($res){
			/**delete failed order**/
			if($delete){
				$wpdb->query("DELETE FROM ".$wpdb->prefix . $this->pluginOrderTable." WHERE id=".$res->id."");
			}
			/**now update order as invalid to not display the same thing twice on reload**/
			if(!$delete){
				$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET payment_status='INVALID' WHERE id=".$res->id." ");
			}

			print"<div class='wppizza-gateway-error'>".$failTxt."</div>";

		}else{
			print"<div class='wppizza-gateway-error'>error [".$this->gatewayIdent."-1101]: ".__('nothing to do !',$this->pluginLocale)."</div>";
		}
		return;
	}


	/******************************************************************
	*
	*	[order has been cancelled by ipn request -> no output]
	*	NB: $delete should probably be always false here, so the user can
	*	get feedback when being returned to the cancel URL by gateway
	*
	******************************************************************/
	function wppizza_gateway_order_cancelled_ipn($orderId, $blogid=false, $delete=false){
		global $wpdb;
		$orderId=(int)$orderId;
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}
		/**delete cancelled order**/
		if($delete){
			$res=$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->prefix . $this->pluginOrderTable." WHERE id=%s AND payment_status=%s AND initiator=%s ", $orderId, 'INITIALIZED', esc_sql($this->gatewayName)));
		}
		/**update order as cancelled**/
		if(!$delete){
			$res=$wpdb->query("UPDATE ".$wpdb->prefix . $this->pluginOrderTable." SET payment_status='CANCELLED' WHERE id='".$orderId."' AND payment_status='INITIALIZED' AND initiator='".esc_sql($this->gatewayName)."' ");
		}

	}

	/******************************************************************
	*
	*	[order has been completed -> display txt]
	*
	******************************************************************/
	function wppizza_gateway_order_completed($orderhash, $blogid=false, $txtPending=false, $txtCancelled=false){
		$orderhash=wppizza_validate_alpha_only($orderhash);/**sanitize**/
		$res = $this->wppizza_gateway_get_order_details($orderhash, false, $blogid);

		if($res){
			/**done**/
			if($res->payment_status=='COMPLETED'){
				print"<div class='wppizza-gateway-success'><h1>".$this->pluginOptions['localization']['thank_you']['lbl']."</h1>".nl2br($this->pluginOptions['localization']['thank_you_p']['lbl'])."</div>";
				/**display order details (if enabled)**/
				print"".$this->gateway_order_on_thankyou($res->id)."";
				return;
			}
			/**waiting for ipn response**/
			if($res->payment_status!='COMPLETED' && ($res->payment_status=='CAPTURED' || $res->payment_status=='AUTHORIZED') ){
				print"<div class='wppizza-gateway-success'>".$txtPending."<p><b>ID:".$res->id."</b></p></div>";
				print'<script>setInterval(function(){window.location.href=window.location.href;},5000);</script>';
				return;
			}
			/**done**/
			if($res->payment_status=='CANCELLED'){
				/*maybe redirect to orderpage without GET vars or homepage ??*/
				print"<div class='wppizza-gateway-success'>".$txtCancelled."</div>";
				return;
			}
			/**all others**/
			//$errors="ORDER ID: ".$res->id."<br />";
			//$errors.="TRANSACTION ID: ".$res->transaction_id."<br />";
			//$errors.="TRANSACTION STATUS: ".$res->payment_status."<br />";
			//".nl2br(print_r(unserialize($res->transaction_errors),true))."
			//print"<div class='wppizza-gateway-error'>error [".$this->gatewayIdent."-1101]: ".__('Sorry, there was an error processing your order. please contact us, providing the details below !',$this->pluginLocale)."<br /><br />".$errors."</div>";


			/**actually lets make this a generic page without too much info**/
			print"<div class='wppizza-gateway-error'>error [".$this->gatewayIdent."-1102]: ".__('Sorry, this order has already been processed or does not exist !',$this->pluginLocale)."</div>";

			return;
		}else{
			print"<div class='wppizza-gateway-error'>error [".$this->gatewayIdent."-1103]: ".__('Sorry, this order does not exist !',$this->pluginLocale)."</div>";
		}
		return;
	}
	/******************************************************************
	*
	*	[complete order by sending emails etc]
	*
	******************************************************************/
	function wppizza_gateway_complete_order($orderId, $blogid=false, $logging=false, $sendAdminErrors=false){
		global $wpdb;
		$orderId=(int)$orderId;
		/**select the right blog table */
		if($blogid && is_int($blogid) && $blogid>1){$wpdb->prefix=$wpdb->base_prefix . $blogid.'_';}

		/**********new send order email class**************/
		$sendEmail=new WPPIZZA_SEND_ORDER_EMAILS();

		/************************************************************
		*
		*	[send the emails returns array
		*	['status']->true/false
		*	['error']->''/msg
		*	['mailer']->mail function name
		*
		************************************************************/
		$mailResults=$sendEmail->wppizza_order_send_email($orderId, $blogid);
		/********************************************************************
		*
		*	[depending on $mailResults['status'],
		*	will insert order into db and output "thankyou"
		*	or justs displays error]
		*
		********************************************************************/
		/*update db*/
		$dbupd['customer_details']		= 	''.$sendEmail->customerDetails.'';
		$dbupd['order_details']			= 	''.$sendEmail->orderDetails.'';
		$dbupd['payment_status']		=	'COMPLETED';
		//$dbupd['transaction_details']	=	esc_sql(serialize($gatewayReply));
		$dbupd['transaction_errors']	=	'';
		$dbupd['mail_sent']				=	!empty($mailResults['status']) ? 'Y' : 'N';
		$dbupd['mail_error']			=	!empty($mailResults['error']) ? esc_sql($mailResults['error']) : '';

		$fields='';
		$i=0;
		foreach($dbupd as $k=>$v){
			if($i>0){$fields.=", ";}
			$fields.="".$k."='".$v."'";
		$i++;
		}
		$sql="UPDATE ".$wpdb->prefix  . $this->pluginOrderTable." SET ".$fields." WHERE id='".$orderId."' ";
		$wpdb->query($sql);

		/**do additional stuff when order has been executed*/
		do_action('wppizza_on_order_executed', $orderId, $this->pluginOrderTable);

		if(!$mailResults['status'] && $sendAdminErrors){
			$this->wppizza_gateway_send_errors_to_admin(true, $blogid, $orderId, false, $mailResults);
		}

		/**logging**/
		if(!$mailResults['status']){
			$this->wppizza_gateway_logging($logging, $blogid, $orderId, $mailResults, 'mail-error');
		}else{
			$this->wppizza_gateway_logging($logging, $blogid, $orderId,'Order ID:'.$orderId . PHP_EOL .'Customer Details:'.PHP_EOL.print_r($sendEmail->customerDetails,true).PHP_EOL.'Order Details:'.PHP_EOL.print_r($sendEmail->orderDetails,true).PHP_EOL.PHP_EOL, 'success');
		}
	}
	/******************************************************************
	*
	*	[send errors to admin]
	*
	******************************************************************/
	function wppizza_gateway_send_errors_to_admin($sendErrors=false, $blogid=false, $orderId=false, $setError=false,$mailResults=false){
			if($sendErrors){/**error sending enabled**/

				if(!$setError && $orderId){
					$orderDetails=$this->wppizza_gateway_get_order_details(false, $orderId, $blogid);
					if($mailResults){/**only when email could not be sent**/
						/**mail error AFTER payment !!! let's try and send another email to the admin to investigate***/
						$warningMessage="Sorry, something has gone wrong sending the order/confirmation email, so - as a last resort - the system has tried to send this email to the email address of the main administrator of the site !!!".PHP_EOL;
						$warningMessage.="An order - details below - has been made, paid and verified via ".$this->gatewayName.". However, the order could not be sent to your shops email address.".PHP_EOL;
						$warningMessage.="Email Error: ".print_r($mailResults['error'],true).PHP_EOL;
						$warningMessage.="Please take the necessary steps to rectify this situation and possibly contact the customer directly using the details provided below".PHP_EOL;
						$warningMessage.="You might also want to check your ".$this->gatewayName." account in relation to the transaction below.".PHP_EOL;

						/**blogid is set and >1**/
						if($blogid && (int)$blogid>1){
							$warningMessage.='BLOG ID: '.(int)$blogid.''.PHP_EOL;
						}
						/**blogid==false , get current and add if >1**/
						if(!$blogid && get_current_blog_id()>1){
							$warningMessage.='BLOG ID: '.get_current_blog_id().''.PHP_EOL;
						}
						if($orderDetails){
							$warningMessage.="ORDER ID: ".print_r($orderDetails->id,true).PHP_EOL;
							$warningMessage.="Customer: ".print_r($orderDetails->customer_details,true).PHP_EOL;
							$warningMessage.="Order Details: ".print_r($orderDetails->order_details,true).PHP_EOL;
							$warningMessage.="Gateway Transaction ID: ".print_r(maybe_unserialize($orderDetails->transaction_id),true).PHP_EOL;
							$warningMessage.="Gateway Transaction Details: ".print_r(maybe_unserialize($orderDetails->transaction_details),true).PHP_EOL;
							$warningMessage.="Gateway Transaction Errors (if any): ".print_r(maybe_unserialize($orderDetails->transaction_errors),true).PHP_EOL.PHP_EOL;
						}else{
							/**should not really ever happen**/
							$warningMessage.="ORDER COULD NOT BE FOUND: ".print_r($orderId,true).PHP_EOL;
						}

						mail(''.get_option('admin_email').'', 'Warning: ['.$this->gatewayName.'] - Order Paid  but could not send Email', $warningMessage);
					}
				}



				/**no mail results set, but order id and error set**/
				if(!$mailResults && $setError && $orderId){/**only when email could not be sent**/
					$orderDetails=$this->wppizza_gateway_get_order_details(false, $orderId, $blogid);

					$warningMessage="".$setError.PHP_EOL;

					if($orderDetails){
						$warningMessage.="ID: ".print_r($orderDetails->id,true).PHP_EOL;
						$warningMessage.="Customer: ".print_r($orderDetails->customer_details,true).PHP_EOL;
						$warningMessage.="Order Details: ".print_r($orderDetails->order_details,true).PHP_EOL;
						$warningMessage.="Gateway Transaction ID (if any): ".print_r(maybe_unserialize($orderDetails->transaction_id),true).PHP_EOL;
						$warningMessage.="Gateway Transaction Details (if any): ".print_r(maybe_unserialize($orderDetails->transaction_details),true).PHP_EOL;
						$warningMessage.="Gateway Transaction Errors (if any): ".print_r(maybe_unserialize($orderDetails->transaction_errors),true).PHP_EOL.PHP_EOL;
					}else{
						/**should not really ever happen**/
						$warningMessage.="ORDER COULD NOT BE FOUND: ".print_r($orderId,true).PHP_EOL;
					}
					mail(''.get_option('admin_email').'', 'Warning: ['.$this->gatewayName.'] - Order could not be verified', $warningMessage);

				}


				if($sendErrors && $setError && !$orderId){/**send specified error if no orderid**/
					mail(''.get_option('admin_email').'', 'Warning: ['.$this->gatewayName.'] - Error', ''.print_r($setError,true).PHP_EOL.'');
				}
			}

	}
	/******************************************************************
	*
	*	[check min wppizza requirements for gateway and display notice]
	*
	******************************************************************/
	function wppizza_gateway_check_requirements(){
		if( version_compare( $this->pluginVersion, $this->gatewayMinWppizzaVersion , '<' )) {
			add_action('admin_notices', array( $this, 'wppizza_gateway_req_notice') );
		}
	}
	function wppizza_gateway_req_notice() {
		$dbpReqNotice='';
		$dbpReqNotice.='<div id="message" class="error wppizza_admin_notice" style="padding:20px;">';
			$dbpReqNotice.='<strong>'.$this->gatewayName.' Gateway for WPPizza requires WPPizza Version '.$this->gatewayMinWppizzaVersion.'+ to work reliably. Please update WPPizza ! </strong>';
			$dbpReqNotice.='<br/><br/> This notice will disappear as soon as you have updated';
			$dbpReqNotice.='<br/> Thank you';
		$dbpReqNotice.='</div>';
		echo"".$dbpReqNotice."";
	}

	/******************************************************************
	*
	*	[orderpage wpml]
	*	helper to return if we are on the orderpage (checking wpml too)
	*	returns true / false
	*
	******************************************************************/
	function wppizza_gateway_current_is_orderpage($isOrderpage=false){
		global $post;
		if(!is_object($post)){return;}
		$currentPage=$post->ID;
		$setOrderPage=$this->pluginOptions['order']['orderpage'];
		/**wpml select of order page**/
		if($setOrderPage>0 && function_exists('icl_object_id')) {
			$setOrderPage=icl_object_id($setOrderPage,'page');
		}
		if($currentPage==$setOrderPage){
			$isOrderpage=true;
		}
		return $isOrderpage;
	}
	/******************************************************************
	*
	*	[get orderpage wpml]
	*	returns permalink of orderpage
	*
	******************************************************************/
	function wppizza_gateway_orderpage(){
		$orderpage=$this->pluginOptions['order']['orderpage'];
		/**wpml select of order page**/
		if($orderpage>0 && function_exists('icl_object_id')) {
			$orderpage=icl_object_id($orderpage,'page');
		}
		$oPagePermalink=get_permalink($orderpage);
		return $oPagePermalink;
	}
	/******************************************************************
	*
	*	[get language wpml]
	*	returns language if base==true returns only first 2 chars lowercase
	*
	******************************************************************/
	function wppizza_gateway_language($base=false){
		$lang='en_US';
		if(WPLANG!=''){
			$lang=WPLANG;
		}
		/**wpml select of full locale**/
		if(function_exists('icl_object_id') && defined('ICL_LANGUAGE_CODE')) {
			$lang=$sitepress->get_locale(ICL_LANGUAGE_CODE);/**get full  locale**/
		}
		/**only first 2**/
		if($base){
			$lang=strtolower(substr($lang,0,2));
			
		}
		return $lang;
	}
	/******************************************************************
	*
	*	[log gateway responses to /logs/]
	*
	******************************************************************/
	function wppizza_gateway_logging($logging=false, $blogid=false,  $id=false, $data=false, $ident='general'){
		if($logging){
			$addBlogId='';
			/**blogid is set and >1**/
			if($blogid && (int)$blogid>1){
				$addBlogId='( BLOG ID: '.(int)$blogid.')';
			}
			/**blogid==false , get current and add if >1**/
			if(!$blogid && get_current_blog_id()>1){
				$addBlogId='( BLOG ID: '.get_current_blog_id().')';
			}

			$timeStamp= current_time('timestamp');
			//$setTimeStamp="".date_i18n(get_option('date_format'),$timeStamp)." ".date_i18n(get_option('time_format'),$timeStamp)."";
			$setTimeStamp="".date('Y-m-d H:i:s', $timeStamp)."";

			if($id){
				file_put_contents($this->gatewayBasePath.'logs/'.$this->gatewayIdent.'-'.strtolower($ident).'-'.wp_hash($this->gatewayIdent).'.log',' ['.$setTimeStamp.']' . PHP_EOL . $id . $addBlogId.':'. PHP_EOL . print_r($data,true).''.PHP_EOL.PHP_EOL,FILE_APPEND);
			}
			/**non order related errors as long as the class is loaded, -> just use WPDEBUG**/
			if(!$id && !$data){
				ini_set('log_errors', true);
				ini_set('error_log', $this->gatewayBasePath.'/logs/'.$this->gatewayIdent.'-error-'.wp_hash($this->gatewayIdent).'.log');
			}
			/*general ipn request and get vars**/
			if(!$id && $data){
				file_put_contents($this->gatewayBasePath.'logs/'.$this->gatewayIdent.'-'.strtolower($ident).'-'.wp_hash($this->gatewayIdent).'.log','['.$setTimeStamp.']' . $addBlogId . PHP_EOL . print_r($data,true).''.PHP_EOL.PHP_EOL,FILE_APPEND);
			}

		}
	}
	/******************************************************************
	*
	*	[gateway helper to be able to - more easily - map order form fields to gateway form fields]
	*
	******************************************************************/
	function wppizza_gateway_map_formfields_setup($ff){
		$mapVars='';
		asort($this->pluginOptions['order_form']);//sort
		foreach($this->pluginOptions['order_form'] as $k => $v){
			if($v['enabled'] && !in_array($v['key'],array('ctips')) && in_array($v['type'],array('email','text','textarea','select')) ){
				$optSelected=!empty($this->gatewayOptions[$ff['key']][$k]) ? $this->gatewayOptions[$ff['key']][$k] : '';
				$mapVars.="<tr><td style='margin:0;padding:0 5px 0 0'>".$v['lbl']."</td><td style='margin:0;padding:0'>";
				$mapVars.="<select name='wppizza[gateways][wppizza_gateway_".$this->gatewaySelect."][".$ff['key']."][".$k."]'>";
					$mapVars.="<option value=''>---".$this->gatewayName." ".__('form fields',$this->pluginLocale)."-----</option>";
					foreach($ff['values'] as $mKey=>$mVal){
						$mapVars.="<option value='".$mKey."' ".selected($optSelected,$mKey,false).">".$mVal."</option>";
					}
				$mapVars.="</select></td></tr>";
			}
		}
		return $mapVars;
	}

	function wppizza_gateway_map_formfields_return($ff,$asarray=false,$omitempty=true){
		$optSelected=array();
		foreach($this->pluginOptions['order_form'] as $k => $v){
			if($v['enabled'] && !in_array($v['key'],array('ctips')) && in_array($v['type'],array('email','text','textarea','select')) ){
				if(!$asarray){
					if(!$omitempty){
						$optSelected[$ff[$k]]=$_POST[$v['key']];
					}else{
						!empty($ff[$k]) && !empty($_POST[$v['key']]) ? $optSelected[$ff[$k]]=$_POST[$v['key']] : null;
					}
				}else{
					if(!$omitempty){
						$optSelected[$ff[$k]]=$v['key'];
					}else{
						!empty($ff[$k]) ? $optSelected[$ff[$k]]=$v['key'] : null;	
					}
				}
			}
		}
		return $optSelected;
	}
	/****************************************************************************************************************************
	*
	*
	*	[ END additional gateway methods]
	*
	*
	*****************************************************************************************************************************/

}
?>