<?php
/*
Plugin Name: WPPizza Delivery By Postcode
Description: Delivery By Post/Zip Code for WPPizza - Requires WPPIZZA 2.9.4+
Author: ollybach
Plugin URI: http://www.wp-pizza.com
Author URI: http://www.wp-pizza.com
Version: 2.1.3

Copyright (c) 2013, Oliver Bach
All rights reserved.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**if one needs to overwrite the below in another extension, just set a higher priority in the add action*/
add_action( 'plugins_loaded', 'wppizza_extend_dbp');
/*on uninstall, remove options from options table*/
register_uninstall_hook( __FILE__, 'wppizza_delivery_by_postcode_uninstall');

/***********************************************************************************
*
*
*	[frontend. every time the main plugin gets instanciated.
*	i.e shortcodes, shoppingcarts, orderpage etc etc]
*
*
************************************************************************************/
function wppizza_extend_dbp(){
if (!class_exists( 'WPPIZZA' )) {return;}

	/*******************************************************************
	*
	*	[version numbers]
	*
	********************************************************************/
	define('WPPIZZA_DBP_CURRENT_VERSION','2.1.3');
	/*******************************************************************
	*
	*	[EDD]
	*
	********************************************************************/
	define("WPPIZZA_DBP_EDD_NAME", "WPPizza – Delivery By Post/ZipCode" );/*checks if there is an update available to this plugin. only ever runs in admin. comment out to disable*/
	define('WPPIZZA_DBP_EDD_URL', 'https://www.wp-pizza.com' );



class WPPIZZA_EXTEND_DBP extends WPPIZZA{
      /************************************************************
      *
      *	[construct]
      *
      *************************************************************/
      function __construct() {
		parent::__construct();
		$this->dbpVersion = WPPIZZA_DBP_CURRENT_VERSION;
		$this->dbpClassName = 'WPPizza -  Delivery by Post/Zip Code';
		$this->dbpOptionsName = 'wppizza_dbp';
		$this->dbpOptions = get_option($this->dbpOptionsName,0);
		$this->dbpSession = $this->dbpOptionsName;
		$this->dbpLocale=$this->dbpOptionsName."-locale";
		$this->dbpSlug=$this->dbpOptionsName;
		$this->dbpNagNotice=1;

		/**text domain**/
		load_plugin_textdomain($this->dbpLocale, false, dirname(plugin_basename( __FILE__ ) ) . '/lang' );
		
		/**get/set session and overwrite delivery values***/
		if(!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)){
			$this->wppizza_dbp_init_session();
      		$this->wppizza_dbp_get_vars();
		}
      }

	/*******************************************************
		[start and set session - as we need the session in
		ajax calls (which access admin ajax php we need
		this available in front and backend]
	******************************************************/
	function wppizza_dbp_init_session() {
		if (!session_id()) {session_start();}
		/*initialize if not set*/
		if(!isset($_SESSION[$this->dbpSession]['dbp']) ){//|| !array_key_exists($_SESSION[$this->dbpSession]['dbp'], $this->dbpOptions['delivery_areas'])
			$_SESSION[$this->dbpSession]['dbp']='';
		}
	}
	/**********************************************************************************************
	*
	*	[set variables depending on postcode selected]
	*
	**********************************************************************************************/
	function wppizza_dbp_get_vars(){
		/*check that session has been set and is !=''*/
		if(isset($_SESSION[$this->dbpSession]['dbp']) && $_SESSION[$this->dbpSession]['dbp']>='0'){

			/*get values associated*/
			$dbpKey=$_SESSION[$this->dbpSession]['dbp'];
			if(isset($this->dbpOptions['delivery_areas'][$dbpKey])){
				$dbpType=$this->dbpOptions['delivery_areas'][$dbpKey]['type'];
				$dbpValue=$this->dbpOptions['delivery_areas'][$dbpKey]['charge'];
				$dbpValueChargeBelowFree=!empty($this->dbpOptions['delivery_areas'][$dbpKey]['charge_below_free']) ? $this->dbpOptions['delivery_areas'][$dbpKey]['charge_below_free'] : 0;
				$dbpMinOrderValue=!empty($this->dbpOptions['delivery_areas'][$dbpKey]['min_order_value']) ? $this->dbpOptions['delivery_areas'][$dbpKey]['min_order_value'] : $this->pluginOptions['order']['order_min_for_delivery'];
				$dbpEmail=$this->dbpOptions['delivery_areas'][$dbpKey]['email'];

				/*set delivery charge values depending on type*/
				if($dbpType=='minimum_total'){
					/*use main plugin value if empty*/
					if($dbpValue!=''){
						$this->pluginOptions['order']['delivery'][$dbpType]['min_total']=$dbpValue;
					}
					if($dbpValueChargeBelowFree!=''){
						$this->pluginOptions['order']['delivery'][$dbpType]['deliverycharges_below_total']=$dbpValueChargeBelowFree;
					}
				}
				if($dbpType=='standard'){
					/*use main plugin value if empty*/
					if($dbpValue!=''){
						$this->pluginOptions['order']['delivery'][$dbpType]['delivery_charge']=$dbpValue;
					}
				}
				if($dbpType=='per_item'){
					/*use main plugin value if empty*/
					if($dbpValue!=''){
						$this->pluginOptions['order']['delivery'][$dbpType]['delivery_charge_per_item']=$dbpValue;
					}
				}
				/**min order value**/
				$this->pluginOptions['order']['order_min_for_delivery']=$dbpMinOrderValue;
				
				/**set email if set**/
				if(is_array($dbpEmail) && count($dbpEmail)>0){
					$this->pluginOptions['order']['order_email_to']=$dbpEmail;
				}
			}
		}
	}
}}
/************************************************************************
*
*
*
*				[frontend once only]
*
*
*
*************************************************************************/
add_action( 'plugins_loaded', 'wppizza_dbp_actions_frontend');

