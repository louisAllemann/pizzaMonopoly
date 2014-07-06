=== WPPizza ===
Contributors: ollybach
Donate link: http://www.wp-pizza.com/
Author URI: http://www.wp-pizza.com
Plugin URI: http://wordpress.org/extend/plugins/wppizza/
Tags: pizza, restaurant, restaurant menu, ecommerce, e-commerce, commerce, wordpress ecommerce, store, shop, sales, shopping, cart, order online, cash on delivery, multilingual, checkout, configurable, variable, widgets, shipping, tax
Requires at least: PHP 5.3, WP 3.3 
Tested up to: 3.9.1
Stable tag: 2.9.4.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Restaurant Plugin (not only for Pizza). Maintain your Menu (sizes, prices, categories). Accept COD orders. Multisite, Multilingual, WPML compatible.



== Description ==

- **Conceived for Pizza Delivery Businesses, but flexible enough to serve any restaurant type.**

- Maintain your restaurant menu online and accept cash on delivery orders.

- Set categories, multiple prices per item and descriptions.

- Multilingual Frontend (just update labels in admin settings page and/or widget as required). WPML compatible.

- Multisite enabled

- Keeps track of your online orders.

- Shortcode enabled. (see <a href='http://wordpress.org/extend/plugins/wppizza/faq/' >FAQ</a> for details)


**To see the plugin in action with different themes try it at <a href="https://www.wp-pizza.com/">www.wp-pizza.com</a>**

**if you wish to allow your customers to add additional ingredients to any given menu item, have a look at the premium <a href='https://www.wp-pizza.com/'>"WPPizza Add Ingredients"</a> extension**

**gateways available to process credit card payments  instead of just cash on delivery at <a href='https://www.wp-pizza.com/'>www.wp-pizza.com</a>** 


== Installation ==

**Install**

1. Download the plugin and upload the entire `wppizza` folder to the `/wp-content/plugins/` directory.  
Alternatively you can download and install WPPizza using the built in WordPress plugin installer.  
2. Activate the plugin through the 'Plugins' menu in WordPress.  
3. You will find all configuration and menu options in your administration sidebar  


**Things to do on first install**

although some defaults will be installed and set when first installing this plugin, **you will have to configure some things** to make it play nicely in your theme  
Note: you might want to start with  Option 1 first, as you can always use Option 2 later

- **Option 1** - using normal pages: easier to setup initially, but more labour intensive to maintain in the long run

	To get you started quickly and to help you decide if you like this plugin, the following defaults are being created on first install:
	- some default categories
	- some default menu items per category
	- pages that display the items in any given category (including the required shortcode)  
	- an order page to send any orders to you via email (including the required shortcode)
	- a default list of additives that a menu item might have 
	- a selection of available meal sizes
	- as well as default opening times, order settings etc.
	- display the shoppingcart somewhere on those pages (typically in a sidebar) using the wppizza widget (using type:cart) or the following shortcode: [wppizza type='cart']  
		
	**to be able to add items to your shopping cart make sure that ALL pages that display your menu items have the shopping cart displayed somewhere**  
	**However, you will most likely NOT want to display the shopping cart on the final order page. If your theme does not allow to adjust this - via templates for example -  use one of the many WP plugins available that lets you choose which widget to display on which page**  
	
	- if the cart displays "currently closed" adjust your opening times in wppizza->settings->openingtimes (and make sure your timezone settings are correct)
	- make sure the navigation to those pages gets displayed somewhere (this will normally already be the case - usually by some pagelist or menu ).

	that should be it.......

	NOTE: **some themes (custom community for example) require that you distinctly save the automatically inserted pages again (i.e "order page", etc) to generate the right markup and display correctly. So if the layout is messed up on initial install, try to just save those pages once and see if that fixes things.**

- **Option 2** - using templates:
	
	- copy your themes 'page.php' file as 'wppizza-wrapper.php' into your theme directory(if your theme does not have a page.php file, look for archive.php or even index.php)
	- open /wppizza/templates/wppizza-wrapper.php and copy everything between 	[copy from here .....] to [...........copy to here]
	- place this snippet in the 'wppizza-wrapper.php' file we created above in your theme directory, REPLACING the original loop (including the while ( have_posts() ) : the_post(); or similar part) 
	- display the navigation by either using the widget (type:navigation) or a shortcode [wppizza type='navigation']
	- ensure you still have an order page that includes the following shortcode [wppizza type='orderpage'] and wppizza->settings->order settings: 'order page' is set to use this page (you might have to re-save your permalink settings)  
	- ensure - as outlined in Option 1 - that you are displaying your shopping cart  
	- if you wish , you can now delete all wppizza default pages EXCEPT THE ORDER PAGE. However,if using permalinks, you might want to keep the parent page (default: Our Menu) and set the permalinks in wppizza->settings to this page. If you do, make sure to update permalinks structure once.

	Now you do not have to maintain any wppizza category pages, or navigation when adding new categories or menu items to wppizza as it's all taken care of automagically.

**PS**: you might have to adjust the css to work within your theme.  
see <a href='http://wordpress.org/extend/plugins/wppizza/faq/'>FAQ: "Can I edit the css ?"</a> for details  


**Uninstall**

Please note:  
although all options, menu items and menu categories get deleted from the database along with the table that holds any orders you may have received, you will manually have to delete any additional pages (such as the order page for example) that have been created as i have no way of knowing if you are using this page elsewhere or have changed the content/name of it.  
the same goes for the 3 example icons that come with this plugin as you might have used them elsewhere.



== Upgrade Notice ==

Please update to version 2.5.7+ asap as metadata might get lost when using *quickedit* in wppizza custom post type
(if you are currently using wppizza <2.4 you should definitely update to the latest version as there were also some security flaws in version <2.4)



