<?php
/***************************************************************************************************************

		[validate things]

**************************************************************************************************************/

		/*************************************************
		*
		*	[plugin data- version and nag->always update]
		*
		*************************************************/
		$newOptions['plugin_data']['version'] = $this->wpptmVersion;
		$newOptions['plugin_data']['nag_notice'] = isset($input['plugin_data']['nag_notice']) ? $input['plugin_data']['nag_notice'] : $this->wpptmOptionsPreWpml['plugin_data']['nag_notice'];
		/*first install*/
		if($this->wpptmOptionsPreWpml==0){
			$newOptions['plugin_data']['display_type'] = '';
		}else{
			$newOptions['plugin_data']['display_type'] = !empty($this->wpptmOptionsPreWpml['plugin_data']['display_type']) ? $this->wpptmOptionsPreWpml['plugin_data']['display_type'] : '' ;
		}
		/*******************************
		*
		*	[edd settings]
		*
		*******************************/
		if(isset($input['license'])){
			if(defined('WPPIZZA_TIMED_MENU_EDD_NAME')){
				$currentLicense=$newOptions['plugin_data']['license'];/**array of key,status,error**/
				$newKey=wppizza_validate_string($input['license']['key']);
				$action	=!empty($input['license']['action']) ? $input['license']['action'] : '';
				/**de/re/activate/update***/
				$newOptions['plugin_data']['license']=$this->wpptmEdd->edd_toggle($currentLicense,$newKey,$action,WPPIZZA_TIMED_MENU_EDD_NAME,WPPIZZA_TIMED_MENU_EDD_URL);
			}
		}

		/*******************************
		*
		*	[access settings]
		*
		*******************************/
		if(isset($input['admin_access_caps'])){
			$newOptions['admin_access_caps']=$this->wpptmUserCaps->user_validate_admin_caps($this->wppizza_tm_caps(),$this->wpptmOptionsPreWpml['admin_access_caps'],$input['admin_access_caps']);
		}
		/*******************************
		*
		*	[options]
		*
		*******************************/
		if(isset($input['options'])){
				$newOptions['options']['display_as_unavailable'] = !empty($input['options']['display_as_unavailable']) ? true : false;
		}

		/*******************************
		*
		*	[timed menu items]
		*
		*******************************/
		if(isset($input['timed_items_ctrl'])){
				$newOptions['plugin_data']['display_type'] = wppizza_validate_string($input['plugin_data']['display_type']);

				/**get associated posts ids etc with cat's and pages to use further down*/
				$itemsCatsPagesPosts=$this->wppizza_tm_admin_items_cats_pages_posts();

				$newOptions['timed_items'] = array();//initialize array
				if(isset($input['timed_items'])){
				foreach($input['timed_items'] as $k=>$v){
					/*internal or posts_pages varsion*/
					$setUsage=wppizza_validate_string($v['display']);

					$newOptions['timed_items'][$k]['label']=wppizza_validate_string($v['label']);
					$newOptions['timed_items'][$k]['start_date']=!empty($v['start_date']) ? wppizza_validate_date($v['start_date'],'Y-m-d') : '';
					$newOptions['timed_items'][$k]['end_date']=!empty($v['end_date']) ? wppizza_validate_date($v['end_date'],'Y-m-d') : '';
					$newOptions['timed_items'][$k]['start_time']=!empty($v['end_time']) ? wppizza_validate_24hourtime($v['start_time']) : '';
					$newOptions['timed_items'][$k]['end_time']=!empty($v['end_time']) ? wppizza_validate_24hourtime($v['end_time']) : '';
					$newOptions['timed_items'][$k]['day']=array();
					if(isset($v['day'])){
						foreach($v['day'] as $day){
							$newOptions['timed_items'][$k]['day'][(int)$day]=(int)$day;
						}
					}
					$newOptions['timed_items'][$k]['menu_item']=array();
					if(isset($v['menu_item'])){
						foreach($v['menu_item'] as $menu_item){
							$newOptions['timed_items'][$k]['menu_item'][(int)$menu_item]=(int)$menu_item;
						}
					}

					/***************************************************************************
					*	save selected categories. saveing the associated menu items as 'posts'
					*	or slugs here. However, as they might get changed after setting
					*	the timed items we have to re-check on save_post, deleted_term_taxonomy etc
					***************************************************************************/
					$newOptions['timed_items'][$k]['categories']=array();
					if($setUsage=='internal'){/**only set if template usage*/
						if(isset($v['categories'])){
							foreach($v['categories'] as $categories){
								$getDetails=get_term_by('id',$categories,WPPIZZA_TAXONOMY);
								/**get associated posts**/
								$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1,'tax_query' => array(array('taxonomy' => ''.WPPIZZA_TAXONOMY.'','field' => 'id','terms' => ''.$categories.'','include_children' => false)));
								$getPostsQuery = new WP_Query( $args );
								$catPosts=array();
								foreach($getPostsQuery->posts as $posts){
									$catPosts[$posts->ID]=$posts->ID;
								}
								$newOptions['timed_items'][$k]['categories'][(int)$categories]=array('id'=>$categories,'slug'=>$getDetails->slug,'posts'=>$catPosts);

								/**also exclude children as we are hierarchical*/
								$termchildren = get_term_children( $categories, WPPIZZA_TAXONOMY );
								if(! is_wp_error( $termchildren )){
									foreach($termchildren as $tChild){
										$getDetails=get_term_by('id',$tChild,WPPIZZA_TAXONOMY);
										/**get associated posts**/
										$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1,'tax_query' => array(array('taxonomy' => ''.WPPIZZA_TAXONOMY.'','field' => 'id','terms' => ''.$tChild.'','include_children' => false)));
										$getPostsQuery = new WP_Query( $args );
										$catPosts=array();
										foreach($getPostsQuery->posts as $posts){
											$catPosts[$posts->ID]=$posts->ID;
										}
										$newOptions['timed_items'][$k]['categories'][$tChild]=array('id'=>$tChild,'slug'=>$getDetails->slug,'posts'=>$catPosts);
									}
								}
							}
						}
					}

					$newOptions['timed_items'][$k]['pages']=array();
					if($setUsage!='internal'){/**only set if posts_page usage*/
						if(isset($v['pages'])){
							foreach($v['pages'] as $pages){
								//$newOptions['timed_items'][$k]['pages'][(int)$pages]=array('id'=>$pages,'cats'=>array('slug'=>'','postIds'=>array()));
								$newOptions['timed_items'][$k]['pages'][$pages]=$itemsCatsPagesPosts['pages'][$pages];
							}
						}
					}


					/***********posts are currently disabled , but my be used in the future maybe ?...*/
					$newOptions['timed_items'][$k]['posts']=array();
					if($setUsage!='internal'){/**only set if posts_page usage*/
						if(isset($v['posts'])){
							foreach($v['posts'] as $posts){
								//$newOptions['timed_items'][$k]['posts'][(int)$posts]=(int)$posts;
								$newOptions['timed_items'][$k]['posts'][$posts]=$itemsCatsPagesPosts['posts'][$posts];

							}
						}
					}
					$newOptions['timed_items'][$k]['display']=$setUsage;
					$newOptions['timed_items'][$k]['enabled'] = !empty($v['enabled']) ? true : false;
				}}


				/********************************************************************
				*
				*	[additionally save an array of all wppizza items that are used
				*	(by shortcode)on a per page basis so we can hide pages in navigation
				*	if all menu items on that page should have been selected/hidden
				********************************************************************/
				$newOptions['items_on_pages']=array();
				foreach($itemsCatsPagesPosts['pages'] as $pgId=>$pgArr){
					$newOptions['items_on_pages'][$pgId]=array();
					foreach($pgArr['catsonpage'] as $catId=>$catArr){
						$newOptions['items_on_pages'][$pgId]+=$catArr['postIds'];
					}
					if(count($newOptions['items_on_pages'][$pgId])<=0){
						unset($newOptions['items_on_pages'][$pgId]);
					}
				}
		}

		/****************************
		*
		*	[localization]
		*
		****************************/
		if(isset($input['localization'])){
		$newOptions['localization'] = array();
		foreach($input['localization'] as $a=>$b){
			if($a=='currently_not_available'){/*allow html*/
				$newOptions['localization'][$a]=wppizza_validate_string($b,true);
			}else{
				$newOptions['localization'][$a]=wppizza_validate_string($b);
			}
		}}
?>