function wppizza_dbp_actions_frontend(){
if (!class_exists( 'WPPIZZA' )) {return;}
class WPPIZZA_DBP_ACTIONS_FRONTEND extends WPPIZZA_EXTEND_DBP{
      /************************************************************
      *
      *	[construct]
      *
      *************************************************************/
      function __construct() {
		parent::__construct();

		/************************************************************************
				[frontend only]
		*************************************************************************/
		if(!is_admin()){
			add_action('wp_enqueue_scripts', array( $this, 'wppizza_dbp_register_scripts_and_styles'));

			/*add dropdown, autocomplete to order page**/
			if($this->dbpOptions['frontend_settings']['enabled']){
				if(!isset($this->dbpOptions['frontend_settings']['orderform_priority']) || $this->dbpOptions['frontend_settings']['orderform_priority']==''){
					add_action('wppizza_gateway_choice_before', array( $this, 'wppizza_dbp_output_label'));
				}else{
					add_action('wppizza_order_before_field_'.$this->dbpOptions['frontend_settings']['orderform_priority'].'', array( $this, 'wppizza_dbp_output_label'));
				}

			}
			/**thickbox if enabled**/
			if($this->dbpOptions['frontend_settings']['show_on_load']){
				add_action('template_redirect', array( $this, 'wppizza_dbp_thickbox'));
			}
			/**add shortcode to display dropdown/autocomplete*/
			add_shortcode($this->dbpSlug, array($this, 'wppizza_dbp_add_shortcode'));
		}

		/************************************************************************
			[regular ajax]
		*************************************************************************/
		/*set post/zipcode session */
		add_action('wp_ajax_wppizza_dbp_json', array(&$this,'wppizza_dbp_json') );// non logged in users
		add_action('wp_ajax_nopriv_wppizza_dbp_json', array(&$this,'wppizza_dbp_json') );
      }

	/*******************************************************************
		[get all valid areas depending on master wppizza order settings]
	******************************************************************/
	function wppizza_dbp_valid_areas(){
		$delivery_areas=$this->dbpOptions['delivery_areas'];
		$validAreas=array();
		foreach($delivery_areas as $k=>$v){
			/*check if slection is conforming to overall delivery charges order settings as well and is enabled*/
			if($v['type']==$this->pluginOptions['order']['delivery_selected'] && $v['enabled']){
					$validAreas[$k]=$v;
			}
		}
		return $validAreas;
	}

	/***********************************************************************************************
	*
	* 	[shortcode functions]
	*	[ensure shortcodes are enabled - DOH]
	*	[to use shortcodes in text widgets add  "add_filter('widget_text','do_shortcode')" to theme function file
	*	or use any suitable plugin]
	*
	************************************************************************************************/
	public function wppizza_dbp_add_shortcode($atts){
		$markup=$this->wppizza_dbp_user_output(true);
		return $markup;
		die();//needed !!!
	}

	/**********************************
		[register scripts/styles]
	***********************************/
	function wppizza_dbp_register_scripts_and_styles() {
		/**get paths/subdir**/
		$dir=get_stylesheet_directory();
		$uri=get_stylesheet_directory_uri();
		if(is_dir($dir.'/'.WPPIZZA_SLUG)){
			$stylesheet_dir=$dir.'/'.WPPIZZA_SLUG;
			$stylesheet_uri=$uri.'/'.WPPIZZA_SLUG;
		}else{
			$stylesheet_dir=$dir;
			$stylesheet_uri=$uri;
		}		
		
		/**css*/
		if (file_exists( $stylesheet_dir . '/wppizza-dbp.css')){
			/**copy stylesheet to theme directory to keep settings**/
			wp_register_style($this->dbpSlug, $stylesheet_uri.'/wppizza-dbp.css', array(), $this->dbpVersion);
		}else{
			wp_register_style($this->dbpSlug, plugins_url( 'css/wppizza-dbp.css', __FILE__ ), array(), $this->dbpVersion);
		}
		wp_enqueue_style($this->dbpSlug);

		/**js***/
		/*autocomplete**/
		if($this->dbpOptions['frontend_settings']['instant_search']){
			wp_register_script($this->dbpSlug.'-autocomplete', plugins_url( 'js/jquery.smart_autocomplete.js', __FILE__ ), array($this->pluginSlug), $this->dbpVersion ,$this->pluginOptions['plugin_data']['js_in_footer']);
			wp_enqueue_script($this->dbpSlug.'-autocomplete');
		}
		/*regular js*/
		wp_register_script($this->dbpSlug, plugins_url( 'js/scripts.min.js', __FILE__ ), array($this->pluginSlug), $this->dbpVersion ,$this->pluginOptions['plugin_data']['js_in_footer']);
		wp_enqueue_script($this->dbpSlug);
	}
	/**************************************
     [ regular frontend ajax call]
    ***************************************/
	function wppizza_dbp_json(){
		require('ajax/get-json.php');
		die();
	}

	/*******************************************************************
		[output as label and select/input]
	******************************************************************/
	function wppizza_dbp_output_label(){
		$markup=$this->wppizza_dbp_user_output();
		echo $markup;
	}
	/*******************************************************************
		[output as fieldset with legend]
	******************************************************************/
	function wppizza_dbp_output_fieldset(){
		$markup=$this->wppizza_dbp_user_output(true);
		echo $markup;
	}
	/*******************************************************
		[output as dropdown or autocomplete fields ]
	******************************************************/
	function wppizza_dbp_user_output($fieldset=false){
		$str='';

		/*make sure its enabled*/
		if($this->dbpOptions['frontend_settings']['enabled']){
			$validAreas=$this->wppizza_dbp_valid_areas();

			if(count($validAreas)>0){
				/*required or not */
				$req=!empty($this->dbpOptions['frontend_settings']['required']) ? 'required' : '';				
				$reqClass=!empty($this->dbpOptions['frontend_settings']['required']) ? ' class="wppizza-order-label-required" ' : '';
				
				/**only required if not self pickup**/
				if(!empty($this->dbpOptions['frontend_settings']['required']) && !empty($this->dbpOptions['frontend_settings']['no_required_onpickup'])){
					$req=!empty($_SESSION[$this->pluginSession]['selfPickup']) ? '' : 'required';
					$reqClass=!empty($_SESSION[$this->pluginSession]['selfPickup']) ? '' : ' class="wppizza-order-label-required" ';
				}
				

				$selAreaId=$_SESSION[$this->dbpSession]['dbp'];
				$selAreaLbl=!empty($validAreas[$selAreaId]['label']) ? $validAreas[$selAreaId]['label'] : '';


				/*add fieldset if not right after customer details*/
				if($this->dbpOptions['frontend_settings']['instant_search']){
					$legendlabel=$this->dbpOptions['localization']['label_instant']['lbl'];
				}else{
					$legendlabel=$this->dbpOptions['localization']['label']['lbl'];
				}


				if($fieldset){
					$str.='<fieldset class="wppizza-dbp-areas">';
					$str.='<legend'.$reqClass.'>'.$legendlabel.'</legend>';
				}else{
					$str.='<label for="wppizza-dbp-area"'.$reqClass.'>'.$legendlabel.'</label>';
				}

					/*variables to post to send order**/
					$str.='<input type="hidden" id="wppizza-dbp-post-label" name="wppizza-dbp-post[label]" value="'.$this->dbpOptions['localization']['dbp_generic']['lbl'].'" />';

					/**autocomplete search or dropdown ?**/
					if($this->dbpOptions['frontend_settings']['instant_search']){
						$str.='<input id="wppizza-dbp-area" type="hidden" value="'.$selAreaId.'" />';
						$str.='<input id="wppizza-dbp-area-is" '.$req.' autocomplete="off" name="wppizza-dbp-post[value]" value="'.$selAreaLbl.'" placeholder="'.$this->dbpOptions['localization']['instant_search_placeholder']['lbl'].'" />';
						$str.='<ul class="wppizza-dbp-ac"></ul>';
					}else{
						$str.='<select id="wppizza-dbp-area" class="wppizza-dbp-area" '.$req.' >';
							$str.='<option value="" '.selected($selAreaId,'',false).'>'.$this->dbpOptions['localization']['select']['lbl'].'</option>';
							foreach($validAreas as $k=>$v){
								$str.='<option value="'.$k.'" '.selected($selAreaId,$k,false).'>'.$v['label'].'</option>';
							}
						$str.='</select>';

						foreach($validAreas as $k=>$v){
							if($selAreaId==$k){
							$str.='<input type="hidden" id="wppizza-dbp-post-value" name="wppizza-dbp-post[value]" value="'.$v['label'].'" />';
							}
						}
					}
				if($fieldset){
				$str.='</fieldset>';
				}
			}
		}
		$str.='';
		return $str;
	}

	/*******************************************************
		[popup thickbox add scripts if enabled]
	******************************************************/
	function wppizza_dbp_thickbox(){
		$this->wppizza_dbp_require_thickbox();
		if(isset($this->requireThickbox)){
			add_thickbox();
			add_action('wp_footer', array($this,'wppizza_dbp_thickbox_container'));
		}
	}

	/*******************************************************
		[popup thickbox - check which pages to set scripts]
	******************************************************/
	function wppizza_dbp_require_thickbox(){
		global $post;

		/**check if wea ctually have anything to display**/
		$areas=$this->wppizza_dbp_valid_areas();
		
		
		$isOpen=1;
		/**check if we are open if enabled*/
		if($this->dbpOptions['frontend_settings']['dont_show_on_load_if_closed']){
			$isOpen=wpizza_are_we_open($this->pluginOptions['opening_times_standard'],$this->pluginOptions['opening_times_custom'],$this->pluginOptions['times_closed_standard']);
		}
		if(count($areas)>0 && $isOpen==1){
			/**show on all pages regardless of posttype, shortcode**/
			if($this->dbpOptions['frontend_settings']['show_on_load_global']){
				$this->requireThickbox=true;
			}else{
				if(isset($post->ID)){
				$postType=get_post_type( $post->ID );
				/**check normal pages/posts for shortcode*/
				if($postType!=WPPIZZA_POST_TYPE){
					$shortcode = '['.WPPIZZA_POST_TYPE.' ';
					$content = $post->post_content;
					$check = strpos($content,$shortcode);
					if($check!==false) {
						$this->requireThickbox=true;
					}
				}}
				if(isset($postType) && $postType!=WPPIZZA_POST_TYPE){/*is wppizza posttype*/
					$this->requireThickbox=true;
				}
			}
		}
	}

	/*******************************************************
		[add the thickbox container to the page if required]
	******************************************************/
	function wppizza_dbp_thickbox_container(){
		echo"<div id='wppizza-dbp-thickbox' style='display:none;'><div>";
		echo"".$this->wppizza_dbp_user_output(true);
		echo"</div></div>";
	}
}
$WPPIZZA_DBP_ACTIONS_FRONTEND=new WPPIZZA_DBP_ACTIONS_FRONTEND();
}


