<?php
	/**add menu page**/
	add_submenu_page('edit.php?post_type='.WPPIZZA_POST_TYPE.'',WPPIZZA_NAME.' '.__('Manage Delivery', $this->dbpLocale),__('&#8919; Post/Zip Codes', $this->dbpLocale), $this->wppdbpCurrentUserCaps['caps'][0] ,$this->dbpSlug, array($this, 'wppizza_dbp_manage_deliveries'));
	/**register settings**/
	register_setting($this->dbpSlug,$this->dbpSlug, array( $this, 'wppizza_dbp_admin_options_validate') );

	/**add settings section for ingredients**/
	add_settings_section('deliveries','',  array( $this, 'wppizza_dbp_admin_page_text_header'), 'deliveries');

	if(current_user_can('wppizza_cap_wppdbp_deliveries')){
		if($tab == 'deliveries'){
			add_settings_field('version','<b>'. __('Version', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'version' );
			add_settings_field('delivery_areas','<b>'. __('Delivery Post/Zip Codes', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'delivery_areas' );
		}
	}

	if(current_user_can('wppizza_cap_wppdbp_frontend_settings')){
		if($tab == 'frontend-settings'){
			add_settings_field('enabled','<b>'.  __('Enabled ?', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'enabled' );
			add_settings_field('required','<b>'.  __('Selection required ?', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'required' );
			add_settings_field('orderform_priority','<b>'.  __('Display Location on Orderform', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'orderform_priority' );
			add_settings_field('show_on_load','<b>'.  __('Popup on page load ?', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'show_on_load' );
			add_settings_field('instant_search','<b>'.  __('Show as Textbox instead of dropdown ?', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'instant_search' );
		}
	}

	if(current_user_can('wppizza_cap_wppdbp_localization')){
		if($tab == 'localization'){
			add_settings_field('localization','<b>'. __('Localization', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'localization' );
		}
	}

	if(current_user_can('wppizza_cap_wppdbp_access')){
		if($tab == 'access'){
			add_settings_field('access','<b>'. __('Set Access Rights', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'access' );
		}
	}

	if(current_user_can('wppizza_cap_wppdbp_license')){
		if($tab == 'license'){
			add_settings_field('license','<b>'. __('License', $this->dbpLocale).'</b>', array( $this, 'wppizza_dbp_admin_settings_input'), 'deliveries', 'deliveries', 'license' );
		}
	}
?>