<?php
/*
Plugin Name: WPPizza Add Ingredients
Description: Extends WPPizza to allow adding of additional ingredients to any given menu item by the customer. Requires WPPIZZA 2.6.5+
Author: ollybach
Plugin URI: http://www.wp-pizza.com/
Author URI: http://www.wp-pizza.com/
Version: 4.3.4.3
*/

/**set the following as  constants so we can use it throughout*/
define('WPPIZZA_ADDINGREDIENTS_CURRENT_VERSION', '4.3.4.3' );
define('WPPIZZA_ADDINGREDIENTS_NAME', 'WPPizza Add Ingredients');
define('WPPIZZA_ADDINGREDIENTS_SLUG', 'wppizza_addingredients');
define('WPPIZZA_ADDINGREDIENTS_LOCALE', ''.WPPIZZA_ADDINGREDIENTS_SLUG.'-locale');
define('WPPIZZA_ADDINGREDIENTS_PATH', plugin_dir_path(__FILE__) );
define('WPPIZZA_ADDINGREDIENTS_URL', plugin_dir_url(__FILE__) );
/**EDD**/
define("WPPIZZA_ADDINGREDIENTS_EDD_NAME", "WPPizza – Add Ingredients Extension" );/*checks if there is an update available to this plugin. runs in admin only . comment out to disable*/
define('WPPIZZA_ADDINGREDIENTS_EDD_URL', 'https://www.wp-pizza.com' );
/**legacy**/
define('WPPIZZA_REQUIRED_PATH', 'wppizza/wppizza.php');/*legacy*/


/*on uninstall, remove ingredients from master option table and delete this option*/
register_uninstall_hook( __FILE__, 'wppizza_addingredients_uninstall' );

global $wppizza_add_ingredients;

