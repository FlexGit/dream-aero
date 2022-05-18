<?php

namespace App\Http\Controllers;

use App\Models\DealPosition;
use App\Models\PaymentMethod;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Models\Certificate;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Status;

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
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден']);
		
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
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден']);
		
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
			'product_id' => 'required|numeric|min:0|not_in:0',
			'city_id' => 'required|numeric|min:0|not_in:0',
			'location_id' => 'required|numeric|min:0|not_in:0',
			'contractor_id' => 'required|numeric|min:0|not_in:0',
			'payment_method_id' => 'required|numeric|min:0|not_in:0',
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
		
		$certificate = Certificate::find($id);
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден']);
		
		$rules = [
			'status_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'status_id' => 'Статус',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$certificate->status_id = $this->request->status_id;
		if (!$certificate->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sendCertificate() {
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$rules = [
			'id' => 'required|numeric|min:0|not_in:0',
			'certificate_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'id' => 'Позиция',
				'certificate_id' => 'Сертификат',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$position = DealPosition::find($this->request->id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		$contractor = $deal->contractor;
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		
		$dealEmail = $deal->email ?? '';
		$contractorEmail = $contractor->email ?? '';
		if (!$dealEmail && !$contractorEmail) {
			return null;
		}
		
		$certificate = Certificate::find($this->request->certificate_id);
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден']);
		
		//dispatch(new \App\Jobs\SendCertificateEmail($certificate));
		$job = new \App\Jobs\SendCertificateEmail($certificate);
		$job->handle();
		
		return response()->json(['status' => 'success', 'message' => 'Задание на отправку Сертификата принято']);
	}
	
	/**
	 * @param $uuid
	 * @return \never|\Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function getCertificateFile($uuid)
	{
		$certificate = HelpFunctions::getEntityByUuid(Certificate::class, $uuid);
		if (!$certificate) {
			abort(404);
		}
		
		//$certificateFilePath = isset($certificate->data_json['certificate_file_path']) ? $certificate->data_json['certificate_file_path'] : '';
		//$certificateFileExists = Storage::disk('private')->exists($certificateFilePath);

		// если файла сертификата по какой-то причине не оказалось, генерим его
		//if (!$certificateFilePath || !$certificateFileExists) {
			$certificate = $certificate->generateFile();
			if (!$certificate) {
				abort(404);
			}
		//}
		
		$certificateFilePath = (is_array($certificate->data_json) && array_key_exists('certificate_file_path', $certificate->data_json)) ? $certificate->data_json['certificate_file_path'] : '';
		
		return Storage::disk('private')->download($certificateFilePath);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function search() {
		$q = $this->request->post('query');
		if (!$q) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$user = \Auth::user();
		
		$certificates = Certificate::where('number', 'like', '%' . $q . '%')
			->orderBy('number')
			->limit(10)
			->get();
		
		$suggestions = [];
		/** @var Certificate[] $certificates */
		foreach ($certificates as $certificate) {
			$data = $certificate->data_json;
			
			if (!$certificate->product_id) {
				$certificateInfo = (isset($data['sell_date']) ? 'от ' . $data['sell_date'] : '') . ($certificate->expire_at ? ' до ' . $certificate->expire_at->format('d.m.Y') : ' - без срока') . (isset($data['duration']) ? ' на ' . $data['duration'] . ' мин' : '') . (isset($data['amount']) ? ' за ' . $data['amount'] . ' руб' : '') . (isset($data['payment_method']) ? ' (' . $data['payment_method'] . ')' : '') . (isset($data['location']) ? '. ' . $data['location'] : '') . (isset($data['status']) ? '. ' . $data['status'] : '') . ((isset($data['comment']) && $data['comment']) ? ', ' . $data['comment'] : '');
			} else {
				//$position = $certificate->position()->where('is_certificate_purchase', true)->first();
				$product = $certificate->product;
				$city = $certificate->city;
				$status = $certificate->status;
				
				$certificateInfo = $certificate->created_at->format('d.m.Y') . ($certificate->expire_at ? ' до ' . $certificate->expire_at->format('d.m.Y') : ' - без срока') . ($product ? ' на ' . $product->duration . ' мин (' . $product->name . ')' : '') . ($city ? '. ' . $city->name : '') . ($status ? '. ' . $status->name : '');
			}
			
			$date = date('Y-m-d');
			
			$suggestions[] = [
				'value' => $certificate->number . ' [' . $certificateInfo . ']',
				'id' => $certificate->uuid,
				'data' => [
					'number' => $certificate->number,
					'is_overdue' => ($certificate->expire_at && Carbon::parse($certificate->expire_at)->lt($date)) ? true : false,
				],
			];
		}
		
		return response()->json(['suggestions' => $suggestions]);
	}
}
