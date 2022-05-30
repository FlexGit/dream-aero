<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use App\Models\Promo;

class PromoController extends Controller
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
		return view('admin.promo.index', [
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

		$promos = Promo::orderby('id', 'desc')
			->get();

		$VIEW = view('admin.promo.list', [
			'promos' => $promos,
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
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.promo.modal.edit', [
			'promo' => $promo,
			'discounts' => $discounts,
			'cities' => $cities,
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
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$VIEW = view('admin.promo.modal.add', [
			'discounts' => $discounts,
			'cities' => $cities,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		$VIEW = view('admin.promo.modal.show', [
			'promo' => $promo,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
		
		$VIEW = view('admin.promo.modal.delete', [
			'promo' => $promo,
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
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$rules = [
			'name' => ['required', 'max:255'],
			'alias' => ['required', 'max:255'],
			'image_file' => 'sometimes|image|max:512',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'alias' => 'Алиас',
				'image_file' => 'Изображение',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$isImageFileUploaded = false;
		if($imageFile = $this->request->file('image_file')) {
			$isImageFileUploaded = $imageFile->move(public_path('upload/promo'), $imageFile->getClientOriginalName());
		}

		$promo = new Promo();
		$promo->name = $this->request->name;
		$promo->alias = $this->request->alias;
		$promo->discount_id = $this->request->discount_id ?? 0;
		$promo->city_id = $this->request->city_id ?? 0;
		$promo->preview_text = $this->request->preview_text ?? '';
		$promo->detail_text = $this->request->detail_text ?? '';
		$promo->is_published = (bool)$this->request->is_published;
		$promo->is_active = (bool)$this->request->is_active;
		$promo->meta_title = $this->request->meta_title;
		$promo->meta_description = $this->request->meta_description;
		$promo->active_from_at = $this->request->active_from_at ? Carbon::parse($this->request->active_from_at)->format('Y-m-d') : null;
		$promo->active_to_at = $this->request->active_to_at ? Carbon::parse($this->request->active_to_at)->format('Y-m-d') : null;
		$data = $promo->data_json;
		if ($this->request->is_discount_booking_allow) {
			$data['is_discount_booking_allow'] = (bool)$this->request->is_discount_booking_allow;
		}
		if ($this->request->is_discount_certificate_purchase_allow) {
			$data['is_discount_certificate_purchase_allow'] = (bool)$this->request->is_discount_certificate_purchase_allow;
		}
		if ($isImageFileUploaded) {
			$data['image_file_path'] = 'promo/' . $imageFile->getClientOriginalName();
		}
		$promo->data_json = $data;
		if (!$promo->save()) {
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
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		$rules = [
			'name' => ['required', 'max:255'],
			'alias' => ['required', 'max:255'],
			'image_file' => 'sometimes|image|max:512',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'alias' => 'Алиас',
				'image_file' => 'Изображение',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$isImageFileUploaded = false;
		if($imageFile = $this->request->file('image_file')) {
			$isImageFileUploaded = $imageFile->move(public_path('upload/promo'), $imageFile->getClientOriginalName());
		}

		// удаляем старый файл оферты
		if ($isImageFileUploaded && $promo->data_json && $promo->data_json['image_file_path'] && is_file(public_path('upload/' . $promo->data_json['image_file_path']))) {
			unlink(public_path('upload/' . $promo->data_json['image_file_path']));
		}

		$promo->name = $this->request->name;
		$promo->alias = $this->request->alias;
		$promo->discount_id = $this->request->discount_id ?? 0;
		$promo->city_id = $this->request->city_id ?? 0;
		$promo->preview_text = $this->request->preview_text ?? '';
		$promo->detail_text = $this->request->detail_text ?? '';
		$promo->is_published = (bool)$this->request->is_published;
		$promo->is_active = (bool)$this->request->is_active;
		$promo->meta_title = $this->request->meta_title;
		$promo->meta_description = $this->request->meta_description;
		$promo->active_from_at = $this->request->active_from_at ? Carbon::parse($this->request->active_from_at)->format('Y-m-d') : null;
		$promo->active_to_at = $this->request->active_to_at ? Carbon::parse($this->request->active_to_at)->format('Y-m-d') : null;
		$data = $promo->data_json;
		/*if ($this->request->is_discount_booking_allow) {*/
			$data['is_discount_booking_allow'] = (bool)$this->request->is_discount_booking_allow;
		/*}
		if ($this->request->is_discount_certificate_purchase_allow) {*/
			$data['is_discount_certificate_purchase_allow'] = (bool)$this->request->is_discount_certificate_purchase_allow;
		/*}*/
		if ($isImageFileUploaded) {
			$data['image_file_path'] = 'promo/' . $imageFile->getClientOriginalName();
		}
		$promo->data_json = $data;
		if (!$promo->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		// удаляем файл изображения
		if ($promo->data_json && isset($promo->data_json['image_file_path']) && is_file(public_path('upload/' . $promo->data_json['image_file_path']))) {
			unlink(public_path('upload/' . $promo->data_json['image_file_path']));
		}

		if (!$promo->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteImage($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		// удаляем файл изображения
		if ($promo->data_json && isset($promo->data_json['image_file_path']) && is_file(public_path('upload/' . $promo->data_json['image_file_path']))) {
			unlink(public_path('upload/' . $promo->data_json['image_file_path']));
		}

		$data = $promo->data_json;
		unset($data['image_file_path']);
		$promo->data_json = $data;
		if (!$promo->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function imageUpload() {
		$file = $this->request->file('file');
		if (!$file->move(public_path('/upload/promo/'), $file->getClientOriginalName())) {
			return response()->json(['status' => 'error', 'reason' => 'Не удалось загрузить файл']);
		}
		
		return response()->json([
			'location' => url('/upload/promo/' . $file->getClientOriginalName()),
		]);
	}
}
