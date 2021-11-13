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

use App\Http\Controllers\Api\ApiUserController;

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
		Route::get('/', [HomeController::class, 'home']);
		
		// Города
		Route::get('city', [CityController::class, 'index']);
		Route::get('city/list/ajax', [CityController::class, 'getListAjax'])->name('cityList');
		
		Route::post('city', [CityController::class, 'store']);
		Route::put('city/{id}', [CityController::class, 'update']);
		Route::delete('city/{id}', [CityController::class, 'delete']);
	
		Route::get('city/add', [CityController::class, 'add']);
		Route::get('city/{id}/edit', [CityController::class, 'edit']);
		Route::get('city/{id}/delete', [CityController::class, 'confirm']);
		Route::get('city/{id}/show', [CityController::class, 'show']);
		
		// Локации
		Route::get('location', [LocationController::class, 'index']);
		Route::get('location/list/ajax', [LocationController::class, 'getListAjax'])->name('locationList');
		
		Route::post('location', [LocationController::class, 'store']);
		Route::put('location/{id}', [LocationController::class, 'update']);
		Route::delete('location/{id}', [LocationController::class, 'delete']);
		
		Route::get('location/add', [LocationController::class, 'add']);
		Route::get('location/{id}/edit', [LocationController::class, 'edit']);
		Route::get('location/{id}/delete', [LocationController::class, 'confirm']);

		// Юр.лица
		Route::get('legal_entity', [LegalEntityController::class, 'index']);
		Route::get('legal_entity/list/ajax', [LegalEntityController::class, 'getListAjax'])->name('legalEntityList');

		Route::post('legal_entity', [LegalEntityController::class, 'store']);
		Route::put('legal_entity/{id}', [LegalEntityController::class, 'update']);
		Route::delete('legal_entity/{id}', [LegalEntityController::class, 'delete']);

		Route::get('legal_entity/add', [LegalEntityController::class, 'add']);
		Route::get('legal_entity/{id}/edit', [LegalEntityController::class, 'edit']);
		Route::get('legal_entity/{id}/delete', [LegalEntityController::class, 'confirm']);

		// Типы авиатренажеров
		Route::get('flight_simulator_type', [FlightSimulatorTypeController::class, 'index']);
		Route::get('flight_simulator_type/list/ajax', [FlightSimulatorTypeController::class, 'getListAjax'])->name('flightSimulatorTypeList');

		Route::post('flight_simulator_type', [FlightSimulatorTypeController::class, 'store']);
		Route::put('flight_simulator_type/{id}', [FlightSimulatorTypeController::class, 'update']);
		Route::delete('flight_simulator_type/{id}', [FlightSimulatorTypeController::class, 'delete']);

		Route::get('flight_simulator_type/add', [FlightSimulatorTypeController::class, 'add']);
		Route::get('flight_simulator_type/{id}/edit', [FlightSimulatorTypeController::class, 'edit']);
		Route::get('flight_simulator_type/{id}/delete', [FlightSimulatorTypeController::class, 'confirm']);

		// Авиатренажеры
		Route::get('flight_simulator', [FlightSimulatorController::class, 'index']);
		Route::get('flight_simulator/list/ajax', [FlightSimulatorController::class, 'getListAjax'])->name('flightSimulatorList');

		Route::post('flight_simulator', [FlightSimulatorController::class, 'store']);
		Route::put('flight_simulator/{id}', [FlightSimulatorController::class, 'update']);
		Route::delete('flight_simulator/{id}', [FlightSimulatorController::class, 'delete']);

		Route::get('flight_simulator/add', [FlightSimulatorController::class, 'add']);
		Route::get('flight_simulator/{id}/edit', [FlightSimulatorController::class, 'edit']);
		Route::get('flight_simulator/{id}/delete', [FlightSimulatorController::class, 'confirm']);

		// Типы тарифов
		Route::get('tariff_type', [TariffTypeController::class, 'index']);
		Route::get('tariff_type/list/ajax', [TariffTypeController::class, 'getListAjax'])->name('tariffTypeList');

		Route::post('tariff_type', [TariffTypeController::class, 'store']);
		Route::put('tariff_type/{id}', [TariffTypeController::class, 'update']);
		Route::delete('tariff_type/{id}', [TariffTypeController::class, 'delete']);

		Route::get('tariff_type/add', [TariffTypeController::class, 'add']);
		Route::get('tariff_type/{id}/edit', [TariffTypeController::class, 'edit']);
		Route::get('tariff_type/{id}/delete', [TariffTypeController::class, 'confirm']);
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
