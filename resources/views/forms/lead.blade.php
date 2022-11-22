<div class="feedback">
	<div class="container">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div class="form wow fadeInRight" data-wow-duration="2s">
					<form class="ajax_form" action="#" method="post" data-lead-type="{{ $leadType }}">
						<input type="text" id="name" name="name" placeholder="@lang('main.lead.ваше-имя') *">
						<input type="email" id="email" name="email" placeholder="@lang('main.lead.ваш-email') *">
						<input type="tel" id="phone" name="phone" placeholder="@lang('main.lead.ваш-телефон') *">
						<div style="width: 84%;margin: 0 auto;">
							<select id="product_id" name="product_id" class="popup-input">
								<option value="">@lang('main.lead.тариф') *</option>
								@foreach($products as $product)
									<option value="{{ $product->id }}" data-product-type-alias="{{ $product->productType ? $product->productType->alias : '' }}" data-alias="{{ $product->alias }}">{{ $product->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="consent-container" style="text-align: center;color: #fff;">
							<label class="cont" style="padding-top: 0;font-size: 14px;">
								@lang('main.modal-callback.я-согласен-на-обработку-моих-данных')
								<input type="checkbox" name="consent" value="1">
								<span class="checkmark" style="padding-top: 0;"></span>
							</label>
						</div>

						<div>
							<div class="alert alert-success hidden" style="background-color: transparent;border-color: transparent;color: #fff;" role="alert">
								@lang('main.lead.сообщение-успешно-отправлено')
							</div>
							<div class="alert alert-danger hidden" style="background-color: transparent;border-color: transparent;color: #fff;" role="alert"></div>
						</div>

						<button type="button" class="button-pipaluk button-pipaluk-white js-lead-btn" disabled>
							<i>@lang('main.lead.хочу-скдику')</i>
						</button>
					</form>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
	</div>
</div>