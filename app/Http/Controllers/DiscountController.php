<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Discount;

class DiscountController extends Controller
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
		return view('admin.discount.index', [
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

		$discounts = Discount::orderBy('is_fixed')
			->orderBy('value')
			->get();

		$VIEW = view('admin.discount.list', ['discounts' => $discounts]);

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

		$discount = Discount::find($id);
		if (!$discount) return response()->json(['status' => 'error', 'reason' => 'Скидка не найдена']);

		$currencies = Currency::get();

		$VIEW = view('admin.discount.modal.edit', [
			'discount' => $discount,
			'currencies' => $currencies,
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

		$currencies = Currency::get();

		$VIEW = view('admin.discount.modal.add', [
			'currencies' => $currencies,
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

		$discount = Discount::find($id);
		if (!$discount) return response()->json(['status' => 'error', 'reason' => 'Скидка не найдена']);
		
		$VIEW = view('admin.discount.modal.delete', [
			'discount' => $discount,
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
			'value' => [
				'required',
				'max:255',
				Rule::unique('discounts')
					->where(function ($query) {
						return $query->where('value', $this->request->value)
							->where('is_fixed', $this->request->is_fixed);
					}),
			],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'value' => 'Значение'
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$discount = new Discount();
		$discount->value = $this->request->value;
		$discount->is_fixed = $this->request->is_fixed;
		$discount->currency_id = $this->request->currency_id;
		$discount->is_active = $this->request->is_active;
		if (!$discount->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $discount->id]);
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

		$discount = Discount::find($id);
		if (!$discount) return response()->json(['status' => 'error', 'reason' => 'Скидка не найдена']);
		
		$rules = [
			'value' => [
				'required',
				'max:255',
				Rule::unique('discounts')
					->where(function ($query) {
						return $query->where('value', $this->request->value)
							->where('is_fixed', $this->request->is_fixed)
							->where('id', '!=', $this->request->id);
					}),
			],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'value' => 'Значение'
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$discount->value = $this->request->value;
		$discount->is_fixed = $this->request->is_fixed;
		$discount->currency_id = $this->request->currency_id;
		$discount->is_active = $this->request->is_active;
		if (!$discount->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $discount->id]);
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

		$discount = Discount::find($id);
		if (!$discount) return response()->json(['status' => 'error', 'reason' => 'Скидка не найдена']);
		
		if (!$discount->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $discount->id]);
	}
}
