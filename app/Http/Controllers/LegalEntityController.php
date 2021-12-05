<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\LegalEntity;

class LegalEntityController extends Controller
{
	private $request;
	private $user;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->middleware('auth');
		
		$this->user = Auth::user();
		$this->request = $request;
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		return view('admin/legalEntity/index', [
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		if (!$this->request->ajax()) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка, попробуйте повторить операцию позже']);
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
		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		return view('admin/legalEntity/modal/edit', [
			'legalEntity' => $legalEntity,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		if (!$this->request->ajax()) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка, попробуйте повторить операцию позже']);
		}

		return view('admin/legalEntity/modal/add');
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		if (!$this->request->ajax()) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка, попробуйте повторить операцию позже']);
		}

		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		return view('admin/legalEntity/modal/delete', [
			'legalEntity' => $legalEntity,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		if (!$this->request->ajax()) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка, попробуйте повторить операцию позже']);
		}

		$rules = [
			'name' => ['required', 'max:255'],
			'public_offer' => ['required', 'mimes:pdf', 'max:5120'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'public_offer' => 'Публичная оферта',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$fileName =  $this->request->file('public_offer')->getFilename();
		$fileExt =  $this->request->file('public_offer')->extension();
		
		if (!$this->request->file('file')->storeAs('upload/public_offer', $fileName . '.' . $fileExt, 'public')) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		$legalEntity = new LegalEntity();
		$legalEntity->name = $this->request->name;
		$legalEntity->is_active = $this->request->is_active;
		$data = json_decode($legalEntity->data_json, true);
		$data['public_offer_file_path'] = [
			'name' => $fileName,
			'ext' => $fileExt,
		];
		$legalEntity->data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		if (!$legalEntity->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $legalEntity->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		if (!$this->request->ajax()) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка, попробуйте повторить операцию позже']);
		}

		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$rules = [
			'name' => 'required|max:255'
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование'
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$legalEntity->name = $this->request->name;
		$legalEntity->is_active = $this->request->is_active;
		if (!$legalEntity->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $legalEntity->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		if (!$this->request->ajax()) {
			return response()->json(['status' => 'error', 'reason' => 'Ошибка, попробуйте повторить операцию позже']);
		}

		$legalEntity = LegalEntity::find($id);
		if (!$legalEntity) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$legalEntity->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $legalEntity->id]);
	}
}
