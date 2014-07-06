<?php
	global $pagenow;
	$options =$this->pluginOptions;
	$masterOptions =$this->masterOptions;
	$optionSizes=wppizza_sizes_available($masterOptions['sizes']);//outputs an array $arr=array(['lbl']=>array(),['prices']=>array());

	if ( $pagenow == 'edit.php' && $_GET['page'] == $this->pluginSlug ){
	    if (isset ($_GET['tab'])){$tab = $_GET['tab'];}else{$tab = ''.$this->pluginAccessCapabilities[0]['tab'].'';}

			switch ( $tab ) :
				case 'ingredients' :

					if($field=='version'){

						echo "".$options['plugin_data'][$field]."";
					}
					if($field=='ingredients'){

						/**add to array from master options for us to use in admin page**/
						$options['layout']['hide_decimals']=$masterOptions['layout']['hide_decimals'];

						echo"<div id='wppizza_".$field."'>";
						echo"<div id='wppizza_".$field."_options'>";
						/*********dd filter******/
						echo"<div id='".$this->pluginSlug."_ingredients_filter_wrap'>".__('Show only Ingredients for', $this->pluginLocale).": ";
							echo"<select id='".$this->pluginSlug."_ingredients_filter_group'>";
							echo"<option value=''>--- ".__('All Groups', $this->pluginLocale)." ---</option>";
								foreach($optionSizes as $l=>$m){
									$ident=empty($this->masterOptions['sizes'][$l][0]['lbladmin']) ? 'ID:'.$l.'' : '"'.$this->masterOptions['sizes'][$l][0]['lbladmin'].'"' ;
									echo"<option value='".$l."'>".implode(", ",$m['lbl'])." [".$ident."]</option>";//".implode(", ",$m['lbl'])."
								}
							echo"</select>";
							echo"</div>";
						/*******filter end********/
						if(is_array($options[$field])){
						/*sort by pricetiers and then item name**/
						//$sortedOptions = wppizza_array_multisort($options[$field], array('sizes'=>SORT_ASC, 'item'=>SORT_ASC));
						asort($options[$field]);
						$i=1;
						foreach($options[$field] as $k=>$v){
							echo"".$this->wppizza_admin_section_ingredients($field,$k,$v,$options,$optionSizes);
						$i++;
						}}
						echo"</div>";
						echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('add', $this->pluginLocale)."</a>";
						if(count($optionSizes)>0){

							echo "<fieldset id='wppizza_ingredients_copy_fs'><div><b>".__('copy group ingredients', $this->pluginLocale)."</b></div>";
							echo "".__('from', $this->pluginLocale).": ";
							echo "<select id='wppizza_ingredients_copy_source'>";
							echo "<option value=''>".__('--select source--', $this->pluginLocale)."</option>";
							foreach($optionSizes as $k=>$v){
								$ident=empty($this->masterOptions['sizes'][$k][0]['lbladmin']) ? 'ID:'.$k.'' : '"'.$this->masterOptions['sizes'][$k][0]['lbladmin'].'"' ;
								echo "<option value='".$k."'>".implode(",",$v['lbl'])." [".$ident."]</option>";
							}
							echo "</select>";

							echo "".__('to', $this->pluginLocale).": ";
							echo "<select id='wppizza_ingredients_copy_dest'>";
							echo "<option value=''>".__('--select destination--', $this->pluginLocale)."</option>";
							foreach($optionSizes as $k=>$v){
								$ident=empty($this->masterOptions['sizes'][$k][0]['lbladmin']) ? 'ID:'.$k.'' : '"'.$this->masterOptions['sizes'][$k][0]['lbladmin'].'"' ;
								echo "<option value='".$k."'>".implode(",",$v['lbl'])." [".$ident."]</option>";
							}
							echo "</select>";
							echo "<span id='wppizza_ingredients_copy' class='button'>".__('copy group ingredients', $this->pluginLocale)."</span>";
							echo "</fieldset>";

						}
						echo"</div>";
					}

				break;

				case 'access-level' :
					global $current_user,$user_level,$wp_roles;
					if($field=='access_level'){
						echo"<b>".__('Set the roles that are allowed to access these pages', $this->pluginLocale)."</b>";
						$roles=get_editable_roles();/*only get roles user is allowed to edit**/
						$access=$this->wppizza_ingredients_capabilities_tabs();
						foreach($roles as $roleName=>$v){
							/*do not display current users role (otherwise he can screw his own access) or levels higher than current*/
							$userRole = get_role($roleName);
								echo"<div class='wppizza-ingr-access'>";
								echo"<input type='hidden' name='".$this->pluginSlug."[admin_access_caps][".$roleName."]' value='".$roleName."'>";
								echo"<ul>";
								print"<li style='width:150px'><b>".$roleName.":</b></li>";
									foreach($access as $aKey=>$aArray){
										echo"<li><input name='".$this->pluginSlug."[admin_access_caps][".$roleName."][".$aArray['cap']."]' type='checkbox'  ". checked(isset($userRole->capabilities[$aArray['cap']]),true,false)." value='".$aArray['cap']."' /> ".$aArray['name']."<br/></li>";//". checked($options['plugin_data']['access_level'],true,false)."
									}
								echo"</ul>";
								echo"</div>";
						}
					}
				break;

				case 'custom-groups' :
						/**group ingredients**/
						if($field=='ingredients_custom_groups'){
							echo"<div id='wppizza_".$field."'>";
							echo"<div id='wppizza_".$field."_options'>";
								echo"<div>";
								echo"<b>".__('Show Price if 0.00 ?', $this->pluginLocale)."</b> ";
								echo"<input name='".$this->pluginSlug."[settings][price_show_if_zero]' type='checkbox' ". checked($options['settings']['price_show_if_zero'],true,false)." value='1' />";
								echo"<br/><span class='small'>".__('By default Ingredients in custom groups have their price displayed after the ingredient even if it is 0.00. Uncheck this box if you do NOT want to display prices that are 0.00', $this->pluginLocale)."</span>";
								echo"<br/><br/></div>";

								echo"<div>";
								echo"<b>".__('Alternative text if ingredient price is 0.00', $this->pluginLocale)."</b> ";
								echo"<br/><input name='".$this->pluginSlug."[settings][price_localize_if_zero]' type='text' size='30' value='".$options['settings']['price_localize_if_zero']."' />";
								echo"<br/><span class='description'>".__('if you want to display alternative text (such as "free") for prices that are 0.00 enter it here. (make sure "Show Price if 0.00" above is checked)', $this->pluginLocale)."</span>";
								echo"<br/><br/></div>";

								if(isset($options[$field])){
								echo"<div  id='".$this->pluginSlug."_custom_groups_label'>";
									echo"<b>".__('Your Custom Ingredients Groups:', $this->pluginLocale)."</b>";
								/*********dd filter******/
									echo"<span id='".$this->pluginSlug."_custom_groups_filter_wrap'>".__('Show only Groups that apply to', $this->pluginLocale).": ";


									$args = array('post_type' => ''.WPPIZZA_POST_TYPE.'','posts_per_page' => -1, 'orderby'=>'title' ,'order' => 'ASC');
									$query = new WP_Query( $args );
									echo"<select id='".$this->pluginSlug."_custom_groups_filter'>";
									echo"<option value=''>--- ".__('Menu Items', $this->pluginLocale)." ---</option>";
									foreach($query->posts as $pKey=>$pVal){
										$pageName[$pVal->ID]=$pVal->post_title;
											echo"<option value='".$pVal->ID."' >".$pVal->post_title."</option>";
										}
									echo"</select>";

									echo"<select id='".$this->pluginSlug."_custom_groups_filter_group'>";
									echo"<option value=''>--- ".__('Group', $this->pluginLocale)." ---</option>";
										foreach($optionSizes as $l=>$m){
											$ident=empty($this->masterOptions['sizes'][$l][0]['lbladmin']) ? 'ID:'.$l.'' : '"'.$this->masterOptions['sizes'][$l][0]['lbladmin'].'"' ;
											echo"<option value='".$l."'>".implode(", ",$m['lbl'])." [".$ident."]</option>";//".implode(", ",$m['lbl'])."
										}

									echo"</select>";
									echo"</span>";
								/*******filter end********/
								echo"</div>";

									asort($options[$field]);
									foreach($options[$field] as $k=>$v){
										$dbGroup=$this->wppizza_admin_section_ingredients_groups($k,$optionSizes,$v['sizes'],$v);
										echo"".$dbGroup;
									}
								}

								echo"</div>";
								echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('Add Custom Ingredients Group', $this->pluginLocale)."</a>";
								echo"</div>";
						}
				break;

				case 'options' :
								if($field=='ingredients_added_sticky'){
									echo"<input name='".$this->pluginSlug."[options][".$field."]' type='checkbox' ". checked($options['options'][$field],true,false)." value='1' />";
									echo" <span class='description'>".__('Keep added ingredients "sticky" when using popup. [if you have a lot of ingredients, you probably want to turn this off to improve usability on small handheld devices]', $this->pluginLocale)."</span>";
								}
								echo'<input type="hidden" name="'.$this->pluginSlug.'[options][set]" value="1" />';/*required so it  still saves when all options are false*/
								if($field=='ingredients_in_popup'){
									echo"<input name='".$this->pluginSlug."[options][".$field."]' type='checkbox' ". checked($options['options'][$field],true,false)." value='1' />";
								}
								if($field=='ingredients_in_popup_wpc'){
								echo"<input name='".$this->pluginSlug."[options][".$field."]' type='text' size='30' value='".$options['options'][$field]."' />";
								echo" <span class='description'>".	__('popup width [in percent]. You probably want to leave this at 100%. The popup gets dynamically resized depending on device used', $this->pluginLocale)."</span>";
								}
								if($field=='ingredients_in_popup_anim'){
								echo"<input name='".$this->pluginSlug."[options][".$field."]' type='text' size='30' value='".$options['options'][$field]."' />";
								echo" <span class='description'>".	__('animation speed of popup [in ms]', $this->pluginLocale)."</span>";
								}

								if($field=='ingredients_show_count'){
								echo"<input name='".$this->pluginSlug."[options][".$field."]' type='checkbox' ". checked($options['options'][$field],true,false)." value='1' />";
								echo" <span class='description'>".	__('you can choose to also display a count in the list of ingredients available to indicate there too how often it has been added', $this->pluginLocale)."</span>";
								}

								if($field=='ingredients_addasis_button_enabled'){
								echo"<input name='".$this->pluginSlug."[options][".$field."]' type='checkbox' ". checked($options['options'][$field],true,false)." value='1' />";
								echo" <span class='description'>".	__('shows an additional button to directly add item to cart without selecting ingredients (only displayed if there are no mandatory or preselected ingredients and there are choices between halfs/quarters etc)', $this->pluginLocale)."</span>";
								}

								if($field=='ingredients_omit_single_count'){
								echo"<input name='".$this->pluginSlug."[options][".$field."]' type='checkbox' ". checked($options['options'][$field],true,false)." value='1' />";
								echo" <span class='description'>".	__('do not show "1x" count in cart, order and users account history if an ingredient has only been added 1 time (multiple selections will always have their count displayed)', $this->pluginLocale)."</span>";
								}

								
								if($field=='ingredients_show_depreselected'){
									echo"<input name='".$this->pluginSlug."[options][".$field."]' type='checkbox' ". checked($options['options'][$field],true,false)." value='1' />";
									echo" <span class='description'>".	__('if you have pre-selected a specific ingredient (for example "Onions") but the customer chose to not have any onions, you can display this in the cart, orderpage and emails as "No Onions" as opposed to just omitting this ingredient', $this->pluginLocale)."</span>";
								
									echo"<br /><input name='".$this->pluginSlug."[options][ingredients_show_depreselected_after]' type='checkbox' ". checked($options['options']['ingredients_show_depreselected_after'],true,false)." value='1' />";								
									echo" <span class='description'>".__('Display After Selected. By default these will be displayed before any actually selected ingredients. If you want them displayed after, check this box', $this->pluginLocale)."</span>";
								
									echo"<br /><input name='".$this->pluginSlug."[options][ingredients_show_depreselected_prefix]' type='text' size='30' value='".$options['options']['ingredients_show_depreselected_prefix']."' />";
									echo" <span class='description'>".__('prefix for pre-selected ingredients that were de-selected such as "NO" or "0x" for example so it would read "No Onions" or "0x Onions" ', $this->pluginLocale)."</span>";
								
							
								}

								if($field=='ingredients_added_show_price'){
									echo"<input name='".$this->pluginSlug."[options][ingredients_added_sort_by_price]' type='checkbox' ". checked($options['options']['ingredients_added_sort_by_price'],true,false)." value='1' />";
									echo" <span class='description'>".	__('sort added ingredients by price', $this->pluginLocale)."</span>";
									echo"<br /><input name='".$this->pluginSlug."[options][".$field."]' type='checkbox' ". checked($options['options'][$field],true,false)." value='1' />";
									echo" <span class='description'>".	__('display the price next to each ingredient *added*. ', $this->pluginLocale)."</span>";
									echo"<br /><input name='".$this->pluginSlug."[options][ingredients_added_show_price_no_zero]' type='checkbox' ". checked($options['options']['ingredients_added_show_price_no_zero'],true,false)." value='1' />";
									echo" <span class='description'>".	__('but do NOT display prices that are zero', $this->pluginLocale)."</span>";
									echo"<br /><input name='".$this->pluginSlug."[options][ingredients_added_zero_price_txt]' type='text' size='30' value='".$options['options']['ingredients_added_zero_price_txt']."' />";
									echo" <span class='description'>".	__('alternative label if zero [has no effect if "do not display prices that are zero" in enabled]', $this->pluginLocale)."</span>";
								}




				break;


				case 'localization' :
						if($field=='localization'){
							/**to get descriptions include default options**/
							require_once(WPPIZZA_ADDINGREDIENTS_PATH .'inc/admin.setup.default.options.inc.php');
							/**add description to array and sort**/
							$localizeOptions=array();
							foreach($options[$field] as $k=>$v){
								$localizeOptions[$k]['descr']=$thisPluginLocalization[$field][$k]['descr'];
								$localizeOptions[$k]['lbl']=$options[$field][$k]['lbl'];
							}
							asort($localizeOptions);
							echo"<div id='wppizza_".$field."'>";
								echo"<div id='wppizza_".$field."_options'>";
								echo'<span style="color:blue">'.__('A note about Whole/Half/Quarter <b>ICONS</b>:',$this->pluginLocale).'</span><br />';
								echo'<b>'.__('if you would like to use images as icons, use css declarations as decribed close to the bottom of the css file as the icons set here will still print in emails !', $this->pluginLocale).'</b><br /><br />';

								$i=0;
								$extraBr=array(0=>''.__('Label and Text for Selection Buttons (if enabled)', $this->pluginLocale).'', 4=>''.__('Whole:  Icons and Tab - Currently not in use', $this->pluginLocale).'',6=>''.__('Halfs: Icons and Tabs', $this->pluginLocale).'',10=>''.__('Quarters: Icons and Tabs', $this->pluginLocale).'',18=>'<br />'.__('General', $this->pluginLocale).'');
								foreach($localizeOptions as $k=>$v){
								if(in_array($i,array_keys($extraBr))){echo'<div><b>&nbsp;'.$extraBr[$i].'</b><br/>';}
									echo "<input name='".$this->pluginSlug."[".$field."][".$k."]' size='40' type='text' value='".$v['lbl']."' />";
									echo" ".$v['descr']."";
									
									if(in_array($i+1,array_keys($extraBr))){echo'</div>';}else{echo'<br />';}
								$i++;
								}
								echo"</div>";
							echo"</div>";
						}
				break;


				case 'license' :

						$license 	= $options['license']['key'];
						$status 	= $options['license']['enabled'];
						$response 	= !empty($options['license']['response']) ? $options['license']['response'] : '' ;

						echo"<input name='".$this->pluginSlug."[license][key]' type='text' placeholder='".__('Enter your license key')."' size='30' class='regular-text' value='".$options['license']['key']."' />";
						echo' '.__('License Key', $this->pluginLocale).'<br />';

						if( $status !== false && $status == 'valid' ) {
							echo"<label class='button-secondary'><input name='".$this->pluginSlug."[license][deactivate]' type='checkbox' value='1' /> ".__('De-Activate License', $this->pluginLocale)."</label>";
							echo'<span style="color:green;"> '. __('License active', $this->pluginLocale).'</span>';
						}else{
							echo"<label class='button-secondary'><input name='".$this->pluginSlug."[license][activate]' type='checkbox'  value='1' /> ".__('Activate License', $this->pluginLocale)."</label>";
							echo'<span style="color:red;"> '. __('License in-active', $this->pluginLocale).'</span>';
						}
						echo'<br/>'.__('Please note: entering and activating the license is optional, but if you choose not to do so, you will not be informed of any future bugfixes and/or updates.', $this->pluginLocale).'<br />';

						//echo"<br/><label class='button-secondary'><input name='".$this->pluginSlug."[license][check]' type='checkbox' value='1' /> ".__('Check License', $this->pluginLocale)."</label>";

						if( isset($response['value']) && $response['value'] == 'connection-error' ) {
							echo'<br / ><div class="wppizza_license_connection_error" style="display:inline-block;color:red;font-size:120%;margin:10px 0;padding:10px;border:1px solid #000000">';
							echo''.__('ERROR', $this->pluginLocale).':<br/>';
							if($response['action']=='activate'){
								echo''.__('There was a connection error, when trying to activate your license.<br />Please try again.', $this->pluginLocale).'';
							}
							if($response['action']=='deactivate'){
								echo''. __('There was a connection error, when trying to de-activate your license.<br />Please try again.', $this->pluginLocale).'';
							}
							echo'</div>';
						}
				break;

				case 'manual' :
					if($field=='custom_groups'){
						echo"

							<p>
								I realize that adding ingredients to a menu item, and especially using custom groups, can be a bit confusing to start off with.<br />
								However, to be able to have a wide range of options for you to tailor your distinct scenario this has been unavoidable.
							</p>
							<p>
								The following is a description of the concept behind it and a description as to how you might want to go about implementing it.<br />
								Of course, how you actually are going to go about it is entirely up to you but I hope the below will get you going.
							</p>

							<br /><hr /><br />



							<p>For simplicity, let's consider the following scenarios (adjust depending on you particular situation):</p>
							<p>
								Your restaurant sells 10 different kinds of Pizzas in the sizes 'small', 'regular' and 'large'.<br />
								Therefore, in the MAIN wppizza plugin 'meal sizes' section you will (should) have one size option with the labels 'small', 'regular' and 'large' and respective default prices (to be overwritten on a per menu item basis) which has been assigned to your pizzas.
							</p>




							<br /><h3>a) Add Ingredients - Simple</h3>
							<blockquote>
								if you want to allow your customers to add any other ingredients to some or all of your pizzas go to wppizza-&gt;ingredients-&gt;ingredienst[tab at top], click 'add' , choose the mealsizes (in this case small,regular,large), add/name the ingredient (let's say tomatoes) and set a price for adding extra tomatoes to a small, regular and large pizza respectivly.<br /><br />
								Add any other ingredients you want to allow the customer to choose from when selecting a pizza in the same manner and save.<br /><br />
								By ticking and saving the relevant checkbox you can now - on a per item basis (wppizza-&gt;all menu items-&gt;'selected item') - allow/enable the ability for a customer to add some extra tomatoes (or whatever ingredients you added in the step above)<br /><br />
								You can furthermore allow the customer to select extra toppings for each half or each quarter. Just enable/check the relevant checkboxes. If half/half is selected for example, added ingredients prices will be calculated based on the price set in the main ingredients screen times the percentage set under whole,halfs and quartes respectively.<br /><br />
								In the frontend, ingredients will be grouped and sorted firstly by price and then alphabetically.
							</blockquote>

							<p>If the above is all you need, you will not need to set-up any custom groups.</p>
							<br />



							<h3>b) Add Ingredients - Advanced / Custom Groups</h3>
							<h4>
								Custom groups are a way to re-order and/or CUSTOMISE any group of ingredients that YOU HAVE ALREADY SETUP for a given menu item / mealsize.<br />
								Essentially, if you want to have more granular control about what your customer can choose where with regards to additional ingredients or other options, you will probably have to set-up one or several custom groups depending on what it is you want to offer.
							</h4>

							<blockquote>
							<b>First of all, add your ingredients that might be applicable to your pizzas as desccribed under a).</b><br />
							<br />Let's also assume you additionally want to allow your customer to choose between a 'thin crust' and 'deep pan' pizza base.
							<br />In this case, add 'thin crust' as well as 'deep pan' to the the list of ingredients (just like the 'tomatoes' example above)<br />
							<br />Most likely, it does not make much sense for a customer to add 'deep pan' as well as 'thin crust' as an ingredient (which is what you would get if you were to use the simple ingredients option as described under a)).<br />
							This is where the 'custom groups' come in.<br />
							<br />Assuming that you have set-up all the ingredients allowable for your pizzas (including the 'deep pan' and 'thin crust' 'ingredient' option, go to wppizza-&gt;ingredients-&gt;custom groups[tab at top] and click 'add custom ingredient group'.
							<br />From the dropdown ['--select group--'], select 'small,regular,large' which in turn will present you with a bunch of options.
							<br /><em>('--Add textbox to item--' is a special case which just adds a textbox and is not relevant for this example. However, if at some point you want to allow the customer to add additional info to a specific item when adding ingredients, feel free to add this too)</em><br />
							<br />As we want to have 'thin crust' and 'deep pan' as a distinct option where the customer can (and must) only select one or the other, set 'custom group type' to 'Group must have one - *and only one ingredient one time* - selected (radio input)' and check - under 'available ingredients for this group' - 'deep pan' and 'thin crust' as well as selecting (probably) all your pizzas under 'Assign to the following menu item(s)'.<br />
							<br />This in turn will take out these two 'ingredients' from the listing of all other ingredients for this meal size and forces the customer to select one or the other.
							<br />If you have somewhat different requirements for particular ingredients , set the 'Custom Group Type' as required.<br />
							<br />Adjust all other available options in this screen as required (these are somewhat self-explanetory I hope.)<br />
							</blockquote>

								<b>NOTE REGARDING THIS PARTICULAR EXAMPLE:</b><br />
								If you are allowing a customer to select ingredients by half or quarter pizzas - and have therefore selected the relevant checkbox(es) in the menu item - you will most likely also want to select:<br />
									<p style='padding-left:30px'><em>'Ingredients can only be applied to whole menu item...etc'</em></p>
								as it would most likely not be desireable to let the customer - when choosing half and half (or quarter) ingredients - to also choose a different base for each half/quarter.<br /><br />



								<p><b>Other Custom Group Options:</b></p>
							<blockquote>
								<em>Exclude Ingredient</em>:<br />will exclude (not display) any ingredients selected here from being available to select by the customer for the respective menu item it has been assignd to regardless of whether it has been made available in any other custom group fro this menu item (i.e it overrides any other selection)<br /><br />

								<em>Pre-select Ingredients</em>:<br />you could set up a menu item called 'Pizza A with extra cheese and garlic', set up a custom group type 'pre-select ingredients', select 'extra cheese' as well as 'extra garlic' for this group and assign it to the menu item 'Pizza A with extra cheese and garlic' (provided you have added these 2 ingredients to the pool of ingredients to choose from in the first place of course)<br />
								This in turn will pre-select extra cheese and extra garlic in the front-end for this item. Note: the price of these 2 pre-selected ingredients will be added to the price of the menu item (so it can be subtracted if the customer chooses to deselect one of these for example)
							</blockquote>

							<br />
							<h3>c) Summary:</h3>

							<blockquote>
							<ul>
							<li>i) add all ingredients you could possibly need for a particular mealsize option to the pool of ingredients for these mealsizes</li>
							<li>ii) adjust/use custom groups for more granular control</li>
							<li>iii) just play around with it</li>
							</ul>
							</blockquote>


							<br /><hr /><br />


							The above is just an example to get you going and to make the concept a little bit easier to understand (hopefully).<br />
							Maybe just start with one custom group (if needed at all of course) and expand from there.<br />
							There are a myriad of options, variations and adjustments you may want to or have to make depending on your particular requirements, but I hope that most of these can be fulfilled with the possibilities available.<br /><br />
							As ever, if there are any questions or suggestions, contact me via wp-pizza.com

						";
					}
				break;
			endswitch;
	}
?>