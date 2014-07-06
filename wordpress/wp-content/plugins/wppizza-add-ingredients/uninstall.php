<?php
if(!defined('WP_UNINSTALL_PLUGIN') ){
    exit();
}
/**get rid of all custom roles***/
function delete_wppizza_ingr_custom_roles(){
	global $wp_roles;
	$wppizzaIngRoleCap=array();
	$wppizzaIngRoleCap[]='wppizza_ingr_cap_ingredients';
	$wppizzaIngRoleCap[]='wppizza_ingr_cap_groups';
	$wppizzaIngRoleCap[]='wppizza_ingr_cap_localization';
	$wppizzaIngRoleCap[]='wppizza_ingr_cap_access';
	$wppizzaIngRoleCap[]='wppizza_ingr_cap_howto';
	$wppizzaIngRoleCap[]='wppizza_ingr_cap_options';
	$wppizzaIngRoleCap[]='wppizza_ingr_cap_license';
	
	foreach($wp_roles->roles as $roleName=>$v){
		$userRole = get_role($roleName);
		foreach($wppizzaIngRoleCap as $cap){
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
           		delete_option('wppizza_addingredients');
           		delete_wppizza_ingr_custom_roles();
			}
			restore_current_blog();
		}
	}else{
		delete_option('wppizza_addingredients');
		delete_wppizza_ingr_custom_roles();
	}
?>