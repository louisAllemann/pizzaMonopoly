<?php
	
		echo'<div id="'.$this->pluginSlug.'-settings" class="wrap wppizza-additives-wrap">';
		echo"<h2>". $this->pluginName." ".__('Additives', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_additives" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('additives');
			submit_button( __('Save Changes', $this->pluginLocale) );
		echo'</form>';
		echo'</div>';
?>