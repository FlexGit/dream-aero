<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\EmployeePosition;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\Employee;

class EmployeeController extends Controller
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
		return view('admin.employee.index', [
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

		$employees = Employee::get();

		$positions = EmployeePosition::get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.employee.list', [
			'employees' => $employees,
			'positions' => $positions,
			'cities' => $cities,
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
		
		$employee = Employee::find($id);
		if (!$employee) return response()->json(['status' => 'error', 'reason' => 'Сотрудник не найден']);

		$positions = EmployeePosition::get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.employee.modal.edit', [
			'employee' => $employee,
			'positions' => $positions,
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

		$positions = EmployeePosition::get();
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.employee.modal.add', [
			'positions' => $positions,
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

		$employee = Employee::find($id);
		if (!$employee) return response()->json(['status' => 'error', 'reason' => 'Сотрудник не найден']);

		$positions = EmployeePosition::get();

		$VIEW = view('admin.employee.modal.show', [
			'employee' => $employee,
			'positions' => $positions,
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

		$employee = Employee::find($id);
		if (!$employee) return response()->json(['status' => 'error', 'reason' => 'Сотрудник не найден']);
		
		$VIEW = view('admin.employee.modal.delete', [
			'employee' => $employee,
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
			'position_id' => ['required'],
			'location_id' => ['required'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'position_id' => 'Должность',
				'location_id' => 'Локация',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$employee = new Employee();
		$employee->name = $this->request->name;
		$employee->employee_position_id = $this->request->position_id;
		$employee->location_id = $this->request->location_id;
		$employee->is_active = $this->request->is_active;
		if (!$employee->save()) {
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
		
		$employee = Employee::find($id);
		if (!$employee) return response()->json(['status' => 'error', 'reason' => 'Сотрудник не найден']);

		$rules = [
			'name' => ['required', 'max:255'],
			'position_id' => ['required'],
			'location_id' => ['required'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'position_id' => 'Должность',
				'location_id' => 'Локация',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$employee->name = $this->request->name;
		$employee->employee_position_id = $this->request->position_id;
		$employee->location_id = $this->request->location_id;
		$employee->is_active = $this->request->is_active;
		if (!$employee->save()) {
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

		$employee = Employee::find($id);
		if (!$employee) return response()->json(['status' => 'error', 'reason' => 'Сотрудник не найден']);
		
		if (!$employee->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
}
