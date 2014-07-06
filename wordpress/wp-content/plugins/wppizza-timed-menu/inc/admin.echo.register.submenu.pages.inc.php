<?php
	/**if we have at least one capability register the page by just choosing the first available capability**/
	add_submenu_page('edit.php?post_type='.WPPIZZA_POST_TYPE.'',WPPIZZA_NAME.' '.__('Timed Menu', $this->wpptmLocale),__('&#8919; Timed Menu', $this->wpptmLocale), $this->wpptmCurrentUserCaps['caps'][0],$this->wpptmSlug, array($this, 'wppizza_admin_manage_tm'));


	/**register settings**/
	register_setting($this->wpptmSlug,$this->wpptmSlug, array( $this, 'wppizza_admin_manage_tm_validate') );

	/**add settings section**/
	add_settings_section('wppizza_tm','',  array( $this, 'wppizza_admin_tm_page_text_header'), 'wppizza_tm');

	if(current_user_can('wppizza_cap_wpptm_timed_items')){
		if($tab == 'timed_items'){
			add_settings_field('version','<b>'.$this->wpptmClassName.' '. __('Version', $this->wpptmLocale).'</b>', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'version' );
			add_settings_field('display_type','<b>'. __('How do you display Wppizza Items and Categories ?', $this->wpptmLocale).'</b>', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'display_type' );
			add_settings_field('timed_items','<b>'. __('Timed Menu Items', $this->wpptmLocale).'</b>', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'timed_items' );
		}
	}

	if(current_user_can('wppizza_cap_wpptm_options')){
		if($tab == 'options'){
			add_settings_field('display_as_unavailable','<b>'. __('Display Menu Items as unavailable ?', $this->wpptmLocale).'</b>', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'display_as_unavailable' );
		}
	}
	if(current_user_can('wppizza_cap_wpptm_localization')){
		if($tab == 'localization'){
			add_settings_field('localization','<b>'. __('Localization', $this->wpptmLocale).'</b>', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'localization' );
		}
	}
	if(current_user_can('wppizza_cap_wpptm_license')){
		if($tab == 'license'){
			add_settings_field('license','<b>'. __('License', $this->wpptmLocale).'</b>', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'license' );
		}
	}
	if(current_user_can('wppizza_cap_wpptm_access')){
		if($tab == 'access'){
			add_settings_field('access','<b>'. __('Set Access Rights', $this->wpptmLocale).'</b>', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'access' );
		}
	}
	if(current_user_can('wppizza_cap_wpptm_howto')){
		if($tab == 'howto'){
			add_settings_field('howto','', array( $this, 'wppizza_admin_tm_settings_input'), 'wppizza_tm', 'wppizza_tm', 'howto' );
		}
	}
?>