@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.price.title')</span></div>

	<article class="article">
		<div class="container">
			<h2 class="block-title">@lang('main.price.title')</h2>
			<div class="article-content">
				<div class="row">
					<div class="col-md-12 price">
						<div class="prices">
							<div class="left-price">
								<div class="top-inf">
									<p class="bold">@lang('main.price.забронировать-время')</p>
									<p>@lang('main.price.стоимость-авиасимулятора-не-меняется')</p>
								</div>
								<div class="bottom-inf">
									<p class="bold">@lang('main.price.подарить-сертификат')</p>
									<p>
										@if(count($locationItems) > 1)
											@lang('main.price.владелец-подарочного-сертификата-несколько-локаций', ['locationItems' => implode(', ', $locationItems)])
										@else
											@lang('main.price.владелец-подарочного-сертификата-одна-локация')
										@endif
									</p>
								</div>
								<div class="ab-inf">
									<p class="bold">@lang('main.price.аэрофлот-бонус')</p>
									<p></p>
									<p>@lang('main.price.аэрофлот-бонус-это-программа')</p>
									<a href="{{ url('news/aeroflot-bonus') }}" target="_blank">@lang('main.price.подробнее')</a><p></p>
								</div>
							</div>

							<div class="right-price">
								@if($isBlacFridayShow)
									<div class="black-friday-banner">
										<script src="//megatimer.ru/get/695cf3c589c8c425ce6dc1c03a54eda8.js"></script>
									</div>
								@endif
								<div class="tabs">
									<div class="flexdiv">
										<ul class="tabs__caption">
											@foreach($productTypes as $productType)
												@if(!in_array($productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS,]))
													@continue;
												@endif
												<li class="@if($productType->alias == app('\App\Models\ProductType')::REGULAR_ALIAS) active @endif">
													<p style="text-transform: uppercase;">{{ $productType->name }}</p>
													<small>{{ $productType->alias == app('\App\Models\ProductType')::REGULAR_ALIAS ? 'только будни' : 'без ограничений' }}</small>
												</li>
											@endforeach
										</ul>
									</div>

									@foreach($productTypes as $productType)
										@if(!in_array($productType->alias,
											[
												app('\App\Models\ProductType')::REGULAR_ALIAS,
												app('\App\Models\ProductType')::ULTIMATE_ALIAS,
											]
										))
											@continue;
										@endif

										<div class="tabs__content @if($productType->alias == app('\App\Models\ProductType')::REGULAR_ALIAS) active @endif">
											<p class="stars"> <i>*</i> @lang('main.price.сертификат-regular-действует')</p>

											@foreach($products[mb_strtoupper($productType->alias)] ?? [] as $productAlias => $product)
												<div class="block-price">
													@if($product['is_hit'])
														<span>@lang('main.price.хит-продаж')</span>
													@endif
													<p class="title">
														{{ $productType->alias }}
													</p>
													<p class="time">{{ $product['duration'] }} @lang('main.price.мин')</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 10px;">
														<div style="margin-top: 10px;height: 30px;">
															@if($product['base_price'] != $product['price'])
																<b class="strikethrough" style="font-size: 18px;">
																	{{ number_format($product['base_price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}
																</b>
															@endif
														</div>
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form_open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}" data-popup-type="product"><i>{{ $product['is_booking_allow'] ? trans('main.price.booking') : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? trans('main.price.certificate') : '' }}</i></a>
												</div>
											@endforeach

											{{--Platinum--}}
											@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
												@if ($productAlias != app('\App\Models\ProductType')::PLATINUM_ALIAS)
													@continue
												@endif
												<div class="block-price">
													@if($product['is_hit'])
														<span>@lang('main.price.хит-продаж')</span>
													@endif
													<p class="title">
														{{ $product['name'] }}
													</p>
													<p class="time">{{ $product['duration'] }} @lang('main.price.мин')</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 40px;">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}" data-popup-type="product"><i>{{ $product['is_booking_allow'] ? trans('main.price.booking') : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? trans('main.price.certificate') : '' }}</i></a>
													<p class="h4plat" style="display: none;">
														@lang('main.price.развлекательный-курс')
														<br>
														<a href="{{ url('upload/doc/Tarif_Platinum.pdf') }}" target="_blank">@lang('main.price.план-полетов')</a>
													</p>
												</div>
											@endforeach

											{{--VIP полеты--}}
											@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS)] ?? [] as $productAlias => $product)
												<div class="block-price">
													@if($product['is_hit'])
														<span>@lang('main.price.хит-продаж')</span>
													@endif
													<p class="title">
														{{ $product['name'] }}
													</p>
													<p class="time">{{ $product['duration'] }} @lang('main.price.мин')</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="" width="132">
													@endif
													<div style="position: relative;margin-top: 42.5px">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}" data-popup-type="product"><i>{{ $product['is_booking_allow'] ? trans('main.price.booking') : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? trans('main.price.certificate') : '' }}</i></a>
													<p class="h4plat" style="display: none;">
														@lang('main.price.сертификат-на-vip-полет', ['name' => $product['name']])
														<br>
														<a href="{{ url('vipflight') }}" target="_blank">@lang('main.home.подробнее')</a>
													</p>
												</div>
											@endforeach
										</div>
									@endforeach
								</div>
							</div>
						</div>

						<h4>@lang('main.price.подготовьтесь-к-полёту')</h4>

						<div class="row download">
							<div class="col-md-4">
								<p>@lang('main.price.выберите-программу', ['link' => url('variantyi-poleta')])</p>
							</div>
							<div class="col-md-4">
								<p>@lang('main.price.внимательно-ознакомьтесь', ['link' => url('pravila')])</p>
							</div>
							<div class="col-md-4">
								<p>@lang('main.price.пройдите-инструктаж', ['link' => url('instruktazh/boeing-737-ng')])</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="pr facts pages" id="home" data-type="background" data-speed="20" style="background-position: 100% 92.5px;">
			<div class="container">
				<h2 class="block-title">@lang('main.price.курс-пилота')</h2>
				<ul class="row bacground">
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/circle.png') }}" alt=""></div>
						<span>6<br>@lang('main.price.часов')</span>
						<p>@lang('main.price.теории-и-практики')</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/docum.png') }}" alt=""></div>
						<span>@lang('main.price.книга-пилота-сувенир')</span>
						<p>@lang('main.price.в-подарок')</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/card.png') }}" alt=""></div>
						<span>@lang('main.price.дисконтная-карта')</span>
						<p>@lang('main.price.в-подарок')</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/aircraft.png') }}" alt=""></div>
						<span>@lang('main.price.удостоверение-виртуального-пилота')</span>
						<p></p>
					</li>
				</ul>
			</div>
		</div>

		<div class="conteiner-min">
			<div class="tabs2">
				<ul class="tabs2__caption">
					@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
						@if(!in_array($product['alias'], ['basic', 'advanced', 'expert']))
							@continue
						@endif

						<li @if($product['alias'] == 'basic') class="active" @endif>
							{{ mb_strtoupper($product['name']) }}
						</li>
					@endforeach
				</ul>
				@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
					@if(!in_array($product['alias'], ['basic', 'advanced', 'expert']))
						@continue
					@endif

					<div class="tabs2__content @if($product['alias'] == 'basic') active @endif">
						@if($product['alias'] == 'basic')
							@lang('main.price.после-обучения-по-базовой-программе')
						@elseif($product['alias'] == 'advanced')
							@lang('main.price.программа-advanced', ['price' => number_format($product['price'], 0, '.', ' ')])
						@elseif($product['alias'] == 'expert')
							@lang('main.price.программа-expert')
						@endif

						@if($product['alias'] != 'advanced')
							<div class="block-price ather">
								<p class="title">@if(App::isLocale('ru')) @lang('main.price.курс-пилота2') @endif {{ mb_strtoupper($product['name']) }} @if(App::isLocale('en')) @lang('main.price.курс-пилота2') @endif</p>
								<p class="time">{{ $product['duration'] / 60 }} @lang('main.price.часов')</p>
								@if($product['icon_file_path'])
									<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
								@endif
								<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
								<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-popup-type="product"><i>@lang('main.price.заказать')</i></a>
							</div>
						@endif
					</div>
				@endforeach
			</div>
		</div>

		@if(App::isLocale('ru'))
			@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
				@if($product['alias'] != 'fly_no_fear')
					@continue
				@endif

				<div class="letaem">
					<div class="container">
						<h2 class="block-title">{{ $product['name'] }}</h2>
						<div class="text col-md-7">
							@lang('main.price.вам-нужно-пройти-курс')
							<a class="button-pipaluk button-pipaluk-orange" href="{{ url('lechenie-aerofobii') }}"><i>@lang('main.price.подробнее')</i></a>
						</div>
						<div class="col-md-5">
							<a href="{{ url('lechenie-aerofobii') }}"><img style="width: 100%;" src="{{ asset('img/letaemkurs.jpg') }}" alt=""></a>
						</div>
					</div>
				</div>
			@endforeach
		@endif

		<div class="container">
			<div class="row free">
				<div class="col-md-6">
					<p>@lang('main.price.для-многих-желание-оказаться-в-кресле')</p>
				</div>
				<div class="col-md-6">
					<div class="photo">
						<img src="{{ asset('img/img1.jpg') }}" alt="">
					</div>
				</div>
				<div class="col-md-6">
					<div class="photo">
						<img src="{{ asset('img/img5.jpg') }}" alt="">
					</div>
				</div>
				<div class="col-md-6">
					<p>@lang('main.price.мы-не-делаем-никаких-скидок')</p>
				</div>
				<div class="button-free">
					<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form" data-popup-type="callback"><i>@lang('main.price.заказать-обратный-звонок')</i></a>
				</div>
			</div>
		</div>
	</article>

	<div class="relax">
		<div class="container">
			<div class="row">
				<div class="col-md-8 wow fadeInLeft" data-wow-duration="2s">
					<h2 class="block-title">@lang('main.price.корпоративный-отдых')</h2>
					<div class="text">
						@lang('main.price.однообразные-и-скучные-вечеринки')
						<a href="{{ url('#popup') }}" class="button-pipaluk button-pipaluk-orange popup-with-form" data-popup-type="callback"><i>@lang('main.price.заказать-обратный-звонок')</i></a>
					</div>
				</div>
				<div class="col-md-4 wow fadeInRight" data-wow-delay="1s" data-wow-duration="2s">
					@lang('main.price.корпоратив', ['photos_links' => url('galereya')])
				</div>
			</div>
		</div>
	</div>

	<div class="stock under">
		<div class="container">
			<div class="row">
				<div class="col-md-8 wow fadeInLeft" data-wow-duration="2s">
					<h2 class="block-title">@lang('main.price.акции')</h2>
					<div class="text">
						@lang('main.price.акция-день-рождения')
					</div>
				</div>
				<div class="col-md-4">
					<div class="img wow fadeInRight" data-wow-delay="1s" data-wow-duration="2s">
						<img src="{{ asset('img/plane.png') }}" alt="">
						<a href="{{ url('#popup') }}" class="button-pipaluk button-pipaluk-orange popup-with-form" data-popup-type="callback"><i>@lang('main.price.мне-это-интересно')</i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/pricestyle.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
@endpush