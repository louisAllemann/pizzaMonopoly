<?php
/****************************************************
*
*	[insert default options into options table]
*
*****************************************************/
		$ingredient=array();
		$ingredient[]=__('Anchovies', $this->pluginLocale);
		$ingredient[]=__('Artichokes', $this->pluginLocale);
		$ingredient[]=__('Asparagus', $this->pluginLocale);
		$ingredient[]=__('Broccoli', $this->pluginLocale);
		$ingredient[]=__('Egg', $this->pluginLocale);
		$ingredient[]=__('extra Cheese', $this->pluginLocale);
		$ingredient[]=__('extra Garlic', $this->pluginLocale);
		$ingredient[]=__('extra spicy', $this->pluginLocale);
		$ingredient[]=__('fresh Mushrooms', $this->pluginLocale);
		$ingredient[]=__('fresh Tomatoes', $this->pluginLocale);
		$ingredient[]=__('Goats Cheese', $this->pluginLocale);
		$ingredient[]=__('Gorgonzola', $this->pluginLocale);
		$ingredient[]=__('Ham', $this->pluginLocale);
		$ingredient[]=__('Jalapeño', $this->pluginLocale);
		$ingredient[]=__('Kapers', $this->pluginLocale);
		$ingredient[]=__('Mozarella', $this->pluginLocale);
		$ingredient[]=__('Olives', $this->pluginLocale);
		$ingredient[]=__('Onions', $this->pluginLocale);
		$ingredient[]=__('Parmaham', $this->pluginLocale);
		$ingredient[]=__('Parmesancheese', $this->pluginLocale);
		$ingredient[]=__('Pineapple', $this->pluginLocale);
		$ingredient[]=__('Rocket', $this->pluginLocale);
		$ingredient[]=__('Salami', $this->pluginLocale);
		$ingredient[]=__('Scampi', $this->pluginLocale);
		$ingredient[]=__('Spinach', $this->pluginLocale);
		$ingredient[]=__('Sweetcorn', $this->pluginLocale);
		$ingredient[]=__('Tuna', $this->pluginLocale);


		/*************************************************************
			[insert default ingredients of first available meal size]
			[size prices are first=50 further add multiples of 0.35]
		*************************************************************/
		$firstPrice=0.50;
		$multiple=0.35;
		$masterOptions=get_option(WPPIZZA_SLUG);
		$masterOptionSizes=$masterOptions['sizes'];
		reset($masterOptionSizes);
		$first_key = key($masterOptionSizes);
		$numberOfSizes=count($masterOptionSizes[$first_key]);

		/**create array of default ingredients**/
		$defaultIngredients=array();
		foreach($ingredient as $k=>$item){
			$defaultIngredients[$k]['sizes']=$first_key;
			$defaultIngredients[$k]['item']=$item;
			$defaultIngredients[$k]['prices']=array();
			for($i=0;$i<(int)$numberOfSizes;$i++){
				if($i==0){$price=$firstPrice;}
				else{$price=($firstPrice+($i*$multiple));}
				$price=sprintf("%.2f", round( $price, 2));


				$defaultIngredients[$k]['prices'][]=$price;
			}
			$defaultIngredients[$k]['enabled']=true;
		}
		asort($defaultIngredients);


		/********************************************************
			[set options of this plugin]
		********************************************************/
		$defaultOptionsThisPlugin['plugin_data'] = array(
				'version' => $this->pluginVersion,
				'nag_notice' => $this->pluginNagNotice
		);

		/********************************************************
			[set license defaults of this plugin]
		********************************************************/
		$defaultOptionsThisPlugin['license'] = array(
				'key' => '',
				'enabled' => false
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
			$wppizzaAddIngrCaps=$this->wppizza_ingredients_capabilities_tabs();

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
				foreach($wppizzaAddIngrCaps as $akey=>$aVal){
					$setCaps[$k][]=$aVal['cap'];
					$userRole->add_cap( ''.$aVal['cap'].'' );
				}
			}
			/**set a variable so we do not overwrite it in future updates*/
			/*might as well save the role->caps array. might come in handy one day**/
			$defaultOptionsThisPlugin['admin_access_caps']=$setCaps;


		}
		else{
			$defaultOptionsThisPlugin['admin_access_caps']=$options['admin_access_caps'];
		}

		/********************************************************
			[set settings of this plugin]
		********************************************************/
		$defaultOptionsThisPlugin['settings'] = array(
				'price_show_if_zero' => true,
				'price_localize_if_zero' => ''
		);
		/********************************************************
			[set settings of this plugin]
		********************************************************/
		$defaultOptionsThisPlugin['options'] = array(
				'ingredients_in_popup' => false,
				'ingredients_in_popup_wpc' => '100',
				'ingredients_in_popup_anim' => '500',
				'ingredients_added_sticky' => false,
				'ingredients_addasis_button_enabled' => false,	
				'ingredients_show_count' => false,
				'ingredients_omit_single_count' => false,	
				'ingredients_added_sort_by_price'=>false,
				'ingredients_added_show_price'=> false,
				'ingredients_added_show_price_no_zero'=>false,
				'ingredients_added_zero_price_txt'=>'',
				'ingredients_show_depreselected'=>false,
				'ingredients_show_depreselected_after'=>false,
				'ingredients_show_depreselected_prefix'=>__('No', $this->pluginLocale)
		);


		/********************************************************
			[add ingredients to master plugin options]
		********************************************************/
		$defaultOptionsThisPlugin['ingredients']=$defaultIngredients;

		/********************************************************
			[add empty array to be filled as required]
		********************************************************/
		$defaultOptionsThisPlugin['ingredients_custom_groups']=array();

		/********************************************************
			[localization variables]
			[we only want to send the lbl to the db]
			[descr should be available as gettext]
			[it just makes it easier to have it all in one place]
		********************************************************/
		$thisPluginLocalization['localization']=array(
			'max_ingredients'=>array('lbl'=>__('Sorry, you have reached tha maximum number of ingredients you can add from this group', $this->pluginLocale),'descr'=>__('alert when trying to add more than the allowed maximum number of *distinct ingredients* for a group', $this->pluginLocale)),
			'max_same_ingredients'=>array('lbl'=>__('Sorry, you cannot add any more of this particular ingredient', $this->pluginLocale),'descr'=>__('alert when trying to add more than the allowed maximum number of *the same ingredient*', $this->pluginLocale)),
			'add_ingredients'=>array('lbl'=>__('add ingredients', $this->pluginLocale),'descr'=>__('label for container that holds all selectable ingredients when shown', $this->pluginLocale)),
			'no_extra_ingredients'=>array('lbl'=>__('no extra ingredients', $this->pluginLocale),'descr'=>__('text to show when no ingredient has been added to item', $this->pluginLocale)),
			'total'=>array('lbl'=>__('total', $this->pluginLocale),'descr'=>__('label before total price of baseprice plus any added ingredients', $this->pluginLocale)),
			'ingredients_for'=>array('lbl'=>__('ingredients for', $this->pluginLocale),'descr'=>__('label for ingredients price tier (i.e. "ingredients for" 0.50)', $this->pluginLocale)),
			'cancel_add_ingredients'=>array('lbl'=>__('cancel', $this->pluginLocale),'descr'=>__('text shown on hover over icon for cancelling adding ingredients', $this->pluginLocale)),
			'add'=>array('lbl'=>__('add', $this->pluginLocale),'descr'=>__('text shown on hover over icon used for adding ingredients', $this->pluginLocale)),
			'remove'=>array('lbl'=>__('remove', $this->pluginLocale),'descr'=>__('text shown on hover over icon used for removing already added ingredients', $this->pluginLocale)),
			'add_to_cart'=>array('lbl'=>__('add to cart', $this->pluginLocale),'descr'=>__('button label adding to cart when finished adding ingredients', $this->pluginLocale)),
			'required_ingredient_missing'=>array('lbl'=>__('Please select the required ingredient before adding the item to the cart.', $this->pluginLocale),'descr'=>__('alert if required ingredient has not been selected', $this->pluginLocale)),
			'js_toggle_comments'=>array('lbl'=>__('show/hide comments', $this->pluginLocale),'descr'=>__('label to toggle comments in cart (if any added via textbox custom group)', $this->pluginLocale)),
			'addasis_button'=>array('lbl'=>__('Just add to cart', $this->pluginLocale),'descr'=>__('button label adding to cart without choosing ingredients (if enabled)', $this->pluginLocale)),
			'preselect_prices_zero_regular'=>array('lbl'=>__('first gratis', $this->pluginLocale),'descr'=>__('text to display next to any preselected ingredient when initial price is zero but regular price >0 [non-custom groups]', $this->pluginLocale)),
			'preselect_prices_zero_custom_0'=>array('lbl'=>__('first gratis', $this->pluginLocale),'descr'=>__('text to display next to any preselected ingredient when initial price is zero but regular price >0 [custom groups]', $this->pluginLocale)),	
			/**************************************************************************/
			'multi_label_buttons'=>array(
				'descr'=>__('Whole/Halfs/Quarters - Label Main: Label next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('How would you like your Ingredients ?', $this->pluginLocale)
			),
			'multi_button_1'=>array(
				'descr'=>__('Whole/Halfs/Quarters - Label Button [1] Whole: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('Everywhere', $this->pluginLocale)
			),
			'multi_button_2'=>array(
				'descr'=>__('Whole/Halfs/Quarters - Label Button [2] Halfs: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('Half & Half', $this->pluginLocale)
			),
			'multi_button_4'=>array(
				'descr'=>__('Whole/Halfs/Quarters - Label Button [4] Quarters: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('Four Quarters', $this->pluginLocale)
			),
			/*whole -> currently unused*/
			'multi_icon_1_1'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [1] Label Whole [Icon]: Icon next to ingredients for whole menu items if more than one choices are available <b>(currently unused)</b>', $this->pluginLocale),
				'lbl'=>''
			),
			'multi_tab_1_1'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [1] Label Whole [Tab]: Text to use in tab <b>(currently unused)</b>', $this->pluginLocale),
				'lbl'=>''
			),
				/*halfs*/
			'multi_icon_2_1'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [2] Label First Half [Icon]: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('&#9680;: ', $this->pluginLocale)
			),
			'multi_tab_2_1'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [2] Label First Half [Tab]: Text to use in tab ', $this->pluginLocale),
				'lbl'=>__('Left Half', $this->pluginLocale)
			),
			'multi_icon_2_2'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [2] Label Second Half [Icon]: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('&#9681;: ', $this->pluginLocale)
			),
			'multi_tab_2_2'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [2] Label Second Half [Tab]: Text to use in tab ', $this->pluginLocale),
				'lbl'=>__('Right Half', $this->pluginLocale)
			),
				/*quarters*/
			'multi_icon_4_1'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label First Quarter [Icon]: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('&#9684;: ', $this->pluginLocale)
			),
			'multi_tab_4_1'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label First Quarter [Tab]: Text to use in tab ', $this->pluginLocale),
				'lbl'=>__('1st Quarter', $this->pluginLocale)
			),
			'multi_icon_4_2'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label Second Quarter [Icon]: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('&#9684;: ', $this->pluginLocale)
			),
			'multi_tab_4_2'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label Second Quarter [Tab]: Text to use in tab ', $this->pluginLocale),
				'lbl'=>__('2nd Quarter', $this->pluginLocale)
			),
			'multi_icon_4_3'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label Third Quarter [Icon]: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('&#9684;: ', $this->pluginLocale)
			),
			'multi_tab_4_3'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label Third Quarter [Tab]: Text to use in tab ', $this->pluginLocale),
				'lbl'=>__('3rd Quarter', $this->pluginLocale)
			),
			'multi_icon_4_4'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label Fourth Quarter [Icon]: Icon next to ingredients for whole menu items if more than one choices are available', $this->pluginLocale),
				'lbl'=>__('&#9684;: ', $this->pluginLocale)
			),
			'multi_tab_4_4'=>array(
				'descr'=>__('Whole/Halfs/Quarters - [4] Label Fourth Quarter [Tab]: Text to use in tab ', $this->pluginLocale),
				'lbl'=>__('4th Quarter', $this->pluginLocale)
			)

		);

		foreach($thisPluginLocalization['localization'] as $k=>$v){
			$defaultOptionsThisPlugin['localization'][$k]['lbl']=$v['lbl'];
		}
?>