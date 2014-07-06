<?php
		echo'<div id="'.$this->pluginSlug.'-gateways" class="wrap">';
		echo"<h2>". $this->pluginName." ".__('Gateways Settings', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_gateways" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('gateways');
			submit_button( __('Save Changes', $this->pluginLocale) );
		echo'</form>';
		echo'</div>';
?>