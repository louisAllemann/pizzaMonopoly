<?php
 /*
 *
 *	Template: WPPizza "Order History"
 *	edit as required or - if possible - use css and/or action and filter hooks provided
 *
 */
?>
<?php
	do_action('wppizza_history_before_orders',$numberOfOrders,$ordersOnPage);
?>
<div id="wppizza-history-wrap">
<?php
/*******************************************************
*
*
*		loop the orders
*
*
*******************************************************/
	if($numberOfOrders>0){
		$j=0;
		foreach($orders as $oId=>$oDetails){
			/********************************
			to make life a bit easier, assigne vars
			********************************/
			$order=$oDetails['order'];
			$items=$oDetails['items'];
			$summary=$oDetails['summary'];
?>
<?php
	do_action('wppizza_history_loop_start',$oId,$j);
?>
<fieldset id="wppizza-history-<?php echo $oId?>" class="wppizza-history">
<?php
	do_action('wppizza_history_loop_before_legend',$oId,$j);
?>
<?php	/**header with time and transaction id**/ ?>
	<legend class="wppizza-history-legend"><span class="wppizza-history-date"><?php echo $order['transaction_date_time'] ?></span></legend>
<?php
	do_action('wppizza_history_loop_before_items',$oId,$j);
?>

<?php	/**order items details**/ 	?>

	<ul class="wppizza-item-details">
	<?php
		/***allow filtering of items (sort, add categories and whatnot)****/
		$items = apply_filters('wppizza_orderhistory_filter_items', $items, 'showorder');
		foreach($items as $k=>$item){
		/***allow action per item - probably to use in conjunction with filter above****/
		do_action('wppizza_orderhistory_item',$item);
	?>
		<li><?php echo''.$item['quantity'].'x '.$item['name'].' '.$item['size'].' <span class="wppizza-price-item-single">['.$order['currency_left'].''.$item['price'].''.$order['currency_right'].']</span>' ?> <span class="wppizza-price-item-total"><?php echo''.$order['currency_left'].''.$item['pricetotal'].''.$order['currency_right']; ?></span>
		<?php if(isset($item['additionalInfo']) && $item['additionalInfo']!=''){?>
			<div class="wppizza-item-additionalinfo">
				<span><?php echo $item['additionalInfo'] ?></span>
			</div>
		<?php } ?>
		</li>
	<?php } ?>
	</ul>
<?php
	do_action('wppizza_history_loop_before_summary',$oId,$j);
?>
<?php	/**order summary**/ 	?>
	<ul class="wppizza-history-subtotals">

			<li class="wppizza-order-total-items"><?php echo $orderlbl['order_items'] ?><span><?php echo $order['currency_left'].''.$summary['total_price_items'].''.$order['currency_right']; ?></span></li>

		<?php if($summary['discount']>0){/*discount applies*/?>
			<li class="wppizza-order-discount"><?php echo $orderlbl['discount'] ?><span><span class="wppizza-minus"></span><?php echo $order['currency_left'].''.$summary['discount'].''.$order['currency_right']; ?></span></li>
		<?php } ?>

		<?php if($summary['item_tax']>0 && $summary['tax_applied']=='items_only' ){/*tax appplied to items only*/ ?>
			<li class="wppizza-order-item-tax"><?php echo $orderlbl['item_tax_total'] ?><span><?php echo $order['currency_left'].''.$summary['item_tax'].''.$order['currency_right']; ?></span></li>
		<?php } ?>

		<?php if(!isset($summary['selfPickup']) ||  $summary['selfPickup']==0){ /*no self pickup enabled or chosen :conditional  NEW IN VERSION  v1.4.1*/ ?>
			<?php if($summary['delivery_charges']!='' ){/*delivery charges if any*/?>
				<li class="wppizza-order-pickup"><?php echo $orderlbl['delivery_charges'] ?><span><?php echo $order['currency_left'].''.$summary['delivery_charges'].''.$order['currency_right']; ?></span></li>
			<?php }else{ ?>
				<li class="wppizza-order-pickup"><?php echo $orderlbl['delivery_charges'] ?><span><?php echo $orderlbl['free_delivery'] ?></span></li>
			<?php } ?>
		<?php } ?>

		<?php if($summary['item_tax']>0 && $summary['tax_applied']=='items_and_shipping' ){/*tax appplied to items_and_shipping */ ?>
			<li class="wppizza-order-item-tax"><?php echo $orderlbl['item_tax_total'] ?><span><?php echo $order['currency_left'].''.$summary['item_tax'].''.$order['currency_right']; ?></span></li>
		<?php } ?>

		<?php if(isset($summary['handling_charge']) && $summary['handling_charge']>0){/*handling charges (probably only used for cc's) */ ?>
			<li class="wppizza-order-item-handling"><?php echo $orderlbl['order_page_handling'] ?><span><?php echo $order['currency_left'].''.$summary['handling_charge'].''.$order['currency_right']; ?></span></li>
		<?php } ?>

		<?php if(isset($summary['tips']) && $summary['tips']>0){/*tips and gratuities */ ?>
			<li class="wppizza-order-item-tax"><?php echo $orderlbl['tips'] ?><span><?php echo $order['currency_left'].''.$summary['tips'].''.$order['currency_right']; ?></span></li>
		<?php } ?>

		<?php if($summary['taxes_included']>0 && $summary['tax_applied']=='taxes_included' ){/*taxes included NEW IN VERSION  v2.8.9.3*/ ?>
			<li class="wppizza-order-item-tax"><?php echo $orderlbl['taxes_included'] ?><span><?php echo $order['currency_left'].''.$summary['taxes_included'].''.$order['currency_right']; ?></span></li>
		<?php } ?>

			<li id="wppizza-cart-total"><?php echo $orderlbl['order_total'] ?><span><?php echo $order['currency_left'].''.$summary['total'].''.$order['currency_right']; ?></span></li>
	</ul>
<?php	/**payment type (by default this is css->display:none)**/ 	?>
	<div class="wppizza-history-payment"><span><?php echo $orderlbl['order_paid_by'] ?> <?php echo $order['gatewayLabel']; ?> [<?php echo $order['transaction_id'] ?>]</span></div>
<?php
	do_action('wppizza_history_loop_after_summary',$oId,$j);
?>
</fieldset>
<?php
	do_action('wppizza_history_loop_end',$oId,$j);
?>
<?php	/****************************************************end of loop**************************************************************/ 	?>
<?php $j++;}} ?>
<?php
/*******************************************************
*
*
*	no previous orders
*
*
*******************************************************/
	if($numberOfOrders<=0){
		echo'<p class="wppizza-history-noorders">'.$orderlbl['history_no_previous_orders'].'</p>';
	}
?>
</div>
<?php
	/*****************output pagination****************************************/
	do_action('wppizza_history_after_orders',$numberOfOrders,$ordersOnPage);
?>