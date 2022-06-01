@if($product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS]))
	<div class="certificate-booking-tabs">
		<a class="button-pipaluk button-pipaluk-green button-tab" data-modal="certificate" data-product-alias="{{ $product->alias }}" data-product-type-alias="{{ $product->productType->alias }}" href="javascript:void(0)"><i>@lang('main.modal-certificate-booking.приобрести-сертификат')</i></a>
		<a class="button-pipaluk button-pipaluk-orange button-tab" data-modal="booking" data-product-alias="{{ $product->alias }}" data-product-type-alias="{{ $product->productType->alias }}" href="javascript:void(0)"><i>@lang('main.modal-certificate-booking.забронировать-полет')</i></a>
	</div>
@endif

<div class="popup-titl">
	<p id="on-title">{{ preg_replace('/[0-9]+/', '', $product->name) }}</p>
	@if($product->alias != 'fly_no_fear')
		<p id="on-number">{{ $product->duration }} @lang('main.common.мин')</p>
	@endif
</div>

<div class="form-container"></div>

