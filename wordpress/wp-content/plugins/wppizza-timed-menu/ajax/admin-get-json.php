<?php
error_reporting(0);
if(!defined('DOING_AJAX') || !DOING_AJAX){
	header('HTTP/1.0 400 Bad Request', true, 400);
	print"you cannot call this script directly";
  exit; //just for good measure
}
/**********set header********************/
header('Content-type: text/html');
$output='';
/**adding new delivery**/
if($_POST['vars']['field']=='timed_items' && $_POST['vars']['id']>=0 && $_POST['vars']['displayOption']!=''){
	/**get all relevant items cats pages and psts*/
	$itemsCatsPagesPosts=$this->wppizza_tm_admin_items_cats_pages_posts();
	$output=$this->wppizza_tm_admin_get_timed_menue($_POST['vars']['field'],$_POST['vars']['id'],'',$this->wpptmOptions,$itemsCatsPagesPosts,$_POST['vars']['displayOption']);
}

/**********************************************************************************************************************
	[upadate options too when drag/dropping (reorder) as non-defined cats in slug might now be a different category]
**********************************************************************************************************************/
	if($_POST['vars']['field']=='cat_sort'){
		$order = explode(',', $_POST['vars']['order']);
		$firstKey=(int)str_replace("tag-","",$order[0]);
		/**this does not output anything but just updates option table**/
		$reOrder=$this->wppizza_tm_menu_update_option_on_reorder_category($firstKey);
		print"".json_encode($reOrder)."";
	die(1);
	}


print"".$output."";
exit();
?>