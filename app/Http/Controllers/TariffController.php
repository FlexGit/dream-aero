<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\TariffType;
use App\Models\Tariff;
use App\Models\City;

class TariffController extends Controller
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
		$tariffTypes = TariffType::where('is_active', true)
			->orderBy('name')
			->get();
		
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();

		return view('admin.tariff.index', [
			'tariffTypes' => $tariffTypes,
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

		$tariffs = Tariff::with(['tariffType', 'city'])
			->orderBy('city_id', 'asc')
			->orderBy('tariff_type_id', 'asc')
			->orderBy('duration', 'asc');
		if ($this->request->filter_city_id) {
			$tariffs = $tariffs->where('city_id', $this->request->filter_city_id);
		}
		if ($this->request->filter_tariff_type_id) {
			$tariffs = $tariffs->where('tariff_type_id', $this->request->filter_tariff_type_id);
		}
		$tariffs = $tariffs->get();

		$VIEW = view('admin.tariff.list', [
			'tariffs' => $tariffs
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

		$tariff = Tariff::find($id);
		if (!$tariff) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$tariffTypes = TariffType::where('is_active', true)
			->orderBy('name')
			->get();
		
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();

		$VIEW = view('admin.tariff.modal.edit', [
			'cities' => $cities,
			'tariff' => $tariff,
			'tariffTypes' => $tariffTypes,
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

		$tariff = Tariff::find($id);
		if (!$tariff) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$tariffTypes = TariffType::where('is_active', true)
			->orderBy('name')
			->get();
		
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.tariff.modal.show', [
			'cities' => $cities,
			'tariff' => $tariff,
			'tariffTypes' => $tariffTypes,
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

		$tariffTypes = TariffType::where('is_active', true)
			->orderBy('name')
			->get();

		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.tariff.modal.add', [
			'tariffTypes' => $tariffTypes,
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

		$tariff = Tariff::find($id);
		if (!$tariff) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$VIEW = view('admin.tariff.modal.delete', [
			'tariff' => $tariff,
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
			'tariff_type_id' => 'required|numeric',
			'duration' => 'required|numeric',
			'price' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'tariff_type_id' => 'Тип тарифа',
				'duration' => 'Длительность',
				'price' => 'Стоимость',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$tariff = new Tariff();
		$tariff->name = $this->request->name;
		$tariff->tariff_type_id = $this->request->tariff_type_id;
		$tariff->employee_id = $this->request->employee_id ?: 0;
		$tariff->city_id = $this->request->city_id ?: 0;
		$tariff->duration = $this->request->duration;
		$tariff->price = $this->request->price;
		$tariff->is_active = $this->request->is_active;
		$tariff->is_hit = $this->request->is_hit;
		$tariff->data_json = [
			'is_booking_allow' => (bool)$this->request->is_booking_allow,
			'is_certificate_allow' => (bool)$this->request->is_certificate_allow,
			'description' => $this->request->description ?: null,
			'icon' => $this->request->icon ?: null,
		];
		if (!$tariff->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $tariff->id]);
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

		$tariff = Tariff::find($id);
		if (!$tariff) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'name' => 'required|max:255',
			'tariff_type_id' => 'required|numeric',
			'duration' => 'required|numeric',
			'price' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'tariff_type_id' => 'Тип тарифа',
				'duration' => 'Длительность',
				'price' => 'Стоимость',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$tariff->name = $this->request->name;
		$tariff->tariff_type_id = $this->request->tariff_type_id;
		$tariff->employee_id = $this->request->employee_id ?: 0;
		$tariff->city_id = $this->request->city_id ?: 0;
		$tariff->duration = $this->request->duration;
		$tariff->price = $this->request->price;
		$tariff->is_active = $this->request->is_active;
		$tariff->is_hit = $this->request->is_hit;
		$tariff->data_json = [
			'is_booking_allow' => (bool)$this->request->is_booking_allow,
			'is_certificate_allow' => (bool)$this->request->is_certificate_allow,
			'description' => $this->request->description ?: null,
			'icon' => $this->request->icon ?: null,
		];
		if (!$tariff->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $tariff->id]);
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

		$tariff = Tariff::find($id);
		if (!$tariff) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$tariff->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $tariff->id]);
	}
}
