<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Validator;
use App\Models\Notification;

class NotificationController extends Controller
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
		return view('admin.notification.index', [
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

		$notifications = Notification::orderby('id', 'desc')
			->get();

		$VIEW = view('admin.notification.list', [
			'notifications' => $notifications,
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
		
		$notification = Notification::find($id);
		if (!$notification) return response()->json(['status' => 'error', 'reason' => 'Уведомление не найдено']);

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.notification.modal.edit', [
			'notification' => $notification,
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

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$VIEW = view('admin.notification.modal.add', [
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

		$notification = Notification::find($id);
		if (!$notification) return response()->json(['status' => 'error', 'reason' => 'Уведомление не найдено']);
		
		$contractorCountSent = $contractorCountNotSent = 0;
		$contractors = Contractor::where('is_active', true)
			->whereNotNull('last_auth_at')
			->orderBy('id');
		if ($notification->city_id) {
			$contractors = $contractors->where('city_id', $notification->city_id);
		}
		$contractors = $contractors->get();
		foreach ($contractors as $contractor) {
			$notificationContractor = $notification->contractors->find($contractor->id);
			if ($notificationContractor) {
				++ $contractorCountSent;
			} else {
				++ $contractorCountNotSent;
			}
		}

		$VIEW = view('admin.notification.modal.show', [
			'notification' => $notification,
			'contractorCountSent' => $contractorCountSent,
			'contractorCountNotSent' => $contractorCountNotSent,
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

		$notification = Notification::find($id);
		if (!$notification) return response()->json(['status' => 'error', 'reason' => 'Уведомление не найдено']);
		
		$VIEW = view('admin.notification.modal.delete', [
			'notification' => $notification,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function confirmSend($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$notification = Notification::find($id);
		if (!$notification) return response()->json(['status' => 'error', 'reason' => 'Уведомление не найдено']);
		
		$contractorCount = 0;
		$contractors = Contractor::where('is_active', true)
			->whereNotNull('last_auth_at')
			->orderBy('id');
		if ($notification->city_id) {
			$contractors = $contractors->where('city_id', $notification->city_id);
		}
		$contractors = $contractors->get();
		foreach ($contractors as $contractor) {
			$notificationContractor = $notification->contractors->find($contractor->id);
			if ($notificationContractor) continue;
			
			++ $contractorCount;
		}
		
		$VIEW = view('admin.notification.modal.send', [
			'notification' => $notification,
			'contractorCount' => $contractorCount,
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
			'title' => ['required', 'max:255'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'title' => 'Заголовок',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$notification = new Notification();
		$notification->title = $this->request->title;
		$notification->description = $this->request->description;
		$notification->city_id = $this->request->city_id ?? 0;
		$notification->is_active = (bool)$this->request->is_active;
		if (!$notification->save()) {
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
		
		$notification = Notification::find($id);
		if (!$notification) return response()->json(['status' => 'error', 'reason' => 'Уведомление не найдено']);

		$rules = [
			'title' => ['required', 'max:255'],
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'title' => 'Заголовок',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$notification->title = $this->request->title;
		$notification->description = $this->request->description;
		$notification->city_id = $this->request->city_id ?? 0;
		$notification->is_active = (bool)$this->request->is_active;
		if (!$notification->save()) {
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

		$notification = Notification::find($id);
		if (!$notification) return response()->json(['status' => 'error', 'reason' => 'Уведомление не найдено']);

		if (!$notification->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		$notification->contractors()->sync([]);
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function send($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$notification = Notification::where('is_active', true)
			->find($id);
		if (!$notification) return response()->json(['status' => 'error', 'reason' => 'Уведомление не найдено или не активно']);
		
		$contractors = Contractor::where('is_active', true)
			->whereNotNull('last_auth_at')
			->orderBy('id');
		if ($notification->city_id) {
			$contractors = $contractors->where('city_id', $notification->city_id);
		}
		$contractors = $contractors->get();
		foreach ($contractors as $contractor) {
			$notificationContractor = $notification->contractors->find($contractor->id);
			if (!$notificationContractor) {
				$notification->contractors()->attach($contractor->id);
			}
		}
		
		return response()->json(['status' => 'success']);
	}
}
