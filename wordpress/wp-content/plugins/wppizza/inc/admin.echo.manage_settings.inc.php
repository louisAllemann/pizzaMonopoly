<?php
		echo'<div id="'.$this->pluginSlug.'-settings" class="wrap wppizza-settings-wrap">';
		echo"<h2>". $this->pluginName." ".__('Global Settings', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_global" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('global');
			submit_button( __('Save Changes', $this->pluginLocale) );
		echo'</form>';
		/**add donate button after last field in global settings**/	
		$donateButton='<div style="margin-left:100px;text-align:center;width:300px">'.__('If you would like to contribute directly to the development of this plugin<br/>feel free to do so via paypal below.', $this->pluginLocale).'<br/><br/>'.__('Thank you, much appreciated.', $this->pluginLocale).'<br/><br/>';
		$donateButton.='<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="N7T5P5FBRW3EA">
			<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
			</form>';
		$donateButton.='</div>';		
		
		print"".$donateButton."";
		echo'</div>';
?>