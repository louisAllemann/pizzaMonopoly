<?php
/****************************************************************************************
*
*
*	WPPizza - Html Email Template
*	Note: do not use divs, stylesheets or  <style></style> declarations
*	many email clients either don't understand/render divs and/or strip any tags from the email
*	tables are definitely your best bet.....
*
****************************************************************************************/

/*
	to keep things consistant and easier to manage, let's put at least some colors etc into variables
	obviously you can change override things as required
	make sure you do not end up with values like background:; or any other invalid styles,
	as gmail - for example - will strip the whole style declaration

*/
$htmlEmailStyle['tableWidth']='500';
$htmlEmailStyle['pageBackgroundColor']='#FFFFFF';
$htmlEmailStyle['fontSize']='14px';
$htmlEmailStyle['fontFamily']='Verdana, Helvetica, Arial, sans-serif';
$htmlEmailStyle['textColour']='#444444';
$htmlEmailStyle['linkColor']='#21759B';
$htmlEmailStyle['aLinkColor']='#21759B';
$htmlEmailStyle['vLinkColor']='#21759B';
$htmlEmailStyle['categories']='padding:5px 0 0 5px;margin:0;text-decoration:underline';/**used when showing categories too*/
$htmlEmailStyle['mailBackgroundColor']='#F4F3F4';
$htmlEmailStyle['mailHeaderBackgroundColor']='#21759B';
$htmlEmailStyle['mailHeaderBackgroundImage']="";//something like: background:url('http://www.domain.com/logo.png') 10px 10px no-repeat;
$htmlEmailStyle['mailHeaderTextColour']='#FFFFFF';
$htmlEmailStyle['mailBorder']='border: 1px dotted #CECECE';
$htmlEmailStyle['mailDivider']='padding:0;border-top:1px dotted #CECECE;';
/*various padding vars**/
$htmlEmailStyle['mailPadding']['20x0x0x15']='padding:20px 0 0 15px';
$htmlEmailStyle['mailPadding']['2x15']='padding:2px 15px';
$htmlEmailStyle['mailPadding']['30']='padding:30px';
$htmlEmailStyle['mailPadding']['2x0x0x15']='padding:2px 0 0 15px';
$htmlEmailStyle['mailPadding']['0x15x15x30']='padding:0px 15px 15px 30px';
$htmlEmailStyle['mailPadding']['0x5']='padding:0 5px';
$htmlEmailStyle['mailPadding']['5']='padding:5px';
$htmlEmailStyle['mailPadding']['10x5']='padding:10px 5px';


/************************************************************************************
	new in version 2.8.9.2 allow filtering of styles
	through a filter rather than editing this template directly
***********************************************************************************/
$htmlEmailStyle = apply_filters('wppizza_filter_html_email_style', $htmlEmailStyle);

?>
<?php
/***********************************************

	[general header and opening tags]

************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;<?php echo get_option('blog_charset'); ?>" />
	</head>
	<body style="margin: 0px; background-color:<?php echo $htmlEmailStyle['pageBackgroundColor'] ?>;" text="<?php echo $htmlEmailStyle['textColour'] ?>" bgcolor="<?php echo $htmlEmailStyle['pageBackgroundColor'] ?>" link="<?php echo $htmlEmailStyle['linkColor'] ?>" alink="<?php echo $htmlEmailStyle['aLinkColor'] ?>" vlink="<?php echo $htmlEmailStyle['vLinkColor'] ?>" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
		<table cellpadding="0" cellspacing="0" width="100%" bgcolor="<?php echo $htmlEmailStyle['pageBackgroundColor'] ?>" border="0" style="margin:10px 0">
			<tr>
				<td>
					<center>
						<table width="<?php echo $htmlEmailStyle['tableWidth'] ?>" cellpadding="0" cellspacing="0" align="center" bgcolor="<?php echo $htmlEmailStyle['mailBackgroundColor'] ?>" style="<?php echo $htmlEmailStyle['mailBorder'] ?>;background:<?php echo $htmlEmailStyle['mailBackgroundColor'] ?>;font-size:<?php echo $htmlEmailStyle['fontSize'] ?>;color:<?php echo $htmlEmailStyle['textColour'] ?>;font-family:<?php echo $htmlEmailStyle['fontFamily'] ?>;">
<?php
/***********************************************

	[add some header]

************************************************/
?>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailPadding']['30'] ?>;text-align:center;<?php echo $htmlEmailStyle['mailHeaderBackgroundImage'] ?>;background-color:<?php echo $htmlEmailStyle['mailHeaderBackgroundColor'] ?>;"><h1 style="font-size:160%;color:<?php echo $htmlEmailStyle['mailHeaderTextColour'] ?>;"><?php echo get_bloginfo(); ?></h1></td></tr>
<?php
/***********************************************

	[add label, date transactionid, gateway info]

************************************************/
?>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailPadding']['20x0x0x15'] ?>"><?php echo $orderLabel['order_details']; ?></td></tr>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailPadding']['2x0x0x15'] ?>"><?php echo $nowdate; ?></td></tr>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailPadding']['2x0x0x15'] ?>"><?php echo $orderLabel['order_paid_by']; ?> <?php echo $gatewayLabel; ?> (<?php echo $transactionId; ?>)</td></tr>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailDivider'] ?>">&nbsp;</td></tr><?php /*add devider**/ ?>

