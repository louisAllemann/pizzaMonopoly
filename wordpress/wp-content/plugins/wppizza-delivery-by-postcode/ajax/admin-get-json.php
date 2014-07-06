<?php
error_reporting(0);
if(!defined('DOING_AJAX') || !DOING_AJAX){
	header('HTTP/1.0 400 Bad Request', true, 400);
	print"you cannot call this script directly";
  exit; //just for good measure
}
/**********set header********************/
header('Content-type: text/html');
$options=$this->dbpOptions;
$output='';
/**adding new delivery**/
if($_POST['vars']['field']=='delivery_areas' && $_POST['vars']['id']>=0){
	$output=$this->wppizza_dbp_admin_get_delivery_areas($_POST['vars']['field'],$_POST['vars']['id'],'',$options);
}
print"".$output."";
exit();
?>