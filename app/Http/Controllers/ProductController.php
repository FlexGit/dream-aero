<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Product;
use App\Models\City;

class ProductController extends Controller
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
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();

		return view('admin.product.index', [
			'cities' => $cities,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$products = Product::with(['city'])
			->orderBy('city_id', 'asc');
		if ($this->request->filter_city_id) {
			$products = $products->where('city_id', $this->request->filter_city_id);
		}
		$products = $products->get();

		$VIEW = view('admin.product.list', [
			'products' => $products
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();

		$VIEW = view('admin.product.modal.edit', [
			'cities' => $cities,
			'product' => $product,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id)
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.product.modal.show', [
			'cities' => $cities,
			'product' => $product,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add()
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.product.modal.add', [
			'cities' => $cities,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$VIEW = view('admin.product.modal.delete', [
			'product' => $product,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$rules = [
			'name' => 'required|max:255',
			'price' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'price' => 'Стоимость',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$product = new Product();
		$product->name = $this->request->name;
		$product->city_id = $this->request->city_id ?: 0;
		$product->price = $this->request->price;
		$product->is_active = $this->request->is_active;
		$product->data_json = [
		];
		if (!$product->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $product->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'name' => 'required|max:255',
			'price' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'price' => 'Стоимость',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$product->name = $this->request->name;
		$product->city_id = $this->request->city_id ?: 0;
		$product->price = $this->request->price;
		$product->is_active = $this->request->is_active;
		$product->data_json = [
		];
		if (!$product->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $product->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$product->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $product->id]);
	}
}
