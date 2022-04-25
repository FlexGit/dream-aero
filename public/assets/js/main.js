$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(document).ready(function(){

	function setLocation(curLoc) {
		try {
			history.pushState(null,null, curLoc);
			return;
		} catch(e) {}

		window.location.hash = '#' + curLoc;
	}

    $(".ajax-container").on("focusin", function() {
 		$("a.fancybox, a.various").fancybox({
  			'padding': 0
 		});
 		/*$("a.various").fancybox({
  			'padding': 0
 		});*/
	});
    
    url = document.location.href;

    if (url.match(/ourguestes/)) {
    	$('html, body').animate({
			scrollTop: ($('#ourguestes').offset().top - 180)
		},800);
    } /*else if (url.match(/virttourair/)) {
    	newContent('tourDIV','virttourair');
    } else if (url.match(/virttourboeing/)) {
    	newContent('tourDIV','virttourboeing');
    }

	if (window.location.hash) {
		var hash = window.location.hash.substring(1);
		newContent('tourDIV',hash);
	}*/

	if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		Modile = $('#mainphone').text().replace('+7','8');
		Modile = Modile.replace(/[^0-9]/g,'');
		$('#mainphone').attr('href', 'tel:' + Modile);
		$('#mainphone').removeClass('popup-with-form');
	} else {
		$('#mainphone').attr('href', '#popup-call-back');
	}

	/*$('.lazy').lazy();*/

	$('.main-menu .dropdown-menu a').click(function() {
		newContent('tourDIV',hash);
	});

	$('#delaydiv .cboxClose').click(function() {
		$('#delaydiv').hide("slow");
	});

	$('.noref').click(function() {
		return false;
	});

	$('.ajax_form').append('<input type="text" name="org" value="" class="_org" style="visibility: hidden; height: 0;width: 0;padding: 0;border: none;" />');

	$('.airbo').append('dfdf');

	$('select').niceSelect();

	$objWindow = $(window);

	$('div[data-type="background"]').each(function() {
		var $bgObj = $(this);
		$(window).scroll(function(){
			var yPos =- ($objWindow.scrollTop() / $bgObj.data('speed')) + 200;
			var coords = '100% ' + yPos + 'px';
			if ($(window).width() > 767) {
				$bgObj.css({
					backgroundPosition: coords
				});
			} else {
				$bgObj.css({
					backgroundPosition: '100% ' + '100%'
				});
			}
		});
	});

	$('.go-up').click(function() {
		$('html, body').animate({
			scrollTop: 0
		},900);
		setTimeout(function() {
			$('.shop-show-title').removeClass('viewed');
		},200);
	});

	$('.mobile-burger').click(function() {
		$(this).toggleClass('open');
		$('.main-menu').slideToggle(300);
	});

	/*$('body').on('click','.promoblock .rugs-button', function () {
    	$('#err_mess').remove();

    	$.ajax({
       		type:'post',//тип запроса: get,post либо head
       		url:'/admin/dealajax',//url адрес файла обработчика
       		data:{'action':'getpromocode','promocode':$('.promoblock input').val(),'client_city':$('#current_city').text(),'prod_type':$('.promoblock input').attr("data-type")},//параметры запроса
       		response:'text',//тип возвращаемого ответа text либо xml
       		success:function (data) {//возвращаемый результат от сервера
        		var promodata = jQuery.parseJSON(data);
        		if (promodata.promo_id){
        			$('.promoblock .rugs-button').hide();
        			$('.promoblock input').attr("readonly",1);
        			$('.promoblock input').css("padding","15px");
        			$('.promoblock input').css("background-color","#E4E4E4");
        			$('.promoblock').prepend("<div id=\"paypromo\"><input type=\"hidden\" name=\"promoid\"  id=\"promoid\"  value="+promodata.promo_id+"><input type=\"hidden\" id=\"promoamount\" value="+promodata.promo_disc+"><input type=\"hidden\" id=\"promotype\" value="+promodata.promo_curr+"></div>");
     			} else {
           			$('.promoblock').prepend("<p id=\"err_mess\" style=\"color:red\">Промокод либо недействителен, либо истек срок его действия.</p>");
        		}
        		TotalPrice();
       		}
    	});
    });*/
    
    /*$("body").on("click", ".aerbonus_btns #charge", function(){
        if ($('#total-amount .strikethrough').length){
            price=parseInt($('#total-amount .strikethrough').text().replace(/\D+/g,""));
            $('#total-amount').html('Стоимость: '+String(price).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+' руб');
        }
        $('#bonus_info').html('<i>Будет начислено <b>'+calculate_mils()+'</b> миль (1 миля за каждые потраченные 50 рублей)</i><input type="hidden" name="bonus_charge" value="'+calculate_mils()+'">');
    });

    $("body").on("click", ".aerbonus_btns #use", function(){
        bonus4use();
    });

    $("body").on("click", ".aerocard .rugs-button", function(){
        $('#errmess').remove();
	     $.ajax({
           type:'post',//тип запроса: get,post либо head
           url:'/admin/dealajax',//url адрес файла обработчика
           data:{'action':'aerocardverificate','cardnum':$('.aerocard input').val()},//параметры запроса
         response:'text',//тип возвращаемого ответа text либо xml
         success:function (data) {//возвращаемый результат от сервера
             if(data==0){
                $(".aerocard .rugs-button, .aerocard svg, .have_promo").hide();
                $('.aerocard input').attr("readonly",1);
                $('.aerocard input').css("background-color","#AADDC7");
                $('.aerocard input').css("padding","15px");
                $('.aerocard').append("<ul class=\"aerbonus_btns\"><li class=\"plan-time\" id=\"charge\">Начислить мили</li><li id=\"use\" class=\"plan-time\">Использовать мили</li></ul><div id=\"bonus_info\"></div>");
                 
             }
             else{
                 $('.aerocard').prepend("<p id=\"errmess\" style=\"color:red\">Номер карты введен некорректно, проверьте данные и попробуйте ещё раз</p>");
             }
         }
         });   
    	        
	});
	   
    $("body").on("click", ".aerbonus_btns li", function(){
        $('.aerbonus_btns li').removeClass('active');
	    $(this).addClass('active');
	});*/

    $(document).on('click', '.have_promo', function() {
	    $(this).hide();
	    $('.aeroflotbonus').hide();
	    $(".promoblock").show();
	});
	
	/*$("body").on("input", "#bonus_mils", function(){
	    mils=$(this).val().replace(/\D+/g,"");
	    console.log(mils+' '+$(this).attr("data-max")+' '+$(this).attr("data-min"));
	    if (parseInt(mils)>parseInt($(this).attr("data-max")))
	        mils=$(this).attr("data-max");
	    else if (parseInt(mils)<parseInt($(this).attr("data-min")))
	        mils=$(this).attr("data-min");
	       $(this).val(mils);
	       
	        $("#rubinmils").val(' => '+(Math.round(($(this).val())*4))+ ' миль');
	   if ($('#total-amount .strikethrough').length){
            price=parseInt($('#total-amount .strikethrough').text().replace(/\D+/g,""));
            $('#total-amount').html('Стоимость: <b class="strikethrough">'+String(price).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+'</b>'+String((price-$(this).val())).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+' руб');
        }
	});*/

	$('.popup-close').click(function(e){
		e.preventDefault();
		$.magnificPopup.close();
	});

	/*$(document).on('af_complete',function(event,response){
		var form=response.form;
		if(form.attr('id')=='popup-payonline'){
			$.magnificPopup.open({
				items:{
					src:"#online-welcome"},
					type:'inline',
					preloader:false,
					removalDelay:3000,
					mainClass:'mfp-fade'});
					onlinepay("popup-payonline");
				}
	else if(form.attr('id')=='popup-mainpay'){$.magnificPopup.open({items:{src:"#online-welcome"},type:'inline',preloader:false,removalDelay:3000,mainClass:'mfp-fade'});onlinepay("popup-mainpay");}
	else if(form.attr('id')=='popup-gensert'){$.magnificPopup.open({items:{src:"#popup-sert"},type:'inline',preloader:false,removalDelay:3000,mainClass:'mfp-fade'});window.open('https://dream-aero.ru/assets/files/'+document.getElementById("sertnum").value+'.jpg');}
	else{$.magnificPopup.open({items:{src:"#popup-welcome"},type:'inline',preloader:false,removalDelay:300,mainClass:'mfp-fade'});}

		setTimeout(function(){
			$.magnificPopup.close();
		},40000)
		return false;
	});*/

	var wow = new WOW({
		boxClass:'wow',
		animateClass:'animated',
		offset:0,
		mobile:false,
		live:true,
		scrollContainer:null
	});

	wow.init();

	$(window).on('resize',function(){
		bodyPadding();
	});

	$(window).resize(function(){
		$('#varsL').height($('#varsR').height());
		$('#varsL img').height($('#varsR').height());
	});

	bodyPadding();
});

