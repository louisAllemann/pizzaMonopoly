<?php
/*
*
*	 WPPizza Order Page
*
*/
/*********************************************************************************************************
*
*	[get cart contents with some variables]
*
*	[$cart['items'] = contains cart contents, grouped and sorted]
*	[$cart['order_value'] = contains subtotal, deliver charges, discounts and grand total]
*	[$txt array= localized variables from settings->localization]
*	[$formelements = form elements from settings->order form]
*
*********************************************************************************************************/
/**if the user is logged in , pre-enter the info we have (if prefill is selected in wppizza->order form settings. CHANGED IN VERSION 2.6.5.3***/
if(is_user_logged_in() ) {
	global $current_user;
	$getUserMeta=get_user_meta( $current_user->ID );
	foreach($getUserMeta as $k=>$v){
		/**for legacy reasons, strip wppizza_ from key*/
		if(substr($k,0,8)=='wppizza_'){
			$k=substr($k,8);
		}
		$userMeta[$k]=$v[0];
	}
}
/***if we are adding get vars to the url (if a tip has been added for instance the page will be refreshed with vars appended), force prefill to be enabled and set values accordingly. ADDED IN VERSION 2.8.6**/
/*$_GET will also include session data set in $_SESSION[$this->pluginSessionGlobal]['userdata'] as they will not be appended to the url anymore (it's just ugly). MODIFIED IN VERSION 2.8.8.3, but no changes made to this file */
$isSelfPickup=!empty($_SESSION[$this->pluginSession]['selfPickup']) ? 1:0;/**check if self pickup has been selected and make fields required as set in order form settings, ADDED in 2.8.9.10*/
foreach($formelements as $elmKey=>$elm){
	if(isset($_GET[$elm['key']])){
		$formelements[$elmKey]['prefill']=1;
		$userMeta[$elm['key']]=$_GET[$elm['key']];
	}
	/**do NOT set required flag on selected elements on self-pickup. ADDED in 2.8.9.10 **/
	if($isSelfPickup==1 && !$elm['required_on_pickup']){
		$formelements[$elmKey]['required']=false;
	}
}
?>

<?php
	/*AMENDED in 2.8.9 to take account of no of items*/
	do_action('wppizza_order_form_before',$cart);
?>
<form id='wppizza-send-order' method='post' action='' accept-charset="<?php echo get_bloginfo('charset') /*accept charset NEW IN VERSION 2.0 */ ?>">
<?php
	do_action('wppizza_order_form_inside_top',$cart);
