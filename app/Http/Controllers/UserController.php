<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Location;
use App\Models\User;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

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
		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$roles = User::ROLES;

		return view('admin.user.index', [
			'cities' => $cities,
			'roles' => $roles,
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

		/*$id = $this->request->id ?? 0;*/

		$users = User::orderBy('lastname')->orderBy('name');
		if ($this->request->city_id) {
			$users = $users->where('city_id', $this->request->city_id);
		}
		if ($this->request->role) {
			$users = $users->where('role', $this->request->role);
		}
		/*if ($id) {
			$users = $users->where('id', '<', $id);
		}*/
		$users = $users/*->limit(20)*/->get();

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
			'role' => ['required'],
			'photo_file' => 'sometimes|image|max:1024',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'lastname' => 'Фамилия',
				'name' => 'Имя',
				'role' => 'Роль',
				'photo_file' => 'Фото',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$email = $this->request->email ?? '';
		if ($email) {
			$user = User::where('email', $email)
				->first();
			if ($user) {
				return response()->json(['status' => 'error', 'reason' => 'Пользователь с таким E-mail уже существует']);
			}
		}
		
		$data = [];
		
		$user = new User();
		
		if($photoFile = $this->request->file('photo_file')) {
			$isPhotoFileUploaded = $photoFile->move(public_path('upload/user/photo'), $photoFile->getClientOriginalName());
			if ($isPhotoFileUploaded) {
				$data['photo_file_path'] = HelpFunctions::webpImage(public_path('upload/user/photo'), $photoFile->getClientOriginalName());
			}
		}
		
		$user->lastname = $this->request->lastname;
		$user->name = $this->request->name;
		$user->middlename = $this->request->middlename ?? null;
		$user->email = $this->request->email;
		$user->password = '';
		$user->role = $this->request->role;
		$user->birthdate = $this->request->birthdate ? Carbon::parse($this->request->birthdate)->format('Y-m-d') : null;
		$user->phone = $this->request->phone ?? null;
		$user->position = $this->request->position ?? null;
		$user->version = $this->request->version;
		$user->city_id = $this->request->city_id ?? 0;
		$user->location_id = $this->request->location_id ?? 0;
		$user->is_reserved = (bool)$this->request->is_reserved;
		$user->is_official = (bool)$this->request->is_official;
		$user->enable = (bool)$this->request->enable;
		$user->data_json = $data;
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
			'role' => ['required'],
			'photo_file' => ['sometimes', 'image', 'max:1024'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'lastname' => 'Фамилия',
				'name' => 'Имя',
				'role' => 'Роль',
				'photo_file' => 'Фото',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$email = $this->request->email ?? '';
		if ($email) {
			$emailUser = User::where('email', $email)
				->first();
			if ($emailUser && $emailUser->id != $id) {
				return response()->json(['status' => 'error', 'reason' => 'Пользователь с таким E-mail уже существует']);
			}
		}
		
		$data = $user->data_json;
		
		if($photoFile = $this->request->file('photo_file')) {
			$isPhotoFileUploaded = $photoFile->move(public_path('upload/user/photo'), $photoFile->getClientOriginalName());
			if ($isPhotoFileUploaded) {
				$data['photo_file_path'] = HelpFunctions::webpImage('upload/user/photo/' . $photoFile->getClientOriginalName());
			}
		}
		
		$user->lastname = $this->request->lastname;
		$user->name = $this->request->name;
		$user->middlename = $this->request->middlename ?? '';
		$user->email = $email;
		$user->role = $this->request->role;
		$user->birthdate = $this->request->birthdate ? Carbon::parse($this->request->birthdate)->format('Y-m-d') : null;
		$user->phone = $this->request->phone ?? null;
		$user->position = $this->request->position ?? null;
		$user->version = $this->request->version;
		$user->city_id = $this->request->city_id ?? 0;
		$user->location_id = $this->request->location_id ?? 0;
		$user->is_reserved = (bool)$this->request->is_reserved;
		$user->is_official = (bool)$this->request->is_official;
		$user->enable = (bool)$this->request->enable;
		$user->data_json = $data;
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