if ( ! class_exists( 'WPPizza_Add_Ingredients' ) ) {
class WPPizza_Add_Ingredients extends WP_Widget {
	private $pluginVersion;
	private $pluginName;
	private $pluginSlug;
	private $pluginLocale;
	private $pluginOptions;
	private $pluginOptionsNoWpml;
	private $pluginMetaValue;
	private $pluginMetaMulti;
	private $pluginMetaMultiDivide;
	private $pluginNagNotice;
	private $pluginSession;
	private $masterOptions;

	/********************************************************
	*
	*
    *	[Constructor]
	*
	*
	********************************************************/
     function __construct() {
		/**init constants***/
		$this->pluginVersion="".WPPIZZA_ADDINGREDIENTS_CURRENT_VERSION."";//increment in line with stable tag in readme and version above
	 	$this->pluginName="".WPPIZZA_ADDINGREDIENTS_NAME."";
	 	$this->pluginSlug="".WPPIZZA_ADDINGREDIENTS_SLUG."";//set also in uninstall when deleting options
		$this->pluginLocale="".WPPIZZA_ADDINGREDIENTS_LOCALE."";
		$this->pluginOptions = get_option($this->pluginSlug,0);
		$this->pluginOptionsNoWpml = $this->pluginOptions;
		$this->pluginMetaValue = 'add_ingredients';
		$this->pluginMetaMulti = 'add_ingredients_multi';
		$this->pluginMetaMultiDivide = 'add_ingredients_multi_divide';
		/**to get the template paths, uri's and possible subdir and set vars accordingly**/
		$pathDirUri=$this->wppizza_add_ingredients_template_paths();
		$this->pluginAiTemplateDir=$pathDirUri['template_dir'];/**to amend get_stylesheet_directory() according to whether wppizza subdir exists*/
		$this->pluginAiTemplateUri=$pathDirUri['template_uri'];/**to amend get_stylesheet_directory_uri() according to whether wppizza subdir exists*/

		/*only required in backend*/
		if(is_admin()){
			$this->pluginAccessCapabilities = $this->wppizza_ingredients_capabilities_tabs(true);
		}
		$this->pluginNagNotice=1;
		if(defined('WPPIZZA_SLUG')){
			$this->masterOptions = get_option(''.WPPIZZA_SLUG.'',0);//get options of master wppizza plugin
			/**make cart contents multisite aware*/
			if(is_multisite() && $this->masterOptions['plugin_data']['wp_multisite_session_per_site']){
				global $blog_id;
				$this->pluginSession=WPPIZZA_SLUG.''.$blog_id;
			}else{
				$this->pluginSession=WPPIZZA_SLUG;
			}
		}
    	//classname and description
        $widget_opts = array (
            'classname' => $this->pluginSlug,
            'description' => __('Extends WPPizza to allow adding of additional ingredients to any given menu item by the customer. Requires WPPizza 2.6.5+', $this->pluginLocale)
        );

        $this->WP_Widget(false, $name=$this->pluginName, $widget_opts);
        load_plugin_textdomain($this->pluginLocale, false, dirname(plugin_basename( __FILE__ ) ) . '/lang' );


		/************************************************************************
			[runs only in frontend]
		*************************************************************************/
		if(!is_admin()){
			/**add sessions to keep track of the ingredients we've added***/
			add_action('init', array(&$this,'wppizza_init_sessions'));
			/***enqueue frontend scripts and styles***/
			add_action('wp_enqueue_scripts', array( $this, 'wppizza_register_scripts_and_styles'));
			/*******change loop to reflect prices for preselected ingredients **************/
			add_filter('wppizza_filter_loop_meta', array( $this, 'wppizza_ingredients_filter_loop_meta'),10,2);
			/******add js functions to run after cart refresh**********************************/
			add_filter('wppizza_filter_js_cart_refresh_functions', array( $this, 'wppizza_ingredients_filter_js_cart_refresh_functions'),10,1);
			/******messages to main plugin localized js**********************************/
			add_filter('wppizza_filter_js_extend', array( $this, 'wppizza_ingredients_filter_js_extend'),10,1);

			/**thickbox if enabled**/
			if($this->pluginOptions['options']['ingredients_in_popup']){
				add_action('template_redirect', array( $this, 'wppizza_ingredients_thickbox'));
			}


		}
		/************************************************************************
			[runs only in  backend]
		*************************************************************************/
		if(is_admin()){
			/**check requirements*/
			add_action('admin_init', array( $this, 'wppizza_addingredients_check_plugin_requirements'));/*check if we have the relevant php version etc**/
			//add_action('admin_init', array( $this, 'wppizza_addingredients_requires_wppizza' ));
			add_action('admin_init', array( $this, 'wppizza_addingredients_admin_options_init'));/*if necessary, add the db option table and fill with defaults**/
	   		add_action('admin_menu', array( $this, 'wppizza_addingredients_register_menu_and_settings' ) );

    		add_action('admin_init', array( $this, 'wppizza_admin_metaboxes') );
			/***enqueue backend scripts and styles***/
			add_action('admin_enqueue_scripts', array( $this, 'wppizza_register_scripts_and_styles_admin'));
			/*when saving custom post*/
			add_action('save_post', array( $this, 'wppizza_admin_save_metaboxes'), 10, 2 );

    		/**make sure post id's are also deleted from custom groups when a post is deleted*/
    		add_action( 'delete_post', array( $this, 'wppizza_ingredients_delete_post') );

    		/**set editable roles for given user**/
    		add_action( 'editable_roles' , array( $this, 'wppizza_ingredients_set_editable_roles' ));

    		/**dismiss admin notice via ajax call**/
    		add_action('wp_ajax_wppizza_add_ingredients_dismiss_notice', array($this, 'wppizza_add_ingredients_dismiss_notice'));

			/**set capability to update options**/
			add_filter( 'option_page_capability_'.$this->pluginSlug.'', array($this, 'wppizza_add_ingredients_admin_option_page_capability' ));

			if(defined('WPPIZZA_ADDINGREDIENTS_EDD_NAME')){
				add_action('admin_init', array( $this, 'wppizza_addingredients_edd'));
			}

		}
    	/**make user defined localizations strings wpml compatible*/
    	add_action('init', array( $this, 'wppizza_add_ingredients_wpml_localization'),12);

    	/**filter additional ingredients output***/
    	add_filter('wppizza_filter_order_summary', array( $this, 'wppizza_ingredients_filter_additionalinfo'),20,1);/*item array orderpage and cart*/
    	add_filter('wppizza_filter_order_extend', array( $this, 'wppizza_filter_ingredients_order_extend'),20,1);/*item array orderpage and cart*/

		/************************************************************************
			[ajax]
		*************************************************************************/
		add_action('wp_ajax_wppizza_admin_ingredients_json', array(&$this,'wppizza_admin_ingredients_json') );
		add_action('wp_ajax_wppizza_ingredients_json', array(&$this,'wppizza_ingredients_json') );// non logged in users
		add_action('wp_ajax_nopriv_wppizza_ingredients_json', array(&$this,'wppizza_ingredients_json') );

    }

/***********************************************************************************************
*
*
*	[check requirements, initialize options, metaboxes etc]
*
*
***********************************************************************************************/
	/********************************************************
		[PHP 5.2 (json_decode) required ,
		so if PHP version is lower then 5.2,
		display an error message and deactivate the plugin]
	********************************************************/
	public function wppizza_addingredients_check_plugin_requirements(){
		if( version_compare( PHP_VERSION, '5.2', '<' )) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
			deactivate_plugins( basename( __FILE__ ) );
			wp_die( __('<strong>"WPPizza Add Ingredients"</strong> requires the server on which your site resides to be running PHP 5.2 or higher. As of version 3.2, WordPress itself will also <a href="http://wordpress.org/news/2010/07/eol-for-php4-and-mysql4">have this requirement</a>. You should get in touch with your web hosting provider and ask them to update PHP.<br /><br /><a href="' . admin_url( 'plugins.php' ) . '">Back to Plugins</a>', $this->pluginLocale) );
		}

		/**show nagscreen if set to 1**/
		if($this->pluginOptions['plugin_data']['nag_notice']==1){
			add_action('admin_notices', array( $this, 'wppizza_add_ingredients_install_notice') );
			add_action('admin_head', array($this, 'wppizza_add_ingredients_dismiss_notice_js') );
		}
	}

	/**************************************************************************
		[insert options and defaults on first install]
	**************************************************************************/
	public function wppizza_addingredients_admin_options_init(){
		$options = $this->pluginOptionsNoWpml;
		if($options==0){/*no options db entry->do stuff*/
			/**include and insert default options***/
			require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.setup.default.options.inc.php');
			add_option($this->pluginSlug, $defaultOptionsThisPlugin );
		}else{
			/*update plugin version*/
			/****************************************************************************************
				@forceUpdate
				[in case we want  to force update without upgrading version, uncomment below
				 - DEVELOPMENT PURPOSES ONLY when adding/deleting default options.
				 @resetCaps
				 [in case the caps got all screwed up we can reset them to their defaults]
			**************************************************************************************/
			//$forceUpdate=1;
			//$resetCaps=1;
			/**update  options if installed version < current version***/
			if( version_compare( $options['plugin_data']['version'], 	$this->pluginVersion, '<' ) || isset($forceUpdate) || isset($resetCaps)) {
				$options['plugin_data']['version']=$this->pluginVersion;/*update version no*/

				/**get default options***/
				require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.setup.default.options.inc.php');

				/**compare table options against default options and delete/add as required***/
				require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.update.options.inc.php');

				/*update*/
				update_option($this->pluginSlug, $options );
			}
		}
	}
	/************************************************************************************
		[admin notices: show and dismiss]
	************************************************************************************/
	/* plugin install notice*/
    function wppizza_add_ingredients_install_notice() {
			$pluginUpdatedNotice='';
			$pluginUpdatedNotice.='<div id="message" class="updated wppizza_add_ingredients_admin_notice" style="padding:20px;">';
			$pluginUpdatedNotice.='<b>'.$this->pluginName.' Installed</b><br/><br/>';
			$pluginUpdatedNotice.='Please go to <b>'.WPPIZZA_NAME.'->settings->ingredients</b> to add/edit the ingredients and their corresponding prices you wish your customers to be able to add to a menu item.';
			$pluginUpdatedNotice.='<br/>When you have finished, you can choose "Allow to add ingredients" for any menu item you wish, provided that you have enabled 1 or more ingredients for the current pricetier of the item';
			$pluginUpdatedNotice.='<br/>';
			$pluginUpdatedNotice.='<br/><a href="#" onclick="wppizza_add_ingredients_dismiss_notice(); return false;" class="button-primary">dismiss</a>';
			$pluginUpdatedNotice.='</div>';
			$pluginUpdatedNotice=__($pluginUpdatedNotice, $this->pluginLocale);
			print"".$pluginUpdatedNotice."";
    }
    function wppizza_add_ingredients_dismiss_notice_js () {
        $js="";
        $js.="<script type='text/javascript' >".PHP_EOL."";
        $js.="jQuery(document).ready(function($) {".PHP_EOL."";
            $js.="wppizza_add_ingredients_dismiss_notice = function () {".PHP_EOL."";
	        	$js.="var data = {action: 'wppizza_add_ingredients_dismiss_notice'};".PHP_EOL."";
	        	// since wp2.8 ajaxurl is defined in admin header pointing to admin-ajax.php
	        	$js.="jQuery.post(ajaxurl, data, function(response) {".PHP_EOL."";
			        $js.="$('.wppizza_add_ingredients_admin_notice').hide('slow');".PHP_EOL."";
	        	$js.="});".PHP_EOL."";
	        $js.="};".PHP_EOL."";
        $js.="});".PHP_EOL."";
        $js.="</script>".PHP_EOL."";
        print"".$js;
    }
    public function wppizza_add_ingredients_dismiss_notice () {
    	$options = $this->pluginOptionsNoWpml;
    	$options['plugin_data']['nag_notice']=0;
    	update_option($this->pluginSlug,$options);
        die();
    }
/***********************************************************************************************
*
*
*	[Register and Enqueue scripts and styles]
*
*
************************************************************************************************/
	/****************************************************************
	*
	*	[get/set Template Directories/Uri's. also check for subdir 'wppizza']
	*
	***************************************************************/
	function wppizza_add_ingredients_template_paths(){
		$paths['template_dir']='';
		$paths['template_uri']='';
		$dir=get_stylesheet_directory();
		$uri=get_stylesheet_directory_uri();

		if(is_dir($dir.'/'.WPPIZZA_SLUG)){
			$paths['template_dir']=$dir.'/'.WPPIZZA_SLUG;
			$paths['template_uri']=$uri.'/'.WPPIZZA_SLUG;
		}else{
			$paths['template_dir']=$dir;
			$paths['template_uri']=$uri;
		}

		return $paths;
	}
    /**************
     	[Admin]
	***************/
    function wppizza_register_scripts_and_styles_admin() {
    		/**css*/
    		wp_register_style($this->pluginSlug, plugins_url( 'css/wppizza-addingredients-admin.css', __FILE__ ), array(), $this->pluginVersion);
    		wp_enqueue_style($this->pluginSlug);
			/**if we want to keep all the original css (including future changes) but only want to overwrite some lines , add wppizza-addingredients-admin-custom.css to your template directory*/
			if (file_exists( $this->pluginAiTemplateDir . '/wppizza-addingredients-admin-custom.css')){
				wp_register_style($this->pluginSlug.'-admin-custom', $this->pluginAiTemplateUri.'/wppizza-addingredients-admin-custom.css', array(''.$this->pluginSlug.''), $this->pluginVersion);
				wp_enqueue_style($this->pluginSlug.'-admin-custom');
			}
      		/**js***/
            wp_register_script($this->pluginSlug, plugins_url( 'js/scripts.admin.js', __FILE__ ), array(WPPIZZA_SLUG), $this->pluginVersion ,true);
            wp_enqueue_script($this->pluginSlug);
    }
    /**************
     	[Frontend]
	***************/
    function wppizza_register_scripts_and_styles() {
      		global $wp_styles;
      		$masterOptions=$this->masterOptions;

    		/**css**/
			if($masterOptions['layout']['include_css']){
				if (file_exists( $this->pluginAiTemplateDir . '/wppizza-addingredients.css')){
				/**copy stylesheet to theme directory to keep settings**/
				wp_register_style($this->pluginSlug, $this->pluginAiTemplateUri.'/wppizza-addingredients.css', array(), $this->pluginVersion);
				}else{
				wp_register_style($this->pluginSlug, plugins_url( 'css/wppizza-addingredients.min.css', __FILE__ ), array(), $this->pluginVersion);
				}
				/**custom styles if copied to theme directory**/
				if (file_exists( $this->pluginAiTemplateDir . '/wppizza-addingredients-custom.css')){
					wp_register_style($this->pluginSlug.'-custom', $this->pluginAiTemplateUri.'/wppizza-addingredients-custom.css', array($this->pluginSlug), $this->pluginVersion);
					wp_enqueue_style($this->pluginSlug.'-custom');
				}
				wp_enqueue_style($this->pluginSlug);

				/* ie-only style sheets*/
				if (file_exists( $this->pluginAiTemplateDir . '/wppizza-addingredients-ie.css')){
					wp_register_style(''.$this->pluginSlug.'-ie', $this->pluginAiTemplateUri.'/wppizza-addingredients-ie.css', array($this->pluginSlug), $this->pluginVersion);
				    $wp_styles->add_data(''.$this->pluginSlug.'-ie', 'conditional', 'IE');
    				wp_enqueue_style(''.$this->pluginSlug.'-ie');
				}
				if (file_exists( $this->pluginAiTemplateDir . '/wppizza-addingredients-ie7.css')){
					wp_register_style(''.$this->pluginSlug.'-ie7', $this->pluginAiTemplateUri.'/wppizza-addingredients-ie7.css', array($this->pluginSlug), $this->pluginVersion);
				}else{
    				wp_register_style(''.$this->pluginSlug.'-ie7', plugins_url( 'css/wppizza-addingredients-ie7.css', __FILE__ ), array($this->pluginSlug), $this->pluginVersion);
				}
    			$wp_styles->add_data(''.$this->pluginSlug.'-ie7', 'conditional', 'IE 7');
    			wp_enqueue_style(''.$this->pluginSlug.'-ie7');
			}

      		/**js***/
            wp_register_script($this->pluginSlug, plugins_url( 'js/scripts.min.js', __FILE__ ), array(WPPIZZA_SLUG), $this->pluginVersion ,$masterOptions['plugin_data']['js_in_footer']);
            wp_enqueue_script($this->pluginSlug);

			/**localize postids of items that have "add ingredients" enabled **/
			if($masterOptions['plugin_data']['js_in_footer']){
				add_action('wp_footer',  array(&$this, 'localize_variables'));
			}else{
				$this->localize_variables();
			}
    }
    /*********************************************************
      [localize scripts]
    *********************************************************/
	 function localize_variables(){
		/**get all page id's that have add ingredients enabled*/
			$ingredientsAllowed=array();
			$posts = get_posts(array(
    			'post_type'   => WPPIZZA_POST_TYPE,
    			'post_status' => 'publish',
    			'posts_per_page' => -1,
    			'fields' => 'ids'
    		));
			foreach($posts as $k=>$p){
    		//get the meta we need from post
    			$thisItemAllowsIngredients = get_post_meta($p,$this->pluginMetaValue,true);
    			 !empty($thisItemAllowsIngredients[$this->pluginMetaValue]) ? $ingredientsAllowed[]=$p : false;
			}
			/*thickbox*/
			$tb=array();
			if($this->pluginOptions['options']['ingredients_in_popup']){
				$tb['tb']=1;
				$tb['tbw']=$this->pluginOptions['options']['ingredients_in_popup_wpc'];
				$tb['tbanim']=$this->pluginOptions['options']['ingredients_in_popup_anim'];
				$tb['tbstky']=$this->pluginOptions['options']['ingredients_added_sticky'];
				$tb['tblbl']=$this->pluginOptions['localization']['add_ingredients']['lbl'];

			}
		//wp_localize_script
		wp_enqueue_script( $this->pluginSlug);
		wp_localize_script( $this->pluginSlug, $this->pluginSlug, array('ing'=>$ingredientsAllowed,'tb'=>$tb,'msg'=>array('error'=>$this->pluginOptions['localization']['required_ingredient_missing']['lbl'],'maxIng'=>$this->pluginOptions['localization']['max_ingredients']['lbl'],'maxSameIng'=>$this->pluginOptions['localization']['max_same_ingredients']['lbl'])));
	 }
/*******************************************************
*
*		[popup thickbox add scripts if enabled]
*
******************************************************/
	function wppizza_ingredients_thickbox(){
		add_thickbox();
		add_action('wp_footer', array($this,'wppizza_ingredients_thickbox_container'));
	}
	/*******************************************************
		[add the thickbox container to the page if required]
	******************************************************/
	function wppizza_ingredients_thickbox_container(){
		echo"<div id='wppizza-addingredients-tb' style='display:none;'><div>";
		//echo"".$this->wppizza_dbp_user_output(true);
		echo"</div></div>";
	}
/***********************************************************************************************
*
*
*		[start session]
*
*
***********************************************************************************************/
	function wppizza_init_sessions() {
	    if (!session_id()) {session_start();}
	    /*initialize if not set*/
	    if(!isset($_SESSION[$this->pluginSession])){
	    	/*holds currently selected ingredients*/
	    	$_SESSION[$this->pluginSession]['diy']=array();
	    }
	}
/***********************************************************************************************
*
*
* 	[ajax calls]
*
*
***********************************************************************************************/
	/******************
     [admin ajax call]
    *******************/
	function wppizza_admin_ingredients_json(){
		require(WPPIZZA_ADDINGREDIENTS_PATH.'ajax/admin-get-json.php');
		die();
	}
	/******************
     [frontend ajax call]
    *******************/
	function wppizza_ingredients_json(){
		require(WPPIZZA_ADDINGREDIENTS_PATH.'ajax/get-json.php');
		die();
	}

/***********************************************************************************************
*
*
*	[Admin Ingredients - set editable roles and add tabbed settings option page to WPPizza]
*
*
************************************************************************************************/
	function wppizza_ingredients_set_editable_roles($roles){
		global $user_level;/*get current user level*/

		foreach($roles as $roleName=>$role){
			$userRole = get_role($roleName);
			for($j=10;$j>=(int)$user_level;$j--){
				if(isset($userRole->capabilities['level_'.$j.''])){
					unset( $roles[$roleName] );
				}
			}
		}
		return $roles;
	}

	function wppizza_addingredients_register_menu_and_settings() {
		global $pagenow;
		// Check if user can access to the plugin
		/*check if we have at least one capability enabled, otherwsie do not bother having a settinsg page*/
		if(defined('WPPIZZA_SLUG') && count($this->pluginAccessCapabilities)>0){
			if (isset ($_GET['tab']) && isset($_GET['page']) && $_GET['page']==$this->pluginSlug){$tab = $_GET['tab'];}else{$tab = $this->pluginAccessCapabilities[0]['tab'];}
			require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.echo.register.submenu.pages.inc.php');
		}
	}
	/**set capability to  save options**/
    function wppizza_add_ingredients_admin_option_page_capability( $capability ) {
		return $this->pluginAccessCapabilities[0]['cap'];
	}
	function wppizza_admin_page_text_header($v) {
		require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.echo.settings.text.header.inc.php');
	}
	function admin_manage_ingredients(){
		require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.echo.manage_ingredients.inc.php');
	}
	function wppizza_admin_settings_input($field='') {
		require(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.echo.settings.input.fields.inc.php');
	}
	private function wppizza_admin_section_ingredients($field,$k,$v,$options,$optionSizes,$copy=true){
		require(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.echo.get_ingredients.inc.php');
		return $str;
	}
/*********************************************************
		[Admin Ingredients Custom Groups ]
*********************************************************/
	function wppizza_admin_section_ingredients_groups($id,$optionSizes,$tierSelected='-1',$dbVal='-1'){
		require(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.echo.get_ingredients_groups.inc.php');
		return $str;
	}
/*********************************************************
		[Admin Ingredients TABS]
*********************************************************/
	function wppizza_admin_ingredients_tabs( $currentTab = 'ingredients' ) {
	    //$tabs = array( 'ingredients' => 'Ingredients', 'custom-groups' => 'Custom Groups', 'localization' => 'Localization', 'access-level' => 'Access Rights', 'manual' => 'How To' );

	    $capTabs=$this->wppizza_ingredients_capabilities_tabs(true);
	    $tabs=array();
	    $capRequired='';
	    foreach($capTabs as $key=>$tab){
	    	if($tab['tab']==$currentTab){
	    		$capRequired=$tab['cap'];
	    	}
	    	$tabs[$tab['tab']]=array('key'=>$key,'name'=>$tab['name']);
	    }
	    	    
		if(current_user_can($capRequired)){
		    echo '<div id="icon-themes" class="icon32"><br></div>';
		    echo '<h2 class="nav-tab-wrapper">';
		    foreach( $tabs as $tab => $vars ){
		        $class = ( $tab == $currentTab ) ? ' nav-tab-active' : '';
		        echo "<a class='nav-tab$class' href='?post_type=".WPPIZZA_POST_TYPE."&page=".$this->pluginSlug."&tab=$tab'>".$vars['name']."</a>";
		    }
		    echo '</h2>';

			echo'<div id="wppizza-settings" class="'.$this->pluginSlug.'-'.$currentTab.' wrap">';
			echo"<h2>". WPPIZZA_NAME ." ".__('Ingredients', $this->pluginLocale)." - ".$tabs[$currentTab]['name']."</h2>";
			echo'<form id="'.$this->pluginSlug.'-'.$currentTab.'-form" action="options.php" method="post">';
			echo'<input type="hidden" name="'.$this->pluginSlug.'_ingredients" value="1">';
				settings_fields($this->pluginSlug);
				do_settings_sections('ingredients');
				/*only print save button when we are not in the howto section*/
				if (isset ( $_GET['tab'] ) && $_GET['tab']=='manual'){}else{
					submit_button('','button button-primary '.$this->pluginSlug.'-'.$currentTab.'-save',''.$this->pluginSlug.'-'.$currentTab.'-save');
				}
			echo'</form>';
			echo'</div>';
		}
	}
/******************************************************************
	[meta boxes , render , save on creation/update of post]
*******************************************************************/
	function wppizza_admin_metaboxes() {
    	add_meta_box( $this->pluginSlug,__('Allow to add ingredients ?', $this->pluginLocale),array($this,'wppizza_admin_render_metaboxes'),WPPIZZA_POST_TYPE, 'normal', 'high');
	}
	function wppizza_admin_render_metaboxes( $meta_options ) {
		require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.echo.metaboxes.inc.php');
	}
	function wppizza_admin_save_metaboxes($item_id, $item_details ) {
		/** bypass, when doing "quickedit" (ajax) and /or "bulk edit"  as it will otherwise loose all meta info (i.e enabled, halfs, quarters etc)!!!***/
		if ( defined('DOING_AJAX') || isset($_GET['bulk_edit'])){return;}

		/**bypass the below when activating plugin as we are installing the default items on first activation via wp_insert_post()**/
		if(!isset($_GET['activate'])){
			/***as this function gets called when creating a new page, we will also insert some default values (as $_POST will be empty)**/
			// Check post type first
		    if($item_details->post_type == WPPIZZA_POST_TYPE ){
		    	/**************************************************************
		    		[check - when trying to enable add ingredients -
		    		if selected size has corresponding ingredients setup,
		    		otherwise do not save enabled state]
		    	***************************************************************/
		    	if(!empty($_POST[$this->pluginSlug][''.$this->pluginMetaValue.''])){
		    		$canEnable=$this->wppizza_check_ingredient_sizes($_POST[WPPIZZA_SLUG]['sizes']);
		    		/*if we do NOT have corresponsing ingredient sizes, override and  set to not enable*/
		    		if(!$canEnable){
		    			$_POST[$this->pluginSlug][''.$this->pluginMetaValue.'']=false;
		    		}
		    		/**if we only have a textbox but no ingredients, override to set to whole only**/
		    		if($canEnable && is_string($canEnable) && (string)$canEnable=='textboxonly'){
		    			$_POST[$this->pluginSlug][''.$this->pluginMetaMulti.'']=array(1=>true);
		    		}
		    		
		    	}
		    	//**allow ingredients**//
		    	$itemMeta[''.$this->pluginMetaValue.'']						= !empty($_POST[$this->pluginSlug][''.$this->pluginMetaValue.'']) ? true : false;
		    	update_post_meta($item_id,''.$this->pluginMetaValue.'',$itemMeta);

		    	$itemMulti[''.$this->pluginMetaMulti.'']					= !empty($_POST[$this->pluginSlug][''.$this->pluginMetaMulti.'']) ? $_POST[$this->pluginSlug][''.$this->pluginMetaMulti.''] : array(1=>true);//default whole pizza if nothing selected
		    	update_post_meta($item_id,''.$this->pluginMetaMulti.'',$itemMulti);

		    	$itemMultiDevide[''.$this->pluginMetaMultiDivide.'']		= !empty($_POST[$this->pluginSlug][''.$this->pluginMetaMultiDivide.'']) ? $_POST[$this->pluginSlug][''.$this->pluginMetaMultiDivide.'']  : false;
		    	update_post_meta($item_id,''.$this->pluginMetaMultiDivide.'',$itemMultiDevide);

			}
		}
	}
	/*a post has been deleted, remove from custom groups if exists*/
	function wppizza_ingredients_delete_post(){
   		global $post;
   		$options=$this->pluginOptions;
   		foreach($options['ingredients_custom_groups'] as $k=>$v){
   			foreach($v['item'] as $iId=>$iVal){
   				if(isset($v['item'][$post->ID])){
   					unset($options['ingredients_custom_groups'][$k]['item'][$post->ID]);
   					$doUpdate=1;
   				}
   			}
   		}
   		if(isset($doUpdate)){
   			update_option($this->pluginSlug, $options );
   		}
	}
/*********************************************************
*
*		[admin options validation]
*
*********************************************************/
    public function wppizza_addingredients_admin_options_validate($input){
		/*new install , use input*/
		if($this->pluginOptions==0){
			$newOptions=$input;
		}
		/*update, keep old settings */
    	if($this->pluginOptions!=0){
    		/**just updating the plugin, will be overwritten when posting*/
    		$newOptions=$input;
			/*do not use require_once here as it may be used more than once .doh!**/
			require(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.options.validate.inc.php');
    	}
		return $newOptions;
    }

/*********************************************************
*
*		[array of wp capabilities]
*
*********************************************************/
function wppizza_ingredients_capabilities_tabs($get_user_caps=false){
	//$tab['plugin']=__('Plugin',$this->pluginLocale);
	$tabs['ingredients']=array('name'=>__('Ingredients',$this->pluginLocale),'cap'=>'wppizza_ingr_cap_ingredients');
	$tabs['options']=array('name'=>__('Options',$this->pluginLocale),'cap'=>'wppizza_ingr_cap_options');
	$tabs['custom-groups']=array('name'=>__('Custom Groups',$this->pluginLocale),'cap'=>'wppizza_ingr_cap_groups');
	$tabs['localization']=array('name'=>__('Localization',$this->pluginLocale),'cap'=>'wppizza_ingr_cap_localization');
	$tabs['access-level']=array('name'=>__('Access Rights',$this->pluginLocale),'cap'=>'wppizza_ingr_cap_access');
	$tabs['license']=array('name'=>__('License',$this->pluginLocale),'cap'=>'wppizza_ingr_cap_license');
	$tabs['manual']=array('name'=>__('How To',$this->pluginLocale),'cap'=>'wppizza_ingr_cap_howto');

	if($get_user_caps){
		global $current_user;
		$usercaps=array();
		$capUnique=array();/*dont need to have the same thing multiple times*/
		/*user can have more than one role**/
		foreach($current_user->roles as $roleName){
			$userRole = get_role($roleName);
			foreach($tabs as $tab=>$v){
				if(isset($userRole->capabilities[$v['cap']]) && !isset($capUnique[$v['cap']])){
					$usercaps[]=array('tab'=>$tab,'cap'=>$v['cap'],'name'=>$v['name']);
					$capUnique[$v['cap']]=1;
				}
			}
		}
		return $usercaps;
	}

	return $tabs;
}
/*********************************************************
*
*		[get current ingredient sizes]
*		[and check posted/current item sizes against that array
*		to see if it even makes sense to enable "add ingredients" ]
*
*********************************************************/
    public function wppizza_check_ingredient_sizes($selectedPriceTier){
		/**initialize value to be able to check "add ingredients"*/
		$canEnable=false;
		/*a) get all enabled sizes in ingrediensgt array*/
		$currentIngredients=$this->pluginOptions;
		$currentIngredients=$currentIngredients['ingredients'];
		$ingredientsSizes=array();
		if(is_array($currentIngredients)){
		foreach($currentIngredients as $k=>$v){
			if($v['enabled']){
				$ingredientsSizes[$v['sizes']]=$v['sizes'];
			}
		}}
		/*b) check if selected size (pricetier) for item is in above array ***/
		if(in_array($selectedPriceTier,$ingredientsSizes)){
			$canEnable=true;
		}
		
		/*c) if we cannot enable yet beacuse there arent any ingredients for this tier, check also if we have a textbox for example -> ADDED 4.3.4 **/
		if(!$canEnable){
		global $post;
		if(is_object($post)){
   		foreach($this->pluginOptions['ingredients_custom_groups'] as $k=>$v){
   			if($v['sizes']=='textbox' && isset($v['item'][$post->ID])){
   				$canEnable='textboxonly';
   			break;
   			}
   		}}		
		
		}
		
		return $canEnable;
    }

/***********************************************************************************
*
*
*	[if we have preselected ingredients, we need to display a from
*	price in cluding all the ingredients instead of just the base price]
*
*
**********************************************************************************/
	function wppizza_ingredients_filter_loop_meta($meta,$itemId){
		/***************************
			[first let's check if this menu item has any preselected ingredients]
		***************************/
		$customGroups=$this->pluginOptions['ingredients_custom_groups'];
		$ingredients=$this->pluginOptions['ingredients'];
		/*******************************************************
			if group5 (i.e exclude ingredients)
			make an array of these so we can - well -exclude them
		*******************************************************/
		$exclIngr=array();
		foreach($customGroups as $kGroup=>$cGroup){
			if(isset($cGroup['item'][$itemId]) && count($cGroup['ingredient'])>0 && $cGroup['type']==5){
				foreach($cGroup['ingredient'] as $kIng=>$mIng){
					$exclIngr[$kIng]=$kIng;
				}
			}
		}
		/*******************************************************
			if group6 (i.e preselect ingredients)
			make an array of these so we can preselect them
		*******************************************************/
		$preSelIngr=array();
		$preSelIngrPriceZero=array();/*initialize array of preselected ingredients where the price is forced to be zero*/
		foreach($customGroups as $kGroup=>$cGroup){
			if(isset($cGroup['item'][$itemId]) && count($cGroup['ingredient'])>0 && $cGroup['type']==6){
				foreach($cGroup['ingredient'] as $kIng=>$mIng){
					if( !isset($exclIngr[$kIng])){/*exclude excluded ingredients**/
						if($cGroup['preselpricezero']){
							$preSelIngrPriceZero[$kIng]=$kIng;/**add to array of preselected, but forced zero prices*/
						}
						$preSelIngr[$kIng]=$kIng;
					}
				}
			}
		}
		/***make sure we do not have more than one ingredient preselected (and therefore calculated) on group type 1 as it's a radio input that can only have one input**/
		foreach($customGroups as $kGroup=>$cGroup){
			if(isset($cGroup['item'][$itemId]) && count($cGroup['ingredient'])>0 && $cGroup['type']==1){
				$i=0;
				foreach($cGroup['ingredient'] as $iId){
					if($i>0){unset($preSelIngr[$iId]);}
				$i++;
				}
			}
		}

		/****************************
			[if there are preselcted ingredients, calculte price on a per size basis]
		****************************/
		if(count($preSelIngr)>0){
			/**whole/quarter/halfs allowed ?*/
			$meta_values_multi = get_post_meta($itemId,$this->pluginMetaMulti,true);
			/**make sure we have a value/array for ingredients that were set in previous versions of the plugin that had no multioption yet*/
			if(!isset($meta_values_multi['add_ingredients_multi']) || !is_array($meta_values_multi['add_ingredients_multi'])){
				$multiIngredients=array(1=>1);/*whole only**/
			}else{
				$multiIngredients=$meta_values_multi['add_ingredients_multi'];
			}


			/**get price multiplier (percentage of whole price)*/
			$priceMultiply=array();
			$meta_values_devide = get_post_meta($itemId,$this->pluginMetaMultiDivide,true);
			if(isset($meta_values_devide[$this->pluginMetaMultiDivide])){
				foreach($meta_values_devide[$this->pluginMetaMultiDivide] as $k=>$v){
					$priceMultiply[$k]=($v/100);
				}
			}else{
				foreach($multiIngredients as $k=>$v){
					$priceMultiply[$k]=1;
				}
			}
			/***foreach price tier calculate preselected ingredients price for whole/half/quarters by multiplier, add to base price, sort ascending and use minimum price as from price**/
			foreach($meta['prices'] as $tierKey=>$tierBasePrice){
				$tierPrice[$tierKey]=array();
				foreach($multiIngredients as $multiSet){
					$tierPrice[$tierKey][$multiSet]=$tierBasePrice;
					for($i=1;$i<=(int)$multiSet;$i++){/**php 5.5 really wants $multiSet to be cast to integer there or we'll have an infinite loop...*/
						foreach($preSelIngr as $ingrID){

							/**preselect item where price is forced to zero**/
							if(isset($preSelIngrPriceZero[$ingrID])){
								$ingredientPrice=0;
							}else{
								$ingredientPrice=$ingredients[$ingrID]['prices'][$tierKey];
							}
							$tierPrice[$tierKey][$multiSet]+=round($priceMultiply[$multiSet]*$ingredientPrice,2);
						}
					}
				}

				/****sort the resulting prices per tier so we can have a "from" price****/
				asort($tierPrice[$tierKey]);
				$lowestPrice = reset($tierPrice[$tierKey]);
				$meta['prices'][$tierKey]=$lowestPrice;
			}
			/******************************************************************************************
				when allowing for more than just one whole/half/quarter with preselected ingredients,
				can result in different prices for the sum of all ingredients for any particular selection
				therefore we might want to display a '***' or some such thing in the loop somewhere
				indicating "from prices" or something.
				this flag allows us to identify that a menu item has preselected ingredients in the loop
				if one wants to do such a thing......
			*****************************************************************************************/
			$meta['ingredientsPreselected']=1;
		}
	return $meta;
	}


	/*******************************************************
     *
     *	[filter output of ingredients array to display in cart etc]
     *	[adding PHP_EOL for linebreaks in plaintext email and order history]
     ******************************************************/

	function wppizza_ingredients_filter_additionalinfo($cart){
		/**convert all localization vars to single dimensional array to make them easier to deal with below **/
		$lbl=wppizza_return_single_dimension_array($this->pluginOptions['localization'],'lbl');
		/** group ident**/
		static $grIdent=0;

		if(isset($cart['items']) && is_array($cart['items'])){
			foreach($cart['items'] as $iID=>$item){
				if(isset($item['additionalinfo']['addingredients'])){
					$arr2Str=array();
					$idCount=0;
					$addIngDetails=$item['additionalinfo']['addingredients'];

					/***if it's a group applicable to whole item only. only set if half or quarter ingredients were selected and one of these type of gourp applies**/
					if(isset($addIngDetails['wholeonly'])){
						$arr2Str[]='<div class="wppizza-ingrinfo-'.$idCount.'"><span class="wppizza-multi-icon wppizza_multi_icon_1_1">'.$lbl['multi_icon_1_1'].'</span>'.$addIngDetails['wholeonly'].'</div>';
						$idCount++;
					}
					/*identify if it should be half or quarter icons*/
					if(isset($addIngDetails['multi'])){
					$iconIdentType=count($addIngDetails['multi']);
					foreach($addIngDetails['multi'] as $k=>$v){
						if(trim($v)==''){$v='-------';}
						$arr2Str[]='<div class="wppizza-ingrinfo-'.$idCount.'"><span class="wppizza-multi-icon wppizza_multi_icon_'.$iconIdentType.'_'.$k.'">'.$lbl['multi_icon_'.$iconIdentType.'_'.$k.''].'</span>'.$v.'</div>';
						$idCount++;
					}}
					/**any textbox ?**/
					if(isset($addIngDetails['textbox'])){
						$arr2Str[]='<div id="wppizza-ingrcomments-'.$grIdent.'-'.$idCount.'" class="wppizza-ingrinfo-'.$idCount.' wppizza-ingredients-comments">'.trim($addIngDetails['textbox']).'</div>';
						$idCount++;
					}

					$grIdent++;

					/**only change output if there's actually something to output**/
					if(count($arr2Str)>0){
						$cart['items'][$iID]['additionalinfo']['addingredients']=implode(PHP_EOL.'   ',$arr2Str).PHP_EOL;
					}
				}
			}
		}
	return $cart;
	}


	function wppizza_filter_ingredients_order_extend($orderItems){

		/**get all labels we may need**/
		$lbl=wppizza_return_single_dimension_array($this->pluginOptions['localization'],'lbl');
		/** group ident**/
		$grIdent=0;

		foreach($orderItems as $iID=>$oItems){

			if(isset($oItems['extend']) && is_array($oItems['extend']) && count($oItems['extend'])>0){
				$idCount=0;
				$arr2Str['html']=array();
				$arr2Str['txt']=array();
				/**only deal with 'addingredients'**/
				$addIngDetails=$oItems['extend']['addingredients'];


				/***if it's a group applicable to whole item only. only set if half or quarter ingredients were selected and one of these type of gourp applies**/
				if(isset($addIngDetails['wholeonly'])){
					$arr2Str['html'][]='<div class="wppizza-ingrinfo-'.$idCount.'"><span class="wppizza-multi-icon wppizza_multi_icon_1_1">'.$lbl['multi_icon_1_1'].'</span>'.$addIngDetails['wholeonly'].'</div>';
					if($lbl['multi_icon_1_1']!=''){
						$arr2Str['txt'][]=''.$lbl['multi_icon_1_1'].' '.$addIngDetails['wholeonly'].'';
					}else{
						$arr2Str['txt'][]=''.$addIngDetails['wholeonly'].'';
					}
					$idCount++;
				}

				/*identify if it should be half or quarter icons*/
				if(isset($addIngDetails['multi'])){
				$iconIdentType=count($addIngDetails['multi']);
				foreach($addIngDetails['multi'] as $k=>$v){
					if(trim($v)==''){$v='-------';}
					$arr2Str['html'][]='<div class="wppizza-ingrinfo-'.$idCount.'"><span class="wppizza-multi-icon wppizza_multi_icon_'.$iconIdentType.'_'.$k.'">'.$lbl['multi_icon_'.$iconIdentType.'_'.$k.''].'</span>'.$v.'</div>';
					$arr2Str['txt'][]=''.$lbl['multi_icon_'.$iconIdentType.'_'.$k.''].' '.$v.'';
					$idCount++;
				}}

				/**any textbox ?**/
				if(isset($addIngDetails['textbox'])){
					$arr2Str['html'][]='<div id="wppizza-ingrcomments-'.$grIdent.'-'.$idCount.'" class="wppizza-ingrinfo-'.$idCount.' wppizza-ingredients-comments">'.trim($addIngDetails['textbox']).'</div>';
					$arr2Str['txt'][]=''.trim($addIngDetails['textbox']).'';
					$idCount++;
				}

				/**only change output if there's actually something to output**/
				if($idCount>0){
					$orderItems[$iID]['addinfo']['html']=implode(PHP_EOL,$arr2Str['html']);/*PHP_EOL just for source readabilities sake. could be ''*/
					$orderItems[$iID]['addinfo']['txt']=''.implode(PHP_EOL.'   ',$arr2Str['txt']);
				}
			}
		}
		return $orderItems;
	}
	/*******************************************************
     *
     *	[add a js function that gets called after cart refresh]
     *
     ******************************************************/
	function wppizza_ingredients_filter_js_cart_refresh_functions($array){
		$array[]='wppizzaCartCommentToggle';
		return $array;
	}

	function wppizza_ingredients_filter_js_extend($array){
		$array['wppizzaAddIngr']['msg']['cmttgl']=$this->pluginOptions['localization']['js_toggle_comments']['lbl'];
		return $array;
	}
	/*******************************************************
     *
     *	[helper to make labels single dimensional]
     *
     ******************************************************/
	function wppizza_ingredients_labels($arr, $key='lbl'){
		$arr=maybe_unserialize($arr);
		$array=array();
		if(is_array($arr)){
		foreach($arr as $k=>$v)
			$array[$k]=$v[$key];
		}
		return $array;
	}
	/*******************************************************
     *
     *	[WPML : make user defined strings wpml compatible]
     *
     ******************************************************/
	function wppizza_add_ingredients_wpml_localization(){
		if(function_exists('icl_translate') && $this->pluginOptions!=0) {
			/*localization*/
			foreach($this->pluginOptions['localization'] as $k=>$arr){
    			$this->pluginOptions['localization'][$k]['lbl'] = icl_translate(WPPIZZA_ADDINGREDIENTS_SLUG,''. $k.'', $arr['lbl']);
			}

			/*ingredients*/
			foreach($this->pluginOptions['ingredients'] as $k=>$arr){
				$this->pluginOptions['ingredients'][$k]['item'] = icl_translate(WPPIZZA_ADDINGREDIENTS_SLUG,'ingredient_'. $k.'', $arr['item']);
			}

			/*label if price is zero zero**/
			$this->pluginOptions['settings']['price_localize_if_zero'] = icl_translate(WPPIZZA_ADDINGREDIENTS_SLUG,'price_localize_if_zero', $this->pluginOptions['settings']['price_localize_if_zero']);

			/*ingredients_custom_groups*/
			foreach($this->pluginOptions['ingredients_custom_groups'] as $k=>$arr){
				$this->pluginOptions['ingredients_custom_groups'][$k]['label'] = icl_translate(WPPIZZA_ADDINGREDIENTS_SLUG,'custom_group_'. $k.'_label', $arr['label']);
				$this->pluginOptions['ingredients_custom_groups'][$k]['info'] = icl_translate(WPPIZZA_ADDINGREDIENTS_SLUG,'custom_group_'. $k.'_info', $arr['info']);
			}
		}
	}

	/**************************************************************************************************
	*
	*	[EDD: allow updates to be delivered automatically]
	*	[EDD CONSTANTS TO ENABLE AUTOMATIC UPDATES NOTFICATIONS IN WP DASHBOARD.]
	*
	**************************************************************************************************/
	function wppizza_addingredients_edd(){
		/*include class*/
		if( !class_exists( 'WPPIZZA_SL_Plugin_Updater' ) ) {
			require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.eddsl.plugin_updater.inc.php');
		}

		/*retrieve our license key from the DB*/
		$license_key = trim($this->pluginOptions['license']['key']);

		/* setup the updater */
		$edd_updater = new WPPIZZA_SL_Plugin_Updater( WPPIZZA_ADDINGREDIENTS_EDD_URL, __FILE__, array(
			'version'		=> WPPIZZA_ADDINGREDIENTS_CURRENT_VERSION, 		// current version number
			'license'		=> $license_key, 	// license key (used get_option above to retrieve from DB)
			'item_name'		=> WPPIZZA_ADDINGREDIENTS_EDD_NAME, 	// name of this plugin
			'author'		=> 'ollybach'  // author of this plugin
			)
		);
	}

	/************************************
	*
	* activate a license key
	*
	*************************************/

	function wppizza_addingredients_activate_license($license) {
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( WPPIZZA_ADDINGREDIENTS_EDD_NAME ) // the name of our product in EDD
		);
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, WPPIZZA_ADDINGREDIENTS_EDD_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return 'connection-error';

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"
		return $license_data->license;
	}
	/************************************
	*
	* de-activate a license key
	*
	*************************************/
	function wppizza_addingredients_deactivate_license($license) {
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( WPPIZZA_ADDINGREDIENTS_EDD_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, WPPIZZA_ADDINGREDIENTS_EDD_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return 'connection-error';

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' || $license_data->license == 'failed' ){
			return false;
		}
	}

	function wppizza_addingredients_check_license($license) {/*currently not in use*/
		global $wp_version;

		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,
			'item_name' => urlencode( WPPIZZA_ADDINGREDIENTS_EDD_NAME )
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, WPPIZZA_ADDINGREDIENTS_EDD_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return 'connection-error';

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		print_r($license_data->license);
		exit();
		return $license_data->license;
	}

}

	function wppizza_add_ingredients_init(){
		if ( ! defined( 'WPPIZZA_CLASS' ) ) {return;}
		global $wppizza_add_ingredients;
		$wppizza_add_ingredients = new WPPizza_Add_Ingredients();
	}
	add_action("init","wppizza_add_ingredients_init",11);
}
?>