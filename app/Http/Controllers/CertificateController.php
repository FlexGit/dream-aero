<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Certificate;
use App\Models\Order;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Status;
use App\Models\Contractor;

class CertificateController extends Controller
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
		$cities = City::orderBy('name')
			->get();
		
		$productTypes = ProductType::orderBy('name')
			->get();
		
		$statuses = Status::where('type', Status::STATUS_TYPE_CERTIFICATE)
			->orderBy('sort')
			->get();
		
		return view('admin.certificate.index', [
			'cities' => $cities,
			'productTypes' => $productTypes,
			'statuses' => $statuses,
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
		
		$certificates = Certificate::with(['contractor', 'status', 'product', 'city'])
			->orderBy('id', 'desc');
		if ($id) {
			$certificates = $certificates->where('id', '<', $id);
		}
		if ($this->request->filter_status_id) {
			$certificates = $certificates->where('status_id', $this->request->filter_status_id);
		}
		if ($this->request->filter_city_id) {
			$certificates = $certificates->where('city_id', $this->request->filter_city_id);
		}
		if ($this->request->filter_product_type_id) {
			$certificates = $certificates->where(function ($query) {
				$query->whereHas('product', function ($q) {
					return $q->where('product_type_id', '=', $this->request->filter_product_type_id);
				});
			});
		}
		if ($this->request->search_doc) {
			$certificates = $certificates->where(function ($query) {
				$query->where('number', 'like', '%' . $this->request->search_doc . '%');
			});
		}
		if ($this->request->search_contractor) {
			$certificates = $certificates->whereHas('contractor', function ($query) {
				return $query->where('name', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('lastname', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%');
			});
		}
		$certificates = $certificates->limit(10)->get();
		
		$VIEW = view('admin.certificate.list', ['certificates' => $certificates]);
		
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
		
		$certificate = Certificate::find($id);
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$cities = City::orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		$productTypes = ProductType::with(['products'])
			->orderBy('name')
			->get();
		
		$products = Product::orderBy('name')
			->get();
		
		$paymentMethods = PaymentMethod::orderBy('name')
			->get();

		$statuses = Status::where('type', Status::STATUS_TYPE_CERTIFICATE)
			->orderBy('sort')
			->get();
		
		$VIEW = view('admin.certificate.modal.edit', [
			'certificate' => $certificate,
			'cities' => $cities,
			'locations' => $locations,
			'productTypes' => $productTypes,
			'products' => $products,
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
		$certificate = Certificate::find($id);
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$cities = City::orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		$products = Product::orderBy('name')
			->get();
		
		$paymentMethods = PaymentMethod::orderBy('name')
			->get();

		$statuses = Status::where('type', Status::STATUS_TYPE_CERTIFICATE)
			->orderBy('sort')
			->get();
		
		$VIEW = view('admin.certificate.modal.show', [
			'certificate' => $certificate,
			'cities' => $cities,
			'locations' => $locations,
			'products' => $products,
			'paymentMethods' => $paymentMethods,
			'statuses' => $statuses,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();
		
		$locations = Location::where('is_active', true)
			->orderBy('name')
			->get();
		
		$productTypes = ProductType::where('is_active', true)
			->with(['products'])
			->orderBy('name')
			->get();
		
		$paymentMethods = PaymentMethod::orderBy('name')
			->get();

		$VIEW = view('admin.deal.modal.add', [
			'cities' => $cities,
			'locations' => $locations,
			'productTypes' => $productTypes,
			'paymentMethods' => $paymentMethods,
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
			'product_id' => 'required|numeric',
			'city_id' => 'required|numeric',
			'location_id' => 'required|numeric',
			'contractor_id' => 'required|numeric',
			'payment_method_id' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'product_id' => 'Продукт',
				'city_id' => 'Город',
				'location_id' => 'Локация',
				'contractor_id' => 'Контрагент',
				'payment_method_id' => 'Способ оплаты',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$data = [];
		
		$certificate = new Certificate();
		$certificate->number = $certificate->generateNumber();
		$certificate->status_id = '';
		$certificate->contractor_id = $this->request->contractor_id;
		$certificate->city_id = $this->request->city_id;
		$certificate->location_id = $this->request->location_id;
		$certificate->product_id = $this->request->product_id;
		$certificate->payment_method_id = $this->request->payment_method_id;
		$certificate->data_json = $data;
		$certificate->expire_at = $this->request->expire_at;
		if (!$certificate->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $certificate->id]);
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
		
		$certificate = Certificate::find($id);
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'status_id' => 'required|numeric',
			'product_id' => 'required|numeric',
			'city_id' => 'required|numeric',
			'location_id' => 'required|numeric',
			'contractor_id' => 'required|numeric',
			'payment_method_id' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'status_id' => 'Статус',
				'product_id' => 'Продукт',
				'city_id' => 'Город',
				'location_id' => 'Локация',
				'contractor_id' => 'Контрагент',
				'payment_method_id' => 'Способ оплаты',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$data = [];
		
		$certificate = new Certificate();
		$certificate->status_id = $this->request->status_id;
		$certificate->contractor_id = $this->request->contractor_id;
		$certificate->city_id = $this->request->city_id;
		$certificate->location_id = $this->request->location_id;
		$certificate->product_id = $this->request->product_id;
		$certificate->payment_method_id = $this->request->payment_method_id;
		$certificate->data_json = $data;
		$certificate->expire_at = $this->request->expire_at;
		
		if (!$certificate->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $certificate->id]);
	}
}
