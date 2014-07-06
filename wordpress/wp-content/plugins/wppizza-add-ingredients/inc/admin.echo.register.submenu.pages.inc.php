<?php
/*no cheating please**/
$wppizzaIngCaps=$this->wppizza_ingredients_capabilities_tabs();

if(isset($wppizzaIngCaps[$tab]['cap'])){
$requiredCap=$wppizzaIngCaps[$tab]['cap'];

if(current_user_can($requiredCap)){

	/**add ingredients page. if we have at least one capability register the page by just choosing the first available capability**/
	add_submenu_page('edit.php?post_type='.WPPIZZA_POST_TYPE.'',WPPIZZA_NAME.' '.__('Manage Ingredients', $this->pluginLocale),__('&#8919; Ingredients', $this->pluginLocale), $this->pluginAccessCapabilities[0]['cap'],$this->pluginSlug, array($this, 'admin_manage_ingredients'));
	/**register settings**/
	register_setting($this->pluginSlug,$this->pluginSlug, array( $this, 'wppizza_addingredients_admin_options_validate') );

	/**add settings section for ingredients**/
	add_settings_section('ingredients','',  array( $this, 'wppizza_admin_page_text_header'), 'ingredients');

	if(current_user_can('wppizza_ingr_cap_ingredients')){
		if($tab == 'ingredients'){
			add_settings_field('version','<b>'.WPPIZZA_ADDINGREDIENTS_NAME.' '. __('Version', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'version' );
			add_settings_field('ingredients','<b>'. __('Ingredients', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients' );
		}
	}
	if(current_user_can('wppizza_ingr_cap_options')){
		if($tab == 'options'){
			add_settings_field('ingredients_in_popup','<b>'.  __('Show Ingredients in a popup window/layer ?', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_in_popup' );
			add_settings_field('ingredients_added_sticky','<b>'.  __('Sticky Added Ingredients', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_added_sticky' );
			add_settings_field('ingredients_in_popup_wpc','<b>'.  __('Popup Width', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_in_popup_wpc' );
			add_settings_field('ingredients_in_popup_anim','<b>'.  __('Popup Animation Speed', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_in_popup_anim' );
			add_settings_field('ingredients_show_count','<b>'.  __('Show count on ingredient when adding ?', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_show_count' );
			add_settings_field('ingredients_omit_single_count','<b>'.  __('Omit "1x" in cart, order and account hostory?', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_omit_single_count' );			
			add_settings_field('ingredients_addasis_button_enabled','<b>'.  __('Show \'add to cart\' button alongside everywhere/half/quarters ?', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_addasis_button_enabled' );						
			add_settings_field('ingredients_added_show_price','<b>'.  __('*Added* ingredients display options', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_added_show_price' );
			add_settings_field('ingredients_show_depreselected','<b>'.  __('Show de-selected pre-selected ingredients ?', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_show_depreselected' );
		}
	}
	if(current_user_can('wppizza_ingr_cap_groups')){
		if($tab == 'custom-groups'){
			add_settings_field('ingredients_custom_groups','<b>'.  __('Custom Ingredients Groups', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'ingredients_custom_groups' );
		}
	}
	if(current_user_can('wppizza_ingr_cap_localization')){
		if($tab == 'localization'){
			add_settings_field('localization','<b>'. __('Localization', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'localization' );
		}
	}
	if(current_user_can('wppizza_ingr_cap_access')){
		if($tab == 'access-level'){
			add_settings_field('access_level','<b>'. __('Set Access Rights', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'access_level' );
		}
	}
	if(current_user_can('wppizza_ingr_cap_howto')){
		if($tab == 'manual'){
			add_settings_field('custom_groups','<b>'. __('How To Use Custom Groups:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'custom_groups' );
		}
	}
	if(current_user_can('wppizza_ingr_cap_license')){
		if($tab == 'license'){
			add_settings_field('license','<b>'. __('Manage your license:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'ingredients', 'ingredients', 'license' );
		}
	}
}}
?>