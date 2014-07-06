<?php
/*************************************************************************************************
*
*	WPPizza PHPMailer Settings
*	[settings to send emails with phpmailer]
*	[if you wish to edit these, copy this file to your template directory 
*	and edit it there so it does not get overwritten in future upgrades]
*
*
**************************************************************************************************/
//$mail->IsSMTP(); // uncomment if you want the class to use SMTP and set accordingly below
try {
	$mail->CharSet = $this->blogCharset; 
	/*if you want to use smtp set as required below**/
	/*settings here are for using gmail . adjust as needed **/	
  	//$mail->Host       = "mail.yourdomain.com"; // SMTP server
  	//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
  	//$mail->SMTPAuth   = true;                  // enable SMTP authentication
  	//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
  	//$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
  	//$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
  	//$mail->Username   = "yourusername@gmail.com";  // GMAIL username
  	//$mail->Password   = "yourpassword";            // GMAIL password
  	
  	/**if you want, you can also try setting the following***/ 
	//$mail->Priority = 1;//Priority : For most clients expecting the Priority header: 1 = High, 2 = Medium, 3 = Low
	//$mail->AddCustomHeader("X-MSMail-Priority: High"); //MS Outlook custom header May set to "Urgent" or "Highest" rather than "High"
	//$mail->AddCustomHeader("X-MimeOLE" , "Produced By Microsoft MimeOLE V6.00.2800.1441"); 
	//$mail->AddCustomHeader("X-Mailer" , "Microsoft Office Outlook, Build 11.0.5510"); // Faking Outlook Header     	
  	
  	

	/**who to send the order to**/
	foreach($this->pluginOptions['order']['order_email_to'] as $k=>$v){
		$mail->AddAddress(''.$v.'', '');
	}
	/**any bcc's set in options ?**/
	if(count($this->pluginOptions['order']['order_email_bcc'])>0){
		foreach($this->pluginOptions['order']['order_email_bcc'] as $bcc){
			$mail->AddBCC("".$bcc."");
		}
	}
	
	/**who the order is from. (if given). might be statically set**/
	if($orderFromEmail!=''){
		$fromName=trim($orderFromName); 
		if($fromName==''){$fromName='----';}
		$replyToCCName=trim($this->orderClientName); 
		if($replyToCCName==''){$replyToCCName='----';}	//if we want, we could set $fromName=get_bloginfo() for example if no name was given...		
		
		
		$mail->SetFrom(''.$orderFromEmail.'',''.$fromName.'');/*by default, these are the customer submitted name/email. However these might also be statically set in order settings*/
		$mail->AddReplyTo(''.$this->orderClientEmail.'', ''.$replyToCCName.'');
		$mail->AddCC(''.$this->orderClientEmail.'',''.$replyToCCName.'');
	}
	/**the subject**/
	$mail->Subject = '' . $this->subjectPrefix . $this->subject .  $this->subjectSuffix . '';
	
	/**the html**/
	$mail->MsgHTML($orderHtml);
	$mail->AltBody = $this->orderMessage['plaintext']; // optional - MsgHTML will create an alternate automatically, however this has been prettied up a little for this plugin. If you must, feel free to comment this line out though

	/**any attachments set in options ?**/
	if(isset($this->pluginOptions['order']['order_email_attachments']) && count($this->pluginOptions['order']['order_email_attachments'])>0){
		foreach($this->pluginOptions['order']['order_email_attachments'] as $attachment){
			if(is_file($attachment)){
			$mail->AddAttachment($attachment);
			}
		}
	}

	/**send the mail**/
	$mail->Send();
	
	$sendMail['status']=true;
} catch (phpmailerException $e) {
	$mailError=$e->errorMessage();//Pretty error messages from PHPMailer
	$sendMail['status']=false;
	$sendMail['error']=$mailError;
} catch (Exception $e) {
	$mailError=$e->getMessage();//Boring error messages from anything else!
	$sendMail['status']=false;
	$sendMail['error']=$mailError;
}
/**set to phpmailer to be able to identify function if errors are thrown**/
$sendMail['mailer']='phpmailer';
	
	/**
		some action hook, do with  this what  you will like so in your function.php:
			
		add_action('wppizza_phpmailer_sent', 'my_custom_phpmailer',10,2);
		function my_custom_phpmailer($mail,$mailsent){
			if($mailsent){
			//**do something with the $mail object (like curl post or whatnot)
			}
		}
	**/
	do_action('wppizza_phpmailer_sent',$mail, $sendMail['status']);
?>