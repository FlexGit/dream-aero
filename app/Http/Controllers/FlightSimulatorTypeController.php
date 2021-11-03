<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\FlightSimulatorType;

class FlightSimulatorTypeController extends Controller
{
	private $request;
	private $user;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->middleware('auth');
		
		$this->user = Auth::user();
		$this->request = $request;
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		return view('admin/flightSimulatorType/index', [
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$flightSimulatorTypes = FlightSimulatorType::get();

		$VIEW = view('admin.flightSimulatorType.list', ['flightSimulatorTypes' => $flightSimulatorTypes]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		$flightSimulatorType = FlightSimulatorType::find($id);
		if (!$flightSimulatorType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		return view('admin/flightSimulatorType/modal/edit', [
			'flightSimulatorType' => $flightSimulatorType,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		return view('admin/flightSimulatorType/modal/add');
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		$flightSimulatorType = FlightSimulatorType::find($id);
		if (!$flightSimulatorType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		return view('admin/flightSimulatorType/modal/delete', [
			'flightSimulatorType' => $flightSimulatorType,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		$rules = [
			'name' => 'required|max:255'
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование'
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$flightSimulatorType = new FlightSimulatorType();
		$flightSimulatorType->name = $this->request->name;
		$flightSimulatorType->is_active = $this->request->is_active;
		if (!$flightSimulatorType->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $flightSimulatorType->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		$flightSimulatorType = FlightSimulatorType::find($id);
		if (!$flightSimulatorType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$rules = [
			'name' => 'required|max:255'
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование'
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$flightSimulatorType->name = $this->request->name;
		$flightSimulatorType->is_active = $this->request->is_active;
		if (!$flightSimulatorType->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $flightSimulatorType->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		$flightSimulatorType = FlightSimulatorType::find($id);
		if (!$flightSimulatorType) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$flightSimulatorType->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $flightSimulatorType->id]);
	}
}
