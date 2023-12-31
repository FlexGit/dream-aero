<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Certificate;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\ProductType;
use App\Models\Promo;
use App\Models\Deal;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\Score;
use App\Models\Status;
use App\Repositories\CityRepository;
use App\Repositories\ProductTypeRepository;
use App\Repositories\PromoRepository;
use App\Repositories\PromocodeRepository;
use App\Repositories\DealPositionRepository;
use App\Repositories\DealRepository;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Validator;
use Throwable;

class PositionController extends Controller
{
	private $request;
	private $cityRepo;
	private $promoRepo;
	private $promocodeRepo;
	private $productTypeRepo;
	private $positionRepo;
	private $dealRepo;
	
	/**
	 * PositionController constructor.
	 *
	 * @param Request $request
	 * @param CityRepository $cityRepo
	 */
	public function __construct(Request $request, CityRepository $cityRepo, PromoRepository $promoRepo, PromocodeRepository $promocodeRepo, ProductTypeRepository $productTypeRepo, DealPositionRepository $positionRepo, DealRepository $dealRepo) {
		$this->request = $request;
		$this->cityRepo = $cityRepo;
		$this->promoRepo = $promoRepo;
		$this->promocodeRepo = $promocodeRepo;
		$this->productTypeRepo = $productTypeRepo;
		$this->positionRepo = $positionRepo;
		$this->dealRepo = $dealRepo;
	}

