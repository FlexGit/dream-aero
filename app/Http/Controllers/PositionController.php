<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\Promo;
use App\Models\Deal;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\Promocode;
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
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user, false);
		$products = $this->productTypeRepo->getActualProductList($user, false);
		$promos = $this->promoRepo->getList($user, false, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user, false);
		
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
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user, false);
		$products = $this->productTypeRepo->getActualProductList($user, false);
		$promos = $this->promoRepo->getList($user, false, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user, false);

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
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user, false);
		$products = $this->productTypeRepo->getActualProductList($user, false, false, true);
		$promos = $this->promoRepo->getList($user, false, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user, false);

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
		
		$deal = Deal::find($dealId);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найден']);
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
			$certificatePeriod = ($product && array_key_exists('certificate_period', $product->data_json)) ? $product->data_json['certificate_period'] : 6;
			$certificate->expire_at = Carbon::parse($certificateExpireAt)->addMonths($certificatePeriod)->format('Y-m-d H:i:s');
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
		$isValidFlightDate = $this->request->is_valid_flight_date ?? 0;
		
		if (!$isValidFlightDate) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректная дата и время начала полета']);
		}

		$deal = $this->dealRepo->getById($dealId);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найден']);
		}

		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if (!$product->validateFlightDate($flightAt)) {
			return response()->json(['status' => 'error', 'reason' => 'Для бронирования полета по тарифу Regular доступны только будние дни']);
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

		if ($certificateNumber) {
			$date = date('Y-m-d');
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			
			// проверка сертификата на валидность
			$certificate = Certificate::whereIn('city_id', [$city->id, 0])
				->where('status_id', $certificateStatus->id)
				->where('product_id', $product->id)
				->where(function ($query) use ($date) {
					$query->where('expire_at', '>=', $date)
						->orWhereNull('expire_at');
				})
				->where('number', $certificateNumber)
				->first();
			if (!$certificate) {
				return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден или не соответствует выбранным параметрам']);
			}
			if (!$certificate->wasUsed()) {
				return response()->json(['status' => 'error', 'reason' => 'Сертификат уже был ранее использован']);
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
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найден']);
		}

		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$city = City::find($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
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
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
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

			$position->product_id = $product->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();
			
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
		$flightAt = ($this->request->flight_date_at ?? '') . ' ' . ($this->request->flight_time_at ?? '');
		$isValidFlightDate = $this->request->is_valid_flight_date ?? 0;
		
		if (!$isValidFlightDate) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректная дата и время начала полета']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if (!$product->validateFlightDate($flightAt)) {
			return response()->json(['status' => 'error', 'reason' => 'Для бронирования полета по тарифу Regular доступны только будние дни']);
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

			$position->product_id = $product->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id ?? 0;
			$position->location_id = $location->id ?? 0;
			$position->flight_simulator_id = $simulator->id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->flight_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
			$position->data_json = !empty($data) ? $data : null;
			$position->save();
			
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
		
		$city = City::find($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
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
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			$position->product_id = $product->id ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $city->id;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ?? 0;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();
			
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

		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$certificateFilePath = ($position->is_certificate_purchase && $position->certificate && is_array($position->certificate->data_json) && array_key_exists('certificate_file_path', $position->certificate->data_json)) ? $position->certificate->data_json['certificate_file_path'] : '';
		
		if (!$position->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		if ($certificateFilePath) {
			Storage::disk('private')->delete($certificateFilePath);
		}

		return response()->json(['status' => 'success']);
	}
}
