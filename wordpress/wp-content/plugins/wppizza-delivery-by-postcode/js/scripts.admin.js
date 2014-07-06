jQuery(document).ready(function($){
	/**********************************
	*	[postcode - add new]
	**********************************/
	$(document).on('click', '#wppizza_add_delivery_areas', function(e){
		e.preventDefault();
		var self=$(this);
		self.hide();/*prevent accidental double click so we can reasonably guarantee we have unique keys*/
		var newKey = wpPizzaCreateNewKey('wppizza_delivery_areas_options');
		jQuery.post(ajaxurl , {action :'wppizza_dbp_admin_json',vars:{'field':'delivery_areas','id':newKey}}, function(response) {
			$('#wppizza_delivery_areas_options').append(response);
			self.fadeIn();
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
})