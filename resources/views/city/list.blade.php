<div class="gl-default uk-modal-dialog">
    <span class="city">Ваш город — <b class="gl-city-name_ru">{{ $city->name }}</b></span>
	<span class="btn-yes">Да</span>
	<span class="btn-change">Изменить</span>
	<ul class="gl-change-list" style="display: none;">
		@foreach ($cities as $city)
			<li @if($city->version == app('\App\Models\City')::EN_VERSION && $prevVersion == app('\App\Models\City')::RU_VERSION) style="padding-top: 10px;" @endif>
				<span class="gl-list-location js-city" data-alias="{{ $city->alias }}">{{ $city->name }}</span>
			</li>
			@php($prevVersion = $city->version)
		@endforeach
	</ul>
</div>
