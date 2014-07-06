<?php
		echo'<div id="'.$this->pluginSlug.'-settings" class="wrap">';
		echo"<h2>". $this->pluginName." ".__('Opening Times', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_opening_times" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('opening_times');
			submit_button( __('Save Changes', $this->pluginLocale) );
		echo'</form>';
		echo'</div>';
?>