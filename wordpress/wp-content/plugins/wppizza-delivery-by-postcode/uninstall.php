<?php
if(!defined('WP_UNINSTALL_PLUGIN') ){
    exit();
}


/**get rid of all custom roles***/
function delete_wppizza_dbp_custom_roles(){
	global $wp_roles;

	$wppizzaDbpCap[]='wppizza_cap_wppdbp_deliveries';
	$wppizzaDbpCap[]='wppizza_cap_wppdbp_frontend_settings';
	$wppizzaDbpCap[]='wppizza_cap_wppdbp_localization';
	$wppizzaDbpCap[]='wppizza_cap_wppdbp_access';
	$wppizzaDbpCap[]='wppizza_cap_wppdbp_license';

	foreach($wp_roles->roles as $roleName=>$v){
		$userRole = get_role($roleName);
		foreach($wppizzaDbpCap as $cap){
			$userRole->remove_cap( ''.$cap.'' );
		} 
	}
}




    /*delete options*/
	if ( is_multisite() ) {
		global $wpdb;
 	   	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
 	   		if ($blogs) {
        	foreach($blogs as $blog) {
           		switch_to_blog($blog['blog_id']);
           		delete_option('wppizza_dbp');
				/*delete custom roles*/
				delete_wppizza_dbp_custom_roles();           		
			}
			restore_current_blog();
		}
	}else{
		delete_option('wppizza_dbp');
		/*delete custom roles*/
		delete_wppizza_dbp_custom_roles();  		
	}
?>