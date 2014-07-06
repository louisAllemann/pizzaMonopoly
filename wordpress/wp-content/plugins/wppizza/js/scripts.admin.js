/**only loaded when post_type == wppizza**/
jQuery(document).ready(function($){
	/**if we are on the category edit page, make it sortable and update on new sort**/
	if(pagenow=='edit-wppizza_menu'){
		var wpPizzaCategories = $('#the-list');
		wpPizzaCategories.sortable({
			update: function(event, ui) {
				jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'cat_sort','order': wpPizzaCategories.sortable('toArray').toString()}}, function(response) {
					//console.log(response);
				},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});

			}
		});
	}
	/*******************************************
	*	[functions]
	********************************************/
	wpPizzaCreateNewKey = function(objId,btn){
		if(typeof btn!=='undefined'){
			btn.hide();/*disable*/
		}
		var self=$('#'+objId+' .wppizza-getkey');
		var currentInputs=self.get();
		/*make array if keys*/
		var keyIds = [];
			for (var i = 0; i < currentInputs.length; i++) {
				keyIds.push($(currentInputs[i]).attr("id").split("_").pop(-1));
			}
			var maxKey = Math.max.apply( null, keyIds );
			/*if none yet, start at zero**/
			if(maxKey<0){var newKey = 0;}else{var newKey = (maxKey+1);}
		return newKey;
	}
	/******************************
	* print order history
	*******************************/
	$(document).on('click touchstart', '.wppizza-print-order', function(e){
			e.preventDefault();
			var ordId=$(this).attr('id').split("-").pop(-1);
            //Get the value of textareas
            var initiator=$('#wppizza_order_initiator_'+ordId+'').val();
            var transaction_id=$('#wppizza_order_transaction_id_'+ordId+'').val();
            var order=$('#wppizza_order_details_'+ordId+'').val();
            var customer=$('#wppizza_order_customer_details_'+ordId+'').val();
            var notes=$('#wppizza-order-notes-'+ordId+'').val();

            //store HTML of current whole page in variable
            var currentPage = document.body.innerHTML;

            //Re-create the page HTML with required info only
           var wpPizzaOrderHistory=
              "<html><head><title></title></head><body>" +
              (initiator) + "<br />" +
              (transaction_id)  + "<br /><br />" +
              wppizzaNl2br(customer) + "<br />" +
              wppizzaNl2br(order) + "<br /><br />" +
              wppizzaNl2br(notes) + "</body></html>";

            //Print Page : as Android doesnt understnd this, let's open a window
            var wpPizzaOrderHistoryWindow = window.open("","WppizzaOrder","width=750,height=550");
			wpPizzaOrderHistoryWindow.document.write(wpPizzaOrderHistory);

            wpPizzaOrderHistoryWindow.focus();
			/*android doesnt understand .print() not my fault really*/
			wpPizzaOrderHistoryWindow.print();

	});
	/**nl2br when printing*/
	var wppizzaNl2br =function(str, is_xhtml) {
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
		/**nl2br*/
		var printFormatted=(str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
		/**format any 2 spaces as nbsp to keep formatting*/
		printFormatted=printFormatted.replace(/\s{2}/g, '&nbsp;&nbsp;');

		return printFormatted;
	}
	/*******************************
	*	[time picker]
	*******************************/
    $('#wppizza-settings,.wppizza-settings').on('click', '.wppizza-time-select', function(e){
    	e.preventDefault();
    	$(this).timepicker({
    	hourText: 'Hour',
		minuteText: 'Min',
    	amPmText: ['', ''],
		hours: {
        starts: 0,                // First displayed hour
        ends: 23                  // Last displayed hour
    	},
    	minutes: {
    		starts: 0,                // First displayed minute
    		ends: 45,                 // Last displayed minute
    		interval: 15               // Interval of displayed minutes
		}}).timepicker( "show" );
    });
    /*******************************
	*	[date picker]
	*******************************/
    $('#wppizza-settings,.wppizza-settings').on('click', '.wppizza-date-select', function(e){
    	e.preventDefault();
    	$(this).datepicker({dateFormat : 'dd M yy'}).datepicker( "show" );
    });
    
    /*******************************
	*
	*
	*	[reports]
	*
	*
	*******************************/    
    /*******************************
	*	[reports - date picker]
	*******************************/
	$(document).on('click', '#wppizza_reports_start_date,#wppizza_reports_end_date', function(e){    
    	e.preventDefault();
    	$(this).datepicker({dateFormat : 'yy-mm-dd'}).datepicker( "show" );
    });    
	/******************************
	*	[reports - default options range select - onchange]
	******************************/
	$(document).on('change', '#wppizza-reports-set-range', function(e){
		var self=$(this);
		var selVal=self.val();
		var theUrl=window.location.href.split('?')[0];
		var redirUrl=theUrl+'?post_type=wppizza&page=wppizza-reports';
		if(selVal!=''){
			redirUrl+='&report=' + selVal;
		}
		window.location.href=redirUrl;
	});    
	/******************************
	*	[reports - custom range]
	******************************/    
	$(document).on('click', '#wppizza_reports_custom_range', function(e){
		var theUrl=window.location.href.split('?')[0];
		var redirUrl=theUrl+'?post_type=wppizza&page=wppizza-reports';
		var startDate=$('#wppizza_reports_start_date').val();
		var endDate=$('#wppizza_reports_end_date').val();
		if(startDate!='' && endDate!=''){
			redirUrl+='&from=' + startDate;
			redirUrl+='&to=' + endDate;
		}
		window.location.href=redirUrl;
	});       
	/******************************
	*	[reports - export]
	******************************/    
	$(document).on('click', '#wppizza_reports_export', function(e){
		var theUrl=window.location.href.split('?')[0];
		var redirUrl=theUrl+'?post_type=wppizza&page=wppizza-reports';
		var startDate=$('#wppizza_reports_start_date').val();
		var endDate=$('#wppizza_reports_end_date').val();
		if(startDate!='' && endDate!=''){
			redirUrl+='&from=' + startDate;
			redirUrl+='&to=' + endDate;
		}
			redirUrl+='&export=true';
		window.location.href=redirUrl;
	});     
    
	/*******************************
		[opening times - add new]
	*******************************/
	$(document).on('click', '#wppizza_add_opening_times_custom', function(e){
		e.preventDefault();
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'opening_times_custom'}}, function(response) {
			$('#wppizza_opening_times_custom_options').append(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*******************************
		[times closed - add new]
	*******************************/
	$(document).on('click', '#wppizza_add_times_closed_standard', function(e){
		e.preventDefault();
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'times_closed_standard'}}, function(response) {
			$('#wppizza_times_closed_standard_options').append(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*******************************
	*	[size option - add new]
	*******************************/
	$(document).on('click', '#wppizza_add_sizes', function(e){
		e.preventDefault();var self=$(this);
		var newKey = wpPizzaCreateNewKey('wppizza_sizes_options',self);
			var newFields=parseInt($('#wppizza_add_sizes_fields').val());
			if(newFields>=1){
				jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'sizes','id':newKey,'newFields':newFields}}, function(response) {
					var html=response;
					$('#wppizza_sizes_options').append(html);
					self.show();/*reenable add button*/
				},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
			}
	});
	/******************************
	*	[additives - add new]
	******************************/
	$(document).on('click', '#wppizza_add_additives', function(e){
		e.preventDefault();var self=$(this);
		var newKey = wpPizzaCreateNewKey('wppizza_additives_options',self);
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'additives','id':newKey}}, function(response) {
			$('#wppizza_additives_options').append(response);
			self.show();/*reenable add button*/
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/******************************
	*	[category - add new]
	******************************/
	$(document).on('click', '#wppizza_add_meals', function(e){
		e.preventDefault();var self=$(this);
		var newKey = wpPizzaCreateNewKey('wppizza_meals .wppizza_meals_category',self);
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'meals','id':newKey}}, function(response) {
			var html='<span class="wppizza_option">';
			html+=response;
			html+='<div id="wppizza_category_items_'+newKey+'" class="wppizza_category_items"></div>';
			html+='</span>';
			$('#wppizza_meals_options').append(html);
			self.show();/*reenable add button*/
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/******************************
		[menu/meal category - add new item to category]
	******************************/
	$(document).on('click', '.wppizza_add_meals_item', function(e){
		e.preventDefault();var self=$(this);
		var self=$(this);
		var CatId=self.attr('id').split("_").pop(-1);
		var newKey = wpPizzaCreateNewKey('wppizza_category_items_'+CatId+'',self);
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'meals','item':1,'id':CatId,'newKey':newKey}}, function(response) {
			$('#wppizza_category_items_'+CatId+'').prepend(response);
			self.show();/*reenable add button*/
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/******************************
	*	[pricetier select - onchange]
	******************************/
	$(document).on('change', '.wppizza_pricetier_select', function(e){
		var self=$(this);
		var selId=self.val();
		var fieldArray=self.attr('name').replace("[sizes]","");
		var classId=self.attr('class').split(" ").pop(-1);
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'sizeschanged','id':selId,'inpname':fieldArray,'classId':classId}}, function(response) {
			self.closest('.wppizza_option').find('.wppizza_pricetiers').empty().html(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});

	/******************************
	*	[order form field type select - onchange]
	******************************/
	$(document).on('change', '.wppizza_order_form_type', function(e){
		var self=$(this);
		var id=self.attr('id').split("_").pop(-1);
		var val=self.val();
		//alert(val);
		self.closest('td').find('.wppizza_order_form_select input').val('');//empty value
		if(val=='select'){
			self.closest('td').find('.wppizza_order_form_select').css('display', 'block');
		}else{
			self.closest('td').find('.wppizza_order_form_select').css('display', 'none');

		}

	});
	/*****************************
	*	[remove an option]
	*****************************/
	$(document).on('click', '.wppizza-delete', function(e){
		e.preventDefault();
		var self=$(this);
		/**we must have at least one size option**/
		if(self.hasClass('sizes')){
			var noOfSizes=$('#wppizza_sizes_options>span').length;
			if(noOfSizes<=1){
				alert('Sorry, at least one size option must be defined');
				return;
			}
		}
		/**allow to remove by class and not just span*/
		var remItem=$(this).closest('.wppizza-row-remove');
		if(remItem.length>0){
			remItem.remove();
		}else{
			$(this).closest('span').remove();
		}
	});
	/*****************************
	*	[poll orders]
	*****************************/
	var pollObj=$('#history_orders_poll_enabled');
	if(pollObj.length>0){
		var pollingInterval=$('#history_orders_poll_interval').val();
		var pollOrdersInterval=setInterval(function(){pollOrders()},(pollingInterval*1000));
	}
	/*****************************
	*	[change poll interval]
	*****************************/
	$(document).on('change', '#history_orders_poll_interval', function(e){
		var pollingInterval=$(this).val();
		clearInterval(pollOrdersInterval);
		pollOrdersInterval=setInterval(function(){pollOrders()},(pollingInterval*1000));
	});
	/*****************************
	*	[do poll if enabled]
	*****************************/
	var pollOrders=function(){
	if($('#history_orders_poll_enabled').is(':checked')){
		$('#wppizza-orders-polling').addClass('wppizza-load');
		var triggerTarget=$('#history_get_orders');
		triggerTarget.trigger('click');
	}}
	/*****************************
	*	[update order status]
	*****************************/
	$(document).on('change', '.wppizza_order_status', function(e){
		var self=$(this);
		var selId=self.attr('id').split("-").pop(-1);
		var selVal=self.val();
		var initiator=$('#wppizza_order_initiator_ident_'+selId+'').val();
		var selClass=selVal.toLowerCase();
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'orderstatuschange','id':selId,'selVal':selVal,'initiator':initiator}}, function(response) {
			self.closest('tr').removeClass().addClass('wppizza-ord-status-'+selClass+'');
			$('#wppizza_order_update-'+selId).html(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*****************************
	*	[update order notes]
	*****************************/
	$(document).on('click', '.wppizza-order-add-notes', function(e){
		var self=$(this);
		var selId=self.attr('id').split("-").pop(-1);
		$('#wppizza-order-notes-tr-'+selId+',#wppizza-order-has-notes-tr-'+selId+'').fadeIn("slow");
	});
	$(document).on('click', '.wppizza-order-do-notes', function(e){
		var self=$(this);
		var selId=self.attr('id').split("-").pop(-1);
		var selVal=$('#wppizza-order-notes-'+selId+'').val();
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'ordernoteschange','id':selId,'selVal':selVal}}, function(response) {
			//$('#wppizza_order_update-'+selId).html(response);
			if(response<=0){
				self.closest('tr').fadeOut(250);
				$('#wppizza-order-add-notes-'+selId+'').fadeIn(250,function(){alert('ok');});
			}else{
				$('#wppizza-order-add-notes-'+selId+'').fadeOut(250,function(){alert('ok');});
			}

		},'text').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});

	/******************************
	*	[show orders]
	******************************/
	$(document).on('click', '#history_get_orders', function(e){
		e.preventDefault();
		var limit=$('#history_orders_limit').val();
		var orderstatus=$('#history_orders_status').val();
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'get_orders','limit':limit,'orderstatus':orderstatus}}, function(response) {
			$('#wppizza_history_orders').html(response.orders);
			$('#wppizza_history_totals').html(response.totals);
			$('#wppizza-orders-polling').removeClass();
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/******************************
	*	[get total of all orders]
	******************************/
	$(document).on('click', '#wppizza_history_totals_getall', function(e){
		e.preventDefault();
		var limit=$('#history_orders_limit').val();
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'get_orders_total'}}, function(response) {
			$('#wppizza_history_totals').html(response.totals);
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});	
	/******************************
	*	[delete orders]
	******************************/
	$(document).on('click', '.wppizza_order_delete', function(e){
		e.preventDefault();
		if(!confirm('are you sure ?')){ return false;}
		var self=$(this);
		var ordId=self.attr('id').split("_").pop(-1);
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'delete_orders','ordId':ordId}}, function(response) {
			alert(response);
			self.closest('tr').empty().remove();
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/******************************
	*	[delete abandoned orders]
	******************************/
	$(document).on('click', '#wppizza_order_abandoned_delete', function(e){
		e.preventDefault();
		if(!confirm('are you sure ?')){ return false;}
		var days=$('#wppizza_order_days_delete').val();
		var failed=$('#wppizza_order_failed_delete').is(':checked');
		jQuery.post(ajaxurl , {action :'wppizza_admin_json',vars:{'field':'delete_abandoned_orders','days':days,'failed':failed}}, function(response) {
			alert(response);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);});
	});
	/*****************************
	*	[show gateway settings option]
	*****************************/
	$(document).on('click', '.wppizza-gateway-show-options', function(e){
		//alert('alarm');
		var self=$(this);
		$('.wppizza-gateway-settings').slideUp();
		self.closest('.wppizza-gateway').find('.wppizza-gateway-settings').slideDown();
	});
	/*********************************************************
	*	[show order form tips/surcharges option - not yet in use]
	*********************************************************/
	$(document).on('click', '#wppizza-toggle-tgs', function(e){
		$('#wppizza_order_form tr.ctips,#wppizza_order_form tr.csurcharges').toggle("slow");
	});

})