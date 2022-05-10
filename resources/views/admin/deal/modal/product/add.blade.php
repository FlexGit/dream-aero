<input type="hidden" id="id" name="id">
<input type="hidden" id="contractor_id" name="contractor_id">
<input type="hidden" id="amount" name="amount">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
			<div class="js-contractor-container hidden">
				<small>Контрагент: <span class="js-contractor"></span></small> <i class="fas fa-times js-contractor-delete" title="Удалить позицию" style="cursor: pointer;color: #aaa;"></i>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" placeholder="+71234567890">
		</div>
	</div>
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
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control js-product" id="product_id" name="product_id">
				<option></option>
				@foreach($products ?? [] as $productTypeName => $productId)
					<optgroup label="{{ $productTypeName }}">
						@foreach($productId as $product)
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
			<select class="form-control" id="promo_id" name="promo_id" disabled>
				<option value=""></option>
				@foreach($promos ?? [] as $promo)
					<option value="{{ $promo->id }}">{{ $promo->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promocode_id">Промокод</label>
			<select class="form-control" id="promocode_id" name="promocode_id" disabled>
				<option value=""></option>
				@foreach($promocodes ?? [] as $promocode)
					<option value="{{ $promocode->id }}">{{ $promocode->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-4">
		<div class="form-group">
			<label for="city_id">Город</label>
			<select class="form-control" id="city_id" name="city_id">
				<option value=""></option>
				@foreach($cities ?? [] as $city)
					<option value="{{ $city->id }}">{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-4">
		<div class="form-group">
			<label for="payment_method_id">Способ оплаты</label>
			<select class="form-control" id="payment_method_id" name="payment_method_id">
				<option value=""></option>
				@foreach($paymentMethods ?? [] as $paymentMethod)
					<option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-8">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
	</div>
	<div class="col-4 text-right">
		<div class="form-group mt-4">
			<div class="custom-control custom-switch custom-control-inline">
				<input type="checkbox" id="is_free" name="is_free" value="1" class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_free">Бесплатно</label>
			</div>
			<div id="amount-text">
				<h1 class="d-inline-block">0</h1> <i class="fas fa-ruble-sign" style="font-size: 25px;"></i>
			</div>
		</div>
	</div>
</div>
