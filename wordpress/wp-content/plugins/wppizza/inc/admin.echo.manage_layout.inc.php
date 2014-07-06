<?php
		echo'<div id="'.$this->pluginSlug.'-settings" class="wrap">';
		echo"<h2>". $this->pluginName." ".__('Layout', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_layout" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('layout');
			submit_button( __('Save Changes', $this->pluginLocale) );
		echo'</form>';
		echo'</div>';
?>