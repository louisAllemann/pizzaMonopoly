��    +      t  ;   �      �  �  �     �  �   �  �      P   �           
   ,     7     U  #   \     �     �     �  1   �  �   �     �     �     �     �  
   �     �     �     �     �     �  
   �               #     3  
   ?     J     [     c     y     �     �     �  [   �  b     �   g  �    �  �      �5  �   �5  �   6  P   �6     7    7  
   +8     68     T8  #   [8     8     �8     �8  1   �8  �   �8     �9     �9     �9     �9  
   �9     �9     �9     �9     �9     �9  
   �9      :     :     ":     2:  
   >:     I:     Z:     b:     x:     �:     �:     �:  [   �:  b   ;  �   f;         #             !       *            	         "             )             &                        '                                $   (   +                     
             %                         
						<p>You can set some. many or all of your menu items to be only available/visible at certain dates, times or days. <b>(Make sure Wordpress->Settings->General->Timezone is correctly set)</b></p>

						<p>To start, click on "add new timed item(s)" where you will be presented with the following options:</p>

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




					 &#8919; Timed Menu <b>install option 1</b>: I am using shortcodes like [wppizza category="xxx"] on several pages to show my menu items / categories <b>install option 2</b> : I am using templates with the navigation widget/shortcode and (optionally) *one* page as root of my menu (often used for nice permalinks) <div style='text-align:center'>Sorry, this item is currently not available</div> Access By default, any menu items not available will not be shown. If you DO want them displayed - with a note like "currently not available" or similar (set in "localization" of this plugin) - tick this box<br />( adjust your css if required - see notes in wppizza-tm.css file) Categories Ctrl+Click to select multiple Day(s) Display Menu Items as unavailable ? End Date End Time [HH:MM] How To How do you display Wppizza Items and Categories ? If you don't know which option you are using you are probably using option 1 (or see <a href="http://wordpress.org/plugins/wppizza/installation/">wppizza installation</a> for details). Label Label (optional) License Localization Menu Items Options Pages Posts Save Changes Set Access Rights Start Date Start Time [HH:MM] Start/End Date Start/End Times Timed Items Timed Menu Timed Menu Items Version add new timed item(s) currently not available delete enabled on/off  please read the "how to" for instructions and information how to set your timed menu items. text on single item when it is not available and option "Display as unavailable" has been selected text to display on page when NO item in a category is currently available and wppizza->timed menu->options->"Display Menu Items as unavailable ?" is NOT enabled (html allowed) Project-Id-Version: WPPizza
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2014-01-30 13:10-0000
PO-Revision-Date: 2014-01-30 13:10-0000
Last-Translator: ollybach <dev@1000db.com>
Language-Team: 
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Poedit-KeywordsList: __;_e;_x
X-Poedit-Basepath: .
X-Poedit-SourceCharset: utf-8
X-Generator: Poedit 1.5.5
X-Poedit-SearchPath-0: ..
 
						<p>You can set some. many or all of your menu items to be only available/visible at certain dates, times or days. <b>(Make sure Wordpress->Settings->General->Timezone is correctly set)</b></p>

						<p>To start, click on "add new timed item(s)" where you will be presented with the following options:</p>

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




					 &#8919; Timed Menu <b>install option 1</b>: I am using shortcodes like [wppizza category="xxx"] on several pages to show my menu items / categories <b>install option 2</b> : I am using templates with the navigation widget/shortcode and (optionally) *one* page as root of my menu (often used for nice permalinks) <div style='text-align:center'>Sorry, this item is currently not available</div> Access By default, any menu items not available will not be shown. If you DO want them displayed - with a note like "currently not available" or similar (set in "localization" of this plugin) - tick this box<br />( adjust your css if required - see notes in wppizza-tm.css file) Categories Ctrl+Click to select multiple Day(s) Display Menu Items as unavailable ? End Date End Time [HH:MM] How To How do you display Wppizza Items and Categories ? If you don't know which option you are using you are probably using option 1 (or see <a href="http://wordpress.org/plugins/wppizza/installation/">wppizza installation</a> for details). Label Label (optional) License Localization Menu Items Options Pages Posts Save Changes Set Access Rights Start Date Start Time [HH:MM] Start/End Date Start/End Times Timed Items Timed Menu Timed Menu Items Version add new timed item(s) currently not available delete enabled on/off  please read the "how to" for instructions and information how to set your timed menu items. text on single item when it is not available and option "Display as unavailable" has been selected text to display on page when NO item in a category is currently available and wppizza->timed menu->options->"Display Menu Items as unavailable ?" is NOT enabled (html allowed) 