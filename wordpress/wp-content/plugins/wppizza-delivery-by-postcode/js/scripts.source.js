jQuery(document).ready(function($){
	/*do we actually have a thickbox container**/
	if ($("#wppizza-dbp-thickbox").length > 0){
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_dbp_json',vars:{'type':'dbp-thickbox'}}, function(res) {
			if(res.nothickbox==false){
			jQuery(function(){
				var tbElm=$("#wppizza-dbp-thickbox");
				var tbCap=tbElm.find('legend').text();
				tb_show(tbCap,"#TB_inline?height=300&amp;width=300&amp;inlineId=wppizza-dbp-thickbox",null);
			});
			}
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);});
	}
	/******************************************************************
	*
	*	[set postcode and associated delivery and emails]
	*
	******************************************************************/
	$(document).on('change', '.wppizza-dbp-area,#wppizza-dbp-area', function(e){
		var self=$(this);
		var wppizzaForm=$('#wppizza-send-order');
		var data=false;
		if(wppizzaForm.length>0){
			data=wppizzaForm.serialize();	
		}
		var wppizzaDbpId=self.val();
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_dbp_json',vars:{'type':'dbp','dbpid':wppizzaDbpId,'data':data}}, function(res) {
			window.location.href=window.location.href;/*make sure page gest reloaded without confirm*/
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);});
	});
	/******************************************************************
	*
	*	[instant search /  autocomplete]
	*
	******************************************************************/
	if ($("#wppizza-dbp-area-is").length > 0){
		var self=$("#wppizza-dbp-area-is");
		var curValElm=$("#wppizza-dbp-area");
		var resCont=self.next('.wppizza-dbp-ac');

		jQuery.post(wppizza.ajaxurl , {action :'wppizza_dbp_json',vars:{'type':'dbpis'}}, function(res) {
			var wppizza_dbp=res.areas;
			var wppizza_dbp_areas=[];
			var wppizza_dbp_ids={};
			$.each(wppizza_dbp, function(k,item){
				wppizza_dbp_areas.push(item);
				wppizza_dbp_ids[item] = String(k);
			});
			self.smartAutoComplete({source: wppizza_dbp_areas, forceSelect: true, minCharLimit:0, resultsContainer: resCont});
			self.bind({
	          		noResults: function(ev){
					resCont.html(res.noresults);
					ev.preventDefault();
          			},
					lostFocus:function(ev) {
						var newVal=wppizza_dbp_ids[self.val()];
						if(typeof newVal==='undefined'){
						var newVal='';
						}
						var currVal=curValElm.val();
						if(currVal!=newVal){
							curValElm.val(newVal);
							curValElm.trigger('change');
						}
				}
        	});
		},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);});
	}
})