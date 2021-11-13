$(function(){
    
    $('#varsL').height($('#varsR').height());$('#varsL img').height($('#varsR').height());$teamer=0;

$('.popup-mainpay input').click(function(){if(this.id=='clemail'){$(this).css("margin-bottom","0px");$('.popup-mainpay #podskazka').show();}
else if($(this).attr('type') != 'checkbox' && this.id!='productPromoCodeId'){$(this).css("margin-bottom","24px");$('.popup-mainpay #podskazka').hide();}});$('#main-bronsert #rtitle').change(function(){var txt4price=getbronprice();if (txt4price[1]) document.getElementById('price-popup').innerHTML=txt4price[1];$('#pop-price').val(txt4price[1].replace("Стоимость",""));$('#type-time').val($(this).val()+' мин');promoprice('bron');});

$(".team .owl-carousel").owlCarousel({items:4,margin:50,nav:true,responsive:{0:{items:1,},500:{items:2},992:{items:3},1200:{items:4}}});var reviewsCarousel=$(".reviews-carousel");reviewsCarousel.owlCarousel({items:1,nav:false,dots:false,animateOut:'fadeOut',animateIn:'fadeIn',smartSpeed:500,loop:true});$('.reviews-prev').click(function(){reviewsCarousel.trigger('prev.owl.carousel');});
$('.form_open').click(function(){
     $.ajax({
       type:'post',//тип запроса: get,post либо head
       url:'/admin/dealajax',//url адрес файла обработчика
       data:{'action':'openform','formname':$(this).attr('data-formname'),'selectcity':$("#current_city").text()},//параметры запроса
     response:'text',//тип возвращаемого ответа text либо xml
     success:function (data) {//возвращаемый результат от сервера
        $('#editform').html(data);
    }
      
    });
});
$('body').on('change',' #tarif_time', function () {
        getbrontarif();
        
}); 
 
$('body').on('change',' #selectproduct', function () {
        selected=$(this).find('option:selected');
        $('#tarif_type').val(selected.attr("data-type"));
        $('#tarif').val(selected.val());
        TotalPrice();
});        

 
$('#deliverych').change(function(){
    if($(this).is(":checked")) {
        $('#deliveryinput').show();
     /*   delprice=parseInt($('#MNT_AMOUNT').val())+500;
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
        $('#deliveryinput').hide();
      /*  delprice=parseInt($('#MNT_AMOUNT').val())-500;
        $('#MNT_AMOUNT').val(delprice);
        $('#on-price-popup').text('Стоимость ' + delprice+ ' руб');*/
        $("#deliadd").prop('required',false);
        document.getElementById('deliaddtxt').innerHTML = '';
        $("#deliadd").val('');
    }
    
});
$('.reviews-next').click(function(){reviewsCarousel.trigger('next.owl.carousel');})});function mainbron(type){if(type=='paytab'){$('#main-bronsert #sernum').attr('required',true);$('#main-bronsert #sernum').show();$('#main-bronsert #on-price-popup').hide();$('#main-bronsert #aktext').hide();stitle=$('#main-bronsert #serttitle').val().replace("Бронирование полета","Запись по сертификату");$('#main-bronsert #serttitle').val(stitle);}
else{$('#main-bronsert #sernum').val('');$('#main-bronsert #sernum').attr('required',false);$('#main-bronsert #sernum').hide();$('#main-bronsert #on-price-popup').show();$('#main-bronsert #aktext').show();stitle=$('#main-bronsert #serttitle').val().replace("Запись по сертификату","Бронирование полета");$('#main-bronsert #serttitle').val(stitle);}}




    