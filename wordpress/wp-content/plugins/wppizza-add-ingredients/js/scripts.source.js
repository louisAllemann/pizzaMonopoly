var wppizzaCartCommentToggle = null;


jQuery(document).ready(function($){

	if ($(".wppizza-open").length > 0){/*are we actually open ?*/

		/*****************************************************************
			[show hide comments in cart]
		*****************************************************************/
		wppizzaCartCommentToggle = function(){
			var cartComments=$('.wppizza-cart-contents .wppizza-ingredients-comments');
			$.each(cartComments,function(i,v){

				var self=$(this);
				var selId = self.attr("id");
				/**if it's the first make it a span to display inline**/
				var count = selId.split("-").pop(-1);
				var tag='div';
				if(count==0){
					tag='span';
				}

				$('#'+selId+'').before( "<"+tag+" class='wppizza-ingr-comment-toggle'>"+wppizza.extend.wppizzaAddIngr.msg.cmttgl+"</"+tag+">" );
			});
		}
		/**also show on re-load of cart/page**/
		wppizzaCartCommentToggle();
		
		$(document).on(''+wppizzaClickEvent+'', '.wppizza-ingr-comment-toggle', function(e){
			e.preventDefault();
			var self=$(this);
			var target=self.closest('.wppizza-item-additional-info-pad').find('.wppizza-ingredients-comments');
			//target.toggle();
			target.slideToggle('100', function(){});
			self.toggleClass( "wppizza-ingr-comment-toggle-sel" );
		});

		/******************************************************************
		*
		*	[replace .wppizza-add-to-cart for selected items with .wppizza-add-ingredient]
		*
		******************************************************************/
		var wppizzaIngrSelectable=wppizza_addingredients.ing;
		var wppizzaIngrTb=wppizza_addingredients.tb;/*[are we using thickbox?]*/
		for(i=0;i<wppizzaIngrSelectable.length;i++){
			var id=wppizzaIngrSelectable[i];
			var elm=$('#wppizza-article-tiers-'+id+' .wppizza-add-to-cart');
			elm.removeClass('wppizza-add-to-cart').addClass('wppizza-add-ingredients');
			if(wppizzaIngrTb.tb==1){
			elm.addClass('thickbox');
			}
		}

		/******************************************************************
		*
		*	[cancel add ingredients]
		*
		******************************************************************/
		$(document).on(''+wppizzaClickEvent+'', '#wppizza-cart-cancel', function(e){
			$('.wppizza-ingredients').empty().remove();
			if(wppizzaIngrTb.tb==1){/*thickbox*/
				tb_remove();
			}
		});
		/******************************************************************
		*
		*	[multi ingredients select (whole/half/quarter]
		*
		******************************************************************/
		$(document).on(''+wppizzaClickEvent+'', '.wppizza-multi-button-main', function(e){
				var selfElm=$(this);
				var self=selfElm.attr('id').split("-");
				var itemId=	self[2];
				var tierId=	self[3];
				var sizeId=	self[4];
				var multi=self[5];
			/*remove all other*/
			if($('.wppizza-ingredients-multi').length>0){
				$('.wppizza-ingredients-multi').empty().remove();
			}
			/*********************************************************
				if we actually want to keep the main button after selection,
				comment out the following and uncomment as described below
			*********************************************************/
			/***remove main button div as we have made our choice****/
			$('.wppizza-multiselect-main').empty().remove();

			/****************if we want to keep the main button after selection, uncomment below************************/
			//			var prevSel=$('.wppizza-multi-button-main-selected');
			//			$.each(prevSel,function(i,v){
			//				$("#" + this.id).removeClass('wppizza-multi-button-main-selected');
			//			});
			//			/**add selected class to this button***/
			//			selfElm.addClass('wppizza-multi-button-main-selected');
			/****************uncomment to here if required**************************************************************/

			/**add sub divs***/
			$('#wppizza-ingredients-'+itemId+'').append('<div id="wppizza-ingredients-multi-'+itemId+'" class="wppizza-ingredients-multi wppizza-ingredients-loading"></div>');

			/*fade it in*/
			$('#wppizza-ingredients-multi-'+itemId+'').fadeIn(500);

			/***********make ajax request****************/
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_ingredients_json',vars:{'type':'getingredients','item':itemId,'tier':tierId,'size':sizeId,'multi':multi}}, function(res) {
				$('#wppizza-ingredients-multi-'+itemId+'').removeClass('wppizza-ingredients-loading');
				$('#wppizza-ingredients-multi-'+itemId+'').html(res.body);
				if(wppizzaIngrTb.tb==1){/*thickbox*/
					if(wppizzaIngrTb.tbstky==1){/*sticky current ingr*/
						$('#TB_title').after(res.head)
					}
					wppizzaAiTbPosition(wppizzaIngrTb.tbstky,null,false,true,true,false);/*resize if required */
				}
			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);$('.wppizza-ingredients').empty().remove();});
		});
		/******************************************************************
		*
		*	[multi ingredients show individual halfs/quarters on button '+wppizzaClickEvent+']
		*
		******************************************************************/
		$(document).on(''+wppizzaClickEvent+'', '.wppizza-multi-tab', function(e){
			var self=$(this);
			var selfVal=self.attr('id').split("-");
			/**hide all others and deselect class**/
			var prevSel=$('.wppizza-multi-tab-selected');
			$.each(prevSel,function(i,v){
				var prevElmId=this.id;
				var prevId=prevElmId.split("-");
				$("#" + prevElmId).removeClass('wppizza-multi-tab-selected');
				$('#wppizza-imulti-'+prevId[2]+'').fadeOut(200);
			});

			/**add selected class to this button***/
			self.addClass('wppizza-multi-tab-selected');
			$('#wppizza-imulti-'+selfVal[2]+'').fadeIn(500);
		});
		/******************************************************************
		*
		*	[list all available ingredients]
		*
		******************************************************************/
		/**add a div container to hold the ingredients, as well as  add buttons etc**/
		$(document).on(''+wppizzaClickEvent+'', '.wppizza-add-ingredients', function(e){
			var self=$(this).attr('id').split("-");
			var itemId=	self[1];
			var tierId=	self[2];
			var sizeId=	self[3];
			/*remove all other*/
			if($('.wppizza-ingredients').length>0){
				$('.wppizza-ingredients').empty().remove();
			}

			/*add a new hidden div when using thickbox popup and show thickbox*/
			if(wppizzaIngrTb.tb==1){
				/*append loading div*/
				$('#wppizza-addingredients-tb>div').append('<div id="wppizza-ingredients-'+itemId+'" class="wppizza-ingredients wppizza-ingredients-loading"></div>');
				/*set opening height and width depending on device etc bearing in mind the set admin values*/
				var setTb=wppizzaAiDeviceInfo();
				/*show thickbox*/
				tb_show(''+wppizzaIngrTb.tblbl+'',"#TB_inline?width="+setTb.w+"&amp;height="+setTb.h+"&amp;inlineId=wppizza-addingredients-tb",null);
				/*position the initial window */
				wppizzaAiTbPosition(wppizzaIngrTb.tbstky,setTb,true,false,false,false);
			}else{
				$('#post-'+itemId+'').append('<div id="wppizza-ingredients-'+itemId+'" class="wppizza-ingredients wppizza-ingredients-loading"></div>');
			}

			/*fade it in*/
			$('.wppizza-ingredients').fadeIn(500);
			/***********make ajax request****************/
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_ingredients_json',vars:{'type':'getingredients','item':itemId,'tier':tierId,'size':sizeId}}, function(res) {
				if(wppizzaIngrTb.tb==1){/*thickbox*/
					/**add title to thickbox header for people that cant remember what they clicked on for more than 2 seconds**/
					$('#TB_ajaxWindowTitle').prepend(res.title+' - ');
					$('#wppizza-ingredients-'+itemId+'').removeClass('wppizza-ingredients-loading');
					$('#wppizza-ingredients-'+itemId+'').html(res.body);
					/*sticky current ingr*/
					if(wppizzaIngrTb.tbstky==1){
						$('#TB_title').after(res.head);
					}
					/*move and adjust thickbox size and position as required*/
					wppizzaAiTbPosition(wppizzaIngrTb.tbstky,setTb,false,false,false,false);
				}else{
					$('#post-'+itemId+' .wppizza-ingredients').removeClass('wppizza-ingredients-loading');
					$('#post-'+itemId+' .wppizza-ingredients').html(res.body);
				}


			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);$('.wppizza-ingredients').empty().remove();});
		});
		/******************************************************************
		*
		*	[add ingredient to item]
		*
		******************************************************************/
		$(document).on(''+wppizzaClickEvent+'', '.wppizza-doingredient,.wppizza-remove-ingredient,.wppizza-ingredient-remove', function(e){

			var self=$(this);
			var selfFieldset=self.closest('fieldset');
			/*stop double click*/
			if(typeof self.attr('disabled')!=='undefined'){
				return;
			}
			self.attr("disabled", "disabled");/*disable add ingr button*/
			selfFieldset.append( "<div class='wppizza-ingredients-disable-click'></div>");/*dealing with slow servers that dont return things on time to reliably do clientside js validation**/


			/*in case we had some error classes added to some groups because we were missing some required ingredients, remove them as we are clearly selectig again*/
			self.closest('ul').removeClass();
			$('.wppizza-multi-tab').removeClass('wppizza-multi-tab-hilight');

			var parent=self.closest('li').attr('id').split("-");
			var postId=self.closest('.wppizza-ingredients').attr('id').split("-");

				if(self.attr('class')=='wppizza-remove-ingredient'){
					var type='removeingredient';
					var multiId=parent[3];
					var itemId=	parent[4];
					var multiType=parent[5];
					var groupId='';
				}
				/*if we use checkboxes (or ie7) they have to be dealt with seperately*/
				if(self.hasClass('wppizza-doingredient') && !self.hasClass('wppizza-ingredient-remove')){
					var type='addingredient';
					var multiId=parent[2];
					var itemId=	parent[3];
					var multiType=	parent[4];

					if(typeof parent[5]!=='undefined'){
					var groupId=parent[5];

					/**check first if we have reached the maximum number of ingredients allowed, may that be per ingredient, or ingredients per group*/
					var addAllowed=wppizzaAddIngredientsCheckAllowed(itemId,groupId,multiId);
					if(addAllowed==false){
						self.removeAttr("disabled", "disabled");/*reenable add ingr button*/
						selfFieldset.find('.wppizza-ingredients-disable-click').remove();/*reenable covered/disabled fielsdet*/
						return;
					}
					}else{var groupId='';}
					self.closest('li').fadeOut(200).fadeIn(200);
				}
				/**deselecting native checkbox-> force remove**/
				if(self.hasClass('wppizza-ingredient-remove')){
					var type='removeingredient';
					var multiId=parent[2];
					var itemId=	parent[3];
					var multiType=parent[4];
					var groupId='';
					self.removeClass('wppizza-ingredient-remove');
				}
			/***********make ajax request****************/
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_ingredients_json',vars:{'type':type,'item':itemId,'groupId':groupId,'postId':postId[2],'multiId':multiId,'multiType':multiType}}, function(res) {

				/**add counter next to ingredient if enabled**/
				var ingrCountElm=$('#wppizza-ingredient-count-'+multiId+'-'+itemId+'-'+multiType+'');
				if(ingrCountElm.length>0){
					/**radios and the like will need to have others set to blank first. only those will have a cssDeselect and cssSelect at the same time**/
					if((typeof res.cssDeselect!=='undefined' && typeof res.cssDeselect!=='cssSelect') ){
					$.each(res.cssDeselect,function(c,j){
						$('#wppizza-ingredient-count-'+multiId+'-'+c+'-'+multiType+'').html('');
					})}
					/*now lets add the counter to the relevant thing**/
					var allIngrQuantities=res.selectedingredients.split(",");
					var ingrQuantity;
					var ingrCount=0;;
					/*get quantity of this ingr **/
					$.each(allIngrQuantities,function(c,j){
						ingrQuantity=j.split(":");
						if(ingrQuantity[0]==itemId && ingrQuantity[1]>0){
							ingrCount=ingrQuantity[1];
						}
					});
					/**print**/
					if(ingrCount>0){
							ingrCountElm.html(''+ingrCount+'x');
					}else{
						ingrCountElm.html('');
					}
				}

				$('#wppizza-current-total').html(res.total);
				$('#wppizza-current-ingredients-'+multiId+'').html(res.ingredients);
				/**set hidden field of currently selected ingredients to check against when putting into cart, in case there were required ingredients**/
				$('#wppizza-selected-ingredients-'+multiId+'').val(res.selectedingredients);

				/**set classes for ingredients where one is allowed and required etc*/
				/**SELECT standard ingredients css*/
				if(typeof res.cssDefaultSelect!=='undefined'){
					$('#wppizza-ingredient-'+res.cssDefaultSelect+'>.wppizza-doingredient').addClass('wppizza-ingredient-selected');
				}
				/**DESELECT standard ingredients css*/
				if(typeof res.cssDefaultRestore!=='undefined'){
					$('#wppizza-ingredient-'+res.cssDefaultRestore+'>.wppizza-doingredient').removeClass('wppizza-ingredient-selected');
				}
				/**DESELECT custom groups ingredients css (must be before SELECT below*/
				if(typeof res.cssDeselect!=='undefined'){
				$.each(res.cssDeselect,function(c,j){
						$('#wppizza-ingredient-'+multiId+'-'+c+'-'+multiType+'-'+j.groupId+'>.wppizza-doingredient').removeClass('wppizza-ingr-'+j.groupType+'-selected').addClass('wppizza-ingr-'+j.groupType+'');
				})}
				/**SELECT group ingredients css must be AFTER deselect above*/
				if(typeof res.cssSelect!=='undefined'){
				$.each(res.cssSelect,function(c,j){
						$('#wppizza-ingredient-'+multiId+'-'+c+'-'+multiType+'-'+j.groupId+'>.wppizza-doingredient').removeClass('wppizza-ingr-'+j.groupType+'').addClass('wppizza-ingr-'+j.groupType+'-selected');
				})}

				/**if we are using standard input checkboxes we need to enable them to remove ingredient too when unchecked**/
				if(typeof res.cssCheckSelect!=='undefined'){

				$.each(res.cssCheckSelect,function(c,j){
					$('#wppizza-ingredient-req-'+multiId+'-'+c+'-'+multiType+'').closest('span').addClass('wppizza-ingredient-remove');
				})}
				/***if we are using native checkboxes and radio, deselect on ingredient remove**/
				if(typeof res.cssCheckDeselect!=='undefined'){
				$.each(res.cssCheckDeselect,function(c,j){
					$('#wppizza-ingredient-req-'+multiId+'-'+c+'-'+multiType+'').prop('checked', false);
					$('#wppizza-ingredient-req-'+multiId+'-'+c+'-'+multiType+'').closest('span').removeClass('wppizza-ingredient-remove');
				})}

				if(wppizzaIngrTb.tb==1){/*thickbox*/
					wppizzaAiTbPosition(wppizzaIngrTb.tbstky,null,false,true,false,false);
				}

				self.removeAttr("disabled", "disabled");/*reenable add ingr button*/
				selfFieldset.find('.wppizza-ingredients-disable-click').remove();/*reenable covered/disabled fielsdet*/
			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);});

		});
		
		/******************************************************************************************************************************
		*
		*	[put diy in cart via ajax, also add directly if "add as is" button is clicked without adding ingredients (if exists]
		*
		******************************************************************************************************************************/
		$(document).on(''+wppizzaClickEvent+'', '#wppizza-diy-to-cart,#wppizza-addasis', function(e){
			var self=$(this);
			var targetContainer=$('.wppizza-ingredients');
			var multiType=$('#wppizza-ingr-multitype').val();
			var textAreas=$(".wppizza-ingredients-textarea");
			var form=$('#wppizza-ingr-form');

			/**check that any required ingredients have been selected*/
			var chkReq=wppizzaAddIngredientsCheckRequired();
			if(chkReq==false){return false;}

			/***add waiting to cart**/
			targetContainer.empty().addClass('wppizza-ingredients-loading');
			/***********add to session, no output****************/
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_ingredients_json',vars:{'type':'diy-to-cart','multiType':multiType,'data':form.serialize()}}, function(res) {

				/**someone has been tampering with the html!!, just silently fail and display message in console*/
				if(typeof res.groupInvalid!=='undefined'){
					console.log(res.groupInvalid);
					/**loose div ingredients div**/
					targetContainer.fadeOut(500,function(){$(this).empty().remove()});
					if(wppizzaIngrTb.tb==1){/*thickbox*/
						tb_remove();
					}
					return;
				}
				/**no ingredients added, so just use a trigger to add as in the master function of WPpizza**/
				var target=$('#wppizza-'+res.id+'-'+res.tier+'-'+res.size+'');
					/*add appropriate class again to trigger**/
					target.removeClass('wppizza-add-ingredients').addClass('wppizza-cart-refresh');
					/**trigger the refresh cart function**/
					target.trigger(''+wppizzaClickEvent+'');

					/*revert class**/
					target.removeClass('wppizza-cart-refresh').addClass('wppizza-add-ingredients');

				/**loose div ingredients div**/
				targetContainer.fadeOut(500,function(){$(this).empty().remove()});

				if(wppizzaIngrTb.tb==1){/*thickbox*/
					tb_remove();
				}
			},'json').complete().error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);targetContainer.empty().remove();});

		});

	}

	/******************************************************************
	*
	*	[get currently selected ingredients of group]
	*
	******************************************************************/
	var wppizzaSelIngrGr = function(g){
		var chkVal=$('#wppizza-selected-ingredients-'+g+'').val();
		return chkVal;
	}

	/******************************************************************
	*
	*	[validate custom groups that require one or more ingredients to be selected]
	*
	******************************************************************/
	var wppizzaAddIngredientsCheckRequired = function(){

		var isValid=true;
		var chkClass=$(".wppizza-ireq");
		var reqNotMet=0;
		var invalidTab='';

		/*remove all previously highlighted/errors**/
		$('.wppizza-list-ingredients>ul').removeClass();

		if (chkClass.length > 0){
			chkClass.each(function(c,j){
				var self=$(this);
				var grpVars=self.val().split("|");/*[0]->ingredients id's in group , [1]->max number of selectable ingredients in group , [2]->max number a single ingredient can be selected, [3]->min number of different ingredients that have to be selected*/
				var reqId=self.attr('id');/**id**/
				var reqVars=reqId.split("-");/**split elm id*/
				var reqType=reqVars[2];/**type of require*/
				var reqMulti=reqVars[3];/*multi id*/
				var reqGroup=reqVars[4];/*group id*/
			//	var minTotalGrp=grpVars[5];/*min of total req in grp*/


				var reqVal=$.parseJSON('['+grpVars[0]+']');/*ingredients id's in group*/
				var chkVal=wppizzaSelIngrGr(reqMulti);

				/*jsonify*/
				var compareKeys = wppizzaAddIngredientsStr2Json(chkVal);
				var hasReq=false;
				var countSelected=0;
				var numIngrSelected=0;
				for(var i=0;i<reqVal.length;i++){
					var chkVal=reqVal[i].toString();/*cast to string*/
					if($.inArray(chkVal, compareKeys.ing)!=-1){
						countSelected++;
						numIngrSelected += +compareKeys.obj[chkVal];
					}
				}

				/*check that we have selected the minimum number of different ingredients*/
				if(countSelected<grpVars[3] || numIngrSelected<grpVars[5]){
					hasReq=false;
				}else{
					hasReq=true;
				}

				/**highlight missing*/
				if(hasReq==false){
					$('#wppizza-ingredients-req-'+reqMulti+'-'+reqGroup+'>ul').addClass('wppizza-list-ingredients-hilight');
					/**tab**/
					$('#wppizza-ingrmulti-'+reqMulti+'').addClass('wppizza-multi-tab-hilight');
					invalidTab=reqMulti;/*get last tab that's missing an item**/
					reqNotMet+=1;
				}
			})
		}
		/**at least one required group item has not been set**/
		if(reqNotMet>0){
			/**trigger last tab with missing items, hmm maybe confuses me if i am suddenly on a different tab**/
			//$('#wppizza-ingrmulti-'+invalidTab+'.wppizza-multi-tab').trigger(''+wppizzaClickEvent+'');
			/**alert**/
			alert(wppizza_addingredients.msg.error );
			isValid=false;
		}
		return isValid;
	}

	/****************************************************************************
	*
	*	[helper to validate custom groups to check if we have reached the maximum
	*	of allowable ingredients per group
	*	check number of allowed different ingedients or of the same ingredient
	*
	****************************************************************************/
	var wppizzaAddIngredientsCheckAllowed = function(itemId,groupId,multiId){

		var isValid=true;
		/*default type groups do not have this input field, so we check against it*/
		var grpInp=$('#wppizza-ingredients-req-'+multiId+'-'+groupId+' .wppizza-ireq');

		if(groupId>=0 && grpInp.length>0){
			/**get values of already selected ingredients**/
			var chkSel=$('#wppizza-selected-ingredients-'+multiId+'').val();

			/*get values of hidden input of this group*/
			var chkInp=grpInp.val().split("|");
			var grpIng=chkInp[0];/*ingredients (id's) in group*/
			var maxIng=chkInp[1];/*maximum of different ingredients*/
			var maxSameIng=chkInp[2];/*maximum of same ingredient*/
			var maxTotalIngGrp=chkInp[4];/*max total sum of all selected ing allowed in this group*/


			var compareKey = wppizzaAddIngredientsStr2Json(chkSel);
			var sameIngCount = compareKey.obj[itemId];
			var noIngCount = compareKey.ing;
			//var noIngSumSel = compareKey.sum;//currently not in use but might come in handy one day

			/**check for maximum number of *the same* ingredient**/
			if(maxSameIng>0 && sameIngCount>=maxSameIng){
				alert(wppizza_addingredients.msg.maxSameIng);
				isValid=false;
				return isValid;
			}

			/**check for maximum number of *different* ingredients in group**/
			if(maxIng>0 || maxTotalIngGrp>0){
				var grpIngArr=$.parseJSON('['+grpIng+']');
				var grpIngArrLen=grpIngArr.length;
				//check sum of total of all ingr in group
				if(maxTotalIngGrp>0){
					var grpIngrTtlSum=0;
					for(var i=0;i<grpIngArrLen;i++){
						if(typeof compareKey.obj[grpIngArr[i]] !=='undefined'){
						grpIngrTtlSum += +compareKey.obj[grpIngArr[i]];
						}
					}
					/*too many to total ingredient sin this group*/
					if(maxTotalIngGrp>0 && grpIngrTtlSum>=maxTotalIngGrp){
						alert(wppizza_addingredients.msg.maxIng);
						isValid=false;
						return isValid;
					}
				}

				/*but ignore the below if we only want to add the same ingredient again*/
				if(maxIng>0){
				if($.inArray(itemId, noIngCount)==-1 ){
					var iCount=0;
					for(var i=0;i<grpIngArrLen;i++){
						var chkVal=grpIngArr[i].toString();/*cast to string*/

						if($.inArray(chkVal, noIngCount)!=-1 ){
							iCount++;
							if(iCount>=maxIng){
								/*max number of ingredients reached for this group*/
								alert(wppizza_addingredients.msg.maxIng);
								isValid=false;
								return isValid;
								break;
							}
						}
					}
			}}}
		}
		return isValid;
	}

	/****************************************************************************
	*
	*	[helper to cast string to json object (whilst making index an integer),
	*	to be used when using inArray() function as "1" !== 1]
	*
	****************************************************************************/
	var wppizzaAddIngredientsStr2Json = function(str){
		var chkVars=str.split(",");/**split elm id*/
		var lngVars=chkVars.length;
		var mapVars={};
		var objVars={};
		//var objSum=0;//currently not in use but might come in handy one day
		for(i=0;i<lngVars;i++){
			if(chkVars[i]!=''){
			var kv=chkVars[i].split(":");
			mapVars[kv[0]]=kv[1];
			objVars[kv[0]]=kv[1];
		//	objSum += +kv[1];//currently not in use but might come in handy one day
			}
		}
		/*return the keys (aka the ingredients id) (returned as strings !) */
		var ingrIds = $.map(mapVars, function(element,index) {return index})

		var chkVals={};
		chkVals.ing=ingrIds;
		chkVals.obj=objVars;
		//chkVals.sum=objSum;//currently not in use but might come in handy one day. total sum of all ingredients selected

		return chkVals;
	}

	/******************************************************************************************************************************************************************************************
	*
	*	[although thickbox/popup works fine on desktops where dpi is 72/96 or similar,
	*	mobile devices might/will have higher dpi which would result in a 100% width thickbox
	*	being way too large. furthermore orientation changes should also be taken care of, so lets
	*	create some helper functions and resize and reposition thickbox as appropriate]
	*
	*******************************
		[at some point I should also make the TB elements into variables to save us a few bytes.....]
	*********************************

	**  based on (but heavily modified):
	**  jQuery Stage -- jQuery Stage Information
	**  Copyright (c) 2013 Ralf S. Engelschall <rse@engelschall.com>
	**
	**  Permission is hereby granted, free of charge, to any person obtaining
	**  a copy of this software and associated documentation files (the
	**  "Software"), to deal in the Software without restriction, including
	**  without limitation the rights to use, copy, modify, merge, publish,
	**  distribute, sublicense, and/or sell copies of the Software, and to
	**  permit persons to whom the Software is furnished to do so, subject to
	**  the following conditions:
	**
	**  The above copyright notice and this permission notice shall be included
	**  in all copies or substantial portions of the Software.
	**
	*******************************************************************************************************************************************************************************************/

	/********some function ******************************************************************************************************/
    /*  calculate some single result value  */
	var wppizzaAiDevicePpi = function (device) {
		var res=130 /*  the reasonable average default value */
		if(device.dp > 1024 && device.dppx <= 1.0){res=100;}/*  large screens with low device pixel ratio   */
		if(device.dp < 1024 && device.dppx >= 2.0){res=160;}/*  small screens with high device pixel ratio  */
		return res;
	}
	var wppizzaAiDeviceOrientation = function (device) {
		var res='square' /*  everything else is nearly square */
		if(device.h > device.w * 1.2){res='portrait';}/*  height is 20% higher than width   */
		if(device.w > device.h * 1.2){res='landscape';}/* width  is 20% higher than height  */
		return res;
	}

	var wppizzaAiDeviceType = function (device) {
		var res='desktop' /*  default */
		if(device.dppx > 1 &&  device.dppx < 2 ){res='tablet';}/* assume if pixelratio <> 2 -> tablet   */
		if(device.dppx >= 2){res='mobile';}/* assume if pixelratio >=2  -> mobile  */
		return res;
	}

	/********get device info ********************************************************************************************************************************************/
    var wppizzaAiDeviceInfo = function () {
        /*  get and calculate device information, some of this is unused but may come in handy in the future  */
        var D         = {};
        D.w           = $(window).width();
        D.h           = $(window).height()-60;/* -> substract 60 to be safe */
        D.dp          = Math.round(10 * Math.sqrt(D.w * D.w + D.h * D.h)) / 10;
        D.dppx        = (typeof window.devicePixelRatio  !== "undefined" ? window.devicePixelRatio : 1.0);
        D.ppi         = wppizzaAiDevicePpi(D);
        D.di          = Math.round(10 * (D.dp / D.ppi)) / 10;
        D.orientation = wppizzaAiDeviceOrientation(D);
        D.device 	  = (D.dp >= 1024 && D.dppx <= 1.0 ? 'desktop' : 'handheld');
        //D.aspect      = (D.orientation=='landscape' ? D.w/D.h : 1);
        D.innerWidth  = (window.innerWidth > 0) ? window.innerWidth : screen.width;
        D.size  	  = (D.innerWidth > 660) ? 'normal' : 'small';
        D.deviceType  = wppizzaAiDeviceType(D);/*mobile tablet or desktop* /

		/**if desktop, just return set values*/
		var tbwh={};
		tbwh.bh=D.h;/*browser height*/
		tbwh.bw=D.w;/*browser width*/
		tbwh.userPercent=(wppizzaIngrTb.tbw/100);

		/**set thickbox width depending on device*/
		if(D.deviceType=='desktop')						{tbwh.w=600*tbwh.userPercent;}/*desktops*/
		if(D.deviceType=='tablet')						{tbwh.w=Math.round((D.innerWidth/2)*tbwh.userPercent);}/*tablets set half width*/
		if(D.deviceType=='mobile' || D.size=='small')	{tbwh.w=(D.innerWidth-60)*tbwh.userPercent;}/*mobiles or screenwidth < 660px set full inner width-60 for margin */

		/**set thickbox margin if mobile or small to be at top of screen as opposed to middle*/
		if(D.deviceType=='mobile' || D.size=='small')	{tbwh.mobile=1}

		/**set thickbox height to the same as width initially if portrait or square*/
		if(D.orientation=='portrait' || D.orientation=='square' ){
			tbwh.h=tbwh.w;
		}else{
			/*set to square if we have the space (leave some margin though) otherwise use availabke height - 60*/
			if(D.h-60>=tbwh.w){
				tbwh.h=tbwh.w
			}else{
				tbwh.h=D.h-60;
			}

		}
		return tbwh;
    };

    /**move and resize thickbox (if required) to have its entirety in viewpoint (as it's fixed position)*******************************************************************/
    var wppizzaAiTbPosition = function (stickyHeader,setTb,ini,addremove,multiselect,ochange) {

    	/***************************************************
    		[size and position the initial window
    		as top==50% in thickbox css,  set negative margin to height/2
    		and add some classes to uniquely identify]
    	****************************************************/
    	if(ini){
    		var iniMargin=Math.round(setTb.h/2)+15;
    		$('#TB_window').addClass('wppizza-add-ingredients-tbw');
    		$('#TB_ajaxWindowTitle').addClass('wppizza-add-ingredients-tbttl');
    		$('#TB_ajaxContent').addClass('wppizza-add-ingredients-tbc');
			$('#TB_window').css({'margin-top':'-'+iniMargin+'px'});/*and/or use height->auto perhaps
    		/**allows us to calculate if scrollbar is visible when using sticky header and resize title bar accordingly as its a fixed position and does not know anything about 100%**/
    		$( "#TB_window" ).append("<div id='wppizza-ai-detect'></div>" );
    		$( "#TB_window" ).prepend("<div id='wppizza-ai-cover'></div>" );/*a div to use to make the bottom when scrolling a bit nicer*/
    		return;
    	}
    	/***************************************************
    		[resize the window depending on sticky header
    		set etc so whole thing fits into viewpoint and
    		then scroll as necessary]
    	****************************************************/
    	/**get relevant sizes of window, title, content etc*/
    		/*set/calculate height of sticky headers and scrollbar*/
    		var tbStickyIngrHeight=0;
    		if(stickyHeader==1){
    			tbStickyIngrHeight=$('#TB_window>.wppizza-ingredients').outerHeight( );/*get sticky Ingr Height*/
    		}
    		if(setTb==null){
    			var setTb={};setTb.bh=$(window).height()-60;
    		}

   			var tbWindowWidth=$('#TB_window').width();/*get total width*/
   			var tbTitleHeight=$('#TB_window>#TB_title').outerHeight( true );/*get title height*/
   			var tbContentHeight=$('#TB_window>#TB_ajaxContent>div').outerHeight( true );/*get content height*/
   			var tbCombinedHeight=tbTitleHeight+tbContentHeight;
   			var tbInnerHeight=tbCombinedHeight+tbStickyIngrHeight;

   			//var tbWindowHeight=$('#TB_window').height();/*get total height - currently unused*/
   			//var tbOuterContentHeight=$('#TB_window>#TB_ajaxContent').outerHeight( true );/*get content height unused - currently unused*/
    	/***************************************************
    		[orientation change get new witdh and resize if needed]
    	***************************************************/
    	if(ochange){
    		var tbwh=wppizzaAiDeviceInfo();
    		$('#TB_window').css({'width':''+tbwh.w+'px','margin-left':'-'+Math.round(tbwh.w/2)+'px'});
    		$('#TB_title').css({'width':''+tbwh.w+'px'});
    		$('#TB_ajaxContent').css({'width':''+(tbwh.w-30)+'px'});
    		/*set new window width**/
    		tbWindowWidth=tbwh.w;
			/**also set width of added ingredients if sticky**/
			if(stickyHeader==1){
			$('#TB_window>.wppizza-current-ingredients-sticky').css({'width':''+(tbWindowWidth-42)+'px'});
    		}
    	}

    	/***********************************************************************
    		[if we are using sticky headers in thickbox, we have to move the
    		margin of the content when adding ingredients as position
    		if the sticky header is fixed and it will/might othersie overlap]
    	************************************************************************/
    	/**hide while animating the rest*/
    	$('#TB_window>#wppizza-ai-cover').css({'display':'none'});
    	if(addremove && stickyHeader==1){
    		/**browser height is > combined title and content -> resize window height and set appropriate margin to display all without having to scroll*/
    		if(setTb.bh>(tbCombinedHeight+tbStickyIngrHeight)){
    				$('#TB_ajaxContent').css({'margin-top':''+(tbStickyIngrHeight+tbTitleHeight)+'px'});/*set position of content*/
    				/**select half/quarter window**/
    				if(multiselect){/*as the content of the div will have changed when coming from multiselect buttons, we have to reset the heights*/
    					$('#TB_window>.wppizza-current-ingredients-sticky').css({'margin':''+tbTitleHeight+'px 0 0 15px','width':''+(tbWindowWidth-42)+'px','position':'fixed','z-index':'1'});/**set distinct margin and width to account for possible scrollbars*/
    					$('#TB_window>#TB_ajaxContent').css({'height':''+(tbContentHeight)+'px','margin-top':''+(tbTitleHeight+tbStickyIngrHeight)+'px'});/*set height of content*/
    				}
    				$('#TB_window').animate({height:(tbCombinedHeight+tbStickyIngrHeight),marginTop:'-'+Math.round((tbCombinedHeight+tbStickyIngrHeight)/2)+'px'},wppizzaIngrTb.tbanim,function(){
    					wppizzaAiStickyHeaderTitleWidth(tbWindowWidth,tbInnerHeight,false);/*resize title depending on whether we have scrollbars or not*/
    				});
    		}else{
 		   			$('#TB_ajaxContent').css({'margin-top':''+(tbStickyIngrHeight+tbTitleHeight)+'px'});/*set height of content*/
 		   			/**select half/quarter window**/
 		   			if(multiselect){/*as the content of the div will have changed when coming from multiselect buttons, we have to reset the heights*/
    					$('#TB_window>.wppizza-current-ingredients-sticky').css({'margin':''+tbTitleHeight+'px 0 0 15px','width':''+(tbWindowWidth-42)+'px','position':'fixed','z-index':'1'});/**set distinct margin and width to account for possible scrollbars*/
						$('#TB_window>#TB_ajaxContent').css({'height':''+(tbContentHeight)+'px','margin-top':''+(tbTitleHeight+tbStickyIngrHeight)+'px'});/*set height of content*/
 		   			}
      				$('#TB_window').animate({height:(setTb.bh),marginTop:'-'+Math.round(setTb.bh/2)+'px'},(wppizzaIngrTb.tbanim),function(){
      					wppizzaAiStickyHeaderTitleWidth(false,setTb.bh,true);/*resize title depending on whether we have scrollbars or not*/
      				});
    		}
    		return;
    	}
    	if(stickyHeader==1){
    		/*****************************
    			[reposition thickbox]
    		*****************************/
    		/**browser height is > combined title and content -> resize window height and set appropriate margin to display all without having to scroll*/
    		if(setTb.bh>tbCombinedHeight+tbStickyIngrHeight){
    			$('#TB_window').animate({height:(tbCombinedHeight+tbStickyIngrHeight),marginTop:'-'+Math.round((tbCombinedHeight+tbStickyIngrHeight)/2)+'px'},wppizzaIngrTb.tbanim,function(){
					$('#TB_window').css({'overflow':'hidden','overflow-y':'auto'});
					$('#TB_window>#TB_title').css({'margin':'0','position':'fixed','z-index':'1'});/**make title sticky too*/
					$('#TB_window>.wppizza-current-ingredients-sticky').css({'margin':''+tbTitleHeight+'px 0 0 15px','width':''+(tbWindowWidth-42)+'px','position':'fixed','z-index':'1'});/**set distinct margin and width to account for possible scrollbars*/
					$('#TB_window>#TB_ajaxContent').css({'height':''+(tbContentHeight)+'px','margin-top':''+(tbTitleHeight+tbStickyIngrHeight)+'px'});/*set height of content*/
					wppizzaAiStickyHeaderTitleWidth(tbWindowWidth,tbInnerHeight,false);/*resize title depending on whether we have scrollbars or not*/
				});
    		}
    		/**browser height is < combined title and content -> set window height to max and make content scrollable*/
    		if(setTb.bh<=tbCombinedHeight+tbStickyIngrHeight){
    			$('#TB_window').animate({height:(setTb.bh),marginTop:'-'+Math.round(setTb.bh/2)+'px'},wppizzaIngrTb.tbanim,function(){
					$('#TB_window').css({'overflow':'hidden','overflow-y':'auto'});
    				$('#TB_window>#TB_title').css({'margin':'0','position':'fixed','z-index':'1'});/**make title sticky too*/
    				$('#TB_window>.wppizza-current-ingredients-sticky').css({'margin':''+tbTitleHeight+'px 0 0 15px','width':''+(tbWindowWidth-42)+'px','position':'fixed','z-index':'1'});/**set distinct margin and width to account for possible scrollbars*/
					$('#TB_window>#TB_ajaxContent').css({'height':''+(tbContentHeight)+'px','margin-top':''+(tbTitleHeight+tbStickyIngrHeight)+'px'});/*set height of content*/
					wppizzaAiStickyHeaderTitleWidth(false,setTb.bh,true);/*resize title depending on whether we have scrollbars or not*/
				});
    		}
		 return;
    	}
    	/*calculate and position when NOT using sticky headers*/
    	if(stickyHeader!=1){
    		/*****************************
    			[reposition thickbox]
    		*****************************/
    		/**browser height is > combined title and content -> resize window height and set appropriate margin to display all without having to scroll*/
    		if(setTb.bh>tbCombinedHeight){
				$('#TB_window>#TB_ajaxContent').css({'height':''+(tbCombinedHeight-tbTitleHeight)+'px'});/*set height of content*/
    			$('#TB_window').animate({height:(tbCombinedHeight),marginTop:'-'+Math.round((tbCombinedHeight)/2)+'px'},(wppizzaIngrTb.tbanim),function(){
					$('#TB_window').css({'overflow':'hidden','overflow-y':'auto'});
					wppizzaAiStickyHeaderTitleWidth(tbWindowWidth,tbInnerHeight,false);/*resize title depending on whether we have scrollbars or not*/
    			});
    		}

    		/**browser height is < combined title and content -> set window height to max and make content scrollable*/
    		if(setTb.bh<=tbCombinedHeight){
    			$('#TB_window>#TB_ajaxContent').css({'height':''+(tbCombinedHeight-tbTitleHeight)+'px'});/*set height of content*/
    			$('#TB_window').animate({height:(setTb.bh),marginTop:'-'+Math.round(setTb.bh/2)+'px'},wppizzaIngrTb.tbanim,function(){
    				$('#TB_window').css({'overflow':'hidden','overflow-y':'auto'});
					wppizzaAiStickyHeaderTitleWidth(false,setTb.bh,true);/*resize title depending on whether we have scrollbars or not*/
				});
    		}

    	}
   }
   /*********************************************************************************************************
   *
   *	[some helper functions]
   *
   *********************************************************************************************************/
    /*when using sticky header , set title width depending on whether we have scrollbars or not*/
    var wppizzaAiStickyHeaderTitleWidth = function (maxWidth,tbInnerHeight,coverReq) {
    	if(!maxWidth){
    		var maxWidth=$('#TB_window>#wppizza-ai-detect').outerWidth(true);
    	}
    	$('#TB_title').css({'width':''+maxWidth+'px'});
    	/**we only need the cover div at bottom if we are / have to  scroll*/
    	if(coverReq){
    		$('#TB_window>#wppizza-ai-cover').css({'margin-top':''+(tbInnerHeight-5)+'px','width':''+maxWidth+'px','height':'5px'}).fadeIn('fast');/**make title sticky too*/
    	}
    };
    /********resize popup on resize window (with a bit of a timeout to not go mad)**************/
    var wppizzaAiResizeTimer;
    $(window).resize(function(ev) {
      if ($('.wppizza-add-ingredients-tbw').length==0){return;}
		clearTimeout(wppizzaAiResizeTimer);
    	wppizzaAiResizeTimer = setTimeout(function() {wppizzaAiTbPosition(wppizzaIngrTb.tbstky,null,false,true,false,true)}, 300);
    });
	/********resize popup on orientationchange*************************************************/
 	$(window).bind("orientationchange", function (ev) {
        if ($('.wppizza-add-ingredients-tbw').length==0){return;}
        wppizzaAiTbPosition(wppizzaIngrTb.tbstky,null,false,true,false,true);
    });
});