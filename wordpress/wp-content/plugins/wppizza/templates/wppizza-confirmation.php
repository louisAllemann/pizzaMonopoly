<?php
/*
*
*	WPPizza Confirmation Page before order is going to be sent to gateway 
*
*	ONLY USED IF ENABLED IN ORDER FORM SETTINGS
*
*/
?>
<?php 
/**************************************************************************************************************
*
*
*		[additional form fields for accepting AGB and what not]
*		$confirmationelements = enabled fields
*
**************************************************************************************************************/ 
?>
<?php if(count($cart['items'])>0 && count($confirmationelements)>0){/*make sure there are elements to display and stuff to order***/?>
	<fieldset id="wppizza-confirm-legal">
		<legend><?php echo $txt['legend_legal'] ?></legend>
		<?php foreach($confirmationelements as $elmKey=>$elm){?>
				<label for="<?php echo $elm['key'] ?>">
					<?php if($elm['type']=='checkbox'){ ?>
						<span id="wppizza-confirm-elm-<?php echo $elm['key'] ?>" class="wppizza-confirm-elm">
							<input id="<?php echo $elm['key'] ?>" name="<?php echo $elm['key'] ?>" type="checkbox" value="1" <?php echo !empty($elm['required'])?'required':'' ?>/>
						</span>
					<?php } ?>
					<span id="wppizza-confirm-lbl-<?php echo $elm['key'] ?>" class="wppizza-confirm-lbl<?php echo !empty($elm['required']) && $elm['type']!='link' ? ' wppizza-confirm-label-required':'' ?>">
						<?php echo $elm['lbl'] ?>
					</span>
				</label>
		<?php } ?>
	</fieldset>
<?php } ?>
<?php 
/**************************************************************************************************************
*
*
*		[user information]
*		$formelements = enabled fields
*		let's add submitted user details as hidden fields again
*
**************************************************************************************************************/ 
?>
<?php if(count($cart['items'])>0){/*make sure there's stuff to order***/?>
	<fieldset id="wppizza-confirm-user">
		<legend><?php echo $txt['legend_personal'] ?> <a href="<?php echo $orderpagelink ?>" class="wppizza-confirm-dochange"><?php echo $txt['change_user_details'] ?></a></legend>
		<?php foreach($formelements as $elmKey=>$elm){?>
			<div>
				<label for="<?php echo $elm['key'] ?>"><?php echo $elm['lbl'] ?></label>
				<span><?php echo $elm['userVal'] ?></span>
				<input name="<?php echo $elm['key'] ?>" type="hidden" value="<?php echo $elm['userVal'] ?>" />
			</div>
		<?php } ?>	
	</fieldset>
<?php } ?>
<?php 
/**************************************************************************************************************
*
*
*		[payment method information]
*
*
**************************************************************************************************************/ 
?>
<?php if(count($cart['items'])>0){/*make sure there's stuff to order***/?>
	<fieldset id="wppizza-confirm-payment-method">
		<legend><?php echo $txt['legend_payment_method'] ?> <a href="<?php echo $orderpagelink ?>" class="wppizza-confirm-dochange"><?php echo $txt['change_user_details'] ?></a></legend>
		<div>
			<label><?php echo $txt['payment_method'] ?></label>
			<span>
				<?php echo $gatewayLabel ?>
			</span>
		</div>	
	</fieldset>
<?php } ?>
<?php 
/**************************************************************************************************************
*
*
*		[cart information and summary]
*
*
**************************************************************************************************************/ 
?>		
<?php 
	/**************************************************************************************************************
		[cart info]
	**************************************************************************************************************/ 
?>
	<fieldset id="wppizza-confirm-cart-contents">
		<legend><?php echo $txt['legend_order_details'] ?> <a href="<?php echo $amendorderlink ?>" class="wppizza-confirm-dochange"><?php echo $txt['change_order_details'] ?></a></legend>		

<?php 
	/**************************************************************************************************************
		[self pickup selected]
	**************************************************************************************************************/ 
