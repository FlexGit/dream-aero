<div id="promobox" class="overlay @if(!$promobox) hidden @endif" data-alias="{{ $promobox ? $promobox->id : '' }}">
	<div class="popup popup-promo">
		<a class="close" href="javascript:void(0)" onclick="localStorage.setItem('promobox-{{ $promobox ? $promobox->id : '' }}', true);">×</a>
		<div class="content">
			<h2>{!! $promobox ? $promobox->title : '' !!}</h2>
			<div>
				@if($promobox)
					@if(mb_strpos($promobox->alias, app('\App\Models\Lead')::BLACK_FRIDAY_TYPE) . '-promobox')
						<script src="//megatimer.ru/get/42a4013bc3fc858a6422cb63a7415b30.js"></script>
					@endif
					{!! $promobox->preview_text ? $promobox->preview_text : '' !!}
				@endif
			</div>
		</div>
	</div>
</div>
