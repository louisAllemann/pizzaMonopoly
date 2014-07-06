<?php
		echo'<div id="'.$this->dbpSlug.'-settings" class="wrap">';
		echo"<h2>". $this->dbpClassName ."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->dbpSlug.'" value="1">';
			settings_fields($this->dbpSlug);
			do_settings_sections('deliveries');
			submit_button( __('Save Changes', $this->dbpLocale) );
		echo'</form>';
		echo'</div>';
?>