<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\ProductType;

class ProductTypeController extends Controller
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
		return view('admin.productType.index', [
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$productTypes = ProductType::get();

		$VIEW = view('admin.productType.list', ['productTypes' => $productTypes]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		return view('admin.productType.modal.edit', [
			'productType' => $productType,
			'durations' => ProductType::DURATIONS,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		return view('admin.productType.modal.add', [
			'durations' => ProductType::DURATIONS,
		]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		return view('admin.productType.modal.delete', [
			'productType' => $productType,
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
		
		$productType = new ProductType();
		$productType->name = $this->request->name;
		$productType->is_tariff = $this->request->is_tariff;
		$productType->is_active = $this->request->is_active;
		$productType->data_json = [
			'duration' => ($this->request->duration && $this->request->is_tariff) ? (!is_array($this->request->duration) ? array_map('intval', explode(',', $this->request->duration)) : array_map('intval', $this->request->duration)) : null,
		];
		if (!$productType->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $productType->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

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
		
		$productType->name = $this->request->name;
		$productType->is_tariff = $this->request->is_tariff;
		$productType->is_active = $this->request->is_active;
		$productType->data_json = [
			'duration' => ($this->request->duration && $this->request->is_tariff) ? (!is_array($this->request->duration) ? array_map('intval', explode(',', $this->request->duration)) : array_map('intval', $this->request->duration)) : null,
		];
		if (!$productType->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $productType->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$productType->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $productType->id]);
	}
}
