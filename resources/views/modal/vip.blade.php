<div class="vip-form">
	<h3>VIP ПОЛЕТ с {{ $product->name ?? '' }}</h3>
	<div class="form cliendataform">
		<div id="buy-vipsert"></div>
		<div class="inputdiv">
			<label>Ваше имя *</label>
			<input type="text" id="name" name="name" class="popup-input">
		</div>
		<div class="inputdiv">
			<label>Ваш телефон *</label>
			<input type="text" id="phone" name="phone" class="popup-input new-phone">
		</div>
		<div class="inputdiv">
			<label>Ваш email *</label>
			<input type="email" id="email" name="email" class="popup-input" onmouseover="$('#vippodskazka').show()" onmouseleave="$('#vippodskazka').hide()">
			<i id="vippodskazka" style="display: none;position: absolute;font-size: 11px;color: white;bottom: -25px;left: -10px;">На Ваш email будет выслан сертификат.</i>
		</div>
	</div>
	<p style="margin-top:35px">Для кого сертификат:</p>
	<div class="form cliendataform">
		<div class="inputdiv">
			<label>Имя</label>
			<input type="text" id="certificate_whom_name" name="certificate_whom_name" class="popup-input">
		</div>
		<div class="inputdiv">
			<label>Телефон</label>
			<input type="tel" id="certificate_whom_phone" name="certificate_whom_phone" class="popup-input">
		</div>
	</div>
	<div class="delivcond">
		<div class="form cliendataform" style="margin-top: 10px;">
			<label class="checkboxs_vip">
				Хочу с бесплатной доставкой
				<input type="checkbox" id="sertdeli" name="with_delivery" class="popup-input" value="1">
				<span class="checksqmark"></span>
			</label>
		</div>
		<div id="sdeliadd">
			<div class="form cliendataform" style="margin-top: 10px;">
				<input type="text" id="delivery_address" name="delivery_address" class="popup-input" style="margin-bottom: 0;min-width: 280px;" placeholder="Адрес доставки в пределах МКАД">
			</div>
			<div class="form cliendataform">
				<span>* срок доставки три дня</span>
			</div>
		</div>
	</div>
	<input type="hidden" id="product_id" name="product_id" value="{{ $product->id }}">
	<input type="hidden" id="duration" name="duration" value="{{ $product->duration }}">
	<input type="hidden" id="city_id" name="city_id" value="{{ $cityId }}">
	<input type="hidden" id="amount" name="amount" value="{{ $amount }}">
	<div class="certificate_btn result_btn">Купить сертификат</div>
	<div class="error_txt" style="display: none;">
		<b>Заполните, пожалуйста, все поля формы</b>
	</div>
	<div class="success" style="display: none;">
	</div>
</div>
