<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Order;
use App\Models\Location;

use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
	private $request;

	/**
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Календарь
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		/*$cities = City::orderBy('name')
			->get();*/
		
		$locations = Location::orderBy('name')
			->get();
		/*foreach ($locations as $location) {
			break;
		}*/

		return view('admin.event.index', [
			/*'cities' => $cities,*/
			'locations' => $locations,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		$locations = Location::with('simulators')
			->get();
		$locationData = [];
		foreach ($locations as $location) {
			foreach ($location->simulators as $simulator) {
				$locationData[$location->id][$simulator->id] = json_decode($simulator->pivot->data_json, true);
			}
		}
		
		$startAt = $this->request->start ?? '';
		$stopAt = $this->request->end ?? '';
		$events = Event::whereDate('start_at', '>=', $startAt)
			->where(function ($query) use ($stopAt) {
				$query->whereDate('stop_at', '<=', $stopAt)
					->orWhereNull('stop_at');
			})
			->with(['deal', 'employee'])
			->get();

		$eventData = [];
		foreach ($events as $event) {
			$data = isset($locationData[$event->location_id][$event->flight_simulator_id]) ? $locationData[$event->location_id][$event->flight_simulator_id] : [];
			
			$color = '#ffffff';
			$allDay = false;
			
			switch ($event->event_type) {
				case 'deal':
					$title = $event->deal->contractor->name . ' ' . HelpFunctions::formatPhone($event->deal->contractor->phone);
					$color = /*$event->deal ? $data['events']['deal_paid'] :*/ $data['events']['deal_notpaid'];
				break;
				case 'shift_admin':
					$title = $event->employee->name;
					$allDay = true;
					if (isset($data['events']['shift_admin'])) {
						$color = $data['events']['shift_admin'];
					}
				break;
				case 'shift_pilot':
					$title = $event->employee->name;
					$allDay = true;
					if (isset($data['events']['shift_pilot'])) {
						$color = $data['events']['shift_pilot'];
					}
				break;
				case 'cleaning':
					$title = 'Уборка';
					$allDay = false;
					if (isset($data['events']['note'])) {
						$color = $data['events']['note'];
					}
				break;
				case 'break':
					$title = 'Перерыв';
					$allDay = false;
					if (isset($data['events']['note'])) {
						$color = $data['events']['note'];
					}
				break;
				case 'test_flight':
					$title = 'Тестовый полет';
					$allDay = false;
					if (isset($data['events']['note'])) {
						$color = $data['events']['note'];
					}
				break;
			}
			
			$commentData = [];
			foreach ($event->comments ?? [] as $comment) {
				$commentData[] = [
					'name' => $comment->name,
					'user' => $comment->updated_by ? $comment->updatedUser->name : $comment->createdUser->name,
					'date' => $comment->updated_at,
					'wasUpdated' => ($comment->created_at != $comment->updated_at) ? 'изменено' : 'создано',
				];
			}
			
			$eventData[] = [
				'id' => $event->id,
				'title' => $title,
				'start' => Carbon::parse($event->start_at)->format('Y-m-d H:i:s'),
				'end' => !$allDay ? Carbon::parse($event->stop_at)->format('Y-m-d H:i:s') : '',
				'allDay' => $allDay,
				'backgroundColor' => $color,
				'borderColor' => $color,
				'notificationType' => $event->notification_type ?? '',
				'comments' => $commentData,
			];
		}
		
		return response()->json($eventData);
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

		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		return view('admin.event.modal.edit', [
			'event' => $event,
		]);
	}
	
	public function clear()
	{
		if (!$this->request->user()->isSuperAdmin()) {
			redirect(route('eventIndex'));
		}

		Order::where('user_id', 0)
			->delete();
		Deal::where('user_id', 0)
			->delete();
	}
}
