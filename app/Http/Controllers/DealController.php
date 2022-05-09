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
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$user = \Auth::user();
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
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
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->whereNotIn('alias', ['uae'])
			->get();

		$productTypes = ProductType::where('is_active', true)
			->whereNotIn('alias', ['services'])
			->orderBy('name')
			->get();

		$promos = Promo::where('is_active', true)
			->orderBy('name')
			->get();

		$promocodes = Promocode::where('is_active', true)
			->orderBy('number')
			->get();

		/*$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();*/

		/*$paymentMethods = PaymentMethod::where('is_active', true)
			->orderBy('name')
			->get();*/

		$VIEW = view('admin.deal.modal.certificate.add', [
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			/*'discounts' => $discounts,*/
			/*'paymentMethods' => $paymentMethods,*/
			'contractor' => $contractor ?? null,
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
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->whereNotIn('alias', ['uae'])
			->get();

		$productTypes = ProductType::where('is_active', true)
			->whereNotIn('alias', ['services'])
			->orderBy('name')
			->get();

		$promos = Promo::where('is_active', true);
		if(!$user->isSuperAdmin()) {
			$promos = $promos->whereIn('city_id', [$user->city_id, 0]);
		}
		$promos = $promos->orderBy('name')->get();

		$promocodes = Promocode::where('is_active', true);
		if(!$user->isSuperAdmin()) {
			$promocodes = $promocodes->whereHas('cities', function($query) use ($user) {
				$query->whereIn('cities.id', [$user->city_id, 0]);
			});
		}
		$promocodes = $promocodes->orderBy('number')->get();

		/*$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();*/

		/*$paymentMethods = PaymentMethod::where('is_active', true)
			->orderBy('name')
			->get();*/

		$VIEW = view('admin.deal.modal.booking.add', [
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			/*'discounts' => $discounts,*/
			/*'paymentMethods' => $paymentMethods,*/
			'contractor' => $contractor ?? null,
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
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->whereNotIn('alias', ['uae'])
			->get();

		$productTypes = ProductType::where('is_active', true)
			->whereIn('alias', ['services'])
			->orderBy('name')
			->get();

		$promos = Promo::where('is_active', true)
			->orderBy('name')
			->get();

		$promocodes = Promocode::where('is_active', true)
			->orderBy('number')
			->get();

		/*$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();*/

		$VIEW = view('admin.deal.modal.product.add', [
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			/*'discounts' => $discounts,*/
			'deal' => $deal ?? null,
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

		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$statuses = Status::where('type', Status::STATUS_TYPE_DEAL)
			->orderBy('sort')
			->get();
		
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
		
		$cityId = $this->request->city_id ?? 0;
		if (!$cityId) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		$city = City::find($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$productId = $this->request->product_id ?? 0;
		if (!$productId) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$cityProduct = $product->cities->find($city->id);
		
		$promoId = $this->request->promo_id ?? 0;
		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}
		
		$promocodeId = $this->request->promocode_id ?? 0;
		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		$promocodeUuid = $this->request->promocode_uuid ?? '';
		if ($promocodeUuid) {
			$promocode = HelpFunctions::getEntityByUuid(Promocode::class, $promocodeUuid);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		$amount = $this->request->amount ?? 0;
		
		// Аэрофлот Бонус
		$hasAeroflotCard = $this->request->has_aeroflot_card ?? 0;
		$cardNumber = $this->request->aeroflot_card_number ?? null;
		$bonusAmount = $this->request->aeroflot_bonus_amount ?? 0;
		$transactionType = $this->request->transaction_type ?? null;
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
		$certificateWhom = $this->request->certificate_whom ?? '';
		if ($certificateWhom) {
			$data['certificate_whom'] = $certificateWhom;
		}
		$certificateWhomPhone = $this->request->certificate_whom_phone ?? '';
		if ($certificateWhomPhone) {
			$data['certificate_whom_phone'] = $certificateWhomPhone;
		}
		$deliveryAddress = $this->request->delivery_address ?? '';
		if ($deliveryAddress) {
			$data['delivery_address'] = $deliveryAddress ?? '';
		}
		$comment = $this->request->comment ?? '';
		if ($comment) {
			$data['comment'] = $comment;
		}
		
		$contractorId = $this->request->contractor_id ?? 0;
		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
			if (!$contractor) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
			}
			//$contractorId = $contractor->id;
		} elseif ($contractorEmail = $this->request->email ?? '') {
			$contractor = Contractor::where('email', $contractorEmail)
				->first();
			/*if ($contractor) {
				$contractorId = $contractor->id;
			}*/
		}
		
		try {
			\DB::beginTransaction();

			if (!$contractor) {
				$contractor = new Contractor();
				$contractor->name = $this->request->name ?? '';
				$contractor->email = $this->request->email ?? '';
				$contractor->phone = $this->request->phone ?? '';
				$contractor->city_id = $city->id ?? 0;
				$contractor->source = $this->request->source ?? Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user() ? $this->request->user()->id : 0;
				$contractor->save();
				//$contractorId = $contractor->id ?? 0;
			}

			$certificate = new Certificate();
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			$certificate->status_id = $certificateStatus->id ?? 0;
			$certificate->city_id = $this->request->is_unified ? 0 : $city->id;
			$certificate->product_id = $product->id ?? 0;
			$certificatePeriod = ($product && array_key_exists('certificate_period', $product->data_json)) ? $product->data_json['certificate_period'] : 6;
			$certificate->expire_at = Carbon::parse($this->request->certificate_expire_at)->addMonths($certificatePeriod)->format('Y-m-d H:i:s');
			$certificate->save();
			
			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus->id ?? 0;
			$deal->contractor_id = $contractor->id ?? 0;
			$deal->name = $this->request->name ?? '';
			$deal->phone = $this->request->phone ?? '';
			$deal->email = $this->request->email ?? '';
			$deal->city_id = $city->id ?? 0;
			$deal->source = $this->request->source ?? Deal::ADMIN_SOURCE;
			$deal->user_id = $this->request->user() ? $this->request->user()->id : 0;
			$deal->data_json = $data;
			$deal->save();
			
			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->certificate_id = $certificate->id ?? 0;
			$position->duration = $product->duration ?? 0;
			$position->amount = $amount ?? 0;
			$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
			$position->city_id = $city->id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocode->id ?? 0;
			$position->is_certificate_purchase = true;
			$position->source = $this->request->source ?? Deal::ADMIN_SOURCE;
			$position->aeroflot_transaction_type = $transactionType ?? null;
			$position->aeroflot_card_number = $cardNumber ?? null;
			$position->aeroflot_bonus_amount = ($transactionType == AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER) ? $bonusAmount : 0;
			$position->user_id = $this->request->user() ? $this->request->user()->id : 0;
			$position->data_json = $data;
			$position->save();

			$deal->positions()->save($position);
			
			if ($this->request->source == Deal::WEB_SOURCE) {
				$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, Bill::ONLINE_PAYMENT_METHOD);
				$billStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
			}
			
			if ($city->version == City::EN_VERSION) {
				$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
			} else {
				$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
			}
			
			$location = $city->getLocationForBill();
			if (!$location) {
				\DB::rollback();
				
				Log::debug('500 - Certificate Deal Create: Не найден номер счета платежной системы');
				
				return response()->json(['status' => 'error', 'reason' => 'Не найден номер счета платежной системы!']);
			}
			
			$bill = new Bill();
			$bill->contractor_id = $contractor->id ?? 0;
			$bill->deal_position_id = $position->id ?? 0;
			$bill->location_id = $location->id ?? 0;
			$bill->location_id = $location->id ?? 0;
			$bill->payment_method_id =  $onlinePaymentMethod->id ?? $this->request->payment_method_id;
			$bill->status_id = $billStatus->id ?? 0;
			$bill->amount = $amount;
			$bill->currency_id = $currency->id ?? 0;
			$bill->user_id = $this->request->user() ? $this->request->user()->id : 0;
			$bill->save();
			
			$deal->bills()->save($bill);
			
			$bill = $bill->fresh();
			
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
			
			$promocode->contractors()->save($contractor);
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Deal Certificate Store: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		if ($this->request->source == Deal::WEB_SOURCE) {
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

		$productId = $this->request->product_id ?? 0;
		if (!$productId) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		if ($this->request->source != Deal::WEB_SOURCE && !$product->validateFlightDate($this->request->flight_date_at . ' ' . $this->request->flight_time_at)) {
			return response()->json(['status' => 'error', 'reason' => 'Для бронирования полета по тарифу Regular доступны только будние дни']);
		}

		$locationId = $this->request->location_id ?? 0;
		if (!$locationId) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}
		$location = Location::find($locationId);
		if (!$location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}
		if (!$location->city) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не привязана к городу']);
		}
		
		$cityId = $this->request->city_id ?: $location->city->id;
		if (!$cityId) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		$city = City::find($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$cityProduct = $product->cities->find($city->id);
		
		$simulatorId = $this->request->flight_simulator_id ?? 0;
		if (!$simulatorId) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
		}
		$simulator = FlightSimulator::find($simulatorId);
		if (!$simulator) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
		}

		$promoId = $this->request->promo_id ?? 0;
		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}
		
		$promocodeId = $this->request->promocode_id ?? 0;
		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		$promocodeUuid = $this->request->promocode_uuid ?? '';
		if ($promocodeUuid) {
			$promocode = HelpFunctions::getEntityByUuid(Promocode::class, $promocodeUuid);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}
		
		$certificateId = 0;
		$certificateNumber = $this->request->certificate ?? '';
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
			$certificateId = $certificate->id;
		}
		
		$contractorId = $this->request->contractor_id ?? 0;
		$contractorEmail = $this->request->email ?? '';
		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
			if (!$contractor) {
				return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
			}
		} elseif ($contractorEmail) {
			$contractor = Contractor::where('email', $contractorEmail)
				->first();
			/*if ($contractor) {
				$contractorId = $contractor->id;
			}*/
		}
		
		$amount = $this->request->amount ?? 0;
		if ($certificateId) {
			$amount = 0;
		}
		
		/*$amount = 0;
		if ($this->request->amount && $this->request->source == Deal::WEB_SOURCE) {
			$amount = $product->calcAmount(0, $city->id, $this->request->source, 0, $location->id, 0, 0, $promocodeId, $certificateId);
			if ($this->request->amount != $amount) {
				return response()->json(['status' => 'error', 'reason' => 'Некорректная стоимость']);
			}
		}*/
		
		$data = [];
		$comment = $this->request->comment ?? '';
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			switch ($this->request->event_type) {
				case Event::EVENT_TYPE_DEAL:
				case Event::EVENT_TYPE_TEST_FLIGHT:
					if (!$contractor) {
						$contractor = new Contractor();
						$contractor->name = $this->request->name ?? '';
						$contractor->email = $this->request->email ?? '';
						$contractor->phone = $this->request->phone ?? '';
						$contractor->city_id = $city->id ?? 0;
						$contractor->source = $this->request->source ?? Contractor::ADMIN_SOURCE;
						$contractor->user_id = $this->request->user() ? $this->request->user()->id : 0;
						$contractor->save();
						//$contractorId = $contractor->id ?? 0;
					}
					
					$deal = new Deal();
					$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
					$deal->status_id = $dealStatus->id ?? 0;
					$deal->contractor_id = $contractor->id ?? 0;
					$deal->name = $this->request->name ?? '';
					$deal->phone = $this->request->phone ?? '';
					$deal->email = $this->request->email ?? '';
					$deal->source = $this->request->source ?? Deal::ADMIN_SOURCE;
					$deal->user_id = $this->request->user() ? $this->request->user()->id : 0;
					$deal->data_json = $data;
					$deal->save();
				
					$position = new DealPosition();
					$position->product_id = $product->id ?? 0;
					$position->certificate_id = $certificateId ?? 0;
					$position->duration = $product->duration ?? 0;
					$position->amount = $amount;
					$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
					$position->city_id = $city->id ?? 0;
					$position->location_id = $location->id ?? 0;
					$position->flight_simulator_id = $simulator->id ?? 0;
					$position->promo_id = $promo->id ?? 0;
					$position->promocode_id = $promocode->id ?? 0;
					$position->flight_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->format('Y-m-d H:i');
					$position->source = $this->request->source ?? Deal::ADMIN_SOURCE;
					$position->user_id = $this->request->user() ? $this->request->user()->id : 0;
					$position->data_json = $data;
					$position->save();
				
					$deal->positions()->save($position);
				
					if ($this->request->source == Deal::WEB_SOURCE && $amount) {
						$onlinePaymentMethod = HelpFunctions::getEntityByAlias(PaymentMethod::class, Bill::ONLINE_PAYMENT_METHOD);
						$billStatus = HelpFunctions::getEntityByAlias(Status::class, Bill::NOT_PAYED_STATUS);
						
						if ($city->version == City::EN_VERSION) {
							$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::USD_ALIAS);
						} else {
							$currency = HelpFunctions::getEntityByAlias(Currency::class, Currency::RUB_ALIAS);
						}
						
						$location = $city->getLocationForBill();
						if (!$location) {
							\DB::rollback();
							
							Log::debug('500 - Certificate Deal Create: Не найден номер счета платежной системы');
							
							return response()->json(['status' => 'error', 'reason' => 'Не найден номер счета платежной системы!']);
						}
						
						$bill = new Bill();
						$bill->contractor_id = $contractor->id ?? 0;
						$bill->deal_id = $deal->id ?? 0;
						$bill->deal_position_id = $position->id ?? 0;
						$bill->location_id = $location->id ?? 0;
						$bill->payment_method_id =  $onlinePaymentMethod->id ?: $this->request->payment_method_id;
						$bill->status_id = $billStatus->id ?? 0;
						$bill->amount = $amount;
						$bill->currency_id = $currency->id ?? 0;
						$bill->user_id = $this->request->user() ? $this->request->user()->id : 0;
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
					if ($this->request->source == 'calendar') {
						// создаем новую карточку контрагента, если E-mail из заявки не совпадает E-mail
						// из карточки клиента, и пишем уже этого клиента в событие
						if ($this->request->email && $this->request->email != $contractor->email) {
							$contractor = new Contractor();
							$contractor->name = $this->request->name ?? '';
							$contractor->email = $this->request->email ?? '';
							$contractor->phone = $this->request->phone ?? '';
							$contractor->city_id = $city->id ?? 0;
							$contractor->source = $this->request->source ?? Contractor::ADMIN_SOURCE;
							$contractor->user_id = $this->request->user() ? $this->request->user()->id : 0;
							$contractor->save();
						}
						
						$event = new Event();
						$event->event_type = $this->request->event_type;
						$event->contractor_id = $contractor->id ?? 0;
						$event->deal_id = $deal->id ?? 0;
						$event->deal_position_id = $position->id ?? 0;
						$event->city_id = $city->id ?? 0;
						$event->location_id = $location->id ?? 0;
						$event->flight_simulator_id = $simulator->id ?? 0;
						$event->start_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->format('Y-m-d H:i');
						$event->stop_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
						$event->extra_time = (int)$this->request->extra_time ?? 0;
						$event->is_repeated_flight = (bool)$this->request->is_repeated_flight ?? 0;
						$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight ?? 0;
						$event->save();
						
						$position->event()->save($event);
					}
				
					$promocode->contractors()->save($contractor);
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
					$event = new Event();
					$event->event_type = $this->request->event_type;
					$event->city_id = ($location && $location->city) ? $location->city->id : 0;
					$event->location_id = $location->id ?? 0;
					$event->flight_simulator_id = $simulator->id ?? 0;
					$event->start_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->addMinutes($this->request->duration ?? 0)->format('Y-m-d H:i');
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
		
		$cityId = $this->request->city_id ?? 0;
		if (!$cityId) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		$city = City::find($cityId);
		if (!$city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}
		
		$productId = $this->request->product_id ?? 0;
		if (!$productId) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$cityProduct = $product->cities->find($city->id);
		
		$promoId = $this->request->promo_id ?? 0;
		if ($promoId) {
			$promo = Promo::find($promoId);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		$promocodeId = $this->request->promocode_id ?? 0;
		if ($promocodeId) {
			$promocode = Promocode::find($promocodeId);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}

		$data = [];
		$comment = $this->request->comment ?? '';
		if ($comment) {
			$data['comment'] = $comment;
		}

		try {
			\DB::beginTransaction();

			$contractorId = $this->request->contractor_id ?? 0;
			if ($contractorId) {
				$contractor = Contractor::find($contractorId);
				if (!$contractor) {
					return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
				}
			} else {
				$contractor = new Contractor();
				$contractor->name = $this->request->name ?? '';
				$contractor->email = $this->request->email ?? '';
				$contractor->phone = $this->request->phone ?? '';
				$contractor->city_id = $city->id ?? 0;
				$contractor->source = Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user()->id;
				$contractor->save();
			}

			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus->id ?? 0;
			$deal->contractor_id = $contractor ? $contractor->id : $this->request->contractor_id;
			$deal->name = $this->request->name ?? '';
			$deal->phone = $this->request->phone ?? '';
			$deal->email = $this->request->email ?? '';
			$deal->source = Deal::ADMIN_SOURCE;
			$deal->user_id = $this->request->user()->id;
			$deal->data_json = $data;
			$deal->save();

			$position = new DealPosition();
			$position->product_id = $product->id ?? 0;
			$position->amount = $this->request->amount ?? 0;
			$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
			$position->city_id = $city_id ?? 0;
			$position->promo_id = $promo->id ?? 0;
			$position->promocode_id = $promocode->id ?? 0;
			$position->source = Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id;
			$position->data_json = $data;
			$position->save();

			$deal->positions()->save($position);

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
		
		$data = [];
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}
		
		try {
			\DB::beginTransaction();
			
			$deal->status_id = $this->request->status_id ?? 0;
			$deal->name = $this->request->name ?? '';
			$deal->phone = $this->request->phone ?? '';
			$deal->email = $this->request->email ?? '';
			$deal->data_json = $data;
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

		if ($this->request->city_id) {
			$cityId = $this->request->city_id ?? 0;
		} elseif ($this->request->location_id) {
			$location = Location::find($locationId);
			$cityId = $location->city ? $location->city->id : 0;
		} else {
			$cityId = 1;
		}
		$isFree = $this->request->is_free ?? 0;
		
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
		$baseAmount = $cityProduct->pivot->price;

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
