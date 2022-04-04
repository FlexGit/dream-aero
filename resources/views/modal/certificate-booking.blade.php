@if($product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS]))
	<div class="certificate-booking-tabs">
		<a class="button-pipaluk button-pipaluk-orange button-tab" data-modal="certificate" data-product-alias="{{ $product->alias }}" data-product-type-alias="{{ $product->productType->alias }}" href="javascript:void(0)"><i>Приобрести сертификат</i></a>
		<a class="button-pipaluk button-pipaluk-orange button-tab" data-modal="booking" data-product-alias="{{ $product->alias }}" data-product-type-alias="{{ $product->productType->alias }}" href="javascript:void(0)"><i>Забронировать полет</i></a>
	</div>
@endif

<div class="popup-titl">
	<p id="on-title">{{ preg_replace('/[0-9]+/', '', $product->name) }}</p>
	<p id="on-number">{{ $product->duration }} МИН</p>
</div>

<div class="form-container"></div>
