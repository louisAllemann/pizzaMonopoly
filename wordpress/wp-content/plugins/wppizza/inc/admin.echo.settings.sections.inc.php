<?php
		register_setting($this->pluginSlug,$this->pluginSlug, array( $this, 'wppizza_admin_options_validate') );

		/**global settings**/
		add_settings_section('global','',  array( $this, 'wppizza_admin_page_text_header'), 'global');
		add_settings_field('version', '<b>'.__('Plugin Version:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'version' );
		add_settings_field('js_in_footer', '<b>'.__('Javascript in Footer:', $this->pluginLocale).'</b> '.__('[combines all jsVars in one tidy place, but requires wp_footer in theme]', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'js_in_footer' );
		add_settings_field('mail_type', '<b>'.__('Select Type of Mail Delivery:', $this->pluginLocale).'</b><br/>'.__('[might be worth changing if you have trouble when sending/receiving orders with the default settings or prefer html emails ]', $this->pluginLocale).'<br/><b>'.__('if using PHPMailer function you probably want to edit the html template. To do so, move "wppizza-order-html-email.php" from the wppizza template directory to your theme folder and edit as required', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'mail_type' );
		add_settings_field('empty_category_and_items', '<b>'.__('Empty/Delete ALL WPPizza Categories and Items:<br/><span style="color:red">use with care<br/>if you select "delete images too", all featured images used for any wppizza menu items will be deleted too.<br/>if you use these images elsewhere, you should not select this !</span>', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'empty_category_and_items' );
//		add_settings_field('install_sample_data', '<b>'.__('Install Sample data:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'install_sample_data' );
		add_settings_field('wp_multisite_session_per_site', '<b>'.__('Multisite Only:', $this->pluginLocale).'</b><br/>'.__('Set cart contents and order on a per site basis when using subdirectories. This has no effect/relevance when there\'s no multisite setup or using different domains per site on the network. Chances are that you want this on when you have a multisite/network install. THERE ARE ONLY VERY FEW SECENARIOS WHERE YOU MIGHT WANT THIS OFF', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'wp_multisite_session_per_site' );
		add_settings_field('using_cache_plugin', '<b>'.__('I am using a caching plugin:', $this->pluginLocale).'<br /><span class="description">'.__('Experimental. please let me know if you experience problems with this.', $this->pluginLocale).'</span>', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'using_cache_plugin' );
		add_settings_field('category_parent_page', ''.__('<b>Permalinks:<br/>(only used and relevant when using widget or shortcode to display wppizza category navigation !!!)<br/><span style="color:red">when changing this setting, you MUST re-save your permalink settings</span></b><br/>(page cannot be used as static post page (wp settings) or have any children', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'global', 'global', 'category_parent_page' );

		/**gateways settings**/
		add_settings_section('gateways','',  array( $this, 'wppizza_admin_page_text_header'), 'gateways');
		add_settings_field('gateways', '<b>'.__('Set Gateway Options:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'gateways', 'gateways', 'gateways' );


		/**layout settings**/
		add_settings_section('layout','',  array( $this, 'wppizza_admin_page_text_header'), 'layout');
		add_settings_field('items_per_loop', '<b>'.__('Menu Items per page:', $this->pluginLocale).'</b><br/>'.__('how many menu items per category page (displays pagination, if there are more menu items for the selected category)<br/>[options: -1=all, >1=items per page]<br/><span style="color:red">if not set to -1, it must be >= wordpress settings->reading->Blog pages show at most</span>', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'items_per_loop' );
		add_settings_field('include_css', '<b>'.__('Include CSS:', $this->pluginLocale).'</b><br/>'.__('include frontend css that came with this plugin (untick if you want to provide your own styles somewhere else)', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'include_css' );
		add_settings_field('style', '<b>'.__('Which style to use (if enabled above):', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'style' );
		add_settings_field('items_group_sort_print_by_category', ''.__('Group, sort and display menu items by category:', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'items_group_sort_print_by_category' );
		add_settings_field('opening_times_format', '<b>'.__('Format of openingtimes (if displayed):', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'opening_times_format' );
		add_settings_field('add_to_cart_on_title_click', '<b>'.__('Add item to cart on click of *item title* if there is only one pricetier for a menu item:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'add_to_cart_on_title_click' );
		add_settings_field('jquery_feedback_added_to_cart', '<b>'.__('Briefly display text in place of price when adding item to cart', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'jquery_feedback_added_to_cart' );
		add_settings_field('placeholder_img', '<b>'.__('Display placeholder image when no image associated with meal item:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'placeholder_img' );
		add_settings_field('prettyPhoto', '<b>'.__('Enable prettyPhoto (Lightbox Clone) on menu item images', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'prettyPhoto' );
		add_settings_field('prettyPhotoStyle', '<b>'.__('Set prettyPhoto Style', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'prettyPhotoStyle' );
		add_settings_field('suppress_loop_headers', '<b>'.__('Globally suppress headers above list of menu items:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'suppress_loop_headers' );
		add_settings_field('hide_cart_icon', '<b>'.__('Hide cart icon next to prices:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'hide_cart_icon' );
		add_settings_field('show_currency_with_price', '<b>'.__('Show a currency symbol directly next to each price', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'show_currency_with_price' );
		add_settings_field('hide_item_currency_symbol', '<b>'.__('Hide *main* currency symbol next to each menu item:', $this->pluginLocale).'</b><br/>'.__('won\'t affect cart, summaries or emails', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'hide_item_currency_symbol' );
		add_settings_field('currency_symbol_left', '<b>'.__('Show *main* currency symbol on the left - if not set to hidden', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'currency_symbol_left' );
		add_settings_field('currency_symbol_position', '<b>'.__('All other [cart, order page, email] currency symbols', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'currency_symbol_position' );
		add_settings_field('hide_single_pricetier', '<b>'.__('Hide pricetier name and cart icon if item has only one size:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'hide_single_pricetier' );
		add_settings_field('hide_prices', '<b>'.__('Hide prices altogether:', $this->pluginLocale).'</b><br/><span style="color:red">'.__('this will disable the adding of any item to the shoppingcart.', $this->pluginLocale).'</span><br/>'.__('Really only useful if you want to display your menu without offering online orders', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'hide_prices' );
		add_settings_field('hide_decimals', '<b>'.__('Don\'t show decimals:', $this->pluginLocale).'</b><br/>'.__('[prices will be rounded if necessary]', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'hide_decimals' );
		add_settings_field('cart_increase', '<b>'.__('Enable increase/decrease of items in cart via input field/textbox', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'cart_increase' );
		add_settings_field('empty_cart_button', '<b>'.__('Enable "empty cart" button', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'empty_cart_button' );
		add_settings_field('sticky_cart_settings', '<b>'.__('"sticky/scolling" cart settings [if used]', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'sticky_cart_settings' );
		add_settings_field('disable_online_order', '<b>'.__('Completely disable online orders:', $this->pluginLocale).'</b><br/><span style="color:red">'.__('this will still display prices (unless set to be hidden above), but will disable shoppingcart and orderpage', $this->pluginLocale).'</span><br/>'.__('Useful if you want to display your menu and prices but without offering online orders.', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'layout', 'layout', 'disable_online_order' );



		/**opening times**/
		add_settings_section('opening_times','',  array( $this, 'wppizza_admin_page_text_header'), 'opening_times');
		add_settings_field('opening_times_standard', '<b>'.__('Standard opening times:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'opening_times', 'opening_times', 'opening_times_standard' );
		add_settings_field('opening_times_custom', '<b>'.__('Any dates/days where opening times differ from the standard times above (such as christmas etc).', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'opening_times', 'opening_times', 'opening_times_custom' );
		add_settings_field('times_closed_standard', '<b>'.__('Closed (if you close for lunch for example):', $this->pluginLocale).'</b><br/><br/>'.__('If you are closed on certain days for a number of hours, enter them here<br/>i.e. if you are generally open on Tuesdays - as set above - from 9:30 to 23:00, but close for lunch between 12:00 and 14:00, enter Tuesdays 12:00 - 14:00 here. If you are also closed on Tuesday between 17:30 and 18:00, set this as well and so on ', $this->pluginLocale).'<br/><br/>'.__('Furthermore, do not enter times here that span midnight. If you are however closed from - let\'s say - 11:00PM Mondays to 1:00AM Tuesdays, enter "Mondays 23:00 to 23:59" as well as "Tuesdays 0:00 to 1:00', $this->pluginLocale).'<br/><br/>'.__('If you have setup any custom dates above (for example christmas or whatever), select "Custom Dates" instead of the day of week if you want to apply these closing times only to those dates', $this->pluginLocale).'<br/><br/><span style="color:red">'.__('Note: if you set anything here, it will not be reflected when displaying openingtimes via shortcode or in the widget, so you might want to display your openingtimes manually somewhere. It DOES, however close the shoppingcart, the ability to order etc as required)', $this->pluginLocale).'</span>', array( $this, 'wppizza_admin_settings_input'), 'opening_times', 'opening_times', 'times_closed_standard' );

		/**order**/
		add_settings_section('order', '',  array( $this, 'wppizza_admin_page_text_header'), 'order');
		add_settings_field('currency', '<b>'.__('Currency:', $this->pluginLocale).'</b><br/>'.__('set to --none-- to have no currency displayed anywhere', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'currency' );
		add_settings_field('orderpage', '<b>'.__('Order Page:', $this->pluginLocale).'</b><br/>'.__('ensure the page includes [wppizza type="orderpage"] or the widget equivalent. <b>You might also want to consider NOT displaying the shopping cart on this page</b> (although it won\'t break things)', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'orderpage' );
		add_settings_field('delivery', '<b>'.__('Delivery Charges:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'delivery' );
		add_settings_field('item_tax', '<b>'.__('(Sales)Tax applied to items in cart [in % - 0 to disable]:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'item_tax' );
		add_settings_field('order_pickup', '<b>'.__('Allow order pickup by customer:', $this->pluginLocale).'</b><br />'.__('Customer can choose to pickup the order him/herself. No delivery charges will be applied if customer chooses to do so.', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'order_pickup' );
		add_settings_field('order_pickup_display_location', '<b>'.__('Where would you like to display the checkbox to let customer select self pickup of order ?', $this->pluginLocale).'</b> '.__('[if enabled above]', $this->pluginLocale).'', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'order_pickup_display_location' );
		add_settings_field('discounts', '<b>'.__('Discounts:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'discounts' );
		add_settings_field('append_internal_id_to_transaction_id', '<b>'.__('Append internal ID to transaction ID:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'append_internal_id_to_transaction_id' );

	
		add_settings_field('order_email_to', '<b>'.__('Which email address should any orders be sent to [separated by comma if multiple]:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'order_email_to' );
		add_settings_field('order_email_bcc', '<b>'.__('If you would like to BCC order emails add these here [separated by comma if multiple]:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'order_email_bcc' );
		add_settings_field('order_email_from', '<b>'.__('If you want to set a static "From" email address set it here, otherwise leave blank . All emails will appear to have been sent from this address. (Some fax gateways for example require a distinct FROM email address). However, the customers email address will still be stored in the db/order history if entered', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'order_email_from' );
		add_settings_field('order_email_from_name', '<b>'.__('Instead of using the customers name - if enabled/entered in the order form fields - you can set a static name here (useful in conjunction with any "From" email address set above).', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'order_email_from_name' );
		add_settings_field('order_email_attachments', '<b>'.__('Email Attachments [separated by comma if multiple]:', $this->pluginLocale).'</b><br /><span style="color:red">'.__('Settings->Mail Delivery must be set to wp_mail or PHPMailer', $this->pluginLocale).'</span>', array( $this, 'wppizza_admin_settings_input'), 'order', 'order', 'order_email_attachments' );


		/**order form**/
		add_settings_section('order_form', '',  array( $this, 'wppizza_admin_page_text_header'), 'order_form');
		add_settings_field('order_form', '<b>'.__('Form Fields:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order_form', 'order_form', 'order_form' );
		add_settings_field('confirmation_form', '<b>'.__('Confirmation Page:', $this->pluginLocale).'</b>', array( $this, 'wppizza_admin_settings_input'), 'order_form', 'order_form', 'confirmation_form' );

		/**size options**/
		add_settings_section('sizes', __('Size Options Available', $this->pluginLocale),  array( $this, 'wppizza_admin_page_text_header'), 'sizes');
		add_settings_field('sizes', '', array( $this, 'wppizza_admin_settings_input'), 'sizes', 'sizes', 'sizes' );

		/**additives**/
		add_settings_section('additives', '',  array( $this, 'wppizza_admin_page_text_header'), 'additives');
		add_settings_field('additives', '', array( $this, 'wppizza_admin_settings_input'), 'additives', 'additives', 'additives' );

		/**localization**/
		add_settings_section('localization', __('Frontened Localization', $this->pluginLocale).' - '.__('edit as required:', $this->pluginLocale),  array( $this, 'wppizza_admin_page_text_header'), 'localization');
		add_settings_field('localization', '', array( $this, 'wppizza_admin_settings_input'), 'localization', 'localization', 'localization' );

		/**order history**/
		add_settings_section('history', '',  array( $this, 'wppizza_admin_page_text_header'), 'history');
		add_settings_field('history', '', array( $this, 'wppizza_admin_settings_input'), 'history', 'history', 'history' );

		/**order reports**/
		add_settings_section('reports', '',  array( $this, 'wppizza_admin_page_text_header'), 'reports');
		//add_settings_field('reports', 'or this', array( $this, 'wppizza_admin_settings_input'), 'reports', 'reports', 'reports' );

		/**access rights**/
		add_settings_section('access', '',  array( $this, 'wppizza_admin_page_text_header'), 'access');
		add_settings_field('access', '', array( $this, 'wppizza_admin_settings_input'), 'access', 'access', 'access' );

		/**tools**/
		add_settings_section('tools', __('Miscellaneous Tools', $this->pluginLocale),  array( $this, 'wppizza_admin_page_text_header'), 'tools');
		add_settings_field('tools', '', array( $this, 'wppizza_admin_settings_input'), 'tools', 'tools', 'tools' );

?>