?>
	<fieldset id="wppizza-cart-contents">
		<legend><?php echo $txt['your_order']['lbl'] ?></legend>
		<?php if(count($cart['items'])>0){/*make sure there's stuff to order***/?>
			<ul id="wppizza-item-details">
			<?php
					/***allow filtering of items (sort, add categories and whatnot) ADDED 2.8.9.4****/
					$cart['items'] = apply_filters('wppizza_order_form_filter_items', $cart['items'],'order');
					foreach($cart['items'] as $item){
					/***allow action per item - probably to use in conjunction with filter above****/
					do_action('wppizza_order_form_item',$item);
			?>
				<li><?php echo''.$item['count'].'x '.$item['name'].' '.$item['size'].' <span class="wppizza-price-single">['.$cart['currency_left'].''.$item['price'].''.$cart['currency_right'].']</span>' ?> <span><?php echo''.$cart['currency_left'].''.$item['pricetotal'].''.$cart['currency_right'].''; ?></span>
					<?php if(is_array($item['additionalinfo']) && count($item['additionalinfo'])>0){?>
					<div class="wppizza-item-additionalinfo">
						<?php foreach($item['additionalinfo'] as $additionalInfo){?>
						<span><?php echo $additionalInfo ?></span>
						<?php } ?>
					</div>
					<?php } ?>
				</li>
			<?php } ?>
			</ul>

			<ul id="wppizza-cart-subtotals">

				<li class="wppizza-order-total-items"><?php echo $txt['order_items']['lbl'] ?><span><?php echo $cart['currency_left'].''.$cart['order_value']['total_price_items']['val'].''.$cart['currency_right']; ?></span></li>

			<?php if($cart['order_value']['discount']['val']>0){/*discount applies*/?>
				<li class="wppizza-order-discount"><?php echo $txt['discount']['lbl'] ?><span><span class="wppizza-minus"></span><?php echo $cart['currency_left'].''.$cart['order_value']['discount']['val'].''.$cart['currency_right']; ?></span></li>
			<?php } ?>

			<?php if($cart['order_value']['item_tax']['val']>0 && $cart['tax_applied']=='items_only'){/*item/sales tax applies (items only)  NEW IN VERSION 2.0/2.5 */ ?>
				<li class="wppizza-order-item-tax wppizza-order-item-tax-itemsonly"><?php echo $txt['item_tax_total']['lbl'] ?><span><?php echo $cart['currency_left'].''.$cart['order_value']['item_tax']['val'].''.$cart['currency_right']; ?></span></li>
			<?php } ?>

			<?php if(!isset($cart['self_pickup_enabled']) ||  $cart['selfPickup']==0){ /*no self pickup enabled or chosen :conditional  NEW IN VERSION  v1.4.1*/ ?>
				<?php if($cart['order_value']['delivery_charges']['val']!='' ){/*delivery charges if any*/?>
					<li class="wppizza-order-pickup"><?php echo $txt['delivery_charges']['lbl'] ?><span><?php echo $cart['currency_left'].''.$cart['order_value']['delivery_charges']['val'].''.$cart['currency_right']; ?></span></li>
				<?php }else{ ?>
					<li class="wppizza-order-pickup"><?php echo $txt['delivery_charges']['lbl'] ?><span><?php echo $txt['free_delivery']['lbl'] ?></span></li>
				<?php } ?>
			<?php } ?>

			<?php if($cart['order_value']['item_tax']['val']>0 && $cart['tax_applied']=='items_and_shipping'){/*item/sales tax applied to items AND shipping  NEW IN VERSION 2.0 / 2.5*/ ?>
				<li class="wppizza-order-item-tax wppizza-order-item-tax-itemsshipping"><?php echo $txt['item_tax_total']['lbl'] ?><span><?php echo $cart['currency_left'].''.$cart['order_value']['item_tax']['val'].''.$cart['currency_right']; ?></span></li>
			<?php } ?>

			<?php if($cart['order_value']['taxes_included']['val']>0 && $cart['tax_applied']=='taxes_included'){/*all taxes included  NEW IN VERSION 2.8.9.3 */ ?>
				<li class="wppizza-order-item-tax-inclusive"><?php echo $cart['order_value']['taxes_included']['lbl'] ?><span><?php echo $cart['currency_left'].''.$cart['order_value']['taxes_included']['val'].''.$cart['currency_right']; ?></span></li>
			<?php } ?>

			<?php /**handling charges if any NEW 2.8.9.4 AMENDED 2.8.9.5**/ ?>
			<?php if(isset($cart['order_value']['handling_charge'])){ ?>
				<?php if(isset($cart['order_value']['handling_charge']['val'])){ /*is number, add currency symbol**/ ?>
					<li class="wppizza-order-handling-charge"><?php echo $txt['order_page_handling']['lbl'] ?><span><?php echo $cart['currency_left'].''.$cart['order_value']['handling_charge']['val'].''.$cart['currency_right']; ?></span></li>
				<?php } ?>
				<?php if(isset($cart['order_value']['handling_charge']['str'])){ /*is string (i.e "surcharge calculated at checkout"), omit currency symbol**/  ?>
					<li class="wppizza-order-handling-charge-checkout"><?php echo $txt['order_page_handling']['lbl'] ?><span><?php echo $cart['order_value']['handling_charge']['str']; ?></span></li>
				<?php } ?>
			<?php } ?>

			<?php if(isset($cart['tips']) && $cart['tips']>0){/*tips NEW 2.8.4*/?>
				<li class="wppizza-order-tips"><?php echo $txt['tips']['lbl'] ?><span><span></span><?php echo $cart['currency_left'].''.$cart['tips']['val'].''.$cart['currency_right']; ?></span></li>
			<?php } ?>

				<li id="wppizza-cart-total"><?php echo $txt['order_total']['lbl'] ?><span><?php echo $cart['currency_left'].''.$cart['order_value']['total']['val'].''.$cart['currency_right']; ?></span></li>

			<?php if(isset($cart['self_pickup_enabled']) &&  $cart['selfPickup']==1 && $txt['order_page_self_pickup']['lbl']!=''){ /*self pickup conditional-> no delivery charges : NEW IN VERSION 1.4.1**/ ?>
				<li id="wppizza-self-pickup"><?php echo $txt['order_page_self_pickup']['lbl'] ?></li>
			<?php } ?>

			<?php if(isset($cart['self_pickup_enabled']) &&  $cart['selfPickup']==2 && $txt['order_page_no_delivery']['lbl']!=''){ /*no delivery offered : NEW IN VERSION 2.8.6**/  ?>
				<li id="wppizza-self-pickup"><?php echo $txt['order_page_no_delivery']['lbl'] ?></li>
			<?php } ?>

			</ul>

			<?php if(isset($cart['self_pickup_enabled']) && isset($cart['self_pickup_order_page'])){ /*allow self pickup and display on order page: NEW IN VERSION 1.4.1**/ ?>
				<div class="wppizza-order-pickup-choice">
					<label><input type='checkbox' id='<?php echo $cart['selfPickupId'] ?>' name='wppizza-order-pickup' value='1' <?php checked($cart['selfPickup'],1,true) ?> /><?php echo $cart['order_self_pickup'] ?></label>
				</div>
			<?php } ?>

		<?php }else{ ?>
			<p><?php echo $txt['cart_is_empty']['lbl'] ?></p>
		<?php } ?>
	</fieldset>

	<?php if(count($cart['items'])>0){/*make sure there's stuff to order***/?>
	<fieldset>
		<legend><?php echo $txt['order_form_legend']['lbl'] ?></legend>
		<?php foreach($formelements as $elmKey=>$elm){ ?>
		<?php
			/*NEW IN VERSION 2.4 */
			do_action('wppizza_order_before_field_'.$elmKey.'');
		?>
			<?php if($elm['enabled']){?>
				<label for="<?php echo $elm['key'] ?>"<?php echo !empty($elm['required']) ? ' class="wppizza-order-label-required"':'' ?>><?php echo $elm['lbl'] ?></label>
				<?php if($elm['type']=='text'){ ?>
					<input id="<?php echo $elm['key'] ?>" name="<?php echo $elm['key'] ?>" type="text" value="<?php echo !empty($elm['prefill']) && isset($userMeta[$elm['key']]) ? $userMeta[$elm['key']] :''  /*CHANGED IN VERSION 2.6.5.3*/ ?>" <?php echo !empty($elm['required'])?'required':'' ?>/>
				<?php } ?>
				<?php if($elm['type']=='tips'){ /*ADDED IN VERSION 2.8.4 */ ?>
					<div id="wppizza-<?php echo $elm['key'] ?>-wrap-outer"><span id="wppizza-<?php echo $elm['key'] ?>-wrap-inner">
					<input id="wppizza-<?php echo $elm['key'] ?>-btn" type="button" class="btn btn-secondary" value="<?php echo $txt['tips_ok']['lbl'] ?>" />
					<input id="<?php echo $elm['key'] ?>" name="<?php echo $elm['key'] ?>" type="text" value="<?php echo !empty($cart['tips']['val']) ? $cart['tips']['val'] :'' ?>" <?php echo !empty($elm['required'])?'required':'' ?>/>
					</span></div>

				<?php } ?>
				<?php if($elm['type']=='email'){?>
					<input id="<?php echo $elm['key'] ?>" name="<?php echo $elm['key'] ?>" type="email" value="<?php echo !empty($elm['prefill']) && isset($userMeta[$elm['key']]) ? $userMeta[$elm['key']] :''  /*CHANGED IN VERSION 2.6.5.3*/ ?>" <?php echo !empty($elm['required'])?'required':'' ?>/>
				<?php } ?>
				<?php if($elm['type']=='textarea'){?>
					<textarea id="<?php echo $elm['key'] ?>" name="<?php echo $elm['key'] ?>" <?php echo !empty($elm['required'])?'required':'' ?>><?php echo !empty($elm['prefill']) && isset($userMeta[$elm['key']]) ? $userMeta[$elm['key']] :''  /*CHANGED IN VERSION 2.6.5.3*/ ?></textarea>
				<?php } ?>
				<?php if($elm['type']=='select'){?>
					<select id="<?php echo $elm['key'] ?>" name="<?php echo $elm['key'] ?>" <?php echo !empty($elm['required'])?'required':'' ?>>
						<option value="">--------</option>
						<?php foreach($elm['value'] as $a=>$b){?>
						<option value="<?php echo wppizza_validate_string($b) ?>" <?php echo !empty($elm['prefill']) && isset($userMeta[$elm['key']]) && $userMeta[$elm['key']]==wppizza_validate_string($a) ? 'selected="selected"' :''  /*CHANGED IN VERSION 2.6.5.3*/ ?>><?php echo $b ?></option>
						<?php } ?>
					</select>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<?php if(is_user_logged_in() ) { /**allow user to update profile ADDED IN VERSION 2.8*/ ?>
			<label for="wppizza_profile_update">
			<input id="wppizza_profile_update" name="wppizza_profile_update" type="checkbox" value="1" />
			<?php echo $txt['update_profile']['lbl'] ?>
			</label>
		<?php } ?>
		<?php
			/*NEW IN VERSION 2.0 => FOR FUTURE USE*/
			do_action('wppizza_gateway_choice_before');
		?>
		<?php
			/*output the buy now/send order button depending on gateway*/
			/*NEW IN VERSION 2.0 IMPORTANT*/
			do_action('wppizza_choose_gateway');
			/* previously hardcoded submit button deleted*/
		?>
	</fieldset>
		<?php
			/*NEW IN VERSION 2.0 => FOR FUTURE USE*/
			do_action('wppizza_gateway_choice_after');
		?>
	<?php } ?>
<?php
	do_action('wppizza_order_form_inside_bottom',$cart);
?>
</form>
<?php
	/*AMENDED in 2.8.9 to take account of no of items*/
	do_action('wppizza_order_form_after',$cart);
?>