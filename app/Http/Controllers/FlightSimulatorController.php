<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\FlightSimulator;
use App\Models\FlightSimulatorType;
use App\Models\Location;

class FlightSimulatorController extends Controller
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
		return view('admin/flightSimulator/index', [
		]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$flightSimulators = FlightSimulator::with(['simulatorType', 'location'])
			->get();

		$VIEW = view('admin.flightSimulator.list', ['flightSimulators' => $flightSimulators]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		$flightSimulator = FlightSimulator::find($id);
		if (!$flightSimulator) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$flightSimulatorTypes = FlightSimulatorType::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		$locations = Location::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		return view('admin/flightSimulator/modal/edit', [
			'flightSimulator' => $flightSimulator,
			'flightSimulatorTypes' => $flightSimulatorTypes,
			'locations' => $locations,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		$flightSimulatorTypes = FlightSimulatorType::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		$locations = Location::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		return view('admin/flightSimulator/modal/add', [
			'flightSimulatorTypes' => $flightSimulatorTypes,
			'locations' => $locations,
		]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		$flightSimulator = FlightSimulator::find($id);
		if (!$flightSimulator) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		return view('admin/flightSimulator/modal/delete', [
			'flightSimulator' => $flightSimulator,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		$rules = [
			'name' => 'required|max:255',
			'flight_simulator_type_id' => 'required|integer',
			'location_id' => 'required|integer',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'flight_simulator_type_id' => 'Тип авиатренажера',
				'location_id' => 'Локация',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$flightSimulator = new FlightSimulator();
		$flightSimulator->name = $this->request->name;
		$flightSimulator->flight_simulator_type_id = $this->request->flight_simulator_type_id;
		$flightSimulator->location_id = $this->request->location_id;
		$flightSimulator->is_active = $this->request->is_active;
		if (!$flightSimulator->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $flightSimulator->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		$flightSimulator = FlightSimulator::find($id);
		if (!$flightSimulator) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		$rules = [
			'name' => 'required|max:255',
			'flight_simulator_type_id' => 'required|integer',
			'location_id' => 'required|integer',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'flight_simulator_type_id' => 'Тип авиатренажера',
				'location_id' => 'Локация',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$flightSimulator->name = $this->request->name;
		$flightSimulator->flight_simulator_type_id = $this->request->flight_simulator_type_id;
		$flightSimulator->location_id = $this->request->location_id;
		$flightSimulator->is_active = $this->request->is_active;
		if (!$flightSimulator->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $flightSimulator->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		$flightSimulator = FlightSimulator::find($id);
		if (!$flightSimulator) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$flightSimulator->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $flightSimulator->id]);
	}
}
