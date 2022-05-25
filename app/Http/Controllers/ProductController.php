<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

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
		/*$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();
		
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();*/

		return view('admin.product.index', [
			/*'productTypes' => $productTypes,
			'cities' => $cities,*/
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

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$products = Product::with(['productType'])
			->orderBy('product_type_id', 'asc')
			->orderBy('duration', 'asc');
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
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		
		$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();

		$pilots = User::where('role', User::ROLE_PILOT)
			->orderBy('lastname')
			->orderBy('name')
			->get();

		$VIEW = view('admin.product.modal.edit', [
			'product' => $product,
			'productTypes' => $productTypes,
			'pilots' => $pilots,
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

		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);

		$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.product.modal.show', [
			'product' => $product,
			'productTypes' => $productTypes,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();

		$pilots = User::where('role', User::ROLE_PILOT)
			->orderBy('lastname')
			->orderBy('name')
			->get();

		$VIEW = view('admin.product.modal.add', [
			'productTypes' => $productTypes,
			'pilots' => $pilots,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		
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
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$rules = [
			'name' => 'required|max:255|unique:products,name',
			'alias' => 'required|max:255|unique:products,alias',
			'product_type_id' => 'required|numeric|min:0|not_in:0',
			'icon_file' => 'sometimes|image|max:512',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
				'product_type_id' => 'Тип продукта',
				'icon_file' => 'Иконка',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$isIconFileUploaded = false;
		if($iconFile = $this->request->file('icon_file')) {
			$isIconFileUploaded = $iconFile->move(public_path('upload/product/icon'), $iconFile->getClientOriginalName());
		}

		$product = new Product();
		$product->name = $this->request->name;
		$product->alias = $this->request->alias;
		$product->product_type_id = $this->request->product_type_id;
		$product->user_id = $this->request->user_id ?? 0;
		$product->duration = $this->request->duration ?? 0;
		$data = [];
		$data['description'] = $this->request->description ?? null;
		if ($isIconFileUploaded) {
			$data['icon_file_path'] = 'product/icon/' . $iconFile->getClientOriginalName();
		}
		$product->data_json = $data;
		if (!$product->save()) {
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

		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		
		$rules = [
			'name' => 'required|max:255|unique:products,name,' . $id,
			'alias' => 'required|max:255|unique:products,alias,' . $id,
			'product_type_id' => 'required|numeric|min:0|not_in:0',
			'icon_file' => 'sometimes|image|max:512',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
				'product_type_id' => 'Тип продукта',
				'icon_file' => 'Иконка',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$isIconFileUploaded = false;
		if($iconFile = $this->request->file('icon_file')) {
			$isIconFileUploaded = $iconFile->move(public_path('upload/product/icon'), $iconFile->getClientOriginalName());
		}

		$product->name = $this->request->name;
		$product->alias = $this->request->alias;
		$product->product_type_id = $this->request->product_type_id;
		$product->user_id = $this->request->user_id ?? 0;
		$product->duration = $this->request->duration ?? 0;
		$data = $product->data_json;
		$data['description'] = $this->request->description ?? null;
		if ($isIconFileUploaded) {
			$data['icon_file_path'] = 'product/icon/' . $iconFile->getClientOriginalName();
		}
		$product->data_json = $data;
		if (!$product->save()) {
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

		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		
		if (!$product->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteIcon($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$product = Product::find($id);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);

		$data = $product->data_json;
		unset($data['icon_file_path']);
		$product->data_json = $data;
		if (!$product->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}
}