	/**
	 * @param $dealId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addCertificate($dealId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$deal = $this->dealRepo->getById($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user);
		$products = $this->productTypeRepo->getActualProductList($user);
		$promos = $this->promoRepo->getList($user, true, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user);
		
		$VIEW = view('admin.position.modal.certificate.add', [
			'deal' => $deal,
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
			'user' => $user,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $dealId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addBooking($dealId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$deal = $this->dealRepo->getById($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user);
		$products = $this->productTypeRepo->getActualProductList($user);
		$promos = $this->promoRepo->getList($user, true, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user);
		
		$VIEW = view('admin.position.modal.booking.add', [
			'deal' => $deal,
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $dealId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addExtraMinutes($dealId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$deal = $this->dealRepo->getById($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$user = \Auth::user();
		
		$products = $this->productTypeRepo->getActualProductList($user, true, true, false, true);
		
		$events = Event::where('deal_id', $deal->id)
			->where('parent_id', 0)
			->whereHas('dealPosition', function ($query) {
				$query->whereHas('product', function ($query) {
					$query->whereRelation('productType', function ($query) {
						$query->whereIn('alias', [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS]);
					});
				});
			})
			->oldest()
			->get();
		
		$simulators = FlightSimulator::whereIsActive(true)
			->get();
		
		$VIEW = view('admin.position.modal.extra_minutes.add', [
			'deal' => $deal,
			'products' => $products,
			'events' => $events,
			'simulators' => $simulators,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $dealId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addProduct($dealId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$deal = $this->dealRepo->getById($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user);
		$products = $this->productTypeRepo->getActualProductList($user, true, false, true);
		$promos = $this->promoRepo->getList($user, true, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user);
		
		$VIEW = view('admin.position.modal.product.add', [
			'deal' => $deal,
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function editCertificate($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$position = $this->positionRepo->getById($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);

		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user, false);
		$products = $this->productTypeRepo->getActualProductList($user, false);
		$promos = $this->promoRepo->getList($user, false, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user, false, false, $deal->contractor_id ?? 0);
		
		$VIEW = view('admin.position.modal.certificate.edit', [
			'position' => $position,
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function editBooking($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$position = $this->positionRepo->getById($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user, false);
		$products = $this->productTypeRepo->getActualProductList($user, false);
		$promos = $this->promoRepo->getList($user, false, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user, false, false, $deal->contractor_id ?? 0);

		$VIEW = view('admin.position.modal.booking.edit', [
			'position' => $position,
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function editProduct($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$position = $this->positionRepo->getById($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);

		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user, false);
		$products = $this->productTypeRepo->getActualProductList($user, false, false, true);
		$promos = $this->promoRepo->getList($user, false, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user, false, false, $deal->contractor_id ?? 0);

		$VIEW = view('admin.position.modal.product.edit', [
			'position' => $position,
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function storeCertificate()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'product_id' => 'required|numeric|min:0|not_in:0',
			'city_id' => 'required|numeric|min:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$dealId = $this->request->deal_id ?? 0;
		$cityId = $this->request->city_id ?? 0;
		$productId = $this->request->product_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$certificateWhom = $this->request->certificate_whom ?? '';
		$certificateWhomPhone = $this->request->certificate_whom_phone ?? '';
		$comment = $this->request->comment ?? '';
		$deliveryAddress = $this->request->delivery_address ?? '';
		$certificateExpireAt = $this->request->certificate_expire_at ?? null;
		$amount = $this->request->amount ?? 0;
		$isTermless = (bool)$this->request->is_termless;
		
		$deal = Deal::find($dealId);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		}
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$city = null;
		if ($cityId) {
			$city = City::find($cityId);
			if (!$city) {
				return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
			}
		}

		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($cityId ?: 1);
		if (!$cityProduct) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
		}
		
		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}

		$data = [];
		if ($certificateWhom) {
			$data['certificate_whom'] = $certificateWhom;
		}
		if ($certificateWhomPhone) {
			$data['certificate_whom_phone'] = $certificateWhomPhone;
		}
		if ($deliveryAddress) {
			$data['delivery_address'] = $deliveryAddress ?? '';
		}
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			$certificate = new Certificate();
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			$certificate->status_id = $certificateStatus->id ?? 0;
			$certificate->city_id = $cityId;
			$certificate->product_id = $product->id ?? 0;
			$certificatePeriod = ($product && $product->validity) ? $product->validity : '';
			$certificate->expire_at = $isTermless ? null : ($certificatePeriod ? Carbon::parse($certificateExpireAt)->addMonths($certificatePeriod)->format('Y-m-d H:i:s') : null);
			$certificate->save();
			
			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->certificate_id = $certificate->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $cityId;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->is_certificate_purchase = true;
			$position->source = Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();

			$deal->positions()->save($position);
			
			if ($promocodeId) {
				$contractor = $deal->contractor;
				if ($contractor) {
					$promocode->contractors()->save($contractor);
				}
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Position Certificate Store: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function storeBooking()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'product_id' => 'required|numeric|min:0|not_in:0',
			'location_id' => 'required|numeric|min:0|not_in:0',
			'flight_date_at' => 'required|date',
			'flight_time_at' => 'required',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'location_id' => 'Локация',
				'flight_date_at' => 'Желаемая дата полета',
				'flight_time_at' => 'Желаемая время полета',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$dealId = $this->request->deal_id ?? 0;
		$productId = $this->request->product_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->flight_simulator_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$comment = $this->request->comment ?? '';
		$amount = $this->request->amount ?? 0;
		$flightAt = ($this->request->flight_date_at ?? '') . ' ' . ($this->request->flight_time_at ?? '');
		$certificateNumber = $this->request->certificate ?? '';
		$certificateUuid = $this->request->certificate_uuid ?? '';
		$isIndefinitely = $this->request->is_indefinitely ?? 0;
		
		$deal = $this->dealRepo->getById($dealId);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		}
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		$location = Location::find($locationId);
		if (!$location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}

		$city = $location->city;
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
		if (!$cityProduct) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
		}

		$simulator = FlightSimulator::find($simulatorId);
		if (!$simulator) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
		}

		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		$certificateId = $certificateProductAmount = 0;
		if ($certificateNumber || $certificateUuid) {
			$date = date('Y-m-d');
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			
			// проверка сертификата на валидность
			if ($certificateNumber) {
				$certificate = Certificate::whereIn('city_id', [$city->id, 0])
					->where('number', $certificateNumber)
					->first();
			} elseif ($certificateUuid) {
				$certificate = HelpFunctions::getEntityByUuid(Certificate::class, $certificateUuid);
			}
			if (!$certificate) {
				return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден']);
			}
			if ($certificate->wasUsed()) {
				return response()->json(['status' => 'error', 'reason' => 'Сертификат уже был ранее использован']);
			}
			if ($certificate->expire_at && Carbon::parse($certificate->expire_at)->lt($date) && !$isIndefinitely) {
				return response()->json(['status' => 'error', 'reason' => 'Срок действия Сертификата истек']);
			}
			$certificateId = $certificate->id;
			$certificateProduct = $certificate->product;
			if ($certificateProduct && $certificateProduct->alias != $product->alias) {
				$certificateCityProduct = $certificateProduct->cities()->where('cities_products.is_active', true)->find($city->id);
				if ($certificateCityProduct && $certificateCityProduct->pivot) {
					$certificateProductAmount = $certificateCityProduct->pivot->price;
				}
			}
		}
		
		$data = [];
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->certificate_id = $certificate->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id ?? 0;
			$position->location_id = $location->id ?? 0;
			$position->flight_simulator_id = $simulator->id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->flight_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
			$position->source = Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id ?? 0;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();

			$deal->positions()->save($position);
			
			if ($promocodeId) {
				$contractor = $deal->contractor;
				if ($contractor) {
					$promocode->contractors()->save($contractor);
				}
			}
			
			// если сделка на бронирование по сертификату, то регистрируем сертификат
			if ($this->request->certificate && $certificate) {
				$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::REGISTERED_STATUS);
				$certificate->status_id = $certificateStatus->id ?? 0;
				$certificate->save();
			}

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			Log::debug('500 - Position Booking Store: ' . $e->getMessage());

			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function storeExtraMinutes()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$rules = [
			'product_id' => 'required|numeric|min:0|not_in:0',
			'event_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'event_id' => 'Связанный полёт',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$dealId = $this->request->deal_id ?? 0;
		$productId = $this->request->product_id ?? 0;
		$eventId = $this->request->event_id ?? 0;
		$amount = $this->request->amount ?? 0;
		$simulatorId = $this->request->simulator_id ?? 0;
		
		$deal = $this->dealRepo->getById($dealId);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		}
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$parentEvent = Event::find($eventId);
		if (!$parentEvent) {
			return response()->json(['status' => 'error', 'reason' => 'Событие не найдена']);
		}
		
		$parentPosition = $parentEvent->dealPosition;
		if (!$parentPosition) {
			return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		}
		
		$parentProduct = $parentPosition->product;
		if ($parentProduct->productType->alias == ProductType::REGULAR_ALIAS && $product->productType->alias != ProductType::REGULAR_EXTRA_ALIAS) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт должен быть типа Regular Extra']);
		}
		
		if ($parentProduct->productType->alias == ProductType::ULTIMATE_ALIAS && $product->productType->alias != ProductType::ULTIMATE_EXTRA_ALIAS) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт должен быть типа Ultimate Extra']);
		}
		
		$city = $parentEvent->city;
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
		if (!$cityProduct) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
		}
		
		$location = $parentEvent->location;
		if (!$location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}
		
		$simulator = $parentEvent->simulator;
		if (!$simulator) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажёр не найден']);
		}
		
		$stopAt = Carbon::parse($parentEvent->stop_at)->addMinutes($parentEvent->extra_time)->format('Y-m-d H:i');
		
		$lastChildEvent = Event::where('deal_id', $deal->id)
			->where('parent_id', $parentEvent->id)
			->orderByDesc('stop_at')
			->first();
		if ($lastChildEvent) {
			$stopAt = $lastChildEvent->stop_at;
		}
		
		try {
			\DB::beginTransaction();
			
			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id ?? 0;
			$position->location_id = $location->id ?? 0;
			$position->flight_simulator_id = $simulatorId ?: $simulator->id;
			$position->flight_at = Carbon::parse($stopAt)->format('Y-m-d H:i');
			$position->source = Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id ?? 0;
			$position->save();
			
			$event = new Event();
			$event->event_type = $parentEvent->event_type;
			$event->parent_id = $parentEvent->id;
			$event->contractor_id = $parentEvent->contractor_id;
			$event->deal_id = $parentEvent->deal_id;
			$event->deal_position_id = $position->id;
			$event->city_id = $city->id ?? 0;
			$event->location_id = $location->id ?? 0;
			$event->flight_simulator_id = $simulatorId ?: $simulator->id;
			$event->user_id = $user->id ?? 0;
			$event->start_at = Carbon::parse($stopAt)->format('Y-m-d H:i');
			$event->stop_at = Carbon::parse($stopAt)->addMinutes($product->duration)->format('Y-m-d H:i');
			$event->nominal_price = $event->nominalPrice();
			$event->save();
			
			$deal->positions()->save($position);
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Position Extra Minutes Store: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function storeProduct()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'product_id' => 'required|numeric|min:0|not_in:0',
			'city_id' => 'required|numeric|min:0|not_in:0',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$dealId = $this->request->deal_id ?? 0;
		$productId = $this->request->product_id ?? 0;
		$cityId = $this->request->city_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$comment = $this->request->comment ?? '';
		$amount = $this->request->amount ?? 0;
		
		$deal = Deal::find($dealId);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		}
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$productType = $product->productType;
		if (!$productType) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$city = City::find($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
		if (!$cityProduct || !$cityProduct->pivot) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
		}
		
		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}

		$data = [];
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			$position = new DealPosition();
			$position->product_id = $product->id;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->source = Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();

			$deal->positions()->save($position);
			
			if ($promocodeId) {
				$contractor = $deal->contractor;
				if ($contractor) {
					$promocode->contractors()->save($contractor);
				}
			}
			
			if ($productType->alias == ProductType::SERVICES_ALIAS && $deal->balance() >= 0) {
				$city->products()->updateExistingPivot($product->id, [
					'availability' =>  --$cityProduct->pivot->availability,
				]);
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			Log::debug('500 - Position Product Store: ' . $e->getMessage());

			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateCertificate($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$certificate = $position->certificate;
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден']);
		
		$rules = [
			'product_id' => 'required|numeric|min:0|not_in:0',
			'city_id' => 'required|numeric|min:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$cityId = $this->request->city_id ?? 0;
		$productId = $this->request->product_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$certificateWhom = $this->request->certificate_whom ?? '';
		$certificateWhomPhone = $this->request->certificate_whom_phone ?? '';
		$comment = $this->request->comment ?? '';
		$deliveryAddress = $this->request->delivery_address ?? '';
		$amount = $this->request->amount ?? 0;

		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if ($cityId) {
			$city = City::find($cityId);
			if (!$city) {
				return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
			}
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($cityId ?: 1);
		if (!$cityProduct) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
		}

		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		$data = is_array($position->data_json) ? $position->data_json : json_decode($position->data_json, true);
		if ($certificateWhom) {
			$data['certificate_whom'] = $certificateWhom;
		}
		if ($certificateWhomPhone) {
			$data['certificate_whom_phone'] = $certificateWhomPhone;
		}
		if ($deliveryAddress) {
			$data['delivery_address'] = $deliveryAddress ?? '';
		}
		if ($comment) {
			$data['comment'] = $comment;
		}
		
		try {
			\DB::beginTransaction();
			
			foreach ($position->bills as $positionBill) {
				if ($positionBill->status && $positionBill->status->alias != Bill::NOT_PAYED_STATUS) continue;
				
				$positionBill->amount = $positionBill->amount - $position->amount + $amount;
				$positionBill->save();
			}
			
			$oldProductId = $position->product_id;
			$oldCityId = $position->city_id;
			
			$position->product_id = $product->id;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->city_id = $city->id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();
			
			$certificate->product_id = $product->id;
			$certificate->city_id = $city->id ?? 0;
			$certificate->save();

			if ($oldProductId != $product->id || $oldCityId != ($city->id ?? 0)) {
				$position->number = $position->generateNumber();
				$position->save();
				
				$certificate->number = $certificate->generateNumber();
				$certificate->save();
			}
			
			if ($promocodeId) {
				$deal = $position->deal;
				if ($deal) {
					$contractor = $deal->contractor;
					if ($contractor) {
						$promocode->contractors()->save($contractor);
					}
				}
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Position Certificate Update: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateBooking($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$rules = [
			'product_id' => 'required|numeric|min:0|not_in:0',
			'location_id' => 'required|numeric|min:0|not_in:0',
			'flight_date_at' => 'required|date',
			'flight_time_at' => 'required',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'location_id' => 'Локация',
				'flight_date_at' => 'Желаемая дата полета',
				'flight_time_at' => 'Желаемая время полета',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$productId = $this->request->product_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->flight_simulator_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$comment = $this->request->comment ?? '';
		$amount = $this->request->amount ?? 0;
		$certificateNumber = $this->request->certificate ?? '';
		$certificateUuid = $this->request->certificate_uuid ?? '';
		$flightAt = ($this->request->flight_date_at ?? '') . ' ' . ($this->request->flight_time_at ?? '');
		$isValidFlightDate = $this->request->is_valid_flight_date ?? 0;
		
		if (!$isValidFlightDate) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректная дата и время начала полета']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		$location = Location::find($locationId);
		if (!$location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}

		$city = $location->city;
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}

		$simulator = FlightSimulator::find($simulatorId);
		if (!$simulator) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
		}

		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		$certificateId = 0;
		if ($certificateNumber || $certificateUuid) {
			// проверка сертификата на валидность
			if ($certificateNumber) {
				$certificate = Certificate::whereIn('city_id', [$city->id, 0])
					->where('number', $certificateNumber)
					->first();
			} elseif ($certificateUuid) {
				$certificate = HelpFunctions::getEntityByUuid(Certificate::class, $certificateUuid);
			}
			if (!$certificate) {
				return response()->json(['status' => 'error', 'reason' => trans('main.error.сертификат-не-найден')]);
			}
			$certificateId = $certificate->id;
		}

		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
		if (!$cityProduct) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
		}
		
		$data = is_array($position->data_json) ? $position->data_json : json_decode($position->data_json, true);
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();
			
			foreach ($position->bills as $positionBill) {
				if ($positionBill->status && $positionBill->status->alias != Bill::NOT_PAYED_STATUS) continue;
				
				$positionBill->amount = $positionBill->amount - $position->amount + $amount;
				$positionBill->save();
			}
			
			$oldProductId = $position->product_id;
			$oldLocationId = $position->location_id;

			$position->product_id = $product->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id ?? 0;
			$position->location_id = $location->id ?? 0;
			$position->certificate_id = $certificateId;
			$position->flight_simulator_id = $simulator->id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->flight_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
			$position->data_json = !empty($data) ? $data : null;
			$position->save();
			
			if ($oldProductId != $product->id || $oldLocationId != ($location->id ?? 0)) {
				$position->number = $position->generateNumber();
				$position->save();
			}
			
			if ($promocodeId) {
				$deal = $position->deal;
				if ($deal) {
					$contractor = $deal->contractor;
					if ($contractor) {
						$promocode->contractors()->save($contractor);
					}
				}
			}
			
			if (isset($certificate)) {
				$certificate->product_id = $product->id;
				$certificateCreatedStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
				$certificateRegisteredStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::REGISTERED_STATUS);
				// если сделка на бронирование по сертификату, то регистрируем сертификат
				if ($certificate->status_id == $certificateCreatedStatus->id) {
					$certificate->status_id = $certificateRegisteredStatus->id;
				}
				$certificate->save();
			}
			
			$event = $position->event;
			if ($event) {
				if ($product->duration) {
					$event->stop_at = Carbon::parse($event->start_at)->addMinutes($product->duration)->format('Y-m-d H:i');;
				}
				$event->location_id = $location->id ?? 0;
				$event->flight_simulator_id = $simulator->id ?? 0;
				$event->nominal_price = $event->nominalPrice();
				$event->save();
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			Log::debug('500 - Position Booking Update: ' . $e->getMessage());

			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateProduct($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$rules = [
			'product_id' => 'required|numeric|min:0|not_in:0',
			'city_id' => 'required|numeric|min:0|not_in:0',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$productId = $this->request->product_id ?? 0;
		$cityId = $this->request->city_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$comment = $this->request->comment ?? '';
		$amount = $this->request->amount ?? 0;
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$productType = $product->productType;
		if (!$productType) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$city = City::find($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
		if (!$cityProduct || !$cityProduct->pivot) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
		}
		
		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		$data = is_array($position->data_json) ? $position->data_json : json_decode($position->data_json, true);
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();
			
			// если в Сделке изменили позицию
			if ($deal->product_id != $product->id) {
				$prevProduct = Product::find($deal->product_id);
				if ($prevProduct) {
					$cityPrevProduct = $prevProduct->cities()->where('cities_products.is_active', true)->find($city->id);
					if ($cityPrevProduct && $cityPrevProduct->pivot && $productType->alias == ProductType::SERVICES_ALIAS) {
						$city->products()->updateExistingPivot($deal->product_id, [
							'availability' => ++$cityPrevProduct->pivot->availability,
						]);
					}
				}
			}
			
			foreach ($position->bills as $positionBill) {
				if ($positionBill->status && $positionBill->status->alias != Bill::NOT_PAYED_STATUS) continue;
				
				$positionBill->amount = $positionBill->amount - $position->amount + $amount;
				$positionBill->save();
			}
			
			$oldCityId = $position->city_id;
			
			$position->product_id = $product->id ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();
			
			if ($oldCityId != ($city->id ?? 0)) {
				$position->number = $position->generateNumber();
				$position->save();
			}
			
			if ($promocodeId) {
				$deal = $position->deal;
				if ($deal) {
					$contractor = $deal->contractor;
					if ($contractor) {
						$promocode->contractors()->save($contractor);
					}
				}
			}
			
			if ($productType->alias == ProductType::SERVICES_ALIAS && $deal->balance() >= 0) {
				$city->products()->updateExistingPivot($product->id, [
					'availability' =>  --$cityProduct->pivot->availability,
				]);
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			Log::debug('500 - Position Product Update: ' . $e->getMessage());

			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$product = $position->product;
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$productType = $product->productType;
		if (!$productType) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$city = $position->city;
		if ($city) {
			$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
			if (!$cityProduct || !$cityProduct->pivot) {
				return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
			}
		}
		
		$certificateFilePath = ($position->is_certificate_purchase && $position->certificate && is_array($position->certificate->data_json) && array_key_exists('certificate_file_path', $position->certificate->data_json)) ? $position->certificate->data_json['certificate_file_path'] : '';
		
		try {
			\DB::beginTransaction();
			
			// если позицию удаляют, а по ней было списание баллов, то начисляем баллы обратно
			$scores = Score::where('deal_position_id', $position->id)
				->where('type', Score::USED_TYPE)
				->get();
			foreach ($scores as $item) {
				$score = new Score();
				$score->score = $item->score;
				$score->type = Score::SCORING_TYPE;
				$score->contractor_id = $item->contractor_id;
				$score->deal_id = $item->deal_id;
				$score->deal_position_id = $item->deal_position_id;
				$score->user_id = $this->request->user()->id;
				$score->save();
			}
			
			$event = $position->event;
			
			// если это позиция на бронирование полета, то удаляем и событие тоже
			if (!$position->is_certificate_purchase && $event) {
				$childEvents = Event::where('parent_id', $event->id)
					->get();
				/** @var Event[] $childEvents */
				foreach ($childEvents as $childEvent) {
					$childPosition = $childEvent->dealPosition;
					if ($childPosition) {
						$childPosition->delete();
					}
					$childEvent->delete();
				}

