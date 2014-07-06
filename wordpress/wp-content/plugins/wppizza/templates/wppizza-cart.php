<?php
 /*
 	WPPizza Shopping Cart
 */
 /**********************************************************************************************************
 	to keep things consistent when adding an item via ajax
 	we build the cart contents here to insert below when
 	loading page, or returning when getting via ajax
 	depending on set currency position in layout currency_left or currency_right will be empty 
 ***********************************************************************************************************/
$cartContents='';
if(isset($cart['innercartinfo'])){
	$cartContents='<p>'.$cart['innercartinfo'].'</p>';
}else{
	$cartContents='<ul class="wppizza-cart-contents">';
	/**allow item filtering. ADDED 2.8.9.4*****/
	$cart['items'] = apply_filters('wppizza_cart_filter_items', $cart['items'], 'cart');
	foreach($cart['items'] as $k=>$item){
		/***allow action per item - probably to use in conjunction with filter above.  ADDED 2.8.9.4****/
		$cartContents = apply_filters('wppizza_cart_item', $item, $cartContents);	
		
		$cartContents.='<li id="wppizza-cart-item-'.$k.'" class="wppizza-cart-item">';
		/********CHANGES IN 2.5, *****alloow increase/decrease changed 2.5*************/
		if(isset($cart['increase_decrease'])){
			$cartContents.="<input type='text' size=3 class='wppizza-cart-incr' name='wppizza-cart-incr' value='".$item['count']."'>";
			$cartContents.='<span id="wppizza-cart-'.$k.'" class="wppizza-cart-increment" title="">&#10003;</span>';
		}else{
			$cartContents.='<span id="wppizza-cart-'.$k.'" class="wppizza-remove-from-cart" title="'.$txt['remove_from_cart']['lbl'].'">x</span>';
		}
		/********CHANGES IN 2.9: we do not need to display this really if we are already using text boxes.**/
		if(isset($cart['increase_decrease'])){
			$cartContents.=''.$item['name'].' ';
		}else{
			$cartContents.=''.$item['count'].'x '.$item['name'].' ';	
		}
		
		
		/***************************************************************/

		if($item['size']!=''){
		$cartContents.='<span class="wppizza-cart-item-size">'.$item['size'].'</span> ';
		}
		/*****************************************
			CHANGES IN 2.5,
			changed	wppizza_output_format_price($item['pricetotal'],$options['layout']['hide_decimals']);
			to just $item['pricetotal']
		***********************************/


		$cartContents.='<span class="wppizza-cart-item-price">'.$cart['currency_left'].''.$item['pricetotal'].''.$cart['currency_right'].'</span>';
		if(is_array($item['additionalinfo']) && count($item['additionalinfo'])>0){
			$cartContents.='<div class="wppizza-item-additional-info"><div class="wppizza-item-additional-info-icon"></div><div class="wppizza-item-additional-info-pad">';
			foreach($item['additionalinfo'] as $addItem){
				$cartContents.='<span>'.$addItem.'</span>';
			}
			$cartContents.='</div></div>';
		}
		$cartContents.='</li>';
	}
	$cartContents.='</ul>';
}
/******************************************************
	[request was made via ajax, when adding item, so only return item html]
******************************************************/
if(isset($request) && $request=='ajax'){
	echo $cartContents;
return;
}
?>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_start');
?>
<?php if(isset($openingTimes)){ ?>
	<div class="wppizza-opening-hours"<?php if(isset($cartStyle['width'])){echo $cartStyle['width'];}?>><?php echo $openingTimes ?></div>
<?php } ?>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_before_cart');
?>
<div class="wppizza-cart <?php echo $stickycart; ?>"<?php if(isset($cartStyle['cart'])){echo $cartStyle['cart'];}?>>
	<?php if($cart['shopopen']==1){ /*make sure that we are open*/ ?>
	<input type='hidden' class='wppizza-open' name='wppizza-open' />
	<?php } ?>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_before_order');
?>
	<div class="wppizza-order">
	<?php echo $cartContents ?>
	</div>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_before_info');
