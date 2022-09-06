<div class="gl-default uk-modal-dialog">
	<span class="city" data-current-alias="{{ $city->alias }}">Ваш город — <b class="gl-city-name_ru">{{ $city->name }}</b></span>
	<span class="btn-yes popup-close">Да</span>
	<span class="btn-change">Изменить</span>
	<ul class="gl-change-list" style="display: none;">
		@foreach ($cities as $cityItem)
			<li>
				<span class="gl-list-location js-city" data-alias="{{ $cityItem->alias }}">{{ ($city->version == app('\App\Models\City')::EN_VERSION) ? $cityItem->name_en : $cityItem->name }}</span>
			</li>
		@endforeach
		<div class="sep"></div>
		<li>
			<a href="https://dream.aero/dc/"><span>Washington D.C.</span></a>
		</li>
	</ul>
</div>
