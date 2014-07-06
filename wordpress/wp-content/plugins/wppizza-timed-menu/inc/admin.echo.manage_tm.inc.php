<?php
		echo'<div id="'.$this->wpptmSlug.'-settings" class="wppizza-settings wrap">';
		echo"<h2>". $this->wpptmClassName ."</h2>";
		echo'<form action="options.php" method="post">';
		echo'<input type="hidden" name="'.$this->wpptmSlug.'" value="1">';
			settings_fields($this->wpptmSlug);
			do_settings_sections('wppizza_tm');
			if($current!='howto'){
			submit_button( __('Save Changes', $this->wpptmLocale) );
			}
		echo'</form>';
		echo'</div>';
?>