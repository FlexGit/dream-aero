<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Certificate;
use App\Models\Contractor;
use App\Models\DealPosition;
use App\Models\Discount;
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
use App\Services\HelpFunctions;
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

		$rules = [
			'name' => 'required|min:3|max:50',
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

		$product = Product::find($this->request->product_id);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if ($this->request->promo_id) {
			$promo = Promo::find($this->request->promo_id);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($this->request->promocode_id) {
			$promocode = Promocode::find($this->request->promocode_id);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}

		$data = [];
		if ($this->request->certificate_whom) {
			$data['certificate_whom'] = $this->request->certificate_whom;
		}
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			if ($this->request->contractor_id) {
				$contractor = Contractor::find($this->request->contractor_id);
				if (!$contractor) {
					return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
				}
			} else {
				$contractor = new Contractor();
				$contractor->name = $this->request->name ?? '';
				$contractor->email = $this->request->email ?? '';
				$contractor->phone = $this->request->phone ?? '';
				$contractor->city_id = $this->request->city_id ?? 0;
				$contractor->source = Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user()->id;
				$contractor->save();
			}

			$certificate = new Certificate();
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			$certificate->status_id = $certificateStatus ? $certificateStatus->id : 0;
			$certificate->city_id = $this->request->city_id ?? 0;
			$certificate->product_id = $product ? $product->id : 0;
			$certificate->expire_at = Carbon::parse($this->request->certificate_expire_at)->addYear()->format('Y-m-d H:i:s');
			$certificate->save();

			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus ? $dealStatus->id : 0;
			$deal->contractor_id = $contractor ? $contractor->id : $this->request->contractor_id;
			$deal->name = $this->request->name ?? '';
			$deal->phone = $this->request->phone ?? '';
			$deal->email = $this->request->email ?? '';
			$deal->source = Deal::ADMIN_SOURCE;
			$deal->user_id = $this->request->user()->id;
			$deal->data_json = $data;
			$deal->save();

			$position = new DealPosition();
			$position->product_id = $product ? $product->id : 0;
			$position->certificate_id = $certificate ? $certificate->id : 0;
			$position->duration = $product ? $product->duration : 0;
			$position->amount = $this->request->amount;
			$position->city_id = $this->request->city_id ?? 0;
			$position->promo_id = ($this->request->promo_id && $promo) ? $promo->id : 0;
			$position->promocode_id = ($this->request->promocode_id && $promocode) ? $promocode->id : 0;
			$position->is_certificate_purchase = 1;
			$position->source = Deal::ADMIN_SOURCE;
			$position->user_id = $this->request->user()->id;
			$position->data_json = $data;
			$position->save();

			$deal->positions()->save($position);

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Deal Certificate Store: ' . $e->getMessage());
			
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

		\Log::debug($this->request);
		
		switch ($this->request->event_type) {
			case Event::EVENT_TYPE_DEAL:
			case Event::EVENT_TYPE_TEST_FLIGHT:
				$rules = [
					'name' => 'required|min:3|max:50',
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

		if ($this->request->product_id) {
			$product = Product::find($this->request->product_id);
			if (!$product) {
				return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
			}

			if (!$product->validateFlightDate($this->request->flight_date_at . ' ' . $this->request->flight_time_at)) {
				return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
			}
		}

		$location = Location::find($this->request->location_id);
		if (!$location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}

		if (!$location->city) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не привязана к городу не найден']);
		}

		$simulator = FlightSimulator::find($this->request->flight_simulator_id);
		if (!$simulator) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
		}

		if ($this->request->promo_id) {
			$promo = Promo::find($this->request->promo_id);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($this->request->promocode_id) {
			$promocode = Promocode::find($this->request->promocode_id);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}

		if ($this->request->certificate) {
			$date = date('Y-m-d');
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			// проверка сертификата на валидность
			$certificate = Certificate::whereIn('city_id', [$location->city->id, 0])
				->where('status_id', $certificateStatus->id)
				->where('product_id', $product->id)
				->where(function ($query) use ($date) {
					$query->where('expire_at', '>=', $date)
						->orWhereNull('expire_at');
				})
				->where('number', $this->request->certificate)
				->first();
			if (!$certificate) {
				return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден или не соответствует выбранным параметрам']);
			}
		}

		$data = [];
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			switch ($this->request->event_type) {
				case Event::EVENT_TYPE_DEAL:
				case Event::EVENT_TYPE_TEST_FLIGHT:
					if ($this->request->contractor_id) {
						$contractor = Contractor::find($this->request->contractor_id);
						if (!$contractor) {
							return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
						}
					}
					else {
						$contractor = new Contractor();
						$contractor->name = $this->request->name ?? '';
						$contractor->email = $this->request->email ?? '';
						$contractor->phone = $this->request->phone ?? '';
						$contractor->city_id = ($location && $location->city) ? $location->city->id : 0;
						$contractor->source = Contractor::ADMIN_SOURCE;
						$contractor->user_id = $this->request->user()->id;
						$contractor->save();
					}
					
					$deal = new Deal();
					$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
					$deal->status_id = $dealStatus ? $dealStatus->id : 0;
					$deal->contractor_id = $contractor ? $contractor->id : $this->request->contractor_id;
					$deal->name = $this->request->name ?? '';
					$deal->phone = $this->request->phone ?? '';
					$deal->email = $this->request->email ?? '';
					$deal->source = Deal::ADMIN_SOURCE;
					$deal->user_id = $this->request->user()->id;
					$deal->data_json = $data;
					$deal->save();
					
					$position = new DealPosition();
					$position->product_id = $product ? $product->id : 0;
					$position->certificate_id = ($this->request->certificate && $certificate) ? $certificate->id : 0;
					$position->duration = $product ? $product->duration : 0;
					$position->amount = $this->request->amount;
					$position->city_id = ($location && $location->city) ? $location->city->id : 0;
					$position->location_id = $location ? $location->id : 0;
					$position->flight_simulator_id = $simulator ? $simulator->id : 0;
					$position->promo_id = ($this->request->promo_id && $promo) ? $promo->id : 0;
					$position->promocode_id = ($this->request->promocode_id && $promocode) ? $promocode->id : 0;
					$position->flight_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->format('Y-m-d H:i');
					$position->source = Deal::ADMIN_SOURCE;
					$position->user_id = $this->request->user()->id;
					$position->data_json = $data;
					$position->save();
					
					$deal->positions()->save($position);
					
					// если сделка на бронирование по сертификату, то регистрируем сертификат
					if ($this->request->certificate && $certificate) {
						$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::REGISTERED_STATUS);
						$certificate->status_id = $certificateStatus->id;
						$certificate->save();
					}
				
				// если сделка создается из календаря, создаем сразу и событие
				if ($this->request->source == 'calendar') {
					$event = new Event();
					$event->event_type = $this->request->event_type;
					$event->contractor_id = $contractor->id ?? 0;
					$event->deal_id = $deal->id ?? 0;
					$event->deal_position_id = $position->id ?? 0;
					$event->city_id = ($location && $location->city) ? $location->city->id : 0;
					$event->location_id = $location ? $location->id : 0;
					$event->flight_simulator_id = $simulator ? $simulator->id : 0;
					$event->start_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
					$event->extra_time = (int)$this->request->extra_time;
					$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
					$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
					$event->save();
					
					$position->event()->save($event);
				}
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
					$event = new Event();
					$event->event_type = $this->request->event_type;
					$event->city_id = ($location && $location->city) ? $location->city->id : 0;
					$event->location_id = $location ? $location->id : 0;
					$event->flight_simulator_id = $simulator ? $simulator->id : 0;
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
			'name' => 'required|min:3|max:50',
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

		$product = Product::find($this->request->product_id);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if ($this->request->promo_id) {
			$promo = Promo::find($this->request->promo_id);
			if (!$promo) {
				return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
			}
		}

		if ($this->request->promocode_id) {
			$promocode = Promocode::find($this->request->promocode_id);
			if (!$promocode) {
				return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
			}
		}

		$data = [];
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			if ($this->request->contractor_id) {
				$contractor = Contractor::find($this->request->contractor_id);
				if (!$contractor) {
					return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
				}
			} else {
				$contractor = new Contractor();
				$contractor->name = $this->request->name ?? '';
				$contractor->email = $this->request->email ?? '';
				$contractor->phone = $this->request->phone ?? '';
				$contractor->city_id = $this->request->city_id ?? 0;
				$contractor->source = Contractor::ADMIN_SOURCE;
				$contractor->user_id = $this->request->user()->id;
				$contractor->save();
			}

			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus ? $dealStatus->id : 0;
			$deal->contractor_id = $contractor ? $contractor->id : $this->request->contractor_id;
			$deal->name = $this->request->name ?? '';
			$deal->phone = $this->request->phone ?? '';
			$deal->email = $this->request->email ?? '';
			$deal->source = Deal::ADMIN_SOURCE;
			$deal->user_id = $this->request->user()->id;
			$deal->data_json = $data;
			$deal->save();

			$position = new DealPosition();
			$position->product_id = $product ? $product->id : 0;
			$position->amount = $this->request->amount;
			$position->city_id = $this->request->city_id ?? 0;
			$position->promo_id = ($this->request->promo_id && $promo) ? $promo->id : 0;
			$position->promocode_id = ($this->request->promocode_id && $promocode) ? $promocode->id : 0;
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
			'name' => 'required|min:3|max:50',
			'email' => 'required|email|unique_email',
			'phone' => 'required|valid_phone|unique_email',
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
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$certificateNumber = $this->request->certificate ?? '';

		if ($this->request->city_id) {
			$cityId = $this->request->city_id ?? 0;
		} elseif ($this->request->location_id) {
			$location = Location::find($locationId);
			$cityId = $location->city ? $location->city->id : 0;
		} else {
			$cityId = 0;
		}
		$isFree = $this->request->is_free ?? 0;
		
		if (!$productId) {
			return response()->json(['status' => 'success', 'amount' => 0]);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		$certificateId = 0;
		if ($certificateNumber) {
			$certificate = Certificate::where('number', $certificateNumber)
				->first();
			$certificateId = $certificate ? $certificate->id : 0;
		}

		$amount = $product->calcAmount($contractorId, $cityId, 'admin', $isFree, $locationId, $paymentMethodId, $promoId, $promocodeId, $certificateId);

		return response()->json(['status' => 'success', 'amount' => $amount]);
	}
}
