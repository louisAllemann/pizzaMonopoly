<?php
global $wpdb;
/*no backticks or apostrophies around fieldnames please**/
/** indexes should be called KEY instead of INDEX. PRIMARY KEY must have 2 spaces before the ()**/
/** if using  multiple column keys there can be no spaces between commas**/
/** see http://codex.wordpress.org/Creating_Tables_with_Plugins **/


//Drop Old Indexes if table exists to avoid duplicates and all sorts of other issues
$table_name = "".$wpdb->prefix . $this->pluginOrderTable ."";
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
	$tblIndexes = $wpdb->get_results("SHOW INDEX FROM " . $table_name ."", ARRAY_A);
	$tIdx=array();
	foreach($tblIndexes as $idx){
		if($idx['Key_name']!='PRIMARY' && !in_array($idx['Key_name'],$tIdx)){
			$tIdx[]=$idx['Key_name'];
		}
	}
	if(count($tIdx)>0){
		$sql = "ALTER TABLE " . $table_name ." DROP INDEX `".implode("`, DROP INDEX `",$tIdx)."`";
		$wpdb->query($sql);
	}
}
/**create and alter table via dbDelta**/
if(isset($setNewOrderStatus) && is_array($setNewOrderStatus)){
	$dbOrderStatus="'".implode("','",$setNewOrderStatus)."'";
}else{
	$dbOrderStatus="'".implode("','",wppizza_custom_order_status())."'";
}

$sql="CREATE TABLE ".$wpdb->prefix ."wppizza_orders (
	id INT(10) NOT NULL AUTO_INCREMENT,
	wp_user_id INT(10) NOT NULL DEFAULT '0',
	order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	order_update TIMESTAMP,
	customer_details TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	order_details TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	order_status ENUM($dbOrderStatus) NOT NULL DEFAULT 'NEW',
	hash VARCHAR(64) NULL DEFAULT NULL,
	order_ini TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	customer_ini TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	payment_status ENUM('INITIALIZED','COMPLETED','PENDING','REFUNDED','AUTHORIZED','FAILED','INVALID','CANCELLED','OTHER','CAPTURED','COD','NOTAPPLICABLE') NULL DEFAULT 'COD',
	transaction_id VARCHAR(32) NULL DEFAULT NULL,
	transaction_details TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	transaction_errors TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	initiator VARCHAR(32) NULL DEFAULT 'COD',
	mail_construct TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	mail_sent ENUM('Y','N','ERROR') NULL DEFAULT 'N',
	mail_error TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	notes TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	PRIMARY KEY  (id),
	KEY hash (hash),
	KEY wp_user_id (wp_user_id),
	KEY orderdate_paymentstatus (order_date,payment_status),
	KEY payment_status (payment_status),
	KEY transaction_id (transaction_id),
	KEY ident (hash,payment_status,initiator),
	KEY history (wp_user_id,order_date,payment_status),
	KEY mail_sent (mail_sent)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
?>