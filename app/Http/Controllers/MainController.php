<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\LegalEntity;
use App\Models\Promo;
use App\Models\Promocode;
use App\Models\City;
use App\Models\Content;
use App\Models\Location;
use App\Models\FlightSimulator;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\User;
use App\Repositories\PromoRepository;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Browser;

class MainController extends Controller
{
	private $request;
	private $promoRepo;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request, PromoRepository $promoRepo)
	{
		$this->request = $request;
		$this->promoRepo = $promoRepo;
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function home($cityAlias = null)
	{
		if (($cityAlias && !in_array($cityAlias, City::RU_ALIASES) && !in_array($cityAlias, ['price', 'contacts'])) || !$cityAlias) {
			abort(404);
		}
		
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		// Отзывы
		$reviewParentContent = HelpFunctions::getEntityByAlias(Content::class, Content::REVIEWS_TYPE);
		if ($reviewParentContent) {
			$reviews = Content::where('is_active', true)
				->where('version', Content::VERSION_RU)
				->where('parent_id', $reviewParentContent->id)
				->latest()
				->limit(10)
				->get();
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'home_' . $city->alias);
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		$isMobile = (Browser::isDesktop() || Browser::isTablet()) ? false : true;
		
		return view('home', [
			'users' => $users ?? [],
			'reviews' => $reviews ?? [],
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
			'isMobile' => $isMobile,
		]);
	}
	
	/**
	 * @param null $productAlias
	 * @param null $cityAlias
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getBookingModal($productAlias = null, $cityAlias = null)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$cityAlias = $cityAlias ?: $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		if ($productAlias) {
			$product = Product::where('alias', $productAlias)
				->first();
		} else {
			// Продукты "Regular"
			$products = $city->products()
				->whereHas('productType', function ($query) {
					return $query->where('alias', ProductType::REGULAR_ALIAS);
				})
				->orderBy('duration')
				->get();
		}
		
		// Локации
		$locations = $city->locations;
		
		// Праздники
		$holidays = Deal::HOLIDAYS;

		$VIEW = view('modal.booking', [
			'city' => $city,
			'product' => $product ?? '',
			'products' => $products ?? [],
			'locations' => $locations,
			'holidays' => $holidays,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param null $productAlias
	 * @param null $cityAlias
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCertificateModal($productAlias = null, $cityAlias = null)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cityAlias = $cityAlias ?: $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		if ($productAlias) {
			$product = Product::where('alias', $productAlias)
				->first();
			$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
			$dataJson = $cityProduct ? json_decode($cityProduct->pivot->data_json, true) : [];
			$period = (is_array($dataJson) && array_key_exists('certificate_period', $dataJson)) ? $dataJson['certificate_period'] : 6;
		} else {
			$products = $city->products()
				->whereRelation('productType', function ($query) {
					$query->whereNotIn('alias', [ProductType::REGULAR_EXTRA_ALIAS, ProductType::ULTIMATE_EXTRA_ALIAS]);
				})
				->orderBy('product_type_id')
				->orderBy('duration')
				->get();
		}
		
		$VIEW = view('modal.certificate', [
			'city' => $city,
			'product' => isset($product) ? $product : new Product(),
			'period' => isset($period) ? $period : 6,
			'products' => $products ?? [],
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $productAlias
	 * @param $cityAlias null
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCertificateBookingModal($productAlias, $cityAlias = null)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cityAlias = $cityAlias ?: $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$product = Product::where('alias', $productAlias)
			->first();
		
		$VIEW = view('modal.certificate-booking', [
			'product' => $product,
			'city' => $city,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function promocodeVerify()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$number = $this->request->promocode ?? '';
		if (!$number) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.не-передан-промокод')]);
		}

		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->simulator_id ?? 0;
		$isCertificatePurchase = $this->request->is_certificate_purchase ?? 0;
		
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$date = date('Y-m-d H:i:s');

		$promocode = Promocode::whereRaw('lower(number) = "' . mb_strtolower($number) . '"')
			->whereRelation('cities', 'cities.id', '=', $city->id)
			->where('is_active', true)
			->where(function ($query) use ($date) {
				$query->where('active_from_at', '<=', $date)
					->orWhereNull('active_from_at');
			})
			->where(function ($query) use ($date) {
				$query->where('active_to_at', '>=', $date)
					->orWhereNull('active_to_at');
			});
		if ($locationId) {
			$promocode = $promocode->whereIn('location_id', [$locationId, 0]);
		}
		if ($simulatorId) {
			$promocode = $promocode->whereIn('flight_simulator_id', [$simulatorId, 0]);
		}
		if ($isCertificatePurchase) {
			$promocode = $promocode->whereNotIn('type', [Promocode::SIMULATOR_TYPE]);
		}
		$promocode = $promocode->first();
		if (!$promocode) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.промокод-не-найден')]);
		}
		
		// для неперсональных промокодов проверяем доступность для Бронирования или Покупки Сертификата
		if (!$promocode->contractor_id) {
			$dataJson = $promocode->data_json ? (array)$promocode->data_json : [];
			if (!$locationId) {
				$isDiscountAllow = array_key_exists('is_discount_certificate_purchase_allow', $dataJson) ? (bool)$dataJson['is_discount_certificate_purchase_allow'] : false;
			}
			else {
				$isDiscountAllow = array_key_exists('is_discount_booking_allow', $dataJson) ? (bool)$dataJson['is_discount_booking_allow'] : false;
			}
			if (!$isDiscountAllow) {
				return response()->json(['status' => 'error', 'reason' => trans('main.error.промокод-не-найден')]);
			}
		}
		
		return response()->json(['status' => 'success', 'message' => trans('main.modal-booking.промокод-применен'), 'uuid' => $promocode->uuid]);
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function about()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$flightSimulators = FlightSimulator::where('is_active', true)
			->get();
		
		// "Наша команда"
		$users = User::where('enable', true)
			->whereIn('city_id', [$city->id, 0])
			->whereIn('role', [User::ROLE_ADMIN, User::ROLE_PILOT])
			->orderBy('name')
			->get();
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'o-trenajere');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('about', [
			'flightSimulators' => $flightSimulators,
			'users' => $users ?? [],
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTour()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'virtual-tour');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('virtual-tour', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTourAir()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'virtual-tour');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('virtual-tour-air', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTourBoeing()
	{
		return view('boeing-virttour');
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTourAirbus()
	{
		return view('airbus-virttour');
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTourAirbusMobile()
	{
		//return view('airbus-virttour-mobile');
		return view('airbus-virttour');
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function giftFlight()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$page = HelpFunctions::getEntityByAlias(Content::class, 'podarit-polet');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('gift-flight', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function flightTypes()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$page = HelpFunctions::getEntityByAlias(Content::class, 'variantyi-poleta');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('flight-types', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}

	/**
	 * @param null $simulator
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function instruction($simulator = null)
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		if ($simulator && $simulator == 'boeing-737-ng') {
			$page = HelpFunctions::getEntityByAlias(Content::class, 'instruktazh/boeing-737-ng');

			return view('instruction-737-ng', [
				'page' => $page ?? new Content,
				'promobox' => $promobox,
				'city' => $city,
				'cityAlias' => $cityAlias,
			]);
		}

		if ($simulator && $simulator == 'airbus-a320') {
			$page = HelpFunctions::getEntityByAlias(Content::class, 'instruktazh/airbus-a320');

			return view('instruction-a320', [
				'page' => $page ?? new Content,
				'promobox' => $promobox,
				'city' => $city,
				'cityAlias' => $cityAlias,
			]);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'instruktazh');
		
		return view('instruction', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	public function unforgettableEmotions()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'nezabyivaemyie-emoczii');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('unforgettable-emotions', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	public function professionalHelp()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'professionalnaya-pomoshh');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('professional-help', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	public function immersionAviationWorld()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'pogruzhenie-v-mir-aviaczii');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('immersion-aviation-world', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}

	public function flyNoFear()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'lechenie-aerofobii');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		$productTypes = ProductType::where('is_active', true)
			->where('version', $city->version)
			->orderBy('name')
			->get();
		
		$cityProducts = $city->products;
		
		$products = [];
		foreach ($productTypes as $productType) {
			$products[mb_strtoupper($productType->alias)] = [];
			
			foreach ($productType->products ?? [] as $product) {
				foreach ($cityProducts ?? [] as $cityProduct) {
					if ($product->id != $cityProduct->id) continue;
					
					$price = $cityProduct->pivot->price;
					if ($cityProduct->pivot->discount) {
						$price = $cityProduct->pivot->discount->is_fixed ? ($price - $cityProduct->pivot->discount->value) : ($price - $price * $cityProduct->pivot->discount->value / 100);
					}
					
					$pivotData = json_decode($cityProduct->pivot->data_json, true);
					
					$products[mb_strtoupper($productType->alias)][$product->alias] = [
						'id' => $product->id,
						'name' => $product->name,
						'alias' => $product->alias,
						'duration' => $product->duration,
						'price' => round($price),
						'currency' => $cityProduct->pivot->currency ? $cityProduct->pivot->currency->name : 'руб',
						'is_hit' => (bool)$cityProduct->pivot->is_hit,
						'is_booking_allow' => false,
						'is_certificate_purchase_allow' => false,
						'icon_file_path' => (is_array($product->data_json) && array_key_exists('icon_file_path', $product->data_json)) ? $product->data_json['icon_file_path'] : '',
					];
					
					if (array_key_exists('is_booking_allow', $pivotData) && $pivotData['is_booking_allow']) {
						$products[mb_strtoupper($productType->alias)][$product->alias]['is_booking_allow'] = true;
					}
					if (array_key_exists('is_certificate_purchase_allow', $pivotData) && $pivotData['is_certificate_purchase_allow']) {
						$products[mb_strtoupper($productType->alias)][$product->alias]['is_certificate_purchase_allow'] = true;
					}
				}
			}
		}
		
		return view('lechenie-aerofobii', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
			'productTypes' => $productTypes,
			'products' => $products,
		]);
	}

	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function contacts($cityAlias = null)
	{
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$locations = Location::where('is_active', true)
			->where('city_id', $city->id)
			->orderByRaw("FIELD(alias, 'afi') DESC")
			->orderByRaw("FIELD(alias, 'veg') DESC")
			->orderBy('name')
			->get();
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'contacts_' . $city->alias);
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('contacts', [
			'locations' => $locations,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function price($cityAlias = null)
	{
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);
		
		$productTypes = ProductType::where('is_active', true)
			->where('version', $city->version)
			->orderBy('name')
			->get();
		
		$products = [];
		foreach ($productTypes as $productType) {
			$products[mb_strtoupper($productType->alias)] = [];
			
			foreach ($productType->products ?? [] as $product) {
				if (!$product->is_active) continue;
				
				$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
				if (!$cityProduct) continue;
				if (!$cityProduct->pivot) continue;
				if (!$cityProduct->pivot->is_active) continue;
				
				$basePrice = $cityProduct->pivot->price;
				$price = $product->calcAmount(0, $city->id, Deal::WEB_SOURCE, false, 0, 0, 0, 0, 0, false, false, 0, true);

				$products[mb_strtoupper($productType->alias)][$product->alias] = [
					'id' => $product->id,
					'name' => $product->name,
					'alias' => $product->alias,
					'duration' => $product->duration,
					'base_price' => $basePrice,
					'price' => round($price),
					'currency' => $cityProduct->pivot->currency ? $cityProduct->pivot->currency->name : 'руб',
					'is_hit' => (bool)$cityProduct->pivot->is_hit,
					'is_booking_allow' => false,
					'is_certificate_purchase_allow' => false,
					'icon_file_path' => (is_array($product->data_json) && array_key_exists('icon_file_path', $product->data_json)) ? $product->data_json['icon_file_path'] : '',
				];
				
				$pivotData = json_decode($cityProduct->pivot->data_json, true);
				if (array_key_exists('is_booking_allow', $pivotData) && $pivotData['is_booking_allow']) {
					$products[mb_strtoupper($productType->alias)][$product->alias]['is_booking_allow'] = true;
				}
				if (array_key_exists('is_certificate_purchase_allow', $pivotData) && $pivotData['is_certificate_purchase_allow']) {
					$products[mb_strtoupper($productType->alias)][$product->alias]['is_certificate_purchase_allow'] = true;
				}
			}
		}
		
		$locationItems = [];
		foreach ($city->locations as $location) {
			$locationItems[] = $location->name;
		}
		
		$isBlacFridayShow = false;
		if (Carbon::now()->isBetween(Carbon::parse(Lead::BLACK_FRIDAY_START), Carbon::parse(Lead::BLACK_FRIDAY_STOP))) {
			$isBlacFridayShow = true;
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'price_' . $city->alias);
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		$isMobile = (Browser::isDesktop() || Browser::isTablet()) ? false : true;
		
		return view('price', [
			'productTypes' => $productTypes,
			'products' => $products,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'cityAlias' => $cityAlias,
			'locationItems' => $locationItems,
			'isBlacFridayShow' => $isBlacFridayShow,
			'isMobile' => $isMobile,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function changeCity()
	{
		$cityAlias = $this->request->alias ?? '';
		
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?? City::MSK_ALIAS);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityName = \App::isLocale('en') ? $city->name_en : $city->name;
		$currentCityAlias = $this->request->session()->get('cityAlias');
		
		$this->request->session()->put('cityId', $city->id);
		$this->request->session()->put('cityAlias', $city->alias);
		$this->request->session()->put('cityVersion', $city->version);
		$this->request->session()->put('cityName', $cityName);
		$this->request->session()->put('isCityConfirmed', true);
		
		return response()->json(['status' => 'success', 'cityAlias' => $city->alias, 'currentCityAlias' => $currentCityAlias]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function confirmCity()
	{
		$this->request->session()->put('isCityConfirmed', true);

		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function reviewCreate()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'name' => 'required',
			'body' => 'required|min:3',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'body' => 'Текст отзыва',
			]);
		if (!$validator->passes()) {
			$errors = [];
			$validatorErrors = $validator->errors();
			foreach ($rules as $key => $rule) {
				foreach ($validatorErrors->get($key) ?? [] as $error) {
					$errors[$key] = $error;
				}
			}
			return response()->json(['status' => 'error', 'errors' => $errors]);
		}

		$reviewParentContent = HelpFunctions::getEntityByAlias(Content::class, Content::REVIEWS_TYPE);
		if (!$reviewParentContent) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.повторите-позже')]);
		}

		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$name = trim(strip_tags($this->request->name));
		$body = trim(strip_tags($this->request->body));

		$content = new Content();
		$content->title = $name ?? '';
		$content->alias = (string)\Webpatser\Uuid\Uuid::generate();
		$content->preview_text = $body ?? '';
		$content->parent_id = $reviewParentContent->id;
		$content->city_id = $city->id;
		$content->meta_title = 'Отзыв от клиента ' . $name . ' из города ' . $city->name . ' от ' . Carbon::now()->format('d.m.Y');
		$content->meta_description = 'Отзыв от клиента ' . $name . ' из города ' . $city->name . ' от ' . Carbon::now()->format('d.m.Y');
		$content->is_active = 0;
		if (!$content->save()) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.повторите-позже')]);
		}
		
		$job = new \App\Jobs\SendReviewEmail($name, $body);
		$job->handle();
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function oferta()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$legalEntityIds = Location::where('city_id', $city->id)
			->where('is_active', true)
			->pluck('legal_entity_id')
			->all();
		
		$legalEntityIds = array_unique($legalEntityIds);
		
		$legalEntities = LegalEntity::whereIn('id', $legalEntityIds)
			->where('is_active', true)
			->get();
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'oferta-dreamaero');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('oferta', [
			'city' => $city,
			'cityAlias' => $cityAlias,
			'legalEntities' => $legalEntities,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function rules()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'rules-dreamaero');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('rules', [
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function howToPay()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$page = HelpFunctions::getEntityByAlias(Content::class, 'how-to-pay');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('how-to-pay', [
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
		]);
	}
	
	/**
	 * @param $locationId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getSchemeModal($locationId)
	{
		$location = Location::find($locationId);
		
		$VIEW = view('modal.scheme', [
			'location' => $location,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param null $alias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function getNews($alias = null)
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		if ($alias) {
			$news = Content::where('alias', $alias)
				->first();
			
			if (!$news) {
				abort(404);
			}
			
			$leadType = '';
			if (in_array($alias, ['black-friday-2022'])) {
				$products = $city->products()
					->whereRelation('productType', function ($query) {
						$query->whereIn('alias', [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS]);
					})
					->orderBy('product_type_id')
					->orderBy('duration')
					->get();
				$leadType = Lead::BLACK_FRIDAY_TYPE;
			}
			
			return view('news-detail', [
				'news' => $news,
				'products' => isset($products) ? $products : [],
				'city' => $city,
				'cityAlias' => $cityAlias,
				'promobox' => $promobox,
				'leadType' => $leadType,
			]);
		}

		$parentNews = HelpFunctions::getEntityByAlias(Content::class, 'news');
	
		$news = Content::where('parent_id', $parentNews->id)
			->where('is_active', true)
			->whereIn('city_id', [$city->id, 0])
			->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
			->latest()
			->get();
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'news');
		
		return view('news-list', [
			'news' => $news,
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function setRating()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$contentId = $this->request->content_id ?? 0;
		$value = $this->request->value ?? 0;
		
		if (!$contentId || !$value) {
			return response()->json(['status' => 'error']);
		}
		
		$content = Content::find($contentId);
		if (!$content) {
			return response()->json(['status' => 'error']);
		}
		
		$ips = $content->rating_ips ?? [];
		if (in_array($_SERVER['REMOTE_ADDR'], $ips)) {
			return response()->json(['status' => 'error']);
		}
		
		$ratingValue = $content->rating_value;
		$ratingCount = $content->rating_count;
		$ips[] = $_SERVER['REMOTE_ADDR'];

		$content->rating_value = round(($ratingValue * $ratingCount + $value) / ($ratingCount + 1), 1);
		$content->rating_count = $ratingCount + 1;
		$content->rating_ips = $ips;
		if (!$content->save()) {
			return response()->json(['status' => 'error']);
		}
		
		return response()->json(['status' => 'success', 'rating_value' => $content->rating_value, 'rating_count' => $content->rating_count]);
	}
	
	/**
	 * @param null $alias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function getPromos($alias = null)
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		$date = date('Y-m-d');
		
		if ($alias) {
			$promo = Promo::where('alias', $alias)
				->where('is_active', true)
				->where('is_published', true)
				->whereIn('city_id', [$city->id, 0])
				->where(function ($query) use ($date) {
					$query->where('active_from_at', '<=', $date)
						->orWhereNull('active_from_at');
				})
				->where(function ($query) use ($date) {
					$query->where('active_to_at', '>=', $date)
						->orWhereNull('active_to_at');
				})
				->first();
			
			if (!$promo) {
				abort(404);
			}
			
			return view('promos-detail', [
				'promo' => $promo,
				'city' => $city,
				'cityAlias' => $cityAlias,
				'promobox' => $promobox,
			]);
		}

		$promos = Promo::where('is_active', true)
			->where('is_published', true)
			->whereIn('city_id', [$city->id, 0])
			->where(function ($query) use ($date) {
				$query->where('active_from_at', '<=', $date)
					->orWhereNull('active_from_at');
			})
			->where(function ($query) use ($date) {
				$query->where('active_to_at', '>=', $date)
					->orWhereNull('active_to_at');
			})
			->latest()
			->get();
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'vse-akcii');
		
		return view('promos-list', [
			'promos' => $promos,
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function getGallery()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$parentGallery = $page = HelpFunctions::getEntityByAlias(Content::class, 'galereya');
		$gallery = Content::where('parent_id', $parentGallery ? $parentGallery->id : 0)
			->where('is_active', true)
			->whereIn('city_id', [$city->id, 0])
			->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
			->latest()
			->get();
		
		$parentGuests = HelpFunctions::getEntityByAlias(Content::class, 'guests');
		$guests = Content::where('parent_id', $parentGuests ? $parentGuests->id : 0)
			->where('is_active', true)
			->whereIn('city_id', [$city->id, 0])
			->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
			->latest()
			->get();
		
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('gallery', [
			'gallery' => $gallery,
			'guests' => $guests,
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function getReviews()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$parentReviews = HelpFunctions::getEntityByAlias(Content::class, 'reviews');
		
		$reviews = Content::where('parent_id', $parentReviews->id)
			->where('is_active', true)
			->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
			->latest()
			->get();
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'reviews');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('reviews-list', [
			'reviews' => $reviews,
			'city' => $city,
			'cityAlias' => $cityAlias,
			'page' => $page ?? new Content,
			'promobox' => $promobox,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getVipFlightModal()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$productAlias = $this->request->product_alias ?? '';
		if (!$productAlias) {
			return response()->json(['status' => 'error']);
		}
		
		$cityId = 1;
		
		$product = Product::where('alias', $productAlias)
			->first();
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($cityId);
		if (!$cityProduct || !$cityProduct->pivot) {
			return response()->json(['status' => 'error']);
		}

		$amount = $product->calcAmount(0, $cityId, 'web');
		
		$VIEW = view('modal.vip', [
			'product' => $product,
			'amount' => $amount,
			'cityId' => $cityId,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCallbackModal()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$VIEW = view('modal.callback');
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getReviewModal()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$VIEW = view('modal.review');
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCityModal()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cityAlias = $this->request->session()->get('cityAlias', City::MSK_ALIAS);
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);
		
		$cities = City::where('is_active', true)
			->where('version', $city->version)
			->get();
		
		$VIEW = view('modal.city', [
			'cities' => $cities,
			'city' => $city,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
		
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function callback()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$rules = [
			'name' => 'required',
			'phone' => 'required',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => trans('main.modal-booking.имя'),
				'phone' => trans('main.modal-booking.телефон'),
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы'), 'errors' => $validator->errors()]);
		}
		
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$name = trim(strip_tags($this->request->name));
		$phone = trim(strip_tags($this->request->phone));
		
		$job = new \App\Jobs\SendCallbackEmail($name, $phone, $city);
		$job->handle();
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function question()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$rules = [
			'name' => 'required',
			'email' => 'required|email',
			'body' => 'required',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => trans('main.form-feedback.имя'),
				'email' => trans('main.form-feedback.email'),
				'body' => trans('main.form-feedback.текст'),
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы'), 'errors' => $validator->errors()]);
		}
		
		$name = trim(strip_tags($this->request->name));
		$email = trim(strip_tags($this->request->email));
		$body = trim(strip_tags($this->request->body));
		
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$job = new \App\Jobs\SendQuestionEmail($name, $email, $body, $city);
		$job->handle();
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function feedback()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$rules = [
			'name' => 'required',
			'age' => 'required',
			'phone' => 'required',
			'email' => 'required|email',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => trans('main.feedback.как-вас-зовут'),
				'age' => trans('main.feedback.возраст-участника'),
				'phone' => trans('main.feedback.ваш-телефон'),
				'email' => trans('main.feedback.ваш-email'),
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы'), 'errors' => $validator->errors()]);
		}
		
		$name = trim(strip_tags($this->request->name));
		$parentName = trim(strip_tags($this->request->parent_name));
		$age = trim(strip_tags($this->request->age));
		$phone = trim(strip_tags($this->request->phone));
		$email = trim(strip_tags($this->request->email));
		
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$job = new \App\Jobs\SendPersonalFeedbackEmail($name, $parentName, $age, $phone, $email, $city ? $city->name : '');
		$job->handle();
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function lead()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$rules = [
			'name' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'product_id' => 'required',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => trans('main.lead.ваше-имя'),
				'email' => trans('main.lead.ваш-email'),
				'phone' => trans('main.lead.ваш-телефон'),
				'product_id' => trans('main.lead.тариф'),
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы'), 'errors' => $validator->errors()]);
		}
		
		$name = trim(strip_tags($this->request->name));
		$email = trim(strip_tags($this->request->email));
		$phone = trim(strip_tags($this->request->phone));
		$productId = $this->request->product_id;
		$cityId = $this->request->city_id;
		$type = $this->request->type;
		
		$lead = new Lead();
		$lead->type = $type;
		$lead->name = $name;
		$lead->email = $email;
		$lead->phone = $phone;
		$lead->product_id = $productId;
		$lead->city_id = $cityId;
		$lead->save();
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function vipFlight()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$productTypes = ProductType::where('is_active', true)
			->where('alias', ProductType::VIP_ALIAS)
			->where('version', $city->version)
			->orderBy('name')
			->get();
		
		$cityProducts = $city->products;
		
		$products = [];
		foreach ($productTypes as $productType) {
			$products[mb_strtoupper($productType->alias)] = [];
			
			foreach ($productType->products as $product) {
				if (!$product->is_active) continue;
				
				foreach ($cityProducts ?? [] as $cityProduct) {
					if ($product->id != $cityProduct->id) continue;
					
					$price = $cityProduct->pivot->price;
					if ($cityProduct->pivot->discount) {
						$price = $cityProduct->pivot->discount->is_fixed ? ($price - $cityProduct->pivot->discount->value) : ($price - $price * $cityProduct->pivot->discount->value / 100);
					}
					
					$pivotData = json_decode($cityProduct->pivot->data_json, true);
					
					$products[mb_strtoupper($productType->alias)][$product->alias] = [
						'id' => $product->id,
						'name' => $product->name,
						'alias' => $product->alias,
						'duration' => $product->duration,
						'description' => (is_array($product->data_json) && array_key_exists('description', $product->data_json)) ? $product->data_json['description'] : '',
						'price' => round($price),
						'currency' => $cityProduct->pivot->currency ? $cityProduct->pivot->currency->name : 'руб',
						'is_hit' => (bool)$cityProduct->pivot->is_hit,
						'is_booking_allow' => false,
						'is_certificate_purchase_allow' => false,
						'icon_file_path' => (is_array($product->data_json) && array_key_exists('icon_file_path', $product->data_json)) ? $product->data_json['icon_file_path'] : '',
						'user' => [
							'fio' => $product->user ? $product->user->fio() : '',
							'instagram' => ($product->user && array_key_exists('instagram', $product->user->data_json)) ? $product->user->data_json['instagram'] : '',
						],
					];
					
					if (array_key_exists('is_booking_allow', $pivotData) && $pivotData['is_booking_allow']) {
						$products[mb_strtoupper($productType->alias)][$product->alias]['is_booking_allow'] = true;
					}
					if (array_key_exists('is_certificate_purchase_allow', $pivotData) && $pivotData['is_certificate_purchase_allow']) {
						$products[mb_strtoupper($productType->alias)][$product->alias]['is_certificate_purchase_allow'] = true;
					}
				}
			}
		}

		$page = HelpFunctions::getEntityByAlias(Content::class, 'vipflight');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('vipflight', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'products' => $products,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function certificateForm()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$products = $city->products()
			->orderBy('product_type_id')
			->orderBy('duration')
			->get();
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'sertbuy');
		$promobox = $this->promoRepo->getActivePromobox($city);
		
		return view('certificate-form', [
			'page' => $page ?? new Content,
			'promobox' => $promobox,
			'city' => $city,
			'product' => '',
			'products' => $products ?? [],
		]);
	}
	
	/**
	 * @return \Illuminate\Http\Response
	 */
	public function turborss()
	{
		$parentNews = HelpFunctions::getEntityByAlias(Content::class, 'news');
		
		$items = Content::where('parent_id', $parentNews->id)
			->where('is_active', true)
			->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
			->latest()
			->get();
		
		return response()->view('turborss', [
			'items' => $items,
		])->header('Content-Type', 'text/xml');
	}
	
	/**
	 * @return \Illuminate\Http\Response
	 */
	public function sitemap()
	{
		$items = [];
		
		$newsPage = HelpFunctions::getEntityByAlias(Content::class, 'news');
		$items[] = [
			'loc' => url($newsPage->alias),
			'lastmod' => $newsPage->updated_at ? $newsPage->updated_at->tz('GMT')->toAtomString() : Carbon::now()->tz('GMT')->toAtomString(),
			'changefreq' => 'weekly',
			'priority' => 1,
		];

		$galleryPage = HelpFunctions::getEntityByAlias(Content::class, 'galereya');
		$items[] = [
			'loc' => url($galleryPage->alias),
			'lastmod' => $galleryPage->updated_at ? $galleryPage->updated_at->tz('GMT')->toAtomString() : Carbon::now()->tz('GMT')->toAtomString(),
			'changefreq' => 'weekly',
			'priority' => 1,
		];
		
		$reviewsPage = HelpFunctions::getEntityByAlias(Content::class, 'reviews');
		$items[] = [
			'loc' => url($reviewsPage->alias),
			'lastmod' => $reviewsPage->updated_at ? $reviewsPage->updated_at->tz('GMT')->toAtomString() : Carbon::now()->tz('GMT')->toAtomString(),
			'changefreq' => 'weekly',
			'priority' => 1,
		];
		
		$parentPages = HelpFunctions::getEntityByAlias(Content::class, 'pages');
		$pages = Content::where('parent_id', $parentPages->id)
			->where('is_active', true)
			->oldest()
			->get();

		foreach ($pages as $page) {
			if (in_array($page->alias, [Content::GUESTS_TYPE, 'payment'])) continue;
			if (mb_strpos($page->title, 'Админка') !== false) continue;
			
			$url = explode('_', $page->alias);
			
			$items[] = [
				'loc' => url((isset($url[1]) ? $url[1] . '/' : '') . (($url[0] == 'home') ? '' : $url[0])),
				'lastmod' => $page->updated_at ? $page->updated_at->tz('GMT')->toAtomString() : Carbon::now()->tz('GMT')->toAtomString(),
				'changefreq' => 'weekly',
				'priority' => 1,
			];
		}
		
		$parentNews = HelpFunctions::getEntityByAlias(Content::class, 'news');
		$news = Content::where('parent_id', $parentNews->id)
			->where('is_active', true)
			->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
			->latest()
			->get();
		
		foreach ($news as $oneNews) {
			$items[] = [
				'loc' => url('news/' . $oneNews->alias),
				'lastmod' => $oneNews->updated_at ? $oneNews->updated_at->tz('GMT')->toAtomString() : Carbon::now()->tz('GMT')->toAtomString(),
				'changefreq' => 'weekly',
				'priority' => 1,
			];
		}
		
		$date = date('Y-m-d');
		$promos = Promo::where('is_active', true)
			->where('is_published', true)
			->where(function ($query) use ($date) {
				$query->where('active_from_at', '<=', $date)
					->orWhereNull('active_from_at');
			})
			->where(function ($query) use ($date) {
				$query->where('active_to_at', '>=', $date)
					->orWhereNull('active_to_at');
			})
			->latest()
			->get();
		foreach ($promos as $promo) {
			$items[] = [
				'loc' => url('vse-akcii/' . $promo->alias),
				'lastmod' => $promo->updated_at ? $promo->updated_at->tz('GMT')->toAtomString() : Carbon::now()->tz('GMT')->toAtomString(),
				'changefreq' => 'weekly',
				'priority' => 1,
			];
		}
		
		return response()->view('sitemap', [
			'items' => $items,
		])->header('Content-Type', 'text/xml');
	}
}