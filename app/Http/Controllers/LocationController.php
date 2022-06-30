<?php

namespace App\Http\Controllers;

use App\Models\FlightSimulator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Location;
use App\Models\City;
use App\Models\LegalEntity;

class LocationController extends Controller
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
		
		$legalEntities = LegalEntity::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		return view('admin.location.index', [
			'cities' => $cities,
			'legalEntities' => $legalEntities,
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

		$locations = Location::with(['city', 'legalEntity']);
		if ($this->request->filter_city_id) {
			$locations = $locations->where('city_id', $this->request->filter_city_id);
		}
		if ($this->request->filter_legal_entity_id) {
			$locations = $locations->where('legal_entity_id', $this->request->filter_legal_entity_id);
		}
		$locations = $locations->get();

		$VIEW = view('admin.location.list', ['locations' => $locations]);

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

		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		
		$cities = City::where('is_active', true)
			->orderBy('name', 'asc')
			->get();
		
		$legalEntities = LegalEntity::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		$simulators = FlightSimulator::get();
		
		$VIEW = view('admin.location.modal.edit', [
			'location' => $location,
			'cities' => $cities,
			'legalEntities' => $legalEntities,
			'simulators' => $simulators,
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

		$cities = City::where('is_active', true)
			->orderBy('name', 'asc')
			->get();
		
		$legalEntities = LegalEntity::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		$VIEW = view('admin.location.modal.add', [
			'cities' => $cities,
			'legalEntities' => $legalEntities,
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
		
		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		
		$VIEW = view('admin.location.modal.show', [
			'location' => $location,
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

		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		
		$VIEW = view('admin.location.modal.delete', [
			'location' => $location,
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
			'name' => 'required|max:255|unique:locations,name',
			'name_en' => 'required|max:255|unique:locations,name_en',
			'alias' => 'required|min:2|max:25|unique:locations,alias',
			'legal_entity_id' => 'required|integer',
			'city_id' => 'required|integer',
			'address' => 'required',
			'address_en' => 'required',
			'working_hours' => 'required',
			'working_hours_en' => 'required',
			'phone' => 'required',
			'email' => 'required|email',
			'scheme_file' => 'sometimes|image|max:512|mimes:jpg,jpeg,png,webp',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'name_en' => 'Наименование (англ.)',
				'alias' => 'Алиас',
				'legal_entity_id' => 'Юр.лицо',
				'city_id' => 'Город',
				'address' => 'Адрес',
				'address_en' => 'Адрес (англ.)',
				'working_hours' => 'Часы работы',
				'working_hours_en' => 'Часы работы (англ.)',
				'phone' => 'Телефон',
				'email' => 'E-mail',
				'scheme_file' => 'План-схема',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$isFileUploaded = false;
		if($file = $this->request->file('scheme_file')) {
			$isFileUploaded = $file->move(public_path('upload/scheme'), $file->getClientOriginalName());
		}

		$location = new Location();
		$location->name = $this->request->name;
		$location->name_en = $this->request->name_en;
		$location->alias = $this->request->alias;
		$location->is_active = $this->request->is_active;
		$location->legal_entity_id = $this->request->legal_entity_id;
		$location->city_id = $this->request->city_id;
		$location->data_json = [
			'address' => $this->request->address,
			'address_en' => $this->request->address_en,
			'working_hours' => $this->request->working_hours,
			'working_hours_en' => $this->request->working_hours_en,
			'phone' => $this->request->phone,
			'email' => $this->request->email,
			'map_link' => $this->request->map_link,
			'review_map_link' => $this->request->review_map_link,
			'skype' => $this->request->skype,
			'whatsapp' => $this->request->whatsapp,
			'scheme_file_path' => $isFileUploaded ? 'scheme/' . $file->getClientOriginalName() : '',
		];
		if (!$location->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $location->id]);
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

		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		
		$rules = [
			'name' => 'required|max:255|unique:locations,name,' . $id,
			'name_en' => 'required|max:255|unique:locations,name_en,' . $id,
			'alias' => 'required|min:2|max:25|unique:locations,alias,' . $id,
			'legal_entity_id' => 'required|integer',
			'city_id' => 'required|integer',
			'address' => 'required',
			'address_en' => 'required',
			'working_hours' => 'required',
			'working_hours_en' => 'required',
			'phone' => 'required',
			'email' => 'required|email',
			'scheme_file' => 'sometimes|image|max:1024|mimes:jpg,jpeg,png,webp',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'name_en' => 'Наименование (англ.)',
				'alias' => 'Алиас',
				'legal_entity_id' => 'Юр.лицо',
				'city_id' => 'Город',
				'address' => 'Адрес',
				'address_en' => 'Адрес (англ.)',
				'working_hours_en' => 'Часы работы (англ.)',
				'phone' => 'Телефон',
				'email' => 'E-mail',
				'scheme_file' => 'План-схема',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$isFileUploaded = false;
		if($file = $this->request->file('scheme_file')) {
			$isFileUploaded = $file->move(public_path('upload/scheme'), $file->getClientOriginalName());
		}
			
		$location->name = $this->request->name;
		$location->name_en = $this->request->name_en;
		$location->alias = $this->request->alias;
		$location->is_active = $this->request->is_active;
		$location->legal_entity_id = $this->request->legal_entity_id;
		$location->city_id = $this->request->city_id;
		
		$data = $location->data_json;
		$data['address'] = $this->request->address;
		$data['address_en'] = $this->request->address_en;
		$data['working_hours'] = $this->request->working_hours;
		$data['working_hours_en'] = $this->request->working_hours_en;
		$data['phone'] = $this->request->phone;
		$data['email'] = $this->request->email;
		$data['map_link'] = $this->request->map_link;
		$data['review_map_link'] = $this->request->review_map_link;
		$data['skype'] = $this->request->skype;
		$data['whatsapp'] = $this->request->whatsapp;
		$data['scheme_file_path'] = $isFileUploaded ? 'scheme/' . $file->getClientOriginalName() : '';
		$location->data_json = $data;

		if (!$location->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		$colors = $this->request->color ?? [];
		$letterNames = $this->request->letter_name ?? [];
		$locationSimulatorData = [];
		foreach (array_keys($this->request->simulator) ?? [] as $simulatorId) {
			$locationSimulatorData[$simulatorId]['data_json'] = [];
			foreach ($colors[$simulatorId] as $eventType => $color) {
				$locationSimulatorData[$simulatorId]['data_json'][$eventType] = $color ?? '';
			}
			$locationSimulatorData[$simulatorId]['data_json']['letter_name'] = isset($letterNames[$simulatorId]) ? $letterNames[$simulatorId] : '';
			$locationSimulatorData[$simulatorId]['data_json'] = json_encode($locationSimulatorData[$simulatorId]['data_json'], JSON_UNESCAPED_UNICODE);
		}

		$location->simulators()->sync($locationSimulatorData);
		
		return response()->json(['status' => 'success', 'id' => $location->id]);
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

		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		
		if (!$location->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $location->id]);
	}
}
