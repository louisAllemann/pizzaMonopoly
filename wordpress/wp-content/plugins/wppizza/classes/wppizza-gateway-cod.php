<?php
if (!class_exists( 'WPPizza' ) ) {return ;}
	/**gateway classes MUST start with WPPIZZA_GATEWAY_ to be recognised by parent plugin as gateway class**/
	class WPPIZZA_GATEWAY_COD extends WPPIZZA_GATEWAYS {

		function __construct() {
			/**get vars from parent**/
			//parent::__construct();/*not used/necessary*/
			$this->gatewayName = __('Cash on Delivery',WPPIZZA_LOCALE);/*required gateway name*/
			$this->gatewayDescription = '';/*required variable (although it can be empty)- additional description of gateway displayed in ADMIN area*/
			$this->gatewayAdditionalInfo = '';/* required variable (although it can be empty) default printed under gateway options FRONTEND - can be changed/localized/emptied in admin */
			$this->gatewayOptionsName = strtolower(get_class());/*required - name of option in option table*/
			$this->gatewayOptions = get_option($this->gatewayOptionsName,0);/**required**/
		}
		/**settings of gateway variables. required function, but can return empty array**/
		function gateway_settings($optionsOnly=false) {
				$gatewaySettings=array();
			return $gatewaySettings;
		}
	}
?>