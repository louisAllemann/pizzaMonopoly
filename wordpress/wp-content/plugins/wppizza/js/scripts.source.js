var wppizzaClickEvent='click';

jQuery(document).ready(function($){

	/*******************************************************
	*	[detect browser supported events
	*	and use touchstart if click is not supported
	*	avoids double trigger as using 'click touchstart'
	*	appears to trigger twice in Android devices
	*******************************************************/

	var wppizzaCheckEventSupport = function(eventName){

    	/*iSomething understands click, but doesnt want to do things with it so lets force touchstsrt*/
    	if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/iPod/i))) {
   			var bindEvent='touchstart';/*default to touchstart for iCrap*/
   			return bindEvent;
		}

    	var el = document.createElement('div');
    	eventName = 'on' + eventName;
    	var isSupported = (eventName in el);
    	if (!isSupported) {
      		el.setAttribute(eventName, 'return;');
      		isSupported = typeof el[eventName] == 'function';
    	}
    	el = null;
    	var bindEvent='click';/*default touchstart*/
    	if(!isSupported){
    		bindEvent='touchstart';	/*if browser does not support touchstart, use click*/
    	}
    	return bindEvent;
  	}
  	wppizzaClickEvent=wppizzaCheckEventSupport("click");


	/****************************************************************************************************************************************************************************************************
	*
	*	[keep cart static on page when scrolling]
	*	[always adds wppizza-cart-fixed class when scrolling is relevant so we can set things if needed]
	*	[will either be executed on page load or after the cart has been created if a cache plugin is used]
	****************************************************************************************************************************************************************************************************/
	var wppizzaCartStickyLoad= function(){
		var wppizzaCartStickyElm=$('.wppizza-cart-sticky');/**get all elements*/
		var wppizzaCartStickyScrollTimeout;/**ini timeout*/
		/**********************
			[get bottom limit]
		***********************/
		var wppizzaCartStickyLimitBottomElm=false
		/**set bottom scroll limit by div id**/
		if(typeof wppizza.crt.lmtb !=='undefined' && $('#'+wppizza.crt.lmtb+'').length>0){
			wppizzaCartStickyLimitBottomElm=$('#'+wppizza.crt.lmtb+'');/*set element*/
			var wppizzaCartStickyLimitOffset=0;/**set limit offset to be substracted from wppizza.crt.mt  if required **/
			var wppizzaCartStickyLimitBottom=wppizzaCartStickyLimitBottomElm.offset().top;/*get element top*/
		}
		var wppizzaCartStickyScrollTop = $(window).scrollTop()+wppizza.crt.mt;/*get top poxition where state toggle (browser + set margin)*/


		/**initialize a couple of vars vor the elements*/
		var wppizzaCartStickySelf = [];
		var wppizzaCartStickyVars = [];
		var wppizzaCartStickyParent = [];


		var wppizzaCartStickyAnimation = false;
		if(wppizza.crt.anim>0 && wppizza.crt.fx!=''){
			wppizzaCartStickyAnimation = true;
		}
		/**get all aplicable elements and their variables**/
		if(wppizzaCartStickyElm.length>0){
			$.each(wppizzaCartStickyElm,function(e,v){
				/***get the element object and add vars as required**/
				wppizzaCartStickySelf[e]=$(this);

				/**wrap in wraper div which is then set to height of cart to stop things jumping around**/
				wppizzaCartStickySelf[e].wrap( "<div class='wppizza-cart-wrap'></div>" );

				wppizzaCartStickyVars[e]=wppizzaCartStickySelf[e].css(["backgroundColor"]);
				wppizzaCartStickyVars[e]['offset-top']= wppizzaCartStickySelf[e].offset().top;/*offset from top of page**/
				wppizzaCartStickyVars[e]['state']='';/**initialize state so - when set below - we dont ever need do the same thing multiple times**/
				wppizzaCartStickyVars[e]['height-int']=wppizzaCartStickySelf[e].height();/*make sure we also have height an integer and call it height-int instead of just height or jQuery 1.8.3 gets confused when SETTING height*/
				wppizzaCartStickyVars[e]['width-int']=wppizzaCartStickySelf[e].width();/*make sure we also have width an integer and call it width-int instead of just width or jQuery 1.8.3 gets confused when SETTING width*/

				/**set limit bottom**/
				if(wppizzaCartStickyLimitBottomElm && wppizzaCartStickyLimitBottom>(wppizzaCartStickyVars[e]['offset-top']+wppizzaCartStickyVars[e]['height-int'])){
					wppizzaCartStickyVars[e]['limit-bottom']=Math.floor(wppizzaCartStickyLimitBottom-wppizzaCartStickyVars[e]['height-int']);
				}

				/*get parent element so we can set height on it*/
				wppizzaCartStickyParent[e] = wppizzaCartStickySelf[e].parent();

				/*set distinct width of element so we dont have to set it all the time when scrolling or setting fixed position*/
				wppizzaCartStickySelf[e].width(wppizzaCartStickyVars[e]['width-int']);
				wppizzaCartStickyParent[e].height(wppizzaCartStickyVars[e]['height-int']);
			});
		}

		/***********************************************
		*	[we have set an element limit past which the
		*	sticky cart should not scroll,
		*	lets calculate the (negative) offset here]
		/***********************************************/
		var wppizzaCartStickyMaxOffset = function(elm,top,limitElm){
			var val=0;
			if(wppizzaCartStickyLimitBottomElm){
				var limit=limitElm.offset().top;/*get limit element top*/
				var elmOffset=Math.floor(limit-top-elm['height-int']);/*if negative we use it**/
				if(elmOffset<0){
					val=elmOffset;
				}
			}
			return val;
		};
		/**********no animation, just add/remove class, top and bg colour*******************************************************************************************************/
		if(!wppizzaCartStickyAnimation){
			/*let's rock n' scroll.....( oh dear )*/
			$(window).scroll(function () {
				var wppizzaCartStickyScrollTop = ($(window).scrollTop()+wppizza.crt.mt);
				$.each(wppizzaCartStickySelf,function(e,v){

					/**calcuate needed offset if we are limiting the scroll by a set element below cart***/
					var wppizzaCartStickyLimitOffset=wppizzaCartStickyMaxOffset(wppizzaCartStickyVars[e],wppizzaCartStickyScrollTop,wppizzaCartStickyLimitBottomElm);

					/**leave it in place**/
					if (wppizzaCartStickyVars[e]['offset-top']>=wppizzaCartStickyScrollTop) {
						wppizzaCartStickySelf[e].removeClass('wppizza-cart-fixed').css({'top':'','background-color':''+wppizzaCartStickyVars[e]['backgroundColor']+''});
					}
					/**set to fixed**/
					if (wppizzaCartStickyVars[e]['offset-top']<wppizzaCartStickyScrollTop) {
						wppizzaCartStickySelf[e].addClass('wppizza-cart-fixed').css({'top':''+(wppizza.crt.mt+wppizzaCartStickyLimitOffset)+'px','background-color':''+wppizza.crt.bg+''});
					}
				});
			});
		}

		/**********with animation, *******************************************************************************************************************************************/
		if(wppizzaCartStickyAnimation){
		var wppizzaCartStickyAnimIni = true;/*set load flag*/

			/***********initialize on load***********/
			setTimeout(function(){/*a little timeout to give the page time to render*/
				$.each(wppizzaCartStickySelf,function(e,v){

					/**calcuate needed offset if we are limiting the scroll by a set element below cart***/
					var wppizzaCartStickyLimitOffset=wppizzaCartStickyMaxOffset(wppizzaCartStickyVars[e],wppizzaCartStickyScrollTop,wppizzaCartStickyLimitBottomElm);

					/**leave it in place**/
					if (wppizzaCartStickyVars[e]['offset-top']>=wppizzaCartStickyScrollTop) {
						wppizzaCartStickyVars[e]['state']='relative';/*set state flag so we dont do the same thing  multiple times**/
					}
					/**move to sticky/fixed**/
					if (wppizzaCartStickyVars[e]['offset-top']<wppizzaCartStickyScrollTop) {
						wppizzaCartStickySelf[e].addClass('wppizza-cart-fixed').css({'background-color':''+wppizza.crt.bg+'','top':'0'});
						wppizzaCartStickySelf[e].animate({'top':''+(wppizza.crt.mt+wppizzaCartStickyLimitOffset)+'px'},wppizza.crt.anim,''+wppizza.crt.fx+'',function(){});
						wppizzaCartStickyVars[e]['state']='fixed';/*set state flag so we dont do the same thing  multiple times**/
					}
				});
				wppizzaCartStickyAnimIni=false;/*unset previously set load flag*/
			},200);


			/*********on scroll after load *************/
			$(window).scroll(function () {
				if(!wppizzaCartStickyAnimIni){/*only react to scrolling after initial load*/

					var wppizzaCartStickyScrollTop = $(window).scrollTop()+wppizza.crt.mt;/*find out if we need fixed or relative*/

						clearTimeout(wppizzaCartStickyScrollTimeout);
						wppizzaCartStickyScrollTimeout = setTimeout(function(){/*a little timeout to not go mad*/
							/*iterate through elements*/
							$.each(wppizzaCartStickySelf,function(e,v){

								/**calcuate needed offset if we are limiting the scroll by a set element below cart***/
								var wppizzaCartStickyLimitOffset=wppizzaCartStickyMaxOffset(wppizzaCartStickyVars[e],wppizzaCartStickyScrollTop,wppizzaCartStickyLimitBottomElm);

								/**put back in its place if state has changed, otherwise just leave in peace**/
								if (wppizzaCartStickyVars[e]['offset-top']>=wppizzaCartStickyScrollTop && wppizzaCartStickyVars[e]['state']!='relative') {
									wppizzaCartStickyVars[e]['state']='relative';/*set state flag so we dont do the same thing  multiple times**/

									wppizzaCartStickySelf[e].removeClass('wppizza-cart-fixed');
									wppizzaCartStickySelf[e].animate({'top':''},wppizza.crt.anim,''+wppizza.crt.fx+'',function(){
										wppizzaCartStickySelf[e].css({'background-color':''+wppizzaCartStickyVars[e]['backgroundColor']+''});
									});
									// if we do not want to animate when returning to relative state , use this instead of the above.
									//wppizzaCartStickySelf[e].removeClass('wppizza-cart-fixed').css({'top':'','background-color':''+wppizzaCartStickyVars[e]['backgroundColor']+''});
								}
								/**move to sticky/fixed if state has changed or we have a limit set , otherwise just leave in peace**/
								if (wppizzaCartStickyVars[e]['offset-top']<wppizzaCartStickyScrollTop && (wppizzaCartStickyVars[e]['state']!='fixed' || wppizzaCartStickyLimitBottomElm)) {

									wppizzaCartStickyVars[e]['state']='fixed';/*set state flag so we dont do the same thing  multiple times**/
									wppizzaCartStickySelf[e].addClass('wppizza-cart-fixed').css({'background-color':''+wppizza.crt.bg+''});
									wppizzaCartStickySelf[e].animate({'top':''+(wppizza.crt.mt+wppizzaCartStickyLimitOffset)+'px'},wppizza.crt.anim,''+wppizza.crt.fx+'',function(){});
								}
							});
					},100);
				}
			});
		}
	}

	/*******************************
	*	[add to cart / remove from cart]
	*******************************/
	/**only allow integers in cart increase/decrease**/
	$(document).on('keyup', '.wppizza-cart-incr', function(e){
		this.value = this.value.replace(/[^0-9]/g,'');
		/**when using textbox in cart to incr/decr allow enter as well as clicking on button */
		if(e.keyCode == 13){
			$(this).closest('li').find('.wppizza-cart-increment').trigger(''+wppizzaClickEvent+'');
		}
	})


	/**run defined functions after cart refresh**/
	var wppizzaCartRefreshed = (function(functionArray) {
		if(functionArray.length>0){
			for(i=0;i<functionArray.length;i++){
				var func = new Function("term", "return " + functionArray[i] + "(term);");
				func();
			}
		}
	});

	$(document).on(''+wppizzaClickEvent+'', '.wppizza-add-to-cart,.wppizza-remove-from-cart,.wppizza-cart-refresh,#wppizza-force-refresh,.wppizza-cart-increment,.wppizza-empty-cart-button', function(e){
		if ($(".wppizza-open").length > 0){//first check if shopping cart exists on page and that we are open
			e.preventDefault();
			e.stopPropagation();

		var self=$(this);
		var selfId=self.attr('id');
		var cartButton=$('.wppizza-cart-button input,.wppizza-cart-button>a,.wppizza-empty-cart-button');
		cartButton.attr("disabled", "true");/*disable place order button to stop trying to order whilst stuff is being added to the cart*/
		/**feedback on item add enabled?*/
		var fbatc=false;
		if(typeof wppizza.itm!=='undefined' && typeof wppizza.itm.fbatc!=='undefined'){
		 fbatc=true;
		}



		var itemCount=1;
		/**get cat id**/
		var catId='';
		if(self.hasClass('wppizza-add-to-cart')){
			type='add';
			var postId = selfId.split('-');
			var catdata = $('#wppizza-category-'+postId[1]+'').val();
			if(typeof catdata!=='undefined'){/*some customised templates may not have catid added, so check first*/
				catId=catdata;
			}
		}
		if(self.hasClass('wppizza-remove-from-cart')){type='remove';}
		if(self.hasClass('wppizza-empty-cart-button')){type='removeall';selfId=0;}
		if(self.hasClass('wppizza-cart-refresh') || selfId=='wppizza-force-refresh'){type='refresh';}
		if(self.hasClass('wppizza-cart-increment')){
			var itemCount=self.closest('li').find('.wppizza-cart-incr').val();
			if(itemCount==0){
				type='remove';
			}else{
				type='increment';
			}
		}
			if(type!='removeall' && type!='add' ){
				self.fadeOut(100).fadeIn(400);
			}
			if(!fbatc && type=='add'){/*make this dedicated for add*/
				self.fadeOut(100).fadeIn(400);
			}
			if(fbatc && type=='add'){
				var currentHtml=self.html();
				self.fadeOut(100, function(){
					self.html( "<div class='wppizza-item-added-feedback'>"+wppizza.itm.fbatc+"</div>" ).fadeIn(400).delay(wppizza.itm.fbatcms).fadeOut(400,function(){
						self.html(currentHtml).fadeIn(100);
					});
				});
			}


			$('.wppizza-order').prepend('<div id="wppizza-loading"></div>');
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':type,'id':selfId,'itemCount':itemCount,'catId':catId}}, function(response) {
				/*show items in cart*/
				$('.wppizza-order').html(response.itemsajax);
				/*button*/
				$('.wppizza-cart-button').html(response.button);

				/*minimum order not reached*/
				$('.wppizza-cart-nocheckout').html(response.nocheckout);
				/*order summary*/
				$('.wppizza-cart-total-items-label').html(response.order_value.total_price_items.lbl);
				$('.wppizza-cart-total-items-value').html(response.currency_left+''+response.order_value.total_price_items.val+''+response.currency_right);
				if(response.nocheckout==''){
					$('.wppizza-cart-discount-label').html(response.order_value.discount.lbl);

					/*addcurrency if discount applies**/
					if(response.order_value.discount.val!=''){
						$('.wppizza-cart-discount-value').html('<span class="wppizza-minus"></span>'+response.currency_left+''+response.order_value.discount.val+''+response.currency_right);
					}else{
						$('.wppizza-cart-discount-value').html(response.order_value.discount.val);
					}
					$('.wppizza-cart-delivery-charges-label').html(response.order_value.delivery_charges.lbl);
					/*addcurrency if its not free delivery**/
					if(response.order_value.delivery_charges.val!=''){
					$('.wppizza-cart-delivery-charges-value').html(response.currency_left+''+response.order_value.delivery_charges.val+''+response.currency_right);
					}else{
					$('.wppizza-cart-delivery-charges-value').html(response.order_value.delivery_charges.val);
					}
					/**tax**/
					$('.wppizza-cart-tax-label').html(response.order_value.item_tax.lbl);
					$('.wppizza-cart-tax-value').html(response.currency_left+''+response.order_value.item_tax.val+''+response.currency_right);

					/**tax included**/
					$('.wppizza-cart-tax-included-label').html(response.order_value.taxes_included.lbl);
					$('.wppizza-cart-tax-included-value').html(response.currency_left+''+response.order_value.taxes_included.val+''+response.currency_right);

					$('.wppizza-cart-total-label').html(response.order_value.total.lbl);
					$('.wppizza-cart-total-value').html(response.currency_left+''+response.order_value.total.val+''+response.currency_right);
				}
				if(response.nocheckout!='' || response.items.length==0){
					$('.wppizza-cart-discount-label').html('');
					$('.wppizza-cart-discount-value').html('');
					$('.wppizza-cart-delivery-charges-label').html('');
					$('.wppizza-cart-delivery-charges-value').html('');
					$('.wppizza-cart-total-label').html('');
					$('.wppizza-cart-total-value').html('');
					$('.wppizza-cart-tax-label').html('');
					$('.wppizza-cart-tax-value').html('');
					$('.wppizza-cart-tax-included-label').html('');
					$('.wppizza-cart-tax-included-value').html('');

				}
				if(response.items.length==0){
					$('.wppizza-cart-total-items-label').html('');
					$('.wppizza-cart-total-items-value').html('');
				}

				cartButton.removeAttr("disabled");/*re-enable place order button*/

				wppizzaCartRefreshed(wppizza.funcCartRefr);

			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);$('.wppizza-order #wppizza-loading').remove();});
		}});

	/***********************************************
	*
	*	[if there's a shopping cart on the page
	*	but we are currently closed, display alert]
	*
	***********************************************/
	$(document).on(''+wppizzaClickEvent+'', '.wppizza-add-to-cart', function(e){
		if ($(".wppizza-open").length == 0 &&  $(".wppizza-cart").length > 0){
			alert(wppizza.msg.closed);
	}});
	/***********************************************
	*
	*	[customer selects self pickup , session gets set via ajax
	*	reload page to reflect delivery charges....
	*	only relevant if there's a shoppingcart or orderpage on page]
	*	[as it's an input element always use click instead of touchstart, cause iStuff is stupid]
	***********************************************/
	$(document).on('click', '#wppizza-order-pickup-sel,#wppizza-order-pickup-js', function(e){
		if (($(".wppizza-open").length > 0 &&  $(".wppizza-cart").length > 0) || $("#wppizza-send-order").length>0){
			var self=$(this);
			self.attr("disabled", "true");/*disable checkbox to give ajax time to do things*/
			var selfValue=self.is(':checked');
			/*js alert if enabled*/
			if(self.attr('id')=='wppizza-order-pickup-js' && selfValue==true){
				alert(wppizza.msg.pickup);
			}
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'order-pickup','value':selfValue,'data':$('#wppizza-send-order').serialize(),'locHref':location.href,'urlGetVars':location.search}}, function(res) {
				window.location.href=res.location;/*make sure page gest reloaded without confirm*/
			},'json').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);});
	}});
	
	/******************************************************
	*
	*	[changing gateways, re-calculate handling charges
	*	if any are >0 which will in turn add the hidden field
	*	'#wppizza_calc_handling' we are checking first ]
	******************************************************/
	if($('#wppizza_calc_handling').length>0){
		var wppizzaGatewaySelected = $("input[name='wppizza-gateway']");
		if(wppizzaGatewaySelected.length==0){
			wppizzaGatewaySelected = $("select[name='wppizza-gateway']");
		}
		wppizzaGatewaySelected.change(function(e){
			$('#wppizza-send-order').prepend('<div id="wppizza-loading"></div>');
			if(wppizzaGatewaySelected.is(':radio')){
				var selectedGateway = $("input[name='wppizza-gateway']:checked").val();
			}else{
				var selectedGateway = $("select[name='wppizza-gateway']").val();
			}
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'wppizza-select-gateway','data':$('#wppizza-send-order').serialize(),'selgw':selectedGateway}}, function(res) {
				window.location.href=window.location.href;/*make sure page gest reloaded without confirm*/
			},'json').error(function(jqXHR, textStatus, errorThrown) {	$('#wppizza-send-order #wppizza-loading').remove(); alert("error : " + errorThrown);console.log(jqXHR.responseText);});
		});
	}
	/***********************************************
	*
	*	[if we are trying to add to cart by clicking on the title
	*	but there's more than one size to choose from, display alert]
	*	[provided  there's a cart on page and we are open]
	***********************************************/
	/*more than one size->choose alert*/
	$(document).on(''+wppizzaClickEvent+'', '.wppizza-trigger-choose', function(e){
		if ($(".wppizza-open").length > 0 &&  $(".wppizza-cart").length > 0){
			alert(wppizza.msg.choosesize);
	}});
	/*only one size, trigger click*/
	$(document).on(''+wppizzaClickEvent+'', '.wppizza-trigger-click', function(e){
		if ($(".wppizza-open").length > 0 &&  $(".wppizza-cart").length > 0){
			/*just loose wppizza-article- from id*/
			 var ArticleId=this.id.split("-");
			ArticleId=ArticleId.splice(2);
			ArticleId = ArticleId.join("-");
			/**make target id*/
			target=$('#wppizza-'+ArticleId+'');
			/*trigger*/
			target.trigger(''+wppizzaClickEvent+'');
	}});

	/***********************************************
	*
	*	[order form: login or continue as guest]
	*
	***********************************************/
	$(document).on(''+wppizzaClickEvent+'', '#wppizza-login,#wppizza-login-cancel', function(e){
		$("#wppizza-user-login-action").toggle(300);
		$("#wppizza-user-login-option>span>a").toggle();
	});
	$(document).on('click', '#wppizza_btn_login', function(e){/**changed to click so iphone understands it too*/
		$("#wppizza-user-login-action").append('<div id="wppizza-loading"></div>');
	});
	$(document).on('change', '#wppizza_account', function(e){
		$("#wppizza-user-register-info" ).toggle(200);
		$(".wppizza-login-error").remove();

	});
	/****insert nonce too via js *******/
	if($("#wppizza-send-order").length>0){
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'nonce','val':'register'}}, function(nonce) {
			$('#wppizza-create-account').append(nonce);
		},'html').error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);});
	}


	/*******************************************************************
	*	[validate and submit order page]
	*	gateway could be either by dropdown,
	*	radio, or if only one, hidden elm
	*******************************************************************/
	$(document).on(''+wppizzaClickEvent+'', '.wppizza-ordernow', function(e){
		$('#wppizza-send-order').validate().settings.ignore = "";
	});
	/*******************************
	*	[validate tips/gratuities]
	*******************************/
	/**current tip value set **/
	var wppizzaCTipsCurr=$("#wppizza-send-order #ctips").val();
	/**click should work here even on iStupid as it's a button **/
	$(document).on('click', '#wppizza-ctips-btn', function(e){
		/*we only want to validate the tips, so lets igmore everythig else*/
		$('#wppizza-send-order').validate().settings.ignore = "#wppizza-send-order>fieldset>input,#wppizza-send-order>fieldset>textarea,#wppizza-send-order>fieldset>select";
		var isValid=$("#wppizza-send-order").valid();
		if(isValid){
			var wppizzaCTipsNew=$("#wppizza-send-order #ctips").val();
			/**only update/refresh if the value has actually changed**/
	  		if(wppizzaCTipsCurr!=wppizzaCTipsNew){
	  			$("#wppizza-send-order").prepend('<div id="wppizza-loading" style="opacity:0.8"></div>');
				jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'add_tips','data':$('#wppizza-send-order').serialize(),'locHref':location.href,'urlGetVars':location.search}}, function(res) {
				window.location.href=res.location;/*make sure page gest reloaded without confirm*/
				},'json');
	  		}
		}
	});
	/*******************************************
	*	[validation login]
	*******************************************/
	$("#wppizza-login-frm").validate({});
	/*******************************************
	*	[ini validation]
	*******************************************/
	$("#wppizza-send-order").validate({
			rules: {
	   			ctips: {
	      			number: true
	    		}
	  		},
			submitHandler: function(form) {
				var hasClassAjax=false;
				var hasClassCustom=false;
				if($("input[name='wppizza-gateway']").length>0){
					var elm = $("input[name='wppizza-gateway']");
					if(elm.is(':radio')){
						var selected = $("input[name='wppizza-gateway']:checked");
					}else{
						var selected = elm;
					}
					hasClassAjax=selected.hasClass("wppizzaGwAjaxSubmit");
					hasClassCustom=selected.hasClass("wppizzaGwCustom");
				}else{
					var selected = $("select[name='wppizza-gateway']");
					hasClassAjax=$("select[name='wppizza-gateway'] option:selected").hasClass("wppizzaGwAjaxSubmit");
					hasClassCustom=$("select[name='wppizza-gateway'] option:selected").hasClass("wppizzaGwCustom");
				}
				var self=$('#wppizza-send-order');
				var currVal = selected.val();
				var profileUpdate=$("#wppizza_profile_update").is(':checked');
				if(profileUpdate==true){
					jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'profile_update','data':self.serialize()}}, function(response) {
						//console.log(response);
					},'html');
				}

				/***if we want to also register account check this first**/
				var wppizzaLoginElm=$("#wppizza-user-login");
				var wppizzaLoginErr=$(".wppizza-login-error");/*remove any previous errors*/
				if(wppizzaLoginErr.length>0){
					wppizzaLoginErr.remove();
				}

				var wppizzaLoginSelect=$("input[type=radio][name='wppizza_account']:checked");

				wppizzaLoginElm.hide();
				if(typeof wppizzaLoginSelect!=='undefined' && wppizzaLoginSelect.val()=='register'){
					self.prepend('<div id="wppizza-loading"></div>');
					jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'new-account','data':self.serialize()}}, function(response) {
						/**account/email exists**/
						if(typeof response.error!=='undefined'){
							$('#wppizza-user-register-info').append(response.error);
							$('#wppizza-send-order #wppizza-loading').remove();
							wppizzaLoginElm.show();
							return;
						}
						/***all is well. go ahead with stuff**/
						if(typeof response.error==='undefined'){
							wppizzaSelectSubmitType(self,currVal,hasClassAjax,hasClassCustom);
						}
					},'json');
					return;
				}else{
					/**we are not registering a new account, so just submit as planned**/
					wppizzaSelectSubmitType(self,currVal,hasClassAjax,hasClassCustom);
				}
			}
		})


	/******************************
	* submit via ajax or send form
	*******************************/
	var wppizzaSelectSubmitType=function(self,currVal,hasClassAjax,hasClassCustom){
		/*****confirmation page enabled*****/
		if(typeof wppizza.cfrm!=='undefined' && !self.hasClass('wppizza-confirm-order')){
			$('#wppizza-user-login').empty().remove();			
			self.prepend('<div id="wppizza-loading"></div>');
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'confirmorder','data':self.serialize()}}, function(response) {
						self.html(response);//replace the form contents
						self.addClass('wppizza-confirm-order');/*set class so we dont do this again**/			
						$('#wppizza-send-order #wppizza-loading').remove();
			},'html').error(function(jqXHR, textStatus, errorThrown) {$('#wppizza-send-order #wppizza-loading').remove();alert("error : " + errorThrown);console.log(jqXHR.responseText);});					
			return;
		}
		/**customised submit/payment via js window/overlay for example - will have to provide its own script**/
		if(hasClassCustom){
			window['wppizza' + currVal + 'payment']();
			return;
		}
		/**cod->transmit form via ajax if cod or forced by gw settings (i.e $this->gatewayTypeSubmit = 'ajax')*/
		if(currVal=='cod' || hasClassAjax){
			self.prepend('<div id="wppizza-loading"></div>');
			$('#wppizza-user-login').empty().remove();
			jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'sendorder','data':self.serialize()}}, function(response) {
				$('#wppizza-send-order #wppizza-loading').remove();
				self.html('<div id="wppizza-order-received">'+response+'</div>');
			},'html').error(function(jqXHR, textStatus, errorThrown) {$('#wppizza-send-order #wppizza-loading').remove();alert("error : " + errorThrown);console.log(jqXHR.responseText);});
		}else{
			self.prepend('<div id="wppizza-loading" style="opacity:0.8;"></div>');
			form.submit();
		}

	};
	/******************************
	* set error messages
	*******************************/
	jQuery.extend(jQuery.validator.messages, {
    	required: wppizza.validate_error.required,
    	email: wppizza.validate_error.email,
    	number: wppizza.validate_error.decimal
	});
	/**allow for commas in number validation but no negatives**/
	$.validator.methods.number = function (value, element) {
	    return this.optional(element) || /^(?:\d+|\d{1,3}(?:[\s\.,]\d{3})+)(?:[\.,]\d+)?$/.test(value);
	    //return this.optional(element) || /^-?(?:\d+|\d{1,3}(?:[\s\.,]\d{3})+)(?:[\.,]\d+)?$/.test(value);//this would allow negatives too
	}
	/******************************
	* Let's make IE7 IE8 happy and stop submitting while other stuff is going on such as adding items etc
	*******************************/
	$(document).on(''+wppizzaClickEvent+'', '.wppizza-cart-button>a', function(e){
		e.preventDefault(); e.stopPropagation();
		var attr = $(this).attr('disabled');
		if (typeof attr !== 'undefined' && attr !== false){}else{
        	var url=jQuery(this).attr("href");
	        window.location.href = url;
		}
	});
	/***********************************************
	*
	*	[using cache plugin, load cart dynamically]
	*	[as the cart does not exist onload we will also
	*	have to execute the sticky cart function after it has been created]
	***********************************************/
	if(typeof wppizza.usingCache!=='undefined'){
		var wppizzaNoCacheAttr=$('#wppizza-cart-nocache-attributes').val();
		jQuery.post(wppizza.ajaxurl , {action :'wppizza_json',vars:{'type':'hasCachePlugin','attributes':wppizzaNoCacheAttr}}, function(response) {
			$('.wppizza-cart-nocache').html(response);
			wppizzaCartRefreshed(wppizza.funcCartRefr);/**also run any cart refreshed functions**/
		},'html').complete(
			function(){wppizzaCartStickyLoad();}/*on complete, exec sticky cart if enabled*/
		).error(function(jqXHR, textStatus, errorThrown) {alert("error : " + errorThrown);console.log(jqXHR.responseText);});
	}else{
		/*if no cache, just exec sticky cart function*/
		wppizzaCartStickyLoad();
	}
})