?>
	<div class="wppizza-cart-info">
			<div class="wppizza-cart-nocheckout"><?php echo $cart['nocheckout'] ?></div>
			<span class="wppizza-cart-total-items">
				<span class="wppizza-cart-total-items-label">
					<?php if(count($cart['items'])>0){?>
						<?php echo $cart['order_value']['total_price_items']['lbl'] ?>
					<?php } ?>
				</span>
				<span class="wppizza-cart-total-items-value wppizza-cart-info-price">
					<?php if(count($cart['items'])>0){?>
						<?php echo $cart['currency_left'].''.$cart['order_value']['total_price_items']['val'].''.$cart['currency_right']; ?>
					<?php } ?>
				</span>
			</span>

			<span class="wppizza-cart-discount">
				<span class="wppizza-cart-discount-label">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['order_value']['discount']['lbl'] ?>
					<?php } ?>
				</span>
				<span class="wppizza-cart-discount-value wppizza-cart-info-price">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0 && $cart['order_value']['discount']['val']>0){?>
					<?php echo '<span class="wppizza-minus"></span>'.$cart['currency_left'].''.$cart['order_value']['discount']['val'].''.$cart['currency_right']; ?>
					<?php } ?>
				</span>
			</span>

			<?php if(isset($cart['tax_enabled']) && $cart['tax_applied']=='items_only'){ /*SUM SALES TAX - ITEMS ONLY: CONDITIONAL ADDED/CHANGED IN VERSION 2.0 and 2.5*/ ?>
			<span class="wppizza-cart-tax wppizza-car-tax-itemsonly">
				<span class="wppizza-cart-tax-label">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['order_value']['item_tax']['lbl'] ?>
					<?php } ?>
				</span>
				<span class="wppizza-cart-tax-value wppizza-cart-info-price">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['currency_left'].''.$cart['order_value']['item_tax']['val'].''.$cart['currency_right']; ?>
					<?php } ?>
				</span>
			</span>
			<?php } ?>

			<?php if(!isset($cart['self_pickup_enabled']) || $cart['selfPickup']==0){ /*NOT SELFPICKUP : CONDITIONAL ADDED/CHANGED IN Version 1.4.1*/ ?>
			<span class="wppizza-cart-delivery">
				<span class="wppizza-cart-delivery-charges-label">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['order_value']['delivery_charges']['lbl'] ?>
					<?php } ?>
				</span>
				<span class="wppizza-cart-delivery-charges-value wppizza-cart-info-price">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php 
						if($cart['order_value']['delivery_charges']['val']!=''){echo $cart['currency_left'].''.$cart['order_value']['delivery_charges']['val'].''.$cart['currency_right']; ?>
					<?php }} ?>
				</span>
			</span>
			<?php } ?>

			<?php if(isset($cart['tax_enabled']) && $cart['tax_applied']=='items_and_shipping'){ /*SUM TAX - applied to items AND shipping: CONDITIONAL ADDED/CHANGED IN VERSION 2.0 and 2.5*/ ?>
			<span class="wppizza-cart-tax wppizza-car-tax-itemsshipping">
				<span class="wppizza-cart-tax-label">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['order_value']['item_tax']['lbl'] ?>
					<?php } ?>
				</span>
				<span class="wppizza-cart-tax-value wppizza-cart-info-price">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['currency_left'].''.$cart['order_value']['item_tax']['val'].''.$cart['currency_right']; ?>
					<?php } ?>
				</span>
			</span>
			<?php } ?>
	
			<?php if(isset($cart['tax_enabled']) && $cart['tax_applied']=='taxes_included'){ /*SUMMARY OF ALL TAXES IF INCLUDED IN SET PRICES ADDED 2.8.9.3*/ ?>
			<span class="wppizza-cart-tax-included">
				<span class="wppizza-cart-tax-included-label">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['order_value']['taxes_included']['lbl'] ?>
					<?php } ?>
				</span>
				<span class="wppizza-cart-tax-included-value wppizza-cart-info-price">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['currency_left'].''.$cart['order_value']['taxes_included']['val'].''.$cart['currency_right']; ?>
					<?php } ?>
				</span>
			</span>
			<?php } ?>

			<?php if(isset($cart['tips']) && $cart['tips']>0){/*tips NEW 2.8.4*/?>
			<span class="wppizza-order-tips">
				<span class="wppizza-order-tips-label">
					<?php echo $cart['tips']['lbl'] ?>
				</span>
				<span class="wppizza-order-tips-value wppizza-cart-info-price">
					<?php echo $cart['currency_left'].''.$cart['tips']['val'].''.$cart['currency_right']; ?>
				</span>
			</span>
			<?php } ?>


			<span class="wppizza-cart-total">
				<span class="wppizza-cart-total-label">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['order_value']['total']['lbl'] ?>
					<?php } ?>
				</span>
				<span class="wppizza-cart-total-value wppizza-cart-info-price">
					<?php if($cart['nocheckout']=='' && count($cart['items'])>0){?>
					<?php echo $cart['currency_left'].''.$cart['order_value']['total']['val'].''.$cart['currency_right'] ?>
					<?php } ?>
				</span>
			</span>
			<?php if($cart['nocheckout']=='' && isset($cart['self_pickup_enabled']) && $cart['selfPickup']==1	){ /*SELFPICKUP ENABLED AND SELECTED : ADDED/CHANGED IN V1.4.1**/ ?>
			<span id="wppizza-cart-self-pickup">
				<?php echo ($cart['order_self_pickup_cart']) ?>
			</span>
			<?php } ?>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_before_cartbutton');
?>
		<div class="wppizza-cart-button"><?php echo $cart['button'] ?></div>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_after_cartbutton');
?>
	</div>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_after_info');
?>
</div>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_after_cart');
?>
<?php if(isset($cart['self_pickup_enabled']) && isset($cart['self_pickup_cart'])){ /*ALLOW SELF PICKUP AND DISPLAY IN CART: ADDED/CHANGED IN V1.4.1**/ ?>
	<div class="wppizza-order-pickup-choice">
	<label><input type='checkbox' id='<?php echo $cart['selfPickupId'] ?>' name='wppizza-order-pickup' value='1' <?php checked($cart['selfPickup'],1,true) ?> /><?php echo $cart['order_self_pickup'] ?></label>
	</div>
<?php } ?>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_before_orderinfo');
?>
<?php if(isset($orderinfo)){ ?>
	<ul id="wppizza-orders-info" <?php if(isset($cartStyle['width'])){echo $cartStyle['width'];}?>>
		<?php if(isset($cart['pricing_discounts'])){foreach($cart['pricing_discounts'] as $discounts){?>
			<li><?php echo $discounts ?></li>
		<?php }}?>
		<?php if(isset($cart['pricing_delivery'])){?>
			<li><?php echo $cart['pricing_delivery'] ?></li>
			<?php if(isset($cart['pricing_delivery_per_item_free'])){?>
			<li><?php echo $cart['pricing_delivery_per_item_free'] ?></li>
			<?php }?>
		<?php }?>
	</ul>
<?php } ?>
<?php
	/*ADDED IN VERSION 2.7.3*/
	do_action('wppizza_cart_end');
?>