?>			
			<?php if(isset($cart['self_pickup_enabled']) &&  $cart['selfPickup']==1 && $txt['order_page_self_pickup']!=''){ /*self pickup conditional-> no delivery charges **/ ?>
				<div id="wppizza-self-pickup"><?php echo $txt['order_page_self_pickup'] ?></div>
			<?php } ?>

			<?php if(isset($cart['self_pickup_enabled']) &&  $cart['selfPickup']==2 && $txt['order_page_no_delivery']!=''){ /*no delivery offered **/  ?>
				<div id="wppizza-self-pickup"><?php echo $txt['order_page_no_delivery'] ?></div>
			<?php } ?>	

<?php 
	/**************************************************************************************************************
		[cart itemised]
	**************************************************************************************************************/ 
?>
		<?php if(count($cart['items'])>0){/*make sure there's stuff to order***/?>
			<table id="wppizza-confirm-itemmised-details">
				<tr class="wppizza-confirm-itemised-th">
					<th class="wppizza-confirm-item-th"><?php echo $txt['header_itemised_article'] ?></th>
					<th class="wppizza-confirm-item-price-th"><?php echo $txt['header_itemised_price_single'] ?></th>
					<th class="wppizza-confirm-item-quantity-th"><?php echo $txt['header_itemised_quantity'] ?></th>
					<th class="wppizza-confirm-item-price-total-th"><?php echo $txt['header_itemised_price'] ?></th>
				</tr>
				
				<?php foreach($cart['items'] as $item){ ?>
				<tr class="wppizza-confirm-itemised">
					<td class="wppizza-confirm-item">
						<?php echo''.$item['name'].' '.$item['size'].''; ?>
						<?php if(is_array($item['additionalinfo']) && count($item['additionalinfo'])>0){?>
							<div class="wppizza-item-additionalinfo">
								<?php foreach($item['additionalinfo'] as $additionalInfo){?>
									<span><?php echo $additionalInfo ?></span>
								<?php } ?>
							</div>
						<?php } ?>						
					</td>
					<td class="wppizza-confirm-item-price">
						<?php echo''.$cart['currency_left'].''.$item['price'].''.$cart['currency_right'].''; ?>
					</td>
					<td class="wppizza-confirm-item-quantity">
						<?php echo''.$item['count'].'x'; ?>
					</td>
					<td class="wppizza-confirm-item-price-total">
						<?php echo''.$cart['currency_left'].''.$item['pricetotal'].''.$cart['currency_right'].''; ?>
					</td>
				</tr>
				<?php } ?>
			</table>
			
<?php 
	/**************************************************************************************************************
		[cart (sub)-totals]
	**************************************************************************************************************/ 
?>
			<div id="wppizza-confirm-cart-subtotals-wrap">
			<table id="wppizza-confirm-cart-subtotals">
				<tr class="wppizza-order-total-items"><td><?php echo $txt['order_items'] ?></td><td><?php echo $cart['currency_left'].''.$cart['order_value']['total_price_items']['val'].''.$cart['currency_right']; ?></td></tr>
				<?php if($cart['order_value']['discount']['val']>0){/*discount applies*/?>
					<tr class="wppizza-order-discount"><td><?php echo $txt['discount'] ?></td><td><span class="wppizza-minus"></span><?php echo $cart['currency_left'].''.$cart['order_value']['discount']['val'].''.$cart['currency_right']; ?></td></tr>
				<?php } ?>			
				
				<?php if($cart['order_value']['item_tax']['val']>0 && $cart['tax_applied']=='items_only' && !$taxIncluded){/*item/sales tax applies (items only) AND prices entered WITHOUT tax */ ?>
					<tr class="wppizza-order-item-tax"><td><?php echo $txt['item_tax_total'] ?></td><td><?php echo $cart['currency_left'].''.$cart['order_value']['item_tax']['val'].''.$cart['currency_right']; ?></td></tr>
				<?php } ?>
	
				<?php if(!isset($cart['self_pickup_enabled']) ||  $cart['selfPickup']==0){ /*no self pickup enabled or chosen :conditional */ ?>
					<?php if($cart['order_value']['delivery_charges']['val']!='' ){/*delivery charges if any*/?>
						<tr class="wppizza-order-pickup"><td><?php echo $txt['delivery_charges'] ?></td><td><?php echo $cart['currency_left'].''.$cart['order_value']['delivery_charges']['val'].''.$cart['currency_right']; ?></td></tr>
					<?php }else{ ?>
						<tr class="wppizza-order-pickup"><td><?php echo $txt['delivery_charges'] ?></td><td><?php echo $txt['free_delivery'] ?></td></tr>
					<?php } ?>
				<?php } ?>
	
				<?php if($cart['order_value']['item_tax']['val']>0 && $cart['tax_applied']=='items_and_shipping' && !$taxIncluded){/*item/sales tax applied to items AND shipping AND prices entered WITHOUT tax */ ?>
					<tr class="wppizza-order-item-tax"><td><?php echo $txt['item_tax_total'] ?></td><td><?php echo $cart['currency_left'].''.$cart['order_value']['item_tax']['val'].''.$cart['currency_right']; ?></td></tr>
				<?php } ?>
				
				<?php /**handling/sur charges if any**/ ?>
				<?php if(isset($cart['order_value']['handling_charge'])){ ?>
					<?php if(isset($cart['order_value']['handling_charge']['val'])){ /*is number, add currency symbol**/ ?>
						<tr class="wppizza-order-handling-charge"><td><?php echo $txt['order_page_handling'] ?></td><td><?php echo $cart['currency_left'].''.$cart['order_value']['handling_charge']['val'].''.$cart['currency_right']; ?></td></tr>
					<?php } ?>
					<?php if(isset($cart['order_value']['handling_charge']['str'])){ /*is string (i.e "surcharge calculated at checkout"), omit currency symbol**/  ?>
						<tr class="wppizza-order-handling-charge-checkout"><td><?php echo $txt['order_page_handling'] ?></td><td><?php echo $cart['order_value']['handling_charge']['str']; ?></td></tr>
					<?php } ?>
				<?php } ?>
				
				<?php if(isset($cart['tips']) && $cart['tips']>0){/*tips NEW 2.8.4*/?>
					<tr class="wppizza-order-tips"><td><?php echo $txt['tips'] ?></td><td><?php echo $cart['currency_left'].''.$cart['tips']['val'].''.$cart['currency_right']; ?></td></tr>
				<?php } ?>
	
				<?php if($cart['order_value']['item_tax']['val']>0 && $taxIncluded){/*prices entered WITH tax, display at end before totals*/ ?>
					<tr class="wppizza-order-item-tax"><td><?php echo $txt['item_tax_total'] ?></td><td><?php echo $cart['currency_left'].''.$cart['order_value']['item_tax']['val'].''.$cart['currency_right']; ?></td></tr>
				<?php } ?>
				

	
				<tr class="wppizza-cart-total"><td><?php echo $txt['order_total'] ?></td><td><?php echo $cart['currency_left'].''.$cart['order_value']['total']['val'].''.$cart['currency_right']; ?></td></tr>
			
			</table>
			</div>
			
			<?php if(trim($txt['subtotals_after_additional_info'])!=''){?> 
				<div id="wppizza-confirm-subtotals-after"><?php echo$txt['subtotals_after_additional_info'] ?></div>
			<?php } ?>
						
		<?php }else{ ?>
			<p><?php echo $txt['cart_is_empty'] ?></p>
		<?php } ?>
	</fieldset>
<?php 
/**************************************************************************************************************
*
*
*		[final buy / sendorder button and (required) hidden hash and gateway choice]
*		[do not change id's or names]
*
**************************************************************************************************************/ 
?>
	<?php if(count($cart['items'])>0){/*make sure there's stuff to order***/?>
	<div class="wppizza-confirm-button">	
		<?php
			/*output the send order button*/
			echo $orderbutton;
		?>
	</div>
	<?php } ?>