@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Wiki
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Wiki</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div>
						<h3>Содержание</h3>
						<ol>
							<li>
								<a href="{{ url('#terms') }}">Термины и определения</a>
							</li>
							<li>
								<a href="{{ url('#objects') }}">Основные объекты и связи между ними</a>
							</li>
							<li>
								<a href="{{ url('#statuses') }}">Статусы операций</a>
							</li>
							<li>
								<a href="{{ url('#calendar') }}">Календарь событий</a>
							</li>
							<li>
								<a href="{{ url('#processes') }}">Операции и процессы</a>
							</li>
							<li>
								<a href="{{ url('#access-rights') }}">Роли и права доступа</a>
							</li>
						</ol>
					</div>
					<div>
						<a name="terms"></a>
						<div>
							<h3>1. Термины и определения</h3>
							<ul>
								<li>
									Сделка -
								</li>
								<li>
									Счет -
								</li>
								<li>
									Сертификат -
								</li>
								<li>
									Событие -
								</li>
							</ul>
						</div>
						<a name="access-rights"></a>
						<div>
							<h3>4. Роли и права доступа</h3>
						</div>
						<a name="objects"></a>
						<div>
							<h3>2. Основные объекты и связи между ними</h3>
							<ul>
								<li>
									Контрагент -
								</li>
								<li>
									Сделка -
								</li>
								<li>
									Позиция сделки -
								</li>
								<li>
									Счет -
								</li>
								<li>
									Сертификат -
								</li>
								<li>
									Событие -
								</li>
								<li>
									Пользователь -
								</li>
								<li>
									Город -
								</li>
								<li>
									Локация -
								</li>
								<li>
									Тип авиатренажера -
								</li>
								<li>
									Продукт -
								</li>
								<li>
									Способ оплаты -
								</li>
								<li>
									Промокод -
								</li>
								<li>
									Акция -
								</li>
								<li>
									Скидка -
								</li>
								<li>
									Балл -
								</li>
								<li>
									Юридическое лицо -
								</li>
							</ul>
						</div>
						<a name="statuses"></a>
						<div>
							<h3>3. Статусы операции</h3>
							<h5>Контрагент</h5>
							<ul>
								<li>
									Базовый
								</li>
								<li>
									Золотой
								</li>
								<li>
									Серебряный
								</li>
								<li>
									Бронзовый
								</li>
								<li>
									Платиновый
								</li>
							</ul>
							<h5>Сделка</h5>
							<ul>
								<li>
									Создана
								</li>
								<li>
									Подтверждена
								</li>
								<li>
									На паузе
								</li>
								<li>
									Возврат
								</li>
								<li>
									Отменена
								</li>
							</ul>
							<h5>Сертификат</h5>
							<ul>
								<li>
									Создан
								</li>
								<li>
									Зарегистрирован
								</li>
								<li>
									Аннулирован
								</li>
								<li>
									Возврат
								</li>
							</ul>
							<h5>Счет</h5>
							<ul>
								<li>
									Не оплачен
								</li>
								<li>
									Оплачен (не подтвержден)
								</li>
								<li>
									Оплачен
								</li>
							</ul>
						</div>
						<a name="processes"></a>
						<div>
							<h3>3. Операции и процессы системы</h3>
						</div>
						<a name="access-rights"></a>
						<div>
							<h3>4. Ценообразование</h3>
							<p></p>
							Алгоритм расчета итоговой стоимости продукта.
							<p></p>
							<b>Итоговая стоимость:</b> <span style="font-size: 20px;"><i>Базовая стоимость продукта - Актуальная скидка</i></span>
							<p></p>
							Актуальная скидка в порядке понижения приоритета:
							<ul>
								<li>
									Скидка на продукт
								</li>
								<li>
									Для продуктов типа Regular или Ultimate:
									<ul>
										<li>
											Скидка по действующему промокоду
										</li>
										<li>
											Скидка по выбранной в учетной системе действующей Акции
										</li>
										<li>
											Скидка с наибольшим значением из списка действующих Акций, включая акцию "День Рождения" (если известна дата рождения контрагента)
										</li>
										<li>
											Скидка контрагента
										</li>
									</ul>
								</li>
							</ul>
							<p></p>
							<b>Исключения:</b>
							<ul>
								<li>
									Есть признак бесплатного полета: <span style="font-size: 20px;"><i>0 (бесплатно)</i></span>
								</li>
								<li>
									Сделка на Бронирование полета по действующему Сертификату: <span style="font-size: 20px;"><i>0 (бесплатно)</i></span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('css')
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
@stop
