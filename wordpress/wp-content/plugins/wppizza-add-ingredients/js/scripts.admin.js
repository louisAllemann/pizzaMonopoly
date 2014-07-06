jQuery(document).ready(function($){
	/**********************************
	*	[ingredients - add new]
	**********************************/
	$(document).on('click', '#wppizza_add_ingredients', function(e){
		e.preventDefault();
		var self=$(this);
		self.attr("disabled", "true");/*disable button*/
		var newKey = wpPizzaCreateNewKey('wppizza_ingredients_options');
		jQuery.post(ajaxurl , {action :'wppizza_admin_ingredients_json',vars:{'field':'ingredients','id':newKey}}, function(response) {
			$('#wppizza_ingredients_options').append(response);
			self.removeAttr("disabled");/*re-enable button*/
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});

	/**********************************
	*	[custom ingredientgroup - add new]
	**********************************/
	$(document).on('click', '#wppizza_add_ingredients_custom_groups', function(e){
		e.preventDefault();
		var self=$(this);
		self.hide();/*prevent accidental double click so we can reasonably guarantee we have unique keys*/
		var newKey = wpPizzaCreateNewKey('wppizza_ingredients_custom_groups_options');
		jQuery.post(ajaxurl , {action :'wppizza_admin_ingredients_json',vars:{'field':'ingredients_group','id':newKey}}, function(response) {
			$('#wppizza_ingredients_custom_groups_options').append(response);
			self.fadeIn();
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});

	/**********************************
	*change/select group to customise
	**********************************/
	$(document).on('change', '.wppizza_addingredients_group_select', function(e){
		e.preventDefault();
		var self = $(this);
		var selKey = self.val();
		var selId = self.attr("id").split("_").pop(-1);
		var target = $('#wppizza_addingredients_group_edit_'+selId+'');

		jQuery.post(ajaxurl , {action :'wppizza_admin_ingredients_json',vars:{'field':'ingredients_group_select','id':selId,'selKey':selKey}}, function(response) {
			target.css({'display':'block'});
			target.empty().append(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/**********************************
	*filter groups by menu item selected
	**********************************/
	$(document).on('change', '#wppizza_addingredients_custom_groups_filter', function(e){
		$('#wppizza_addingredients_custom_groups_filter_group').val('');/*reset the oter*/
		var filterId=$(this).val();
		var filterOption=$('.wppizza_addingredients_custom_groups_item');
		$.each(filterOption,function(i,v){
			/**reset to show all*/
			if(filterId==''){
				$(this).closest('.wppizza_option').show();
			}else{
				var hasSelected=0;
				$.each($(this).val(),function(e,s){
					if(s==filterId){
						hasSelected=1;
					}
				});
				if(hasSelected==0){
					$(this).closest('.wppizza_option').hide();
				}else{
					$(this).closest('.wppizza_option').show();
				}
			}
		});
	});
	/**********************************
	*filter groups by menu group selected
	**********************************/
	$(document).on('change', '#wppizza_addingredients_custom_groups_filter_group', function(e){
		$('#wppizza_addingredients_custom_groups_filter').val('');/*reset the oter*/
		var filterId=$(this).val();
		var filterOption=$('.wppizza_addingredients_group_select');
		$.each(filterOption,function(i,v){
			/**reset to show all*/
			if(filterId==''){
				$(this).closest('.wppizza_option').show();
			}else{
				if($(this).val()==filterId){
					$(this).closest('.wppizza_option').show();
				}else{
					$(this).closest('.wppizza_option').hide();
				}
			}
		});
	});

	/**********************************
	*filter ingredients by menu group selected
	**********************************/
	$(document).on('change', '#wppizza_addingredients_ingredients_filter_group', function(e){
		$('#wppizza_addingredients_ingredients_filter').val('');/*reset the oter*/
		var filterId=$(this).val();
		var filterOption=$('.wppizza_pricetier_select');
		$.each(filterOption,function(i,v){
			/**reset to show all*/
			if(filterId==''){
				$(this).closest('.wppizza_option').show();
			}else{
				if($(this).val()==filterId){
					$(this).closest('.wppizza_option').show();
				}else{
					$(this).closest('.wppizza_option').hide();
				}
			}
		});
	});


	/**********************************
	*show/hide appropriate options on group type select
	**********************************/
	$(document).on('change', '.wppizza-custom-groups-type', function(e){
		e.preventDefault();
		var self = $(this);
		var selKey = self.val();
		var selId = self.attr("id").split("_").pop(-1);

		/*first hide all*/
		var target0=$('#wppizza-custom-group-max-ing-'+selId+'');
		var target1=$('#wppizza-custom-group-max-same-ing-'+selId+'');
		var target2=$('#wppizza-custom-group-min-ing-'+selId+'');
		var target3=$('.wppizza-custom-group-info-'+selId+'');
		var target4=$('#wppizza-custom-group-presel-'+selId+'');

			target0.hide();
			target1.hide();
			target2.hide();

			target4.hide();
		if(selKey==2){
			target1.show();
		}
		if(selKey==3){
			target0.show();
			target2.show();
		}
		if(selKey==4){
			target0.show();
			target1.show();
			target2.show();
		}
		/*exclude from group or preselect*/
		if(selKey==5 || selKey==6){
			target3.hide();
		}else{
			target3.show();
		}
		/**preselect, add pricetozero option**/
		if(selKey==6){
			target4.show();
		}

	});
	/*****************************
	*	[show/hide custom groups info on menu item]
	*****************************/
	$(document).on('click', '.wppizza_ingr_summary_toggle', function(e){
		e.preventDefault();
		var target=$('.wppizza_ingr_summary');
		target.toggle('400', function(){});
	});
	/*****************************
	*	[copy ingredient]
	*****************************/
	$(document).on('click', '.wppizza_ingr_copy', function(e){
		e.preventDefault();
		var self=$(this);
		self.hide();/*disable button*/
		var aIngrButton=$('#wppizza_add_ingredients');
		aIngrButton.attr("disabled", "true");/*disable button*/
		var newKey = wpPizzaCreateNewKey('wppizza_ingredients_options');
		var copyId = $(this).attr("id").split("_").pop(-1);
		jQuery.post(ajaxurl , {action :'wppizza_admin_ingredients_json',vars:{'field':'ingredients','id':newKey,'copyId':copyId}}, function(response) {
			$('#wppizza_ingredients_options').append(response);
			aIngrButton.removeAttr("disabled");/*re-enable button*/
			self.show();/*re-enable button*/
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*****************************
	*	[copy ingredients of whole group]
	*****************************/
	$(document).on('click', '#wppizza_ingredients_copy', function(e){
		e.preventDefault();
		var self=$(this);
		self.hide();/*disable button*/
		var aIngrButton=$('#wppizza_add_ingredients');
		aIngrButton.attr("disabled", "true");/*disable button*/
		var sourceId = $('#wppizza_ingredients_copy_source').val();
		var destId = $('#wppizza_ingredients_copy_dest').val();

		if(sourceId=='' || destId==''){
			alert('please select a source and destination');
			aIngrButton.removeAttr("disabled");/*re-enable button*/
			self.show();/*re-enable button*/
			return;
		}


		jQuery.post(ajaxurl , {action :'wppizza_admin_ingredients_json',vars:{'field':'ingredients_group_copy','sourceId':sourceId,'destId':destId}}, function(response) {
			alert(response);
			/**reload page**/
			var url=window.location;
        	window.location.href = url;
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*****************************
	*	[show/hide custom groups settings option]
	*****************************/
	$(document).on('click', '.wppizza-custom-groups-visibility', function(e){
		e.preventDefault();
		var self=$(this);
		var target=self.closest('.wppizza_option').find('.wppizza_addingredients_group_edit');
		target.toggle('400', function(){});
	});
	/*****************************
	*	[validate custom groups]
	*****************************/
	$(document).on('click', '#wppizza_addingredients-custom-groups-save', function(e){
		/**remove all previously highlighted**/
		var prevSel=$('.wppizza-ingr-cg-highlight');
			$.each(prevSel,function(i,v){
				var prevElmId=this.id;
				$("#" + prevElmId).removeClass('wppizza-ingr-cg-highlight');
			});
		var self=$(this);
		self.attr("disabled", "true");/*disable button*/
		jQuery.post(ajaxurl , {action :'wppizza_admin_ingredients_json',vars:{'field':'ingredients_group_validate','data':$('#wppizza_addingredients-custom-groups-form').serialize()}}, function(res) {
			verifyCustomGroups(res);
			self.removeAttr("disabled");/*re-enable button*/
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
		return false;
	});

	var verifyCustomGroups=function(res){
		if(res.valid==0){
			/*highlight doubles*/
			$('#wppizza-custom-groups-header-'+res.grpId_1+'').addClass("wppizza-ingr-cg-highlight");
			$('#wppizza-custom-groups-header-'+res.grpId_2+'').addClass("wppizza-ingr-cg-highlight");
			$('#wppizza_addingredients-ingredients_custom_group-'+res.grpId_1+'-ingredient-'+res.ingrID+'').addClass("wppizza-ingr-cg-highlight");
			$('#wppizza_addingredients-ingredients_custom_group-'+res.grpId_2+'-ingredient-'+res.ingrID+'').addClass("wppizza-ingr-cg-highlight");
			/*show alert*/
			alert(res.msg);


			return false;
		}else{
			/*all is well->submit*/
			$('#wppizza_addingredients-custom-groups-form').submit();
		}
	}

})