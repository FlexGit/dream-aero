<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

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
		$user = \Auth::user();
		
		// Временный редирект для админов
		if (!$user->isSuperAdmin()) {
			return redirect('/contractor');
		}

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		return view('admin.event.index', [
			'cities' => $cities,
			'user' => $user,
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
		$cityId = $this->request->city_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->simulator_id ?? 0;
		
		//\Log::debug($cityId . ' - ' . $locationId . ' - ' . $simulatorId);
		
		$events = Event::whereDate('start_at', '>=', $startAt)
			->where(function ($query) use ($stopAt) {
				$query->whereDate('stop_at', '<=', $stopAt)
					->orWhereNull('stop_at');
			});
		if ($cityId) {
			$events = $events->where('city_id', $cityId);
		}
		if ($locationId) {
			$events = $events->where('location_id', $locationId);
		}
		if ($simulatorId) {
			$events = $events->where('flight_simulator_id', $simulatorId);
		}
		$events = $events->with(['dealPosition', 'user'])
			->get();

		//\Log::debug($events);
		
		$eventData = [];
		foreach ($events as $event) {
			$data = isset($locationData[$event->location_id][$event->flight_simulator_id]) ? $locationData[$event->location_id][$event->flight_simulator_id] : [];

			$color = '#ffffff';
			$allDay = false;
			
			switch ($event->event_type) {
				case 'deal':
					$balance = ($event->position && $event->position->deal) ? $event->position->deal->balance() : 0;
					$title = $event->deal->contractor->name . ' ' . HelpFunctions::formatPhone($event->deal->contractor->phone) . ' ' . $event->dealPosition->product->name;
					if ($event->extra_time) {
						$title .= ' (+' . $event->extra_time . ')';
					}
					if ($data) {
						if ($balance < 0 && isset($data['deal_notpaid'])) {
							$color = $data['deal_notpaid'];
						} else {
							$color = $data['deal_paid'];
						}
					}
				break;
				case 'shift_admin':
					$title = $event->user->name;
					$allDay = true;
					if ($data && isset($data['shift_admin'])) {
						$color = $data['shift_admin'];
					}
				break;
				case 'shift_pilot':
					$title = $event->user->name;
					$allDay = true;
					if ($data && isset($data['shift_pilot'])) {
						$color = $data['shift_pilot'];
					}
				break;
				case 'cleaning':
					$title = 'Уборка';
					$allDay = false;
					if ($data && isset($data['note'])) {
						$color = $data['note'];
					}
				break;
				case 'break':
					$title = 'Перерыв';
					$allDay = false;
					if ($data && isset($data['note'])) {
						$color = $data['note'];
					}
				break;
				case 'test_flight':
					$title = 'Тестовый полет';
					$allDay = false;
					if ($data && isset($data['note'])) {
						$color = $data['note'];
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
				'start' => Carbon::parse($event->start_at)->format('Y-m-d H:i'),
				'end' => !$allDay ? Carbon::parse($event->stop_at)->addMinutes($event->extra_time)->format('Y-m-d H:i') : '',
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
	 * @param $positionId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function add($positionId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$position = DealPosition::find($positionId);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция сделки не найдена']);

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$VIEW = view('admin.event.modal.add', [
			'position' => $position,
			'cities' => $cities,
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

		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);

		$productTypes = ProductType::orderBy('name')
			->whereNotIn('alias', ['services'])
			->get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->whereNotIn('alias', ['uae'])
			->get();

		$VIEW = view('admin.event.modal.edit', [
			'event' => $event,
			'productTypes' => $productTypes,
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
			'start_at_date' => 'required_if:source,deal|date',
			'start_at_time' => 'required_if:source,deal',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'start_at_date' => 'Дата начала полета',
				'start_at_time' => 'Время начала полета',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$position = DealPosition::find($this->request->position_id);
		if (!$position) {
			return response()->json(['status' => 'error', 'reason' => 'Позиция сделки не найдена']);
		}

		if (!$product = $position->product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if (!$location = $position->location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}

		if (!$city = $position->city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}

		if (!$simulator = $position->simulator) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
		}

		$startAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
		$stopAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');

		if (!$product->validateFlightDate($startAt)) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
		}

		$event = new Event();
		$event->event_type = Event::EVENT_TYPE_DEAL;
		$event->deal_id = $position->deal ? $position->deal->id : 0;
		$event->deal_position_id = $position->id ?? 0;
		$event->city_id = $city ? $city->id : 0;
		$event->location_id = $location ? $location->id : 0;
		$event->flight_simulator_id = $simulator ? $simulator->id : 0;
		$event->start_at = $startAt;
		$event->stop_at = $stopAt;
		$event->extra_time = (int)$this->request->extra_time;
		$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
		$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
		if (!$event->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	public function update($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'start_at_date' => 'required_if:source,deal|date',
			'start_at_time' => 'required_if:source,deal',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'start_at_date' => 'Дата начала полета',
				'start_at_time' => 'Время начала полета',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);

		$position = DealPosition::find($event->deal_position_id);
		if (!$position) {
			return response()->json(['status' => 'error', 'reason' => 'Позиция сделки не найдена']);
		}

		if (!$product = $position->product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if (!$location = $position->location) {
			return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
		}

		if (!$city = $position->city) {
			return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
		}

		if (!$simulator = $position->simulator) {
			return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
		}

		try {
			\DB::beginTransaction();

			if ($this->request->source == Event::EVENT_SOURCE_DEAL) {
				$startAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
				$stopAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');

				if (!$product->validateFlightDate($startAt)) {
					return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
				}

				$event->city_id = $city ? $city->id : 0;
				$event->location_id = $location ? $location->id : 0;
				$event->flight_simulator_id = $simulator ? $simulator->id : 0;
				$event->extra_time = (int)$this->request->extra_time;
				$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
				$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
			} elseif ($this->request->source == Event::EVENT_SOURCE_CALENDAR) {
				$startAt = Carbon::parse($this->request->start_at)->format('Y-m-d H:i');
				$stopAt = Carbon::parse($this->request->stop_at)->subMinutes($event->extra_time ?? 0)->format('Y-m-d H:i');

				if (!$product->validateFlightDate($startAt)) {
					return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
				}
			}
			$event->start_at = $startAt;
			$event->stop_at = $stopAt;
			$event->save();

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			\Log::debug('500 - Event Update: ' . $e->getMessage());

			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);

		if (!$event->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}

	/*public function clear()
	{
		if (!$this->request->user()->isSuperAdmin()) {
			redirect(route('eventIndex'));
		}

		Deal::where('user_id', 0)
			->delete();
	}*/
}
