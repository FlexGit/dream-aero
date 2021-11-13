<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\TariffType;

class TariffTypeController extends Controller
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
		return view('admin/tariffType/index', [
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$tariffTypes = TariffType::get();

		$VIEW = view('admin.tariffType.list', ['tariffTypes' => $tariffTypes]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		$tariffType = TariffType::find($id);
		if (!$tariffType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		return view('admin/tariffType/modal/edit', [
			'tariffType' => $tariffType,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		return view('admin/tariffType/modal/add');
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		$tariffType = TariffType::find($id);
		if (!$tariffType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		return view('admin/tariffType/modal/delete', [
			'tariffType' => $tariffType,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
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

		$tariffType = new TariffType();
		$tariffType->name = $this->request->name;
		$tariffType->is_active = $this->request->is_active;
		$tariffType->data_json = [];
		if (!$tariffType->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $tariffType->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		$tariffType = TariffType::find($id);
		if (!$tariffType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

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

		$tariffType->name = $this->request->name;
		$tariffType->is_active = $this->request->is_active;
		$tariffType->data_json = [];
		if (!$tariffType->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $tariffType->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		$tariffType = TariffType::find($id);
		if (!$tariffType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$tariffType->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $tariffType->id]);
	}
}
