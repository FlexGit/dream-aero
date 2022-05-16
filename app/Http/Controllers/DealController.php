<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Certificate;
use App\Models\Contractor;
use App\Models\Currency;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\Deal;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Promocode;
use App\Models\Status;
use App\Repositories\CityRepository;
use App\Repositories\ProductTypeRepository;
use App\Repositories\PromoRepository;
use App\Repositories\PromocodeRepository;
use App\Repositories\DealPositionRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\DealRepository;
use App\Repositories\StatusRepository;
use App\Services\AeroflotBonusService;
use App\Services\HelpFunctions;
use App\Services\PayAnyWayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Throwable;

class DealController extends Controller
{
	private $request;
	private $cityRepo;
	private $promoRepo;
	private $promocodeRepo;
	private $productTypeRepo;
	private $positionRepo;
	private $dealRepo;
	private $statusRepo;
	private $paymentRepo;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request, CityRepository $cityRepo, PromoRepository $promoRepo, PromocodeRepository $promocodeRepo, ProductTypeRepository $productTypeRepo, DealPositionRepository $positionRepo, DealRepository $dealRepo, StatusRepository $statusRepo, PaymentRepository $paymentRepo) {
		$this->request = $request;
		$this->cityRepo = $cityRepo;
		$this->promoRepo = $promoRepo;
		$this->promocodeRepo = $promocodeRepo;
		$this->productTypeRepo = $productTypeRepo;
		$this->positionRepo = $positionRepo;
		$this->dealRepo = $dealRepo;
		$this->statusRepo = $statusRepo;
		$this->paymentRepo = $paymentRepo;
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$user = \Auth::user();
		
		if ($user->isSuperAdmin()) {
			$cities = City::where('version', $user->version)
				->orderByRaw("FIELD(alias, 'msk') DESC")
				->orderByRaw("FIELD(alias, 'spb') DESC")
				->orderBy('name')
				->get();
		} else {
			$cities = [];
		}
		
		$productTypes = ProductType::orderBy('name')
			->get();
		
		$statuses = Status::whereNotIn('type', [Status::STATUS_TYPE_CONTRACTOR])
			->orderby('type')
			->orderBy('sort')
			->get();
		$statusData = [];
		foreach ($statuses as $status) {
			$statusData[Status::STATUS_TYPES[$status->type]][] = [
				'id' => $status->id,
				'alias' => $status->alias,
				'name' => $status->name,
			];
		}
		
		return view('admin.deal.index', [
			'user' => $user,
			'cities' => $cities,
			'productTypes' => $productTypes,
			'statusData' => $statusData,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$id = $this->request->id ?? 0;
		
		$deals = Deal::whereHas('contractor', function ($query) use ($user) {
			$query->whereHas('city', function ($query) use ($user) {
				$query->where('version', $user->version);
			});
		})->orderBy('id', 'desc');
		if ($this->request->filter_status_id) {
			$deals = $deals->where(function ($query) {
				$query->whereIn('status_id', $this->request->filter_status_id)
					->orWhereRelation('positions', function ($query) {
						return $query->orWhereHas('certificate', function ($query) {
							return $query->whereIn('certificates.status_id', $this->request->filter_status_id);
						});
					})
					->orWhereHas('bills', function ($query) {
						return $query->whereIn('bills.status_id', $this->request->filter_status_id);
					});
			});
		}
		if ($this->request->filter_location_id) {
			$deals = $deals->whereHas('positions', function ($query) {
				return $query->whereIn('location_id', $this->request->filter_location_id);
			});
		}
		if ($this->request->filter_product_id) {
			$deals = $deals->whereHas('positions', function ($query) {
				return $query->whereIn('product_id', $this->request->filter_product_id);
			});
		}
		if ($this->request->search_doc) {
			$deals = $deals->where(function ($query) {
				$query->where('number', 'like', '%' . $this->request->search_doc . '%')
					->orWhereRelation('positions', function ($query) {
						return $query->orWhereHas('certificate', function ($query) {
							return $query->where('certificates.number', 'like', '%' . $this->request->search_doc . '%');
						});
					})
					->orWhereHas('bills', function ($q) {
						return $q->where('bills.number', 'like', '%' . $this->request->search_doc . '%');
					});
			});
		}
		if ($this->request->search_contractor) {
			$deals = $deals->where(function ($query) {
				$query->where('name', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%')
					->orWhereHas('contractor', function ($query) {
						return $query->where('name', 'like', '%' . $this->request->search_contractor . '%')
							->orWhere('lastname', 'like', '%' . $this->request->search_contractor . '%')
							->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
							->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%');
					});
			});
		}
		if (!$user->isSuperAdmin() && $user->city) {
			$deals = $deals->whereIn('city_id', [$user->city->id, 0]);
		}
		if ($id) {
			$deals = $deals->where('id', '<', $id);
		}
		$deals = $deals->limit(20)->get();

		$VIEW = view('admin.deal.list', ['deals' => $deals]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addCertificate()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user);
		$products = $this->productTypeRepo->getActualProductList($user);
		$promos = $this->promoRepo->getList($user, true, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user);
		$paymentMethods = $this->paymentRepo->getPaymentMethodList();
		
		$VIEW = view('admin.deal.modal.certificate.add', [
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
			'paymentMethods' => $paymentMethods,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addBooking()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user);
		$products = $this->productTypeRepo->getActualProductList($user);
		$promos = $this->promoRepo->getList($user, true, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user);
		$paymentMethods = $this->paymentRepo->getPaymentMethodList();

		$VIEW = view('admin.deal.modal.booking.add', [
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
			'paymentMethods' => $paymentMethods,
			'source' => $this->request->source ?? '',
			'flightAt' => $this->request->flight_at ?? '',
			'user' => $user,
			'locationId' => $this->request->location_id ?? 0,
			'simulatorId' => $this->request->simulator_id ?? 0,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addProduct()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		$cities = $this->cityRepo->getList($user);
		$products = $this->productTypeRepo->getActualProductList($user, true, false, true);
		$promos = $this->promoRepo->getList($user, true, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user);
		$paymentMethods = $this->paymentRepo->getPaymentMethodList();

		$VIEW = view('admin.deal.modal.product.add', [
			'cities' => $cities,
			'products' => $products,
			'promos' => $promos,
			'promocodes' => $promocodes,
			'paymentMethods' => $paymentMethods,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$deal = $this->dealRepo->getById($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$statuses = $this->statusRepo->getList(Status::STATUS_TYPE_DEAL);
		
		$VIEW = view('admin.deal.modal.edit', [
			'deal' => $deal,
			'statuses' => $statuses,
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
		
		if ($this->request->source == Deal::WEB_SOURCE) {
			$rules = [
				'name' => 'required',
				'email' => 'required|email',
				'phone' => 'required',
				'product_id' => 'required',
			];
			
			$validator = Validator::make($this->request->all(), $rules)
				->setAttributeNames([
					'name' => trans('main.modal-certificate.имя'),
					'email' => trans('main.modal-certificate.email'),
					'phone' => trans('main.modal-certificate.телефон'),
					'product_id' => trans('main.modal-certificate.выберите-продолжительность-полета'),
				]);
			if (!$validator->passes()) {
				return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы'), 'errors' => $validator->errors()]);
			}
		} else {
			$rules = [
				'name' => 'required',
				'email' => 'required|email|unique_email',
				'phone' => 'required|valid_phone',
				'product_id' => 'required|numeric|min:0|not_in:0',
				'city_id' => 'required|numeric|min:0',
			];
			
			$validator = Validator::make($this->request->all(), $rules)
				->setAttributeNames([
					'name' => 'Имя',
					'email' => 'E-mail',
					'phone' => 'Телефон',
					'product_id' => 'Продукт',
					'city_id' => 'Город',
				]);
			if (!$validator->passes()) {
				return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
			}
		}
		
		$cityId = $this->request->city_id ?: $this->request->user()->city_id;
		$productId = $this->request->product_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$promocodeUuid = $this->request->promocode_uuid ?? '';
		$certificateWhom = $this->request->certificate_whom ?? '';
		$certificateWhomPhone = $this->request->certificate_whom_phone ?? '';
		$comment = $this->request->comment ?? '';
		$deliveryAddress = $this->request->delivery_address ?? '';
		$certificateExpireAt = $this->request->certificate_expire_at ?? null;
		$amount = $this->request->amount ?? 0;
		$hasAeroflotCard = $this->request->has_aeroflot_card ?? 0;
		$cardNumber = $this->request->aeroflot_card_number ?? null;
		$bonusAmount = $this->request->aeroflot_bonus_amount ?? 0;
		$transactionType = $this->request->transaction_type ?? null;
		$contractorId = $this->request->contractor_id ?? 0;
		$name = $this->request->name ?? '';
		$email = $this->request->email ?? '';
		$phone = $this->request->phone ?? '';
		$source = $this->request->source ?? '';
		$isUnified = $this->request->is_unified ?? 0;
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		
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
		
		if ($promocodeUuid) {
			$promocode = HelpFunctions::getEntityByUuid(Promocode::class, $promocodeUuid);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		if ($paymentMethodId) {
			$paymentMethod = PaymentMethod::where('is_active', true)
				->find($paymentMethodId);
			if (!$paymentMethod) {
				return response()->json(['status' => 'error', 'reason' => 'Способ оплаты не найден']);
			}
		}
		
		// Аэрофлот Бонус
		if ($hasAeroflotCard) {
			if (!trim($cardNumber) || !$transactionType) {
				return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы')]);
			}
			
			switch ($transactionType) {
				case AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER:
					if (!trim($bonusAmount)) {
						return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы')]);
					}
					
					// проверяем лимиты по карте и на бэке тоже
					$cardInfoResult = AeroflotBonusService::getCardInfo($cardNumber, $product, $amount);
					$minLimit = floor($amount / 100 * 20);
					if ($bonusAmount > $cardInfoResult['max_limit'] && $bonusAmount < $minLimit) {
						return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы')]);
					}
				break;
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
		
		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
			if (!$contractor) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
			}
		} elseif ($contractorEmail = $this->request->email ?? '') {
			$contractor = Contractor::where('email', $contractorEmail)
				->first();
		}
		
		try {
			\DB::beginTransaction();

			if (!$contractor) {
				$contractor = new Contractor();
				$contractor->name = $name;
				$contractor->email = $email;
				$contractor->phone = $phone;
				$contractor->city_id = $cityId;
				$contractor->source = $source ?: Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user()->id ?? 0;
				$contractor->save();
			}

			$certificate = new Certificate();
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			$certificate->status_id = $certificateStatus->id ?? 0;
			$certificate->city_id = $isUnified ? 0 : $cityId;
			$certificate->product_id = $product->id ?? 0;
			$certificatePeriod = ($product && array_key_exists('certificate_period', $product->data_json)) ? $product->data_json['certificate_period'] : 6;
			$certificate->expire_at = Carbon::parse($certificateExpireAt)->addMonths($certificatePeriod)->format('Y-m-d H:i:s');
			$certificate->save();
			
			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus->id ?? 0;
			$deal->contractor_id = $contractor->id ?? 0;
			$deal->city_id = $cityId;
			$deal->name = $name;
			$deal->phone = $phone;
			$deal->email = $email;
			$deal->source = $source ?: Deal::ADMIN_SOURCE;
			$deal->user_id = $this->request->user()->id ?? 0;
			$deal->save();
			
			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->certificate_id = $certificate->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $cityId;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = ($promocodeId || $promocodeUuid) ? $promocode->id : 0;
			$position->is_certificate_purchase = true;
			$position->source = $source ?: Deal::ADMIN_SOURCE;
			$position->aeroflot_transaction_type = $transactionType;
			$position->aeroflot_card_number = $cardNumber;
			$position->aeroflot_bonus_amount = ($transactionType == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER) ? $bonusAmount : 0;
			$position->user_id = $this->request->user()->id ?? 0;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();

			$deal->positions()->save($position);
			
			
			if ($amount) {
				$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, Bill::ONLINE_PAYMENT_METHOD);
				$billStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
				
				if ($city) {
					if ($city->version == City::EN_VERSION) {
						$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
					}
					else {
						$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
					}
				} else {
					$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
				}
				
				if ($source == Deal::WEB_SOURCE) {
					$billLocation = $city->getLocationForBill();
					if (!$billLocation) {
						\DB::rollback();
						
						Log::debug('500 - Certificate Deal Create: Не найден номер счета платежной системы');
						
						return response()->json(['status' => 'error', 'reason' => 'Не найден номер счета платежной системы!']);
					}
					$billLocationId = $billLocation->id;
				} else {
					$billLocationId = $this->request->user()->location_id ?? 0;
				}
				
				$bill = new Bill();
				$bill->contractor_id = $contractor->id ?? 0;
				$bill->deal_id = $deal->id ?? 0;
				$bill->deal_position_id = $position->id ?? 0;
				$bill->location_id = $billLocationId;
				$bill->payment_method_id = ($source == Deal::WEB_SOURCE) ? $onlinePaymentMethod->id : ($paymentMethodId ?? 0);
				$bill->status_id = $billStatus->id ?? 0;
				$bill->amount = $amount;
				$bill->currency_id = $currency->id ?? 0;
				$bill->user_id = $this->request->user()->id ?? 0;
				$bill->save();
				
				$deal->bills()->save($bill);
			}

			if ($transactionType) {
				switch ($transactionType) {
					case AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER:
						$registerOrderResult = AeroflotBonusService::registerOrder($position);
						if ($registerOrderResult['status']['code'] != 0) {
							\DB::rollback();
							
							\Log::debug('500 - Certificate Deal Create: ' . $registerOrderResult['status']['description']);
							
							return response()->json(['status' => 'error', 'reason' => 'Aeroflot Bonus: ' . $registerOrderResult['status']['description']]);
						}
						
						\DB::commit();
						
						return response()->json(['status' => 'success', 'message' => 'Заявка успешно отправлена! Перенаправляем на страницу "Аэрофлот Бонус"...', 'payment_url' => $registerOrderResult['paymentUrl']]);
					break;
				}
			}
			
			if ($promocodeId || $promocodeUuid) {
				$promocode->contractors()->save($contractor);
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Deal Certificate Store: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		if ($source == Deal::WEB_SOURCE) {
			dispatch(new \App\Jobs\SendPayLinkEmail($bill));
			
			$paymentFormHtml = PayAnyWayService::generatePaymentForm($bill);
			return response()->json(['status' => 'success', 'message' => 'Заявка успешно отправлена! Перенаправляем на страницу оплаты. Пожалуйста, подождите...', 'html' => $paymentFormHtml]);
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

		if ($this->request->source == Deal::WEB_SOURCE) {
			$rules = [
				'name' => 'required',
				'email' => 'required|email',
				'phone' => 'required',
				'product_id' => 'required',
				'location_id' => 'required',
				'flight_date_at' => 'required',
			];
			
			$validator = Validator::make($this->request->all(), $rules)
				->setAttributeNames([
					'name' => trans('main.modal-booking.имя'),
					'email' => trans('main.modal-booking.email'),
					'phone' => trans('main.modal-booking.телефон'),
					'product_id' => trans('main.modal-booking.выберите-продолжительность-полета'),
					'location_id' => trans('main.modal-booking.локация'),
					'flight_date_at' => trans('main.modal-booking.дата-полета'),
				]);
			if (!$validator->passes()) {
				return response()->json(['status' => 'error', 'reason' => trans('main.error.проверьте-правильность-заполнения-полей-формы'), 'errors' => $validator->errors()]);
			}
		} else {
			switch ($this->request->event_type) {
				case Event::EVENT_TYPE_DEAL:
				case Event::EVENT_TYPE_TEST_FLIGHT:
					$rules = [
						'name' => 'required',
						'email' => 'required|email|unique_email',
						'phone' => 'required', //|valid_phone
						'product_id' => 'required|numeric|min:0|not_in:0',
						'location_id' => 'required|numeric|min:0|not_in:0',
						'flight_date_at' => 'required|date',
						'flight_time_at' => 'required',
					];
					
					$validator = Validator::make($this->request->all(), $rules)
						->setAttributeNames([
							'name' => 'Имя',
							'email' => 'E-mail',
							'phone' => 'Телефон',
							'product_id' => 'Продукт',
							'location_id' => 'Локация',
							'flight_date_at' => 'Дата',
							'flight_time_at' => 'Время',
						]);
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
					$rules = [
						'location_id' => 'required|numeric|min:0|not_in:0',
						'flight_date_at' => 'required|date',
						'flight_time_at' => 'required',
						'duration' => 'required|numeric|min:0|not_in:0',
					];
					
					$validator = Validator::make($this->request->all(), $rules)
						->setAttributeNames([
							'location_id' => 'Локация',
							'flight_date_at' => 'Дата',
							'flight_time_at' => 'Время',
							'duration' => 'Длительность',
						]);
				break;
			}
			if (!$validator->passes()) {
				return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
			}
		}
		
		$cityId = $this->request->city_id ?: $this->request->user()->city_id;
		$productId = $this->request->product_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$promocodeUuid = $this->request->promocode_uuid ?? '';
		$comment = $this->request->comment ?? '';
		$amount = $this->request->amount ?? 0;
		$contractorId = $this->request->contractor_id ?? 0;
		$name = $this->request->name ?? '';
		$email = $this->request->email ?? '';
		$phone = $this->request->phone ?? '';
		$source = $this->request->source ?? '';
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		$flightAt = ($this->request->flight_date_at ?? '') . ' ' . ($this->request->flight_time_at ?? '');
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->flight_simulator_id ?? 0;
		$certificateNumber = $this->request->certificate ?? '';
		$eventType = $this->request->event_type ?? '';
		$extraTime = (int)$this->request->extra_time ?? 0;
		$isRepeatedFlight = (bool)$this->request->is_repeated_flight ?? false;
		$isUnexpectedFlight = (bool)$this->request->is_unexpected_flight ?? false;
		$duration = $this->request->duration ?? 0;
		$isValidFlightDate = $this->request->is_valid_flight_date ?? 0;
		
		/*if (!in_array($source, [Deal::WEB_SOURCE, Deal::MOB_SOURCE]) && in_array($eventType, [Event::EVENT_TYPE_DEAL, Event::EVENT_TYPE_TEST_FLIGHT]) && !$isValidFlightDate) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректная дата и время начала полета']);
		}*/
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		if ($source != Deal::WEB_SOURCE && !$product->validateFlightDate($flightAt)) {
			return response()->json(['status' => 'error', 'reason' => 'Для бронирования полета по тарифу Regular доступны только будние дни']);
		}

		$location = Location::find($locationId);
		if (!$location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}
		
		if (!$cityId) {
			$cityId = $location->city->id ?? 0;
		}

		$city = $this->cityRepo->getById($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($cityId);
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

		if ($promocodeUuid) {
			$promocode = HelpFunctions::getEntityByUuid(Promocode::class, $promocodeUuid);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		if ($paymentMethodId) {
			$paymentMethod = PaymentMethod::where('is_active', true)
				->find($paymentMethodId);
			if (!$paymentMethod) {
				return response()->json(['status' => 'error', 'reason' => 'Способ оплаты не найден']);
			}
		}
		
		$certificateId = 0;
		if ($certificateNumber) {
			$date = date('Y-m-d');
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			
			// проверка сертификата на валидность
			$certificate = Certificate::whereIn('city_id', [$cityId, 0])
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
			$certificateId = $certificate->id;
		}
		
		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
			if (!$contractor) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
			}
		} elseif ($email) {
			$contractor = Contractor::where('email', $email)
				->first();
		}
		
		if ($certificateId) {
			$amount = 0;
		}
		
		$data = [];
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			switch ($eventType) {
				case Event::EVENT_TYPE_DEAL:
				case Event::EVENT_TYPE_TEST_FLIGHT:
					if (!$contractor) {
						$contractor = new Contractor();
						$contractor->name = $name;
						$contractor->email = $email;
						$contractor->phone = $phone;
						$contractor->city_id = $cityId;
						$contractor->source = $source ?: Contractor::ADMIN_SOURCE;
						$contractor->user_id = $this->request->user()->id ?? 0;
						$contractor->save();
					}
					
					$deal = new Deal();
					$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
					$deal->status_id = $dealStatus->id ?? 0;
					$deal->contractor_id = $contractor->id ?? 0;
					$deal->city_id = $cityId;
					$deal->name = $name;
					$deal->phone = $phone;
					$deal->email = $email;
					$deal->source = $source ?: Deal::ADMIN_SOURCE;
					$deal->user_id = $this->request->user()->id ?? 0;
					$deal->save();
					
					$position = new DealPosition();
					$position->product_id = $product->id ?? 0;
					$position->certificate_id = $certificateId;
					$position->duration = $product->duration ?? 0;
					$position->amount = $amount;
					$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
					$position->city_id = $cityId;
					$position->location_id = $location->id ?? 0;
					$position->flight_simulator_id = $simulator->id ?? 0;
					$position->promo_id = $promo->id ?? 0;
					$position->promocode_id = ($promocodeId || $promocodeUuid) ? $promocode->id : 0;
					$position->flight_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
					$position->source = $source ?: Deal::ADMIN_SOURCE;
					$position->user_id = $this->request->user()->id ?? 0;
					$position->data_json = !empty($data) ? $data : null;
					$position->save();
				
					$deal->positions()->save($position);
				
					if ($amount) {
						$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, Bill::ONLINE_PAYMENT_METHOD);
						$billStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
						
						if ($city->version == City::EN_VERSION) {
							$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
						} else {
							$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
						}
						
						if ($source == Deal::WEB_SOURCE) {
							$billLocation = $city->getLocationForBill();
							if (!$billLocation) {
								\DB::rollback();
								
								Log::debug('500 - Certificate Deal Create: Не найден номер счета платежной системы');
								
								return response()->json(['status' => 'error', 'reason' => 'Не найден номер счета платежной системы!']);
							}
							$billLocationId = $billLocation->id;
						} else {
							$billLocationId = $this->request->user()->location_id ?? 0;
						}
						
						$bill = new Bill();
						$bill->contractor_id = $contractor->id ?? 0;
						$bill->deal_id = $deal->id ?? 0;
						$bill->deal_position_id = $position->id ?? 0;
						$bill->location_id = $billLocationId;
						$bill->payment_method_id = ($source == Deal::WEB_SOURCE) ? $onlinePaymentMethod->id : ($paymentMethodId ?? 0);
						$bill->status_id = $billStatus->id ?? 0;
						$bill->amount = $amount;
						$bill->currency_id = $currency->id ?? 0;
						$bill->user_id = $this->request->user()->id ?? 0;
						$bill->save();
						
						$deal->bills()->save($bill);
					}

					// если сделка на бронирование по сертификату, то регистрируем сертификат
					if ($certificateId && $certificate) {
						$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::REGISTERED_STATUS);
						$certificate->status_id = $certificateStatus->id ?? 0;
						$certificate->save();
					}
				
					// если сделка создается из календаря, создаем сразу и событие
					if ($source == 'calendar') {
						// создаем новую карточку контрагента, если E-mail из заявки не совпадает E-mail
						// из карточки клиента, и пишем уже этого клиента в событие
						if ($email && $email != $contractor->email) {
							$contractor = new Contractor();
							$contractor->name = $name;
							$contractor->email = $email;
							$contractor->phone = $phone;
							$contractor->city_id = $cityId;
							$contractor->source = $source ?: Contractor::ADMIN_SOURCE;
							$contractor->user_id = $this->request->user()->id ?? 0;
							$contractor->save();
						}
						
						$event = new Event();
						$event->event_type = $eventType;
						$event->contractor_id = $contractor->id ?? 0;
						$event->deal_id = $deal->id ?? 0;
						$event->deal_position_id = $position->id ?? 0;
						$event->city_id = $cityId;
						$event->location_id = $location->id ?? 0;
						$event->flight_simulator_id = $simulator->id ?? 0;
						$event->start_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
						$event->stop_at = Carbon::parse($flightAt)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
						$event->extra_time = $extraTime;
						$event->is_repeated_flight = $isRepeatedFlight;
						$event->is_unexpected_flight = $isUnexpectedFlight;
						$event->save();
						
						$position->event()->save($event);
					}

					if ($promocodeId || $promocodeUuid) {
						$promocode->contractors()->save($contractor);
					}
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
					$event = new Event();
					$event->event_type = $eventType;
					$event->city_id = $cityId;
					$event->location_id = $location->id ?? 0;
					$event->flight_simulator_id = $simulator->id ?? 0;
					$event->start_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($flightAt)->addMinutes($duration)->format('Y-m-d H:i');
					$event->save();
				break;
			}

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			Log::debug('500 - Deal Booking Store: ' . $e->getMessage());

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
			'name' => 'required',
			'email' => 'required|email|unique_email',
			'phone' => 'required|valid_phone',
			'product_id' => 'required|numeric|min:0|not_in:0',
			'city_id' => 'required|numeric|min:0|not_in:0',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'phone' => 'Телефон',
				'product_id' => 'Продукт',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$cityId = $this->request->city_id ?: $this->request->user()->city_id;
		$productId = $this->request->product_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$comment = $this->request->comment ?? '';
		$amount = $this->request->amount ?? 0;
		$contractorId = $this->request->contractor_id ?? 0;
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		$name = $this->request->name ?? '';
		$email = $this->request->email ?? '';
		$phone = $this->request->phone ?? '';
		$source = $this->request->source ?? '';
		
		$city = $this->cityRepo->getById($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
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
		
		if ($paymentMethodId) {
			$paymentMethod = PaymentMethod::where('is_active', true)
				->find($paymentMethodId);
			if (!$paymentMethod) {
				return response()->json(['status' => 'error', 'reason' => 'Способ оплаты не найден']);
			}
		}

		$data = [];
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			if ($contractorId) {
				$contractor = Contractor::find($contractorId);
				if (!$contractor) {
					return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
				}
			} else {
				$contractor = new Contractor();
				$contractor->name = $name;
				$contractor->email = $email;
				$contractor->phone = $phone;
				$contractor->city_id = $cityId;
				$contractor->source = Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user()->id ?? 0;
				$contractor->save();
			}

			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus->id ?? 0;
			$deal->contractor_id = $contractor->id ?? 0;
			$deal->city_id = $cityId;
			$deal->name = $name;
			$deal->phone = $phone;
			$deal->email = $email;
			$deal->user_id = $this->request->user()->id ?? 0;
			$deal->source = $source ?: Deal::ADMIN_SOURCE;
			$deal->save();

			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $cityId;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocodeId ? $promocode->id : 0;
			$position->source = Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id ?? 0;
			$position->source = $source ?: Deal::ADMIN_SOURCE;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();

			$deal->positions()->save($position);
			
			if ($promocodeId) {
				$promocode->contractors()->save($contractor);
			}
			
			if ($amount) {
				$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, Bill::ONLINE_PAYMENT_METHOD);
				$billStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
				
				if ($city->version == City::EN_VERSION) {
					$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
				} else {
					$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
				}
				
				if ((!$paymentMethodId && $source == Deal::WEB_SOURCE) || ($paymentMethodId && $paymentMethod->alias == PaymentMethod::ONLINE_ALIAS)) {
					$billLocation = $city->getLocationForBill();
					if (!$billLocation) {
						\DB::rollback();
						
						Log::debug('500 - Certificate Deal Create: Не найден номер счета платежной системы');
						
						return response()->json(['status' => 'error', 'reason' => 'Не найден номер счета платежной системы!']);
					}
					$billLocationId = $billLocation->id;
				} else {
					$billLocationId = $this->request->user()->location_id ?? 0;
				}
				
				$bill = new Bill();
				$bill->contractor_id = $contractor->id ?? 0;
				$bill->deal_id = $deal->id ?? 0;
				$bill->deal_position_id = $position->id ?? 0;
				$bill->location_id = $billLocationId;
				$bill->payment_method_id = ($source == Deal::WEB_SOURCE) ? $onlinePaymentMethod->id : ($paymentMethodId ?? 0);
				$bill->status_id = $billStatus->id ?? 0;
				$bill->amount = $amount;
				$bill->currency_id = $currency->id ?? 0;
				$bill->user_id = $this->request->user()->id ?? 0;
				$bill->save();
				
				$deal->bills()->save($bill);
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
	public function update($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$rules = [
			'name' => 'required',
			'email' => 'required|email',
			'phone' => 'required|valid_phone',
			'status_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'phone' => 'Телефон',
				'status_id' => 'Статус',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$statusId = $this->request->status_id ?? '';
		/*$comment = $this->request->comment ?? '';*/
		$name = $this->request->name ?? '';
		$email = $this->request->email ?? '';
		$phone = $this->request->phone ?? '';
		
		/*$data = is_array($deal->data_json) ? $deal->data_json : json_decode($deal->data_json, true);
		if ($comment) {
			$data['comment'] = $comment;
		}*/
		
		try {
			\DB::beginTransaction();
			
			$deal->status_id = $statusId;
			$deal->name = $name;
			$deal->email = $email;
			$deal->phone = $phone;
			/*$deal->data_json = $data;*/
			$deal->save();

			// если сделку отменяют, а по ней было списание баллов, то начисляем баллы обратно
			/*if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {

			}*/

			/*if (in_array($deal->status->alias, [Deal::CANCELED_STATUS]) && $deal->certificate && $deal->is_certificate_purchase) {
				$certificateStatus = HelpFunctions::getEntityByAlias('\App\Models\Status', Certificate::CANCELED_STATUS);
				if ($certificateStatus) {
					$certificate = Certificate::find($deal->certificate->id);
					$certificate->status_id = $certificateStatus->id;
					$certificate->save();
				}
			} elseif (in_array($deal->status->alias, [Deal::RETURNED_STATUS]) && $deal->certificate && $deal->is_certificate_purchase) {
				$certificateStatus = HelpFunctions::getEntityByAlias('\App\Models\Status', Certificate::RETURNED_STATUS);
				if ($certificateStatus) {
					$certificate = Certificate::find($deal->certificate->id);
					$certificate->status_id = $certificateStatus->id;
					$certificate->save();
				}
			}*/
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Deal Update: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function calcProductAmount()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$productId = $this->request->product_id ?? 0;
		$contractorId = $this->request->contractor_id ?? 0;
		$promoId = $this->request->promo_id ?? 0;
		$promocodeId = $this->request->promocode_id ?? 0;
		$promocodeUuid = $this->request->promocode_uuid ?? '';
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$certificateNumber = $this->request->certificate ?? '';
		$source = $this->request->source ?? 'admin';
		$flightDate = $this->request->flight_date ?? '';
		$isFree = $this->request->is_free ?? 0;

		if ($this->request->city_id) {
			$cityId = $this->request->city_id ?? 0;
		} elseif ($this->request->location_id) {
			$location = Location::find($locationId);
			$cityId = $location->city ? $location->city->id : 0;
		} else {
			$cityId = 1;
		}
		
		if (!$productId) {
			return response()->json(['status' => 'success', 'amount' => 0]);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		// Если дата - выходный день или праздник, меняем Regular на Ultimate
		if ($flightDate && (in_array(date('w', strtotime(Carbon::parse($flightDate)->format('d.m.Y'))), [0, 6]) || in_array(Carbon::parse($flightDate)->format('d.m.Y'), Deal::HOLIDAYS))) {
			$product = Product::where('alias', ProductType::ULTIMATE_ALIAS . '_' . $product->duration)
				->first();
		}
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($cityId);
		if (!$cityProduct || !$cityProduct->pivot) {
			return response()->json(['status' => 'error', 'reason' => 'Не задана базовая стоимость продукта']);
		}
		
		// базовая стоимость продукта
		$baseAmount = $cityProduct->pivot->price ?? 0;

		if ($promocodeUuid) {
			$promocode = HelpFunctions::getEntityByUuid(Promocode::class, $promocodeUuid);
			if ($promocode) {
				$promocodeId = $promocode->id;
			}
		}

		$certificateId = 0;
		if ($certificateNumber) {
			$certificate = Certificate::where('number', $certificateNumber)
				->first();
			$certificateId = $certificate ? $certificate->id : 0;
		}

		$amount = $product->calcAmount($contractorId, $cityId, $source, $isFree, $locationId, $paymentMethodId, $promoId, $promocodeId, $certificateId);
		
		return response()->json(['status' => 'success', 'amount' => $amount, 'baseAmount' => $baseAmount]);
	}
}
