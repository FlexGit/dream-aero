<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\Promocode;

class PromocodeController extends Controller
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

		return view('admin.promocode.index', [
			'cities' => $cities,
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
		
		$promocodes = Promocode::whereNull('type')
			->orderBy('number')
			->get();
		
		$VIEW = view('admin.promocode.list', ['promocodes' => $promocodes]);

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

		$promocode = Promocode::find($id);
		if (!$promocode) return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();
		
		$promocodeCityIds = $promocode->cities()->pluck('cities.id')->toArray();
		
		$VIEW = view('admin.promocode.modal.edit', [
			'promocode' => $promocode,
			'promocodeCityIds' => $promocodeCityIds,
			'cities' => $cities,
			'discounts' => $discounts,
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

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();
		
		$VIEW = view('admin.promocode.modal.add', [
			'cities' => $cities,
			'discounts' => $discounts,
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

		$promocode = Promocode::find($id);
		if (!$promocode) return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
		
		$VIEW = view('admin.promocode.modal.show', [
			'promocode' => $promocode,
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
		
		$promocode = Promocode::find($id);
		if (!$promocode) return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
		
		$VIEW = view('admin.promocode.modal.delete', [
			'promocode' => $promocode,
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
			'number' => [
				'required',
				'max:255',
				/*'unique:promocodes,number',*/
			],
			'city_id' => [
				'required',
			],
			'discount_id' => [
				'required',
				'numeric',
				'min:0',
				'not_in:0',
			],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'number' => 'Номер',
				'city_id' => 'Город',
				'discount_id' => 'Скидка',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		//$data = [];
		
		$promocode = new Promocode();
		$promocode->number = $this->request->number;
		$promocode->discount_id = $this->request->discount_id;
		$promocode->is_active = $this->request->is_active;
		if ($this->request->active_from_at_date) {
			$promocode->active_from_at = Carbon::parse($this->request->active_from_at_date . ' ' . $this->request->active_from_at_time)->format('Y-m-d H:i:s');
		}
		if ($this->request->active_to_at_date) {
			$promocode->active_to_at = Carbon::parse($this->request->active_to_at_date . ' ' . $this->request->active_to_at_time)->format('Y-m-d H:i:s');
		}
		//$promocode->data_json = $data;
		if (!$promocode->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		$promocode->cities()->sync((array)$this->request->city_id);
		
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

		$promocode = Promocode::find($id);
		if (!$promocode) return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
		
		$rules = [
			'number' => [
				'required',
				'max:255',
				/*'unique:promocodes,number,' . $id,*/
			],
			'city_id' => [
				'required',
			],
			'discount_id' => [
				'required',
				'numeric',
				'min:0',
				'not_in:0',
			],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'number' => 'Номер',
				'city_id' => 'Город',
				'discount_id' => 'Скидка',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		//$data = [];
		
		$promocode->number = $this->request->number;
		$promocode->discount_id = $this->request->discount_id;
		$promocode->is_active = $this->request->is_active;
		if ($this->request->active_from_at_date) {
			$promocode->active_from_at = Carbon::parse($this->request->active_from_at_date . ' ' . $this->request->active_from_at_time)->format('Y-m-d H:i:s');
		}
		if ($this->request->active_to_at_date) {
			$promocode->active_to_at = Carbon::parse($this->request->active_to_at_date . ' ' . $this->request->active_to_at_time)->format('Y-m-d H:i:s');
		}
		//$promocode->data_json = $data;
		if (!$promocode->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		$promocode->cities()->sync((array)$this->request->city_id);

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

		$promocode = Promocode::find($id);
		if (!$promocode) return response()->json(['status' => 'error', 'reason' => 'Промокод не найден']);
		
		if (!$promocode->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
}
