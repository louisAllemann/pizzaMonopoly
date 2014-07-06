<?php
		/**only run on first install**/
		if(isset($install_options) && $install_options==1){
				/*some lorem ipsum to insert as default description for items**/
				$loremIpsum[0]='Praesent ut massa dolor. Aenean pharetra quam at risus aliquet laoreet posuere ipsum porta.' ;
				$loremIpsum[1]='Integer id lacus sapien, eu porta lectus. Vestibulum justo elit, rutrum a pharetra id, ornare ac est. ' ;
				$loremIpsum[2]='Sed commodo scelerisque magna, eu tempus ante faucibus vitae. Nulla tempus varius ornare. ' ;
				$loremIpsum[3]='Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. ' ;
				$loremIpsum[4]='Praesent non pulvinar neque. Donec ut ante tortor. Fusce sit amet velit eget arcu lobortis imperdiet.' ;
				$loremIpsum[5]='Nunc odio libero, tempor quis mollis eu, gravida vel augue. Aliquam erat volutpat.' ;
				$loremIpsum[6]='Sed neque metus, tincidunt quis fermentum id, rhoncus ut neque. Fusce non metus enim.' ;
				$loremIpsum[7]='Aliquam nec turpis est, id consequat dolor. Etiam rhoncus elementum cursus.' ;
				$loremIpsum[8]='Etiam et dolor turpis, id gravida eros. Ut eu orci nulla. Fusce porta porttitor arcu sed sollicitudin.' ;
				$loremIpsum[9]='Quisque a augue dui, quis venenatis leo. Curabitur bibendum faucibus neque at vehicula. ' ;
				$loremIpsum[10]='Donec feugiat metus vel metus gravida et accumsan tellus pretium. Phasellus tortor sapien, aliquam convallis faucibus non.' ;
				$loremIpsum[11]='Suspendisse potenti. Sed feugiat lectus et odio dignissim at congue libero fermentum.' ;
				$loremIpsum[12]='Sed sodales felis lorem. Nullam eleifend magna eget turpis rutrum ac auctor mauris pharetra.' ;
				$loremIpsum[13]='Aliquam convallis lacinia suscipit. Mauris ac diam enim. Nullam quis lacus odio, et sagittis sem.' ;
				$loremIpsum[14]='Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.' ;
				$loremIpsum[15]='Suspendisse potenti. Pellentesque habitant morbi tristique senectus et netus.' ;
				$loremIpsum[16]='Aenean vitae est arcu, ut ullamcorper dolor.' ;
				$loremIpsum[16]='Sed at tellus quam, in vulputate sem. Ut eu orci nulla. Fusce porta porttitor arcu sed.';
				$loremIpsum[16]='Mauris gravida, nisl a mollis lobortis.';
				$loremIpsum[16]='Phasellus molestie mauris nec sem malesuada rhoncus. Donec volutpat interdum elit.';
				$loremIpsum[16]='Vivamus nisi enim, faucibus ut auctor nec, vulputate vitae nibh. Maecenas scelerisque malesuada risus, sit.';


				/*default sizeoptions/tiers and associated prices**/
				$defaultSizes=array(
					0=>array(
						0=>array('lbl'=>__('regular', $this->pluginLocale),'price'=>'5.99')
					),
					1=>array(
						0=>array('lbl'=>__('small', $this->pluginLocale),'price'=>'4.95'),
						1=>array('lbl'=>__('large', $this->pluginLocale),'price'=>'9.95')
					),
					2=>array(
						0=>array('lbl'=>__('small', $this->pluginLocale),'price'=>'4.95'),
						1=>array('lbl'=>__('medium', $this->pluginLocale),'price'=>'7.45'),
						2=>array('lbl'=>__('large', $this->pluginLocale),'price'=>'9.95')
					),
					3=>array(
						0=>array('lbl'=>__('small', $this->pluginLocale),'price'=>'4.95'),
						1=>array('lbl'=>__('medium', $this->pluginLocale),'price'=>'7.45'),
						2=>array('lbl'=>__('large', $this->pluginLocale),'price'=>'9.95'),
						3=>array('lbl'=>__('xxl', $this->pluginLocale),'price'=>'14.99')
					),
					4=>array(
						0=>array('lbl'=>__('0.25l', $this->pluginLocale),'price'=>'0.99'),
						1=>array('lbl'=>__('0.33l', $this->pluginLocale),'price'=>'1.25'),
						2=>array('lbl'=>__('0.75l', $this->pluginLocale),'price'=>'1.99'),
						3=>array('lbl'=>__('1.00l', $this->pluginLocale),'price'=>'2.25'),
						4=>array('lbl'=>__('1.50l', $this->pluginLocale),'price'=>'2.99'),
					)
				);
				$defaultPrices=array();
				foreach($defaultSizes as $k=>$v){
					foreach($v as $l=>$m){
						$defaultPrices[$k][$l]=$m['price'];
					}
				}

				/*default additives**/
				$defaultAdditives=array(
					0=>array('sort'=>1,'name'=>__('Food coloring', $this->pluginLocale)),
					1=>array('sort'=>2,'name'=>__('Flavor enhancers', $this->pluginLocale)),
					2=>array('sort'=>3,'name'=>__('Preservatives', $this->pluginLocale)),
					3=>array('sort'=>4,'name'=>__('Stabilizers', $this->pluginLocale)),
					4=>array('sort'=>5,'name'=>__('Sweeteners', $this->pluginLocale))
				);

				/********************************************************************************************
				*
				*	[insert default categories and menu items]
				*
				*********************************************************************************************/
							/*************************************
								[categories]
							/*************************************/
							$defaultCategories = array(
								0=>__('Special Offers', $this->pluginLocale),
								1=>__('Pizza', $this->pluginLocale),
								2=>__('Pasta', $this->pluginLocale),
								3=>__('Salads', $this->pluginLocale),
								4=>__('Desserts', $this->pluginLocale),
								5=>__('Beverages', $this->pluginLocale),
								6=>__('Snacks', $this->pluginLocale)
							);
							/*************************************
								[additional pages]
							/*************************************/
							$defaultMainPages = array(
								0=>array('title'=>__('Our Menu', $this->pluginLocale),'shortcode'=>'['.WPPIZZA_SLUG.' noheader="1"]'),
								1=>array('title'=>__('Orders', $this->pluginLocale),'shortcode'=>'['.WPPIZZA_SLUG.' type="orderpage"]')
							);

							/*array to cach/initialize sortorder of categories [inserted into default options below]**/
							$category_sort=array();

							/*************************************
								[add item to categories [linked by key]]
							/*************************************/
							$defaultItems[0] = array(
								array('title'=>__('Special Pizza', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>3,'prices'=>$defaultPrices[3]),'featuredimage'=>'pizza-64.png'),
								array('title'=>__('Great Steak', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>3,'prices'=>$defaultPrices[3]),'featuredimage'=>'steak-64.png'),
								array('title'=>__('Yummy Pudding', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>3,'prices'=>$defaultPrices[3]),'featuredimage'=>'cake-64.png')
							);
							$defaultItems[1] = array(
								array('title'=>__('Pizza A', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$defaultPrices[2]),'featuredimage'=>'pizza-64.png'),
								array('title'=>__('Pizza B', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$defaultPrices[2]),'featuredimage'=>'pizza-64.png'),
								array('title'=>__('Pizza C', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$defaultPrices[2]),'featuredimage'=>'pizza-64.png'),
								array('title'=>__('Pizza D', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1,2),'sizes'=>2,'prices'=>$defaultPrices[2]),'featuredimage'=>''),
								array('title'=>__('Pizza E', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>2,'prices'=>$defaultPrices[2]),'featuredimage'=>''),
								array('title'=>__('Pizza F', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(2,3,4),'sizes'=>2,'prices'=>$defaultPrices[2]),'featuredimage'=>'')
							);
							$defaultItems[2] = array(
								array('title'=>__('Pasta A', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Pasta B', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Pasta C', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Pasta D', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Pasta E', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Pasta F', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>'')
							);
							$defaultItems[3] = array(
								array('title'=>__('Salad A', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Salad B', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Salad C', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Salad D', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Salad E', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Salad F', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>'')
							);
							$defaultItems[4] = array(
								array('title'=>__('Dessert A', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1,2),'sizes'=>0,'prices'=>$defaultPrices[0]),'featuredimage'=>'cake-64.png'),
								array('title'=>__('Dessert B', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>0,'prices'=>$defaultPrices[0]),'featuredimage'=>''),
								array('title'=>__('Dessert C', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1,4),'sizes'=>0,'prices'=>$defaultPrices[0]),'featuredimage'=>''),
								array('title'=>__('Dessert D', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>0,'prices'=>$defaultPrices[0]),'featuredimage'=>''),
								array('title'=>__('Dessert E', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(2,3,4),'sizes'=>0,'prices'=>$defaultPrices[0]),'featuredimage'=>''),
								array('title'=>__('Dessert F', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(),'sizes'=>0,'prices'=>$defaultPrices[0]),'featuredimage'=>'')
							);
							$defaultItems[5] = array(
								array('title'=>__('Drink A', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$defaultPrices[4]),'featuredimage'=>''),
								array('title'=>__('Drink B', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$defaultPrices[4]),'featuredimage'=>''),
								array('title'=>__('Drink C', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$defaultPrices[4]),'featuredimage'=>''),
								array('title'=>__('Drink D', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$defaultPrices[4]),'featuredimage'=>''),
								array('title'=>__('Drink E', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$defaultPrices[4]),'featuredimage'=>''),
								array('title'=>__('Drink F', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(1),'sizes'=>4,'prices'=>$defaultPrices[4]),'featuredimage'=>'')
							);

							$defaultItems[6] = array(
								array('title'=>__('Snack A', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Snack B', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Snack C', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Snack D', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Snack E', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>''),
								array('title'=>__('Snack F', $this->pluginLocale),'descr'=>array_rand($loremIpsum,1),'meta'=>array('add_ingredients'=>false,'additives'=>array(0,1),'sizes'=>1,'prices'=>$defaultPrices[1]),'featuredimage'=>'')
							);




						/**********************************************
						*
						*	[now insert categories and items]
						*
						**********************************************/
							$parent_term = term_exists(''.$this->pluginSlug.''); // array is returned if taxonomy is given
							$parent_term_id = $parent_term['term_id']; // get numeric term id
							$upload_dir = wp_upload_dir();//err, upload dir . doh
							$i=0;
							foreach($defaultCategories as $k=>$v){
								/*insert category*/
								$term=wp_insert_term(
								  ''.$v.'',
								  ''.$this->pluginSlugCategoryTaxonomy.'',
								  array(
								    'description'=> ''.__('Description of', $this->pluginLocale).' '.$v.'',
								    'slug' => sanitize_title($v),
								    'parent'=> $parent_term_id
								  )
								);

								if ( is_wp_error($term) ) {
									echo $term->get_error_message();
								}else{
									/*insert item into category*/
									$j=0;
									foreach($defaultItems[$k] as $iKey=>$items){
										$item = array(
									  	'post_title'    	=> wp_strip_all_tags( $items['title'] ),
									  	'post_content'  	=> $loremIpsum[$items['descr']],
									  	'post_type'     	=> $this->pluginSlug,
									  	'post_status'   	=> 'publish',
									  	'menu_order'	  	=> $j,
									  	'comment_status'	=> 'closed',
									  	'ping_status'		=> 'closed',
									  	'post_category' 	=> array($term['term_id']),
									  	'tax_input'      	=> array(''.$this->pluginSlugCategoryTaxonomy.'' => array($term['term_id']))
										);
										//					  'post_author'   => 1, ? needed ?
										$post_id=wp_insert_post($item);
										/**add meta boxes values**/
										$metaId=update_post_meta($post_id, ''.$this->pluginSlug.'', $items['meta']) ;

										/*add thumbnail/featured image if set and available**/
										if($items['featuredimage']!='' && is_file(WPPIZZA_PATH.'img/'.$items['featuredimage'].'')){
											$image_data = file_get_contents(WPPIZZA_URL.'img/'.$items['featuredimage']);
											$filename = basename($items['featuredimage']);

											if(wp_mkdir_p($upload_dir['path'])){
					    						$file = $upload_dir['path'] . '/' . $filename;
											}else{
					    						$file = $upload_dir['basedir'] . '/' . $filename;
											}
											file_put_contents($file, $image_data);
											$wp_filetype = wp_check_filetype($filename, null );
											$attachment = array(
											   	'post_mime_type' => $wp_filetype['type'],
										    	'guid' => $upload_dir['url'] . '/' .  $filename ,
										    	'post_title' => sanitize_file_name($filename),
										    	'post_content' => '',
										    	'post_status' => 'inherit'
											);
											$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
											require_once(ABSPATH . 'wp-admin/includes/image.php');
											$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
											wp_update_attachment_metadata( $attach_id, $attach_data );

											set_post_thumbnail( $post_id, $attach_id );
										}

									$j++;
									}
									/*add term id to category sort array to be inserted into options table below*/
									$category_sort[$term['term_id']]=$i;
								$i++;
							}}


						/**********************************************
						*
						*	[insert main category pages and order page
						*	and get their corresponding ids to use further down]
						*
						**********************************************/
						foreach($defaultMainPages as $iKey=>$items){
							$item = array(
							  'post_title'    	=> wp_strip_all_tags( $items['title']),
							  'post_content'  	=> $items['shortcode'],
							  'post_name'  		=> sanitize_title_with_dashes($items['title']),
							  'post_type'     	=> 'page',
							  'post_status'   	=> 'publish',
							  'menu_order'	  	=> 0,
							  'post_parent'	  	=> 0,
							  'comment_status'	=> 'closed',
							  'ping_status'		=> 'closed'
							);
							if($iKey==0){
								$postParent=wp_insert_post($item);
							}
							if($iKey==1){
								$orderPageId=wp_insert_post($item);
							}
						}

						/**********************************************
						*
						*	[insert menu category pages]
						*
						**********************************************/
						$i=0;
						foreach($defaultCategories as $iKey=>$items){
							$item = array(
							  'post_title'    	=> wp_strip_all_tags( $items),
							  'post_content'  	=> '['.WPPIZZA_SLUG.' category="'.sanitize_title_with_dashes($items).'" noheader="1"]',
							  'post_name'  		=> sanitize_title_with_dashes($items),
							  'post_type'     	=> 'page',
							  'post_status'   	=> 'publish',
							  'menu_order'	  	=> $iKey,
							  'post_parent'	  	=> $postParent,
							  'comment_status'	=> 'closed',
							  'ping_status'		=> 'closed'
							);
							$post_id=wp_insert_post($item);
						}

		}else{
			/******************************************************************************************
				[as we are updating the pugin, we use the options in table
				as we are not adding any new pages and categories above]
			**************************************************************************************/
			$category_sort=$options['layout']['category_sort'];
			$defaultSizes=$options['sizes'];
			if(!isset($options['additives'])){$options['additives']=array();}
			$defaultAdditives=$options['additives'];
			$orderPageId=$options['order']['orderpage'];
		}
	/****************************************************
	*
	*	[insert default options into options table]
	*
	*****************************************************/
		$defaultOptions = array(
			'plugin_data'=>array(
				'version' => $this->pluginVersion,
				'js_in_footer' => false,
				'mail_type' => 'mail',
				'category_parent_page' => array(),
				'empty_category_and_items' => false,
				'wp_multisite_session_per_site' => true,
				'using_cache_plugin' => false,
				'nag_notice' => $this->pluginNagNotice
			),
			'layout'=>array(
				'category_sort' => $category_sort,
				'category_sort_hierarchy' => $this->wppizza_complete_sorted_hierarchy($category_sort),
				'include_css' => true,
				'css_priority' => 11,
				'style' => 'default',
				'placeholder_img' => true,
				'items_per_loop' => '-1',
				'suppress_loop_headers' => false,
				'hide_cart_icon' => false,
				'hide_prices' => false,
				'hide_item_currency_symbol' => false,
				'hide_single_pricetier' => false,
				'disable_online_order' => false,
				'add_to_cart_on_title_click' => false,
				'hide_decimals' => false,
				'show_currency_with_price' => 0,
				'currency_symbol_left' => false,
				'currency_symbol_position' => 'left',
				'cart_increase' => false,
				'empty_cart_button' => false,
				'prettyPhoto' => false,
				'prettyPhotoStyle' => 'pp_default',
				'items_group_sort_print_by_category' => false,
				'items_category_hierarchy' => 'full',
				'items_category_hierarchy_cart' => 'parent',
				'items_category_separator' => ' &raquo; ',					
				'sticky_cart_animation' =>450,
				'sticky_cart_animation_style' =>'',
				'sticky_cart_margin_top' =>20,
				'sticky_cart_background' =>'inherit',
				'sticky_cart_limit_bottom_elm_id' =>'',
				'jquery_fb_add_to_cart' =>false,
				'jquery_fb_add_to_cart_ms' =>1000
			),
			'opening_times_standard'=>array(
				0=>array('open'=>'14:30','close'=>'01:00'),
				1=>array('open'=>'09:30','close'=>'02:00'),
				2=>array('open'=>'09:30','close'=>'02:00'),
				3=>array('open'=>'09:30','close'=>'02:00'),
				4=>array('open'=>'09:30','close'=>'02:00'),
				5=>array('open'=>'09:30','close'=>'02:00'),
				6=>array('open'=>'09:30','close'=>'02:00')
			),
			'opening_times_custom'=>array(
				'date'=>array(''.date("Y").'-12-25',''.(date("Y")+1).'-01-01'),
				'open'=>array('17:00','17:00'),
				'close'=>array('01:00','01:00')
			),
			'opening_times_format'=>array(
				'hour'=>'G',
				'separator'=>':',
				'minute'=>'i',
				'ampm'=>''
			),
			'times_closed_standard'=>array(
			),
			'order'=>array(
				'currency'=>'GBP',
				'currency_symbol'=>'£',
				'orderpage'=>$orderPageId,
				'orderpage_exclude'=>true,
				'delivery_selected'=>'minimum_total',
				'discount_selected'=>'none',
				'order_pickup' => false,
				'order_pickup_alert' => false,
				'order_pickup_discount' => 0,
				'order_min_for_delivery' => 0,
				'order_pickup_display_location' => 1,
				'delivery'=>array(
					'no_delivery'=>'',
					'minimum_total'=>array('min_total'=>'7.5','deliver_below_total'=>true,'deliverycharges_below_total'=>'0'),
					'standard'=>array('delivery_charge'=>'7.5'),
					'per_item'=>array('delivery_charge_per_item'=>'0','delivery_per_item_free'=>'50')
				),
				'delivery_calculation_exclude_item'=>array(),
				'discounts'=>array(
					'none'=>array(),
					'percentage'=>array(
						'discounts'=>array(
							0=>array('min_total'=>'20','discount'=>'5'),
							1=>array('min_total'=>'50','discount'=>'10')
						)
					),
					'standard'=>array(
						'discounts'=>array(
							0=>array('min_total'=>'20','discount'=>'5'),
							1=>array('min_total'=>'50','discount'=>'10')
						)
					)
				),
				'item_tax'=>0,
				'taxes_included'=>false,
				'shipping_tax'=>false,
				'append_internal_id_to_transaction_id'=>false,
				'order_email_to'=>array(''.get_option('admin_email').''),
				'order_email_bcc'=>array(),
				'order_email_attachments'=>array(),
				'order_email_from'=>'',
				'order_email_from_name'=>'',
				'order_sms'=>''
			),
			'order_form'=>array(
				0=>array('sort'=>0,'key'=>'cname','lbl'=>__('Name :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>true,'required'=>false,'required_on_pickup'=>false,'prefill'=>true,'onregister'=>false),
				1=>array('sort'=>1,'key'=>'cemail','lbl'=>__('Email :', $this->pluginLocale),'value'=>array(),'type'=>'email','enabled'=>true,'required'=>true,'required_on_pickup'=>true,'prefill'=>true,'onregister'=>false),
				2=>array('sort'=>2,'key'=>'caddress','lbl'=>__('Address :', $this->pluginLocale),'value'=>array(),'type'=>'textarea','enabled'=>true,'required'=>true,'required_on_pickup'=>false,'prefill'=>true,'onregister'=>false),
				3=>array('sort'=>3,'key'=>'ctel','lbl'=>__('Telephone :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>true,'required'=>true,'required_on_pickup'=>true,'prefill'=>true,'onregister'=>false),
				4=>array('sort'=>4,'key'=>'ccomments','lbl'=>__('Comments :', $this->pluginLocale),'value'=>array(),'type'=>'textarea','enabled'=>true,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false),
				5=>array('sort'=>5,'key'=>'ccustom1','lbl'=>__('Custom Field 1 :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false),
				6=>array('sort'=>6,'key'=>'ccustom2','lbl'=>__('Custom Field 2 :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false),
				7=>array('sort'=>7,'key'=>'ccustom3','lbl'=>__('Custom Field 3 :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false),
				8=>array('sort'=>8,'key'=>'ccustom4','lbl'=>__('Custom Field 4 :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false),
				9=>array('sort'=>9,'key'=>'ccustom5','lbl'=>__('Custom Field 5 :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false),
				10=>array('sort'=>10,'key'=>'ccustom6','lbl'=>__('Custom Field 6 :', $this->pluginLocale),'value'=>array(),'type'=>'text','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false),
				11=>array('sort'=>11,'key'=>'ctips','lbl'=>__('Tips/Gratuities :', $this->pluginLocale),'value'=>array(),'type'=>'tips','enabled'=>false,'required'=>false,'required_on_pickup'=>false,'prefill'=>false,'onregister'=>false)
			),
			'confirmation_form_enabled'=>false,
			'confirmation_form_amend_order_link'=>'',
			'confirmation_form'=>array(
				0=>array('sort'=>0,'key'=>'wpppizza_confirm_1','lbl'=>__('Accept Terms and Conditions', $this->pluginLocale),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false),
				1=>array('sort'=>1,'key'=>'wpppizza_confirm_2','lbl'=>__('Distance Selling Regulations ', $this->pluginLocale),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false),
				2=>array('sort'=>2,'key'=>'wpppizza_confirm_3','lbl'=>__('Other', $this->pluginLocale),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false),
				3=>array('sort'=>3,'key'=>'wpppizza_confirm_4','lbl'=>__('Other', $this->pluginLocale),'value'=>array(),'type'=>'checkbox','enabled'=>false,'required'=>false)
			),		
			'localization_confirmation_form'=>array(/**make sure keys are NOT used in "normal" localization vars too, as we are merging those two arrays to use in confirmation page */
				'change_user_details'=>array(
					'descr'=>__('Confirmation Form - [labels]: link text to use for link to return to previous page for changing personal details', $this->pluginLocale),
					'lbl'=>__('change', $this->pluginLocale)
				),
				'change_order_details'=>array(
					'descr'=>__('Confirmation Form - [labels]: text and associated link to use to direct customer to a page where he/she can amend the order.', $this->pluginLocale),
					'lbl'=>__('amend order', $this->pluginLocale)
				),						
				'payment_method'=>array(
					'descr'=>__('Confirmation Form - [labels]: label for payment method used', $this->pluginLocale),
					'lbl'=>__('selected payment method :', $this->pluginLocale)
				),		
				'legend_legal'=>array(
					'descr'=>__('Confirmation Form - [section header]: legal aspects', $this->pluginLocale),
					'lbl'=>__('legal aspects', $this->pluginLocale)
				),								
				'legend_personal'=>array(
					'descr'=>__('Confirmation Form - [section header]: personal details', $this->pluginLocale),
					'lbl'=>__('personal information', $this->pluginLocale)
				),								
				'legend_payment_method'=>array(
					'descr'=>__('Confirmation Form - [section header]: payment method', $this->pluginLocale),
					'lbl'=>__('payment method', $this->pluginLocale)
				),	
				'legend_order_details'=>array(
					'descr'=>__('Confirmation Form - [section header]: order details', $this->pluginLocale),
					'lbl'=>__('order details', $this->pluginLocale)
				),	
				'confirm_now_button'=>array(
					'descr'=>__('Confirmation Form - [labels]: label buy now button', $this->pluginLocale),
					'lbl'=>__('buy now (legally binding)', $this->pluginLocale)
				),
				'header_itemised_article'=>array(
					'descr'=>__('Confirmation Form - [itemised header]: article', $this->pluginLocale),
					'lbl'=>__('article', $this->pluginLocale)
				),
				'header_itemised_price_single'=>array(
					'descr'=>__('Confirmation Form - [itemised header]: single price', $this->pluginLocale),
					'lbl'=>__('single price', $this->pluginLocale)
				),
				'header_itemised_quantity'=>array(
					'descr'=>__('Confirmation Form - [itemised header]: quantity', $this->pluginLocale),
					'lbl'=>__('quantity', $this->pluginLocale)
				),
				'header_itemised_price'=>array(
					'descr'=>__('Confirmation Form - [itemised header]: price', $this->pluginLocale),
					'lbl'=>__('price', $this->pluginLocale)
				),
				'subtotals_after_additional_info'=>array(
					'descr'=>__('Confirmation Form - [miscellaneous]: additional/optional info/text to display after (sub)totals', $this->pluginLocale),
					'lbl'=>''
				)						
											
			),							
			'gateways'=>array(
				'gateway_selected'=>array('COD'=>true),
				'gateway_select_as_dropdown'=>false,
				'gateway_select_label'=>__('Please select your payment method:', $this->pluginLocale),
				'gateway_showorder_on_thankyou'=>false
			),
			'sizes'=>$defaultSizes,
			'additives'=>$defaultAdditives,
			'localization'=>array(
				'contains_additives'=>array(
					'descr'=>__('Menu Item: label when hovering over additives (if set)', $this->pluginLocale),
					'lbl'=>__('contains additives', $this->pluginLocale)
				),
				'add_to_cart'=>array(
					'descr'=>__('Menu Item: text to display when hovering over prices', $this->pluginLocale),
					'lbl'=>__('add to cart', $this->pluginLocale)
				),
				'alert_closed'=>array(
					'descr'=>__('Menu Item: alert when trying to add to cart but shop is closed (only displayed when shoppingcart is displayed on page)', $this->pluginLocale),
					'lbl'=>__('sorry, we are currently closed', $this->pluginLocale)
				),
				'alert_choose_size'=>array(
					'descr'=>__('Menu Item: alert when adding to cart by clicking on menu name but more than one size is available. (Only relevant if "Add item to cart on click of *item title* " is enabled)', $this->pluginLocale),
					'lbl'=>__('please choose a size', $this->pluginLocale)
				),
				'jquery_fb_add_to_cart_info'=>array(
					'descr'=>__('Menu Item: text that briefly replaces selected item price when adding item to cart [html allowed]. (Only relevant if "Briefly replace item price with customised text" in WPPizza->Layout is enabled. CSS Class: "wppizza-item-added-feedback")', $this->pluginLocale),
					'lbl'=>__('<div>&#10004;</div>item added', $this->pluginLocale)
				),
				'previous'=>array(
					'descr'=>__('Menu Pagination : previous page', $this->pluginLocale),
					'lbl'=>__('< previous', $this->pluginLocale)
				),
				'next'=>array(
					'descr'=>__('Menu Pagination : next page', $this->pluginLocale),
					'lbl'=>__('next >', $this->pluginLocale)
				),
				'closed'=>array(
					'descr'=>__('Shoppingcart: text to display when shop closed ', $this->pluginLocale),
					'lbl'=>__('currently closed', $this->pluginLocale)
				),
				'empty_cart'=>array(
					'descr'=>__('Shoppingcart: empty cart button text', $this->pluginLocale),
					'lbl'=>__('empty cart', $this->pluginLocale)
				),
				'cart_is_empty'=>array(
					'descr'=>__('Shoppingcart: text to display when cart is empty', $this->pluginLocale),
					'lbl'=>__('your cart is empty', $this->pluginLocale)
				),
				'remove_from_cart'=>array(
					'descr'=>__('Shoppingcart: text to display when hovering over remove from cart icon', $this->pluginLocale),
					'lbl'=>__('remove from cart', $this->pluginLocale)
				),
				'place_your_order'=>array(
					'descr'=>__('Shoppingcart: text of button in cart to proceed to order page', $this->pluginLocale),
					'lbl'=>__('place your order', $this->pluginLocale)
				),
				'order_self_pickup'=>array(
					'descr'=>__('Shoppingcart - Self Pickup: text next to self pickup checkbox (if enabled)', $this->pluginLocale),
					'lbl'=>__('I would like to pickup the order myself', $this->pluginLocale)
				),
				'order_self_pickup_cart'=>array(
					'descr'=>__('Shoppingcart - Self Pickup: text under total value (if selected by customer)', $this->pluginLocale),
					'lbl'=>__('Delivery: pickup', $this->pluginLocale)
				),
				'order_self_pickup_cart_js'=>array(
					'descr'=>__('Shoppingcart - Self Pickup: javascript alert when customer selects self pickup (if enabled)', $this->pluginLocale),
					'lbl'=>__('You have chosen to pickup the order yourself. This order will not be delivered. Please allow 30 min. for us to prepare your order.', $this->pluginLocale)
				),
				'history_no_previous_orders'=>array(
					'descr'=>__('History Page: Text to display when the user has not had any previous orders', $this->pluginLocale),
					'lbl'=>__('you have no previous orders', $this->pluginLocale)
				),					
				'your_order'=>array(
					'descr'=>__('Order Page: label above itemised order', $this->pluginLocale),
					'lbl'=>__('your order', $this->pluginLocale)
				),
				'send_order'=>array(
					'descr'=>__('Order Page: button label for sending order', $this->pluginLocale),
					'lbl'=>__('send order', $this->pluginLocale)
				),
				'order_form_legend'=>array(
					'descr'=>__('Order Page: label above personal info', $this->pluginLocale),
					'lbl'=>__('please enter the required information below', $this->pluginLocale)
				),
				'order_page_self_pickup'=>array(
					'descr'=>__('Order Page - Self Pickup: text on order page / email to highlight self pickup (if applicable)', $this->pluginLocale),
					'lbl'=>__('Note: you have chosen to pickup the order yourself. This order will not be delivered. Please allow 30 min. for us to prepare your order.', $this->pluginLocale)
				),
				'order_page_no_delivery'=>array(
					'descr'=>__('Order Page - No Delivery Offered / Pickup Only: text on order page / email if delivery is not being offered (if applicable)', $this->pluginLocale),
					'lbl'=>__('Please collect your order at the store.', $this->pluginLocale)
				),
				'order_page_handling'=>array(
					'descr'=>__('Order Page [Handling Charges]: text on order page if a handling charge for payment processing has been made (if applicable)', $this->pluginLocale),
					'lbl'=>__('handling charge', $this->pluginLocale)
				),
				'order_page_handling_oncheckout'=>array(
					'descr'=>__('Order Page [Handling Charges]: text on order page if any handling charge will be calculated on checkout by a/the gateway itself', $this->pluginLocale),
					'lbl'=>__('calculated on checkout', $this->pluginLocale)
				),					
				'required_field'=>array(
					'descr'=>__('Order Page: message when required field is missing', $this->pluginLocale),
					'lbl'=>__('this is a required field', $this->pluginLocale)
				),
				'required_field_decimal'=>array(
					'descr'=>__('Order Page: message when field should be a decimal number', $this->pluginLocale),
					'lbl'=>__('decimal numbers only please', $this->pluginLocale)
				),
				'thank_you'=>array(
					'descr'=>__('Order Page: label of thank you page after order has been sent', $this->pluginLocale),
					'lbl'=>__('thank you', $this->pluginLocale)
				),
				'thank_you_p'=>array(
					'descr'=>__('Order Page: text of thank you page after order has been successfully sent', $this->pluginLocale),
					'lbl'=>__('thank you, we have received your order', $this->pluginLocale)
				),
				'thank_you_error'=>array(
					'descr'=>__('Order Page: text on "thank you" page if there was an *error* sending order emails ', $this->pluginLocale),
					'lbl'=>__('Apologies. There was an error receiving your order. Please try again.', $this->pluginLocale)
				),
				'update_profile'=>array(
					'descr'=>__('Order Page: label next to checkbox text to allow user to update profile', $this->pluginLocale),
					'lbl'=>__('update my user data with the details above', $this->pluginLocale)
				),
				'tips'=>array(
					'descr'=>__('Order Page: Tips/Gratuities', $this->pluginLocale),
					'lbl'=>__('tips/gratuities', $this->pluginLocale)
				),
				'tips_ok'=>array(
					'descr'=>__('Order Page: Tips/Gratuities confirm button', $this->pluginLocale),
					'lbl'=>__('ok', $this->pluginLocale)
				),
				'loginout_have_account'=>array(
					'descr'=>__('Order Page [login/logout]: text before login link', $this->pluginLocale),
					'lbl'=>__('already registered ?', $this->pluginLocale)
				),					
				'register_option_label'=>array(
					'descr'=>__('Order Page [register]: text label register or continue as guest', $this->pluginLocale),
					'lbl'=>__('continue as :', $this->pluginLocale)
				),						
				'register_option_guest'=>array(
					'descr'=>__('Order Page [register]: register option -> as guest', $this->pluginLocale),
					'lbl'=>__('guest', $this->pluginLocale)
				),
				'register_option_create_account'=>array(
					'descr'=>__('Order Page [register]: register option -> create account', $this->pluginLocale),
					'lbl'=>__('create account', $this->pluginLocale)
				),										
				'register_option_create_account_info'=>array(
					'descr'=>__('Order Page [register]: additional info when create account option is chosen [html allowed]', $this->pluginLocale),
					'lbl'=>__('Please ensure your email address is correct. A password will be emailed to you.', $this->pluginLocale)
				),
				'register_option_create_account_error'=>array(
					'descr'=>__('Order Page [register]: error if email was already registered [html allowed]', $this->pluginLocale),
					'lbl'=>__('This email address has already been registered. Please either <a href="#login">login</a>, use a different email address or continue as guest.', $this->pluginLocale)
				),	
				'order_details'=>array(
					'descr'=>__('Order Email: label for order details', $this->pluginLocale),
					'lbl'=>__('order details', $this->pluginLocale)
				),
				'order_paid_by'=>array(
					'descr'=>__('Order Email: label to identify how the order was paid for', $this->pluginLocale),
					'lbl'=>__('Paid By:', $this->pluginLocale)
				),
				'order_email_footer'=>array(
					'descr'=>__('Order Email: Text you would like to display at the end of emails after everything else.', $this->pluginLocale),
					'lbl'=>''
				),
				'spend'=>array(
					'descr'=>__('Label Discount (Spend): i.e "spend" 50.00 save 10.00', $this->pluginLocale),
					'lbl'=>__('spend', $this->pluginLocale)
				),
				'save'=>array(
					'descr'=>__('Label Discount (Save): i.e spend 50.00 "save" 10.00', $this->pluginLocale),
					'lbl'=>__('save', $this->pluginLocale)
				),
				'free_delivery_for_orders_of'=>array(
					'descr'=>__('Label Info: i.e. "free delivery for orders over"...', $this->pluginLocale),
					'lbl'=>__('free delivery for orders over', $this->pluginLocale)
				),
				'minimum_order'=>array(
					'descr'=>__('Label Info: required minimum order value (displayed if applicable)', $this->pluginLocale),
					'lbl'=>__('minimum order', $this->pluginLocale)
				),
				'minimum_order_delivery'=>array(
					'descr'=>__('Label Info: required minimum order value for delivery (displayed if applicable)', $this->pluginLocale),
					'lbl'=>__('minimum order for delivery', $this->pluginLocale)
				),
				'free_delivery'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text to display when free delivery applies', $this->pluginLocale),
					'lbl'=>__('free delivery', $this->pluginLocale)
				),
				'delivery_charges'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text delivery charges - when set to "Fixed" or "Free delivery over" (if applicable)', $this->pluginLocale),
					'lbl'=>__('delivery charges', $this->pluginLocale)
				),
				'delivery_charges_per_item'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text delivery when set to "Delivery Charges per item" (if applicable)', $this->pluginLocale),
					'lbl'=>__('delivery charges per item', $this->pluginLocale)
				),
				'discount'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text before sum of discounts applied(if any)', $this->pluginLocale),
					'lbl'=>__('discount', $this->pluginLocale)
				),
				'item_tax_total'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text before sum of tax applied to all items(if > 0)', $this->pluginLocale),
					'lbl'=>__('Sales Tax', $this->pluginLocale)
				),
				'taxes_included'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text before sum of tax applied if prices have been entered *inclusive* of tax (if > 0) [%s%% will be replaced by taxrate applied - i.e 10%]', $this->pluginLocale),
					'lbl'=>__('incl. tax at %s%%', $this->pluginLocale)
				),	
				'order_total'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text before total sum of ORDER', $this->pluginLocale),
					'lbl'=>__('total', $this->pluginLocale)
				),
				'order_items'=>array(
					'descr'=>__('Price Labels (Sub)Totals: text before total sum of ITEMS in cart', $this->pluginLocale),
					'lbl'=>__('your items', $this->pluginLocale)
				),
				'openinghours_closed'=>array(
					'descr'=>__('Openinghours: text to display when shop is closed that day ', $this->pluginLocale),
					'lbl'=>__('closed', $this->pluginLocale)
				)
			)
		);

/********************************************************
	[set initial admin access to plugin pages/tabs]
********************************************************/
	/**********************
		if the default cap vars have never been set before, do it now (one time only)
		essentially, every user role that has manage_options caps will get all cpas for this
		plugin added to start off with, after which they can be edited in the acees rights tab
		(provided the user has access to that tab of course)
	**********************/
	if(!isset($options['admin_access_caps'])){	
		global $wp_roles;
		$wppizzaCaps=$this->wppizza_set_capabilities();

		/*get all roles that have manage_options capabilities**/
		$defaultAdmins=array();
		foreach($wp_roles->roles as $rName=>$rVal){
			if(isset($rVal['capabilities']['manage_options'])){
				$defaultAdmins[$rName]=$rName;
			}
		}
		/**foreach of these, add all capabilities**/
		$setCaps=array();
		foreach($defaultAdmins as $k=>$roleName){
			$userRole = get_role($roleName);
			foreach($wppizzaCaps as $akey=>$aVal){
				$setCaps[$k][]=$aVal['cap'];
				$userRole->add_cap( ''.$aVal['cap'].'' );
			}
		}
		/**set a variable so we do not overwrite it in future updates*/
		/*might as well save the role->caps array. might come in handy one day**/
		$defaultOptions['admin_access_caps']=$setCaps;
	}else{
		global $wp_roles;
		/******************************************
			[check for newly added capabilities
			end enable for roles that have
			ALL previous caps set]
		******************************************/
		$wppizzaCaps=$this->wppizza_set_capabilities();
		
		$capsAvailable=array();
		foreach($wppizzaCaps as $caps){
			$capsAvailable[]=$caps['cap'];	
		}		
		/**make an array with all unique roles**/
		$previousCaps=array();
		foreach($options['admin_access_caps'] as $rName=>$rVal){
			foreach($rVal as $cap){
				$previousCaps[$cap]=$cap;
			}
		}
		/**count number of prev caps**/
		$prevCapsCount=count($previousCaps);
		
		/**get newly added caps**/
		$newCaps=array_diff($capsAvailable,$previousCaps);
		
		/**if there are new caps add them**/
		if(is_array($newCaps) && count($newCaps)>0){
			/*get all roles that had ALL previous caps enabled and add this new one**/
			foreach($wp_roles->roles as $rName=>$rVal){
				$userRole = get_role($rName);
				$capsCount=0;
				foreach($previousCaps as $pCaps){
					if(isset($rVal['capabilities'][$pCaps])){
						$capsCount++;	
					}
				}
				/***role has ALL previous caps, add new ones***/
				if($capsCount==$prevCapsCount){
					foreach($newCaps as $nCap){
						$userRole->add_cap($nCap);
						/***add to options too**/
						$options['admin_access_caps'][$rName][]=$nCap;				
					}
				}
			}
		}
		$defaultOptions['admin_access_caps']=$options['admin_access_caps'];
	}
?>