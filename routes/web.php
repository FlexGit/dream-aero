<?php

use App\Http\Controllers\AeroflotBonusController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LegalEntityController;
use App\Http\Controllers\FlightSimulatorController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\PromocodeController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\WikiController;
use App\Http\Controllers\RevisionController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;

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

//Auth::routes(['register' => false]);

Route::group(['middleware' => ['setlanguage']], function () {
	Route::domain(env('DOMAIN_ADMIN', 'dev.dream-aero.ru'))->group(function () {
		Route::get('sitemap.xml', function () {
			abort(404);
		});
		Route::get('robots.txt', function () {
			header('Content-Type: text/plain; charset=UTF-8');
			readfile(dirname(__FILE__) . '/../public/robots-dev.txt');
		});
		
		// Авторизация
		/*Route::get('/register', [RegisteredUserController::class, 'create'])
			->middleware('guest')
			->name('register');
		
		Route::post('/register', [RegisteredUserController::class, 'store'])
			->middleware('guest');*/
	
		Route::get('/login', [AuthenticatedSessionController::class, 'create'])
			->middleware('guest')
			->name('login');
		
		Route::post('/login', [AuthenticatedSessionController::class, 'store'])
			->middleware('guest');
		
		Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
			->middleware('guest')
			->name('password.request');
		
		Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
			->middleware('guest')
			->name('password.email');
		
		Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
			->middleware('guest')
			->name('password.reset');
		
		Route::post('/reset-password', [NewPasswordController::class, 'store'])
			->middleware('guest')
			->name('password.update');
		
		Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
			->middleware('auth')
			->name('verification.notice');
		
		Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
			->middleware(['auth', 'signed', 'throttle:6,1'])
			->name('verification.verify');
		
		Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
			->middleware(['auth', 'throttle:6,1'])
			->name('verification.send');
		
		Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
			->middleware('auth')
			->name('password.confirm');
		
		Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
			->middleware('auth');
		
		Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
			->middleware('auth')
			->name('logout');

		Route::group(['middleware' => ['auth']], function () {
			// Контрагенты
			Route::get('contractor/add', [ContractorController::class, 'add']);
			Route::get('contractor/{id}/edit', [ContractorController::class, 'edit']);
			/*Route::get('contractor/{id}/show', [ContractorController::class, 'show']);*/
	
			Route::get('contractor/{id?}', [ContractorController::class, 'index'])->name('contractorIndex');
			Route::get('contractor/list/ajax', [ContractorController::class, 'getListAjax'])->name('contractorList');
			Route::post('contractor', [ContractorController::class, 'store']);
			Route::post('contractor/search', [ContractorController::class, 'search'])->name('contractorSearch');
			Route::put('contractor/{id}', [ContractorController::class, 'update']);
	
			Route::get('contractor/{id}/score', [ContractorController::class, 'addScore']);
			Route::post('contractor/{id}/score', [ContractorController::class, 'storeScore']);
	
			// Очистка тестовых данных
			//Route::get('/clear', [EventController::class, 'clear'])->name('clear');
	
			// События
			Route::get('/', [EventController::class, 'index'])->name('eventIndex');
			Route::get('event/list/ajax', [EventController::class, 'getListAjax'])->name('eventList');
			Route::post('event/notified', [EventController::class, 'notified'])->name('notified-event');
			Route::post('event', [EventController::class, 'store'])->name('store-event');
			Route::put('event/drag_drop/{id}', [EventController::class, 'dragDrop'])->name('drag-drop-event');
			Route::put('event/{id}', [EventController::class, 'update'])->name('update-event');
			Route::delete('event/{id}/comment/{comment_id}/remove', [EventController::class, 'deleteComment'])->name('delete-comment');
			Route::delete('event/{id}', [EventController::class, 'delete'])->name('delete-event');
	
			Route::get('event/{position_id}/add/{event_type?}', [EventController::class, 'add'])->name('add-event');
			Route::get('event/{id}/edit/{is_shift?}', [EventController::class, 'edit'])->name('edit-event');
			Route::get('event/{id}/show', [EventController::class, 'show'])->name('show-event');
			
			Route::get('event/{uuid}/file', [EventController::class, 'getFlightInvitationFile'])->name('getFlightInvitation');
			Route::post('event/send', [EventController::class, 'sendFlightInvitation'])->name('sendFlightInvitation');
			Route::get('event/{uuid}/doc/file', [EventController::class, 'getDocFile'])->name('getDocFile');
	
			// Сделки
			Route::get('deal/{id?}', [DealController::class, 'index'])->name('dealIndex');
			Route::get('deal/list/ajax', [DealController::class, 'getListAjax'])->name('dealList');
			Route::post('deal/product', [DealController::class, 'storeProduct']);
			Route::put('deal/{id}', [DealController::class, 'update']);
	
			Route::get('deal/certificate/add', [DealController::class, 'addCertificate']);
			Route::get('deal/booking/add', [DealController::class, 'addBooking']);
			Route::get('deal/product/add', [DealController::class, 'addProduct']);
			Route::get('deal/{id}/edit', [DealController::class, 'edit']);
	
			// Позиции сделки
			Route::post('deal_position/certificate', [PositionController::class, 'storeCertificate']);
			Route::put('deal_position/certificate/{id}', [PositionController::class, 'updateCertificate']);
			Route::post('deal_position/booking', [PositionController::class, 'storeBooking']);
			Route::put('deal_position/booking/{id}', [PositionController::class, 'updateBooking']);
			Route::post('deal_position/product', [PositionController::class, 'storeProduct']);
			Route::put('deal_position/product/{id}', [PositionController::class, 'updateProduct']);
			Route::delete('deal_position/{id}', [PositionController::class, 'delete']);
	
			Route::get('deal_position/certificate/add/{deal_id}', [PositionController::class, 'addCertificate']);
			Route::get('deal_position/certificate/{id}/edit', [PositionController::class, 'editCertificate']);
			Route::get('deal_position/booking/add/{deal_id}', [PositionController::class, 'addBooking']);
			Route::get('deal_position/booking/{id}/edit', [PositionController::class, 'editBooking']);
			Route::get('deal_position/product/add/{deal_id}', [PositionController::class, 'addProduct']);
			Route::get('deal_position/product/{id}/edit', [PositionController::class, 'editProduct']);
	
			// Сертификаты
			Route::post('certificate', [CertificateController::class, 'store']);
			Route::put('certificate/{id}', [CertificateController::class, 'update']);
			
			Route::get('certificate/{deal_id}/add', [CertificateController::class, 'add']);
			Route::get('certificate/{id}/edit', [CertificateController::class, 'edit']);
			Route::post('certificate/search', [CertificateController::class, 'search'])->name('certificateSearch');
			
			Route::get('certificate/{uuid}/file', [CertificateController::class, 'getCertificateFile'])->name('getCertificate');
			Route::post('certificate/send', [CertificateController::class, 'sendCertificate'])->name('sendCertificate');
			
			// Счета
			Route::post('bill', [BillController::class, 'store']);
			Route::put('bill/{id}', [BillController::class, 'update']);
			Route::delete('bill/{id}', [BillController::class, 'delete']);
	
			Route::get('bill/{deal_id}/add', [BillController::class, 'add']);
			Route::get('bill/{id}/edit', [BillController::class, 'edit']);
	
			Route::post('bill/paylink/send', [BillController::class, 'sendPayLink'])->name('sendPayLink');
	
			// Скидки
			Route::get('discount', [DiscountController::class, 'index'])->name('discountIndex');
			Route::get('discount/list/ajax', [DiscountController::class, 'getListAjax'])->name('discountList');
	
			Route::post('discount', [DiscountController::class, 'store']);
			Route::put('discount/{id}', [DiscountController::class, 'update']);
			Route::delete('discount/{id}', [DiscountController::class, 'delete']);
	
			Route::get('discount/add', [DiscountController::class, 'add']);
			Route::get('discount/{id}/edit', [DiscountController::class, 'edit']);
			Route::get('discount/{id}/delete', [DiscountController::class, 'confirm']);
			Route::get('discount/{id}/show', [DiscountController::class, 'show']);
	
			// Промокоды
			Route::get('promocode', [PromocodeController::class, 'index'])->name('promocodeIndex');
			Route::get('promocode/list/ajax', [PromocodeController::class, 'getListAjax'])->name('promocodeList');
	
			Route::post('promocode', [PromocodeController::class, 'store']);
			Route::put('promocode/{id}', [PromocodeController::class, 'update']);
			Route::delete('promocode/{id}', [PromocodeController::class, 'delete']);
	
			Route::get('promocode/add', [PromocodeController::class, 'add']);
			Route::get('promocode/{id}/edit', [PromocodeController::class, 'edit']);
			Route::get('promocode/{id}/delete', [PromocodeController::class, 'confirm']);
			Route::get('promocode/{id}/show', [PromocodeController::class, 'show']);
	
			// Цены
			Route::get('pricing', [PricingController::class, 'index'])->name('pricingIndex');
			Route::get('pricing/list/ajax', [PricingController::class, 'getListAjax'])->name('pricingList');
	
			Route::put('pricing/{city_id}/{product_id}', [PricingController::class, 'update']);
			Route::delete('pricing/{city_id}/{product_id}/certificate_template/delete', [PricingController::class, 'deleteCertificateTemplate']);
			Route::delete('pricing/{city_id}/{product_id}', [PricingController::class, 'delete']);
	
			Route::get('pricing/{city_id}/{product_id}/edit', [PricingController::class, 'edit']);
			Route::get('pricing/{city_id}/{product_id}/delete', [PricingController::class, 'confirm']);
			Route::get('pricing/{city_id}/{product_id}/show', [PricingController::class, 'show']);
			
			Route::get('certificate/template/{city_id}/{product_id}/download', [PricingController::class, 'getCertificateTemplateFile'])->name('downloadCertificateTemplateFile');
			Route::post('certificate/template/{city_id}/{product_id}/delete', [PricingController::class, 'deleteCertificateTemplateFile']);
			
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
	
			Route::get('city/user', [CityController::class, 'getUserList'])->name('userListByCity');
	
			// Локации
			Route::get('location', [LocationController::class, 'index'])->name('locationIndex');
			Route::get('location/list/ajax', [LocationController::class, 'getListAjax'])->name('locationList');
	
			Route::post('location', [LocationController::class, 'store']);
			Route::put('location/{id}', [LocationController::class, 'update']);
			Route::delete('location/{id}', [LocationController::class, 'delete']);
	
			Route::get('location/add', [LocationController::class, 'add']);
			Route::get('location/{id}/edit', [LocationController::class, 'edit']);
			Route::get('location/{id}/delete', [LocationController::class, 'confirm']);
			Route::get('location/{id}/show', [LocationController::class, 'show']);
	
			// Юр.лица
			Route::get('legal_entity', [LegalEntityController::class, 'index'])->name('legalEntityIndex');
			Route::get('legal_entity/list/ajax', [LegalEntityController::class, 'getListAjax'])->name('legalEntityList');
	
			Route::post('legal_entity', [LegalEntityController::class, 'store']);
			Route::put('legal_entity/{id}', [LegalEntityController::class, 'update']);
			Route::delete('legal_entity/{id}', [LegalEntityController::class, 'delete']);
	
			Route::get('legal_entity/add', [LegalEntityController::class, 'add']);
			Route::get('legal_entity/{id}/edit', [LegalEntityController::class, 'edit']);
			Route::get('legal_entity/{id}/delete', [LegalEntityController::class, 'confirm']);
			Route::get('legal_entity/{id}/show', [LegalEntityController::class, 'show']);
	
			// Авиатренажероы
			Route::get('flight_simulator', [FlightSimulatorController::class, 'index'])->name('flightSimulatorIndex');
			Route::get('flight_simulator/list/ajax', [FlightSimulatorController::class, 'getListAjax'])->name('flightSimulatorList');
	
			Route::post('flight_simulator', [FlightSimulatorController::class, 'store']);
			Route::put('flight_simulator/{id}', [FlightSimulatorController::class, 'update']);
			Route::delete('flight_simulator/{id}', [FlightSimulatorController::class, 'delete']);
	
			Route::get('flight_simulator/add', [FlightSimulatorController::class, 'add']);
			Route::get('flight_simulator/{id}/edit', [FlightSimulatorController::class, 'edit']);
			Route::get('flight_simulator/{id}/delete', [FlightSimulatorController::class, 'confirm']);
			Route::get('flight_simulator/{id}/show', [FlightSimulatorController::class, 'show']);
	
			// Типы продуктов
			Route::get('product_type', [ProductTypeController::class, 'index'])->name('productTypeIndex');
			Route::get('product_type/list/ajax', [ProductTypeController::class, 'getListAjax'])->name('productTypeList');
	
			Route::post('product_type', [ProductTypeController::class, 'store']);
			Route::put('product_type/{id}', [ProductTypeController::class, 'update']);
			Route::delete('product_type/{id}', [ProductTypeController::class, 'delete']);
	
			Route::get('product_type/add', [ProductTypeController::class, 'add'])->name('productTypeAdd');
			Route::get('product_type/{id}/edit', [ProductTypeController::class, 'edit']);
			Route::get('product_type/{id}/delete', [ProductTypeController::class, 'confirm']);
			Route::get('product_type/{id}/show', [ProductTypeController::class, 'show']);
	
			// Продукты
			Route::get('product', [ProductController::class, 'index'])->name('productIndex');
			Route::get('product/list/ajax', [ProductController::class, 'getListAjax'])->name('productList');
	
			Route::post('product', [ProductController::class, 'store']);
			Route::put('product/{id}', [ProductController::class, 'update']);
			Route::delete('product/{id}', [ProductController::class, 'delete']);
			Route::put('product/{id}/icon/delete', [ProductController::class, 'deleteIcon']);
	
			Route::get('product/add', [ProductController::class, 'add']);
			Route::get('product/{id}/edit', [ProductController::class, 'edit']);
			Route::get('product/{id}/delete', [ProductController::class, 'confirm']);
			Route::get('product/{id}/show', [ProductController::class, 'show']);
	
			// Статусы
			Route::get('status', [StatusController::class, 'index'])->name('statusIndex');
			Route::get('status/list/ajax', [StatusController::class, 'getListAjax'])->name('statusList');
	
			Route::put('status/{id}', [StatusController::class, 'update']);
	
			Route::get('status/{id}/edit', [StatusController::class, 'edit']);
			Route::get('status/{id}/show', [StatusController::class, 'show']);
	
			// Способы оплаты
			Route::get('payment_method', [PaymentMethodController::class, 'index'])->name('paymentMethodIndex');
			Route::get('payment_method/list/ajax', [PaymentMethodController::class, 'getListAjax'])->name('paymentMethodList');
	
			Route::post('payment_method', [PaymentMethodController::class, 'store']);
			Route::put('payment_method/{id}', [PaymentMethodController::class, 'update']);
			Route::delete('payment_method/{id}', [PaymentMethodController::class, 'delete']);
	
			Route::get('payment_method/add', [PaymentMethodController::class, 'add']);
			Route::get('payment_method/{id}/edit', [PaymentMethodController::class, 'edit']);
			Route::get('payment_method/{id}/delete', [PaymentMethodController::class, 'confirm']);
			Route::get('payment_method/{id}/show', [PaymentMethodController::class, 'show']);
	
			// Пользователи
			Route::get('user', [UserController::class, 'index'])->name('userIndex');
			Route::get('user/list/ajax', [UserController::class, 'getListAjax'])->name('userList');
	
			Route::post('user', [UserController::class, 'store']);
			Route::put('user/{id}', [UserController::class, 'update']);
			Route::delete('user/{id}', [UserController::class, 'delete']);
	
			Route::get('user/add', [UserController::class, 'add']);
			Route::get('user/{id}/edit', [UserController::class, 'edit']);
			Route::get('user/{id}/delete', [UserController::class, 'confirm']);
			Route::get('user/{id}/show', [UserController::class, 'show']);
	
			Route::post('user/{id}/password/reset/notification', [UserController::class, 'passwordResetNotification'])->name('passwordResetNotification');
	
			// Акции
			Route::get('promo', [PromoController::class, 'index'])->name('promoIndex');
			Route::get('promo/list/ajax', [PromoController::class, 'getListAjax'])->name('promoList');
	
			Route::post('promo', [PromoController::class, 'store']);
			Route::put('promo/{id}', [PromoController::class, 'update']);
			Route::delete('promo/{id}', [PromoController::class, 'delete']);
	
			Route::get('promo/add', [PromoController::class, 'add']);
			Route::get('promo/{id}/edit', [PromoController::class, 'edit']);
			Route::get('promo/{id}/delete', [PromoController::class, 'confirm']);
			Route::get('promo/{id}/show', [PromoController::class, 'show']);
			Route::put('promo/{id}/image/delete', [PromoController::class, 'deleteImage']);
			Route::post('promo/image/upload', [PromoController::class, 'imageUpload']);
	
			// Уведомления
			Route::get('notification', [NotificationController::class, 'index'])->name('notificationIndex');
			Route::get('notification/list/ajax', [NotificationController::class, 'getListAjax'])->name('notificationList');
			
			Route::post('notification', [NotificationController::class, 'store']);
			Route::put('notification/{id}', [NotificationController::class, 'update']);
			Route::delete('notification/{id}', [NotificationController::class, 'delete']);
			Route::post('notification/{id}/send', [NotificationController::class, 'send']);
	
			Route::get('notification/add', [NotificationController::class, 'add']);
			Route::get('notification/{id}/edit', [NotificationController::class, 'edit']);
			Route::get('notification/{id}/delete', [NotificationController::class, 'confirm']);
			Route::get('notification/{id}/show', [NotificationController::class, 'show']);
			Route::get('notification/{id}/send', [NotificationController::class, 'confirmSend']);
	
			// Лог операций
			Route::get('log/list/ajax', [RevisionController::class, 'getListAjax'])->name('revisionList');
			Route::get('log/{entity?}/{object_id?}', [RevisionController::class, 'index'])->name('revisionIndex');
	
			// Wiki
			Route::get('wiki', [WikiController::class, 'index'])->name('wikiIndex');
	
			// Контент
			Route::get('site/{version}/{type}', [ContentController::class, 'index']);
			Route::get('site/{version}/{type}/list/ajax', [ContentController::class, 'getListAjax']);
			Route::get('site/{version}/{type}/add', [ContentController::class, 'add']);
			Route::get('site/{version}/{type}/{id}/edit', [ContentController::class, 'edit']);
			Route::get('site/{version}/{type}/{id}/delete', [ContentController::class, 'confirm']);
	
			Route::post('site/{version}/{type}', [ContentController::class, 'store']);
			Route::put('site/{version}/{type}/{id}', [ContentController::class, 'update']);
			Route::delete('site/{version}/{type}/{id}', [ContentController::class, 'delete']);
			Route::post('site/{version}/{type}/image/upload', [ContentController::class, 'imageUpload']);
			
			// Отчеты
			Route::get('report/nps', [ReportController::class, 'npsIndex'])->name('npsIndex');
			Route::get('report/nps/list/ajax', [ReportController::class, 'npsGetListAjax'])->name('npsList');
			Route::get('report/file/{filepath}', [ReportController::class, 'getExportFile'])->name('getExportFile');
		});
	});

	Route::domain(env('DOMAIN_RU', 'dream-aero.ru'))->group(function () {
		Route::get('sitemap.xml', function () {
			header('Content-Type: text/xml; charset=UTF-8');
			readfile(dirname(__FILE__) . '/../public/sitemap.xml');
		});
		Route::get('robots.txt', function () {
			header('Content-Type: text/plain; charset=UTF-8');
			readfile(dirname(__FILE__) . '/../public/robots-ru.txt');
		});

		Route::get('o-trenazhere', [MainController::class, 'about'])->name('o-trenazhere');
		Route::get('virtualt', [MainController::class, 'virtualTour'])->name('virtualt');
		
		Route::get('boeing-virttour', [MainController::class, 'virtualTourBoeing'])->name('boeing-virttour');
		Route::post('boeing-virttour', [MainController::class, 'virtualTourBoeing']);
		Route::get('desktop', [MainController::class, 'virtualTourAirbus'])->name('desktop');
		Route::post('desktop', [MainController::class, 'virtualTourAirbus']);
		Route::get('airbus-virttour-mobile', [MainController::class, 'virtualTourAirbusMobile'])->name('airbus-virttour-mobile');
		Route::post('airbus-virttour-mobile', [MainController::class, 'virtualTourAirbusMobile']);
		
		Route::get('podarit-polet', [MainController::class, 'giftFlight'])->name('podarit-polet');
		Route::get('variantyi-poleta', [MainController::class, 'flightTypes'])->name('variantyi-poleta');
		Route::get('instruktazh/{simulator?}', [MainController::class, 'instruction'])->name('instruktazh');
		Route::get('oferta-dreamaero', [MainController::class, 'oferta'])->name('oferta-dreamaero');
		Route::get('pravila', [MainController::class, 'rules'])->name('pravila');
		Route::get('rules-dreamaero', [MainController::class, 'rules'])->name('rules-dreamaero');
		Route::get('how-to-pay', [MainController::class, 'howToPay'])->name('how-to-pay');
		
		Route::get('vipflight', [MainController::class, 'vipFlight'])->name('vip-flight');
		Route::get('sertbuy', [MainController::class, 'certificateForm'])->name('certificate-form');
		
		Route::get('lechenie-aerofobii', [MainController::class, 'flyNoFear'])->name('lechenie-aerofobii');
		
		Route::post('promocode/verify', [MainController::class, 'promocodeVerify']);
		
		Route::post('aeroflot-card/verify', [AeroflotBonusController::class, 'cardVerify']);
		Route::post('aeroflot-card/info', [AeroflotBonusController::class, 'getCardInfo']);
		
		Route::post('review/create', [MainController::class, 'reviewCreate']);
		
		Route::get('city/list/ajax', [MainController::class, 'getCityListAjax']);
		Route::get('city/change', [MainController::class, 'changeCity']);
		
		Route::post('payment/callback', [PaymentController::class, 'paymentCallback']);
		Route::get('payment/success', [PaymentController::class, 'paymentSuccess'])->name('paymentSuccess');
		Route::get('payment/fail', [PaymentController::class, 'paymentFail'])->name('paymentFail');
		Route::get('payment/{uuid}/{type?}', [PaymentController::class, 'payment'])->name('payment');
		
		Route::get('news/{alias?}', [MainController::class, 'getNews'])->name('news');
		Route::post('rating', [MainController::class, 'setRating'])->name('set-rating');
		
		Route::get('vse-akcii/{alias?}', [MainController::class, 'getPromos'])->name('vse-akcii');
		
		Route::get('galereya', [MainController::class, 'getGallery'])->name('galereya');
		
		Route::get('reviews', [MainController::class, 'getReviews'])->name('reviews');
		
		Route::get('modal/booking/{product_alias?}', [MainController::class, 'getBookingModal']);
		Route::get('modal/certificate/{product_alias?}', [MainController::class, 'getCertificateModal']);
		Route::get('modal/certificate-booking/{product_alias}', [MainController::class, 'getCertificateBookingModal']);
		Route::get('modal/order/{product_alias?}', [MainController::class, 'getOrderModal']);
		Route::get('modal/review', [MainController::class, 'getReviewModal']);
		Route::get('modal/scheme/{location_id}', [MainController::class, 'getSchemeModal']);
		Route::get('modal/callback', [MainController::class, 'getCallbackModal']);
		Route::get('modal/vip', [MainController::class, 'getVipFlightModal']);
		
		Route::post('callback', [MainController::class, 'callback'])->name('callbackRequestStore');
		Route::post('question', [MainController::class, 'question'])->name('questionStore');
		
		Route::group(['middleware' => ['citycheck']], function () {
			Route::get('{alias?}', [MainController::class, 'home'])->name('home');
			Route::get('{alias}/price', [MainController::class, 'price']);
			Route::get('{alias}/contacts', [MainController::class, 'contacts']);
		});
	});
	
	Route::domain(env('DOMAIN_EN', 'en.dream-aero.ru'))->group(function () {
		Route::get('sitemap.xml', function () {
			header('Content-Type: text/xml; charset=UTF-8');
			readfile(dirname(__FILE__) . '/../public/sitemap-en.xml');
		});
		Route::get('robots.txt', function () {
			header('Content-Type: text/plain; charset=UTF-8');
			readfile(dirname(__FILE__) . '/../public/robots-en.txt');
		});

		Route::get('o-trenazhere', [MainController::class, 'about'])->name('o-trenazhere');
		Route::get('virtualt', [MainController::class, 'virtualTour'])->name('virtualt');
		
		Route::get('boeing-virttour', [MainController::class, 'virtualTourBoeing'])->name('boeing-virttour');
		Route::post('boeing-virttour', [MainController::class, 'virtualTourBoeing']);
		Route::get('desktop', [MainController::class, 'virtualTourAirbus'])->name('desktop');
		Route::post('desktop', [MainController::class, 'virtualTourAirbus']);
		Route::get('airbus-virttour-mobile', [MainController::class, 'virtualTourAirbusMobile'])->name('airbus-virttour-mobile');
		Route::post('airbus-virttour-mobile', [MainController::class, 'virtualTourAirbusMobile']);
		
		Route::get('podarit-polet', [MainController::class, 'giftFlight'])->name('podarit-polet');
		Route::get('variantyi-poleta', [MainController::class, 'flightTypes'])->name('variantyi-poleta');
		Route::get('instruktazh/{simulator?}', [MainController::class, 'instruction'])->name('instruktazh');
		Route::get('oferta-dreamaero', [MainController::class, 'oferta'])->name('oferta-dreamaero');
		Route::get('pravila', [MainController::class, 'rules'])->name('pravila');
		Route::get('rules-dreamaero', [MainController::class, 'rules'])->name('rules-dreamaero');
		Route::get('how-to-pay', [MainController::class, 'howToPay'])->name('how-to-pay');
		
		Route::get('vipflight', [MainController::class, 'vipFlight'])->name('vip-flight');
		Route::get('sertbuy', [MainController::class, 'certificateForm'])->name('certificate-form');
		
		Route::post('promocode/verify', [MainController::class, 'promocodeVerify']);
		
		Route::post('aeroflot-card/verify', [AeroflotBonusController::class, 'cardVerify']);
		Route::post('aeroflot-card/info', [AeroflotBonusController::class, 'getCardInfo']);
		
		Route::post('review/create', [MainController::class, 'reviewCreate']);
		
		Route::get('city/list/ajax', [MainController::class, 'getCityListAjax']);
		Route::get('city/change', [MainController::class, 'changeCity']);
		
		Route::get('news/{alias?}', [MainController::class, 'getNews'])->name('news');
		Route::post('rating', [MainController::class, 'setRating'])->name('set-rating');
		
		Route::get('vse-akcii/{alias?}', [MainController::class, 'getPromos'])->name('vse-akcii');
		
		Route::get('galereya', [MainController::class, 'getGallery'])->name('galereya');
		
		Route::get('reviews', [MainController::class, 'getReviews'])->name('reviews');
		
		Route::get('modal/booking/{product_alias?}', [MainController::class, 'getBookingModal']);
		Route::get('modal/certificate/{product_alias?}', [MainController::class, 'getCertificateModal']);
		Route::get('modal/certificate-booking/{product_alias}', [MainController::class, 'getCertificateBookingModal']);
		Route::get('modal/order/{product_alias?}', [MainController::class, 'getOrderModal']);
		Route::get('modal/review', [MainController::class, 'getReviewModal']);
		Route::get('modal/scheme/{location_id}', [MainController::class, 'getSchemeModal']);
		Route::get('modal/callback', [MainController::class, 'getCallbackModal']);
		
		Route::post('callback', [MainController::class, 'callback'])->name('callbackRequestStore');
		Route::post('question', [MainController::class, 'question'])->name('questionStore');
		
		Route::group(['middleware' => ['citycheck']], function () {
			Route::get('{alias?}', [MainController::class, 'home'])->name('home');
			Route::get('{alias}/price', [MainController::class, 'price']);
			Route::get('{alias}/contacts', [MainController::class, 'contacts']);
		});
	});

	Route::get('deal/product/calc', [DealController::class, 'calcProductAmount'])->name('calcProductAmount');
	Route::post('deal/certificate', [DealController::class, 'storeCertificate'])->name('dealCertificateStore');
	Route::post('deal/booking', [DealController::class, 'storeBooking'])->name('dealBookingStore');
	Route::post('aeroflot-use/retry', [AeroflotBonusController::class, 'useRetry'])->name('useRetry');
	Route::post('aeroflot-use/refresh', [AeroflotBonusController::class, 'useRefresh'])->name('useRefresh');
	
	Route::get('test/parse/file', [TestController::class, 'parseFile']);
	
	Route::fallback(function () {
		abort(404);
	});
});