				$flightInvitationFilePath = (is_array($event->data_json) && array_key_exists('flight_invitation_file_path', $event->data_json)) ? $event->data_json['flight_invitation_file_path'] : '';
				
				$event->delete();
				
				if ($flightInvitationFilePath) {
					Storage::disk('private')->delete($flightInvitationFilePath);
				}
			}

			$isPaid = false;
			foreach ($position->bills as $positionBill) {
				if ($positionBill->status && $positionBill->status->alias == Bill::PAYED_STATUS) {
					$isPaid = true;
					break;
				}
			}
			
			if ($isPaid) {
				\DB::rollback();
				return response()->json(['status' => 'error', 'reason' => 'Позиция недоступна для удаления, т.к. уже привязана к оплаченному счету']);
			}
			
			foreach ($position->bills as $positionBill) {
				$positionBill->positions()->detach($position->id);
				if (!$positionBill->positions()->count()) {
					$positionBill->delete();
				}
				$positionBill->amount = $positionBill->amount - $position->amount;
				$positionBill->save();
			}
			
			$position->delete();
			
			if ($certificateFilePath) {
				Storage::disk('private')->delete($certificateFilePath);
			}
			
			if ($productType->alias == ProductType::SERVICES_ALIAS && $city) {
				$city->products()->updateExistingPivot($product->id, [
					'availability' =>  ++$cityProduct->pivot->availability,
				]);
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Position Delete: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}
}
