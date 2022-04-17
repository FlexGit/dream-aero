<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\LegalEntity;
use App\Models\Promocode;
use App\Services\HelpFunctions;
use App\Services\PayAnyWayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Content;
use App\Models\Location;
use App\Models\FlightSimulator;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\User;
use Validator;

class MainController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function home($cityAlias = null)
	{
		if ($cityAlias && !in_array($cityAlias, City::RU_ALIASES)) {
			abort(404);
		}
		
		//$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		// "Наша команда"
		$users = User::where('enable', true)
			->whereIn('city_id', [$city->id, 0])
			->whereIn('role', [User::ROLE_ADMIN, User::ROLE_PILOT])
			->orderBy('name')
			->get();

		// Отзывы
		$reviewParentContent = HelpFunctions::getEntityByAlias(Content::class, Content::REVIEWS_TYPE);
		if ($reviewParentContent) {
			$reviews = Content::where('is_active', true)
				->where('version', Content::VERSION_RU)
				->where('parent_id', $reviewParentContent->id)
				->orderByDesc('created_at')
				->limit(10)
				->get();
		}

		return view('home', [
			'users' => $users,
			'reviews' => $reviews,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	/**
	 * @param null $productAlias
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getBookingModal($productAlias = null)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$cityAlias = $this->request->session()->get('cityAlias');
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
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCertificateModal($productAlias = null)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		if ($productAlias) {
			$product = Product::where('alias', $productAlias)
				->first();
		} else {
			$products = $city->products()
				->orderBy('product_type_id')
				->orderBy('duration')
				->get();
		}
		
		$VIEW = view('modal.certificate', [
			'city' => $city,
			'product' => $product ?? '',
			'products' => $products ?? [],
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $productAlias
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCertificateBookingModal($productAlias)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$product = Product::where('alias', $productAlias)
			->first();
		
		$VIEW = view('modal.certificate-booking', [
			'product' => $product,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	public function promocodeVerify()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$number = $this->request->promocode ?? '';
		if (!$number) {
			return response()->json(['status' => 'error', 'reason' => 'Не передан промокод']);
		}

		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$date = date('Y-m-d');

		$promocode = Promocode::where('number', $number)
			->whereRelation('cities', 'cities.id', '=', $city->id)
			->where('is_active', true)
			->where(function ($query) use ($date) {
				$query->where('active_from_at', '<=', $date)
					->orWhereNull('active_from_at');
			})
			->where(function ($query) use ($date) {
				$query->where('active_to_at', '>=', $date)
					->orWhereNull('active_to_at');
			})
			->first();
		if (!$promocode) {
			return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
		}

		return response()->json(['status' => 'success', 'message' => 'Промокод применен', 'uuid' => $promocode->uuid]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function about()
	{
		$flightSimulators = FlightSimulator::where('is_active', true)
			->get();

		return view('about', [
			'flightSimulators' => $flightSimulators,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTour()
	{
		return view('virtual-tour');
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
		return view('airbus-virttour-mobile');
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function giftFlight()
	{
		return view('gift-flight');
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function flightTypes()
	{
		return view('flight-types');
	}

	/**
	 * @param null $simulator
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function instruction($simulator = null)
	{
		if ($simulator && $simulator == 'boeing-737-ng') {
			return view('instruction-737-ng');
		}

		if ($simulator && $simulator == 'airbus-a320') {
			return view('instruction-a320');
		}

		return view('instruction');
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function contacts()
	{
		$cityId = $this->request->session()->get('cityId') ?? 1;
		
		$locations = Location::where('is_active', true)
			->where('city_id', $cityId)
			->orderByRaw("FIELD(alias, 'afi') DESC")
			->orderByRaw("FIELD(alias, 'veg') DESC")
			->orderBy('name')
			->get();

		return view('contacts', [
			'locations' => $locations,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function price()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
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
		
		return view('price', [
			'productTypes' => $productTypes,
			'products' => $products,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	public function getCityListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cityAlias = $this->request->session()->get('cityAlias', City::MSK_ALIAS);
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);

		$cities = City::where('is_active', true)
			->where('version', $city->version)
			->get();

		$VIEW = view('city.list', [
			'cities' => $cities,
			'city' => $city,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function changeCity()
	{
		$cityAlias = $this->request->alias ?? '';
		
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?? City::MSK_ALIAS);
		
		$cityName = \App::isLocale('en') ? $city->name_en : $city->name;
		
		$this->request->session()->put('cityId', $city->id);
		$this->request->session()->put('cityAlias', $city->alias);
		$this->request->session()->put('cityVersion', $city->version);
		$this->request->session()->put('cityName', $cityName);
		
		return response()->json(['status' => 'success', 'cityAlias' => $city->alias]);
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
			'consent' => 'required',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'body' => 'Текст отзыва',
				'consent' => 'Согласие на обработку персональых данных',
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
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
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
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success', 'message' => 'Спасибо за Ваш отзыв!']);
	}
	
	/**
	 * Ответ на уведомление об оплате от сервиса Монета
	 *
	 * @return string
	 */
	public function paymentCallback()
	{
		return PayAnyWayService::checkPayCallback($this->request) ? 'SUCCESS' : 'FAIL';
	}
	
	public function paymentSuccess()
	{
	
	}
	
	public function paymentFail()
	{
	
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
		
		return view('oferta', [
			'city' => $city,
			'cityAlias' => $cityAlias,
			'legalEntities' => $legalEntities,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function rules()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		return view('rules', [
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function howToPay()
	{
		$cityAlias = $this->request->session()->get('cityAlias');
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		return view('how-to-pay', [
			'city' => $city,
			'cityAlias' => $cityAlias,
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
		
		if ($alias) {
			$news = Content::where('alias', $alias)
				->where('is_active', true)
				->whereIn('city_id', [$city->id, 0])
				->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
				->first();
			
			if (!$news) {
				abort(404);
			}
			
			return view('news-detail', [
				'news' => $news,
				'city' => $city,
				'cityAlias' => $cityAlias,
			]);
		} else {
			$parentNews = HelpFunctions::getEntityByAlias(Content::class, 'news');
		
			$news = Content::where('parent_id', $parentNews->id)
				->where('is_active', true)
				->whereIn('city_id', [$city->id, 0])
				->where('published_at', '<=', Carbon::now()->format('Y-m-d H:i:s'))
				->latest()
				->get();
			
			return view('news-list', [
				'news' => $news,
				'city' => $city,
				'cityAlias' => $cityAlias,
			]);
		}
	}
	
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
		
		$ips = $content->rating_ips;
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
}