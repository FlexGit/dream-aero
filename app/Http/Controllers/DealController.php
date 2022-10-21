<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Certificate;
use App\Models\Content;
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
use App\Models\Score;
use App\Models\Status;
use App\Models\User;
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
	 * @param null $dealId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index($dealId = null)
	{
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$locationCount = $user->city ? $user->city->locations->count() : 0;

		if ($user->isSuperAdmin() || $locationCount > 1) {
			$cities = City::where('version', $user->version)
				->orderByRaw("FIELD(alias, 'msk') DESC")
				->orderByRaw("FIELD(alias, 'spb') DESC")
				->orderBy('name');
			if (!$user->isSuperAdmin() && $user->city_id) {
				$cities = $cities->where('id', $user->city_id);
			}
			$cities = $cities->get();
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
		
		if ($dealId) {
			$deal = Deal::find($dealId);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'deals');
		
		return view(	'admin.deal.index', [
			'user' => $user,
			'cities' => $cities,
			'productTypes' => $productTypes,
			'statusData' => $statusData,
			'locationCount' => $locationCount,
			'deal' => $deal ?? null,
			'page' => $page,
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
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$id = $this->request->id ?? 0;
		
		$deals = Deal::/*whereHas('contractor', function ($query) use ($user) {
			$query->whereHas('city', function ($query) use ($user) {
				$query->where('version', $user->version);
			});
		})->*/orderBy('id', 'desc');
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
		if ($this->request->filter_advanced) {
			if (in_array('with_promo', $this->request->filter_advanced)) {
				$deals = $deals->whereHas('positions', function ($query) {
					return $query->has('promo');
				});
			}
			if (in_array('with_promocode', $this->request->filter_advanced)) {
				$deals = $deals->whereHas('positions', function ($query) {
					return $query->has('promocode');
				});
			}
			if (in_array('with_score', $this->request->filter_advanced)) {
				$deals = $deals->whereHas('scores', function ($query) {
					return $query->where('type', '=', Score::USED_TYPE);
				});
			}
			if (in_array('with_miles', $this->request->filter_advanced)) {
				$deals = $deals->whereHas('bills', function ($query) {
					return $query->whereNotNull('aeroflot_transaction_type');
				});
			}
		}
		if ($this->request->search_doc) {
			$deals = $deals->where(function ($query) {
				$query->where('number', 'like', '%' . $this->request->search_doc . '%')
					->orWhere('uuid', $this->request->search_doc)
					->orWhere('name', 'like', '%' . $this->request->search_doc . '%')
					->orWhere('email', 'like', '%' . $this->request->search_doc . '%')
					->orWhere('phone', 'like', '%' . $this->request->search_doc . '%')
					->orWhere('id', $this->request->search_doc)
					->orWhereRelation('positions', function ($query) {
						return $query->where('number', 'like', '%' . $this->request->search_doc . '%')
							->orWhereHas('certificate', function ($query) {
								return $query->where('certificates.number', 'like', '%' . $this->request->search_doc . '%');
							});
					})
					->orWhereHas('bills', function ($q) {
						return $q->where('bills.number', 'like', '%' . $this->request->search_doc . '%');
					})
					->orWhereHas('contractor', function ($query) {
						return $query->where('name', 'like', '%' . $this->request->search_doc . '%')
							->orWhere('lastname', 'like', '%' . $this->request->search_doc . '%')
							->orWhere('email', 'like', '%' . $this->request->search_doc . '%')
							->orWhere('phone', 'like', '%' . $this->request->search_doc . '%')
							->orWhere('id', $this->request->search_doc);
					});
			});
		}
		/*if ($this->request->search_contractor) {
			$deals = $deals->where(function ($query) {
				$query->where('name', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('id', $this->request->search_contractor)
					->orWhereHas('contractor', function ($query) {
						return $query->where('name', 'like', '%' . $this->request->search_contractor . '%')
							->orWhere('lastname', 'like', '%' . $this->request->search_contractor . '%')
							->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
							->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%')
							->orWhere('id', $this->request->search_contractor);
					});
			});
		}*/
		if (!$user->isSuperAdmin() && $user->city) {
			$deals = $deals->where('city_id', $user->city->id);
		}
		if ($id) {
			$deals = $deals->where('id', '<', $id);
		}
		$deals = $deals->limit(20)->get();

		$data = [
			'deals' => $deals,
			'user' => $user,
		];
		
		$VIEW = view('admin.deal.list', $data);

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
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

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
			'user' => $user,
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
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$cities = $this->cityRepo->getList($user);
		$products = $this->productTypeRepo->getActualProductList($user);
		$promos = $this->promoRepo->getList($user, true, true, [Promo::MOB_REGISTRATION_SCORES_ALIAS]);
		$promocodes = $this->promocodeRepo->getList($user);
		$paymentMethods = $this->paymentRepo->getPaymentMethodList();
		$employees = User::where('enable', true)
			->orderBy('lastname')
			->orderBy('name')
			->get();
		$pilots = User::where('enable', true);
		if ($user->city_id) {
			$pilots = $pilots->whereIn('city_id', [$user->city_id, 0]);
		}
		$pilots = $pilots->where('role', User::ROLE_PILOT)
			->orderBy('lastname')
			->orderBy('name')
			->get();

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
			'employees' => $employees,
			'pilots' => $pilots,
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
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

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
			'user' => $user,
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
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$deal = $this->dealRepo->getById($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$statuses = $this->statusRepo->getList(Status::STATUS_TYPE_DEAL);
		$cities = $this->cityRepo->getList($user);
		
		$VIEW = view('admin.deal.modal.edit', [
			'deal' => $deal,
			'statuses' => $statuses,
			'cities' => $cities,
			'user' => $user,
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
		
		$user = \Auth::user();
		
		if ($user) {
			if (!$user->isAdminOrHigher()) {
				return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
			}
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
				'name' => ['required'],
				'email' => ['required', 'email'],
				'phone' => ['required', 'valid_phone'],
				'product_id' => ['required', 'numeric', 'min:0', 'not_in:0'],
			];
			if ($user && $user->isAdminOBOrHigher()) {
				$rules['bill_location_id'] = ['required', 'numeric', 'min:0', 'not_in:0'];
			}
			
			$attrNames = [
				'name' => 'Имя',
				'email' => 'E-mail',
				'phone' => 'Телефон',
				'product_id' => 'Продукт',
			];
			if ($user && $user->isAdminOBOrHigher()) {
				$attrNames['bill_location_id'] = 'Локация счета';
			}
			$validator = Validator::make($this->request->all(), $rules)
				->setAttributeNames($attrNames);
			if (!$validator->passes()) {
				return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
			}
		}
		
		$cityId = $this->request->city_id;
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
		$transactionType = $this->request->transaction_type ?? null;
		if ($transactionType == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER) {
			$bonusAmount = ($this->request->aeroflot_bonus_amount && $this->request->aeroflot_bonus_amount != 'NaN') ? $this->request->aeroflot_bonus_amount : 0;
		} elseif ($transactionType == AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS) {
			$bonusAmount = floor($amount / AeroflotBonusService::ACCRUAL_MILES_RATE);
		}
		$contractorId = $this->request->contractor_id ?? 0;
		$name = $this->request->name ?? '';
		$email = $this->request->email ?? '';
		$phone = $this->request->phone ?? '';
		$source = $this->request->source ?? '';
		$isUnified = $this->request->is_unified ?? 0;
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		$roistatVisit = ($source == Deal::WEB_SOURCE) ? (array_key_exists('roistat_visit', $_COOKIE) ? $_COOKIE['roistat_visit'] : null) : ($this->request->roistat_visit ?? null);
		$isPaid = (bool)$this->request->is_paid;
		
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
					$minLimit = floor($amount / 100 * 10);
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
			$contractor = Contractor::whereRaw('LOWER(email) = (?)', [mb_strtolower($contractorEmail)])
				->first();
			if ($contractor && !in_array($source, [Contractor::WEB_SOURCE, Contractor::MOB_SOURCE])) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент с таким E-mail уже существует']);
			}
		}
		
		try {
			\DB::beginTransaction();

			if (!$contractor) {
				$contractor = new Contractor();
				$contractor->name = $name;
				$contractor->email = $email;
				$contractor->phone = $phone;
				$contractor->city_id = $cityId ?: $this->request->user()->city_id;
				$contractor->source = $source ?: Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user()->id ?? 0;
				$contractor->save();
			}
			
			$roistatDeal = Deal::where('contractor_id', $contractor->id)
				->whereNotNull('roistat')
				->where('roistat', '!=', 'unknown')
				->where('email', '!=', Contractor::ANONYM_EMAIL)
				->first();
			
			$certificate = new Certificate();
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			$certificate->status_id = $certificateStatus->id ?? 0;
			$certificate->city_id = ($isUnified || !$cityId) ? 0 : $cityId;
			$certificate->product_id = $product->id ?? 0;
			$certificatePeriod = ($product && $product->validity) ? $product->validity : '';
			$certificate->expire_at = $certificatePeriod ? Carbon::parse($certificateExpireAt)->addMonths($certificatePeriod)->format('Y-m-d H:i:s') : null;
			$certificate->save();
			
			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus->id ?? 0;
			$deal->contractor_id = $contractor->id ?? 0;
			$deal->city_id = $cityId ?: $this->request->user()->city_id;
			$deal->name = $name;
			$deal->phone = $phone;
			$deal->email = $email;
			$deal->source = $source ?: Deal::ADMIN_SOURCE;
			$deal->user_id = $this->request->user()->id ?? 0;
			$deal->roistat = $roistatVisit ?: ($roistatDeal ? $roistatDeal->roistat : null);
			$deal->bill_location_id = ($user && $user->isAdminOBOrHigher()) ? ($this->request->bill_location_id ?? 0) : 0;
			$deal->save();
			
			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->certificate_id = $certificate->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = /*$isUnified ? 0 : */$cityId ?: $this->request->user()->city_id;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = ($promocodeId || $promocodeUuid) ? $promocode->id : 0;
			$position->is_certificate_purchase = true;
			$position->source = $source ?: Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id ?? 0;
			$position->data_json = !empty($data) ? $data : null;
			$position->save();

			$deal->positions()->save($position);
			
			if ($amount) {
				$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, Bill::ONLINE_PAYMENT_METHOD);
				$billStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
				$billPayedStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::PAYED_STATUS);
				
				if ($this->request->user()) {
					if ($this->request->user()->city && $this->request->user()->city->version == City::EN_VERSION) {
						$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
					}
					else {
						$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
					}
				} else {
					$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
				}
				
				if ($source == Deal::WEB_SOURCE) {
					$billLocation = $city->getLocationForBill($product);
					if (!$billLocation) {
						\DB::rollback();
						
						Log::debug('500 - Certificate Deal Create: Не найден номер счета платежной системы');
						
						return response()->json(['status' => 'error', 'reason' => 'Не найден номер счета платежной системы!']);
					}
					$billLocationId = $billLocation->id;
				} else {
					$billLocationId = ($user && $user->isAdminOBOrHigher()) ? ($this->request->bill_location_id ?? 0) : ($this->request->user()->location_id ?? 0);
				}
				
				$bill = new Bill();
				$bill->contractor_id = $contractor->id ?? 0;
				$bill->deal_id = $deal->id ?? 0;
				//$bill->deal_position_id = $position->id ?? 0;
				$bill->location_id = $billLocationId;
				$bill->payment_method_id = ($source == Deal::WEB_SOURCE) ? $onlinePaymentMethod->id : ($paymentMethodId ?? 0);
				$bill->status_id = ($isPaid && $paymentMethodId != $onlinePaymentMethod->id) ? $billPayedStatus->id : $billStatus->id;
				$bill->payed_at = ($isPaid && $paymentMethodId != $onlinePaymentMethod->id) ? Carbon::now()->format('Y-m-d H:i:s') : null;
				$bill->amount = $amount;
				$bill->currency_id = $currency->id ?? 0;
				$bill->aeroflot_transaction_type = $transactionType;
				$bill->aeroflot_card_number = $cardNumber;
				$bill->aeroflot_bonus_amount = $bonusAmount ?? 0;
				$bill->user_id = $this->request->user()->id ?? 0;
				$bill->save();
				
				$deal->bills()->save($bill);
				if ($position) {
					$bill->positions()->sync((array)$position->id);
				}
			}

			if ($transactionType) {
				switch ($transactionType) {
					case AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER:
						$registerOrderResult = AeroflotBonusService::registerOrder($bill);
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
		
		if (in_array($source, [Deal::WEB_SOURCE])) {
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
		
		$user = \Auth::user();
		
		if ($user) {
			if (!$user->isAdminOrHigher()) {
				return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
			}
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
					$rules = [
						'name' => ['required'],
						'email' => ['required', 'email'],
						'phone' => ['required'],
						'product_id' => ['required', 'numeric', 'min:0', 'not_in:0'],
						'location_id' => ['required', 'numeric', 'min:0', 'not_in:0'],
						'flight_date_at' => ['required', 'date'],
						'flight_time_at' => ['required'],
					];
					if ($user && $user->isAdminOBOrHigher()) {
						$rules['bill_location_id'] = ['required', 'numeric', 'min:0', 'not_in:0'];
					}
					
					$attrNames = [
						'name' => 'Имя',
						'email' => 'E-mail',
						'phone' => 'Телефон',
						'product_id' => 'Продукт',
						'location_id' => 'Локация',
						'flight_date_at' => 'Дата начала',
						'flight_time_at' => 'Время начала',
					];
					if ($user && $user->isAdminOBOrHigher()) {
						$attrNames['bill_location_id'] = 'Локация счета';
					}
					$validator = Validator::make($this->request->all(), $rules)
						->setAttributeNames($attrNames);
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
					$rules = [
						'location_id' => 'required|numeric|min:0|not_in:0',
						'flight_date_at' => 'required|date',
						'flight_time_at' => 'required',
						'flight_date_stop_at' => 'required|date',
						'flight_time_stop_at' => 'required',
					];
					
					$validator = Validator::make($this->request->all(), $rules)
						->setAttributeNames([
							'location_id' => 'Локация',
							'flight_date_at' => 'Дата начала',
							'flight_time_at' => 'Время начала',
							'flight_date_stop_at' => 'Дата окончания',
							'flight_time_stop_at' => 'Время окончания',
						]);
				break;
				case Event::EVENT_TYPE_USER_FLIGHT:
					$rules = [
						'location_id' => 'required|numeric|min:0|not_in:0',
						'flight_date_at' => 'required|date',
						'flight_time_at' => 'required',
						'flight_date_stop_at' => 'required|date',
						'flight_time_stop_at' => 'required',
						'employee_id' => 'required|numeric|min:0|not_in:0',
					];
					
					$validator = Validator::make($this->request->all(), $rules)
						->setAttributeNames([
							'location_id' => 'Локация',
							'flight_date_at' => 'Дата начала',
							'flight_time_at' => 'Время начала',
							'flight_date_stop_at' => 'Дата окончания',
							'flight_time_stop_at' => 'Время окончания',
							'employee_id' => 'Сотрудник',
						]);
				break;
				case Event::EVENT_TYPE_TEST_FLIGHT:
					$rules = [
						'location_id' => 'required|numeric|min:0|not_in:0',
						'flight_date_at' => 'required|date',
						'flight_time_at' => 'required',
						'flight_date_stop_at' => 'required|date',
						'flight_time_stop_at' => 'required',
						'pilot_id' => 'required|numeric|min:0|not_in:0',
					];
					
					$validator = Validator::make($this->request->all(), $rules)
						->setAttributeNames([
							'location_id' => 'Локация',
							'flight_date_at' => 'Дата начала',
							'flight_time_at' => 'Время начала',
							'flight_date_stop_at' => 'Дата окончания',
							'flight_time_stop_at' => 'Время окончания',
							'pilot_id' => 'Пилот',
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
		$flightAt = str_replace('/', '.', $flightAt);
		$flightAt = str_replace(',', '', $flightAt);
		$flightAt = str_replace(' AM', '', $flightAt);
		$flightAt = str_replace(' PM', '', $flightAt);
		$flightStopAt = ($this->request->flight_date_stop_at ?? '') . ' ' . ($this->request->flight_time_stop_at ?? '');
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->flight_simulator_id ?? 0;
		$certificateNumber = $this->request->certificate ?? '';
		$certificateUuid = $this->request->certificate_uuid ?? '';
		$isIndefinitely = $this->request->is_indefinitely ?? 0;
		$eventType = $this->request->event_type ?? '';
		$extraTime = (int)$this->request->extra_time ?? 0;
		$isRepeatedFlight = (bool)$this->request->is_repeated_flight ?? false;
		$isUnexpectedFlight = (bool)$this->request->is_unexpected_flight ?? false;
		/*$duration = $this->request->duration ?? 0;
		$isValidFlightDate = $this->request->is_valid_flight_date ?? 0;*/
		$employeeId = $this->request->employee_id ?? 0;
		$pilotId = $this->request->pilot_id ?? 0;
		$roistatVisit = ($source == Deal::WEB_SOURCE) ? (array_key_exists('roistat_visit', $_COOKIE) ? $_COOKIE['roistat_visit'] : null) : ($this->request->roistat_visit ?? null);
		$isPaid = (bool)$this->request->is_paid;
		
		/*if (!in_array($source, [Deal::WEB_SOURCE, Deal::MOB_SOURCE]) && in_array($eventType, Event::EVENT_TYPE_DEAL) && !$isValidFlightDate) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректная дата и время начала полета']);
		}*/
		
		if (in_array($eventType, [Event::EVENT_TYPE_DEAL])) {
			$product = Product::find($productId);
			if (!$product) {
				return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
			}
			if ($source != Deal::WEB_SOURCE && !$product->validateFlightDate($flightAt)) {
				return response()->json(['status' => 'error', 'reason' => 'Для бронирования полета по тарифу Regular доступны только будние дни']);
			}
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
		
		if (in_array($eventType, [Event::EVENT_TYPE_DEAL])) {
			$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($cityId);
			if (!$cityProduct) {
				return response()->json(['status' => 'error', 'reason' => 'Продукт в данном городе не найден']);
			}
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
		
		$certificateId = $certificateProductAmount = 0;
		if ($certificateNumber || $certificateUuid) {
			$date = date('Y-m-d');
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			
			// проверка сертификата на валидность
			if ($certificateNumber) {
				$certificate = Certificate::whereIn('city_id', [$cityId, 0])
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
			if (in_array($source, [Deal::WEB_SOURCE, Deal::MOB_SOURCE]) && !in_array($certificate->status_id, [$certificateStatus->id, 0])) {
				return response()->json(['status' => 'error', 'reason' => 'Некорректный статус Сертификата']);
			}
			if (!in_array($certificate->product_id, [$product->id, 0]) && in_array($source, [Deal::WEB_SOURCE, Deal::MOB_SOURCE])) {
				return response()->json(['status' => 'error', 'reason' => 'Продукт по Сертификату не совпадает с выбранным']);
			}
			if ($certificate->expire_at && Carbon::parse($certificate->expire_at)->lt($date) && !$isIndefinitely) {
				return response()->json(['status' => 'error', 'reason' => 'Срок действия Сертификата истек']);
			}
			$certificateId = $certificate->id;
			$certificateProduct = $certificate->product;
			if ($certificateProduct && $certificateProduct->alias != $product->alias) {
				$certificateCityProduct = $certificateProduct->cities()->where('cities_products.is_active', true)->find($cityId);
				if ($certificateCityProduct && $certificateCityProduct->pivot) {
					$certificateProductAmount = $certificateCityProduct->pivot->price;
				}
			}
		}
		
		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
			if (!$contractor) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
			}
		} elseif ($email) {
			$contractor = Contractor::whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
				->first();
			if ($contractor && !in_array($source, [Contractor::WEB_SOURCE, Contractor::MOB_SOURCE])) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент с таким E-mail уже существует']);
			}
		}
		
		if (in_array($source, [Deal::ADMIN_SOURCE, Deal::CALENDAR_SOURCE]) || !$source) {
			// проверяем наличие блокировки другого пользователя
			$lockingPeriod = HelpFunctions::getLockingPeriod($this->request->user()->id, $location->id, Carbon::parse($flightAt)->format('Y-m-d H:i'), Carbon::parse($flightAt)->addMinutes($product->duration ?? 0)->addMinutes($extraTime)->format('Y-m-d H:i'));
			if ($lockingPeriod) {
				return response()->json(['status' => 'error', 'reason' => 'Выбранный период пересекается с зарезервированным ранее периодом пользователя ' . ($lockingPeriod->user ? $lockingPeriod->user->fioFormatted() : '-')]);
			}
		}
		
		$data = [];
		if ($comment) {
			$data['comment'] = $comment;
		}
		
		try {
			\DB::beginTransaction();

			switch ($eventType) {
				case Event::EVENT_TYPE_DEAL:
					if (!$contractor) {
						$contractor = new Contractor();
						$contractor->name = $name;
						$contractor->email = $email;
						$contractor->phone = $phone;
						$contractor->city_id = $cityId ?: $this->request->user()->city_id;
						$contractor->source = $source ?: Contractor::ADMIN_SOURCE;
						$contractor->user_id = $this->request->user()->id ?? 0;
						$contractor->save();
					}
					
					$roistatDeal = Deal::where('contractor_id', $contractor->id)
						->whereNotNull('roistat')
						->where('roistat', '!=', 'unknown')
						->where('email', '!=', Contractor::ANONYM_EMAIL)
						->first();
					
					$deal = new Deal();
					$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
					$deal->status_id = $dealStatus->id ?? 0;
					$deal->contractor_id = $contractor->id ?? 0;
					$deal->city_id = $cityId ?: $this->request->user()->city_id;
					$deal->name = $name;
					$deal->phone = $phone;
					$deal->email = $email;
					$deal->source = $source ?: Deal::ADMIN_SOURCE;
					$deal->user_id = $this->request->user()->id ?? 0;
					$deal->roistat = $roistatVisit ?: ($roistatDeal ? $roistatDeal->roistat : null);
					$deal->bill_location_id = ($user && $user->isAdminOBOrHigher()) ? ($this->request->bill_location_id ?? 0) : 0;
					$deal->save();
					
					$position = new DealPosition();
					$position->product_id = $product->id ?? 0;
					$position->certificate_id = $certificateId;
					$position->duration = $product->duration ?? 0;
					$position->amount = $amount;
					$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
					$position->city_id = $cityId ?: $this->request->user()->city_id;
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
						$billPayedStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::PAYED_STATUS);
						
						if ($city->version == City::EN_VERSION) {
							$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
						} else {
							$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
						}
						
						if ($source == Deal::WEB_SOURCE) {
							$billLocationId = $location->id ?? 0;
						} else {
							$billLocationId = ($user && $user->isAdminOBOrHigher()) ? ($this->request->bill_location_id ?? 0) : ($this->request->user()->location_id ?? 0);
						}
						
						$bill = new Bill();
						$bill->contractor_id = $contractor->id ?? 0;
						$bill->deal_id = $deal->id ?? 0;
						//$bill->deal_position_id = $position->id ?? 0;
						$bill->location_id = $billLocationId;
						$bill->payment_method_id = ($source == Deal::WEB_SOURCE) ? 0 : ($paymentMethodId ?? 0);
						$bill->status_id = ($isPaid && $paymentMethodId != $onlinePaymentMethod->id) ? $billPayedStatus->id : $billStatus->id;
						$bill->payed_at = ($isPaid && $paymentMethodId != $onlinePaymentMethod->id) ? Carbon::now()->format('Y-m-d H:i:s') : null;
						$bill->amount = $amount;
						$bill->currency_id = $currency->id ?? 0;
						$bill->user_id = $this->request->user()->id ?? 0;
						$bill->save();
						
						$deal->bills()->save($bill);
						if ($position) {
							$bill->positions()->sync((array)$position->id);
						}
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
						if ($email && $email != $contractor->email && $email != Contractor::ANONYM_EMAIL) {
							$contractor = new Contractor();
							$contractor->name = $name;
							$contractor->email = $email;
							$contractor->phone = $phone;
							$contractor->city_id = $cityId ?: $this->request->user()->city_id;
							$contractor->source = $source ?: Contractor::ADMIN_SOURCE;
							$contractor->user_id = $this->request->user()->id ?? 0;
							$contractor->save();
						}
						
						$event = new Event();
						$event->event_type = $eventType;
						$event->contractor_id = $contractor->id ?? 0;
						$event->deal_id = $deal->id ?? 0;
						$event->deal_position_id = $position->id ?? 0;
						$event->city_id = $cityId ?: $this->request->user()->city_id;
						$event->location_id = $location->id ?? 0;
						$event->flight_simulator_id = $simulator->id ?? 0;
						$event->start_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
						$event->stop_at = Carbon::parse($flightAt)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
						$event->extra_time = $extraTime;
						$event->is_repeated_flight = $isRepeatedFlight;
						$event->is_unexpected_flight = $isUnexpectedFlight;
						$event->nominal_price = $event->nominalPrice();
						$event->save();
						
						$position->event()->save($event);
					}

					if ($promocodeId || $promocodeUuid) {
						$promocode->contractors()->save($contractor);
					}
					
					if (in_array($source, [Deal::ADMIN_SOURCE, Deal::CALENDAR_SOURCE]) || !$source) {
						// снимаем блокировку с периода
						$lockingPeriod = HelpFunctions::getLockingPeriod($this->request->user()->id, $location->id, Carbon::parse($flightAt)->format('Y-m-d H:i'), Carbon::parse($flightAt)->addMinutes($product->duration ?? 0)->addMinutes($extraTime)->format('Y-m-d H:i'), true);
						if ($lockingPeriod) {
							$lockingPeriod->delete();
						}
					}
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
				case Event::EVENT_TYPE_TEST_FLIGHT:
				case Event::EVENT_TYPE_USER_FLIGHT:
					$event = new Event();
					$event->event_type = $eventType;
					$event->city_id = $cityId ?: $this->request->user()->city_id;
					$event->location_id = $location->id ?? 0;
					$event->flight_simulator_id = $simulator->id ?? 0;
					$event->user_id = $this->request->user()->id ?? 0;
					$event->employee_id = $employeeId;
					$event->test_pilot_id = $pilotId;
					$event->start_at = Carbon::parse($flightAt)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($flightStopAt)->format('Y-m-d H:i');
					$event->save();
				break;
			}
			
			if (in_array($source,[Deal::WEB_SOURCE, Deal::MOB_SOURCE])) {
				$job = new \App\Jobs\SendDealEmail($deal);
				$job->handle();
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
		
		$user = \Auth::user();
		
		if ($user) {
			if (!$user->isAdminOrHigher()) {
				return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
			}
		}
		
		$rules = [
			'name' => ['required'],
			'email' => ['required', 'email'],
			'phone' => ['required'],
			'product_id' => ['required', 'numeric', 'min:0', 'not_in:0'],
			'city_id' => ['required', 'numeric', 'min:0', 'not_in:0'],
		];
		if ($user && $user->isAdminOBOrHigher()) {
			$rules['bill_location_id'] = ['required', 'numeric', 'min:0', 'not_in:0'];
		}

		$attrNames = [
			'name' => 'Имя',
			'email' => 'E-mail',
			'phone' => 'Телефон',
			'product_id' => 'Продукт',
			'city_id' => 'Город',
		];
		if ($user && $user->isAdminOBOrHigher()) {
			$attrNames['bill_location_id'] = 'Локация счета';
		}
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames($attrNames);
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
		$roistatVisit = ($source == Deal::WEB_SOURCE) ? (array_key_exists('roistat_visit', $_COOKIE) ? $_COOKIE['roistat_visit'] : null) : ($this->request->roistat_visit ?? null);
		$isPaid = (bool)$this->request->is_paid;
		
		$city = $this->cityRepo->getById($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
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
		
		if ($paymentMethodId) {
			$paymentMethod = PaymentMethod::where('is_active', true)
				->find($paymentMethodId);
			if (!$paymentMethod) {
				return response()->json(['status' => 'error', 'reason' => 'Способ оплаты не найден']);
			}
		}
		
		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
			if (!$contractor) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
			}
		} elseif ($email) {
			$contractor = Contractor::whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
				->first();
			if ($contractor && !in_array($source, [Contractor::WEB_SOURCE, Contractor::MOB_SOURCE])) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент с таким E-mail уже существует']);
			}
		}
		
		$data = [];
		if ($comment) {
			$data['comment'] = $comment;
		}
		
		try {
			\DB::beginTransaction();
			
			if (!$contractor) {
				$contractor = new Contractor();
				$contractor->name = $name;
				$contractor->email = $email;
				$contractor->phone = $phone;
				$contractor->city_id = $cityId ?: $this->request->user()->city_id;
				$contractor->source = $source ?: Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user()->id ?? 0;
				$contractor->save();
			}
			
			$roistatDeal = Deal::where('contractor_id', $contractor->id)
				->whereNotNull('roistat')
				->where('roistat', '!=', 'unknown')
				->where('email', '!=', Contractor::ANONYM_EMAIL)
				->first();
			
			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus->id ?? 0;
			$deal->contractor_id = $contractor->id ?? 0;
			$deal->city_id = $cityId ?: $this->request->user()->city_id;
			$deal->name = $name;
			$deal->phone = $phone;
			$deal->email = $email;
			$deal->user_id = $this->request->user()->id ?? 0;
			$deal->source = $source ?: Deal::ADMIN_SOURCE;
			$deal->roistat = $roistatVisit ?: ($roistatDeal ? $roistatDeal->roistat : null);
			$deal->bill_location_id = ($user && $user->isAdminOBOrHigher()) ? ($this->request->bill_location_id ?? 0) : 0;
			$deal->save();

			$position = new DealPosition();
			$position->product_id = $product->id;
			$position->amount = $amount;
			$position->currency_id = $cityProduct->pivot->currency_id ?? 0;
			$position->city_id = $cityId ?: $this->request->user()->city_id;
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
				$billPayedStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::PAYED_STATUS);
				
				if ($city->version == City::EN_VERSION) {
					$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
				} else {
					$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
				}
				
				if ($source == Deal::WEB_SOURCE) {
					$billLocationId = 0;
				} else {
					$billLocationId = ($user && $user->isAdminOBOrHigher()) ? ($this->request->bill_location_id ?? 0) : ($this->request->user()->location_id ?? 0);
				}
				
				$bill = new Bill();
				$bill->contractor_id = $contractor->id ?? 0;
				$bill->deal_id = $deal->id ?? 0;
				//$bill->deal_position_id = $position->id ?? 0;
				$bill->location_id = $billLocationId;
				$bill->payment_method_id = ($source == Deal::WEB_SOURCE) ? 0 : ($paymentMethodId ?? 0);
				$bill->status_id = ($isPaid && $paymentMethodId != $onlinePaymentMethod->id) ? $billPayedStatus->id : $billStatus->id;
				$bill->payed_at = ($isPaid && $paymentMethodId != $onlinePaymentMethod->id) ? Carbon::now()->format('Y-m-d H:i:s') : null;
				$bill->amount = $amount;
				$bill->currency_id = $currency->id ?? 0;
				$bill->user_id = $this->request->user()->id ?? 0;
				$bill->save();
				
				$deal->bills()->save($bill);
				if ($position) {
					$bill->positions()->sync((array)$position->id);
				}
				
				if ($isPaid && $deal->balance() >= 0) {
					$city->products()->updateExistingPivot($product->id, [
						'availability' =>  --$cityProduct->pivot->availability,
					]);
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
	public function update($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if ($user) {
			if (!$user->isAdminOrHigher()) {
				return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
			}
		}
		
		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$dealStatus = $deal->status;
		if (!$dealStatus) {
			return response()->json(['status' => 'error', 'reason' => 'Статус сделки не найден']);
		}
		
		if (in_array($dealStatus->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$rules = [
			'name' => ['required'],
			'email' => ['required', 'email'],
			'phone' => ['required'],
			'status_id' => ['required', 'numeric', 'min:0', 'not_in:0'],
		];
		if ($user && $user->isAdminOBOrHigher()) {
			$rules['bill_location_id'] = ['required', 'numeric', 'min:0', 'not_in:0'];
		}
		
		$attrNames = [
			'name' => 'Имя',
			'email' => 'E-mail',
			'phone' => 'Телефон',
			'status_id' => 'Статус',
		];
		if ($user && $user->isAdminOBOrHigher()) {
			$attrNames['bill_location_id'] = 'Локация счета';
		}
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames($attrNames);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$contractorId = $this->request->contractor_id ?? 0;
		$statusId = $this->request->status_id ?? 0;
		$name = $this->request->name ?? '';
		$email = $this->request->email ?? '';
		$phone = $this->request->phone ?? '';
		$roistatVisit = $this->request->roistat_visit ?? null;
		
		$status = Status::find($statusId);
		if (!$status) {
			return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		}
		
		try {
			\DB::beginTransaction();
			
			if (in_array($status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
				// при отмене или возврате сделки со списанными баллами начисляем баллы обратно
				$scores = Score::where('deal_id', $deal->id)
					->where('type', Score::USED_TYPE)
					->get();
				foreach ($scores as $item) {
					$score = new Score();
					$score->score = abs($item->score);
					$score->type = Score::SCORING_TYPE;
					$score->contractor_id = $item->contractor_id;
					$score->deal_id = $item->deal_id;
					$score->deal_position_id = $item->deal_position_id;
					$score->user_id = $this->request->user()->id;
					$score->save();
				}
				
				// при отмене сделки пересчет наличия для сувениров
				foreach ($deal->positions as $position) {
					$product = $position ? $position->product : null;
					$productType = $product ? $product->productType : null;
					if ($productType->alias != ProductType::SERVICES_ALIAS) continue;
					
					$city = $product ? $position->city : null;
					$cityProduct = ($product && $city) ? $product->cities()->where('cities_products.is_active', true)->find($city->id) : null;
					if (!$cityProduct || !$cityProduct->pivot) continue;
					
					$city->products()->updateExistingPivot($product->id, [
						'availability' => ++$cityProduct->pivot->availability,
					]);
				}
			}

			$deal->status_id = $statusId;
			$deal->name = $name;
			$deal->email = $email;
			$deal->phone = $phone;
			if ($contractorId) {
				$deal->contractor_id = $contractorId;
			}
			$deal->roistat = $roistatVisit;
			$deal->bill_location_id = ($user && $user->isAdminOBOrHigher()) ? ($this->request->bill_location_id ?? 0) : 0;
			$deal->save();
			
			if ($contractorId) {
				$bills = $deal->bills;
				foreach ($bills as $bill) {
					$bill->contractor_id = $contractorId;
					$bill->save();
				}
				$events = $deal->events;
				foreach ($events as $event) {
					$event->contractor_id = $contractorId;
					$event->save();
				}
			}
			
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
		$certificateUuid = $this->request->certificate_uuid ?? '';
		$source = $this->request->source ?? 'admin';
		$flightDate = $this->request->flight_date ?? '';
		$isFree = $this->request->is_free ?? 0;
		$isUnified = $this->request->is_unified ?? 0;
		$score = $this->request->score ?? 0;
		$isCertificatePurchase = $this->request->is_certificate_purchase ?? 0;
		
		if ($isUnified) {
			$cityId = 1;
		} elseif ($this->request->city_id) {
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

		if ($flightDate) {
			$flightDate = str_replace('/', '.', $flightDate);
		}
		
		// Если дата - выходный день или праздник, меняем Regular на Ultimate
		if ($flightDate && (in_array(date('w', strtotime(Carbon::parse($flightDate)->format('d.m.Y'))), [0, 6]) || in_array(Carbon::parse($flightDate)->format('d.m.Y'), Deal::HOLIDAYS))) {
			$product = Product::where('alias', ProductType::ULTIMATE_ALIAS . '_' . $product->duration)
				->first();
			if (!$product) {
				return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
			}
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
		} elseif ($certificateUuid) {
			$certificate = HelpFunctions::getEntityByUuid(Certificate::class, $certificateUuid);
			$certificateId = $certificate ? $certificate->id : 0;
		}

		$amount = $product->calcAmount($contractorId, $cityId, $source, $isFree, $locationId, $paymentMethodId, $promoId, $promocodeId, $certificateId, $isUnified, false, $score, $isCertificatePurchase);
		
		return response()->json(['status' => 'success', 'amount' => $amount, 'baseAmount' => $baseAmount, 'certificateUuid' => $certificateUuid, 'certificateId' => $certificateId]);
	}
	
	/**
	 * @param $source
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function dealWebhook($source)
	{
		$name = $this->request->name ?? null;
		$phone = $this->request->phone ?? null;
		$email = $this->request->email ?? null;
		$visit = $this->request->visit ?? null;
		$id = $this->request->id ?? null;
		$title = $this->request->title ?? null;
		$text = $this->request->text ?? null;
		$data = $this->request->data ?? null;
		$createdDate = $this->request->created_date ?? null;
		$user = $this->request->user ?? null;
		$token = $this->request->token ?? null;
		$action = $this->request->action ?? null;
		
		if (!$visit) {
			return response()->json([
				'status' => 'error',
				'order_id' => null,
			]);
		}
		
		$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);

		$dataJson = [
			'id' => $id,
			'title' => $title,
			'text' => $text,
			'data' => $data,
			'createdDate' => $createdDate,
			'user' => $user,
			'token' => $token,
			'action' => $action,
		];
		
		$deal = new Deal();
		$deal->status_id = $dealStatus->id ?? 0;
		$deal->name = $name;
		$deal->phone = $phone;
		$deal->email = $email;
		$deal->source = $source ?? '';
		if ($source == Deal::ROISTAT_SOURCE) {
			$deal->roistat = $visit;
		}
		$deal->data_json = $dataJson;
		if (!$deal->save()) {
			return response()->json([
				'status' => 'error',
				'order_id' => null,
			]);
		}
		
		return response()->json([
			'status' => 'ok',
			'order_id' => $deal->id,
		]);
	}
	
	/**
	 * @param $source
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function dealExtendedWebhook($source)
	{
		$dealId = $this->request->leadId ?? 0;
		
		if (!$dealId) {
			return response()->json([
				'status' => 'error',
				'order_id' => null,
			]);
		}

		$deal = Deal::find($dealId);
		
		$number = $deal->number ?? null;
		$name = $deal->name ?? null;
		$phone = $deal->phone ?? null;
		$email = $deal->email ?? null;
		$title = $this->request->title ?? null;
		$text = $this->request->text ?? null;
		$action = $this->request->action ?? null;
		$user = $this->request->user ?? null;
		$token = $this->request->token ?? null;
		
		$dataJson = [
			'number' => $number,
			'title' => $title,
			'text' => $text,
			'user' => $user,
			'token' => $token,
			'action' => $action,
		];
		
		$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);

		$deal = new Deal();
		$deal->status_id = $dealStatus->id ?? 0;
		$deal->name = $name;
		$deal->phone = $phone;
		$deal->email = $email;
		$deal->source = $source ?? '';
		$deal->data_json = $dataJson;
		if (!$deal->save()) {
			return response()->json([
				'status' => 'error',
				'order_id' => null,
			]);
		}
		
		return response()->json([
			'status' => 'ok',
			'order_id' => $deal->id,
		]);
	}
}
