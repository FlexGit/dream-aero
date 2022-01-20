<input type="hidden" id="id" name="id">
<input type="hidden" id="contractor_id" name="contractor_id">
<input type="hidden" id="amount" name="amount">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="contractor">Поиск контрагента</label>
			<input type="text" class="form-control" id="contractor" name="contractor" placeholder="Имя, Фамилия, E-mail, Телефон">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="name">Имя</label>
			<input type="text" class="form-control" id="name" name="name" placeholder="Имя">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="lastname">Фамилия</label>
			<input type="text" class="form-control" id="lastname" name="lastname" placeholder="Фамилия">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" placeholder="+71234567890">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control js-product" id="product_id" name="product_id">
				<option></option>
				@foreach($productTypes ?? [] as $productType)
					<optgroup label="{{ $productType->name }}">
						@foreach($productType->products ?? [] as $product)
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}">{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promo_id">Акция</label>
			<select class="form-control" id="promo_id" name="promo_id">
				<option value=""></option>
				@foreach($promos ?? [] as $promo)
					<option value="{{ $promo->id }}">{{ $promo->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col text-center">
		<div class="form-group">
			{{--<label for="payment_method_id">Способ оплаты</label>
			<select class="form-control" id="payment_method_id" name="payment_method_id">
				<option value=""></option>
				@foreach($paymentMethods ?? [] as $paymentMethod)
					<option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
				@endforeach
			</select>--}}
			<div class="custom-control custom-switch custom-control-inline" style="margin-top: 40px;">
				<input type="checkbox" id="is_free" name="is_free" value="1" class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_free">Бесплатно</label>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="city_id">Город</label>
			{{--<div class="custom-control custom-switch custom-control-inline ml-1">
				<input type="checkbox" id="is_unified" name="is_unified" value="1" class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_unified">Любой</label>
			</div>--}}
			<select class="form-control" id="city_id" name="city_id">
				<option value="0">Любой</option>
				@foreach($cities ?? [] as $city)
					<option value="{{ $city->id }}">{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<label for="certificate_whom">Для кого сертификат</label>
		<input type="text" class="form-control" id="certificate_whom" name="certificate_whom">
	</div>
	<div class="col text-right mt-4">
		<div id="amount-text" class="d-inline-block ml-1">
			<h1 class="d-inline-block">0</h1> <i class="fas fa-ruble-sign" style="font-size: 25px;"></i>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
	</div>
</div>
