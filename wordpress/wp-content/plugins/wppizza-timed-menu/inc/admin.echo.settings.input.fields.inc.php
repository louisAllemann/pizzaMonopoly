<?php

			switch ( $tab ) :
				case 'timed_items' :

					if($field=='version'){
						echo "".$options['plugin_data'][$field]."";
					}
					if($field=='display_type'){

						$displSel=!empty($options['plugin_data'][$field]) ? $options['plugin_data'][$field] : '';
						echo "<input id='".$this->wpptmSlug."_plugin_data_".$field."' name='".$this->wpptmSlug."[plugin_data][".$field."]' ".checked($displSel,'posts_pages',false)." type='radio' value='posts_pages' />";
						echo " ".__('<b>install option 1</b>: I am using shortcodes like [wppizza category="xxx"] on several pages to show my menu items / categories', $this->wpptmLocale)."";
						echo "<br />";
						echo "<input id='".$this->wpptmSlug."_plugin_data_".$field."' name='".$this->wpptmSlug."[plugin_data][".$field."]' ".checked($displSel,'internal',false)." type='radio' value='internal' />";
						echo " ".__('<b>install option 2</b> : I am using templates with the navigation widget/shortcode and (optionally) *one* page as root of my menu (often used for nice permalinks)', $this->wpptmLocale)."";


						echo "<br/><span class='description'>".__('If you don\'t know which option you are using you are probably using option 1 (or see <a href="http://wordpress.org/plugins/wppizza/installation/">wppizza installation</a> for details).', $this->wpptmLocale)."</span>";

					}

					if($field=='timed_items'){
						/**get all relevant items cats pages and psts*/
						$itemsCatsPagesPosts=$this->wppizza_tm_admin_items_cats_pages_posts();

						echo"<input name='".$this->wpptmSlug."[".$field."_ctrl]' type='hidden'  value='1' />";/**make sure we are also submiting something (i.e empty array)if no timed item was defined**/
						echo"<div id='wppizza_".$field."'>";
						echo"<div>";
							if(!empty($options['plugin_data']['display_type'])==''){$hStyle='none';}else{$hStyle='table';}
							$displayValue=!empty($options['plugin_data']['display_type']) ? $options['plugin_data']['display_type'] : '';
							if($displayValue=='internal'){
								$postPagesVis='none';
								$internalVis='table-cell';
							}else{
								$postPagesVis='table-cell';
								$internalVis='none';
							}


							echo"<table id='wppizza_".$field."_options' style='display:".$hStyle."'>";
							echo"<thead>";
							echo"<tr>";
								echo"<th>".__('Label (optional)', $this->wpptmLocale)."</th>";
								if(count($itemsCatsPagesPosts['items'])>0){
									echo"<th>".__('Menu Items', $this->wpptmLocale)."</th>";
								}
								if(count($itemsCatsPagesPosts['categories'])>0){
								echo"<th style='display:".$internalVis."' class='wppizza-tm-int'>".__('Categories', $this->wpptmLocale)."</th>";
								}
								if(count($itemsCatsPagesPosts['pages'])>0){
								echo"<th style='display:".$postPagesVis."' class='wppizza-tm-pp'>".__('Pages', $this->wpptmLocale)."</th>";
								}
								if(count($itemsCatsPagesPosts['posts'])>0){
								echo"<th style='display:".$postPagesVis."' class='wppizza-tm-pp'>".__('Posts', $this->wpptmLocale)."</th>";
								}
								echo"<th>".__('Start/End Date', $this->wpptmLocale)."</th>";
								echo"<th>".__('Start/End Times', $this->wpptmLocale)."</th>";
								echo"<th>".__('Day(s)', $this->wpptmLocale)."</th>";
								echo"<th></th>";
							echo"</tr>";
							echo"</thead>";
							echo"<tbody>";
							if(is_array($options[$field])){
							asort($options[$field]);
							foreach($options[$field] as $k=>$v){
								echo"".$this->wppizza_tm_admin_get_timed_menue($field,$k,$v,$options,$itemsCatsPagesPosts,$v['display']);
							}}
							echo"</tbody>";
							echo"</table>";
						echo"</div>";
						echo "<a href='#' id='wppizza_add_".$field."' class='button'>".__('add new timed item(s)', $this->wpptmLocale)."</a>";
						echo"</div>";
					}

				break;

				case 'options' :
					echo"<input name='".$this->wpptmSlug."[options][ini]' type='hidden'  value='ini' />";
					if($field=='display_as_unavailable'){
						echo "<input id='".$this->wpptmSlug."_options_".$field."' name='".$this->wpptmSlug."[options][".$field."]'  ".checked($options['options'][$field],true,false)." type='checkbox' value='1' />";
						echo " <span class='description'>".__('By default, any menu items not available will not be shown. If you DO want them displayed - with a note like "currently not available" or similar (set in "localization" of this plugin) - tick this box<br />( adjust your css if required - see notes in wppizza-tm.css file)', $this->wpptmLocale)."</span>";
					}

				break;


				case 'access' :
					if($field=='access'){
						$this->wpptmUserCaps->user_echo_admin_caps($this->wppizza_tm_caps(),$this->wpptmSlug,'admin_access_caps');
					}

				break;

				case 'localization' :
					if($field=='localization'){
						/**to get descriptions include default options**/
						require('admin.setup.default.options.inc.php');
						echo"<div id='wppizza_".$field."'>";
							echo"<div id='wppizza_".$field."_options'>";
							$localize=array();
							foreach($localizationOptions as $k=>$v){
								$localize[$k]=array('descr'=>$v['descr'],'name'=>''.$this->wpptmSlug.'['.$field.']['.$k.']','value'=>$options[$field][$k],'type'=>$v['type']);
							}
							asort($localize);//sort but keep index
							foreach($localize as $k=>$v){
								if($v['type']=='textarea'){
										$editorId="".strtolower($this->wpptmSlug."_".$field."_".$k)."";
										$editorName="".$this->wpptmSlug."[".$field."][".$k."]";
										echo"<br/>".$v['descr']."";
										echo"<div style='width:500px;'>";
										wp_editor( $v['value'], $editorId, array('teeny'=>1, 'wpautop'=>false, 'textarea_name'=>$editorName) );
										echo"</div>";
										echo"<br/>";
								}else{
									echo "<input name='".$v['name']."' size='30' type='text' value='".$v['value']."' />";
									echo "".$v['descr']."<br/>";
								}
							}
							echo"</div>";
						echo"</div>";
					}
				break;


				case 'license' :
					if($field=='license'){
						$slug		= $this->wpptmSlug;
						$fieldName	= $this->wpptmSlug."[license][key]";
						$license 	= $options['plugin_data']['license']['key'];
						$status 	= $options['plugin_data']['license']['status'];
						$this->wpptmEdd->echo_edd_settings($slug,$fieldName,$license,$status);
					}
				break;


				case 'howto' :
					echo"<div>";
					echo"".__("
						<p>You can set some. many or all of your menu items to be only available/visible at certain dates, times or days. <b>(Make sure Wordpress->Settings->General->Timezone is correctly set)</b></p>

						<p>To start, click on \"add new timed item(s)\" where you will be presented with the following options:</p>

						<br />
						<br />

						<p style='font-weight:600'>
						 Label:
						<p>

						<blockquote>
							<p>
								An arbitrary and optional label you can set as you wish to help you identify this particular timed menu setting (also used to sort the settings by). This will neither be displayed nor has it any effect on the frontend.
							</p>
						</blockquote>
						<br />
						<br />

						<p style='font-weight:600'>
						 Items/Pages/Categories:
						<p>
						<blockquote>
							<p>
								<b>Menu Items :</b> Select all the menu items you would like to have these particular timing settings applied for.
							</p>

							<p>
								<b>Pages/Categories (depending on install option) :</b> Additionally to or instead of menu items above, you can set timing settings for a whole category/page.
							</p>

							<p style='color:red'>
								Please Note: The settings made for a page/category will apply for any item in said page/category even if an item is added to this category at a later date.<br />
								For example: If you choose to only display/make available your dessert category on fridays, any items added to the dessert category at a later date will also only be available on fridays.
							</p>
						</blockquote>
						<br />
						<br />

						<p style='font-weight:600'>
							Timing Section: The settings you make here are cumulative (i.e all settings apply if set)
						<p>
						<blockquote>
							<p>
								<b>i) Start/End Date:</b>
									<blockquote>
									<ul>
									<li>Click on the relevant text field to bring up a calender.</li>
									<li>If you only set a Start Date but leave the End Date blank, the items selected will be available ONLY AFTER set Start Date.</li>
									<li>If you only set an End Date but leave the Start Date blank, the items selected will be available ONLY UNTIL the End Date.</li>
									<li>If you set both dates, the items selected the items selected will ONLY be available between the two dates.</li>
									<li>If you set neither date, they will be ignored</li>
									</ul>
									</blockquote>
							</p>

							<p>
								<b>ii) Start/End Times:</b>
									<blockquote>
									<ul>
									<li>Click on the relevant text field to bring up a time picker.</li>
									<li>If you only set a Start Time but leave the End Time blank, the items selected will ONLY BE AVAILABLE AFTER set Start Time until 23:59:59.</li>
									<li>If you only set an End Time but leave the Start Time blank, the items selected will ONLY BE AVAILABLE from 0:00:00 UNTIL the End Time.</li>
									<li>If you set both times, the items selected the items selected will ONLY BE AVAILABLE between these two times. <b>If End Time is earlier than Start Time , End Time is assumed to be on the next day</b></li>
									<li>If you set neither, they will be ignored</li>
									</ul>
									</blockquote>
							</p>

							<p>
								<b>iii) Days:</b>
									<blockquote>
									<ul>
									<li>Select the relevant days you want the applicable menu items to be available.</li>
									<li>If set, the items selected will ONLY BE AVAILABLE on the selected days</li>
									<li>If you set none, this will be ignored</li>
									</ul>
									</blockquote>
							</p>
							<p style='color:red'>To reiterate: your settings in i, ii and iii are cumulative. I.e if you set a start and end date, as well as mon,tue,wed as days, the items selected will only be available on mon,tue and wed between the two set dates. The same applies for any other combination of dates, times and days if set.</p>
							<p>The settings are only applied if you have set at least one of the settings under i, ii or iii and of course enabled this particular setting.</p>
							<p style='font-weight:600'>Be careful when setting end dates as when the end date has passed, the associated menu items will never be displayed again (unless of course you disable/turn off or delete this particular setting)</p>

							<br />

							<p style='font-weight:600'>
								Enable/Delete:
							<p>

							<blockquote>
								<ul>
									<li>Settings are only applied when enabled.</li>
									<li>To permanently delete a setting, click the [x] button and save</li>
								</ul>
							</blockquote>
						</blockquote>

						<br />
						<br />

						<p style='font-weight:600'>
						Summary / Notes :
						<p>
							<blockquote>
									<ul>
										<li>Do NOT use a cache plugin on the effected pages. It will cause unexpected results (for obvious reasons I would have thought)</li>
										<li>Although the navigation to pages/categories will be omitted when all items of a selected page/category are unavailable at a given time, these pages may still be indexed by searchengines.<br/>Therefore, if a user comes to one of these pages directly from a searchengine results page your page will display whatever you have set in WPPizza->Timed Menu->localization</li>
									</ul>
							</blockquote>




					",$this->wpptmLocale)."";

					echo"</div>";
				break;
			endswitch;
?>