$(function(){
    
   $('.tabs2__caption li').click(function(){
       if(this.innerHTML=='Expert')
           
           document.getElementById("kurstime").innerHTML="9<br/>часов";
       else
       document.getElementById("kurstime").innerHTML="6<br/>часов";
       
   });


$('#deliverych').change(function(){
    if($(this).is(":checked")) {
        $('#deliveryinput').show();
       /* delprice=parseInt($('#MNT_AMOUNT').val())+500;
        $('#MNT_AMOUNT').val(delprice);
        $('#on-price-popup').text('Стоимость ' + delprice+ ' руб');*/
        $("#deliadd").prop('required',true);
        if (document.getElementById('delcity').value=='Санкт-Петербург')
        document.getElementById('deliaddtxt').innerHTML = 'Только в пределах КАД<br/>Доставка осуществляется в течение трех дней';
        else if (document.getElementById('delcity').value=='Москва')
        document.getElementById('deliaddtxt').innerHTML = 'Только в пределах МКАД<br/>Доставка осуществляется в течение трех дней';
        else
        document.getElementById('deliaddtxt').innerHTML = 'в пределах города<br/>Доставка осуществляется в течение трех дней';
    }
    else{
        /*delprice=parseInt($('#MNT_AMOUNT').val())-500;
        $('#MNT_AMOUNT').val(delprice);
        $('#on-price-popup').text('Стоимость ' + delprice+ ' руб');*/
        $('#deliveryinput').hide();
        $("#deliadd").prop('required',false);
        document.getElementById('deliaddtxt').innerHTML = '';
        $("#deliadd").val('');
    }
    
});
$('#popup-payonline input').click(function(){
    if (this.id=='clemail'){
        $(this).css( "margin-bottom", "0px");
        $('#podskazka').show();
    }
    else if(this.id!='deliadd' && this.id!='pravila' && this.id!='productPromoCodeId'){
    
        $(this).css( "margin-bottom", "24px");
        $('#podskazka').hide();
    }
    
});


$( "body" ).on("submit", "#vip-payonline", function() {
    $('#VIP_DESCR').val($('#vip_clname').val() + ' (VIPFLIGHT)');
    formdata=$(this).serialize();
    $.ajax({
            type:'post',//тип запроса: get,post либо head
            url:'/admin/dealajax',//url адрес файла обработчика
            data:formdata,//параметры запроса
            response:'text',//тип возвращаемого ответа text либо xml
            beforeSend: function() {
                $('#vip-payonline').hide();
                $('#buy_vipres').html('Подождите! Вы будете перенаправлены на страницу оплаты через минуту.');
            },
            success:function (data) {//возвращаемый результат от сервера
                
            }
      
    });
    
    });
    $( "body" ).on( "change", ".Rpaytype", function() {
    paytype=$(this).attr('id');
    tarif_type=$('#tarif_type').val();
    tarif=$('#tarif').val();
    
    if(paytype=='brontab'){
       $.ajax({
       type:'post',//тип запроса: get,post либо head
       url:'/admin/dealajax',//url адрес файла обработчика
       data:{'action':'openform','formname':'tpl.bronform','fromtype':'shortbron','selectcity':$("#current_city").text(),'tarif':tarif,'tarif_type':tarif_type,'tarif_title':tarif_type,'cityalias':$("#current_city").attr("data-context")},//параметры запроса
     response:'text',//тип возвращаемого ответа text либо xml
     success:function (data) {//возвращаемый результат от сервера
        $('#online-reservation #editform').html(data);
        
    }
      
    });
    }
    else if(paytype=='paytab'){
        $.ajax({
       type:'post',//тип запроса: get,post либо head
       url:'/admin/dealajax',//url адрес файла обработчика
       data:{'action':'openform','formname':'tpl.main_payonline','fromtype':'shortsert','selectcity':$("#current_city").text(),'tarif':tarif,'tarif_type':tarif_type,'tarif_title':tarif_type,'cityalias':$("#current_city").attr("data-context")},//параметры запроса
     response:'text',//тип возвращаемого ответа text либо xml
     success:function (data) {//возвращаемый результат от сервера
        $('#online-reservation #editform').html(data);
        TotalPrice();
    }
      
    });
    }
    
});

$( "body" ).on( "change", "#sertdeli", function() {
    if($(this).is(":checked")) {
        $('#sdeliadd').show();
        $('#sdeliadd input').attr('required', true);
    }
    else{
        $('#sdeliadd').hide();
               $('#sdeliadd input').attr('required', false);
               $('#sdeliadd').val('');
    }
});



});


function bronsert(sid){
    var article = document.getElementById(sid);
      if (article.dataset.title=='ЛЕТЧИК ЛЕХА' || article.dataset.title=='ДЕНИС ОКАНЬ' ){
        $.ajax({
            type:'post',//тип запроса: get,post либо head
            url:'/admin/dealajax',//url адрес файла обработчика
            data:{'action':'getviptarif','btype':article.dataset.title,'pageid':article.dataset.pageid},//параметры запроса
            response:'text',//тип возвращаемого ответа text либо xml
            success:function (data) {//возвращаемый результат от сервера
            $('#buy-vipsert').html(data);
            }
      
    });
    }
    else{
        document.getElementById('paytab').checked = true;
        if (article.dataset.type=='kurs' || article.dataset.title=='Platinum'){
            document.getElementById('lbron').style.display="none";
            document.getElementById('lpay').style.display="none";
        }
        else{
            document.getElementById('lbron').style.display="inline-table";
            document.getElementById('lpay').style.display="inline-table";
            
        }
        
        $.ajax({
       type:'post',//тип запроса: get,post либо head
       url:'/admin/dealajax',//url адрес файла обработчика
       data:{'action':'openform','formname':'tpl.main_payonline','fromtype':'shortsert','selectcity':$("#current_city").text(),'tarif':sid,'tarif_type':article.dataset.type,'tarif_title':article.dataset.title,'cityalias':$("#current_city").attr("data-context")},//параметры запроса
     response:'text',//тип возвращаемого ответа text либо xml
     success:function (data) {//возвращаемый результат от сервера
        $('#online-reservation #editform').html(data);
        TotalPrice();
    }
      
    });
    }
}



function setCheckedValue(radioObj, newValue) {
    
	if(!radioObj)
		return;
	var radioLength = radioObj.length;
	if(radioLength === undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}