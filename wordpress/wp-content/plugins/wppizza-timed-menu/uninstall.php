<?php
if(!defined('WP_UNINSTALL_PLUGIN') ){
    exit();
}

/**get rid of all custom roles***/
function delete_wppizza_tm_custom_roles(){
	global $wp_roles;

	$wppizzaTmCap[]='wppizza_cap_wpptm_timed_items';
	$wppizzaTmCap[]='wppizza_cap_wpptm_localization';
	$wppizzaTmCap[]='wppizza_cap_wpptm_options';
	$wppizzaTmCap[]='wppizza_cap_wpptm_license';
	$wppizzaTmCap[]='wppizza_cap_wpptm_access';
	$wppizzaTmCap[]='wppizza_cap_wpptm_howto';

	foreach($wp_roles->roles as $roleName=>$v){
		$userRole = get_role($roleName);
		foreach($wppizzaTmCap as $cap){
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
           		delete_option('wppizza_timed_menu');
				/*delete custom roles*/
				delete_wppizza_tm_custom_roles();
			}
			restore_current_blog();
		}
	}else{
		delete_option('wppizza_timed_menu');
		/*delete custom roles*/
		delete_wppizza_tm_custom_roles();
	}
?>