<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Certificate;
use App\Models\Contractor;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\Deal;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
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
		$cities = City::orderBy('version', 'desc')
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
		
		$id = $this->request->id ?? 0;
		
		$deals = Deal::with('event')
			->orderBy('id', 'desc');
		if ($this->request->filter_status_id) {
			$deals = $deals->where(function ($query) {
				$query->where('status_id', $this->request->filter_status_id)
					->orWhereHas('certificate', function ($query) {
						return $query->where('status_id', $this->request->filter_status_id);
					})
					->orWhereHas('bills', function ($query) {
						return $query->where('status_id', $this->request->filter_status_id);
					});
			});
		}
		if ($this->request->filter_location_id) {
			$deals = $deals->where('location_id', $this->request->filter_location_id);
		}
		if ($this->request->filter_product_id) {
			$deals = $deals->where('product_id', $this->request->filter_product_id);
		}
		if ($this->request->search_doc) {
			$deals = $deals->where(function ($query) {
				$query->where('number', 'like', '%' . $this->request->search_doc . '%')
					->orWhereHas('certificate', function ($q) {
						return $q->where('number', 'like', '%' . $this->request->search_doc . '%');
					})
					->orWhereHas('bills', function ($q) {
						return $q->where('number', 'like', '%' . $this->request->search_doc . '%');
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

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();

		$promos = Promo::where('is_active', true)
			->orderBy('name')
			->get();

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$paymentMethods = PaymentMethod::where('is_active', true)
			->orderBy('name')
			->get();

		$VIEW = view('admin.deal.modal.add', [
			'isCertificatePurchase' => (bool)$this->request->isCertificatePurchase,
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'discounts' => $discounts,
			'paymentMethods' => $paymentMethods,
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

		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$statuses = Status::where('type', Status::STATUS_TYPE_DEAL)
			->orderBy('sort')
			->get();
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();
		
		$promos = Promo::where('is_active', true)
			->orderBy('name')
			->get();
		
		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();
		
		$paymentMethods = PaymentMethod::where('is_active', true)
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.deal.modal.edit', [
			'deal' => $deal,
			'cities' => $cities,
			'productTypes' => $productTypes,
			'promos' => $promos,
			'discounts' => $discounts,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id)
	{
		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		$cities = City::orderBy('name')
			->get();

		$locations = Location::orderBy('name')
			->get();

		$products = Product::orderBy('name')
			->get();

		$statuses = Status::orderBy('sort')
			->get();

		$VIEW = view('admin.deal.modal.show', [
			'deal' => $deal,
			'cities' => $cities,
			'locations' => $locations,
			'products' => $products,
			'statuses' => $statuses,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
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
		
		$productId = $this->request->product_id ?? 0;
		if ($productId) {
			$product = Product::find($productId);
		}
		
		$data = [];
		if ($this->request->certificate_whom) {
			$data['certificate_whom'] = $this->request->certificate_whom;
		}
		if ($this->request->certificate_comment) {
			$data['certificate_comment'] = $this->request->certificate_comment;
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
				$contractor->save();
			}
			
			$certificate = new Certificate();
			$certificateStatus = HelpFunctions::getEntityByAlias(Status::class, Certificate::CREATED_STATUS);
			$certificate->status_id = $certificateStatus ? $certificateStatus->id : 0;
			$certificate->expire_at = Carbon::parse($this->request->certificate_expire_at)->addYear()->format('Y-m-d H:i:s');
			$certificate->save();
			
			$deal = new Deal();
			$dealStatus = HelpFunctions::getEntityByAlias(Status::class, Deal::CREATED_STATUS);
			$deal->status_id = $dealStatus ? $dealStatus->id : 0;
			$deal->contractor_id = $contractor ? $contractor->id : $this->request->contractor_id;
			$deal->name = $this->request->name ?? '';
			$deal->phone = $this->request->phone ?? '';
			$deal->email = $this->request->email ?? '';
			$deal->product_id = $product ? $product->id : 0;
			$deal->certificate_id = $certificate->id;
			$deal->duration = $product ? $product->duration : 0;
			$deal->amount = $this->request->amount;
			$deal->city_id = $this->request->city_id ?? 0;
			$deal->promo_id = $this->request->promo_id ?? 0;
			$deal->is_certificate_purchase = 1;
			$deal->source = Deal::WEB_SOURCE;
			$deal->user_id = $this->request->user()->id;
			$deal->data_json = $data;
			$deal->save();

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Deal Create: ' . $e->getMessage());
			
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
			'product_id' => 'required|numeric|min:0|not_in:0',
			'city_id' => 'required|numeric|min:0|not_in:0',
			'status_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'phone' => 'Телефон',
				'product_id' => 'Продукт',
				'city_id' => 'Город',
				'status_id' => 'Статус',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$productId = $this->request->product_id ?? 0;
		if ($productId) {
			$product = Product::find($productId);
		}
		
		$data = [];
		if ($this->request->certificate_whom) {
			$data['certificate_whom'] = $this->request->certificate_whom;
		}
		if ($this->request->certificate_comment) {
			$data['certificate_comment'] = $this->request->certificate_comment;
		}
		if ($this->request->comment) {
			$data['comment'] = $this->request->comment;
		}
		
		try {
			\DB::beginTransaction();
			
			$deal->status_id = $this->request->status_id ?? 0;
			$deal->name = $this->request->name ?? '';
			$deal->phone = $this->request->phone ?? '';
			$deal->email = $this->request->email ?? '';
			$deal->product_id = $product ? $product->id : 0;
			$deal->duration = $product ? $product->duration : 0;
			$deal->amount = $this->request->amount;
			$deal->city_id = $this->request->city_id ?? 0;
			$deal->promo_id = $this->request->promo_id ?? 0;
			/*$deal->is_certificate_purchase = 1;*/
			$deal->data_json = $data;
			$deal->save();
			
			if (in_array($deal->status->alias, [Deal::CANCELED_STATUS]) && $deal->certificate && $deal->is_certificate_purchase) {
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
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			Log::debug('500 - Deal Create: ' . $e->getMessage());
			
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
		/*$isUnified = (bool)$this->request->is_unified;*/
		$paymentMethodId = $this->request->payment_method_id ?? 0;
		$cityId = $this->request->city_id ?? 0;
		$isFree = $this->request->is_free ?? 0;
		
		if (!$productId) {
			return response()->json(['status' => 'success', 'amount' => 0]);
		}
		
		$product = Product::find($productId);
		if (!$product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}
		
		$amount = $product->calcAmount($contractorId, $cityId, $paymentMethodId, $promoId, $promocodeId, $isFree, 'admin');

		return response()->json(['status' => 'success', 'amount' => $amount]);
	}
}
