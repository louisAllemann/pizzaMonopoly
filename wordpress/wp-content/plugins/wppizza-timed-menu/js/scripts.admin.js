jQuery(document).ready(function($){
	/**if we are on the category edit page, we need to uupdate options too if the order changes**/
	if(pagenow=='edit-wppizza_menu'){
		var wpPizzaTmCategories = $('#the-list');	
		wpPizzaTmCategories.bind( "sortupdate", function(event, ui) {
			jQuery.post(ajaxurl , {action :'wppizza_tm_admin_json',vars:{'field':'cat_sort','order': wpPizzaTmCategories.sortable('toArray').toString()}}, function(response) {
			//	console.log(response);
			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});  			
		});
	}
	/**********************************
	*	[timed - add new]
	**********************************/
	$(document).on('click', '#wppizza_add_timed_items', function(e){
		e.preventDefault();
		var wppizzaTmCurrDispl=$('input[name="wppizza_timed_menu[plugin_data][display_type]"]:checked');
		 if (wppizzaTmCurrDispl.length<=0) {
			alert("Please select your display options first !"); 
			return;
		}		
		var self=$(this);
		self.hide();/*prevent accidental double click so we can reasonably guarantee we have unique keys*/
		var newKey = wpPizzaCreateNewKey('wppizza_timed_items_options');
		jQuery.post(ajaxurl , {action :'wppizza_tm_admin_json',vars:{'field':'timed_items','id':newKey,'displayOption':wppizzaTmCurrDispl.val()}}, function(response) {			
			$('#wppizza_timed_items_options').append(response);
			self.fadeIn();
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	
	/**********************************
	*	[change display options]
	**********************************/
	$(document).on('change', 'input[name="wppizza_timed_menu[plugin_data][display_type]"]', function(e){
		if(!$("#wppizza_timed_items_options").is(":visible")){
			$("#wppizza_timed_items_options").show();
		}
		if($(this).val()=='posts_pages'){
			$("#wppizza_timed_items_options").find("tr .wppizza-tm-int").hide();
			$("#wppizza_timed_items_options").find("tr .wppizza-tm-pp").show('slow');
		}
		if($(this).val()=='internal'){			
			$("#wppizza_timed_items_options").find("tr .wppizza-tm-pp").hide();
			$("#wppizza_timed_items_options").find("tr .wppizza-tm-int").show('slow');
			
		}	
		$(".wppizza_timed_items_display").val($(this).val());
		
	});
})
