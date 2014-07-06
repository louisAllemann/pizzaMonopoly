<?php
/***************************************************************************************************************
*
*		[to save us running unnecessary queries on the frontend
*		lets update the option table with the relevant data
*		whenever a WPPIZZA post (ignoring ALL OTHER POSTS - cpt or otherwise - though), page or wppizza category gets updated]
*
**************************************************************************************************************/
		/*all current options*/
		$newOptions= $this->wpptmOptions;

		/*******************************
		*
		*	[current timed menu items]
		*
		*******************************/
		/*all current timed menu options*/
		$tmOptions=$this->wpptmOptions['timed_items'];


		/****************************************************************
		*
		*	[get the relevant item BEFORE looping]
		*	really no need to do this multiple times
		*
		****************************************************************/
		/****
			a category has been updated -> get slug
			and update in loop below if set
		****/
		if($updateVars['type']=='category' && $updateVars['action']=='edit' ){
			$getDetails=get_term_by('id',$updateVars['id'],WPPIZZA_TAXONOMY);
			$catSlug=$getDetails->slug;
		}

		/*************************************************
		*
		*	[amend timed menu items/cats etc where required]
		*
		*************************************************/

		$newOptions['timed_items'] = array();//initialize array

		foreach($tmOptions as $k=>$v){
			/**get current options, but overwrite where necessary**/
			$newOptions['timed_items'][$k]=$v;

			/****************************************************************
			*
			*	[WPPIZZA CATEGORIES EDITED/DELETED]
			*
			*****************************************************************/
			/*category edited -> update slug*/
			if($updateVars['type']=='category' && $updateVars['action']=='edit' ){
				/*update category slug*/
				if(isset($newOptions['timed_items'][$k]['categories'][$updateVars['id']])){
					$newOptions['timed_items'][$k]['categories'][$updateVars['id']]['slug']=$catSlug;
				}
			}
			/*categories reordered*/
			if($updateVars['type']=='category' && ($updateVars['action']=='reorder' || $updateVars['action']=='create')){
				/**if autocat (i.e first category is now different update the page that has one on it*/
				foreach($newOptions['timed_items'][$k]['pages'] as $pgId=>$pgArr){
					foreach($pgArr['catsonpage'] as $catsId=>$catsOnPage){
						if($catsOnPage['autocat']){
							/**unset old**/
							unset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$catsId]);
							/**set new**/
							$query=get_term_by('id',$updateVars['id'],WPPIZZA_TAXONOMY);
							/**get associated posts**/
							$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1,'tax_query' => array(array('taxonomy' => ''.WPPIZZA_TAXONOMY.'','field' => 'id','terms' => ''.$updateVars['id'].'','include_children' => false)));
							$getPostsQuery = new WP_Query( $args );
							$catPosts=array();
							foreach($getPostsQuery->posts as $posts){
								$catPosts[$posts->ID]=$posts->ID;
							}
							$newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$updateVars['id']]=array('id'=>$updateVars['id'],'slug'=>$query->slug,'autocat'=>true,'postIds'=>$catPosts);
						}
					}
				}

			}
			/*category deleted*/
			if($updateVars['type']=='category' && $updateVars['action']=='delete' ){
				/*unset cats*/
				if(isset($newOptions['timed_items'][$k]['categories'][$updateVars['id']])){
					unset($newOptions['timed_items'][$k]['categories'][$updateVars['id']]);
				}
				/*unset cat on pages*/
				foreach($newOptions['timed_items'][$k]['pages'] as $pgId=>$pgArr){
					foreach($pgArr['catsonpage'] as $catsId=>$catsOnPage){
						if($catsId==$updateVars['id']){
							unset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$updateVars['id']]);
						}
					}
				}
			}
			/****************************************************************
			*
			*	[WPPIZZA POSTS(MENU ITMES) EDITED/DELETED]
			*
			*****************************************************************/
			/*post deleted*/
			if($updateVars['type']=='post' && $updateVars['action']=='delete' ){
				/**delete from menu items**/
				if(isset($newOptions['timed_items'][$k]['menu_item'][$updateVars['id']])){
					unset($newOptions['timed_items'][$k]['menu_item'][$updateVars['id']]);
				}
				/**delete from set categories**/
				foreach($newOptions['timed_items'][$k]['categories'] as $catId=>$catArr){
					if(isset($catArr['posts'][$updateVars['id']])){
						unset($newOptions['timed_items'][$k]['categories'][$catId]['posts'][$updateVars['id']]);
					}
				}
				/**delete from set pages**/
				foreach($newOptions['timed_items'][$k]['pages'] as $pgId=>$pgArr){
					foreach($pgArr['catsonpage'] as $catsId=>$catsOnPage){
						if(isset($catsOnPage['postIds'][$updateVars['id']])){
							unset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$catsId]['postIds'][$updateVars['id']]);
						}
					}
				}
			}
			/*post/pages add/edit*/
			if($updateVars['type']=='savepost' && $updateVars['action']=='addedit'){

				/*get associated categories*/
				$terms = get_the_terms($updateVars['id'], WPPIZZA_TAXONOMY);
				$associatedCatIds=array();
				if ($terms && ! is_wp_error($terms)){
					foreach ($terms as $term) {
						$associatedCatIds[$term->term_id]= $term->term_id;
					}
				}


				/*we are editing/updateing/saving a wppizza post item*/
				if($updateVars['post_type']==WPPIZZA_POST_TYPE){
					foreach($newOptions['timed_items'][$k]['pages'] as $pgId=>$pgArr){
						foreach($pgArr['catsonpage'] as $catsId=>$catsOnPage){
							/**first always remove post to ensure that when we are just de-selecting a category from a post, it does not stay part of this*/
							if(isset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$catsId]['postIds'][$updateVars['id']])){
								unset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$catsId]['postIds'][$updateVars['id']]);
							}
							foreach($associatedCatIds as $acId){
								/*only amend categories associated with this page*/
								if(isset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$acId])){
									/*add or remove, depending on publish status*/
									if($updateVars['post_status']=='publish' && !isset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$acId]['postIds'][$updateVars['id']])){
										$newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$acId]['postIds'][$updateVars['id']]=$updateVars['id'];
									}
									if($updateVars['post_status']!='publish' && isset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$acId]['postIds'][$updateVars['id']])){
										unset($newOptions['timed_items'][$k]['pages'][$pgId]['catsonpage'][$acId]['postIds'][$updateVars['id']]);
									}
								}
							}
						}
					}
				}

				/*we are editing/updateing/saving a page*/
				if($updateVars['post_type']=='page'){
						$pages=array();
						$args = array(
							'include' => ''.$updateVars['id'].'',
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
					/*if not post type publish, this will be an empty array so we just unset*/
					if(isset($pages[$updateVars['id']])){
						$newOptions['timed_items'][$k]['pages'][$updateVars['id']]=$pages[$updateVars['id']];
					}else{
						unset($newOptions['timed_items'][$k]['pages'][$updateVars['id']]);
					}
				}

				/*we are editing/updateing/saving a normal post -> lets just skip that . shouldn't be using posts anyway.*/
				if($updateVars['post_type']=='post'){

				}
			}
		}

		/********************************************************************
		*
		*	[additionally ALWAYS save an array of all wppizza items that are used
		*	(by shortcode)on a per page basis so we can hide pages in navigation
		*	if all menu items on that page should have been selected/hidden
		*
		********************************************************************/
		$itemsCatsPagesPosts=$this->wppizza_tm_admin_items_cats_pages_posts();
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
update_option($this->wpptmOptionsName, $newOptions );
?>