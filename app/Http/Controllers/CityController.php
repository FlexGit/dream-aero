<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\City;
use App\Models\Employee;

class CityController extends Controller
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
		return view('admin.city.index', [
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

		$cities = City::get();
		
		$VIEW = view('admin.city.list', ['cities' => $cities]);

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

		$city = City::find($id);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$VIEW = view('admin.city.modal.edit', [
			'city' => $city,
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

		$VIEW = view('admin.city.modal.add', [
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
		
		$city = City::find($id);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$VIEW = view('admin.city.modal.show', [
			'city' => $city,
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

		$city = City::find($id);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$VIEW = view('admin.city.modal.delete', [
			'city' => $city,
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
			'name' => 'required|max:255|unique:cities,name',
			'alias' => 'required|min:2|max:3|unique:cities,alias',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$city = new City();
		$city->name = $this->request->name;
		$city->alias = $this->request->alias;
		$city->is_active = $this->request->is_active;
		if (!$city->save()) {
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

		$city = City::find($id);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$rules = [
			'name' => 'required|max:255|unique:cities,name,' . $id,
			'alias' => 'required|min:2|max:3|unique:cities,alias,' . $id,
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'alias' => 'Алиас',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$city->name = $this->request->name;
		$city->alias = $this->request->alias;
		$city->is_active = $this->request->is_active;
		if (!$city->save()) {
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

		$city = City::find($id);
		if (!$city) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$city->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getEmployeeList()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$employeeData = [];

		if ($this->request->cityId) {
			$city = City::find($this->request->cityId);
			if (!$city) {
				return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
			}
			
			foreach ($city->location ?? [] as $location) {
				$employees = $location->employee;
				foreach ($employees as $employee) {
					if (!$employee->is_active) continue;
					
					$employeeData[] = [
						'id' => $employee->id,
						'name' => $employee->name,
					];
				}
			}
		} else {
			$employees = Employee::where('is_active', true)
				->orderBy('name', 'asc')
				->get();
			foreach ($employees as $employee) {
				$employeeData[] = [
					'id' => $employee->id,
					'name' => $employee->name,
				];
			}
		}

		usort($employeeData, function($a, $b) {
			return $a['name'] <=> $b['name'];
		});

		return response()->json(['status' => 'success', 'employees' => $employeeData]);
	}
}
