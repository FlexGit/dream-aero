<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\LegalEntity;

class LegalEntityController extends Controller
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
		return view('admin.legalEntity.index', [
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

		$legalEntities = LegalEntity::get();

		$VIEW = view('admin.legalEntity.list', ['legalEntities' => $legalEntities]);

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

		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Юр.лицо не найдено']);

		$VIEW = view('admin.legalEntity.modal.edit', [
			'legalEntity' => $legalEntity,
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

		$VIEW = view('admin.legalEntity.modal.add', [
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
		
		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Юр.лицо не найдено']);
		
		$VIEW = view('admin.legalEntity.modal.show', [
			'legalEntity' => $legalEntity,
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

		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Юр.лицо не найдено']);
		
		$VIEW = view('admin.legalEntity.modal.delete', [
			'legalEntity' => $legalEntity,
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
			'name' => ['required', 'max:255', 'unique:legal_entities,name'],
			'alias' => ['required', 'min:3', 'max:50', 'unique:legal_entities,alias'],
			'public_offer' => ['required', 'mimes:pdf', 'max:5120'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
				'public_offer' => 'Публичная оферта',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$isFileUploaded = false;
		if($file = $this->request->file('public_offer')) {
			$filename = md5(rand(1000, 9999)) . '.pdf';
			$isFileUploaded = $file->move(public_path('upload/public_offer'), $filename);
		}
		
		$legalEntity = new LegalEntity();
		$legalEntity->name = $this->request->name;
		$legalEntity->alias = $this->request->alias;
		$legalEntity->is_active = $this->request->is_active;
		$legalEntity->data_json = [
			'public_offer_file_path' => $isFileUploaded ? 'public_offer/' . $filename : '',
		];
		if (!$legalEntity->save()) {
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

		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Юр.лицо не найдено']);

		$rules = [
			'name' => ['required', 'max:255', 'unique:legal_entities,name, ' . $id],
			'alias' => ['required', 'min:3', 'max:50', 'unique:legal_entities,alias,' . $id],
			'public_offer' => ['required', 'mimes:pdf', 'max:5120'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
				'public_offer' => 'Публичная оферта',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$isFileUploaded = false;
		if($file = $this->request->file('public_offer')) {
			$filename = md5(rand(1000, 9999)) . '.pdf';
			$isFileUploaded = $file->move(public_path('upload/public_offer'), $filename);
		}
		
		// удаляем старый файл оферты
		if ($isFileUploaded && $legalEntity->data_json && isset($legalEntity->data_json['public_offer_file_path']) && is_file(public_path('upload/' . $legalEntity->data_json['public_offer_file_path']))) {
			unlink(public_path('upload/' . $legalEntity->data_json['public_offer_file_path']));
		}

		$legalEntity->name = $this->request->name;
		$legalEntity->alias = $this->request->alias;
		$legalEntity->is_active = $this->request->is_active;
		$legalEntity->data_json = [
			'public_offer_file_path' => $isFileUploaded ? 'public_offer/' . $filename : '',
		];
		if (!$legalEntity->save()) {
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

		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Юр.лицо не найдено']);
		
		if (!$legalEntity->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		// удаляем файл оферты
		if ($legalEntity->data_json && isset($legalEntity->data_json['public_offer_file_path']) && is_file(public_path('upload/' . $legalEntity->data_json['public_offer_file_path']))) {
			unlink(public_path('upload/' . $legalEntity->data_json['public_offer_file_path']));
		}
		
		return response()->json(['status' => 'success']);
	}
}
