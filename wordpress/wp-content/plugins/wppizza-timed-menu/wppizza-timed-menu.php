<?php
/*
Plugin Name: WPPizza Timed Menu
Description: An extension for WPPizza to set times and dates when your menu items are available - Requires WPPIZZA 2.8.4+
Author: ollybach
Plugin URI: http://www.wp-pizza.com
Author URI: http://www.wp-pizza.com
Version: 1.2

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

add_action( 'plugins_loaded', 'wppizza_timed_menu',9);/*priority must be < 10*/

/*on uninstall, remove options from options table*/
register_uninstall_hook( __FILE__, 'wppizza_timed_menu_uninstall');

function wppizza_timed_menu(){
	if (!class_exists( 'WPPIZZA' )) {return;}

	/*******************************************************************
	*
	*	[version numbers]
	*
	********************************************************************/
	define('WPPIZZA_TIMED_MENU_CURRENT_VERSION','1.2');
	/*******************************************************************
	*
	*	[EDD]
	*
	********************************************************************/
	define("WPPIZZA_TIMED_MENU_EDD_NAME", "WPPizza – Timed Menu" );/*checks if there is an update available to this plugin. only ever runs in admin. comment out to disable*/
	define('WPPIZZA_TIMED_MENU_EDD_URL', 'https://www.wp-pizza.com' );

	/*******************************************************************
	*
	*	[WPPIZZA_TIMED_MENU CLASS]
	*
	********************************************************************/
	if ( ! class_exists( 'WPPIZZA_TIMED_MENU' ) ){
		class WPPIZZA_TIMED_MENU {
		      /************************************************************
		      *
		      *	[construct]
		      *
		      *************************************************************/
		      function __construct() {
				$this->wpptmVersion = WPPIZZA_TIMED_MENU_CURRENT_VERSION;
				$this->wpptmClassName = 'WPPizza -  Timed Menu';
				$this->wpptmOptionsName = 'wppizza_timed_menu';
				$this->wpptmOptions = get_option($this->wpptmOptionsName,0);
				$this->wpptmOptionsPreWpml = $this->wpptmOptions;
				$this->wpptmLocale=$this->wpptmOptionsName."-locale";
				$this->wpptmSlug=$this->wpptmOptionsName;
				$this->wpptmCurrentTime=current_time('timestamp');
				$this->wpptmNotice=0;
				/**to get the template paths, uri's and possible subdir and set vars accordingly**/
				$pathDirUri=$this->wppizza_tm_template_paths();	
				$this->wpptmTemplateDir=$pathDirUri['template_dir'];/**to amend get_stylesheet_directory() according to whether wppizza subdir exists*/
				$this->wpptmTemplateUri=$pathDirUri['template_uri'];/**to amend get_stylesheet_directory_uri() according to whether wppizza subdir exists*/							

				/**text domain**/
				load_plugin_textdomain($this->wpptmLocale, false, dirname(plugin_basename( __FILE__ ) ) . '/lang' );


				/**admin****************/
				if(is_admin()){
					add_action('plugins_loaded', array( $this, 'wppizza_tm_load_classes'));/*load classes**/
					add_action('admin_init', array( $this, 'wppizza_tm_init'));/*if necessary, add the db option table and fill with defaults**/
					add_action('admin_init', array( $this, 'wppizza_tm_do_admin_notice'));/*if necessary,show admin info screens**/
					add_action('admin_enqueue_scripts', array( $this, 'wppizza_tm_register_scripts_and_styles_admin'));
					add_action('admin_menu', array( $this, 'wppizza_tm_menu_and_settings' ) );

					/***********************************************************************************************************************
						to avoid heavy dbQueries in the frontend - especially given that menu items and cats wont change too often anyway,
						let's update the timed menu option on save posts or when the categories get updated to reflect any slug or post changes
						just a LOT better than running these queries allo the time in the frontend
					***********************************************************************************************************************/
					/*edit / delete categories*/
					add_action('edited_'.WPPIZZA_TAXONOMY.'', array($this,'wppizza_tm_menu_update_option_on_update_category'),99);/*runs in quick edit and normal edit AFTER update. Allegedly this hook doesnt exist ? hmm, works for me ....*/
					add_action('delete_'.WPPIZZA_TAXONOMY.'', array($this,'wppizza_tm_menu_update_option_on_delete_category'),99);/*category deleted there does not seem to be a hook delete_{$taxonomy} or it just doesnt work  ?!*/
					add_action('created_'.WPPIZZA_TAXONOMY.'', array($this,'wppizza_tm_menu_update_option_on_create_category'),99);
					/*add / edit  post*/
					add_action('save_post', array( $this, 'wppizza_tm_menu_update_option_on_save_post'), 99, 2 );
					/*delete  post*/
					add_action('after_delete_post', array( $this, 'wppizza_tm_menu_update_option_on_delete_post'));


					/**set capability to update options**/
					add_filter( 'option_page_capability_'.$this->wpptmSlug.'', array($this, 'wppizza_tm_admin_option_page_capability' ));

					/**add licensing things if defined**/
					if(defined('WPPIZZA_TIMED_MENU_EDD_NAME')){
						add_action('admin_init', array( $this, 'wppizza_timed_menu_edd'),2);
					}
				}

				if(!is_admin()){
					add_filter('init', array( $this, 'wppizza_tm_getitems'));

					/**enqueu script and styles frontend **/
					add_action('wp_enqueue_scripts', array( $this, 'wppizza_tm_register_scripts_and_styles'),99);//$this->pluginOptions['layout']['css_priority']
					/*mark item as unavailable**/
					if($this->wpptmOptions['options']['display_as_unavailable']){
						add_action( 'wppizza_loop_inside_before_article', array( $this, 'wppizza_tm_nadiv_before'));
						add_action( 'wppizza_loop_inside_after_article', array( $this, 'wppizza_tm_nadiv_after'));
					}

					/**filter pages, posts, nav**/
					add_filter('get_pages', array($this,'wppizza_tm_exclude_pages'));/*exclude pages from page widgets*/
					add_filter('wp_get_nav_menu_items', array($this,'wppizza_tm_exclude_nav_menu'), null, 3 );/*exclude posts and pages from nav menu (appearance->menus)*/
					add_filter('wppizza_filter_navigation', array( $this, 'wppizza_tm_filter_navigation'));/*exclude and/or recount categories from wppizza nav (widget)*/
					add_filter('wppizza_filter_loop', array( $this, 'wppizza_tm_filter_loop'));/**exclude particular menu items*/
					add_action( 'wppizza_loop_outside_before', array( $this, 'wppizza_tm_currently_not_available'));
					/**add wpml . must run in FRONTEND ONLY otherwsie the backend vars might get changed when updating plugin whilst being in another language than the main one***/
					add_action('init', array( $this, 'wppizza_tm_wpml_localization'),99);
				}


				/************************************************************************
					[ajax]
				*************************************************************************/
				/*admin only*/
				add_action('wp_ajax_wppizza_tm_admin_json', array($this,'wppizza_tm_admin_json') );
		      }
			/**********************************************************************************************
			*
			*
			*	[admin functions]
			*
			*
			**********************************************************************************************/
				/******************************************************
				*
				*	[load classes]
				*
				******************************************************/
				function wppizza_tm_load_classes() {
					/**edd**/
					if(defined('WPPIZZA_TIMED_MENU_EDD_NAME')){
						require_once(WPPIZZA_PATH.'classes/wppizza.edd.inc.php');
						$this->wpptmEdd=new WPPIZZA_EDD_SL();
					}
					/**user caps***/
					require_once(WPPIZZA_PATH.'classes/wppizza.user.caps.inc.php');
					$this->wpptmUserCaps=new WPPIZZA_USER_CAPS();
					/*get caps of current user*/
					$this->wpptmCurrentUserCaps=$this->wpptmUserCaps->current_user_caps($this->wppizza_tm_caps());
				}


				/************************************************************************************
					[admin notices: show and dismiss]
				************************************************************************************/
			    function wppizza_tm_do_admin_notice() {/*check if we need to show any notices i.e when set to 1 or on first install*/
					if($this->wpptmOptions['plugin_data']['nag_notice']!=0 || $this->wpptmOptions==0){
						add_action('admin_notices', array( $this, 'wppizza_tm_install_notice') );
						add_action('admin_head', array($this, 'wppizza_tm_dismiss_notice_js') );
						add_action('wp_ajax_wppizza_tm_dismiss_notice', array($this, 'wppizza_tm_dismiss_notice'));
			    	}
			  	}

				/* plugin admin screen notices/nags */
			    function wppizza_tm_install_notice() {
						/**get url to info screens**/
						$pluginInfoInstallationUrl = admin_url('edit.php?post_type=wppizza&page=wppizza_timed_menu&tab=howto');

						$pluginUpdatedNotice='';
						$pluginUpdatedNotice.='<div id="message" class="updated wppizza_tm_admin_notice" style="padding:20px;">';
						/*set text depending on notice number*/
						if($this->wpptmOptions['plugin_data']['nag_notice']=='1' || $this->wpptmNotice==1){
							$pluginUpdatedNotice.='<b>'.$this->wpptmClassName.' Installed</b><br/><br/>';
							$pluginUpdatedNotice.='Thank you for installing '.$this->wpptmClassName.'<br />';
							$pluginUpdatedNotice.='<span style="color:red">Make sure to read the <a href="'.$pluginInfoInstallationUrl.'">"HowTo"</a>. DO NOT SKIP THIS</span>.';
							$pluginUpdatedNotice.='<br/>';
						}

						$pluginUpdatedNotice.='<br/><a href="#" onclick="wppizza_tm_dismiss_notice(); return false;" class="button-primary">dismiss</a>';
						$pluginUpdatedNotice.='</div>';
						print"".$pluginUpdatedNotice."";
			    }

			    function wppizza_tm_dismiss_notice_js () {
			        $js="";
			        $js.="<script type='text/javascript' >".PHP_EOL."";
			        $js.="jQuery(document).ready(function($) {".PHP_EOL."";
			            $js.="wppizza_tm_dismiss_notice = function () {".PHP_EOL."";
				        	$js.="var data = {action: 'wppizza_tm_dismiss_notice'};".PHP_EOL."";
				        	// since wp2.8 ajaxurl is defined in admin header pointing to admin-ajax.php
				        	$js.="jQuery.post(ajaxurl, data, function(response) {".PHP_EOL."";
						        $js.="$('.wppizza_tm_admin_notice').hide('slow');".PHP_EOL."";
				        	$js.="});".PHP_EOL."";
				        $js.="};".PHP_EOL."";
			        $js.="});".PHP_EOL."";
			        $js.="</script>".PHP_EOL."";
			        print"".$js;
			    }
			    public function wppizza_tm_dismiss_notice() {
			    	$options = $this->wpptmOptionsPreWpml;
			    	$options['plugin_data']['nag_notice']=0;
			    	update_option($this->wpptmOptionsName,$options);
			        die();
			    }

				/******************************************************
				*
				*	[insert options and defaults on first install]
				*
				******************************************************/
				function wppizza_tm_init(){
					if($this->wpptmOptionsPreWpml==0){
						/*include default options**/
						require('inc/admin.setup.default.options.inc.php');
						$this->wpptmNotice=1;
						$defaultOptions['plugin_data']['nag_notice']=1;/*set nag notice*/
						update_option($this->wpptmOptionsName, $defaultOptions );
					}
					/**update version**/
					//$force=1;/*development only*/
					if($this->wpptmOptionsPreWpml!=0 && version_compare( $this->wpptmVersion, $this->wpptmOptions['plugin_data']['version'], '>' ) || isset($force)){
					
						
						$defaultOptions=$this->wpptmOptionsPreWpml;
										
						/**include default options and keep or amend currently not required***/
						//require('inc/admin.setup.default.options.inc.php');

						/*distinctly set version*/
						$defaultOptions['plugin_data']['version'] = $this->wpptmVersion;
						update_option($this->wpptmOptionsName, $defaultOptions );
					}
				}

				/******************************************************
				*
				*	[set caps]
				*
				******************************************************/
				function wppizza_tm_caps() {
					$caps=array();
					$caps['timed_items']=array('name'=>__('Timed Items',$this->wpptmLocale),'cap'=>'wppizza_cap_wpptm_timed_items');
					$caps['localization']=array('name'=>__('Localization',$this->wpptmLocale),'cap'=>'wppizza_cap_wpptm_localization');
					$caps['options']=array('name'=>__('Options',$this->wpptmLocale),'cap'=>'wppizza_cap_wpptm_options');
					$caps['access']=array('name'=>__('Access',$this->wpptmLocale),'cap'=>'wppizza_cap_wpptm_access');
					$caps['howto']=array('name'=>__('How To',$this->wpptmLocale),'cap'=>'wppizza_cap_wpptm_howto');
					if(defined('WPPIZZA_TIMED_MENU_EDD_NAME')){
						$caps['license']=array('name'=>__('License',$this->wpptmLocale),'cap'=>'wppizza_cap_wpptm_license');
					}
					return $caps;
				}
				/******************************************************
				*
				*	[register and display menu settings page]
				*
				******************************************************/

				function wppizza_tm_menu_and_settings() {
					$this->wppizza_tm_load_classes();
					/**if we have at least one capability register the page by just choosing the first available capability**/
					if(defined('WPPIZZA_SLUG') && count($this->wpptmCurrentUserCaps)>0){
						if (isset ($_GET['tab']) && isset ($_GET['page']) && $_GET['page']==$this->wpptmSlug){$tab = $_GET['tab'];}else{$tab = $this->wpptmCurrentUserCaps['tabs'][0];}
						$allTabs=$this->wppizza_tm_caps();
						$requiredCap=$allTabs[$tab]['cap'];
						if(current_user_can($requiredCap)){
							require_once('inc/admin.echo.register.submenu.pages.inc.php');
						}
					}
				}

				function wppizza_admin_manage_tm(){
					$this->wppizza_tm_admin_tabs();
					$firstAllowedTab=$this->wpptmCurrentUserCaps['tabs'][0];

					$current = !empty($_GET['tab']) ?  $_GET['tab'] : $firstAllowedTab;
					require_once('inc/admin.echo.manage_tm.inc.php');
				}

				/*********************************************************
					[Admin TABS]
				*********************************************************/
				function wppizza_tm_admin_tabs() {
					$tabs = $this->wppizza_tm_caps();
				    $current = !empty($_GET['tab']) ?  $_GET['tab'] : $this->wpptmCurrentUserCaps['tabs'][0];
				    echo '<div id="icon-themes" class="icon32"><br></div>';
				    echo '<h2 class="nav-tab-wrapper">';
				    foreach( $tabs as $tab => $arr ){
				    	if(in_array($tab,$this->wpptmCurrentUserCaps['tabs'])){
				        	$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				        	echo "<a class='nav-tab".$class."' href='?post_type=".WPPIZZA_POST_TYPE."&page=".$this->wpptmSlug."&tab=".$tab."'>".$arr['name']."</a>";
				    	}
				    }
				    echo '</h2>';
				}
				function wppizza_admin_tm_page_text_header(){
					settings_errors();
					if(!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab']=='timed_items')){
						echo '<h3>'.__('please read the "how to" for instructions and information how to set your timed menu items.', $this->wpptmLocale).'</h3>';
					}

				}
				function wppizza_admin_tm_settings_input($field=''){
					global $pagenow;
					$options=$this->wpptmOptionsPreWpml;
					if ( $pagenow == 'edit.php' && $_GET['page'] == $this->wpptmSlug ){
			    	if (isset ($_GET['tab'])){$tab = $_GET['tab'];}else{$tab = $this->wpptmCurrentUserCaps['tabs'][0];}
						require('inc/admin.echo.settings.input.fields.inc.php');
					}
				}

				function wppizza_admin_manage_tm_validate($input){
					/*initialize options array (i.e on first install), will be overwritten below on post*/
					$newOptions=$input;
					/*update, keep old settings */
			    	if($this->wpptmOptionsPreWpml!=0 && isset($_POST[''.$this->wpptmSlug.''])){
			    		/**validate when posting. set all previous first as we might only be posting some of the vars and dont want to loose any other ones*/
			    		$newOptions = $this->wpptmOptionsPreWpml;
						/*do not use require_once here as it may be used more than once .doh!**/
						require('inc/admin.options.validate.inc.php');
			    	}
					return $newOptions;
				}
				private function wppizza_tm_admin_get_timed_menue($field,$k,$v,$options,$itemsCatsPagesPosts,$display){
					require('inc/admin.echo.get_timed_menu.inc.php');
					return $str;
				}

				/**set capability to  save options**/
    			function wppizza_tm_admin_option_page_capability( $capability ) {
					return $this->wpptmCurrentUserCaps['caps'][0];
				}





				/**get all relevant posts pages and cats so we only have to run these queries onece*/
				function wppizza_tm_admin_items_cats_pages_posts(){
					$masterOptions=get_option(WPPIZZA_SLUG,0);
					$itemsCatsPagesPosts=array();


					/**menu items*/
					$items=array();
					$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1, 'orderby'=>'title' ,'order' => 'ASC');
					$iQuery = new WP_Query( $args );
					foreach($iQuery->posts as $iObj){
						$items[$iObj->ID]=array('id'=>$iObj->ID,'title'=>$iObj->post_title);
					}
					/**add items to array*/
					$itemsCatsPagesPosts['items']=$items;


					/**categories**/
					$categories=array();
					$terms = get_terms(WPPIZZA_TAXONOMY);
					/**********************************************
						get right sort order
					**********************************************/
					$args=array(
						'taxonomy' 			=> WPPIZZA_TAXONOMY,
						'echo'				=> 0,
						'hide_empty'		=> 0,
						'title_li'			=> '',
						'style'				=> 'none',
						'walker'			=> new WPPIZZA_TM_SORTED_CATEGORY_WALKER()
					);
					add_filter('terms_clauses', array($this,'wppizza_tm_term_filter'), '', 1);
					$catsSorted=wp_list_categories($args);
					$catsSorted=array_flip(explode("|",substr($catsSorted,0,-1)));
					foreach($terms as $cObj){
						$ancestors = get_ancestors( $cObj->term_id, WPPIZZA_TAXONOMY );
						$categories[$cObj->term_id]=array('sort'=>$catsSorted[$cObj->term_id],'id'=>$cObj->term_id,'title'=>$cObj->name,'depth'=>count($ancestors));
					}
					/*sort according to sortorder*/
					asort($categories);

					/**add categories to array*/
					$itemsCatsPagesPosts['categories']=$categories;


					/**pages***/
					$pages=array();
						$args = array(
							'sort_order' => 'ASC',
							'sort_column' => 'post_title',
							'hierarchical' => 1,
							'exclude' => '',
							'include' => '',
							'meta_key' => '',
							'meta_value' => '',
							'authors' => '',
							'child_of' => 0,
							'parent' => -1,
							'exclude_tree' => '',
							'number' => '',
							'offset' => 0,
							'post_type' => 'page',
							'post_status' => 'publish'
						);
					$pgs = get_pages($args);
					foreach($pgs as $pgObj){
						$pageArray=$this->wppizza_tm_wppizza_shortcodes_and_attributes($pgObj);
						if(is_array($pageArray)){
							$pages[$pgObj->ID]=$pageArray;
						}

					}

					/*exclude all pages that have no slugs associated**/
					foreach($pages as $pId=>$pObj){
						if(!isset($pObj['catsonpage'])){
							unset($pages[$pId]);
						}
					}
					/**add pages to array*/
					$itemsCatsPagesPosts['pages']=$pages;


					/**posts*/
					$posts=array();

					/**********************************************************************************************************
					*
					*	disabled as it will cause way too many problems further down the line
					*	between custom posts types and normal posts. should really be using pages
					*	anyway or the templates.
					*	furthermore, there may be a LOT of normal blog posts which will only slow things down unacceptably
					*
					*********************************************************************************************************/
				//	$args = array(
				//		'posts_per_page'   => -1,
				//		'offset'           => 0,
				//		'category'         => '',
				//		'orderby'          => 'post_date',
				//		'order'            => 'DESC',
				//		'include'          => '',
				//		'exclude'          => '',
				//		'meta_key'         => '',
				//		'meta_value'       => '',
				//		'post_type'        => 'post',
				//		'post_mime_type'   => '',
				//		'post_parent'      => '',
				//		'post_status'      => 'publish',
				//		'suppress_filters' => true );
				//	$pst = get_posts( $args );
				//	foreach($pst as $pstObj){
				//		if ( has_shortcode( $pstObj->post_content, 'wppizza' ) || has_shortcode( $pstObj->post_excerpt, 'wppizza' )  ) {
				//
				//
				//			/**get shortcode attributes from post_content and exclude anything that has "type" set as we only want categories or default cat*/
				//			if (   preg_match_all( '/'. $shortcodeRegEx .'/s', $pstObj->post_content, $matches ) && array_key_exists( 2, $matches )  && in_array( 'wppizza', $matches[2] ) ) {
        		//				// shortcode is being used get attributes
        		//				$shortcodeAttributes=shortcode_parse_atts($matches[3][0]);
        		//				if(!isset($shortcodeAttributes['type'])){
        		//					$posts[$pstObj->ID]=array('id'=>$pstObj->ID,'title'=>$pstObj->post_title);
        		//				}
    			//			}
				//			/**get shortcode attributes from post_excerpt and exclude anything that has "type" set as we only want categories or default cat*/
				//			if (   preg_match_all( '/'. $shortcodeRegEx .'/s', $pstObj->post_excerpt, $matches ) && array_key_exists( 2, $matches )  && in_array( 'wppizza', $matches[2] ) ) {
        		//				// shortcode is being used get attributes
        		//				$shortcodeAttributes=shortcode_parse_atts($matches[3][0]);
        		//				if(!isset($shortcodeAttributes['type'])){
        		//					$posts[$pstObj->ID]=array('id'=>$pstObj->ID,'title'=>$pstObj->post_title);
        		//				}
    			//			}
				//		}
				//	}
					/**add posts to array*/
					$itemsCatsPagesPosts['posts']=$posts;


				return $itemsCatsPagesPosts;
				}

				/*********************************************************************
				*
				*	[to keep db access on frontend pages to a minimum
				*	we update the options of this plugin as required
				*	on post/cat adds, updates , edits, deletes ]
				*
				**********************************************************************/

				/***************************************************
					[update options if neccessary on category update]
				***************************************************/
				function wppizza_tm_menu_update_option_on_update_category($id){/*update category*/
					$updateVars=array('type'=>'category','action'=>'edit','id'=>$id);
					require('inc/admin.options.update.inc.php');
				}
				function wppizza_tm_menu_update_option_on_reorder_category($id){/*re-order category*/
					$updateVars=array('type'=>'category','action'=>'reorder','id'=>$id);
					require('inc/admin.options.update.inc.php');
				}
				function wppizza_tm_menu_update_option_on_create_category($id){/*re-order category*/
					$updateVars=array('type'=>'category','action'=>'create','id'=>$id);
					require('inc/admin.options.update.inc.php');
				}
				function wppizza_tm_menu_update_option_on_delete_category($id){/*delete category*/
					$updateVars=array('type'=>'category','action'=>'delete','id'=>$id);
					require('inc/admin.options.update.inc.php');
				}
				/***************************************************
					[update options if neccessary on save post/pages]
				***************************************************/
				function wppizza_tm_menu_update_option_on_delete_post($id){
					$updateVars=array('type'=>'post','action'=>'delete','id'=>$id);
					require('inc/admin.options.update.inc.php');
				}
				function wppizza_tm_menu_update_option_on_save_post($id,$obj){
					if($obj->post_type=='post' || $obj->post_type=='page' || $obj->post_type==WPPIZZA_POST_TYPE){
					$updateVars=array('type'=>'savepost','action'=>'addedit','post_status'=>$obj->post_status,'post_type'=>$obj->post_type,'id'=>$id);
					require('inc/admin.options.update.inc.php');
					}
				}

				/*****************************************************
				*
				*	[Register and Enqueue admin scripts and styles]
				*
				******************************************************/
			    function wppizza_tm_register_scripts_and_styles_admin() {
			    		/**css*/
						if (file_exists( $this->wpptmTemplateDir . '/wppizza-tm-admin.css')){
							/**copy stylesheet to template directory to keep settings**/
							wp_register_style($this->wpptmSlug.'-admin', $this->wpptmTemplateUri.'/wppizza-tm-admin.css', array(), $this->wpptmVersion);
		            	}else{
							wp_register_style($this->wpptmSlug, plugins_url( 'css/wppizza-tm-admin.css', __FILE__ ), array(), $this->wpptmVersion);
		            	}
			    		wp_enqueue_style($this->wpptmSlug);
			      		/**js***/
			            wp_register_script($this->wpptmSlug, plugins_url( 'js/scripts.admin.js', __FILE__ ), array(WPPIZZA_SLUG), $this->wpptmVersion ,true);
			            wp_enqueue_script($this->wpptmSlug);
			    }
				/******************
				    [admin ajax call]
				 *******************/
				function wppizza_tm_admin_json(){
					require('ajax/admin-get-json.php');
					die();
				}

			/**************************************************************************************************
			*
			*	[EDD: allow updates to be delivered automatically]
			*	[EDD TO ENABLE AUTOMATIC UPDATES NOTFICATIONS IN WP DASHBOARD.]
			*
			**************************************************************************************************/
				function wppizza_timed_menu_edd(){
					/*include class*/
					if( !class_exists( 'WPPIZZA_EDD_SL_PLUGIN_UPDATER' ) ) {
						require_once(WPPIZZA_PATH.'classes/wppizza.edd.plugin.updater.inc.php');
					}
					/*retrieve our license key from the DB*/
					$license_key=empty($this->wpptmOptions['plugin_data']['license']['key']) ? '' : $this->wpptmOptions['plugin_data']['license']['key'];
					/* setup the updater */
					$edd_updater = new WPPIZZA_EDD_SL_PLUGIN_UPDATER( WPPIZZA_TIMED_MENU_EDD_URL, __FILE__, array(
						'version'		=> WPPIZZA_TIMED_MENU_CURRENT_VERSION, 		// current version number
						'license'		=> $license_key, 	// license key (used get_option above to retrieve from DB)
						'item_name'		=> WPPIZZA_TIMED_MENU_EDD_NAME, 	// name of this plugin
						'author'		=> 'ollybach'  // author of this plugin
						)
					);
				}
			/***************************************************************************
		    *
		    *
		    *	[FRONTEND]
		    *	the bit that shows/excludes timed items from loop if applicable
		    *	from a db/query perspective, it would be easier to set the items that
		    * 	should NOT be displayed, but people would probably get confused in the admin.
		    *	ho hum.
		    *
		    ****************************************************************************/
				/*****************************************************
				*
				*	[Register and Enqueue admin scripts and styles]
				*
				******************************************************/
			    function wppizza_tm_register_scripts_and_styles() {
						/**css*/
						if($this->wpptmOptions['options']['display_as_unavailable']){
							if (file_exists( $this->wpptmTemplateDir . '/wppizza-tm.css')){
								/**copy stylesheet to template directory to keep settings**/
								wp_register_style($this->wpptmSlug, $this->wpptmTemplateUri.'/wppizza-tm.css', array(), $this->wpptmVersion);
		            		}else{
								wp_register_style($this->wpptmSlug, plugins_url( 'css/wppizza-tm.css', __FILE__ ), array(), $this->wpptmVersion);
		            		}
			    			wp_enqueue_style($this->wpptmSlug);

			    			/**if we want to keep all the original css (including future changes) but only want to overwrite some lines , add wppizza-admin-custom.css to your template directory*/
							if (file_exists( $this->wpptmTemplateDir . '/wppizza-tm-custom.css')){
								wp_register_style($this->wpptmSlug.'-custom', $this->wpptmTemplateUri.'/wppizza-tm-custom.css', array(''.$this->wpptmSlug.''), $this->wpptmVersion);
								wp_enqueue_style($this->wpptmSlug.'-custom');
							}
						}
			    }

				/**wraps a "non available" div around any item not available to be able to display it, but make it non clickable**/
				function wppizza_tm_nadiv_before($postId) {
					if(isset($this->wpptmTimedItems['tmItemsHide'][$postId])){
						print"<div id='wppizza_tm_nawrap-".$postId."' class='wppizza_tm_nawrap'><div id='wppizza_tm_na-".$postId."' class='wppizza_tm_na'></div>";
						print"<div id='wppizza_tm_na_inner-".$postId."' class='wppizza_tm_na_inner'><div>".$this->wpptmOptions['localization']['item_na']."</div></div>";
					}
				}
				function wppizza_tm_nadiv_after($postId) {
					if(isset($this->wpptmTimedItems['tmItemsHide'][$postId])){
						print"</div>";
					}
				}

				function wppizza_tm_getitems($args,$args2=null){
					$tmItemsAll=array();/*ini array*/
					$wppizzaCats=array();
					//$wppizzaCatsBySlug=array();

					$tmItems=array('show'=>array(),'hide'=>array());/*ini array*/
					$tmCats=array('show'=>array(),'hide'=>array());/*ini array*/
					$tmPages=array('show'=>array(),'hide'=>array());/*ini array*/
					$tmPosts=array('show'=>array(),'hide'=>array());/*ini array*/

					$wpTime=$this->wpptmCurrentTime;
					$wpDay=date("w",$wpTime);
					$wpDayStart=mktime(0, 0, 0, date("m", $wpTime), date("d", $wpTime), date("Y", $wpTime));
					$wpDayStartplusOne=mktime(0, 0, 0, date("m", $wpTime), date("d", $wpTime)+1, date("Y", $wpTime));


					/**check first if we need to do any calculations at all**/
					if($this->wpptmOptions['timed_items']>0){
						foreach($this->wpptmOptions['timed_items'] as $ti){
							if($ti['enabled'] && ( count($ti['day'])>0 || $ti['start_time']!='' || $ti['end_time']!='' || $ti['start_date']!='' || $ti['end_date']!='')){
								$hasTm=1;
								break;
							}
						}
					}

					/**get all wppizza catagories and post items within provided there's something to do**/
					if(isset($hasTm)){
						$getWppizzaCats = get_terms(WPPIZZA_TAXONOMY);
						$wppizzaCats=array();
						foreach($getWppizzaCats as $wppCats){
							$catItemArgs = array(
								'post_type' => ''.WPPIZZA_POST_TYPE.'',
								'posts_per_page' => -1,
								'tax_query' => array(
									array(
										'taxonomy' => ''.WPPIZZA_TAXONOMY.'',
										'field' => 'id',
										'terms' => $wppCats->term_id,
										'include_children' => false
									)
								)
							);
							$objInCat = new WP_Query( $catItemArgs );
							$postsInCat=array();
							foreach($objInCat->posts as $postInCat){
								$postsInCat[$postInCat->ID]=$postInCat->ID;
							}
							$wppizzaCats[$wppCats->term_id] = array('slug'=>$wppCats->slug,'count'=>count($postsInCat),'postIds'=>$postsInCat);/*get id and count and postIds in cat*/
							//$wppizzaCatsBySlug[$wppCats->slug] = array('id'=>$wppCats->term_id,'count'=>count($postsInCat),'postIds'=>$postsInCat);/*get id and count and postIds in cat*/
						}
					}

					/************************************************************************
					*
					*	for each set timed row, check if it is enabled and has anything set
					*	if so add to array of items , catagories, pages, posts
					*
					************************************************************************/
					foreach($this->wpptmOptions['timed_items'] as $ti){
						/**first check if this is actually enabled and at least one timing item has been set***/
						if($ti['enabled'] && ( count($ti['day'])>0 || $ti['start_time']!='' || $ti['end_time']!='' || $ti['start_date']!='' || $ti['end_date']!='')){
								/**check if days have been set**/
								/**if some days have been set, but today is not in that array set to false*/
								$tmDayApplies=true;
								if(count($ti['day'])>0){
									if(!in_array($wpDay,$ti['day'])){
									$tmDayApplies=false;/*some days are set, but today is not one of them, so item should not be shown*/
									}
								}

								/**check if times have been set**/
								$tmTimeApplies=true;
								if($ti['start_time']!='' || $ti['end_time']!='' ){
									$sTime=explode(":",$ti['start_time']);
									$eTime=explode(":",$ti['end_time']);
									$startTimeSet=$wpDayStart+($sTime[0]*60*60)+($sTime[1]*60);/*lets make a start time timestamp*/
									$endTimeSet=$wpDayStart+($eTime[0]*60*60)+($eTime[1]*60);/*lets make an end time timestamp*/

									/**add one day if both have been set but end time is < start time (therefore the next day)**/
									if($ti['start_time']!='' && $ti['end_time']!='' && $startTimeSet>=$endTimeSet){
										$endTimeSet=$wpDayStartplusOne+($eTime[0]*60*60)+($eTime[1]*60);/*lets make an end time timestamp + one day*/
									}

									/*only start has been set*/
									if($ti['start_time']!='' && $ti['end_time']=='' ){
										if($wpTime<$startTimeSet){
											$tmTimeApplies=false;/*start time is set, but "now" is earlier than start time, so item should not be shown*/
										}
									}
									/*only start has been set*/
									if($ti['start_time']=='' && $ti['end_time']!='' ){
										if($wpTime>$endTimeSet){
											$tmTimeApplies=false;/*end time is set, but "now" is later than end time, so item should not be shown*/
										}
									}
									/*both*/
									if($ti['start_time']!='' && $ti['end_time']!='' ){
										if($wpTime<$startTimeSet || $wpTime>$endTimeSet){
											$tmTimeApplies=false;/*both dates are set, but "now" is not in between the 2, so item should not be shown*/
										}
									}
								}


								/**check if dates have been set**/
								$tmDateApplies=true;
								if($ti['start_date']!='' || $ti['end_date']!='' ){
									$startDateSet=strtotime($ti['start_date'].' 00:00:00');
									$endDateSet=strtotime($ti['end_date'].' 23:59:59');
									/*only start has been set*/
									if($ti['start_date']!='' && $ti['end_date']=='' ){
										if($wpTime<$startDateSet){
											$tmDateApplies=false;/*start date is set, but "now" is earlier than start date, so item should not be shown*/
										}
									}
									/*only start has been set*/
									if($ti['start_date']=='' && $ti['end_date']!='' ){
										if($wpTime>$endDateSet){
											$tmDateApplies=false;/*end date is set, but "now" is later than end date, so item should not be shown*/
										}
									}
									/*both, making sure end is after start*/
									if($ti['start_date']!='' && $ti['end_date']!='' ){
										if($startDateSet<=$endDateSet){
										if($wpTime<$startDateSet || $wpTime>$endDateSet){
											$tmDateApplies=false;/*both dates are set, but "now" is not in between the 2, so item should not be shown*/
										}}
									}
								}



							/***************************************************************************************
								if a page has been selected without ANY items specifically selected, 
								exclude ALL itmes on that page otherwise only the selected item
								and set that page too to being hidden
								ADDED AS OF 1.2
							/****************************************************************************************/
							$selectedItems=$ti['menu_item'];
							if(count($ti['pages'])>0){//count($ti['menu_item'])<=0 && 
								foreach($ti['pages'] as $id=>$pgDetails){
									foreach($pgDetails['catsonpage'] as $catId=>$catDetails){
										/**check if we have a specific item selected that belongs to that page otherwise select all applicable to that page**/
										$intersect=array_intersect_key($catDetails['postIds'],$selectedItems);
										if(count($intersect)>0){
											$ti['menu_item']+=$intersect;
										}else{
											$ti['menu_item']+=$catDetails['postIds'];
										}
									}
								}
							}


							/**********************************************************************
							*
							*	if this particular timed item setting applies,
							*	i.e enabled and between the set times/dates/days,
							*	then ......
							*	.......set items, categories,pages,post (show or hide)
							***********************************************************/
							if($tmDayApplies && $tmTimeApplies && $tmDateApplies){
								$tmItems['show']+=$ti['menu_item'];
								$tmCats['show']+=$ti['categories'];
								$tmPages['show']+=$ti['pages'];
								$tmPosts['show']+=$ti['posts'];
							}else{
								$tmItems['hide']+=$ti['menu_item'];
								$tmCats['hide']+=$ti['categories'];
								$tmPages['hide']+=$ti['pages'];
								$tmPosts['hide']+=$ti['posts'];
							}
						}
					
					}

					/***************************************************************************************
						as we might have three settings of the same item/cat/pages/post for example that 
						apply to different dates/times/days let get the difference to make sure it isn't 
						actually set  to be displayed in one of the settings even though its hidden on the other
						ADDED AS OF 1.2
					****************************************************************************************/
					$tmItems['hide']=array_diff($tmItems['hide'],$tmItems['show']);
					$tmCats['hide']=array_diff($tmCats['hide'],$tmCats['show']);
					$tmPages['hide']=array_diff_key($tmPages['hide'],$tmPages['show']);
					$tmPosts['hide']=array_diff($tmPosts['hide'],$tmPosts['show']);		
					
					$this->wpptmTimedItems=array('wppizzaCats'=>$wppizzaCats,'tmItemsHide'=>$tmItems['hide'], 'tmCatsHide'=>$tmCats['hide'],'tmPagesHide'=>$tmPages['hide'],'tmPostsHide'=>$tmPosts['hide']);
				}



	/********************************************************************************************************
	*
	*
	*	[exclude relevant pages and categories based on timed menu settings from
	*
	*		-	Page Menus
	*		-	Navigation Menus
	*		-	Wppizza Navigation
	*		-	Loop
	*
	********************************************************************************************************/


				/********************************************************************************************************
				*
				*	-	exclude pages from wp pages navigation (widget)
				*	[exclude any pages set (they would only be excludable if they had a wppizza shortcode on it]
				*	[will also exclude a page if no menu items are available on this page]
				*
				********************************************************************************************************/
				function wppizza_tm_exclude_pages($pages) {
					/**check excluded items against page. if a page has all items on it exluded, exclude whole page*/
					$itemsExclude=$this->wpptmTimedItems['tmItemsHide'];

					/**although option1 and option2 should not really be used together, lets attemot to exclude page if a wppizza cat has been excluded**/
					if(isset($this->wpptmTimedItems['tmCatsHide']) && is_array($this->wpptmTimedItems['tmCatsHide'])){
					foreach($this->wpptmTimedItems['tmCatsHide'] as $catId=>$catDet){
						$itemsExclude+=$catDet['posts'];
					}}

					$pgExcludeByItems=array();
					if(isset($this->wpptmOptions['items_on_pages']) && is_array($this->wpptmOptions['items_on_pages'])){
					foreach($this->wpptmOptions['items_on_pages'] as $pgKey=>$iop){
						$getDiff=array_diff($iop, $itemsExclude);
						if(count($getDiff)<=0){
							$pgExcludeByItems[$pgKey]=1;
						}
					}}

					/**add all "normally" excluded (i.e set in admin) pages*/
					$pgExclude=$this->wpptmTimedItems['tmPagesHide'];
					foreach($pages as $k=>$pg){
						if(isset($pgExclude[$pg->ID]) || isset($pgExcludeByItems[$pg->ID])){
							unset($pages[$k]);
						}
					}
					return $pages;
				}

				/********************************************************************************************************
				*
				*		-	exclude any pages/posts set from NAVIGATION MENU(s)
				*	[they would only be excludable if they had a wppizza shortcode on it]
				*	[will also exclude a page if no menu items are available on this page]
				*
				********************************************************************************************************/
				function wppizza_tm_exclude_nav_menu($items,$menu,$args){
					/**check excluded items against page. if a page has all items on it exluded, exclude whole page*/
					$itemsExclude=$this->wpptmTimedItems['tmItemsHide'];
					/**although option1 and option2 should not really be used together, lets attemot to exclude page if a wppizza cat has been excluded**/
					foreach($this->wpptmTimedItems['tmCatsHide'] as $catId=>$catDet){
						$itemsExclude+=$catDet['posts'];
					}

					$pgExcludeByItems=array();
					foreach($this->wpptmOptions['items_on_pages'] as $pgKey=>$iop){
						$getDiff=array_diff($iop, $itemsExclude);
						if(count($getDiff)<=0){
							$pgExcludeByItems[$pgKey]=1;
						}
					}
					// Iterate over the pages to search and destroy
					foreach ( $items as $key => $item ) {
						$postExclude=$this->wpptmTimedItems['tmPostsHide'];
						$pgExclude=$this->wpptmTimedItems['tmPagesHide'];
						if(isset($postExclude[$item->object_id]) || isset($pgExclude[$item->object_id]) || isset($pgExcludeByItems[$item->object_id])){
							unset( $items[$key] );
						}
					}
					return $items;
				}

				/********************************************************************************************************
				*
				*	[exclude terms (i.e cat links) from wppizza nav]
				*	[distinctly set cats, as well as any cats that are now empty because all itmes have been selected
				*	provided $args['hide_empty']==1]
				*
				********************************************************************************************************/
				/*make sure navigation reflects changes too**/
				function wppizza_tm_filter_navigation($args){
					$catsSetToExclude=$this->wpptmTimedItems['tmCatsHide'];

					/***get all *not to show* menu items (if any) and reduce count from cat**/
					foreach($this->wpptmTimedItems['tmItemsHide'] as $iExclId=>$iExclArr){
						foreach($this->wpptmTimedItems['wppizzaCats'] as $catId=>$catArr){
							if(in_array($iExclId,$catArr['postIds'])){
								$this->wpptmTimedItems['wppizzaCats'][$catId]['count']--;
							}
						}
					}

					/**although option1 and option2 should not really be used together, lets attemot to exclude navigation link if a wppizza PAGE has been excluded**/
					foreach($this->wpptmTimedItems['tmPagesHide'] as $pExclId=>$pExclArr){
						foreach($pExclArr['catsonpage'] as $pId=>$pArr){
							$pSlug=$pArr['slug'];
							$pPostIds=$pArr['postIds'];
							foreach($this->wpptmTimedItems['wppizzaCats'] as $catId=>$catArr){
								if($catArr['slug']==$pSlug){
									$catsSetToExclude[$catId]=array('id'=>$catId,'slug'=>$catArr['slug'],'posts'=>$catArr['postIds']);
								}
							}
						}
					}
					/**hmm, maybe*/
					//if(count($this->wpptmTimedItems['tmPagesHide'])>0){
						//$args['hierarchical']=0;
					//}


					/**exclude empty from nav if set to 1**/
					if($args['hide_empty']==1){
						//$catsSetToExclude=$this->wpptmTimedItems['tmCatsHide'];
						$catsExclude=array();
						foreach($catsSetToExclude as $catId=>$catArr){
							$catsExclude[$catId]=$catId;
						}
						if(is_array($args['exclude'])){
							$catsExclude=array_merge($args['exclude'],$catsExclude);
						}
						/**add to cat exclusion array also all cats that do now have a count of 0**/
						foreach($this->wpptmTimedItems['wppizzaCats'] as $catId=>$catArr){
							if($catArr['count']<=0){
								$catsExclude+=array($catId=>$catId);
							}
						}
						$args['exclude'] = $catsExclude ;
					}
					/**if we show the count, we have to use the custom walker as the count is now different to the one in the db**/
					if($args['show_count']==1){
						$args['walker'] = new WPPizza_Timed_Menu_Walker($this->wpptmTimedItems['wppizzaCats']) ;
					}
					return $args;
				}


				/********************************************************************************************************
				*
				*
				*	[filter menu items that are not to be displayed in loop]
				*
				*
				********************************************************************************************************/
				/**add items to exclude array in loop**/
				function wppizza_tm_filter_loop($args){
					/**we  want to display the items but just as unavailbale stop right here**/
					if($this->wpptmOptions['options']['display_as_unavailable']){
						return $args;
					}

					/*ini array*/
					$itemsExclude=array();

					/**add distincly hidden menu items**/
					$itemsExclude+=$this->wpptmTimedItems['tmItemsHide'];

					/**add any previously set to exclude menu items (in shortcode for example)**/
					if($args['post__not_in']!='' && is_array($args['post__not_in'])){
						$itemsExclude+=$args['post__not_in'];
					}

					/***********************************************************
					*
					*	if we have selected (hidden) a whole category which is currently being displayed,
					*	we need to also find all the posts (menu items) in that category
					*
					***********************************************************/
					/**get the cat slug used to display things in this loop*/
					$taxSlug=array();
					if(is_array($args['tax_query'])){
						foreach($args['tax_query'] as $taxQ){
							$taxSlug[]=$taxQ['terms'];
						}
					}

					/**get the cat id and menu items (postIds) of specifically excluded cat id's*/
					foreach($this->wpptmTimedItems['tmCatsHide'] as $catId=>$catDet){
						//$excludedCat=$this->wpptmTimedItems['wppizzaCats'][$catId];
						if(in_array($catDet['slug'],$taxSlug)){
							$itemsExclude+=$catDet['posts'];
						}
					}

					/***********************************************************
					*
					*	if we have selected (hidden) a whole page which is currently being displayed,
					*	we need to also find all the posts used  on that page
					*
					***********************************************************/
					foreach($this->wpptmTimedItems['tmPagesHide'] as $pgId=>$pgDet){
						foreach($pgDet['catsonpage'] as $catId=>$catDet){
							if(in_array($catDet['slug'],$taxSlug)){
								$itemsExclude+=$catDet['postIds'];
							}
						}
					}

					/*set the to be excluded items for this loop **/
					$args['post__not_in']=$itemsExclude;

				return $args;
				}

				/****************************************************************
					display message if no item found
					(although no link should point to this page anymore,
					it might still be indexed by searchengines)
				*****************************************************************/
				function wppizza_tm_currently_not_available($query){
					$count=did_action('wppizza_loop_outside_before');
					if ( (!isset($query->posts) || count($query->posts)<=0) && $count==1){/**only display once*/
						print"".$this->wpptmOptions['localization']['currently_not_available']."";
					}
				}

	/*******************************************************
	*
	*	[WPML : make localizations strings wpml compatible]
	*
	******************************************************/
	function wppizza_tm_wpml_localization() {
		require('inc/wpml.inc.php');
	}

	/********************************************************************************************************
	*
	*	[Helper Function: get wppizza shortcodes and attributes on pages (in excerpts and content)]
	*	$pgObj : post/page object
	*
	********************************************************************************************************/
				function wppizza_tm_wppizza_shortcodes_and_attributes($pgObj){
					$masterOptions=get_option(WPPIZZA_SLUG,0);
					$shortcodeRegEx=get_shortcode_regex();
						$pageArr=false;
						if ( has_shortcode( $pgObj->post_content, WPPIZZA_SLUG ) || has_shortcode( $pgObj->post_excerpt, WPPIZZA_SLUG )  ) {
							/*ini pg array*/
							$pageArr=array('id'=>$pgObj->ID,'title'=>$pgObj->post_title,'catsonpage'=>array());
							/**get shortcode attributes from post_content and exclude anything that has "type" set as we only want categories or default cat*/
							if (   preg_match_all( '/'. $shortcodeRegEx .'/s', $pgObj->post_content, $matches ) && array_key_exists( 2, $matches )  && in_array( WPPIZZA_SLUG, $matches[2] ) ) {
								foreach($matches[3] as $mAttributes){
	        						// shortcode is being used get attributes
	        						$shortcodeAttributes=shortcode_parse_atts($mAttributes);
	        						if(!isset($shortcodeAttributes['type'])){
	        							/*get first if no cat defined in attributes*/
	        							if(!isset($shortcodeAttributes['category'])){
												$termSort=$masterOptions['layout']['category_sort'];
												asort($termSort);
												reset($termSort);
												$firstTermId=key($termSort);
												//get slug and taxonomy from id
												$query=get_term_by('id',$firstTermId,WPPIZZA_TAXONOMY);
											$shortcodeAttributes['category']=$query->slug;
											$term_id=$firstTermId;
											$autoCategory=true;/**catslug has not been set, so its the first one on list*/
	        							}else{
	        								$query=get_term_by('slug',$shortcodeAttributes['category'],WPPIZZA_TAXONOMY);
	        								$term_id=$query->term_id;
	        								$autoCategory=false;
	        							}
	        							/****get associated posts***/
										$catItemArgs = array(
											'post_type' => ''.WPPIZZA_POST_TYPE.'',
											'posts_per_page' => -1,
											'tax_query' => array(
												array(
													'taxonomy' => ''.WPPIZZA_TAXONOMY.'',
													'field' => 'slug',
													'terms' => $shortcodeAttributes['category'],
													'include_children' => false
												)
											)
										);
										$objInCat = new WP_Query( $catItemArgs );
										$postsInCat=array();
										foreach($objInCat->posts as $postInCat){
											$postsInCat[$postInCat->ID]=$postInCat->ID;
										}
	        							$pageArr['catsonpage'][$term_id]=array('id'=>$term_id,'slug'=>$shortcodeAttributes['category'],'autocat'=>$autoCategory,'postIds'=>$postsInCat);
	        						}
								}
    						}
							/**get shortcode attributes from post_excerpt and exclude anything that has "type" set as we only want categories or default cat*/
							if (   preg_match_all( '/'. $shortcodeRegEx .'/s', $pgObj->post_excerpt, $matches ) && array_key_exists( 2, $matches )  && in_array( WPPIZZA_SLUG, $matches[2] ) ) {
								foreach($matches[3] as $mAttributes){
	        						// shortcode is being used get attributes
	        						$shortcodeAttributes=shortcode_parse_atts($mAttributes);
	        						if(!isset($shortcodeAttributes['type'])){
	        							/*get first if no cat defined in attributes*/
	        							if(!isset($shortcodeAttributes['category'])){
												$termSort=$masterOptions['layout']['category_sort'];
												asort($termSort);
												reset($termSort);
												$firstTermId=key($termSort);
												//get slug and taxonomy from id
												$query=get_term_by('id',$firstTermId,WPPIZZA_TAXONOMY);
											$shortcodeAttributes['category']=$query->slug;
											$term_id=$firstTermId;
											$autoCategory=true;/**catslug has not been set, so its the first one on list*/
	        							}else{
	        								$query=get_term_by('slug',$shortcodeAttributes['category'],WPPIZZA_TAXONOMY);
	        								$term_id=$query->term_id;
	        								$autoCategory=false;
	        							}
	        							/****get associated posts***/
										$catItemArgs = array(
											'post_type' => ''.WPPIZZA_POST_TYPE.'',
											'posts_per_page' => -1,
											'tax_query' => array(
												array(
													'taxonomy' => ''.WPPIZZA_TAXONOMY.'',
													'field' => 'slug',
													'terms' => $shortcodeAttributes['category'],
													'include_children' => false
												)
											)
										);
										$objInCat = new WP_Query( $catItemArgs );
										$postsInCat=array();
										foreach($objInCat->posts as $postInCat){
											$postsInCat[$postInCat->ID]=$postInCat->ID;
										}
	        							$pageArr['catsonpage'][$term_id]=array('id'=>$term_id,'slug'=>$shortcodeAttributes['category'],'autocat'=>$autoCategory,'postIds'=>$postsInCat);
	        						}
								}
    						}
						}
					if(count($pageArr['catsonpage'])>0){
						return $pageArr;
					}
					return;
				}

				/****************************************************************
				*
				*	[get/set Template Directories/Uri's. also check for subdir 'wppizza']
				*
				***************************************************************/
				function wppizza_tm_template_paths(){
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

				/*********************************************************************************
				*
				*	[HELPER ]
				*	[changes wppizza custom sort order query to display category navigation in the right order]
				*
				*********************************************************************************/
				function wppizza_tm_term_filter($pieces){
					$masterOptions=get_option(WPPIZZA_SLUG,0);
					$cat=$masterOptions['layout']['category_sort'];
					asort($cat);
					$sort=implode(",",array_keys($cat));
					/*customise order by clause*/
					$pieces['orderby'] = 'ORDER BY FIELD(t.term_id,'.$sort.')';
				return $pieces;
				}

		}
		/*=========================load class====================================================*/
		add_action('plugins_loaded', 'wppizza_load_timed_menu');
		function wppizza_load_timed_menu() {
			$WPPIZZA_TIMED_MENU=new WPPIZZA_TIMED_MENU();
		}

		/****************************************************************************************
		*
		*
		*	[Modify the walker so the navigation reflects count too (if shown)]
		*
		*
		****************************************************************************************/
		class WPPizza_Timed_Menu_Walker extends Walker_Category {
			private $cats = array();
			function __construct($cats )  {
				// get new counter of categories
				$this->cats = $cats;
			}
			/**set the right counter value**/
			function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
				if($this->cats[$element->term_id]['count']>0){
					$elmCount=$this->cats[$element->term_id]['count'];
				}else{
					$elmCount=0;
				}
				$element->category_count=$elmCount;
				parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
			}
		}


		/****************************************************************************************
		*
		*
		*	[Return an explodable string of cat ids / term_id's to be used for sorting]
		*
		*
		****************************************************************************************/
		class WPPIZZA_TM_SORTED_CATEGORY_WALKER extends Walker_Category {

    		function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        		extract($args);
        		$cat_name = esc_attr( $category->name );
        		$cat_name = apply_filters( 'list_cats', $cat_name, $category );
				$termchildren = get_term_children( $category->term_id, $category->taxonomy );

					$output.=''.$category->term_id.'|';
        	}
    	}
	}
}
?>