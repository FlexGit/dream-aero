<?php

namespace App\Http\Controllers;

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
		return view('admin/location/index', [
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$locations = Location::with(['city', 'legalEntity'])
			->get();

		$VIEW = view('admin.location.list', ['locations' => $locations]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$cities = City::where('is_active', true)
			->orderBy('name', 'asc')
			->get();
		
		$legalEntities = LegalEntity::where('is_active', true)
			->orderBy('name', 'asc')
			->get();
		
		return view('admin/location/modal/edit', [
			'location' => $location,
			'cities' => $cities,
			'legalEntities' => $legalEntities,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		$cities = City::where('is_active', true)
			->orderBy('name', 'asc')
			->get();
		
		$legalEntities = LegalEntity::where('is_active', true)
			->orderBy('name', 'asc')
			->get();

		return view('admin/location/modal/add', [
			'cities' => $cities,
			'legalEntities' => $legalEntities,
		]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirm($id)
	{
		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		return view('admin/location/modal/delete', [
			'location' => $location,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		$rules = [
			'name' => 'required|max:255',
			'legal_entity_id' => 'required|integer',
			'city_id' => 'required|integer',
			'address' => 'required',
			'working_hours' => 'required',
			'phone' => 'required',
			'email' => 'required|email',
			'scheme_file' => 'file|max:512|mimes:webp',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'legal_entity_id' => 'Юр.лицо',
				'city_id' => 'Город',
				'address' => 'Адрес',
				'working_hours' => 'Часы работы',
				'phone' => 'Телефон',
				'email' => 'E-mail',
				'scheme_file' => 'План-схема',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$location = new Location();
		$location->name = $this->request->name;
		$location->is_active = $this->request->is_active;
		$location->legal_entity_id = $this->request->legal_entity_id;
		$location->city_id = $this->request->city_id;
		$location->data_json = [
			'address' => $this->request->address,
			'working_hours' => $this->request->working_hours,
			'phone' => $this->request->phone,
			'email' => $this->request->email,
			'map_link' => $this->request->map_link,
			'skype' => $this->request->skype,
			'whatsapp' => $this->request->whatsapp,
			'scheme_file_path' => $this->request->scheme_file,
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
		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'name' => 'required|max:255',
			'legal_entity_id' => 'required|integer',
			'city_id' => 'required|integer',
			'address' => 'required',
			'working_hours' => 'required',
			'phone' => 'required',
			'email' => 'required|email',
			'scheme_file' => 'sometimes|image|max:1024|mimes:webp',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Наименование',
				'legal_entity_id' => 'Юр.лицо',
				'city_id' => 'Город',
				'address' => 'Адрес',
				'working_hours' => 'Часы работы',
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
		$location->is_active = $this->request->is_active;
		$location->legal_entity_id = $this->request->legal_entity_id;
		$location->city_id = $this->request->city_id;
		$location->data_json = [
			'address' => $this->request->address,
			'working_hours' => $this->request->working_hours,
			'phone' => $this->request->phone,
			'email' => $this->request->email,
			'map_link' => $this->request->map_link,
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
	public function delete($id)
	{
		$location = Location::find($id);
		if (!$location) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		if (!$location->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $location->id]);
	}
}
