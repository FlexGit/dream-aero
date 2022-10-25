<div class="gl-default uk-modal-dialog">
	<span class="city">Ваш город — <b class="gl-city-name_ru">{{ $city->name }}</b></span>
	<ul class="gl-change-list">
		@foreach ($cities as $cityItem)
			<li>
				<span class="gl-list-location js-city" data-alias="{{ $cityItem->alias }}">{{ App::isLocale('en') ? $cityItem->name_en : $cityItem->name }}</span>
			</li>
		@endforeach
		<div class="sep"></div>
		<li>
			<a href="https://dream.aero/dc/"><span>Washington D.C.</span></a>
		</li>
	</ul>
</div>