<?php
/**********************************************************

	[customer details loop of all submittecd fields]

***********************************************************/
?>
<?php 	foreach($customer_details_array as $k=>$v){ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['2x15'] ?>;vertical-align:top"><?php echo $v['label']; ?></td><td><?php echo $v['value']; ?><td></tr>
<?php } ?>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailDivider'] ?>">&nbsp;</td></tr><?php /*add devider**/ ?>

<?php
/**********************************************************

	[order details loop name/price/additional info]

***********************************************************/
?>
<?php
		/***allow filtering of items (sort, add categories and whatnot)****/
		$order_items = apply_filters('wppizza_emailhtml_filter_items', $order_items, 'htmlemail');
		foreach($order_items as $k=>$v){
		/***allow action per item - probably to use in conjunction with filter above****/
		do_action('wppizza_emailhtml_item',$v,$htmlEmailStyle);
?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['2x15'] ?>"><?php echo $v['label']; ?></td><td><?php echo $v['value']; ?></td></tr>
		<?php if($v['additional_info']!=''){ /*only print if there's something to print*/ ?>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailPadding']['0x15x15x30'] ?>;font-size:90%"><?php echo $v['additional_info']; ?></td></tr>

		<?php } ?>
<?php } ?>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailDivider'] ?>">&nbsp;</td></tr><?php /*add devider**/ ?>

<?php
/**********************************************************

	[order summaries - sum of items, discount, delivery, total, tax, self pickup if selected]

***********************************************************/
?>
		<?php /**cart items summary**/ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['0x5'] ?>"><?php echo $order_summary['cartitems']['label']; ?></td><td><?php echo $currency_left.''.$order_summary['cartitems']['price'].''.$currency_right; ?></td></tr>

		<?php /**if discounts applied**/ ?>
		<?php if(isset($order_summary['discount'])){ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['0x5'] ?>"><?php echo $order_summary['discount']['label']; ?></td><td>-<?php echo $currency_left.''.$order_summary['discount']['price'].''.$currency_right; ?></td></tr>
		<?php } ?>

		<?php /**if items are taxed -> NEW IN VERSION 2.0**/ ?>
		<?php if(isset($order_summary['item_tax'])){ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['0x5'] ?>"><?php echo $order_summary['item_tax']['label']; ?></td><td><?php echo $currency_left.''.$order_summary['item_tax']['price'].''.$currency_right; ?></td></tr>
		<?php } ?>

		<?php /**delivery costs (if free or self pickup, price and currency will be empty)**/ ?>
		<?php if(isset($order_summary['delivery'])){ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['0x5'] ?>"><?php echo $order_summary['delivery']['label']; ?></td><td><?php if($order_summary['delivery']['price']!=''){echo $currency_left.''.$order_summary['delivery']['price'].''.$currency_right;} ?></td></tr>
		<?php } ?>

		<?php /**handling charges if any**/ ?>
		<?php if(isset($order_summary['handling_charge'])){ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['0x5'] ?>"><?php echo $order_summary['handling_charge']['label']; ?></td><td><?php echo $currency_left.''.$order_summary['handling_charge']['price'].''.$currency_right; ?></td></tr>
		<?php } ?>
		<?php /**tips if any**/ ?>
		<?php if(isset($order_summary['tips'])){ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['0x5'] ?>"><?php echo $order_summary['tips']['label']; ?></td><td><?php echo $currency_left.''.$order_summary['tips']['price'].''.$currency_right; ?></td></tr>
		<?php } ?>

		<?php /**taxes included**/ ?>
		<?php if(isset($order_summary['taxes_included'])){ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['0x5'] ?>"><?php echo $order_summary['taxes_included']['label']; ?></td><td><?php echo $currency_left.''.$order_summary['taxes_included']['price'].''.$currency_right; ?></td></tr>
		<?php } ?>

		<?php /*add devider**/ ?>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailDivider'] ?>">&nbsp;</td></tr>

		<?php /**total**/ ?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['5'] ?>;font-weight:600"><?php echo $order_summary['total']['label']; ?></td><td style="font-weight:600"><?php echo $currency_left.''.$order_summary['total']['price'].''.$currency_right; ?></td></tr>

		<?php /**self pickup if selected. NEW IN VERSION 1.4.1**/ ?>
		<?php if(isset($order_summary['self_pickup'])){?>
							<tr><td style="<?php echo $htmlEmailStyle['mailPadding']['10x5'] ?>;font-weight:600;color:#ff3333" colspan="2"><?php echo $order_summary['self_pickup']['label']; ?></td></tr>
		<?php } ?>

<?php
/**********************************************************

	[some thank you text or whatever]

***********************************************************/
?>
<?php
if(trim($orderLabel['order_email_footer'])!=''){
?>
		<?php /*add devider**/ ?>
							<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailDivider'] ?>">&nbsp;</td></tr>
		<tr><td colspan="2" style="<?php echo $htmlEmailStyle['mailPadding']['2x15'] ?>; font-size:90%"><?php echo $orderLabel['order_email_footer']; ?></td></tr>
<?php } ?>
<?php
/**********************************************************

	[general body table,html closing tags]

***********************************************************/
?>
						</table>
					</center>
				</td>
			</tr>
		</table>
	</body>
</html>