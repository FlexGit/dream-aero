<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
		$cities = City::where('is_active', true)
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
		$contractors = Contractor::with(['city'])
		->orderBy('city_id', 'asc')
		->orderBy('name', 'asc');
		if ($this->request->filter_city_id) {
			$contractors = $contractors->where('city_id', $this->request->filter_city_id);
		}
		$contractors = $contractors->get();
		
		$VIEW = view('admin.contractor.list', [
			'contractors' => $contractors
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		$contractor = Contractor::find($id);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);

		return view('admin.contractor.modal.edit', [
			'contractor' => $contractor,
		]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		return view('admin.contractor.modal.add');
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		$rules = [
			'name' => 'required|min:3|max:50',
			'email' => 'required|email',
			'city_id' => 'required|numeric',
			'discount' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'city_id' => 'Город',
				'discount' => 'Скидка',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$data = [];
		
		$contractor = new Contractor();
		$contractor->name = $this->request->name;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone;
		$contractor->city_id = $this->request->city_id;
		$contractor->discount = $this->request->discount;
		$contractor->data_json = $data;
		if (!$contractor->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $contractor->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		$contractor = Contractor::find($id);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'name' => 'required|min:3|max:50',
			'email' => 'required|email',
			'city_id' => 'required|numeric',
			'discount' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				'city_id' => 'Город',
				'discount' => 'Скидка',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$data = [];
		
		$contractor->name = $this->request->name;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone;
		$contractor->city_id = $this->request->city_id;
		$contractor->discount = $this->request->discount;
		$contractor->data_json = $data;
		if (!$contractor->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $contractor->id]);
	}
}
