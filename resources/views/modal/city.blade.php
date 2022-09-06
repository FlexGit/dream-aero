<div class="gl-default uk-modal-dialog">
	<span class="city" data-current-alias="{{ $city->alias }}" data-choose-city-text="@lang('main.common.выберите-ваш-город')">@lang('main.common.ваш-город') — <b class="gl-city-name_ru">{{ App::isLocale('en') ? $city->name_en : $city->name }}</b></span>
	<span class="btn-yes popup-close">@lang('main.common.да')</span>
	<span class="btn-change">@lang('main.common.изменить')</span>
	<ul class="gl-change-list" style="display: none;">
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
