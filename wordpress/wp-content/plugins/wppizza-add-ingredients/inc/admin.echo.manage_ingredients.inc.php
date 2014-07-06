<?php /*do and echo tabs***/
if ( isset ( $_GET['tab'] ) ){
	$this->wppizza_admin_ingredients_tabs($_GET['tab']);
} else {
	$this->wppizza_admin_ingredients_tabs(''.$this->pluginAccessCapabilities[0]['tab'].'');
}
?>