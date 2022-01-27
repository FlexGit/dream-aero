<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Location;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;

class UserController extends Controller
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
		return view('admin.user.index', [
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

		$users = User::orderBy('city_id')
			->get();

		$roles = User::ROLES;
		
		$VIEW = view('admin.user.list', [
			'users' => $users,
			'roles' => $roles,
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
		
		$user = User::find($id);
		if (!$user) return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);
		
		$roles = User::ROLES;
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		$VIEW = view('admin.user.modal.edit', [
			'user' => $user,
			'roles' => $roles,
			'cities' => $cities,
			'locations' => $locations,
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
		
		$roles = User::ROLES;
		
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();

		$VIEW = view('admin.user.modal.add', [
			'roles' => $roles,
			'cities' => $cities,
			'locations' => $locations,
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
		
		$user = User::find($id);
		if (!$user) return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);
		
		$roles = User::ROLES;

		$VIEW = view('admin.user.modal.show', [
			'user' => $user,
			'roles' => $roles,
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
		
		$user = User::find($id);
		if (!$user) return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);
		
		$VIEW = view('admin.user.modal.delete', [
			'user' => $user,
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
			'lastname' => ['required', 'max:255'],
			'name' => ['required', 'max:255'],
			'middlename' => ['required', 'max:255'],
			'email' => ['required', 'email', 'unique:users,email,NULL,id,deleted_at,NULL'],
			'role' => ['required'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'lastname' => 'Фамилия',
				'name' => 'Имя',
				'middlename' => 'Отчество',
				'email' => 'E-mail',
				'role' => 'Роль',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$user = new User();
		$user->lastname = $this->request->lastname;
		$user->name = $this->request->name;
		$user->middlename = $this->request->middlename;
		$user->email = $this->request->email;
		$user->password = '';
		$user->role = $this->request->role;
		$user->city_id = $this->request->city_id ?? 0;
		$user->location_id = $this->request->location_id ?? 0;
		$user->enable = $this->request->enable;
		if (!$user->save()) {
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
		
		$user = User::find($id);
		if (!$user) return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);

		$rules = [
			'lastname' => ['required', 'max:255'],
			'name' => ['required', 'max:255'],
			'middlename' => ['required', 'max:255'],
			'email' => ['required', 'email', 'unique:users,email,' . $id . ',id,deleted_at,NULL'],
			'role' => ['required'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'lastname' => 'Фамилия',
				'name' => 'Имя',
				'middlename' => 'Отчество',
				'email' => 'E-mail',
				'role' => 'Роль',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$user->lastname = $this->request->lastname;
		$user->name = $this->request->name;
		$user->middlename = $this->request->middlename;
		$user->email = $this->request->email;
		$user->role = $this->request->role;
		$user->city_id = $this->request->city_id ?? 0;
		$user->location_id = $this->request->location_id ?? 0;
		$user->enable = $this->request->enable;
		if (!$user->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
	
	public function passwordResetNotification($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$user = User::find($id);
		if (!$user) return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);
		
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
		
		$user = User::find($id);
		if (!$user) return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);

		if (in_array($user->id, [1])) {
			return response()->json(['status' => 'error', 'reason' => 'Запрещено удаление данного пользователя']);
		}
		
		if (!$user->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
}
