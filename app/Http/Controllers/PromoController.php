<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\Promo;

class PromoController extends Controller
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
		return view('admin.promo.index', [
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

		$promos = Promo::orderby('id', 'desc')
			->get();

		$VIEW = view('admin.promo.list', [
			'promos' => $promos,
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
		
		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.promo.modal.edit', [
			'promo' => $promo,
			'discounts' => $discounts,
			'cities' => $cities,
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

		$discounts = Discount::where('is_active', true)
			->orderBy('is_fixed')
			->orderBy('value')
			->get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$VIEW = view('admin.promo.modal.add', [
			'discounts' => $discounts,
			'cities' => $cities,
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

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		$VIEW = view('admin.promo.modal.show', [
			'promo' => $promo,
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

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
		
		$VIEW = view('admin.promo.modal.delete', [
			'promo' => $promo,
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
			'name' => ['required', 'max:255'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$promo = new Promo();
		$promo->name = $this->request->name;
		$promo->discount_id = $this->request->discount_id ?? 0;
		$promo->city_id = $this->request->city_id ?? 0;
		$promo->preview_text = $this->request->preview_text ?? '';
		$promo->detail_text = $this->request->detail_text ?? '';
		$promo->is_published = (bool)$this->request->is_published;
		$promo->is_active = (bool)$this->request->is_active;
		$promo->active_from_at = Carbon::parse($this->request->active_from_at)->format('Y-m-d') ?? null;
		$promo->active_to_at = Carbon::parse($this->request->active_to_at)->format('Y-m-d') ?? null;
		if (!$promo->save()) {
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
		
		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);

		$rules = [
			'name' => ['required', 'max:255'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$promo->name = $this->request->name;
		$promo->discount_id = $this->request->discount_id ?? 0;
		$promo->city_id = $this->request->city_id ?? 0;
		$promo->preview_text = $this->request->preview_text ?? '';
		$promo->detail_text = $this->request->detail_text ?? '';
		$promo->is_published = (bool)$this->request->is_published;
		$promo->is_active = (bool)$this->request->is_active;
		$promo->active_from_at = Carbon::parse($this->request->active_from_at)->format('Y-m-d') ?? null;
		$promo->active_to_at = Carbon::parse($this->request->active_to_at)->format('Y-m-d') ?? null;
		if (!$promo->save()) {
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

		$promo = Promo::find($id);
		if (!$promo) return response()->json(['status' => 'error', 'reason' => 'Акция не найдена']);
		
		if (!$promo->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
}
