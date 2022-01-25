<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Contractor;
use App\Models\City;

class ContractorController extends Controller
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

		return view('admin.contractor.index', [
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
		
		$id = $this->request->id ?? 0;
		
		$contractors = Contractor::orderBy('created_at', 'desc');
		if ($this->request->filter_city_id) {
			$contractors = $contractors->where('city_id', $this->request->filter_city_id);
		}
		if ($this->request->search_contractor) {
			$contractors = $contractors->where(function ($query) {
				$query->where('name', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%');
			});
		}
		if ($id) {
			$contractors = $contractors->where('id', '<', $id);
		}
		$contractors = $contractors->limit(20)->get();
		
		$statuses = Status::where('is_active', true)
			->get();

		$VIEW = view('admin.contractor.list', [
			'contractors' => $contractors,
			'statuses' => $statuses,
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
		
		$contractor = Contractor::find($id);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрганет не найден']);
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.contractor.modal.edit', [
			'contractor' => $contractor,
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
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.contractor.modal.add', [
			'cities' => $cities,
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

		$rules = [
			'name' => 'required|min:3|max:50',
			'email' => 'required|email|unique_email',
			'phone' => 'required|valid_phone',
			'city_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'phone' => 'Телефон',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$data = [];
		
		$contractor = new Contractor();
		$contractor->name = $this->request->name;
		$contractor->lastname = $this->request->lastname;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone;
		$contractor->city_id = $this->request->city_id;
		if ($this->request->birthdate) {
			$contractor->birthdate = Carbon::parse($this->request->birthdate)->format('Y-m-d');
		}
		$contractor->source = Contractor::ADMIN_SOURCE;
		$contractor->is_active = (bool)$this->request->is_active;
		$contractor->is_subscribed = (bool)$this->request->is_subscribed;
		$contractor->user_id = $this->request->user()->id;
		$contractor->data_json = $data;
		if (!$contractor->save()) {
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

		$contractor = Contractor::find($id);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		
		$rules = [
			'name' => 'required|min:3|max:50',
			'email' => 'required|email|unique_email',
			'phone' => 'required|valid_phone',
			'city_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'phone' => 'Телефон',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$data = [];
		
		$contractor->name = $this->request->name;
		$contractor->lastname = $this->request->lastname;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone;
		$contractor->city_id = $this->request->city_id;
		if ($this->request->birthdate) {
			$contractor->birthdate = Carbon::parse($this->request->birthdate)->format('Y-m-d');
		}
		$contractor->is_active = (bool)$this->request->is_active;
		$contractor->is_subscribed = (bool)$this->request->is_subscribed;
		$contractor->data_json = $data;
		if (!$contractor->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
	
	public function search() {
		$q = $this->request->post('query');
		if (!$q) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$contractors = Contractor::where('is_active', true)
			/*->where(function($query) use ($q) {
				$query->where("name", "LIKE", "%{$q}%")
					->orWhere("lastname", "LIKE", "%{$q}%")
					->orWhere("email", "LIKE", "%{$q}%")
					->orWhere("phone", "LIKE", "%{$q}%");
			})*/
			->where("email", "LIKE", "%{$q}%")
			->orderBy("name")
			->orderBy("lastname")
			->get();
		
		$suggestions = [];
		foreach ($contractors as $contractor) {
			$suggestions[] = [
				'value' => $contractor->name . ($contractor->lastname ? ' ' . $contractor->lastname : '') . ' [' . $contractor->email . ($contractor->phone ? ', ' . $contractor->phone : '') . ($contractor->city ? ', ' . $contractor->city->name : '') . ']',
				'id' => $contractor->id,
				'data' => [
					'name' => $contractor->name,
					'lastname' => $contractor->lastname ?? '',
					'email' => $contractor->email ?? '',
					'phone' => $contractor->phone ?? '',
					'city_id' => $contractor->city ? $contractor->city->id : 0,
				],
			];
		}
		
		return response()->json(['suggestions' => $suggestions]);
	}
}