/************************************************************************
*
*
*
*				[admin only]
*
*
*
*************************************************************************/
if(is_admin()){
	add_action( 'init', 'wppizza_dbp_backend');
}
function wppizza_dbp_backend(){
	if (!class_exists( 'WPPizza' ) ) {return;}
	/********************************************************************************************
	*
	*
	*	[WPPIZZA DBP ACTIONS]
	*
	*
	*******************************************************************************************/
	class WPPIZZA_DBP_ACTIONS_ADMIN extends WPPIZZA_EXTEND_DBP {
		/***********************************************
		*
		*	[constructor]
		*
		**********************************************/
		function __construct() {
			parent::__construct();

			add_action('plugins_loaded', array( $this, 'wppizza_dbp_load_classes'));/*load classes**/

				if(	version_compare( $this->pluginOptions['plugin_data']['version'], '2.8', '<' )){/*make sure wppizza is>=2.8*/
					add_action('admin_notices', array( $this, 'wppizza_dbp_req_notice') );
				}
				add_action('admin_init', array( $this, 'wppizza_dbp_init'));/*if necessary, add the db option table and fill with defaults**/
				add_action('admin_enqueue_scripts', array( $this, 'wppizza_dbp_register_scripts_and_styles_admin'));
				add_action('admin_menu', array( $this, 'wppizza_dbp_register_menu_and_settings' ) );

				/**set capability to update options**/
				add_filter( 'option_page_capability_'.$this->dbpSlug.'', array($this, 'wppizza_dbp_admin_option_page_capability' ));
			
			/**add licensing things if defined**/
			if(defined('WPPIZZA_DBP_EDD_NAME')){
				add_action('admin_init', array( $this, 'wppizza_dbp_edd'),2);
			}


			/************************************************************************
				[ajax]
			*************************************************************************/
			/*admin only*/
			add_action('wp_ajax_wppizza_dbp_admin_json', array(&$this,'wppizza_dbp_admin_json') );
		}

		/******************************************************
		*
		*	[load classes]
		*
		******************************************************/
		function wppizza_dbp_load_classes() {
			/**edd**/
			if(defined('WPPIZZA_DBP_EDD_NAME')){
				require_once(WPPIZZA_PATH.'classes/wppizza.edd.inc.php');
				$this->wppdbpEdd=new WPPIZZA_EDD_SL();
			}
			/**user caps***/
			require_once(WPPIZZA_PATH.'classes/wppizza.user.caps.inc.php');
			$this->wppdbpUserCaps=new WPPIZZA_USER_CAPS();
			///*get caps of current user*/
			$this->wppdbpCurrentUserCaps=$this->wppdbpUserCaps->current_user_caps($this->wppizza_dbp_caps());
		}

		/******************************************************
		*
		*	[set caps]
		*
		******************************************************/
		function wppizza_dbp_caps() {
			$caps=array();
			$caps['deliveries']=array('name'=>__('Delivery Options',$this->dbpLocale),'cap'=>'wppizza_cap_wppdbp_deliveries'); 
			$caps['frontend-settings']=array('name'=>__('Frontend Settings',$this->dbpLocale),'cap'=>'wppizza_cap_wppdbp_frontend_settings');
			$caps['localization']=array('name'=>__('Localization',$this->dbpLocale),'cap'=>'wppizza_cap_wppdbp_localization');
			$caps['access']=array('name'=>__('Access',$this->dbpLocale),'cap'=>'wppizza_cap_wppdbp_access');
			if(defined('WPPIZZA_DBP_EDD_NAME')){
				$caps['license']=array('name'=>__('License',$this->dbpLocale),'cap'=>'wppizza_cap_wppdbp_license');
			}
			return $caps;
		}

		/**************************************************************************************************
		*
		*	[EDD: allow updates to be delivered automatically]
		*	[EDD TO ENABLE AUTOMATIC UPDATES NOTFICATIONS IN WP DASHBOARD.]
		*
		**************************************************************************************************/
			function wppizza_dbp_edd(){
				/*include class*/
				if( !class_exists( 'WPPIZZA_EDD_SL_PLUGIN_UPDATER' ) ) {
					require_once(WPPIZZA_PATH.'classes/wppizza.edd.plugin.updater.inc.php');
				}
				/*retrieve our license key from the DB*/
				$license_key=empty($this->dbpOptions['plugin_data']['license']['key']) ? '' : $this->dbpOptions['plugin_data']['license']['key'];
				/* setup the updater */
				$edd_updater = new WPPIZZA_EDD_SL_PLUGIN_UPDATER( WPPIZZA_DBP_EDD_URL, __FILE__, array(
					'version'		=> WPPIZZA_DBP_CURRENT_VERSION, 		// current version number
					'license'		=> $license_key, 	// license key (used get_option above to retrieve from DB)
					'item_name'		=> WPPIZZA_DBP_EDD_NAME, 	// name of this plugin
					'author'		=> 'ollybach'  // author of this plugin
					)
				);
			}

		/******************************************************
		*
		*	[check requirements]
		*
		******************************************************/
		function wppizza_dbp_req_notice() {
			$dbpReqNotice='';
			$dbpReqNotice.='<div id="message" class="error wppizza_admin_notice" style="padding:20px;">';
				$dbpReqNotice.='<strong>WPPizza Delivery By Postcode requires WPPizza Version 2.8+ to work. Please update WPPizza ! </strong>';
				$dbpReqNotice.='<br/><br/> This notice will disappear as soon as you have updated';
				$dbpReqNotice.='<br/> Tank you';
			$dbpReqNotice.='</div>';
			echo"".$dbpReqNotice."";
		}

		/******************************************************
		*
		*	[insert options and defaults on first install]
		*
		******************************************************/
		function wppizza_dbp_init(){
			//$force=1;/*development only*/
			if($this->dbpOptions==0 || isset($force)){
				/**include and insert default options***/
				require('inc/admin.setup.default.options.inc.php');
				update_option($this->dbpOptionsName, $defaultOptions );
			}
			/**update version**/
			if($this->dbpOptions!=0 && version_compare( $this->dbpVersion, $this->dbpOptions['plugin_data']['version'], '>' )){
				
				$setOptions=$this->dbpOptions;
				/**include default options and keep or amend.***/
				$pluginUpdate=1;
				require('inc/admin.setup.default.options.inc.php');
				
				/*distinctly set version*/
				$defaultOptions['plugin_data']['version'] = $this->dbpVersion;
				update_option($this->dbpOptionsName, $defaultOptions );
			}
		}
		/*****************************************************
		*
		*	[Register and Enqueue scripts and styles]
		*
		******************************************************/
	    function wppizza_dbp_register_scripts_and_styles_admin() {
	    		/**css*/
	    		wp_register_style($this->dbpSlug, plugins_url( 'css/wppizza-dbp-admin.css', __FILE__ ), array(), $this->dbpVersion);
	    		wp_enqueue_style($this->dbpSlug);
	      		/**js***/
	            wp_register_script($this->dbpSlug, plugins_url( 'js/scripts.admin.js', __FILE__ ), array(WPPIZZA_SLUG), $this->dbpVersion ,true);
	            wp_enqueue_script($this->dbpSlug);
	    }
		/*****************************************************
		*
		*	[Register menu and Settings]
		*
		******************************************************/
		function wppizza_dbp_register_menu_and_settings() {
			global $pagenow;
			$this->wppizza_dbp_load_classes();
			// Check if user can access to the plugin
			if(defined('WPPIZZA_SLUG') &&  count($this->wppdbpCurrentUserCaps)>0){				
				if (isset ($_GET['tab']) && isset($_GET['page']) && $_GET['page']==$this->dbpSlug){$tab = $_GET['tab'];}else{$tab = $this->wppdbpCurrentUserCaps['tabs'][0];}
				$allTabs=$this->wppizza_dbp_caps();
				$requiredCap=$allTabs[$tab]['cap'];
				if(current_user_can($requiredCap)){
					require_once('inc/admin.echo.register.submenu.pages.inc.php');
				}
			}
		}
	
		function wppizza_dbp_manage_deliveries(){
			$this->wppizza_dbp_admin_tabs();
			$firstAllowedTab=$this->wppdbpCurrentUserCaps['tabs'][0];
			$current = !empty($_GET['tab']) ?  $_GET['tab'] : $firstAllowedTab;
			require_once('inc/admin.echo.manage_deliveries.inc.php');
		}
		function wppizza_dbp_admin_page_text_header($v) {
			require_once('inc/admin.echo.settings.text.header.inc.php');
		}
		function wppizza_dbp_admin_settings_input($field='') {
			global $pagenow;
			$options=$this->dbpOptions;
			if ( $pagenow == 'edit.php' && $_GET['page'] == $this->dbpSlug ){
			if (isset ($_GET['tab'])){$tab = $_GET['tab'];}else{$tab = $this->wppdbpCurrentUserCaps['tabs'][0];}
				require('inc/admin.echo.settings.input.fields.inc.php');
			}			
		}
		private function wppizza_dbp_admin_get_delivery_areas($field,$k,$v,$options){
			require('inc/admin.echo.get_delivery_areas.inc.php');
			return $str;
		}
		/*********************************************************
			[Admin TABS]
		*********************************************************/
		function wppizza_dbp_admin_tabs() {
		    //$tabs = array('deliveries' => 'Delivery Options', 'frontend-settings' => 'Frontend Settings', 'localization' => 'Localization', 'access' => 'Access', 'license' => 'License'  );
		    $tabs = $this->wppizza_dbp_caps();
		    $current = !empty($_GET['tab']) ?  $_GET['tab'] : $this->wppdbpCurrentUserCaps['tabs'][0];
		    echo '<div id="icon-themes" class="icon32"><br></div>';
		    echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $arr ){
				if(in_array($tab,$this->wppdbpCurrentUserCaps['tabs'])){
					$class = ( $tab == $current ) ? ' nav-tab-active' : '';
						echo "<a class='nav-tab".$class."' href='?post_type=".WPPIZZA_POST_TYPE."&page=".$this->dbpSlug."&tab=".$tab."'>".$arr['name']."</a>";
				}
			}
		    echo '</h2>';
		}
		/*********************************************************
		*
		*		[admin options validation]
		*
		*********************************************************/
	    public function wppizza_dbp_admin_options_validate($input){
			/*new install , use input*/
			if($this->dbpOptions==0){
				$newOptions=$input;
			}

			/*update, keep old settings */
	    	if($this->dbpOptions!=0){
	    		/**just updating the plugin, will be overwritten when posting*/
	    		$newOptions=$input;
				/*do not use require_once here as it may be used more than once .doh!**/
				require('inc/admin.options.validate.inc.php');
	    	}
			return $newOptions;
	    }

		/**set capability to  save options**/
    	function wppizza_dbp_admin_option_page_capability( $capability ) {
			return $this->wppdbpCurrentUserCaps['caps'][0];
		}

		/******************
		    [admin ajax call]
		 *******************/
		function wppizza_dbp_admin_json(){
			require('ajax/admin-get-json.php');
			die();
		}
	}
$WPPIZZA_DBP_ACTIONS_ADMIN=new WPPIZZA_DBP_ACTIONS_ADMIN();
}
?>