<div id="promobox" class="overlay @if(!$promobox) hidden @endif" data-alias="{{ $promobox ? $promobox->id : '' }}">
	<div class="popup popup-promo">
		<a class="close" href="javascript:void(0)" onclick="localStorage.setItem('promobox-{{ $promobox ? $promobox->id : '' }}', true);">×</a>
		<div class="content">
			<h2>{!! $promobox ? $promobox->title : '' !!}</h2>
			<div>
				{!! ($promobox && $promobox->preview_text) ? $promobox->preview_text : '' !!}
			</div>
		</div>
	</div>
</div>
