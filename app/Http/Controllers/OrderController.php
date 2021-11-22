<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Order;

class OrderController extends Controller
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
		return view('admin/order/index', [
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$orders = Order::get();
		
		$VIEW = view('admin.order.list', ['orders' => $orders]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		$order = Order::find($id);
		if (!$order) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		return view('admin/order/modal/edit', [
			'order' => $order,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		return view('admin/order/modal/add');
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		$rules = [
			'status_id' => 'required|numeric',
			'tariff_id' => 'required|numeric',
			'city_id' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'status_id' => 'Статус',
				'tariff_id' => 'Тариф',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$order = new Order();
		$order->number = $this->request->number;
		$order->status_id = $this->request->status_id;
		$order->tariff_id = $this->request->tariff_id;
		$order->city_id = $this->request->city_id;
		if (!$order->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $order->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		$order = Order::find($id);
		if (!$order) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'status_id' => 'required|numeric',
			'tariff_id' => 'required|numeric',
			'city_id' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'status_id' => 'Статус',
				'tariff_id' => 'Тариф',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$order->number = $this->request->number;
		$order->status_id = $this->request->status_id;
		$order->tariff_id = $this->request->tariff_id;
		$order->city_id = $this->request->city_id;

		if (!$order->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $order->id]);
	}
}
