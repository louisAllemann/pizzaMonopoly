<?php
/****************************************************************************************************************
*
*	WPPizza - Plaintext Email Template
*
*	Note: do not use html tags. it will not work . Know what you are doing.
*
*	if you are sending html emails with phpmailer (wppizza->settings->Select Type of Mail Delivery)
*	you could also comment OUT
*	$mail->AltBody = $this->orderMessage['plaintext'];
*	in wppizza-phpmailer-settings.php as phpmailer will automatically generate plaintext emails from the html input
*	if you do this, changes to this file will not make any difference
*	make sure however, that you copy wppizza-phpmailer-settings.php to your theme directory
*	as otherwise your changes will be overwritten in the next update of this plugin. just saying.....
*
****************************************************************************************************************/
?>
<?php
/****************************************************************************
*
*	[header: date and time of order,gateway used,transactionid  etc]
*
****************************************************************************/
?>
===========<?php echo $orderLabel['order_details'] ?>============
<?php echo $nowdate ?>

<?php echo $orderLabel['order_paid_by'] ?> <?php echo $gatewayLabel ?> (<?php echo $transactionId ?>)



<?php
/****************************************************************************
*
*	[customer details: whatever fields where enabled on order page]
*
****************************************************************************/
echo $emailPlaintext['customer_details'];
?>

<?php
/****************************************************************************
*
*	[order items: list of items ordered]
*	to make thing reasonably pretty in plaintext emails , we pad with spaces
*	as required as tabs do not seem to want to work
*
****************************************************************************/
$output='';

/***allow filtering of items (sort, add categories and whatnot)****/
$emailPlaintext['items'] = apply_filters('wppizza_emailplaintext_filter_items', $emailPlaintext['items'], 'plaintextemail');

foreach($emailPlaintext['items'] as $k=>$v){
	/***allow action per item - probably to use in conjunction with filter above****/
	$output = apply_filters('wppizza_emailplaintext_item', $v, $output);	

	$strPartLeft=''.$v['label'].'';/*made up of => '.$v['quantity'].'x '.$v['name'].' '.$v['size'].' ['.$v['currency'].' '.$v['price'].']'*/
	$spaces=75-strlen($strPartLeft);
	$strPartRight=''.$v['value'].'';/*made up of => '.$v['currency'].' '.$v['pricetotal'].'*/

	/**add to string, spacing left and right out somewhat and put linebreak before any additional info**/
	$output.=''.$strPartLeft.''.str_pad($strPartRight,$spaces," ",STR_PAD_LEFT).''.PHP_EOL.'';

	/**NOTE: DO NOT DELETE OR ALTER THE ADDITIONAL INFO DECLARATIONS OR YOU MIGHT BREAK THINGS. IF NOT NOW THAN POSSIBLY IN THE FUTURE AS OTHER EXTENSIONS MAY RELY ON THIS!!!*/
	if(isset($v['additional_info']) && trim($v['additional_info'])!=''){$output.=''.$v['additional_info'].''.PHP_EOL.'';}

	/**add additional line break as spacer between items**/
	//$output.=PHP_EOL;
}
/* print it */
echo''.$output.'';
/*****************************************************************************************************
	if you've changed the above and also want to store these changes in the order history->order details
	as opposed to just in plaintext emails uncomment the line below.
	YOU PROBABLY SHOULD DO A TEST ORDER AND CHECK HISTORY->ORDER DETAILS !!!
	IF IN DOUBT, LEAVE IT COMMENTED OUT
*****************************************************************************************************/
/* $emailPlaintext['db_items']=$output; */
?>


<?php
/************************************************************************************************
*
*	[order summary: price/tax/discount/delivery options etc]
*
************************************************************************************************/
echo $emailPlaintext['order_summary'];
?>

<?php
/****************************************************************************
*
*	[footer]
*
****************************************************************************/
?>
====================================================================

<?php
	echo $orderLabel['order_email_footer'];
?>