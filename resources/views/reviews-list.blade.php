@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.reviews.title')</span></div>

	<div class="news-list">
		<div class="container">
			<h1 class="block-title">@lang('main.reviews.title')</h1>
			<div class="article-content">
				<div class="row">
					<div class="col-md-12 price">
						<div class="reviews">
							<div class="container">
								<a href="javascript: void(0)" class="button button-pipaluk button-pipaluk-orange popup-with-form" data-popup-type="review"><i>@lang('main.reviews.оставить-отзыв')</i></a>
								<div id="pdopage">
									<div class="rows">
										@foreach($reviews as $item)
											<div class="item">
												<div class="row">
													<div class="col-md-8">
														<div class="reviews-item">
															<div class="reviews-body wow fadeIn" style="visibility: visible; animation-name: fadeIn;">
																<p class="reviews-text">
																	{{ $item->preview_text }}
																	@if($item->detail_text)
																		<br>
																		<em>
																			<b>@lang('main.reviews.ответ-администрации')</b>:
																			{{ $item->detail_text }}
																		</em>
																	@endif
																</p>
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="reviews-author wow fadeIn" data-wow-delay="1s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 1s; animation-name: fadeIn;">
															<span class="reviews-name">{{ $item->title }}{{ $item->city ? ' | ' . $item->city->name : '' }}</span>
															<span class="reviews-sent">@lang('main.reviews.отправлено'): {{ $item->published_at->format('d.m.Y') }}</span>

														</div>
													</div>
												</div>
											</div>
											<hr>
										@endforeach
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/newsstyle.css') }}">
	<style>
		.article-content .reviews {
			margin-top: 60px;
		}
		.article-content .item {
			display: block;
			color: #4d4d51;
			padding-top: 5%;
			font-size: 18px;
			line-height: 30px;
			letter-spacing: .25px;
		}
	</style>
@endpush

@push('scripts')
	<script>
		$(function(){
			$(document).on('click', '.popup-with-form', function(e) {
				popup($(this));
			});

			function popup($el) {
				$.magnificPopup.open({
					items: {
						src: '#popup'
					},
					type: 'inline',
					preloader: false,
					removalDelay: 300,
					mainClass: 'mfp-fade',
					callbacks: {
						open: function () {
							$.magnificPopup.instance.close = function () {
								$.magnificPopup.proto.close.call(this);
							};

							var $popup = $('#popup');

							$popup.hide();

							var url = '';

							switch ($el.data('popup-type')) {
								case 'review':
									url = '/modal/review';
									break;
							}

							$.ajax({
								type: 'GET',
								url: url,
								success: function (result) {
									if (result.status != 'success') {
										return;
									}

									$popup.find('.popup-container').html(result.html);
									$popup.show();
								}
							});
						}
					}
				});
			}

			$(document).on('change', 'input[name="consent"]', function() {
				var $popup = $(this).closest('.popup'),
					$btn = $popup.find('.js-booking-btn, .js-certificate-btn, .js-callback-btn, .js-review-btn');

				if ($(this).is(':checked')) {
					$btn.removeClass('button-pipaluk-grey')
						.addClass('button-pipaluk-orange')
						.prop('disabled', false);
				} else {
					$btn.removeClass('button-pipaluk-orange')
						.addClass('button-pipaluk-grey')
						.prop('disabled', true);
				}
			});

			$(document).on('click', '.js-review-btn', function() {
				var $popup = $(this).closest('.popup'),
					name = $popup.find('#name').val(),
					body = $popup.find('#body').val(),
					$alertSuccess = $popup.find('.alert-success'),
					$alertError = $popup.find('.alert-danger');

				var data = {
					'name': name,
					'body': body,
				};

				$.ajax({
					url: '/review/create',
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function (result) {
						$alertSuccess.addClass('hidden');
						$alertError.text('').addClass('hidden');
						$('.field-error').removeClass('field-error');

						if (result.status !== 'success') {
							if (result.reason) {
								$alertError.text(result.reason).removeClass('hidden');
							}
							if (result.errors) {
								const entries = Object.entries(result.errors);
								entries.forEach(function (item, key) {
									var fieldId = item[0];
									$('#' + fieldId).addClass('field-error');
								});
							}
							return;
						}

						$alertSuccess.removeClass('hidden');
						$popup.find('#name, #body').val('');
					}
				});
			});
		});
	</script>
@endpush
