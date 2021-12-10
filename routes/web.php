<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LegalEntityController;
use App\Http\Controllers\FlightSimulatorTypeController;
use App\Http\Controllers\FlightSimulatorController;
use App\Http\Controllers\TariffTypeController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\AccessRightController;
use App\Http\Controllers\RevisionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

Route::domain(env('DOMAIN_ADMIN', 'admin.dream-aero.ru'))->group(function () {
	Route::group(['middleware' => ['auth']], function () {
		// Календарь
		Route::get('/', [HomeController::class, 'home'])->name('home');
		
		// Заявки
		Route::get('order', [OrderController::class, 'index'])->name('orderIndex');
		Route::get('order/list/ajax', [OrderController::class, 'getListAjax'])->name('orderList');
		Route::post('order', [OrderController::class, 'store']);
		Route::put('order/{id}', [OrderController::class, 'update']);
		
		Route::get('order/add', [OrderController::class, 'add']);
		Route::get('order/{id}/edit', [OrderController::class, 'edit']);
		Route::get('order/{id}/show', [OrderController::class, 'show']);
		
		// Контрагенты
		Route::get('contractor', [ContractorController::class, 'index'])->name('contractorIndex');
		Route::get('contractor/list/ajax', [ContractorController::class, 'getListAjax'])->name('contractorList');
		Route::post('contractor', [ContractorController::class, 'store']);
		Route::put('contractor/{id}', [ContractorController::class, 'update']);
		
		Route::get('contractor/add', [ContractorController::class, 'add']);
		Route::get('contractor/{id}/edit', [ContractorController::class, 'edit']);
		Route::get('contractor/{id}/show', [ContractorController::class, 'show']);
		
		// Скидки
		Route::get('discount', [DiscountController::class, 'index'])->name('discountIndex');
		
		// Города
		Route::get('city', [CityController::class, 'index'])->name('cityIndex');
		Route::get('city/list/ajax', [CityController::class, 'getListAjax'])->name('cityList');
		
		Route::post('city', [CityController::class, 'store']);
		Route::put('city/{id}', [CityController::class, 'update']);
		Route::delete('city/{id}', [CityController::class, 'delete']);
	
		Route::get('city/add', [CityController::class, 'add']);
		Route::get('city/{id}/edit', [CityController::class, 'edit']);
		Route::get('city/{id}/delete', [CityController::class, 'confirm']);
		Route::get('city/{id}/show', [CityController::class, 'show']);
		
		Route::get('city/employee', [CityController::class, 'getEmployeeList'])->name('employeeListByCity');
		
		// Локации
		Route::get('location', [LocationController::class, 'index'])->name('locationIndex');
		Route::get('location/list/ajax', [LocationController::class, 'getListAjax'])->name('locationList');
		
		Route::post('location', [LocationController::class, 'store']);
		Route::put('location/{id}', [LocationController::class, 'update']);
		Route::delete('location/{id}', [LocationController::class, 'delete']);
		
		Route::get('location/add', [LocationController::class, 'add']);
		Route::get('location/{id}/edit', [LocationController::class, 'edit']);
		Route::get('location/{id}/delete', [LocationController::class, 'confirm']);

		// Юр.лица
		Route::get('legal_entity', [LegalEntityController::class, 'index'])->name('legalEntityIndex');
		Route::get('legal_entity/list/ajax', [LegalEntityController::class, 'getListAjax'])->name('legalEntityList');

		Route::post('legal_entity', [LegalEntityController::class, 'store']);
		Route::put('legal_entity/{id}', [LegalEntityController::class, 'update']);
		Route::delete('legal_entity/{id}', [LegalEntityController::class, 'delete']);

		Route::get('legal_entity/add', [LegalEntityController::class, 'add']);
		Route::get('legal_entity/{id}/edit', [LegalEntityController::class, 'edit']);
		Route::get('legal_entity/{id}/delete', [LegalEntityController::class, 'confirm']);

		// Типы авиатренажеров
		Route::get('flight_simulator_type', [FlightSimulatorTypeController::class, 'index'])->name('flightSimulatorTypeIndex');
		Route::get('flight_simulator_type/list/ajax', [FlightSimulatorTypeController::class, 'getListAjax'])->name('flightSimulatorTypeList');

		Route::post('flight_simulator_type', [FlightSimulatorTypeController::class, 'store']);
		Route::put('flight_simulator_type/{id}', [FlightSimulatorTypeController::class, 'update']);
		Route::delete('flight_simulator_type/{id}', [FlightSimulatorTypeController::class, 'delete']);

		Route::get('flight_simulator_type/add', [FlightSimulatorTypeController::class, 'add']);
		Route::get('flight_simulator_type/{id}/edit', [FlightSimulatorTypeController::class, 'edit']);
		Route::get('flight_simulator_type/{id}/delete', [FlightSimulatorTypeController::class, 'confirm']);

		// Авиатренажеры
		Route::get('flight_simulator', [FlightSimulatorController::class, 'index'])->name('flightSimulatorIndex');
		Route::get('flight_simulator/list/ajax', [FlightSimulatorController::class, 'getListAjax'])->name('flightSimulatorList');

		Route::post('flight_simulator', [FlightSimulatorController::class, 'store']);
		Route::put('flight_simulator/{id}', [FlightSimulatorController::class, 'update']);
		Route::delete('flight_simulator/{id}', [FlightSimulatorController::class, 'delete']);

		Route::get('flight_simulator/add', [FlightSimulatorController::class, 'add']);
		Route::get('flight_simulator/{id}/edit', [FlightSimulatorController::class, 'edit']);
		Route::get('flight_simulator/{id}/delete', [FlightSimulatorController::class, 'confirm']);

		// Типы тарифов
		Route::get('tariff_type', [TariffTypeController::class, 'index'])->name('tariffTypeIndex');
		Route::get('tariff_type/list/ajax', [TariffTypeController::class, 'getListAjax'])->name('tariffTypeList');

		Route::post('tariff_type', [TariffTypeController::class, 'store']);
		Route::put('tariff_type/{id}', [TariffTypeController::class, 'update']);
		Route::delete('tariff_type/{id}', [TariffTypeController::class, 'delete']);

		Route::get('tariff_type/add', [TariffTypeController::class, 'add'])->name('tariffTypeAdd');
		Route::get('tariff_type/{id}/edit', [TariffTypeController::class, 'edit']);
		Route::get('tariff_type/{id}/delete', [TariffTypeController::class, 'confirm']);
		
		// Тарифы
		Route::get('tariff', [TariffController::class, 'index'])->name('tariffIndex');
		Route::get('tariff/list/ajax', [TariffController::class, 'getListAjax'])->name('tariffList');
		
		Route::post('tariff', [TariffController::class, 'store']);
		Route::put('tariff/{id}', [TariffController::class, 'update']);
		Route::delete('tariff/{id}', [TariffController::class, 'delete']);
		
		Route::get('tariff/add', [TariffController::class, 'add']);
		Route::get('tariff/{id}/edit', [TariffController::class, 'edit']);
		Route::get('tariff/{id}/delete', [TariffController::class, 'confirm']);
		Route::get('tariff/{id}/show', [TariffController::class, 'show']);
		
		// Продукты
		Route::get('product', [ProductController::class, 'index'])->name('productIndex');
		Route::get('product/list/ajax', [ProductController::class, 'getListAjax'])->name('productList');
		
		Route::post('product', [ProductController::class, 'store']);
		Route::put('product/{id}', [ProductController::class, 'update']);
		Route::delete('product/{id}', [ProductController::class, 'delete']);
		
		Route::get('product/add', [ProductController::class, 'add']);
		Route::get('product/{id}/edit', [ProductController::class, 'edit']);
		Route::get('product/{id}/delete', [ProductController::class, 'confirm']);
		Route::get('product/{id}/show', [ProductController::class, 'show']);
		
		// Права доступа
		/*Route::get('access_right', [AccessRightController::class, 'index'])->name('accessRightIndex');*/
		
		// Лог операций
		Route::get('log/list/ajax', [RevisionController::class, 'getListAjax'])->name('revisionList');
		Route::get('log/{entity?}/{object_id?}', [RevisionController::class, 'index'])->name('revisionIndex');
	});
});

Route::domain(env('DOMAIN_RU', 'dream-aero.ru'))->group(function () {
	Route::get('/', [MainController::class, 'home']);
	Route::get('/o-trenazhere', [MainController::class, 'about']);
	Route::get('/virtualt', [MainController::class, 'virtualTour']);
	Route::get('/contacts', [MainController::class, 'contacts']);
	Route::get('/price', [MainController::class, 'price']);
});

Route::domain(env('DOMAIN_EN', 'dream.aero'))->group(function () {
	Route::get('/', [MainController::class, 'en/home']);
});

/*Route::fallback(function () {
	abort(404);
});*/
