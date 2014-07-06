=== WPPizza Add Ingredients===
Contributors: ollybach
Author URI: http://www.wp-pizza.com/
Plugin URI: http://www.wp-pizza.com/
Tags: pizza, restaurant, order online
Requires at least: WPPizza 2.6.5+, PHP 5.2, WP 3.5 
Tested up to: 3.9.1
Stable tag: 4.3.4.3


Extends WPPizza to allow adding of additional ingredients to any given menu item by the customer. Requires WPPIZZA 2.6.5+


== Description ==

Extends WPPizza to allow adding of additional ingredients to any given menu item by the customer. Requires WPPIZZA 2.6.5+


== Installation ==

1. Upload the entire `wppizza-addingredients` folder to the `/wp-content/plugins/` directory.  
2. Activate the plugin through the 'Plugins' menu in WordPress.  
3. Add/Edit Ingredients in wppizza->settings->ingredients as required  
4. set custom groups if required  
5. Enable the addition of ingredients and whole/hals/quarters per menu item  


== Upgrade Notice ==


== Screenshots ==

1. frontend when "add ingredients" is enabled  
2. admin - ingredients administration  
3. admin - enable "add ingredients" option to menu item  


== Other Notes ==


== Changelog ==

4.3.4.3  
* Added a bunch of filters to customise output  
10th June 2014  

4.3.4.2  
* BUGFIX: under certain circumstances, in conjunction with a "add textbox to item" custom group, halfs and quarters were not possible to be enabled      
04th June 2014  


