<?php settings_errors(); ?>
<?php
	if(!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab']=='deliveries')){
		echo '<p style="color:red">'.__('Note: Before you start adding post/zipcodes, make sure you have made the right general selection for your shop in WPPIzza->Settings->Order Settings->Delivery Charges. (i.e "Free Delivery", "Fixed Delivery" or "Charges per item") ', $this->dbpLocale).'<br />';
		echo ''.__('Any post/zipcodes you add below will apply to whatever has been set there. Therefore, if you change your Main Delivery Charges Settings in the future, you will have to add your postcodes again for these new settings (although postcodes for the previous one will remain, so you could switch back in the future if you so wish).', $this->dbpLocale).'</p>';

		echo '<h4>'.__('Start adding a new Post/Zipcode by clicking on "add new delivery area"', $this->dbpLocale).'</h4>';
		echo '<div><p>';
		echo ''.__('Add your Post/Zip codes as required by entering it into the relevant field.', $this->dbpLocale).'<br/>';
		echo ''.__('Enter an email address (multiple separated by comma) the order should be sent to when this Post/Zip code is selected by the client or leave empty to send it to the email address defined in your general wppizza->order settings.', $this->dbpLocale).'<br/>';
		echo ''.__('Set delivery charges and free delivery amount applicable to this Post/Zip Code, enable and save.', $this->dbpLocale).' ';
		echo ''.__('Charges set will be applied according to your delivery charges settings in wppizza->order settings->delivery charges and - of course - depending on the selected Post/Zip Code by the User.', $this->dbpLocale).'<br/>';
		echo '</p></div>';
	}
?>