== Screenshots ==

1. frontend example
2. administration - widget
3. administration - categories
4. administration - menu item
5. administration - order settings (one of many option screens)


== Other Notes ==

= Translations provided by: =

- Italien:  Silvia Palandri  
- Hebrew:  Yair10 [&#1492;&#1500;&#1489;&#32;&#1489;&#1504;&#1497;&#1497;&#1514;&#32;&#1488;&#1514;&#1512;&#1497;&#1501;&#32;]  
- Dutch:  Jelmer  
- Spanish:  Andrew Kurtis at <a href="http://www.webhostinghub.com/">WebHostingHub</a>  

Many, many thanks guys and girls.  

Note: As the plugin gets updated over time and has some other strings and features added, the translations above (and future ones) will probably have a few strings not yet translated. If you wish, feel free to provide any of those missing and I will update the translations accordingly.  

If you want to contribute your own translation, feel free to send me your files and I will be more than happy to include them.  



= Demo Icons: =
please note that the icons used in the demo installation are <a href="http://www.iconarchive.com/show/desktop-buffet-icons-by-aha-soft.html">iconarchive.com</a> icons and not for commercial use.  
if you do wish to use any icon from this set commercially, please follow <a href="http://www.desktop-icon.com/stock-icons/desktop-buffet-icons.htm">this link</a> to purchase it.



== Changelog ==

2.9.4.6  
* added filter (wppizza_filter_formfields_order, wppizza_filter_formfields_register, wppizza_filter_formfields_profile) to filter customer form elements on order page  and (if used) confirmation page as well as profile and/or registration   
20th June 2014  

2.9.4.5  
* BUGFIX: (admin) previous version only loaded admin js when post_type == wppizza although some js functions where required in widgets page too, so there's now a distinct scripts.admin.global.js  
* added some more gateway functions to use in future gateway development    
17th June 2014  

2.9.4.4  
* added: enabled wppizza order form fields now also displaying in user profile when using a themed/frontend profile page (for instance when used in conjunction with "theme my login" plugin)   
* added: a couple of action hooks *inside* the form tags of order page  
10th June 2014  


2.9.4.3  
* added turkish lira to currencies  
* only load admin js when actually required  
* added filter - wppizza_custom_transaction_id - to append or prepend things to the transaction id as required  
* now using WPPIZZA_LOCALE constant in load_plugin_textdomain to make some other plugins happy   
* upped minimum php requirement to 5.3 ('Requires at least' display only)  
9th June 2014  


2.9.4.2  
* bugfix: any distinctly set js functions to run on cart refresh by external plugins were not run when "I am using a cache plugin" is/was enabled  
27th May 2014  

2.9.4.1  
* bugfix: iPhones (unlike iPads/Android etc) were not able to login on orderpage (if enabled)  
27th May 2014  


2.9.4  
* added some more gateway functions to use in future gateway development    
* added 'REFUNDED' as payment_status  
* bugfix: when using mail() or wp_mail() as mail delivery, external plugins (notably "delivery by post/zipcode") were not able to override "To" email address although they should have been able to do so  
24th May 2014  


2.9.3  
* streamlined some gateway functions  
* some more tidy up  
20th May 2014  


2.9.2  
* changed tax display default value from "incl. tax at %d%%" to "incl. tax at %s%%"  to also display decimals accurately  
* added CAPTURED as payment_status  
* fixed number/price formatting on history page  
* fix: replaced wp_reset_query with wp_reset_postdata after end of loop  
* load smoothness jQuery UI theme (used in timepicker) in admin only when post_type == wppizza to not interfere with other plugins layouts/css    
* added a bunch of helper functions in wppizza.gateways.inc.php that can be used in gateways to make development of other/future gateways easier  
* added default vars when filtering wp_title to eliminate some possible php notices/warnings regarding missing arguments  
* some maintenance/tidy up in places  
17th May 2014  



2.9.1  
* all filtering of order history by order status (admin)  
* added filter hook (wppizza_history_query_where_filter) to add/filter where clause in order history  (frontend)  
28th April 2014  


2.9  
* added reporting (admin)  
* added order history (and template) to display users previously bought items (use appropriate shortcode - see faq's)  
* closed some - erroneously - not closed span elements in loop templates  
* added distinct id's in cart li elements  to aid possible css declarations   
* added classes and id's as appropriate to additives display in frontend  
* removed superflous menu item count in cart when allowing input/textbox to to multiply cart items  
* added ability to add/remove actions etc used in wppizza_actions class in a themes function.php file if required [e.g. remove_action( [action-hook], array( WPPIZZA_ACTIONS::this() , [action] )); ]  
28th April 2014  

  
2.8.9.11  
* to enable updating to WP 2.9 and/or PHP 5.5+:  
* eliminated a couple of php notices when using/updating to Wordpress 3.9 (as some things have changed in WP 3.9)  
* replaced mysql_real_ascape_string with esc_sql (as usage of mysql has been has been deprecated in WP 3.9 in favour of mysqli when running php 5.5+)  
* eliminated brackets from wp_editor() ID (as these are not allowed anymore in WP 3.9)
17th April 2014  


2.8.9.10  
* added option to not have an order form field required when self pickup is chosen (set in order form settings).  
* amended and added alternative to Serbian Currency Symbols  
* Added 'Authorized' ENUM value to wppizza_orders table payment_status field (might come in handy in the future)  
15th April 2014  

  
2.8.9.9  
* added reset_query to end of loop so as to not confuse some themes    
* added spans to opening times to aid styling  
* added a few more filters to summary function that could be used if required  
* re-added (got lost somewhere in a previous update since 2.8.6.2 ) using the gateway frontend label in emails  to identify gateway as opposed to obscure things like COD  
* optionally append internal order table id to transaction id in emails and order history (enable in order settings)  
4th April 2014  


2.8.9.8  
* added option to enable a final, non-editable confirmation page before payment/order processing  (some countries/jurisdictions might require this. enable and edit as required in wppizza->order form settings)  
1st April 2014  


2.8.9.7     
* allow include in shortcode (as well as exclude -> see faq:shortcodes)  
* removed login from orderpage if "anyone can register" has NOT been enabled  
* customised templates should now be in child theme (if used)  
* customised templates should now also work from within a subdirectory called 'wppizza' (if used, ALL customised templates will have to be in this directory though)  
31st March 2014   


2.8.9.6     
* Maintenance Release. (Eliminated some php notices amongst other things)  
* Added proviso for gateways to add surcharges themselves on checkout (epay.dk for example) in which case "handling charges" on order page will be displayed as "calculated at checkout" (amended wppizza-order.php template accordingly)  
* Amended js to allow gateways to add their own javascript to show/execute payments - in an overlay for example - instead of being tied to a redirect  
* amended readme  
25th March 2014   


2.8.9.5  
* ADDED: handling charges (if set in gateway) will now also be displayed/calculated on orderpage with any future gateways (and/or PayPal Standard Gateway 2.1.6+ , Authorize.net 1.1+)   
* eliminated some - possible - rounding errors when hide decimals has been enabled  
* amended readme  
20th March 2014   


2.8.9.4  
* ADDED: option to group, sort and display items by category in cart, order page, thank you page and emails  
* ADDED: filters and action hooks in the relevant templates to enable the above (which could of course also be used for other purposes if required)  
18th March 2014   


2.8.9.3  
* ADDED: option to have any taxes displayed without adding them to the prices/totals (i.e if prices are entered with taxes already included but applicable taxes do still need to be visible)   
* prettied up admin somewhat  
13th March 2014   


2.8.9.2  
* ADDED: option to set currency symbol in cart, emails, order and thank you page to be to the right of the price   
* made currency symbol display consistently (i.e left / right of price) in conjunction with the above    
* ADDED: filter to set style of html email template via filter hook instead of editing template directly  
* removed some superflous linebreaks in order history display of items  
* some minor css tweaks  
12th March 2014   


2.8.9.1  
* Maintenance Release - nothing too dramatic   
* eliminate some possible php notices  
* ensure wppizza user meta gets also deleted on uninstall  
* eliminate possible conflict when another plugin also uses user_register action hook  
11th March 2014   



2.8.9  
* ADDED: order page - option to login, register new account on order or continue/order as guest [if not logged in already] (wppizza->order form settings "email" field must be set to enabled, required and Settings->General must have "anyone can register" enabled for registration of new account on order to work)    
* ADDED: Spanish Translation  
* Readme update  
* Maintenance  
7th March 2014   



2.8.8.4  
* BUGFIX: previous version broke distinctly set closing times during normal opening hours   
3rd March 2014   

2.8.8.3   
* added filter (wppizza_filter_summary) to summary(wppizza_order_summary)  to selectively being able to overwrite variables in cart and orderpage  
* added filter (wppizza_filter_is_open) to summary(wppizza_order_summary)  to selectively being able to force the shop to be open    
* entered details on order page also stay if switching between self-pickup and delivery (see also using $_SESSIONS instead of $_GET now)    
* using $_SESSIONS instead of $_GET variables to keep user info on orderpage when adding tips or changing from self-pickup to delivery and vice versa  
* max decimals in any percentages settings now 5 instead of just 2 
* BUGFIX: fixed some issues in some circumstances where percentages were not being saved with decimals depending on language used and hide decimals being on   
23rd February 2014   


2.8.8.2   
* eliminated some more php notices  
* made sticky/scrolling cart work when using 3rd party cache plugins (and wppizza->settings->"I am using a caching plugin" has been enabled)   
11th February 2014   

2.8.8.1   
* eliminated php warning when no additive was defined  
* BUGFIX: js sticky cart (used or not) threw "object has no method height" in certain circumstances    
10th February 2014   



2.8.8   
* added ability to custom sort (and ident) to additives  
* added spans with id's and classes to additives to enable css styling  
* added simple display of order total (displayed orders or all orders) in order history   
* made scrolling cart behave with older versions of jQuery  
* removed silly red border on scrolling cart when using default.css (accidentally left in when used during development)  
* internal: added additional edd_sl methods (classes/wppizza.edd.inc.php) to make future developments of additional gateways easier  
* internal: added mbstring check  
* internal: added boolean validation function  
* BUGFIX: fixed float bug (due to different number format locale) when ommitting decimals (wppizza->layout) and item/discount/total values etc  price(s)  is/are >1000   
* BUGFIX: when selecting "do not display decimals", percentage discounts also had decimals omitted in admin view (albeit they were still applied correctly including decimals)  
* BUGFIX: non-admins who had rights to view selected pages were able to access but not able to save them (surprised no-one noticed this before really)  
9th February 2014   


2.8.7.3   
* added option to briefly replace item price with an (editable) "item added" text to give more immediate feedback to user  
* stopped sticky cart from jumping all over the place in certain layout scenarios  
* added option to make sticky cart not scroll further down than a user definable element. Might be useful in certain themes  
1st February 2014   


2.8.7.2   
* made an error when committing previous version  
29th January 2014   
 
2.8.7.1   
* added action hook to wppizza-phpmailer-settings.php to do additional stuff if required after $mail->Send();  
29th January 2014   
 
 
2.8.7   
* WPML: Added missing translations to order page, loop templates and emails and fixed some bugs when updating plugin whilst not being in the main language  
* WPML: Order page was not excluded from navigation (if set) in any other than the main language  
* WPML: eliminated some php notices/warnings on new install of WPPizza when WPML is already installed  
* WPML: removed superflous (and wpml interfering) get_option query from loop templates   
* minified frontend css  (and updated readme regarding customisation of css accordingly) 
* added some animation options (wppizza->layout) when setting cart to be always visible when scrolling ("sticky")  
29th January 2014  

2.8.6.3   
* EXPERIMENTAL: added option to force cart to be loaded dynamically via ajax when using a cache plugin - wppizza->settings->I am using a caching plugin (you still want to exclude your order page from caching)  
20th January 2014  

2.8.6.2  
* using set gateway frontend label in emails  to identify gateway as opposed to obscure things like COD (especially given that it might be something different in different languages)  
16th January 2014  


2.8.6.1
* fixed wmpl related email attachments php warning when value did not exist(yet)  
* updated the readme in a couple of places  
16th January 2014  

  
2.8.6  
* made sure input fields do not loose their entered value on orderpage when tip is being added   
* optionally add attachments to order emails  
* added distinct option to disable delivery altogether ( wppizza->order settings->delivery charges)   
* added function to redirect to the right language orderpage when using wpml  
* added notes section/field in order history  
16th January 2014  

2.8.5.2  
* updated Hebrew translation (thanks Yair)  
* added action hooks to after emails have been sent with order id as parameter  
* filtered title tag to NOT add 'WPPizza Categories' in frontend when using templates (as opposed tp pages)   
* fixed some minor character decoding issues  
20th December 2013  

2.8.5.1  
* accidentally committed a development version (2.8.5) which had links to single posts displayed by default in the loop template . now fixed..
13th December 2013  

2.8.5  
* added a bunch of action hooks to the loop templates  
* added a filter that can be hooked into for the navigation  
* admin  - when trying to print in the order history, Android did not understand window.print(). Introduced a workaround to open a window that can then be printed on Android (via additionally available Android printing Apps)  
* admin - added transaction id and gateway used when printing order  
* eliminated some php notices when using shortcode in wp posts (although using the shortcodes in pages is recommended. but if you must...)     
* added "translatable" array of row numbers to style localization (admin) odd/even rows depending on language used   
* added string that can be localized when mail sending error occurs (displayed additionally to the actual error)  
* tested with WP 3.8 (although the admin could be *visually* improved as WP 3.8 uses bigger fonts - to address in future versions -  it all seems to behave just fine, which is the important thing)   
* BUGFIX: in some scenarios "thank you page" displayed "thank you" instead of error (as it should do) when email had NOT been sent due to mailer/server error      
13th December 2013  



2.8.4  
* added space between fromname and fromemail when sending email to make spamassassin happier (only applies when using wp_mail and mail)  
* added gratuity/tip from field that can be used in order form inclding relevant css declarations  
* added some more action hooks  
* added user caps class to deal more easily with acces rights to plugin and extensions to the plugin  
* added easy digital download sl classes for any possible extensions to hook into to allow notifications of updates for non-wp hosted extensions (not used in the main plugin , but might come in handy for extensions)  
* eliminated some more php notices  
* some minor bugfixes  
4th December 2013  

2.8.3  
* BUGFIX: in some circumstances - when updating the plugin - some values of some newly added localization variables - did not get set correctly due to the use of require_once instead of require.  
* Maintenance: changed priority of wppizza_set_order_status filter as it might otherwise interfere with options update   
26th November 2013  

2.8.2  
* BUGFIX: 2.8 and 2.8.1 was broken for new installs !!! Do not use 2.8 or 2.8.1 for new installs   
25th November 2013  


2.8.1  
* Maintenance Release  
25th November 2013  


2.8  
* added ids and classes to additives list at bottom of page  
* added a bunch of do_actions to wppizza-cart.php template to be hooked into as required  
* added "empty cart" button  
* added some more comments to templates  
* allow menu item ids to be excluded when using shortcodes 
note: if you are a customised version of wppizza-loop.php or wppizza-loop-responsive.php please add : 'post__not_in' => $exclude (at approx line 70) , as - for simplicities sake - it  has been added there 
* added method (gateway_settings_non_editable) that can be used in gateways to update/add non-editable options values to gatways options (like version numbers etc)  
* added filter to add other order status types in order history (wppizza_filter_order_status). use with caution, backup your data first  

25th November 2013  

2.7.1  
* added display of label/id to uniquely identify meal sizes/price tiers in admin screens as these might have the same frontend labels    
22th November 2013  


2.7  
* allow prefill of orderpage of user is logged in and adding of formfields at registration    
20th November 2013  

2.6.7.3  
* fixed Poland [PLN] Currency Symbol    
* exclude id's from category navigation (shortcode only at the moment)
20th November 2013  

2.6.7.2  
* Maintenance Release  
* Added RMB as alternative to CNY currency (chinese)   
20th November 2013  

2.6.7.1  
* fixed "cash on delivery" not submitting when gatway choice set to dropdown  
19th November 2013  

   
2.6.7   
* prefill orderpage with wp email and nicename when user is logged in but has never ordered before  
* added option to add and display additional handling charges (might be used and probably most useful for CC gateways)  
* BUGFIX: fixed reintroduced bug in previous version in certain circumstances, that allowed order even when minimum order value had not been reached  
* some other minor bugfixes  

18th November 2013  


2.6.6.1   
* eliminated some php undefined notices     
17th November 2013  


2.6.6    
* finally, storing customer data in uesermeta table to enable prefill of order page if user is logged in. Subsequently, amended wppizza-order.php template and admin order form settings   
14th November 2013  


2.6.5.2    
* added option to exclude menu items when calculating delivery charges  
14th November 2013  


2.6.5.1    
* forced charset/collate to utf8/utf8_general_ci respectively on wppizza_orders table to deal with any non latin characters when the default charset of the db is non utf8   
* allow "enter" to update cart contents (i.e number of specific items in cart) when using "Enable increase/decrease of items in cart via input field/textbox"  
* added fixed delivery charge option if free delivery value has not been reached  
14th November 2013  


2.6.5    
* added 'extenddata' key to session items array to be able to save extended data to db and aid future developments of extensions  
11th November 2013  


2.6.4.1    
* fixed bug when using custom non cc gateways always defaulting to COD  
6th November 2013  

2.6.4    
* added checkbox to cartwidget to keep cart visible on page when scrolling  
* added order last status update to order history
* minor css additions to avoid possible linebreaks between prices and currency symbols  
* minor admin css additions/edits for gateway page and order history page    
* added ability (and template) to use internal ajax order submit if one wants to add another 'cod' like gateway i.e without going through external processing (bacs or cheque for instance. see included zip in "wppizza/add-ons" for example).   
6th November 2013  

2.6.3  
* 2.6.2 fixed iThing but broke Android instead (what can i say)....this should work on both now   
5th November 2013  

2.6.2  
* fixed touchstart event for iThing devices (were broken in versions 2.6 and 2.6.1)   
5th November 2013  

2.6.1  
* added filter to cpt labels and arguments ('wppizza_cpt_lbls' and 'wppizza_cpt_args' respectively if you want to use it)   
* minor lng fix
4th November 2013  


2.6  
*  some themes/jQuery combinations may have double triggered adding to cart clicks on some mobile devices, so the javascript functions have been amended to address this issue  
3rd November 2013  


2.5.7  
* BUGFIX: plugin lost metadata when using quickedit/bulkedit in custom post type  
2nd November 2013  

2.5.6  
* added option to enable prettyPhoto (Lightbox Clone) to menu item images in Layout Options Page     
* BUGFIX: jQuery validation plugin did not follow "Javascript in Footer" Settings, but was always added to footer (which broke things when there was no wp_footer() in theme)  
1st November 2013  


2.5.5  
* fixed Bulgarian [BGN] Currency Symbol    
* added Italien Translation   
* updated po/mo files   
* allow change of WPPizza name in Admin (just add "define('WPPIZZA_NAME', 'The Name You Want');" to your wp-config.php) 
* allow change of WPPizza Menu Icon in Admin (just add "define('WPPIZZA_MENU_ICON', 'http://path/to/icon.png');" to your wp-config.php) 
31st October 2013  

2.5.4.1  
* added shekel sign (&#8362; instead of ILS) to ILS currency  
* updated po/mo files   
17th October 2013  


2.5.4  
* re-added print order button in history which was accidentally ommitted again in previous versions)  
* added option to grant access to pages depending on role   
* moved option to clean up the order history database to dedicated "tools" page   
11th October 2013  

2.5.3    
* updated dutch translation.  
* option to set minimum order value before any delivery will be offered (free or paid)  
9th October 2013  


2.5.2  
* eliminated some notices when updating plugin.  
* any "selects"  in order form settings also lost their associated values if set. this should not happen anymore.  nevertheless, please check your order form settings and resave if necessary  
3rd October 2013  


2.5.1  
* allow tax to be applied to delivery/shipping too  
2nd October 2013  


2.5  
* made plugin frontend wpml compatible (tested with WP 3.6 , WPML Multilingual CMS Version 2.9.2, WPML String Translation Version 1.8.2 and WPML Translation Management Version 1.7.2, but should work with any reasonably recent wpml version(s) )     
* Dutch translation added (thank you Jelmer)  - (1 string still in english. Will be updated when available)  
* more consistant handling of localized number formatting throughout the plugin  
* added option to enable increase/decrease of items in cart (enable/disable in wppizza->layout) and respective css  
* added 4 more custom order form fields  
* added template for plaintext emails  
* added print order button in history  
* added category (name etc) to items array that can be used in email templates for each item  
* added filter/ability to use loop so it can be used for single posts too (see notes in templates/wppizza-single.php for how to do that to make it play nicely with your theme)  
* added various other filters for order items and metadata  
* added option to use customised admin css in theme directory  (either use wppizza-admin-custom.css in theme directory to load original and overwrite only some declarations or copy wppizza-admin.css to overwrite all)    
* prettied up localization page somewhat  
* prettied up remove button in cart  
* added admin js error checking to have at least one meal size option set  
* js disable (and re-enable) 'place order' button in cart when/while updating cart  
* added filter for localized javascript variables for other extensions to hook into  
* added javascript function for extensions to hook into after cart has been refreshed (in conjunction with above)  
* other minor improvements and tidy-ups  
* TODO -> more consistency in encoding and decoding of entities  
1st October 2013  



2.4.3  
* Added "AddAttachment" comment/placeholder  in templates/wppizza-phpmailer-settings.php (might be necessary for some email2fax gateways)   
* added entity decoding function to subject line in emails (in case encoded apostrophies etc are being used)  
* BUGFIX templates/wppizza-order-email-html.php ( wppizza-order-html-email.php : legacy version) as well as templates/wppizza-phpmailer-settings.php were included uing uri instead of absolute path when using a custom version in theme directory  
* BUGFIX Fixed short php opening tag in templates/wppizza-phpmailer-settings.php  
6th Sep 2013  


2.4.2  
* corrected some html email character encoding for non-standard characters  
* added option to set from email name and address statically (some fax gateways might need that)  
* minor css adjustments  
* escaped a few more db update entries which in some circumstances might have cuased problems  
27th Aug 2013  



2.4.1  
* updated some erroneous documentation at the top of the wppizza-order-email-subject.php template   
* timestamp of orders gets updated to when the emails are actually being sent as opposed to when the order was initialized on th eorder page  
* BUGFIX in conjunction with the above: when orders were displayed on thank you page, the timestamp shown was based on utc instead of local time  
16th Aug 2013  

2.4  
* changed email handling to make it more consistant and easier to use in gateways   
* added transaction id's as well as gateway that was used to emails being sent (html and plaintext)  
* added localization variable/text to be displayed in footer of emails (if set)  
* changed email html templates to use ob_start instead of variables (although old customised templates will still work)  
* added a bit more information to wppizza-order-email-subject template  
* formatted submitted textarea fields more appropriatly for html email template   
* added action to each form field in order page  
* allow editor role to see order history and edit status (but not delete any order, that can only be done by admin role)  
* BUGFIX added minus sign to discount in cart when item has been added via ajax  
* SECURITY FIX regarding post variables    
* SECURITY FIX which might have allowed (with a lot of effort mind you and only if logged in) to change SOME variables of the plugin without having sufficient privileges   
13th Aug 2013  

2.3.2  
* added polling interval to order history  
* eliminated some php notices that might occur in some rare circumstances  
6th Aug 2013  


2.3.1.2  
* made error when committing 2.3.1.1. no other changes made  

2.3.1.1  
* set multisite checkbox as being selected by default as chances are every site in the network has different settings/belong to a different restaurant   


2.3.1  
* email subject now changeable/customisable (see template wppizza-order-email-subject.php)  
* eliminate fatal error when plugin is re-installed without properly uninstalling it first (why would you do that anyway though ? oh well...)  
* BUGFIX when empty delete all categories was selected, only non empty categories were deleted  
* BUGFIX when using shortcode [wppizza category='non-existing-cat'] with a non-existing category, errors were thrown on page instead of just a blank page without menu items  
- 29th July 2013  



2.3  
* eliminated some more php notices when not displaying any currency symbols  
* moved some common functions out of individual gateway classes to shared wppizza_gateway class to be able to re-use them  
* changed template name wppizza-cod-show-order.php to wppizza-show-order.php as it can be used for all gateways  
* all initialized orders will be stored in db (it used to be that cod orders were only inserted when completed)  
* put index on order table wp_user_id column  
* added loading div when redirecting to gateway  
* added option to delete abandoned/cancelled orders older than 1+ days from db  
* added actions that can be used when displaying order in thank you page (such as a print button for example)  
* added option to navigation/navigation widget to only show subcategories of a given category  
* BUGFIX js displayed "null" (instead of it being empty) in cart when adding/removing items and not displaying currency symbol  

- 24th July 2013  

2.2  
* eliminated some more php notices  
* add ability to extend send email class  
* changed Bulgarian Currency Symbol  
* prettied up some css in admin  
- 13th July 2013  

2.1.3  
* eliminated some more php notices in cod gateway class    
- 8th July 2013  

2.1.2  
* eliminated some (inconsequential) php warnings when no additives were defined  
- 5th July 2013  

2.1.1  
* BUGFIX Weekdays in oredering times were not localized  
- 2nd July 2013  


2.1  
* added option to display order details on "thankyou" page  
* made some more variables translatable via po/mo files  
* moved localization description out of the option table as it really doesnt belong there (which in turn makes it translatable via mo/po files)  
- 1st July 2013  


2.0  
* quite a major re-write   
* added destinct gateway page/options (default COD) as class to enable (future)additions of other gateway extensions 
* added distinct class to "send order" button on orderpage  
* updated/changed following files/templates to work with the classes "wppizza-order.php / wppizza-cart.php / wppizza-order-html-email.php / wppizza-phpmailer-settings.php / css/wppizza-default.css"  
* NOTE: if you are using customised versions of the above you MUST update these. (ESPECIALLY   wppizza-order.php and  wppizza-phpmailer-settings.php)
* added option to charge tax on sales  
* added option to apply discount to self-pickup of order  
* wppizza_orders table changed to store and retrieve gateway variables and hashes and to be able to distinguish which gateway was used if(and which) user - if logged in - ordered etc  
* autofill orderpage emails and name if user is logged in  
* added responsive layout option  
* BUGFIX categories were not displaying in the right order when using widget/shortcode to display category navigation  
* BUGFIX bcc's not sending when using phpmailer  
- 21st June 2013  


1.4.1.2    
* added warning regarding changes in upcoming release  


1.4.1.1  
* BUGFIX fixed orderpage not refreshing when switching between self-pickup and delivery  
- 27th May 2013  


1.4.1  
* added option for customer to select self-pickup instead of delivery (results in no delivery charges)   
* changed templates(wppizza-cart.php/wppizza-order.php/wppizza-order-html-email.php) and css(wppizza-default.css) to account for self-pickup   
* added ability to keep main plugin css to reflect future additions etc and only overwrite the required line by copying wppizza-custom.css to theme directory    
* added some comments about IE legend css in css file   
- 14th May 2013  

1.4  
* BUGFIX fixed ac ouple of short open tags  
* Multisite: add option to distinguish between cart contents when using subdirectories  
* Moved from using plugin bundled phpmailer - introduced in 1.3 to send HTML mails - to using phpmailer that comes with WP  
* added option to set delivery charges on a per item basis  
* added touchstart to click events in js  
* updated readme  
- 10th May 2013  

1.3.2.1  
* BUGFIX fixed some STRICT "variables should be passed by reference" errors  
- 5th May 2013  

1.3.2  
* BUGFIX fixed error messed up category sorting when using drag and drop whilst displaying fewer than all categories  
- 3rd May 2013  

1.3.1.2  
* BUGFIX fixed error that stopped non-administrators accessing the backend   
- 2nd May 2013  

1.3.1.1  
* BUGFIX fixed parse error (introduced in 1.3) in metaboxes that stopped these being available   
- 2nd May 2013  

1.3.1  
* custom formatted plaintext email when sending via phpmailer instead of phpmailer autogenerated plaintext  
- 1st May 2013  

1.3  
* Added Layout option (display currency symbols directly next to prices left/right/none)   
* Added Layout option (display main big currency symbols next to menu item left/right/none )   
* Added Email Send Options (mail / wp_mail / phpmailer with templates)   
- 1st May 2013  

1.2.4  
* BUGFIX IE7 and IE8 did not go to the order page when clicking button at bottom of cart   
- 22nd April 2013   

1.2.3  
* BUGFIX rounding/display error when using non-english number format  
- 16th April 2013  

1.2.2.1  
* minor template changes (superflous apostrophies)  
- 13th April 2013  

1.2.2
* added ability to add item to cart when clicking on item title (if enabled)  
* added time formatting options for frontend openingtimes display  
* BUGFIX a couple of settings - if changed by user (email and bcc) - might have got reset to defaults when updating plugin  
* BUGFIX timezone DST bug - opening times were out between 0:00 and 1:00 when server time != wordpress time   
- 3rd April 2013  

1.2
* BUGFIX plugin lost custom closed times when updating from 1.1   
- 2nd April 2013  

1.1.1  
* localized number formatting  
* option to hide pricetier in frontend when there's only one
* some css beautifications in cart  
- 2nd April 2013  


1.1  
* add ability to be closed for certain hours (i.e be open 2 or more times per day)  
* made localization of thank you order page info html enabled textarea as opposed to plain text input  
* BUGFIX missing currency symbol in total when adding items to cart
* BUGFIX not being able to save "globally suppress headers" in layout option screen
* BUGFIX timezone (DST) issue  
* BUGFIX custom order form: when using selects only ids were submitted by email not the actual value set in comma separated value/array  
- 1st April 2013  

1.0.3  
* fixed bug that displayed default as opposed to actual prices
* updated google library for timepicker when setting opening times  
* readme.txt updates  
* removed some obsolete functions and stripped tags when submitting an order  
* removed "loading" string in div when waiting for order to be processed/sent (ajax spinner should be enough)  
* added padding to div to pretty up thank you page  
* added a lot more currencies (including not displaying any at all)  
* added the ability to hide currency symbol next to items only  
- 30th March 2013  

1.0.2  
* minor readme.txt updates  
* enable screen info when first installing plugin  
* moved screenshots out of plugin into repository assets directory  
* minor bugfixes  
 - 17th March 2013

1.0.1  
* minor readme.txt updates - 15th March 2013

1.0  
* Initial release - 15th March 2013



== Frequently Asked Questions ==


= Can I edit the css ? =

although the css has been written so that it works with many themes out of the box (see www.wp-pizza.com - all themes use the same default stylesheet) you might want to adjust some things here and there to work with your theme (especially if you want to support older browsers).  
if you do, copy the wppizza/css/wppizza-default.source.css (or wppizza/css/wppizza-responsive.source.css if using the responsive style) as wppizza-default.css (or wppizza-responsive.css)  to your theme directory (so it does not get overwritten by future updates of the plugin) and edit as required.  

**alternatively *(and possibly better as any future updates to the main css will still be reflected)*, just copy wppizza-custom.css to your theme directory (or child theme directory if used as of v2.8.9.7) and only overwrite the styles you need to override.**  

(this file will be read AFTER the main default.css). "Include css" has to be enabled for this to apply   

PS: Optionally as of version 2.8.9.7 - you could use a subdirectory called wppizza for all your customised wppizza templates.   


= Can I use this plugin when I am not using english on my site ? =

of course you can.  
although the administration backend is currently only available in a few other languages apart from english (albeit translation ready, contact me if you want to help me translating it into a different language), 
you can have the frontend in whatever language you want. just go to wppizza->settings->localization and edit the variables as required. Furthermore, WPPizza is WPML compatible.


= How do I sort the categories ? =

go to: wppizza->categories in the administration and drag and drop. If you wish to have subcategories, edit the required category and select the appropriate parent.


= How do I add a menu item ? =

go to: wppizza->add new  
add title, description, any preservatives the item might have (editable from wppizza->settings->additives), select pricetier (editable from wppizza->settings->meal sizes) and add prices as required.  
make sure to also select at least one category.  
if you wish to add an image, either set a featured image for this menu item (if available in your theme) or add an image as normal in your description.  
the size of the featured image displayed will depend on your thumbnail size set in settings->media: thumbnail size
if your theme does not allow featured images, and you are adding pictures directly into the description, you might want to turn off "display placeholder image" in wppizza->settings->layout  
alternatively, add " add_theme_support( 'post-thumbnails'); " (without the quotes) to your themes function.php file  


= Can I just display the menu without offering online order  ? =

Sure.  
Just don't display the shoppingcart anywhere. If you choose to do this, you might also want to delete any orderpage you might have (as there's nothing to order).  
Alternatively, if you still want to show the cart (to show combined prices/taxes/discounts) but want to disable ordering, you could copy wppizza-custom.css to your them directory and add ".wppizza-cart-button{display:none}" without the quotes to it.
You can probably also achieve the same thing by using a combination of the layout and/or order setting options available from within the plugin.
You would still probably want to delete/hide your order page or at least disable all your gateways  


= I'm using the plugin with xyz theme and it's all messed up  =

first thing to try (if you haven't done so already): re-save the pages WPPizza has created   
(I'm NOT talking about the WPPizza Categories and Menu Item Pages, but the pages that display a list of your item - typically a page that has somthing like [wppizza -some attributes-] on it)

make sure your theme (or one of your plugins) does not use an old version of jQuery (some themes/plugins override the version that comes bundled with wordpress - no, i don't know either why they would do that)  

If that still doesn't help, you might have to adjust the CSS (see 'Can I edit the css ?' above)

If you have problems, send me a link to your site I'll have a look...


= can i send html emails ? =  

yup.  
go to wppizza->settings and change "Select Type of Mail Delivery" to "HTML and plaintext"  
if you do , you probably want to edit the html template. To do so, move "wppizza-order-html-email.php" from the wppizza template directory to your theme folder and edit as required  
if you want to use smtp, do the same with "wppizza-phpmailer-settings.php"  and edit as needed  


= What are the available shortcodes ? =  

in case where you cannot or do not want to use a widget, here are the corresponding shortcodes:
	
- **display orderpage**

	attributes:  
	- type='orderpage' 		(required [str])  
	
	example: 		[wppizza type='orderpage']	


- **display items** in category 

	attributes:  
	- category='pizza' 		(optional: '[category-slug]'. if omitted, will display the first category)  
	- noheader='1' 			(optional: 'anything'. omit attribute to show header. will suppress header (category title and description) in wppizza-loop.php. you can globally hide all category headers by setting "suppress headers" in wppizza->settings->layout.)  
	- showadditives='0' 	(optional: '0' or '1'. if omitted, a list of additives will be displayed if any of the category items has additives added. if set (0 or 1): force to display/hide additives list. useful when displaying more than 1 category on a page)  
	- exclude='6,5,8' 		(optional [comma separated menu item id's]): exclude some id's  
	- include='6,5,8' 		(optional [comma separated menu item id's]): include only these id's (overrides exclude)  
	- note: if you want to edit the category loop and/or headers, copy wppizza-loop.php from the plugins template directory into your theme directory and edit it there.  
	
	example: 		[wppizza category='pizza' noheader='1' showadditives='0' exclude='6,5,8']


- **display openingtimes** (returns grouped opening times in a string):

	attributes:  
	- type='openingtimes' 	(required [str])  

	example: 		[wppizza type='openingtimes']


- **display shopping cart**

	attributes:  
	- type='cart' 			(required [str])  
 	- stickycart='1' 		(optional[bool]: anything. if its defined the cart will always be in viewpoint even when scrolling)  
 	- openingtimes='1' 		(optional[bool]: anything. if its defined openingtimes get displayed above cart)  
 	- orderinfo=1				(optional[bool]: anything. if its defined order info (discounts free delivery etc) get displayed below cart)  
 	- width='200px' 			(optional[str]: value in px or % -> defaults to 100%) (although < 150px is probably bad)  
 	- height='200' 			(optional[str]: value in px -> defaults to 250px)  
	
	example: 		[wppizza type='cart' openingtimes='1' orderinfo='1' width='90%' height='350']  


- **display the navigation** (when using template files , see What to do on first install ? -> option 2):
	
	attributes:  
	- type='navigation' 		(required [str])  
	- title='some title' 		(optional[str]: will render as h2 as first element in cart element if set)  
	- parent='slug-name' 		(optionsl [str]): only show child categories of this slug  
	- exclude='6,5,8' 			(optional [comma separated category id's]): exclude some id's  

	example: 		[wppizza type='navigation' title='some title' parent='slug-name' exclude='6,5,8']  



- **display the users order history** (displays login form if not logged in, "anyone can register" has to be enabled):  
	
	attributes:  
	- type='orderhistory' 		(required [str])  	

	example: 		[wppizza type='orderhistory']  

	note: you would probably want to disable comments etc on that page  


= Shortcodes do not work in widgets ? =  

Some Themes do not have shortcode support enabled in text widgets.  
To enable, either add this line to the functions.php of your theme;

	add_filter('widget_text', 'do_shortcode');

or use one of the wordpress plugins available here for example, that do this  
http://wordpress.org/plugins/search.php?q=shortcode+in+text+widget  



= Can I edit the templates ? =

Sure, if you want.  
Just make sure you copy the relevant template from wppizza/templates/ to your theme directory (or as of version 2.8.9.7 child theme directory if used) and edit those, so you don't loose your edits when the plugin gets updated. Make sure to read the comments in the relevant files.

**Preferably though - if at all possible - I would suggest to use css and/or some of the action hooks provided  to be able to take advantage of any possible future updates or improvements of those templates**


= How do the orders get to my restaurant ? =

by email.  
if you need them to be sent by fax for example you will have to look into integrating your orders with a fax2email gateway. (search for it on your favourite search engine)


= Where do I set the images for any particular menu item? =

just use the "featured image" for a menu item  
if you do not have this options it's either hidden (go to "screen options" at the top of the page to enable/show it) or your theme does not have thumbnails enabled (tell the author about it. not my fault)
if the auther doesnt want to or cannot do anything about it, you can also try just to put the following code at the bottom of your themes function file (before the closing ?> if any )
	
	add_theme_support('post-thumbnails');


= My shop doesn't do Pizzas, how do I change the name and icon in the administration panel ? =

* to change the name (i.e WPPizza) just add "define('WPPIZZA_NAME', 'The Name You Want');" to your wp-config.php 
* to change the WPPizza Menu Icon in Admin Panel next to the Name just add "define('WPPIZZA_MENU_ICON', 'http://path/to/icon.png');" to your wp-config.php 
	  


= using a cache plugin =  

* if you are using a cache plugin, you MUST exclude your order page from being cached  
* you will probably also want to enable "I am using a caching plugin" in wppizza->settings  
	  


= How can I submit a bug, ask for help or request a new feature? =

	- leave a message on the <a href="http://wordpress.org/support/plugin/wppizza">wordpress forum</a> and I'll respond asap.  
	- send an email to dev[at]wp-pizza.com with as much info as you can give me or 
	- use the "contact us" or "feature request" page on <a href="http://www.wp-pizza.com/">www.wp-pizza.com</a>

