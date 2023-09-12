<?php

namespace App\Http\Controllers;

use App\Services\HelpFunctions;
use Illuminate\Http\Request;
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
		if (!$this->request->ajax()) {
			abort(404);
		}

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
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Тип продукта не найден']);
		
		$VIEW = view('admin.productType.modal.edit', [
			'productType' => $productType,
			'durations' => ProductType::DURATIONS,
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
		
		$VIEW = view('admin.productType.modal.add', [
			'durations' => ProductType::DURATIONS,
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
		
		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Тип продукта не найден']);
		
		$VIEW = view('admin.productType.modal.show', [
			'productType' => $productType,
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

		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Тип продукта не найден']);
		
		$VIEW = view('admin.productType.modal.delete', [
			'productType' => $productType,
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
			'name' => ['required', 'max:255', 'unique:product_types,name'],
			'alias' => ['required', 'min:3', 'max:50', 'unique:product_types,alias'],
			'version' => ['required'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
				'version' => 'Версия',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$productType = new ProductType();
		$productType->name = $this->request->name;
		$productType->alias = $this->request->alias;
		$productType->is_tariff = $this->request->is_tariff;
		$productType->version = $this->request->version;
		$productType->is_active = $this->request->is_active;
		$productType->data_json = [
			'duration' => ($this->request->duration && $this->request->is_tariff) ? (!is_array($this->request->duration) ? array_map('intval', explode(',', $this->request->duration)) : array_map('intval', $this->request->duration)) : null,
		];
		if (!$productType->save()) {
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

		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Тип продукта не найден']);
		
		if (HelpFunctions::isDemo($productType->created_at)) {
			return response()->json(['status' => 'error', 'reason' => 'Демо-данные недоступны для редактирования']);
		}
		
		$rules = [
			'name' => ['required', 'max:255', 'unique:product_types,name,' . $id],
			'alias' => ['required', 'min:3', 'max:50', 'unique:product_types,alias,' . $id],
			'version' => ['required'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
				'version' => 'Версия',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$productType->name = $this->request->name;
		$productType->alias = $this->request->alias;
		$productType->is_tariff = $this->request->is_tariff;
		$productType->version = $this->request->version;
		$productType->is_active = $this->request->is_active;
		$productType->data_json = [
			'duration' => ($this->request->duration && $this->request->is_tariff) ? (!is_array($this->request->duration) ? array_map('intval', explode(',', $this->request->duration)) : array_map('intval', $this->request->duration)) : null,
		];
		if (!$productType->save()) {
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

		$productType = ProductType::find($id);
		if (!$productType) return response()->json(['status' => 'error', 'reason' => 'Тип продукта не найден']);
		
		if (HelpFunctions::isDemo($productType->created_at)) {
			return response()->json(['status' => 'error', 'reason' => 'Демо-данные недоступны для удаления']);
		}
		
		if (!$productType->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
}
