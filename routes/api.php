<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::domain(env('DOMAIN_ADMIN', 'admin.dream-aero.ru'))->group(function () {
	Route::group(['middleware' => ['throttle:30,1', 'apikey', 'apilog']], function () {
		Route::post('login', [ApiController::class, 'login']);
		Route::post('logout', [ApiController::class, 'logout']);
		Route::post('code/send', [ApiController::class, 'sendCode']);
		Route::get('code/verify', [ApiController::class, 'verifyCode']);
		Route::post('register', [ApiController::class, 'register']);
		Route::post('password/reset', [ApiController::class, 'resetPassword']);
		Route::get('profile', [ApiController::class, 'getProfile']);
		Route::post('profile/save', [ApiController::class, 'saveProfile']);
		Route::post('profile/delete', [ApiController::class, 'deleteProfile']);
		Route::get('profile/reset', [ApiController::class, 'resetProfile']);
		Route::post('profile/avatar/save', [ApiController::class, 'saveAvatar']);
		Route::post('profile/avatar/delete', [ApiController::class, 'deleteAvatar']);
		Route::get('tariff_types', [ApiController::class, 'getTariffTypes']);
		Route::get('tariffs', [ApiController::class, 'getTariffs']);
		Route::get('tariff', [ApiController::class, 'getTariff']);
		Route::get('tariff/price', [ApiController::class, 'getTariffPrice']);
		Route::get('cities', [ApiController::class, 'getCities']);
		Route::get('locations', [ApiController::class, 'getLocations']);
		Route::get('legal_entities', [ApiController::class, 'getLegalEntities']);
		Route::get('promos', [ApiController::class, 'getPromos']);
		Route::get('promo', [ApiController::class, 'getPromo']);
		Route::get('promocode/verify', [ApiController::class, 'verifyPromocode']);
		Route::get('flights', [ApiController::class, 'getFlights']);
		Route::post('deal/create', [ApiController::class, 'createDeal']);
		Route::post('deal/simplified/create', [ApiController::class, 'createDealSimplified']);
		Route::get('certificate/verify', [ApiController::class, 'verifyCertificate']);
		Route::get('notifications', [ApiController::class, 'getNotifications']);
		Route::get('notification', [ApiController::class, 'getNotification']);
		Route::post('feedback', [ApiController::class, 'feedback']);
	});
});