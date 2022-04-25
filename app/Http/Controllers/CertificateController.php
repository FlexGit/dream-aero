<?php

namespace App\Http\Controllers;

use App\Models\DealPosition;
use App\Models\PaymentMethod;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\Certificate;
use App\Models\Deal;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Status;
use App\Models\Contractor;
use Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

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

		$certificate = Certificate::find($this->request->certificate_id);
		if (!$certificate) return response()->json(['status' => 'error', 'reason' => 'Сертификат не найден']);
		
		$email = ($position->deal && $position->deal->contractor) ? $position->deal->contractor->email : '';
		
		if (!$email) return response()->json(['status' => 'error', 'reason' => 'E-mail не найден']);
		
		$certificateFilePath = storage_path('app/private/certificate/' . $certificate->uuid . '.jpg');
		
		Mail::send('admin.emails.send_certificate', ['path' => $certificateFilePath], function ($message) use ($email, $certificateFilePath) {
			$message->to($email)->subject('Сертификат на полет на авиатренажере');
			$message->attach($certificateFilePath);
		});
		
		$failures = Mail::failures();
		if ($failures) {
			return response()->json(['status' => 'error', 'reason' => implode(' ', $failures)]);
		}
		
		$certificateSentAt = Carbon::now()->format('Y-m-d H:i:s');
		$position->link_sent_at = $certificateSentAt;
		if (!$position->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'certificate_sent_at' => $certificateSentAt]);
	}
	
	/**
	 * @param $uuid
	 * @return \never|\Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function generateCertificate($uuid)
	{
		$certificate = HelpFunctions::getEntityByUuid(Certificate::class, $uuid);
		if (!$certificate) {
			return abort(404);
		}
		
		$product = $certificate->product;
		if (!$product) return abort(404);
		
		$productType = $product->productType;
		if (!$productType) return abort(404);
		
		$city = $certificate->city;
		if (!$city) return abort(404);
		
		$cityProduct = $product->cities()->where('cities_products.is_active', true)->find($city->id);
		if (!$cityProduct || !$cityProduct->pivot) {
			return abort(404);
		}
		
		$data = json_decode($cityProduct->pivot->data_json, true);
		if (!isset($data['certificate_template_file_path'])) {
			return abort(404);
		}
		
		if (!Storage::disk('private')->exists($data['certificate_template_file_path'])) {
			return abort(404);
		}
		
		$certificateTemplateFilePath = Storage::disk('private')->path($data['certificate_template_file_path']);
		
		$certificateFile = Image::make($certificateTemplateFilePath)->encode('jpg');
		$fontPath = public_path('assets/fonts/GothamProRegular/GothamProRegular.ttf');
		
		/*switch ($city->alias) {
			case City::MSK_ALIAS:*/
				switch ($product->productType->alias) {
					case ProductType::REGULAR_ALIAS:
					case ProductType::ULTIMATE_ALIAS:
						$certificateFile->text($certificate->number, 833, 121, function($font) use ($fontPath) {
							$font->file($fontPath);
							$font->size(22);
							$font->color('#333333');
						});
						$certificateFile->text($certificate->created_at->format('d.m.Y'), 1300, 121, function($font) use ($fontPath) {
							$font->file($fontPath);
							$font->size(22);
							$font->color('#333333');
						});
						$certificateFile->text($certificate->product->duration ?? '-', 355, 1225, function($font) use ($fontPath) {
							$font->file($fontPath);
							$font->size(46);
							$font->color('#ffffff');
						});
					break;
					case ProductType::COURSES_ALIAS:
					case ProductType::PLATINUM_ALIAS:
						$certificateFile->text($certificate->number, 4700, 3022, function($font) use ($fontPath) {
							$font->file($fontPath);
							$font->size(70);
							$font->color('#333333');
						});
						$certificateFile->text($certificate->created_at->format('d.m.Y'), 6100, 3022, function($font) use ($fontPath) {
							$font->file($fontPath);
							$font->size(70);
							$font->color('#333333');
						});
					break;
					case ProductType::VIP_ALIAS:
						$certificateFile->text($certificate->number, 1880, 430, function($font) use ($fontPath) {
							$font->file($fontPath);
							$font->size(36);
							$font->color('#333333');
						});
						$certificateFile->text($certificate->created_at->format('d.m.Y'), 1965, 505, function($font) use ($fontPath) {
							$font->file($fontPath);
							$font->size(36);
							$font->color('#333333');
						});
					break;
				}
			/*break;
		}*/
		
		$certificateFileName = $certificate->uuid . '.jpg';
		
		$certificateFile->save(storage_path('app/private/certificate/' . $certificateFileName));
		
		$headers = [
			'Content-Type' => 'image/jpeg',
			'Content-Disposition' => 'attachment; filename=' . $certificateFileName,
		];
		return response()->stream(function() use ($certificateFile) {
			echo $certificateFile;
		}, 200, $headers);
	}
}
