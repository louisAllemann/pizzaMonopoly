<?php
		echo'<div id="'.$this->pluginSlug.'-settings" class="wrap wppizza-order-settings-wrap">';
		echo"<h2>". $this->pluginName." ".__('Orders Setting', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_order" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('order');
			submit_button( __('Save Changes', $this->pluginLocale) );
		echo'</form>';
		echo'</div>';
?>