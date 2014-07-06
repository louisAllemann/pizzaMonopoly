<?php
		echo'<div id="'.$this->pluginSlug.'-settings" class="wrap wppizza-order-form-wrap">';
		echo"<h2>". $this->pluginName." ".__('Orders Form', $this->pluginLocale)."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->pluginSlug.'_order_form" value="1">';
			settings_fields($this->pluginSlug);
			do_settings_sections('order_form');
			submit_button( __('Save Changes', $this->pluginLocale) );
		echo'</form>';
		echo'</div>';
?>