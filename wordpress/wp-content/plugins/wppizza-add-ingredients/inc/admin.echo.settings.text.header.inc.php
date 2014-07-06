<?php settings_errors(); ?>
<?php
	if(isset($_GET['tab']) && $_GET['tab']=='manual'){
		echo "<span style='color:red'>".__('if you seem to encounter a limit as to how many ingredients or custom groups you can add, <a href="http://www.wp-pizza.com/downloads/wppizza-add-ingredients/" target="_blank">please read this faq/troubleshooting guide</a>', $this->pluginLocale)."</span>";
	}
	if(isset($_GET['tab']) && $_GET['tab']=='ingredients'){
		echo '<h4>'.__('If you want to allow customers to add extra ingredients to a meal (adding extra cheese to a pizza for example), add all possible ingredients here, and enable "allow extra ingredients" on that particular meal.<br/>Only items checked here will be available to be selected by the customer.', $this->pluginLocale).'</h4>';
		
	}
	if(isset($_GET['tab']) && $_GET['tab']=='custom-groups'){

	}
?>