4.3.4.1  
* distinctly cast some vars as integers in for loops as php 5.5+ (at least 5.5.11 seems to and there's no harm done in doing it anyway) ends up in infinite loops otherwise   
26th May 2014  


4.3.4  
* allow textbox to be displayed/added without having to set ingredient  
* when using popup to display ingredient selection, set distinct background color to white and text color to black (as in some themes the text might otherwise close to being invisible)    
* INTERNAL: setting EDD license check link to use https instead of http  
26th May 2014  



4.3.3  
* added option to omit "1x" before selected ingredients in cart, orderpage, emails and order history (counts > 1 will always be displayed)  
* added classes to ingredients when selecting  
* added filter [wppizza_filter_ingredients_custom_sort] to be able to custom sort order of ingredients  
* added "add to cart as is" button option alongside choices for everywhere/half/quarters (will only be available if there are no mandatory selections, no pre-selections and more than one choice - e.g. halfs *and* quarters - are available to choose from)  
* made prices next to ingredients (if displayed) adhere to locale set  
* bugfix: when setting a custom group to have pre-selected items whilst setting preselected item prices to zero, the first selection of one of these ingredients was added at full price again (instead of 0) if it was entirely deselected first by the user and then added again (provided he was able to do that in the first place)  
2nd May 2014  


4.3.2  
* FIX: previous versions only ever looked in parent theme for customised css even when using a child theme. Therefore, IF using a child theme, make sure you copy any customised css there. (Installations not using a childtheme are unaffected)     
31st March 2014  


4.3.1  
* some themes might add things at li:before{content:'some stuff'} which could mess up the layout in the add ingredients window. this is now by default being forced to be empty i.e {content:''}  
* when selecting "Add textbox to item" in admin custom groups screen,  "hide prices" and "sort by price" are now NOT being displayed (as they make no sense there)  
* added option to specifically display pre-selected ingredients that have been de-selected by the customer (as in "No Onions" for example)  
* added menu item title to thickbox/popup header  
* added link to troubleshooting guide (regarding max_input_vars etc) to howto tab  
* fixed some layout issues with sticky header in popup on resize/orientationchange  
* added dynamic (added and then  removed) div to selectable ingredient fieldsets to disable additional selections whilst the server thinks about things to aid client side validation. as the js validation might otherwise not work reliably on slow (and i mean slow) servers  
* BUGFIX: in theory one could have set more ingredients as being pre-selected than the max different ingredients setting allows  
* BUGFIX: given that it is possible (although nonsensical) to create a custom group which requires 1,2 or more ingredient whilst also having another group that - in turn - excludes ALL of those ingredients again (so there would not really be anything at all to start off with) the serverside validation was still trying to validate this non-existent group (and therefore fail) . Now fixed  
* BUGFIX: cart did not show any comments/text associated with an ordered item (if entered) unless the page was refreshed  
26th February 1014


4.3  
* Added: option to sort ingredients by price first and then by name (custom groups and/or added ingredients)  
* Added: option to display or do not display prices next to ingredienst (custom groups and/or added ingredients)  
* Added: when displaying prices next added ingredients, allow 0.00 prices to not display or be replaced with customised text  
* Added: allow count of added ingredient to also be displayed next to the ingredient in the list of addable ingredients  
* Added: filter in backend to only display ingredients of a certain group  
* Added: Added custom group id to extenddata to identify which custom group the ingredient belonged to (by request - not used anywhere in this plugin though)    
* Bugfix: server side validation when trying to add a menu item that had halfs or quarters selected whilst also having a custom group applied to whole menu failed in certain circumstances and item did not get added to cart  
* Bugfix: when choosing a half/quarter menu item with a custom group applied to whole only where some ingredients were supposed to be pre-selected in this "whole" group relevant ingredients did not get pre-selected  
7th February 2014  


4.2.2  
* fixed some possible issues in certain scenarios when updating the plugin with WPML being active  
26th January 2014  


4.2.1  
* eliminated some php notices/warnings on first install when WPML is being used    
24th January 2014  


4.2  
* made ingredients popup - if used - more usable on frontend  when using mobile devices  
* added ability to filter custom groups in admin depending on which menu item or menu group a custom group applies to  
* added filter (wppizza_add_ingredients_filter_head,$array,$itemId,$tierId,$sizeId )to be able to inject customised html into add ingredients header section (where total price and 'add to cart' button are being displayed)     
* load minified version of css  
* bugfix: when setting a custom group to apply to whole item only, ingredients prices were still calculated by quarter / half as opposed to whole  
* bugfix: fixed some possible rounding errors  
20th January 2014  


4.1.1  
* bugfix: added ingredients could not be removed when added ingredients were set to "sticky" when using popup   
18th December 2013  

4.1  
* added option to keep selected ingredients sticky when using popup  
* added option to restrict custom menu to have a min total of ingredients selected (in a group)  
* bugfix that made admin link sometimes disappear when tab in another extension/plugin was selected   
8th December 2013  

4.0.1  
* added display of label/id to uniquely identify meal sizes/price tiers in admin screens as these might have the same frontend labels   
22nd November 2013  

4.0
* added licensing key to automatically inform users of available updates via the normal WP Dashboard  
17th November 2013  


3.2.1
* BUGFIX: wrong count/alert when setting restrict total sum of selected ingredients in custom group  
13th November 2013  


3.2.0.1
* prevented double click when adding ingredients    
* BUGFIX: allowed all custom groups to be deleted (previous version always kept at least one once it had been set)  
* BUGFIX: textboxes (custom groups) where always displayed when a group was selected as "Default [just like regular groups]
13th November 2013  


3.2  
*  added option to open add ingredients in popup layer (via WP thickbox)   
*  added option to restrict total sum of selected ingredients in custom group: "Group must have *AT LEAST* minimum number of ingredient selected below (multiple allowed and multiple per ingredient)"     
13th November 2013  

3.1.2  
*  now saving added ingredients data in [extenddata] key of session to aid future development    
11th November 2013  

3.1.1  
*  eliminated some php notices  
7th November 2013  


3.1  
*  some themes/jQuery combinations may have double triggered adding to cart / adding ingredients clicks on some mobile devices, so the javascript functions have been amended to address this issue  
3rd November 2013  

3.0.7  
* BUGFIX: plugin lost metadata when using quickedit/bulkedit  
2nd November 2013

3.0.6  
* fixed added ingredients displayed as "array" when custom group "Minimum number of different ingredients to select" allowed 0 selection and customer did not choose any     
31st October 2013


3.0.5  
* allow preselect ingredients prices to be set to zero  
* fixed fontend bug where preselected ingredients were not deselected in the pool of selectable ingredients when removed  
12th October 2013


3.0.4  
* allow negative pricing of ingredients  
* fixed bug when saving accessrights (previously it did not allow to revoke ALL rights for a user role, once one had been set)
12th October 2013



3.0.3  
* option to copy all ingredients of one menu sizes group to another  
* option to restrict custom group to whole menu item only even when toppings per half/quarter are enable on menu item  
* example: offering a pizza with a ingredienst for half and half or quarter toppings, but being able to create a group of "thin crust" or "deep pan" for the whole pizza - as you cannot have one half "deep pan" and the other "thin crust"  
* updated howto  
7th October 2013

3.0.1  
* made plugin wpml compatible (tested with WP 3.6 , WPML Multilingual CMS Version 2.9.2, WPML String Translation Version 1.8.2 and WPML Translation Management Version 1.7.2, but should work with any reasonably recent version(s))  
* added class (and css declaration) to half/quarter icons  
* made custom current groups label in admin custom groups more prominent  
* summary of custom groups in menu item view  
* option to copy single ingredient  
* add comment/textbox on a per menu item basis (in custom group)  
- 16th September 2013  


3.0    
* add ingredients per whole/half/quarter of menu item  
* pre-select ingredients per menu item  
* exclude ingredients per menu item  
* made custom groups "must have at least..."  optional by being able to set minimum required ingredients to 0  
* "Access Rights" Tab to allow access to pages depending on user role  
* summary in custom groups to not have to expand group to see basic settings  
* sorted custom groups somewhat more intuitively (one hopes)  
* sorted localization strings by description    
* added css declarations for buttons etc when enabling ingredients for whole/halfs/quarters  
* some minor other css changes/additions  
* javascript check for custom groups to avoid having the same ingredient 2x in different custom groups for the same menu item  
* allow override of admin css (wppizza-addingredients-admin.css) by adding a wppizza-addingredients-admin-custom.css to theme directory  
- 13th September 2013  


2.1.1  
* added "How To" tab  
* stopped plugin annoyingly deactivating itself whenever the main plugin "WPPizza" gets updated/deactivated  
 - 6th July 2013  

2.0/2.1  
* added option for custom groups  
* split ingredients, groups and localization into tabs  
 - June/July 2013  

1.1  
* cart contents multisite aware (if checked in main plugin settings)  
* added touchstart to click events in js   
 - 10th May 2013  

1.0  
* Initial release - 17th March 2013  

== Frequently Asked Questions ==

= Can I edit the css ? =

although the css has been written so that it works with many themes out of the box (see www.wp-pizza.com - all demos/themes use the same default stylesheet) you might want to adjust some things here and there to work with your particular theme (especially if you want to support older browsers).  
if you do, copy the wppizza-addingredients/css/wppizza-addingredients.css to your theme directory (so it does not get overwritten by future updates of the plugin) and edit as required.  
alternatively - and possibly a better option - copy wppizza-addingredients-custom.css to your theme directory and only overwrite the items that need changing (in which case wppizza-addingredients.css will still be loaded before your customised wppizza-addingredients-custom.css)  

= additional info =

if you would like to use browser native radio and checkboxes (where appropriate) have a look at the very bottom of the wppizza-addingredients.css. (just uncomment the css declarations as indicated and edit as/if required)  
Please note, IE7 will always display these elements (via the conditional wppizza-addingredients-ie7.css) as it does not understand certain declarations used in the main stylesheet.  
(Same applies here. I.e you can copy wppizza-addingredients-ie7.css to your theme directory and edit it there if you which to keep changes for future updates)  
