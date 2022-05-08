<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\DealPosition;
use App\Models\Discount;
use App\Models\FlightSimulator;
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
use Illuminate\Support\Facades\Storage;
use Validator;
use Throwable;

class PositionController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
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

		$deal = Deal::find($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
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

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$VIEW = view('admin.position.modal.certificate.add', [
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			'discounts' => $discounts,
			'deal' => $deal ?? null,
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

		$deal = Deal::find($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		$cities = City::orderBy('version', 'desc')
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

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$VIEW = view('admin.position.modal.booking.add', [
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			'discounts' => $discounts,
			'deal' => $deal ?? null,
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

		$deal = Deal::find($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		$cities = City::orderBy('version', 'desc')
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

		$VIEW = view('admin.position.modal.product.add', [
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
	public function editCertificate($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->whereNotIn('alias', ['uae'])
			->get();
		
		$productTypes = ProductType::where('is_active', true)
			->whereNotIn('alias', ['services'])
			->orderBy('name')
			->get();
		
		$promos = Promo::/*where('is_active', true)
			->*/orderBy('name')
			->get();

		$promocodes = Promocode::/*where('is_active', true)
			->*/orderBy('number')
			->get();

		/*$discounts = Discount::orderBy('is_fixed')
			->orderBy('value')
			->get();*/
		
		$VIEW = view('admin.position.modal.certificate.edit', [
			'position' => $position,
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			/*'discounts' => $discounts,*/
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

		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->whereNotIn('alias', ['uae'])
			->get();

		$productTypes = ProductType::where('is_active', true)
			->whereNotIn('alias', ['services'])
			->orderBy('name')
			->get();

		$promos = Promo::/*where('is_active', true)
			->*/orderBy('name')
			->get();

		$promocodes = Promocode::/*where('is_active', true)
			->*/orderBy('number')
			->get();

		/*$discounts = Discount::orderBy('is_fixed')
			->orderBy('value')
			->get();*/

		$VIEW = view('admin.position.modal.booking.edit', [
			'position' => $position,
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			/*'discounts' => $discounts,*/
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

		$position = DealPosition::find($id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->whereNotIn('alias', ['uae'])
			->get();

		$productTypes = ProductType::where('is_active', true)
			->whereIn('alias', ['services'])
			->orderBy('name')
			->get();

		$promos = Promo::/*where('is_active', true)
			->*/orderBy('name')
			->get();

		$promocodes = Promocode::/*where('is_active', true)
			->*/orderBy('number')
			->get();

		/*$discounts = Discount::orderBy('is_fixed')
			->orderBy('value')
			->get();*/

		$VIEW = view('admin.position.modal.product.edit', [
			'position' => $position,
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'promocodes' => $promocodes,
			/*'discounts' => $discounts,*/
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

		$deal = Deal::find($this->request->deal_id);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найден']);
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

		$cityProduct = $product->cities->find($this->request->city_id);

		$data = [];
		if ($this->request->certificate_whom) {
			$data['certificate_whom'] = $this->request->certificate_whom;
		}
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			$certificate = new Certificate();
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			$certificate->status_id = $certificateStatus ? $certificateStatus->id : 0;
			$certificate->city_id = $this->request->city_id ?? 0;
			$certificate->product_id = $product ? $product->id : 0;
			$certificatePeriod = ($product && array_key_exists('certificate_period', $product->data_json)) ? $product->data_json['certificate_period'] : 6;
			$certificate->expire_at = Carbon::parse($this->request->certificate_expire_at)->addMonths($certificatePeriod)->format('Y-m-d H:i:s');
			$certificate->save();
			
			$position = new DealPosition();
			$position->product_id = $product ? $product->id : 0;
			$position->certificate_id = $certificate ? $certificate->id : 0;
			$position->duration = $product ? $product->duration : 0;
			$position->amount = $this->request->amount;
			$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
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

		$deal = Deal::find($this->request->deal_id);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найден']);
		}

		$product = Product::find($this->request->product_id);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if (!$product->validateFlightDate($this->request->flight_date_at . ' ' . $this->request->flight_time_at)) {
			return response()->json(['status' => 'error', 'reason' => 'Для бронирования полета по тарифу Regular доступны только будние дни']);
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
			if (!$certificate->wasUsed()) {
				return response()->json(['status' => 'error', 'reason' => 'Сертификат уже был ранее использован']);
			}
		}

		if ($location && $location->city) {
			$cityProduct = $product->cities->find($location->city->id);
		}

		$data = [];
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			$position = new DealPosition();
			$position->product_id = $product ? $product->id : 0;
			$position->certificate_id = ($this->request->certificate && $certificate) ? $certificate->id : 0;
			$position->duration = $product ? $product->duration : 0;
			$position->amount = $this->request->amount;
			$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
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

		$deal = Deal::find($this->request->deal_id);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найден']);
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

		$cityProduct = $product->cities->find($this->request->city_id);

		$data = [];
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			$position = new DealPosition();
			$position->product_id = $product ? $product->id : 0;
			$position->amount = $this->request->amount;
			$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
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
		
		$productId = $this->request->product_id ?? 0;
		if ($productId) {
			$product = Product::find($productId);
		}

		if ($this->request->city_id) {
			$city = City::find($this->request->city_id);
			if (!$city) {
				return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
			}
		} else {
			$city = HelpFunctions::getEntityByAlias(City::class, City::MSK_ALIAS);
			if (!$city) {
				return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
			}
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

		$cityProduct = $product->cities->find($city->id);

		$data = [];
		if ($this->request->certificate_whom) {
			$data['certificate_whom'] = $this->request->certificate_whom;
		}
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}
		
		try {
			\DB::beginTransaction();

			$position->product_id = $product ? $product->id : 0;
			$position->duration = $product ? $product->duration : 0;
			$position->amount = $this->request->amount;
			$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
			$position->city_id = $this->request->city_id ?? 0;
			$position->promo_id = ($this->request->promo_id && $promo) ? $promo->id : 0;
			$position->promocode_id = ($this->request->promocode_id && $promocode) ? $promocode->id : 0;
			$position->data_json = $data;
			$position->save();
			
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

		$product = Product::find($this->request->product_id);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if (!$product->validateFlightDate($this->request->flight_date_at . ' ' . $this->request->flight_time_at)) {
			return response()->json(['status' => 'error', 'reason' => 'Для бронирования полета по тарифу Regular доступны только будние дни']);
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

		if ($location && $location->city) {
			$cityProduct = $product->cities->find($location->city->id);
		}
		\Log::debug($cityProduct);

		$data = [];
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			$position->product_id = $product ? $product->id : 0;
			$position->duration = $product ? $product->duration : 0;
			$position->amount = $this->request->amount;
			$position->currency_id = ($cityProduct && $cityProduct->pivot) ? $cityProduct->pivot->currency_id : 0;
			$position->city_id = ($location && $location->city) ? $location->city->id : 0;
			$position->location_id = $location ? $location->id : 0;
			$position->flight_simulator_id = $simulator ? $simulator->id : 0;
			$position->promo_id = ($this->request->promo_id && $promo) ? $promo->id : 0;
			$position->promocode_id = ($this->request->promocode_id && $promocode) ? $promocode->id : 0;
			$position->flight_at = Carbon::parse($this->request->flight_date_at . ' ' . $this->request->flight_time_at)->format('Y-m-d H:i');
			$position->data_json = $data;
			$position->save();

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

		$cityProduct = $product->cities->find($this->request->city_id);

		$data = [];
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}

		try {
			\DB::beginTransaction();

			$position->product_id = $product ? $product->id : 0;
			$position->amount = $this->request->amount;
			$position->currency_id = $cityProduct ? $cityProduct->currency_id : 0;
			$position->city_id = $this->request->city_id ?? 0;
			$position->promo_id = ($this->request->promo_id && $promo) ? $promo->id : 0;
			$position->promocode_id = ($this->request->promocode_id && $promocode) ? $promocode->id : 0;
			$position->data_json = $data;
			$position->save();

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
