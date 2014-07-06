<?php
		echo'<div id="'.$this->pluginSlug.'-settings" class="wrap wppizza-history-wrap">';
		echo"<h2>". $this->pluginName." ".__('Order History', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_history" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('history');
		echo'</form>';
		echo'</div>';	
?>