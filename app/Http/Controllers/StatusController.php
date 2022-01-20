<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Status;

class StatusController extends Controller
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
		$statusTypes = Status::STATUS_TYPES;
		
		return view('admin/status/index', [
			'statusTypes' => $statusTypes,
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

		$statuses = Status::orderBy('type')
			->orderBy('sort');
		if ($this->request->filter_status_type_id) {
			$statuses = $statuses->where('type', $this->request->filter_status_type_id);
		}
		$statuses = $statuses->get();
		
		$statusTypes = \App\Models\Status::STATUS_TYPES;
		
		$discounts = Discount::get();
		
		$discountData = [];
		foreach ($discounts ?? [] as $discount) {
			$discountData[$discount->id] = $discount->valueFormatted();
		}

		$VIEW = view('admin.status.list', [
			'statuses' => $statuses,
			'statusTypes' => $statusTypes,
			'discountData' => $discountData,
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
		
		$status = Status::find($id);
		if (!$status) return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		
		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();
		
		$statusTypes = \App\Models\Status::STATUS_TYPES;
		
		$VIEW = view('admin.status.modal.edit', [
			'status' => $status,
			'discounts' => $discounts,
			'statusTypes' => $statusTypes,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
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
		
		$status = Status::find($id);
		if (!$status) return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		
		$rules = [
			'name' => 'required|max:255|unique:statuses,name,' . $id,
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$status->name = $this->request->name;
		if ($this->request->flight_time) {
			$status->flight_time = $this->request->flight_time;
		}

		$data = [];
		/*if ($this->request->flight_time) {
			$data['flight_time'] = $this->request->flight_time;
		}
		if ($this->request->discount_id) {
			$data['discount_id'] = $this->request->discount_id;
		}*/
		if ($this->request->color) {
			$data['color'] = $this->request->color;
		}
		$status->data_json = $data;
		if (!$status->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
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
		
		$status = Status::find($id);
		if (!$status) return response()->json(['status' => 'error', 'reason' => 'Статус не найден']);
		
		$discounts = Discount::get();
		
		$discountData = [];
		foreach ($discounts ?? [] as $discount) {
			$discountData[$discount->id] = $discount->valueFormatted();
		}

		$VIEW = view('admin.status.modal.show', [
			'status' => $status,
			'discountData' => $discountData,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
}
