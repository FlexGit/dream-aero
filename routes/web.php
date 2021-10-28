<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;

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
		Route::get('/', [HomeController::class, 'home']);
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
