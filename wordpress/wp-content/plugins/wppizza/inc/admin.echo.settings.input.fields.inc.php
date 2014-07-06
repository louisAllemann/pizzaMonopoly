<?php
$options = $this->pluginOptions;
	if($options!=0){

	$optionInUse=wppizza_options_in_use();//outputs an array $arr=array(['sizes']=>array(),['additives']=>array());
	$optionSizes=wppizza_sizes_available($options['sizes']);//outputs an array $arr=array(['lbl']=>array(),['prices']=>array());
	$optionsDecimals=$options['layout']['hide_decimals'];


			if($field=='version'){
				echo "{$options['plugin_data'][$field]}";
			}
			if($field=='js_in_footer'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[plugin_data][".$field."]' type='checkbox'  ". checked($options['plugin_data'][$field],true,false)." value='1' />";
			}
			if($field=='wp_multisite_session_per_site'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[plugin_data][".$field."]' type='checkbox'  ". checked($options['plugin_data'][$field],true,false)." value='1' />";
			}
			if($field=='using_cache_plugin'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[plugin_data][".$field."]' type='checkbox'  ". checked($options['plugin_data'][$field],true,false)." value='1' />";
				echo" <span class='description'>".__('Will ALWAYS load the cart dynamically via ajax. Especially useful if your caching plugin does not support the exclusion of only parts of a page.', $this->pluginLocale)."</span>";
				echo"<br /><span class='description'><b>".__('Note: you still want to exclude your entire *order page* - or at least the main content of that page - from being cached in your cache plugin (please see the documentation for your choosen cache plugin for how to do this). After you enable this, clear your cache.', $this->pluginLocale)."</b></span>";
			}
			if($field=='mail_type'){
				//echo "<input id='".$field."' name='".$this->pluginSlug."[".$field."]' type='checkbox'  ". checked($options['plugin_data'][$field],true,false)." value='1' />";
				echo "<select name='".$this->pluginSlug."[plugin_data][".$field."]' />";
					echo"<option value='mail' ".selected($options['plugin_data'][$field],"mail",false).">".__('default [uses mail]', $this->pluginLocale)."</option>";
					echo"<option value='wp_mail' ".selected($options['plugin_data'][$field],"wp_mail",false).">".__('Wordpress Mail Function [uses wp_mail]', $this->pluginLocale)."</option>";
					echo"<option value='phpmailer' ".selected($options['plugin_data'][$field],"phpmailer",false).">".__('HTML and Plaintext [uses PHPMailer]', $this->pluginLocale)."</option>";
				echo "</select>";
			}
			if($field=='category_parent_page'){
				/*get all pages**/
				$pages=get_pages(array('post_type'=> 'page','echo'=>0,'title_li'=>''));

				/**check which pages have children (so we can exclude from dropdown as otherwise children pages will not be accessible*/
				$exclude=array();
				foreach($pages as $k=>$v){
					$children = get_pages('child_of='.$v->ID);
					if( count( $children ) != 0 ) {$exclude[]=$v->ID;}
				}
				$exclude[]=get_option('page_for_posts');/*also exclude page thats set for default posts*/

				echo "<select name='".$this->pluginSlug."[plugin_data][".$field."]' />";
					echo"<option value=''>".__('no parent [default]', $this->pluginLocale)."</option>";
				foreach($pages as $k=>$v){
					if(in_array($v->ID,$exclude)){
						echo"<option value='' style='color:red'>".$v->post_title." ".__('[not selectable]', $this->pluginLocale)."</option>";
					}else{
						if($options['plugin_data'][$field]==$v->ID){$sel=' selected="selected"';}else{$sel='';}
						echo"<option value='".$v->ID."' ".$sel.">".$v->post_title."</option>";
					}
				}
				echo "</select>";
			}
			if($field=='empty_category_and_items'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[plugin_data][".$field."]' type='checkbox'  value='1' />";
				echo" ".__('delete images too ?', $this->pluginLocale)."";
				echo" <input id='".$field."_delete_attachments' name='".$this->pluginSlug."[plugin_data][delete_attachments]' type='checkbox'  value='1' />";
				echo" ".__('empty order table ?', $this->pluginLocale)."";
				echo" <input id='".$field."_truncate_orders' name='".$this->pluginSlug."[plugin_data][truncate_orders]' type='checkbox'  value='1' />";
			}
			if($field=='include_css' ){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
				echo "<input name='".$this->pluginSlug."[layout][css_priority]' size='2' type='text'  value='{$options['layout']['css_priority']}' />";
				echo "".__('Stylesheet Priority', $this->pluginLocale)."";
				echo "<br />".__('By default, the stylesheet will be loaded AFTER the main theme stylesheet (which should have a priority of "10"). If you experience strange behaviour or layout issues (in conjunction with other plugins for example), you can try adjusting this priority here (the bigger the number, the later it gets loaded).', $this->pluginLocale)."";

			}
			if($field=='hide_decimals' ){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='show_currency_with_price'){
				echo "".__('do not show', $this->pluginLocale)." <input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='radio'  ".checked($options['layout'][$field],0,false)." value='0' /> ";
				echo "".__('on left', $this->pluginLocale)." <input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='radio'  ".checked($options['layout'][$field],1,false)." value='1' />";
				echo "".__('on right', $this->pluginLocale)." <input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='radio'  ".checked($options['layout'][$field],2,false)." value='2' />";
			}


			if($field=='items_group_sort_print_by_category'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
				echo" <span class='description'>".__('displays categories in cart, order page and emails', $this->pluginLocale)."</span>";
				echo "<br />";
				echo "<br />";
				echo" <span class='description'>".__('How would you like to display the categories in order pages and emails ? [only relevant in hierarchical category structure]', $this->pluginLocale)."</span>";
				echo "<br />";
				echo "".__('full path', $this->pluginLocale)." <input name='".$this->pluginSlug."[layout][items_category_hierarchy]' type='radio'  ".checked($options['layout']['items_category_hierarchy'],'full',false)." value='full' /> ";
				echo "".__('parent category', $this->pluginLocale)." <input name='".$this->pluginSlug."[layout][items_category_hierarchy]' type='radio'  ".checked($options['layout']['items_category_hierarchy'],'parent',false)." value='parent' />";
				echo "".__('topmost category', $this->pluginLocale)." <input name='".$this->pluginSlug."[layout][items_category_hierarchy]' type='radio'  ".checked($options['layout']['items_category_hierarchy'],'topmost',false)." value='topmost' />";
				echo "<br />";
				echo "<br />";
				echo" <span class='description'>".__('How would you like to display the categories in the cart ?  [as the cart might have space restrictions you can adjust this separately]', $this->pluginLocale)."</span>";
				echo "<br />";
				echo "".__('do not display categories', $this->pluginLocale)." <input name='".$this->pluginSlug."[layout][items_category_hierarchy_cart]' type='radio'  ".checked($options['layout']['items_category_hierarchy_cart'],'none',false)." value='none' /> ";
				echo "".__('full path', $this->pluginLocale)." <input name='".$this->pluginSlug."[layout][items_category_hierarchy_cart]' type='radio'  ".checked($options['layout']['items_category_hierarchy_cart'],'full',false)." value='full' /> ";
				echo "".__('parent category', $this->pluginLocale)." <input name='".$this->pluginSlug."[layout][items_category_hierarchy_cart]' type='radio'  ".checked($options['layout']['items_category_hierarchy_cart'],'parent',false)." value='parent' />";
				echo "".__('topmost category', $this->pluginLocale)." <input name='".$this->pluginSlug."[layout][items_category_hierarchy_cart]' type='radio'  ".checked($options['layout']['items_category_hierarchy_cart'],'topmost',false)." value='topmost' />";
				echo "<br />";
				echo "<br />";
				echo "<input name='".$this->pluginSlug."[layout][items_category_separator]' size='2' type='text'  value='{$options['layout']['items_category_separator']}' />";
				echo" <span class='description'>".__('Category Separator', $this->pluginLocale)."</span>";
			}

			if($field=='currency_symbol_left'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='currency_symbol_position'){
				echo "".__('on left', $this->pluginLocale)." <input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='radio'  ".checked($options['layout'][$field],'left',false)." value='left' />";
				echo "".__('on right', $this->pluginLocale)." <input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='radio'  ".checked($options['layout'][$field],'right',false)." value='right' />";
			}
			if($field=='cart_increase'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}

			if($field=='items_per_loop' ){
				echo "<input name='".$this->pluginSlug."[layout][".$field."]' size='2' type='text'  value='{$options['layout'][$field]}' />";
			}
			if($field=='style'){
				echo "<select name='".$this->pluginSlug."[layout][".$field."]' />";
					foreach(wppizza_public_styles($options['layout'][$field]) as $k=>$v){
						echo"<option value='".$v['id']."' ".$v['selected'].">".$v['value']."</option>";
					}
				echo "</select>";
			}


			if($field=='add_to_cart_on_title_click' ){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}

			if($field=='opening_times_format'){
				echo"<span class='wppizza_label'>".__('Hours', $this->pluginLocale)."</span>";
				echo "<select name='".$this->pluginSlug."[".$field."][hour]' />";
						echo"<option value='G' ".selected($options[$field]['hour'],"G",false).">".__('24-hour format without leading zeros', $this->pluginLocale)."</option>";
						echo"<option value='g' ".selected($options[$field]['hour'],"g",false).">".__('12-hour format without leading zeros', $this->pluginLocale)."</option>";
						echo"<option value='H' ".selected($options[$field]['hour'],"H",false).">".__('24-hour format with leading zeros', $this->pluginLocale)."</option>";
						echo"<option value='h' ".selected($options[$field]['hour'],"h",false).">".__('12-hour format with leading zeros', $this->pluginLocale)."</option>";
				echo "</select>";
				echo "<br />";
				echo"<span class='wppizza_label'>".__('Separator', $this->pluginLocale)."</span>";
				echo "<select name='".$this->pluginSlug."[".$field."][separator]' />";
						echo"<option value='' ".selected($options[$field]['separator'],"",false).">".__('no seperator', $this->pluginLocale)."</option>";
						echo"<option value='&nbsp;' ".selected($options[$field]['separator'],"&nbsp;",false).">".__('space', $this->pluginLocale)."</option>";
						echo"<option value=':' ".selected($options[$field]['separator'],":",false).">:</option>";
						echo"<option value='.' ".selected($options[$field]['separator'],".",false).">.</option>";
						echo"<option value='-' ".selected($options[$field]['separator'],"-",false).">-</option>";
						echo"<option value=';' ".selected($options[$field]['separator'],";",false).">;</option>";
				echo "</select>";
				echo "<br />";
				echo"<span class='wppizza_label'>".__('Minutes', $this->pluginLocale)."</span>";
				echo "<select name='".$this->pluginSlug."[".$field."][minute]' />";
						echo"<option value='' ".selected($options[$field]['minute'],"",false).">".__('hide minutes', $this->pluginLocale)."</option>";
						echo"<option value='i' ".selected($options[$field]['minute'],"i",false).">".__('show minutes', $this->pluginLocale)."</option>";
				echo "</select>";
				echo "<br />";
				echo"<span class='wppizza_label'>".__('Show AM/PM ?', $this->pluginLocale)."</span>";
				echo "<select name='".$this->pluginSlug."[".$field."][ampm]' />";
						echo"<option value='' ".selected($options[$field]['ampm'],"",false).">".__('do not show', $this->pluginLocale)."</option>";
						echo"<option value='a' ".selected($options[$field]['ampm'],"a",false).">".__('lowercase', $this->pluginLocale)."</option>";
						echo"<option value='A' ".selected($options[$field]['ampm'],"A",false).">".__('UPPERCASE', $this->pluginLocale)."</option>";
						echo"<option value=' a' ".selected($options[$field]['ampm']," a",false).">".__('lowercase (with leading space)', $this->pluginLocale)."</option>";
						echo"<option value=' A' ".selected($options[$field]['ampm']," A",false).">".__('UPPERCASE (width leading space)', $this->pluginLocale)."</option>";
				echo "</select>";
			}


			if($field=='jquery_feedback_added_to_cart'){
				echo "<input id='' name='".$this->pluginSlug."[layout][jquery_fb_add_to_cart]' type='checkbox'  ". checked($options['layout']['jquery_fb_add_to_cart'],true,false)." value='1' />";
				echo" <span class='description'>".__('Replace item price with customised text when adding an item to cart [set/edit text in localization]', $this->pluginLocale)."</span>";
				echo "<br />";
				echo "<input id='' name='".$this->pluginSlug."[layout][jquery_fb_add_to_cart_ms]' size='4' type='text'  value='{$options['layout']['jquery_fb_add_to_cart_ms']}' />";
				echo" <span class='description'>".__('How long is it visible for before reverting back to displaying price [in ms]', $this->pluginLocale)."</span>";
			}



			if($field=='placeholder_img'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='prettyPhoto' ){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='prettyPhotoStyle'){
				echo "<select name='".$this->pluginSlug."[layout][".$field."]'>";
					echo "<option value='pp_default' ".selected($options['layout'][$field],"pp_default",false).">default</option>";
					echo "<option value='light_rounded' ".selected($options['layout'][$field],"light_rounded",false).">light rounded</option>";
					echo "<option value='dark_rounded' ".selected($options['layout'][$field],"dark_rounded",false).">dark rounded</option>";
					echo "<option value='light_square' ".selected($options['layout'][$field],"light_square",false).">light square</option>";
					echo "<option value='dark_square' ".selected($options['layout'][$field],"dark_square",false).">dark square</option>";
					echo "<option value='facebook' ".selected($options['layout'][$field],"facebook",false).">facebook</option>";
				echo "</select>";
				echo' '.__('see wppizza.prettyPhoto.custom.js.php if you want to adjust prettyPhoto options', $this->pluginLocale).'';
			}




			if($field=='hide_cart_icon'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='hide_item_currency_symbol'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='hide_single_pricetier'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='hide_prices'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='disable_online_order'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='suppress_loop_headers'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='empty_cart_button'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[layout][".$field."]' type='checkbox'  ". checked($options['layout'][$field],true,false)." value='1' />";
			}
			if($field=='sticky_cart_settings'){
				echo "<input id='' name='".$this->pluginSlug."[layout][sticky_cart_animation]' size='2' type='text'  value='{$options['layout']['sticky_cart_animation']}' />";
				echo" <span class='description'>".__('Animation Speed [in ms - 0 to disable animation]', $this->pluginLocale)."</span>";
				echo"<br />";
				echo "<select name='".$this->pluginSlug."[layout][sticky_cart_animation_style]'>";
					echo "<option value='' ".selected($options['layout']['sticky_cart_animation_style'],'',false).">---".__('no animation', $this->pluginLocale)."---</option>";
					echo "<option value='linear' ".selected($options['layout']['sticky_cart_animation_style'],'linear',false).">linear</option>";
					echo "<option value='swing' ".selected($options['layout']['sticky_cart_animation_style'],'swing',false).">swing</option>";
					echo "<option value='easeInQuad' ".selected($options['layout']['sticky_cart_animation_style'],'easeInQuad',false).">easeInQuad</option>";
					echo "<option value='easeOutQuad' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutQuad',false).">easeOutQuad</option>";
					echo "<option value='easeInOutQuad' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutQuad',false).">easeInOutQuad</option>";
					echo "<option value='easeInCubic' ".selected($options['layout']['sticky_cart_animation_style'],'easeInCubic',false).">easeInCubic</option>";
					echo "<option value='easeOutCubic' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutCubic',false).">easeOutCubic</option>";
					echo "<option value='easeInOutCubic' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutCubic',false).">easeInOutCubic</option>";
					echo "<option value='easeInQuart' ".selected($options['layout']['sticky_cart_animation_style'],'easeInQuart',false).">easeInQuart</option>";
					echo "<option value='easeOutQuart' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutQuart',false).">easeOutQuart</option>";
					echo "<option value='easeInOutQuart' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutQuart',false).">easeInOutQuart</option>";
					echo "<option value='easeInQuint' ".selected($options['layout']['sticky_cart_animation_style'],'easeInQuint',false).">easeInQuint</option>";
					echo "<option value='easeOutQuint' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutQuint',false).">easeOutQuint</option>";
					echo "<option value='easeInOutQuint' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutQuint',false).">easeInOutQuint</option>";
					echo "<option value='easeInExpo' ".selected($options['layout']['sticky_cart_animation_style'],'easeInExpo',false).">easeInExpo</option>";
					echo "<option value='easeOutExpo' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutExpo',false).">easeOutExpo</option>";
					echo "<option value='easeInOutExpo' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutExpo',false).">easeInOutExpo</option>";
					echo "<option value='easeInSine' ".selected($options['layout']['sticky_cart_animation_style'],'easeInSine',false).">easeInSine</option>";
					echo "<option value='easeOutSine' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutSine',false).">easeOutSine</option>";
					echo "<option value='easeInOutSine' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutSine',false).">easeInOutSine</option>";
					echo "<option value='easeInCirc' ".selected($options['layout']['sticky_cart_animation_style'],'easeInCirc',false).">easeInCirc</option>";
					echo "<option value='easeOutCirc' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutCirc',false).">easeOutCirc</option>";
					echo "<option value='easeInOutCirc' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutCirc',false).">easeInOutCirc</option>";
					echo "<option value='easeInElastic' ".selected($options['layout']['sticky_cart_animation_style'],'easeInElastic',false).">easeInElastic</option>";
					echo "<option value='easeOutElastic' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutElastic',false).">easeOutElastic</option>";
					echo "<option value='easeInOutElastic' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutElastic',false).">easeInOutElastic</option>";
					echo "<option value='easeInBack' ".selected($options['layout']['sticky_cart_animation_style'],'easeInBack',false).">easeInBack</option>";
					echo "<option value='easeOutBack' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutBack',false).">easeOutBack</option>";
					echo "<option value='easeInOutBack' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutBack',false).">easeInOutBack</option>";
					echo "<option value='easeInBounce' ".selected($options['layout']['sticky_cart_animation_style'],'easeInBounce',false).">easeInBounce</option>";
					echo "<option value='easeOutBounce' ".selected($options['layout']['sticky_cart_animation_style'],'easeOutBounce',false).">easeOutBounce</option>";
					echo "<option value='easeInOutBounce' ".selected($options['layout']['sticky_cart_animation_style'],'easeInOutBounce',false).">easeInOutBounce</option>";
				echo "</select>";
				echo" <span class='description'>".__('Animation Style ["no animation" to disable].<br />Note: any style other than "swing" or "linear" will additionally include "jquery.ui.effect" in the page [if not already loaded]', $this->pluginLocale)."</span>";
				echo"<br />";
				echo " <input id='' name='".$this->pluginSlug."[layout][sticky_cart_margin_top]' size='2' type='text'  value='{$options['layout']['sticky_cart_margin_top']}' />";
				echo" <span class='description'>".__('Distance from top of browser when cart is "sticky" to allow for theme specific requirements [in px]', $this->pluginLocale)."</span>";
				echo"<br />";
				echo " <input id='' name='".$this->pluginSlug."[layout][sticky_cart_background]' size='5' type='text'  value='{$options['layout']['sticky_cart_background']}' />";
				echo" <span class='description'>".__('Distinct CSS Background Colour when cart is "sticky" [hexdec (i.e #ffeeff) or string (i.e transparent, inherit, red etc)]', $this->pluginLocale)."</span>";
				echo"<br />";
				echo " <input id='' name='".$this->pluginSlug."[layout][sticky_cart_limit_bottom_elm_id]' size='5' type='text'  value='{$options['layout']['sticky_cart_limit_bottom_elm_id']}' />";
				echo" <span class='description'>".__('If you want to have a sticky cart NOT scroll further down that the TOP of a particular element that is further down on the page (might be useful in som layouts/themes), set that elements ID here [leave blank to ignore]', $this->pluginLocale)."</span>";
			}
			if($field=='opening_times_standard'){
				echo"<div id='wppizza_".$field."'>";
				foreach(wppizza_days() as $k=>$v){
					echo "<span class='wppizza_option'>";
					echo"<span class='wppizza_weekday'>".$v.":</span> ".__('open from', $this->pluginLocale).":";
					echo "<input name='".$this->pluginSlug."[".$field."][".$k."][open]' size='3' type='text' class='wppizza-time-select' value='{$options[$field][$k]['open']}' />";
					echo"".__('to', $this->pluginLocale).":";
					echo "<input name='".$this->pluginSlug."[".$field."][".$k."][close]' size='3' type='text' class='wppizza-time-select' value='{$options[$field][$k]['close']}' />";
					echo"</span>";
				}
				echo"</div>";
			}

			if($field=='opening_times_custom'){
				echo"<div id='wppizza_".$field."' >";
				echo"<div id='wppizza_".$field."_options'>";
				if(isset($options[$field]['date'])){
				foreach($options[$field]['date'] as $k=>$v){
					echo"".$this->wppizza_admin_section_opening_times_custom($field,$k,$options[$field]);
				}}
				echo"</div>";
				echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('add', $this->pluginLocale)."</a>";
				echo"</div>";
			}


			if($field=='times_closed_standard'){
				echo"<div id='wppizza_".$field."' >";
				echo"<div id='wppizza_".$field."_options'>";
				if(isset($options[$field]) && is_array($options[$field])){
				foreach($options[$field] as $k=>$v){
					echo"".$this->wppizza_admin_section_times_closed_standard($field,$k,$v);
				}}
				echo"</div>";
				echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('add', $this->pluginLocale)."</a>";
				echo"</div>";
			}


			if($field=='currency'){
				echo "<select name='".$this->pluginSlug."[order][".$field."]'>";
				foreach(wppizza_currencies($options['order'][$field]) as $l=>$m){
					echo "<option value='".$m['id']."' ".$m['selected'].">[".$m['id']."] - ".$m['value']."</option>";
				}
				echo "</select>";
			}

			if($field=='orderpage'){
				wp_dropdown_pages('name='.$this->pluginSlug.'[order]['.$field.']&selected='.$options['order'][$field].'&show_option_none='.__('select your orderpage', $this->pluginLocale).'');
				echo " ".__('Exclude from Navigation ?', $this->pluginLocale)." <input id='orderpage_exclude' name='".$this->pluginSlug."[order][orderpage_exclude]' type='checkbox'  ". checked($options['order']['orderpage_exclude'],true,false)." value='1' />";
			}

			if($field=='order_form'){

				asort($options[$field]);

				echo"<table id='wppizza_".$field."'>";
					echo"<tr><th>".__('Sort', $this->pluginLocale)."</th><th>".__('Label', $this->pluginLocale)."</th><th>".__('Enabled', $this->pluginLocale)."</th><th>".__('Required', $this->pluginLocale)."</th><th>".__('Also Required<br />on Pickup', $this->pluginLocale)."</th><th>".__('Prefill<br />[if known]', $this->pluginLocale)."</th><th>".__('Use when<br />Registering ?', $this->pluginLocale)."</th><th>".__('Type', $this->pluginLocale)."</th></tr>";
				foreach($options[$field] as $k=>$v){
					$disableRegister=false;$disablePrefill=false;$fixedType='';$fixedTypeLabel='';

					if($v['key']=='cemail'){$disableRegister=true;$fixedType='email';$fixedTypeLabel='email';}
					if($v['key']=='ctips'){$disableRegister=true;$disablePrefill=true;$fixedType='tips';$fixedTypeLabel='';}
					if($v['key']=='csurcharges'){$disableRegister=true;$disablePrefill=true;$fixedType='selectcustom';}


					if($v['key']=='cemail'){$style=' style="margin-bottom:0"';}else{$style='';}
					echo"<tr class='".$v['key']."'>";
					echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][sort]' size='1' type='text' value='".$v['sort']."' /></td>";
					echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][lbl]' size='15' type='text' value='".$v['lbl']."' /></td>";
					echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][enabled]' type='checkbox' ". checked($v['enabled'],true,false)." value='1' /></td>";
					echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][required]' type='checkbox' ". checked($v['required'],true,false)." value='1' /></td>";
					echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][required_on_pickup]' type='checkbox' ". checked($v['required_on_pickup'],true,false)." value='1' /></td>";
					echo"<td>";
						if(!$disablePrefill){echo"<input name='".$this->pluginSlug."[".$field."][".$k."][prefill]' type='checkbox' ". checked($v['prefill'],true,false)." value='1' />";}else{echo"".__('N/A', $this->pluginLocale)."";}
					echo"</td>";
					echo"<td>";
						if(!$disableRegister){echo"<input name='".$this->pluginSlug."[".$field."][".$k."][onregister]' type='checkbox' ". checked($v['onregister'],true,false)." value='1' />";}else{echo"".__('N/A', $this->pluginLocale)."";}
					echo"</td>";

					echo "<td>";

						if($fixedType!=''){
							echo "<input type='hidden' id='".$this->pluginSlug."_".$field."_type_".$k."' name='".$this->pluginSlug."[".$field."][".$k."][type]' value='".$fixedType."' />";
							echo "".$fixedTypeLabel."";
						}else{
							echo "<select id='".$this->pluginSlug."_".$field."_type_".$k."' class='".$this->pluginSlug."_".$field."_type' name='".$this->pluginSlug."[".$field."][".$k."][type]' />";
								echo'<option value="text" '.selected($v['type'],"text",false).'>text</option>';
								echo'<option value="email" '.selected($v['type'],"email",false).'>email</option>';
								echo'<option value="textarea" '.selected($v['type'],"textarea",false).'>textarea</option>';
								echo'<option value="select" '.selected($v['type'],"select",false).'>select</option>';
							echo "</select>";
						}

						$display=' style="display:none"';$val='';

						if($v['type']=='select'){$display='';$val=''.implode(",",$v['value']).'';}
						if($v['type']=='selectcustom'){$display='';
							$valArr=array();
							foreach($v['value'] as $vKey=>$vVal){
								$valArr[]=''.$vKey.':'.$vVal.'';
							}
							$val=implode("|",$valArr);
						}


						echo "<span class='".$this->pluginSlug."_".$field."_select'".$display.">";
							echo "<input name='".$this->pluginSlug."[".$field."][".$k."][value]' type='text' value='".$val."' />";
						echo "</span>";
						echo "<span class='".$this->pluginSlug."_".$field."_select'".$display.">";
							if($v['type']!='selectcustom'){echo "<span class='description'>".__('separate multiple with comma', $this->pluginLocale)."</span>";}
							if($v['type']=='selectcustom'){echo "".__('enter required value pairs', $this->pluginLocale)."";}
						echo "</span>";
					echo"</td>";// ".$v['key']." ".$v['type']."
					echo"</tr>";

					if($v['key']=='ctips'){
						echo"<tr class='".$v['key']."'><td colspan='8' style='margin:0;padding:0 0 0 10px'>";
						echo"<span class='description'>";
						echo"".__('<b>Tips/Gratuities:</b> allow the customer can enter a <b>numerical</b> amount to be used as tips/gratuities.<br />This field will not be added to the users profile and can therefore not be pre-filled or used in the registration form.', $this->pluginLocale)."";
						/**the following notice can probably be removed in a few months**/
						if (class_exists( 'WPPIZZA_GATEWAY_PAYPAL') ) {
							$pluginPath=dirname(dirname(plugin_dir_path( __FILE__ )));
							$gwPaypalData=get_plugin_data($pluginPath.'/wppizza-gateway-paypal/wppizza-gateway-paypal.php', false, false );
							if( version_compare( $gwPaypalData['Version'], '2.1' , '<' )) {
								echo"<br /><span style='color:red'>If you want to enable this field you MUST update to Wppizza Paypal Gateway 2.1+.<br />If your version of the paypal gateway is < 2.0 <a href='mailto:dev@wp-pizza.com'> contact me</a> with your purchase id for an update.<br />If your version is >= 2.0 you should be able to update via your dashboard (provided you activated your license).<br />This notice will disappear as soon as you have updated the Paypal Gateway. </span>";
							}
						}
						/*********************end of notice******************************/
						echo"</span>";
						echo"</td></tr>";
					}
				}
				echo"</table>";
			}

			if($field=='confirmation_form'){
				echo"<hr /><br />";
				echo "<input id='confirmation_form_enabled' name='".$this->pluginSlug."[confirmation_form_enabled]' type='checkbox'  ". checked($options['confirmation_form_enabled'],true,false)." value='1' />";
				echo"<span class='description'><b>".__('Some Countries/Jurisdictions require another, final , non-editable confirmation page before sending the order. If this is the case, tick this box and save. You will get some additional formfields you can make available in that final form', $this->pluginLocale)."</b></span>";
				if($options['confirmation_form_enabled']){
				echo"<br /><span style='color:red'>Please note, this is still a bit experimantal, please let me know if you experience problems with this</span>";
				}
				echo"<br /><br />";
				if($options['confirmation_form_enabled']){
					
					/***form fields*/
					asort($options[$field]);
					echo"<table id='wppizza_".$field."'>";
						echo"<tr><th colspan='5'>".__('Legal', $this->pluginLocale)." <span class='description'>[".__('enable some formfields or text/links you might want to use and/or make required', $this->pluginLocale)."]</span></th></tr>";
						echo"<tr><th>".__('Sort', $this->pluginLocale)."</th><th>".__('Label [html allowed]', $this->pluginLocale)."</th><th>".__('Enabled', $this->pluginLocale)."</th><th>".__('Required', $this->pluginLocale)."</th><th>".__('Type', $this->pluginLocale)."</th></tr>";
					foreach($options[$field] as $k=>$v){
						echo"<tr class='".$v['key']."'>";
						echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][sort]' size='1' type='text' value='".$v['sort']."' /></td>";
						echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][lbl]' size='55' type='text' value='".esc_html($v['lbl'])."' /></td>";
						echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][enabled]' type='checkbox' ". checked($v['enabled'],true,false)." value='1' /></td>";
						echo"<td><input name='".$this->pluginSlug."[".$field."][".$k."][required]' type='checkbox' ". checked($v['required'],true,false)." value='1' /></td>";
						echo"<td>";
							echo "<select id='".$this->pluginSlug."_".$field."_type_".$k."' class='".$this->pluginSlug."_".$field."_type' name='".$this->pluginSlug."[".$field."][".$k."][type]' />";
								echo'<option value="checkbox" '.selected($v['type'],"checkbox",false).'>'.__('checkbox', $this->pluginLocale).'</option>';
								echo'<option value="text" '.selected($v['type'],"text",false).'>'.__('text/link', $this->pluginLocale).'</option>';
							echo "</select>";
						echo"</td>";
						echo"</tr>";
	
					}
					echo"</table>";
				
					/***localization****/
					/**to get descriptions include default options. do not use require_once, as we need this more than once**/
					require(WPPIZZA_PATH .'inc/admin.setup.default.options.inc.php');
					/**add description to array**/
					$localizeOptions=array();
					foreach($defaultOptions['localization_confirmation_form'] as $k=>$v){
						$localizeOptions[$k]['descr']=$v['descr'];
						$localizeOptions[$k]['lbl']=$options['localization_confirmation_form'][$k]['lbl'];
					}
					asort($localizeOptions);
					echo"<table id='wppizza_".$field."'>";
					echo"<tr><th>".__('Localization', $this->pluginLocale)."</th></tr>";
					foreach($localizeOptions as $k=>$v){
						echo "<tr><td>";
						echo "<input name='".$this->pluginSlug."[localization_confirmation_form][".$k."]' size='30' type='text' value='".$v['lbl']."' />";
						echo"<span class='description'>".$v['descr']."</span>";
						if($k=='change_order_details'){
							echo"<br />";
							wp_dropdown_pages('name='.$this->pluginSlug.'[confirmation_form_amend_order_link]&selected='.$options['confirmation_form_amend_order_link'].'&show_option_none='.__('-- select page to link to --', $this->pluginLocale).'');
							echo"<span class='description'>".__('set link to page to allow customer to amend order', $this->pluginLocale)."</span>";
						}
						echo "</td></tr>";
					}
					echo"</table>";				
				}
			}
			
			if($field=='delivery'){
				/****sort in a more sensible manner**/
				$options['order'][$field]=array('no_delivery'=>$options['order'][$field]['no_delivery'],'minimum_total'=>$options['order'][$field]['minimum_total'],'standard'=>$options['order'][$field]['standard'],'per_item'=>$options['order'][$field]['per_item']);
				/**end custom sort**/

				echo "<span id='wppizza-delivery-options-select'>";
				foreach($options['order'][$field] as $k=>$v){
					echo "<span class='wppizza_option'>";
					echo "<input name='".$this->pluginSlug."[order][delivery_selected]' type='radio' ". checked($options['order']['delivery_selected']==$k,true,false)." value='".$k."' />";

					if($k=='no_delivery'){
						echo" ".__('No delivery offered / pickup only', $this->pluginLocale)."";
						echo"<br /><span class='description'>".__('removes any labels, text, charges, checkboxes etc associated with delivery options. You can still set a minimum order value below.', $this->pluginLocale)."</span>";

					}
					if($k=='minimum_total'){
						echo" ".__('Free delivery when total order value reaches', $this->pluginLocale).":";
						echo"<input name='".$this->pluginSlug."[order][".$field."][minimum_total][min_total]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field]['minimum_total']['min_total'],$optionsDecimals)."' />";
						echo"<div style='margin-left:20px'>";
						echo"<input name='".$this->pluginSlug."[order][".$field."][minimum_total][deliver_below_total]' type='checkbox' ". checked($v['deliver_below_total'],true,false)." value='1' />";
						echo" ".__('Deliver even when total order value is below minimum (the difference between total and "Minimum Total" above will be added to the Total as "Delivery Charges")', $this->pluginLocale)."";
						echo"<br />";
						echo"<span class='description'>".__('(If this is not selected and the total order is below the set value above, the customer will not be able to submit the order to you)', $this->pluginLocale)."</span>";
						echo"<br />";
						echo"<input name='".$this->pluginSlug."[order][".$field."][minimum_total][deliverycharges_below_total]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field]['minimum_total']['deliverycharges_below_total'],$optionsDecimals)."' />";
						echo" ".__('Fixed Delivery charges if order has not reached total for free delivery [0 to disable]', $this->pluginLocale)."";
						echo"<br />";
						echo" <em style='color:red'>(".__('if set (i.e. not 0) "Deliver even when total order value is below minimum" must be checked for this to have any effect', $this->pluginLocale).")</em>";

						echo"</div>";
					}
					if($k=='standard'){
						echo" ".__('Fixed Delivery Charges [added to order total]', $this->pluginLocale).":";
						echo "<input name='".$this->pluginSlug."[order][".$field."][standard][delivery_charge]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field]['standard']['delivery_charge'],$optionsDecimals)."' />";

					}
					if($k=='per_item'){
						echo" ".__('Delivery Charges per item', $this->pluginLocale).":";
						echo"<div style='margin-left:20px'>";
						echo "<input name='".$this->pluginSlug."[order][".$field."][per_item][delivery_charge_per_item]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field]['per_item']['delivery_charge_per_item'],$optionsDecimals)."' />";
						echo" ".__('Do not apply delivery charges when total order value reaches ', $this->pluginLocale).":";
						echo"<input name='".$this->pluginSlug."[order][".$field."][per_item][delivery_per_item_free]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field]['per_item']['delivery_per_item_free'],$optionsDecimals)."' />";
						echo" ".__('[set to 0 to always apply charges per item]', $this->pluginLocale)."";
						echo"</div>";
					}
					echo "</span>";
				}
				echo "</span>";

				/**min order for delivery**/
				echo "<span class='wppizza_option' style='margin-top:20px'>";
				echo"<input name='".$this->pluginSlug."[order][order_min_for_delivery]' size='3' type='text' value='".wppizza_output_format_price($options['order']['order_min_for_delivery'],$optionsDecimals)."' />";
				echo" ".__('minimum order value [will disable "place order" button in cart and order page until set order value (before any discounts etc) has been reached. 0 to disable.]', $this->pluginLocale)."<br />";
				echo" <span class='description'>".__('Customer can still choose "self-pickup" (if enabled / applicable).', $this->pluginLocale)."</span>";
				echo "</span>";

				/**Exclude following menu items when calculating if free delivery**/
				echo "<span class='wppizza_option' style='margin:20px 0'>";
					echo" ".__('<b>Exclude</b> following menu items when calculating if free delivery applies', $this->pluginLocale)." :<br />";
					echo'<span class="description">'.__('For example: you might want to offer free delivery only when total order of *meals* exceeds the set free delivery amount. In this case, exclude all your *drinks and non-meals* by selecting those below.', $this->pluginLocale).'</span><br />';
					echo"<select name='".$this->pluginSlug."[order][delivery_calculation_exclude_item][]' multiple='multiple' class='wppizza_delivery_calculation_exclude_item'>";
					$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1, 'orderby'=>'title' ,'order' => 'ASC');
					$query = new WP_Query( $args );
					foreach($query->posts as $pKey=>$pVal){
						echo"<option value='".$pVal->ID."' ";
							if(isset($options['order']['delivery_calculation_exclude_item']) && in_array($pVal->ID,$options['order']['delivery_calculation_exclude_item'])){
								echo" selected='selected'";
							}
						echo">".$pVal->post_title."</option>";
					}
					echo"</select>";
					echo'<br />'.__('Ctrl+Click to select multiple', $this->pluginLocale).'';
				echo "</span>";

			}
			/**I don't think this actually in use anywhere ?!**/
			if($field=='delivery_per_item'){
				echo"<input name='".$this->pluginSlug."[order][".$field."]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field],$optionsDecimals)."' />";
			}

			if($field=='order_pickup'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' type='checkbox'  ". checked($options['order'][$field],true,false)." value='1' /> ".__('tick to enable', $this->pluginLocale)."";

				echo "<br />".__('Discount for self-pickup ?', $this->pluginLocale)." <input id='order_pickup_discount' name='".$this->pluginSlug."[order][order_pickup_discount]' size='5' type='text' value='".wppizza_output_format_float($options['order']['order_pickup_discount'],'percent')."' /> ".__('in % - 0 to disable', $this->pluginLocale)."";

				echo "<br /><input id='order_pickup_alert' name='".$this->pluginSlug."[order][order_pickup_alert]' type='checkbox'  ". checked($options['order']['order_pickup_alert'],true,false)." value='1' /> ".__('enable javascript alert when user selects self pickup (set corresponding text in localization)', $this->pluginLocale)."";
			}
			if($field=='order_pickup_display_location'){
				echo "".__('under cart only', $this->pluginLocale)."<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' type='radio'  ".checked($options['order'][$field],1,false)." value='1' /> ";
				echo "".__('on order page only', $this->pluginLocale)."<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' type='radio'  ".checked($options['order'][$field],2,false)." value='2' /> ";
				echo "".__('both', $this->pluginLocale)."<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' type='radio'  ".checked($options['order'][$field],3,false)." value='3' />";
			}


			if($field=='discounts'){
				foreach($options['order'][$field] as $k=>$v){
					echo "<span class='wppizza_option'>";
					echo "<input name='".$this->pluginSlug."[order][discount_selected]' type='radio' ". checked($options['order']['discount_selected']==$k,true,false)." value='".$k."' />";
					if($k=='none'){
						echo"".__('No Discounts', $this->pluginLocale)."";
					}
					if($k=='percentage'){
						echo"".__('Percentage Discount', $this->pluginLocale).":";
						echo"<br />";
						foreach($v['discounts'] as $l=>$m){
							echo"".__('If order total >', $this->pluginLocale).":";
							echo"<input name='".$this->pluginSlug."[order][".$field."][".$k."][discounts][".$l."][min_total]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field][$k]['discounts'][$l]['min_total'],$optionsDecimals)."' />";
							echo"".__('discount', $this->pluginLocale).":";
							echo"<input name='".$this->pluginSlug."[order][".$field."][".$k."][discounts][".$l."][discount]' size='5' type='text' value='".wppizza_output_format_float($options['order'][$field][$k]['discounts'][$l]['discount'],'percent')."' />";
							echo"".__('percent', $this->pluginLocale)."";
							echo"<br />";
						}
					}
					if($k=='standard'){
						echo"".__('Standard Discount [money off]', $this->pluginLocale).":";
						echo"<br />";
						foreach($v['discounts'] as $l=>$m){
							echo"".__('If order total >', $this->pluginLocale).":";
							echo"<input name='".$this->pluginSlug."[order][".$field."][".$k."][discounts][".$l."][min_total]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field][$k]['discounts'][$l]['min_total'],$optionsDecimals)."' />";
							echo"".__('get', $this->pluginLocale).":";
							echo"<input name='".$this->pluginSlug."[order][".$field."][".$k."][discounts][".$l."][discount]' size='3' type='text' value='".wppizza_output_format_price($options['order'][$field][$k]['discounts'][$l]['discount'],$optionsDecimals)."' />";
							echo"".__('off', $this->pluginLocale)."";
							echo"<br />";
						}
					}
					echo "</span>";
				}
			}

			if($field=='append_internal_id_to_transaction_id'){
				echo "<input name='".$this->pluginSlug."[order][".$field."]' type='checkbox'  ". checked($options['order'][$field],true,false)." value='1' />";
				echo" ".__('enable to append internal order ID to transaction ID [e.g COD13966037358 will become COD13966037358/123 where 123 = internal id of order table]', $this->pluginLocale)."";				
			}

			if($field=='order_email_to' || $field=='order_email_cc' || $field=='order_email_bcc' ){//$field==order_sms => not implemented
				if(is_array($options['order'][$field])){$val=implode(",",$options['order'][$field]);}else{$val='';}
				echo "<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' size='30' type='text' value='".$val."' />";
			}
			if($field=='order_email_attachments'){
				if(isset($options['order'][$field]) && is_array($options['order'][$field])){$val=implode(",",$options['order'][$field]);}else{$val='';}
				echo "<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' size='30' type='text' value='".$val."' placeholder='/absolute/path/to/your/file'/>";
				echo" <span class='description'>".__('if you wish to add an attachment to the order emails add the FULL ABSOLUTE PATH to the file(s) here', $this->pluginLocale)."</span>";
			}

			if($field=='order_email_from'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' size='30' type='text' value='".$options['order'][$field]."' />";
			}
			if($field=='order_email_from_name'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' size='30' type='text' value='".$options['order'][$field]."' />";
			}



			if($field=='item_tax'){
				echo "<input id='".$field."' name='".$this->pluginSlug."[order][".$field."]' size='5' type='text' value='".wppizza_output_format_float($options['order'][$field],'percent')."' />%";
				echo"<br />";
				echo"<input name='".$this->pluginSlug."[order][shipping_tax]' type='checkbox'  ". checked($options['order']['shipping_tax'],true,false)." value='1' />";
				echo" ".__('apply tax to delivery/shipping  too', $this->pluginLocale)."";
				echo"<br />";
				echo"<input name='".$this->pluginSlug."[order][taxes_included]' type='checkbox'  ". checked($options['order']['taxes_included'],true,false)." value='1' />";
				echo" ".__('all prices are entered including tax, but I distinctly need to display the sum of taxes applied', $this->pluginLocale)."";
				echo"<br /><span class='description'>".__('if enabled, the sum of applicable taxes will be displayed separately without however adding it to the total (if taxrate > 0%).', $this->pluginLocale)."</span>";
			}
			if($field=='sizes'){
				echo"<div id='wppizza_".$field."'>";
				echo"<div id='wppizza_".$field."_options'>";
				foreach($options[$field] as $k=>$v){
					echo"".$this->wppizza_admin_section_sizes($field,$k,$v,$optionInUse);
				}
				echo"</div>";
				echo"<div id='wppizza-sizes-add'>";
				echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('add', $this->pluginLocale)."</a>";
				echo "<input id='wppizza_add_".$field."_fields' size='1' type='text' value='1' />".__('how many size option fields ?', $this->pluginLocale)."";
				echo"</div>";
				echo"</div>";
			}
			if($field=='additives'){
				echo"<div id='wppizza_".$field."'>";
					echo"<div id='wppizza_".$field."_options'>";
					if(isset($options[$field]) && is_array($options[$field])){
					asort($options[$field]);//sort
					foreach($options[$field] as $k=>$v){
						echo"".$this->wppizza_admin_section_additives($field,$k,$options[$field][$k],$optionInUse);
					}}
					echo"</div>";
					echo"<div id='wppizza-additives-add'>";
					echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('add', $this->pluginLocale)."</a>";
					echo"</div>";
				echo"</div>";
			}

			if($field=='gateways'){
				echo"<div id='wppizza_".$field."'>";

				echo"<div id='wppizza_".$field."_options'>";
					echo"<div>";
						echo"<input name='".$this->pluginSlug."[gateways][gateway_select_as_dropdown]' type='checkbox'  ". checked($options['gateways']['gateway_select_as_dropdown'],true,false)." value='1' />";
						echo" <b>".__('Display Gateway choices as dropdowns instead of buttons', $this->pluginLocale)."</b> ".__('[only applicable if more than one gateway installed, activated and enabled]', $this->pluginLocale)."";
					echo"</div>";

					echo"<div>";
						echo"<b>".__('Label:', $this->pluginLocale)."</b> ";
						echo"<input name='".$this->pluginSlug."[gateways][gateway_select_label]' type='text' size='50' value='". $options['gateways']['gateway_select_label']."' />";
						echo"<br />".__('by default displayed above if choices are displayed as full width buttons, next to if dropdown. edit css as required', $this->pluginLocale)." ".__('[only applicable if more than one gateway installed, activated and enabled]', $this->pluginLocale)."";
					echo"</div>";

					echo"<div>";
						echo"<input name='".$this->pluginSlug."[gateways][gateway_showorder_on_thankyou]' type='checkbox'  ". checked($options['gateways']['gateway_showorder_on_thankyou'],true,false)." value='1' />";
						echo" <b>".__('Show Order Details on "Thank You" page (Y/N)', $this->pluginLocale)."</b> ".__('Will add any order details after your thank you text on successful order', $this->pluginLocale)."";
					echo"</div>";




						$this->wppizza_admin_section_gateways($field,$options[$field]);
					echo"</div>";
				echo"</div>";
			}
			if($field=='localization'){
				/**to get descriptions include default options. do not use require_once, as we need this more than once**/
				require(WPPIZZA_PATH .'inc/admin.setup.default.options.inc.php');

				/**add description to array**/
				$localizeOptions=array();
				foreach($defaultOptions['localization'] as $k=>$v){
					$localizeOptions[$k]['descr']=$v['descr'];
					$localizeOptions[$k]['lbl']=$options[$field][$k]['lbl'];
				}

				$textArea=array('thank_you_p');/*tinymce textareas*/
				echo"<div id='wppizza_".$field."'>";
					echo"<div id='wppizza_".$field."_options'>";
					asort($localizeOptions);
					$lngOddEvenArray=__('0,1,6,13,14,17,38,46', $this->pluginLocale);
					$lngOddEvan=explode(",",$lngOddEvenArray);
					$bgStyle=$lngOddEvan;
					$i=0;
					foreach($localizeOptions as $k=>$v){
					if(in_array($i,$bgStyle)){echo'<div>';}
						if(in_array($k,$textArea)){
							$editorId="".strtolower($this->pluginSlug."_".$field."_".$k)."";/* WP 3.9 doesnt like brackets in id's*/
							$editorName="".$this->pluginSlug."[".$field."][".$k."]";
							echo"<br />".$v['descr']."";
							echo"<div style='width:500px;'>";
							wp_editor( $v['lbl'], $editorId, array('teeny'=>1,'wpautop'=>false,'textarea_name'=>$editorName) );
							echo"<br /></div>";
							echo"<br />";
						//echo "<textarea name='".$this->pluginSlug."[".$field."][".$k."]' style='width:185px;height:150px'>".$v['lbl']."</textarea>";
						}else{
						echo "<input name='".$this->pluginSlug."[".$field."][".$k."]' size='30' type='text' value='".$v['lbl']."' />";
						echo"<span class='description'>".$v['descr']."</span><br />";
						}
					$i++;
					if(in_array($i,$bgStyle)){echo'</div>';}
					}
					echo"</div>";
				echo"</div>";
			}


			if($field=='access'){
				global $current_user,$user_level,$wp_roles;
				echo"<div id='wppizza_".$field."'>";
					$roles=get_editable_roles();/*only get roles user is allowed to edit**/
					/*do not display current users role (otherwise he can screw his own access) or levels higher than current*/
					if(is_array($current_user->roles)){
					foreach($current_user->roles as $curRoles){
						if(isset($roles[$curRoles])){
							unset($roles[$curRoles]);
						}
					}}

					$access=$this->wppizza_set_capabilities();
					foreach($roles as $roleName=>$v){

						$userRole = get_role($roleName);
							echo"<div class='wppizza-access'>";
							echo"<input type='hidden' name='".$this->pluginSlug."[admin_access_caps][".$roleName."]' value='".$roleName."'>";
							echo"<ul>";
							print"<li style='width:150px'><b>".$roleName.":</b></li>";
								foreach($access as $aKey=>$aArray){
									echo"<li><input name='".$this->pluginSlug."[admin_access_caps][".$roleName."][".$aArray['cap']."]' type='checkbox'  ". checked(isset($userRole->capabilities[$aArray['cap']]),true,false)." value='".$aArray['cap']."' /> ".$aArray['name']."<br /></li>";//". checked($options['plugin_data']['access_level'],true,false)."
								}
							echo"</ul>";
							echo"</div>";
					}
				echo"</div>";
			}

			if($field=='history'){
				echo"<div id='wppizza_".$field."'>";

					echo"<div id='wppizza_".$field."_totals'></div>";

					echo"<div id='wppizza_".$field."_search' class='button' style='overflow:auto'>";
						echo "<span style='float:left;'>";
						echo "<a href='#' id='".$field."_get_orders' class='button' style='margin-top:6px'>".__('show most recent *confirmed* orders', $this->pluginLocale)."</a>";
						echo" ".__('status', $this->pluginLocale).": ";
						echo "<select id='".$field."_orders_status' name='".$field."_orders_status' />";
							echo"<option value=''>".__('-- All --', $this->pluginLocale)."</option>";
							echo"<option value='NEW'>".__('new', $this->pluginLocale)."</option>";
							echo"<option value='ACKNOWLEDGED'>".__('acknowledged', $this->pluginLocale)."</option>";
							echo"<option value='ON_HOLD'>".__('on hold', $this->pluginLocale)."</option>";
							echo"<option value='PROCESSED'>".__('processed', $this->pluginLocale)."</option>";
							echo"<option value='DELIVERED'>".__('delivered', $this->pluginLocale)."</option>";
							echo"<option value='REJECTED'>".__('rejected', $this->pluginLocale)."</option>";
							echo"<option value='REFUNDED'>".__('refunded', $this->pluginLocale)."</option>";
							echo"<option value='OTHER'>".__('other', $this->pluginLocale)."</option>";
						echo "</select>";						
						echo " ".__('maximum results [0 to show all]', $this->pluginLocale)."<input id='".$field."_orders_limit' size='3' type='text' value='20' />";
						echo "</span>";
						echo "<span style='float:right;margin-right:50px;'>";
						echo " ".__('poll for new orders every', $this->pluginLocale)."<input id='".$field."_orders_poll_interval' size='2' type='text' value='30' />".__('seconds', $this->pluginLocale)." ";
						echo "<label class='button' style='margin-top:6px'><input id='".$field."_orders_poll_enabled' type='checkbox' value='1' />".__('on/off', $this->pluginLocale)."</span>";
								echo "<span id='wppizza-orders-polling'></span>";/*shows loading icon*/
						echo "</label>";
					echo"</div>";
					echo"<div id='wppizza_".$field."_orders'></div>";

				echo"</div>";

			}
			if($field=='tools'){
				echo"<div id='wppizza_".$field."'>";
					echo"<div id='wppizza_".$field."_clear'>";
						echo" <b>".__('Delete abandoned/cancelled orders from database older than', $this->pluginLocale)."</b> ";
						echo"<input id='wppizza_order_days_delete' type='text' size='2' value='7' />";
						echo" <b>".__('Days (minimum: 1)', $this->pluginLocale)."</b> ";
						echo"<input id='wppizza_order_failed_delete' type='checkbox' value='1' />";
						echo" <b>".__('delete failed, tampered or otherwise invalid entries too.', $this->pluginLocale)."</b> ";
						echo"<br /><span id='wppizza_order_abandoned_delete' class='button'>".__('go', $this->pluginLocale)."</span>";
						echo"<br />".__('As soon as customers go to the order page an order will be initialized and stored in the db to be checked against when going through with the purchase to make sure nothing has been tampered with. However, not every customer will actually go through with the purchase which leaves this initialised order orphaned in the db.Click the "ok" button to clean your db of these entries (it will NOT affect any completed or pending orders)', $this->pluginLocale)."";
						echo"<br /><span style='color:red'>".__('Note: This will delete these entries PERMANENTLY from the db and is not reversable.', $this->pluginLocale)."</style>";
					echo"</div>";
				echo"</div>";
			}

	}
?>