function bodyPadding(){
	var header=$('.header');var headerHeight=$(header).outerHeight();$('body').css('padding-top',headerHeight);
}

/*function bonus4use(){
     $.ajax({
       type:'post',//тип запроса: get,post либо head
       url:'/admin/dealajax',//url адрес файла обработчика
       data:{'action':'getbonusinfo','cardnum':$('.aerocard input').val(),'tarif':$('#tarif').val(),'tarif_type':$('#tarif_type').val(),'contextalias':$('#current_city').attr('data-context')},
       beforeSend: function() {
           $('#bonus_info').html('<div style="text-align: center;"><p><b>Идет расчет</b></p><img src="/assets/planes.gif"></div>');},
       response:'text',
       success:function (data) {
           price=parseInt($('#total-amount').text().replace(/\D+/g,""));
           minbonus=Math.floor((price/100)*20);
           if (data){
            var bonusdata = jQuery.parseJSON(data);
            if (typeof bonusdata['pointsAllocation'] !== "undefined"
            && bonusdata['pointsAllocation']['maxChequePoints']> 0){
              maxChequePoints=bonusdata['pointsAllocation']['maxChequePoints']/100;
           $('#bonus_info').html('<p>Доступно для списания '+maxChequePoints+' руб.</p><p>Сколько из них Вы готовы списать?</p> <div style="display:flex"><input style="width:48%;border-bottom: 2px solid #828285;margin-top:10px" name="bonus_mils" data-min="'+minbonus+'" data-max="'+maxChequePoints+'" id="bonus_mils" type="text" value="'+maxChequePoints+'" required=""><input style="width:48%;border-bottom: 2px solid #828285;margin-top:10px" readonly id="rubinmils" type="text" value=" => '+Math.round(maxChequePoints*4)+' миль" required=""></div><i>Вы можете списать в милях не менее 20% и не более 50% от стоимости сертификата</i>');
           $('#total-amount').html('Стоимость: <b class="strikethrough">'+String(price).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+'</b>'+String((price-maxChequePoints)).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+' руб');
            }
            else
            $('#bonus_info').html('<p class="error">Произошла ошибка. Повторите, пожалуйста, попытку позже</p>');
           }
          else
            $('#bonus_info').html('<p class="error">Произошла ошибка. Повторите, пожалуйста, попытку позже</p>');
       }
	    });
}*/

