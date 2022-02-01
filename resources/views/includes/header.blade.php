<header class="header">
	<div class="flexy_row">
		<div>
			<a href="{{ url(($cityAlias && $city) ? $city->alias : '/') }}" class="logo">
				<img src="{{ asset('img/logo-new.webp') }}" alt="logo"></a>
		</div>
		<div class="main-menu">
			<ul>
				<li class="first active" id="mob">
					<a href="{{ url(($cityAlias && $city) ? $city->alias : '/') }}">Главная</a>
				</li>
				<ul>
					<li class="first dropdownf"><a href="{{ url(($cityAlias && $city) ? $city->alias . '/o-trenazhere' : 'o-trenazhere') }}">О тренажере</a>
						<ul class="dropdown-menu">
							<li class="first">
								<a href="{{ url(($cityAlias && $city) ? $city->alias . '/virtualt/#virttourboeing' : 'virtualt/#virttourboeing') }}">Виртуальный тур B737</a>
							</li>
							<li class="last">
								<a href="{{ url(($cityAlias && $city) ? $city->alias . '/virtualt/#virttourair' : 'virtualt/#virttourair') }}">Виртуальный тур A320</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/podarit-polet' : 'podarit-polet') }}">Подарить полет</a>
					</li>
					<li>
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/variantyi-poleta' : 'variantyi-poleta') }}">Варианты полета</a>
					</li>
					<li>
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/news' : 'news') }}" >Новости</a>
					</li>
					<li class="dropdownf">
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/instruktazh' : 'instruktazh') }}">Инструктаж</a>
						<ul class="dropdown-menu">
							<li class="first">
								<a href="{{ url(($cityAlias && $city) ? $city->alias . '/instruktazh/airbus-a320' : 'instruktazh/airbus-a320') }}">Airbus A320</a>
							</li>
							<li class="last">
								<a href="{{ url(($cityAlias && $city) ? $city->alias . '/instruktazh/boeing-737-ng' : 'instruktazh/boeing-737-ng') }}">Boeing 737 NG</a>
							</li>
						</ul>
					</li>
					<li class="dropdownf">
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/price' : 'price') }}">Цены</a>
						<ul class="dropdown-menu">
							<li class="first">
								<a href="{{ url(($cityAlias && $city) ? $city->alias . '/vse-akcii' : 'vse-akcii') }}">Акции</a>
							</li>
						</ul>
					</li>
					<li class="dropdownf">
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/galereya' : 'galereya') }}">Галерея</a>
						<ul class="dropdown-menu">
							<li class="first">
								<a href="{{ url(($cityAlias && $city) ? $city->alias . '/galereya/#ourguestes' : 'galereya/#ourguestes') }}">Наши гости</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/reviews' : 'reviews') }}">Отзывы</a>
					</li>
					<li class="last">
						<a href="{{ url(($cityAlias && $city) ? $city->alias . '/contacts' : 'contacts') }}">Контакты</a>
					</li>
				</ul>
			</ul>
		</div>
		<div class="flexy_column nav">
			<div class="item">
				<p class="gl-current-select" id="city" data-toggle="modal" data-target="#city_modal">
					{{ $city->name }}
				</p>
			</div>
			<div>
				<span class="phone">
					<a href="{{ url('#popup-call-back') }}" id = "mainphone" class="popup-with-form">
						+7 (495) 532-87-37
					</a>
				</span>
				<a id="langlink" href="{{ url('//en.dream-aero.ru') }}">En</a>
			</div>
		</div>
		<div class="mobile-burger">
			<span></span><span></span><span></span>
		</div>
	</div>
</header>
