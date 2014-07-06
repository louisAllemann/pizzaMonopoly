=== WPPizza Timed Menu===
Contributors: ollybach
Donate link: http://www.wp-pizza.com/
Author URI: http://www.wp-pizza.com
Plugin URI: http://www.wp-pizza.com
Tags: wppizza
Requires at least: WPPIZZA 2.8.4, WP 3.5.1 
Tested up to: 3.9.1
Stable tag: 1.2


An extension for WPPizza  to set times and dates when your menu items are available - Requires WPPIZZA 2.8.4+

== Description ==

Allows the administrator to set times, days and dates when selected menu items or categories are available to be ordered by the customer


== Changelog ==

1.2  
* eliminating some edd check server redirection overheads by using https (as the wp-pizza.com is ssl encrypted) 
* incresed main menu dropdown z-index when showing items as unavailable to cover divs of unavailable items  
* BUGFIX: using several settings for the same menu item/category/page resulted in hidden/non-available settings having precedence when it should have been the other way round   
* BUGFIX: plugin interfered with the ability to sort wppizza categories by drag and drop   
* ADDED: when selecting to show/hide a page without specifically selecting one or more menu items of this page, all associated items will automatically be shown/hidden as appropriate  
5th June 2014  

1.1.2  
* replaced mysql_real_escape_string with esc_sql (for updates to WP 3.9 and PHP 5.5+)  
17th April 2014  
  
1.1.1  
* FIX: previous versions only ever looked in parent theme for customised css even when using a child theme. Therefore, IF using a child theme, make sure you copy any customised css there. (Installations not using a childtheme are unaffected)     
31st March 2014  

1.1   
* Bugfix: erroneously compared start_date with start_date (i.e itself) instead of end_date when both dates are set  
3rd February 2014   

1.0   
* initial release  
29th January 2014   