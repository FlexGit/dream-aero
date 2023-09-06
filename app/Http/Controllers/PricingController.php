<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Discount;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\City;

class PricingController extends Controller
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
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		return view('admin.pricing.index', [
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

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name');
		if ($this->request->filter_city_id) {
			$cities = $cities->where('id', $this->request->filter_city_id);
		}
		$cities = $cities->get();
		
		$products = Product::orderBy('product_type_id')
			->orderBy('duration')
			->get();
		
		$citiesProductsData = [];
		foreach ($cities as $city) {
			foreach ($products as $product) {
				$cityProduct = $city->products->find($product->id);
				if (!$cityProduct) continue;

				$citiesProductsData[$city->id][$product->id] = [
					'availability' => $cityProduct->pivot->availability,
					'purchase_price' => $cityProduct->pivot->purchase_price,
					'price' => $cityProduct->pivot->price,
					'currency' => $cityProduct->pivot->currency ? $cityProduct->pivot->currency->name : '',
					'is_hit' => $cityProduct->pivot->is_hit,
					'score' => $cityProduct->pivot->score,
					'is_active' => $cityProduct->pivot->is_active,
					'data_json' => $cityProduct->pivot->data_json,
				];
				if ($cityProduct->pivot->discount) {
					$citiesProductsData[$city->id][$product->id]['discount'] = [
						'value' => $cityProduct->pivot->discount->value,
						'is_fixed' => $cityProduct->pivot->discount->is_fixed,
					];
				}
			}
		}
		
		$VIEW = view('admin.pricing.list', [
			'cities' => $cities,
			'products' => $products,
			'citiesProductsData' => $citiesProductsData,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $cityId
	 * @param $productId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($cityId, $productId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$city = City::find($cityId);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Город не найден']);

		$product = Product::find($productId);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);

		$cityProduct = $city->products()->find($productId);

		$cities = City::orderBy('name')
			->get();
		
		$products = Product::orderBy('name')
			->get();

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$currencies = Currency::get();

		$VIEW = view('admin.pricing.modal.edit', [
			'cityId' => $cityId,
			'productId' => $productId,
			'cities' => $cities,
			'products' => $products,
			'discounts' => $discounts,
			'currencies' => $currencies,
			'cityProduct' => $cityProduct ? $cityProduct->pivot : null,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $cityId
	 * @param $productId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function confirm($cityId, $productId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$city = City::find($cityId);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		
		$product = Product::find($productId);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		
		$cityProduct = $city->products->find($productId);
		if (!$cityProduct) return response()->json(['status' => 'error', 'reason' => 'Продукт в городе не найден']);

		$VIEW = view('admin.pricing.modal.delete', [
			'city' => $city,
			'product' => $product,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $cityId
	 * @param $productId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($cityId, $productId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$city = City::find($cityId);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		
		$product = Product::find($productId);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		
		$cityProduct = $city->products->find($productId);
		
		$rules = [
			'price' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'price' => 'Стоимость',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$data = $cityProduct ? json_decode($cityProduct->pivot->data_json, true) : [];
		$data['is_booking_allow'] = (bool)$this->request->is_booking_allow;
		$data['is_certificate_purchase_allow'] = (bool)$this->request->is_certificate_purchase_allow;
		$data['is_discount_booking_allow'] = (bool)$this->request->is_discount_booking_allow;
		$data['is_discount_certificate_purchase_allow'] = (bool)$this->request->is_discount_certificate_purchase_allow;
		$data['certificate_period'] = $this->request->certificate_period;

		$data = [
			'availability' => $this->request->availability ?? 0,
			'purchase_price' => $this->request->purchase_price ?? 0,
			'price' => $this->request->price ?? 0,
			'currency_id' => $this->request->currency_id ?? 0,
			'discount_id' => $this->request->discount_id ?? 0,
			'is_hit' => (bool)$this->request->is_hit,
			'score' => $this->request->score ?? 0,
			'is_active' => (bool)$this->request->is_active,
			'data_json' => json_encode($data, JSON_UNESCAPED_UNICODE),
		];
		
		if ($cityProduct) {
			$city->products()->updateExistingPivot($product->id, $data);
		} else {
			$city->products()->attach($product->id, $data);
		}
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @param $cityId
	 * @param $productId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($cityId, $productId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$city = City::find($cityId);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		
		$product = Product::find($productId);
		if (!$product) return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		
		$cityProduct = $city->products->find($productId);
		if (!$cityProduct) return response()->json(['status' => 'error', 'reason' => 'Продукт в указанном городе не найден']);
		
		if (!$city->products()->detach($product->id)) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
}
