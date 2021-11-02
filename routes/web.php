<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LocationController;

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

Route::group(['middleware' => ['auth']], function () {
	Route::domain(env('DOMAIN_ADMIN', 'admin.dream-aero.ru'))->group(function () {
		// Календарь
		Route::get('/', [HomeController::class, 'home']);
		
		// Города
		Route::get('city', [CityController::class, 'index']);
		Route::get('city/list/ajax', [CityController::class, 'getListAjax']);
		
		Route::post('city', [CityController::class, 'store']);
		Route::put('city/{id}', [CityController::class, 'update']);
		Route::delete('city/{id}', [CityController::class, 'delete']);
	
		Route::get('city/add', [CityController::class, 'add']);
		Route::get('city/{id}/edit', [CityController::class, 'edit']);
		Route::get('city/{id}/delete', [CityController::class, 'confirm']);
		
		// Локации
		Route::get('location', [LocationController::class, 'index']);
		Route::get('location/list/ajax', [LocationController::class, 'getListAjax']);
		
		Route::post('location', [LocationController::class, 'store']);
		Route::put('location/{id}', [LocationController::class, 'update']);
		Route::delete('location/{id}', [LocationController::class, 'delete']);
		
		Route::get('location/add', [LocationController::class, 'add']);
		Route::get('location/{id}/edit', [LocationController::class, 'edit']);
		Route::get('location/{id}/delete', [LocationController::class, 'confirm']);
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

Route::fallback(function () {
	abort(404);
});