/*function calculate_mils(){
    if ($('#total-amount .strikethrough').length) 
        price=parseInt($('#total-amount .strikethrough').text().replace(/\D+/g,""));
     else
        price=parseInt($('#total-amount').text().replace(/\D+/g,""));
    return Math.floor(price/50);
}*/

/*function TotalPrice(){
    $('#submibtn').hide();
     $.ajax({
       type:'post',//тип запроса: get,post либо head
       url:'/admin/dealajax',//url адрес файла обработчика
       data:{'action':'getprice','contextalias':$('#current_city').attr('data-context'),'tarif_type':$('#tarif_type').val(),'tarif':$('#tarif').val()},//параметры запроса
       response:'text',//тип возвращаемого ответа text либо xml
       success:function (data) {//возвращаемый результат от сервера
        var tarifdata = jQuery.parseJSON(data);
        sale=0;
        old_price='';
        if (($('.promoblock input').attr('data-type')=='sert' && tarifdata['sale']) || ($('.promoblock input').attr('data-type')=='bron' && tarifdata['sale_bron'])){
            sale=1;
            price=tarifdata['price_new'];
            old_price='<b class="strikethrough">'+String(tarifdata['price']).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+'</b>';
        }
        else
            price=tarifdata['price'];
            
        if ($('#promoamount').val()>0 && sale==0 && ($('#tarif').val().toUpperCase().indexOf('ULTIMATE') >= 0 || $('#tarif').val().toUpperCase().indexOf('REGULAR') >= 0)){
            
            old_price='<b class="strikethrough">'+String(price).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+'</b>';
            if ($('#promotype').val()=='percent')
                price=(1-($('#promoamount').val()/100))*price;
            else
                price=price-$('#promoamount').val();
        }
        
        
       $('#total-amount').html('Стоимость: '+old_price+String(price).replace(/(\d)(?=(\d{3})+([^\d]|$))/g, '$1 ')+' руб');
       if ($(".aerbonus_btns #charge").hasClass("active"))
         $('#bonus_info').html('<i>Будет начислено <b>'+calculate_mils()+'</b> миль (1 миля за каждые потраченные 50 рублей)</i><input type="hidden" name="bonus_charge" value="'+calculate_mils()+'">');
        else if($(".aerbonus_btns #use").hasClass("active"))
            bonus4use();
        $('#submibtn').show();
     }
    });
}*/

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

function calcAmount() {
	var $popup = $('#popup'),
		productId = $popup.find('#product').val(),
		promocodeUuid = $popup.find('#promocode_uuid').val(),
		locationId = $popup.find('input[name="locationSimulator"]:checked').data('location-id'),
		simulatorId = $popup.find('input[name="locationSimulator"]:checked').data('simulator-id'),
		flightDate = $popup.find('#flight_date').val(),
		certificate = $popup.find('#certificate_number').val(),
		cityId = $('#city_id').val(),
		$amount = $popup.find('#amount'),
		$amountContainer = $popup.find('.js-amount'),
		amount = 0;

	var data = {
		product_id: productId,
		promocode_uuid: promocodeUuid,
		location_id: locationId,
		simulator_id: simulatorId,
		city_id: cityId,
		flight_date: flightDate,
		certificate: certificate,
		source: 'web',
	};

	//console.log(data);

	$.ajax({
		type: 'GET',
		url: '/deal/product/calc',
		data: data,
		dataType: 'json',
		success: function(result) {
			if (result.status != 'success') {
				return;
			}

			if (result.amount != result.baseAmount) {
				amount = '<span class="strikethrough">' + result.baseAmount + '</span>' + result.amount;
			} else if (result.amount) {
				amount = result.amount;
			}
			$amount.val(result.amount);
			$amountContainer.html(amount);
		}
	});
}
