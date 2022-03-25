<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\FlightSimulator;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use PhpParser\Comment;
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

		$cities = $user->city
			? new Collection([$user->city])
			: City::where('version', $user->version)
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
		$user = \Auth::user();
		
		$locations = Location::where('is_active', true)
			->whereRelation('city', 'version', '=', $user->version)
			->with('simulators')
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
		$events = $events
			->whereRelation('city', 'version', '=', $user->version)
			->with(['dealPosition', 'user'])
			->get();

		//\Log::debug($events);
		
		$eventData = [];
		foreach ($events as $event) {
			$data = isset($locationData[$event->location_id][$event->flight_simulator_id]) ? $locationData[$event->location_id][$event->flight_simulator_id] : [];

			$color = '#ffffff';
			$allDay = false;
			
			switch ($event->event_type) {
				case 'deal':
					$balance = ($event->deal) ? $event->deal->balance() : 0;
					$title = $event->contractor ? $event->contractor->name . ' ' . HelpFunctions::formatPhone($event->deal->contractor->phone) . ' ' . $event->dealPosition->product->name : 'неизвестно';
					$allDay = false;
					if ($event->extra_time) {
						$title .= '(+' . $event->extra_time . ')';
					}
					if ($data) {
						$color = ($balance >= 0) ? $data['deal_paid'] : $data['deal'];
					}
				break;
				case 'shift_admin':
					$title = $event->user->fio();
					$allDay = true;
					if ($data && isset($data['shift_admin'])) {
						$color = $data['shift_admin'];
					}
				break;
				case 'shift_pilot':
					$title = $event->user->fio();
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
					'user' => $comment->updated_by ? $comment->updatedUser->fio() : $comment->createdUser->fio(),
					'date' => $comment->updated_at,
					'wasUpdated' => ($comment->created_at != $comment->updated_at) ? 'изменено' : 'создано',
				];
			}
			
			$eventData[] = [
				'eventType' => $event->event_type,
				'id' => $event->id,
				'title' => $title,
				'start' => Carbon::parse($event->start_at)->format('Y-m-d H:i'),
				'end' => !$allDay ? Carbon::parse($event->stop_at)->addMinutes($event->extra_time)->format('Y-m-d H:i') : '',
				'allDay' => $allDay,
				'backgroundColor' => $color,
				'borderColor' => $color,
				'notificationType' => $event->notification_type ?? '',
				'comments' => $commentData,
				'cityId' => $event->city_id,
				'locationId' => $event->location_id,
				'simulatorId' => $event->flight_simulator_id,
			];
		}
		
		return response()->json($eventData);
	}
	
	/**
	 * @param $positionId
	 * @param null $eventType
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add($positionId, $eventType = null)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();

		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		switch ($eventType) {
			case 'shift':
				$users = User::where('enable', true)
					->orderBy('lastname')
					->orderBy('name')
					->get();
				
				$VIEW = view('admin.event.modal.shift_add', [
					'users' => $users,
					'cities' => $cities,
					'cityId' => $this->request->city_id,
					'locationId' => $this->request->location_id,
					'simulatorId' => $this->request->simulator_id,
					'eventType' => $eventType,
					'eventDate' => $this->request->event_date,
				]);
			break;
			default:
				$position = DealPosition::find($positionId);
				if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция сделки не найдена']);
				
				$VIEW = view('admin.event.modal.add', [
					'position' => $position,
					'cities' => $cities,
				]);
			break;
		}

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @param bool $isShift
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($id, $isShift = false)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$isShift = filter_var($isShift, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (!$isShift) {
			$commentData = [];
			foreach ($event->comments as $comment) {
				$commentData[] = [
					'id' => $comment->id,
					'name' => $comment->name,
					'user' => $comment->updated_by ? $comment->updatedUser->fio() : $comment->createdUser->fio(),
					'date' => $comment->updated_at->format('d.m.Y H:i'),
					'wasUpdated' => ($comment->created_at != $comment->updated_at) ? 'изменено' : 'создано',
				];
			}
			
			$productTypes = ProductType::orderBy('name')
				->whereNotIn('alias', ['services'])
				->get();
			
			$VIEW = view('admin.event.modal.edit', [
				'event' => $event,
				'comments' => $commentData,
				'productTypes' => $productTypes,
				'cities' => $cities,
			]);
		} else {
			$users = User::where('enable', true)
				->orderBy('lastname')
				->orderBy('name')
				->get();
			
			$VIEW = view('admin.event.modal.shift_edit', [
				'event' => $event,
				'users' => $users,
				'cities' => $cities,
			]);
		}

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
	
		$eventType = $this->request->event_type ?? '';
		
		switch ($eventType) {
			case 'shift':
				$rules = [
					'user_id' => 'required',
				];
				$validator = Validator::make($this->request->all(), $rules)
					->setAttributeNames([
						'user_id' => 'Пользователь',
					]);
				if (!$validator->passes()) {
					return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
				}
			
				$userId = $this->request->user_id ?? 0;
				$cityId = $this->request->city_id ?? 0;
				$locationId = $this->request->location_id ?? 0;
				$simulatorId = $this->request->simulator_id ?? 0;
				
				if (!$locationId) {
					return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
				}
				
				if (!$cityId) {
					return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
				}
				
				if (!$simulatorId) {
					return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
				}
				
				$shiftUser = 'shift_' . $this->request->shift_user;
				$startAt = $this->request->event_date . ' ' . $this->request->start_at;
				$stopAt = $this->request->event_date . ' ' . $this->request->stop_at;
				
				if (Carbon::parse($startAt)->gte(Carbon::parse($stopAt))) {
					return response()->json(['status' => 'error', 'reason' => 'Время окончания смены должно быть больше времени начала']);
				}
				
				//\DB::connection()->enableQueryLog();
				$existingEvent = Event::where('event_type', $shiftUser)
					->where('start_at', '<', Carbon::parse($stopAt)->format('Y-m-d H:i'))
					->where('stop_at', '>', Carbon::parse($startAt)->format('Y-m-d H:i'))
					->first();
				//\Log::debug(\DB::getQueryLog());
				if ($existingEvent) {
					return response()->json(['status' => 'error', 'reason' => 'Пересечение со сменой ' . (($existingEvent->event_type == Event::EVENT_TYPE_SHIFT_ADMIN) ? 'администратора' : 'пилота') . ' ' . $existingEvent->user->fio()]);
				}
			break;
			default:
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
				
				$startAt = $this->request->start_at_date . ' ' . $this->request->start_at_time;
				$stopAt = $this->request->start_at_date . ' ' . $this->request->start_at_time;
			break;
		}

		try {
			\DB::beginTransaction();
			
			switch ($eventType) {
				case 'shift':
					$event = new Event();
					$event->event_type = $shiftUser;
					$event->start_at = Carbon::parse($startAt)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($stopAt)->format('Y-m-d H:i');
					$event->user_id = $userId;
					$event->city_id = $cityId;
					$event->location_id = $locationId;
					$event->flight_simulator_id = $simulatorId;
					$event->save();
				break;
				default:
					if (!$product->validateFlightDate(Carbon::parse($startAt)->format('Y-m-d H:i'))) {
						return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
					}
					
					$data = [
						'pilot_assessment' => $this->request->pilot_assessment ?? '',
						'admin_assessment' => $this->request->admin_assessment ?? '',
					];
					
					$event = new Event();
					$event->event_type = Event::EVENT_TYPE_DEAL;
					$event->deal_id = $position->deal ? $position->deal->id : 0;
					$event->deal_position_id = $position->id ?? 0;
					$event->city_id = $city->id;
					$event->location_id = $location->id;
					$event->flight_simulator_id = $simulator->id;
					$event->start_at = Carbon::parse($startAt)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($stopAt)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
					$event->extra_time = (int)$this->request->extra_time;
					$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
					$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
					$event->data_json = $data;
					$event->save();
					
					$commentText = $this->request->comment ?? '';
					if ($commentText) {
						$user = \Auth::user();
						
						$event->comments()->create([
							'name' => $commentText,
							'created_by' => $user->id ?? 0,
						]);
					}
				break;
			}

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			\Log::debug('500 - Event Update: ' . $e->getMessage());
			
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
		
		$userId = $this->request->user_id ?? 0;

		//\Log::debug($this->request);
		
		switch ($event->event_type) {
			case Event::EVENT_TYPE_DEAL:
				$position = DealPosition::find($event->deal_position_id);
				if (!$position) {
				return response()->json(['status' => 'error', 'reason' => 'Позиция сделки не найдена']);
				}
				
				if (!$product = $position->product) {
					return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
				}
			break;
			case Event::EVENT_TYPE_SHIFT_ADMIN:
			case Event::EVENT_TYPE_SHIFT_PILOT:
				if (!$event->user) {
					return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);
				}
			
				$shiftUser = 'shift_' . $this->request->shift_user;
				$startAt = Carbon::parse($event->start_at)->format('Y-m-d') . ' ' . $this->request->start_at_time;
				$stopAt = Carbon::parse($event->stop_at)->format('Y-m-d') . ' ' . $this->request->stop_at_time;

				if (Carbon::parse($startAt)->gte(Carbon::parse($stopAt))) {
					return response()->json(['status' => 'error', 'reason' => 'Время окончания смены должно быть больше времени начала']);
				}
	
				//\DB::connection()->enableQueryLog();
				$existingEvent = Event::where('event_type', $shiftUser)
					->where('start_at', '<', Carbon::parse($stopAt)->format('Y-m-d H:i'))
					->where('stop_at', '>', Carbon::parse($startAt)->format('Y-m-d H:i'))
					->where('id', '<>', $event->id)
					->first();
				//\Log::debug(\DB::getQueryLog());
				if ($existingEvent) {
					return response()->json(['status' => 'error', 'reason' => 'Пересечение со сменой ' . (($existingEvent->event_type == Event::EVENT_TYPE_SHIFT_ADMIN) ? 'администратора' : 'пилота') . ' ' . $existingEvent->user->fio()]);
				}
			break;
		}
		
		if ($this->request->source == Event::EVENT_SOURCE_DEAL) {
			if (!$location = $position->location) {
				return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
			}
			
			if (!$city = $position->city) {
				return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
			}
			
			if (!$simulator = $position->simulator) {
				return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
			}
		}
		
		try {
			\DB::beginTransaction();
			
			switch ($event->event_type) {
				case Event::EVENT_TYPE_DEAL:
					if ($this->request->source == Event::EVENT_SOURCE_DEAL) {
						$startAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
						$stopAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
						
						if (!$product->validateFlightDate($startAt)) {
							return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
						}
						
						$data = [
							'pilot_assessment' => $this->request->pilot_assessment ?? '',
							'admin_assessment' => $this->request->admin_assessment ?? '',
						];
						
						$event->city_id = $city ? $city->id : 0;
						$event->location_id = $location ? $location->id : 0;
						$event->flight_simulator_id = $simulator ? $simulator->id : 0;
						$event->extra_time = (int)$this->request->extra_time;
						$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
						$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
					}
					else if ($this->request->source == Event::EVENT_SOURCE_CALENDAR) {
						$startAt = Carbon::parse($this->request->start_at)->format('Y-m-d H:i');
						$stopAt = Carbon::parse($this->request->stop_at)->subMinutes($event->extra_time ?? 0)->format('Y-m-d H:i');
						
						if (!$product->validateFlightDate($startAt)) {
							return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
						}
					}
					$event->start_at = $startAt;
					$event->stop_at = $stopAt;
					if (isset($city)) {
						$event->city_id = $city->id;
					}
					if (isset($location)) {
						$event->location_id = $location->id;
					}
					if (isset($simulator)) {
						$event->flight_simulator_id = $simulator->id;
					}
					if (isset($data)) {
						$event->data_json = $data;
					}
					$event->save();
					
					$commentId = $this->request->comment_id ?? 0;
					$commentText = $this->request->comment ?? '';
					$user = \Auth::user();
					if ($commentText) {
						if ($commentId) {
							$comment = $event->comments->find($commentId);
							if (!$comment) {
								return response()->json(['status' => 'error', 'reason' => 'Комментарий не найден']);
							}
							$comment->name = $commentText;
							$comment->updated_by = $user->id ?? 0;
							$comment->save();
						}
						else {
							$event->comments()->create([
								'name' => $commentText,
								'created_by' => $user->id ?? 0,
							]);
						}
					}
				break;
				case Event::EVENT_TYPE_SHIFT_ADMIN:
				case Event::EVENT_TYPE_SHIFT_PILOT:
					$event->start_at = Carbon::parse($event->start_at)->format('Y-m-d') . ' ' . $this->request->start_at_time;
					$event->stop_at = Carbon::parse($event->stop_at)->format('Y-m-d') . ' ' . $this->request->stop_at_time;
					$event->user_id = $userId;
					$event->save();
				break;
			}

			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();

			\Log::debug('500 - Event Update: ' . $e->getMessage());

			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success']);
	}
	
	public function dragDrop($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		switch ($event->event_type) {
			case Event::EVENT_TYPE_DEAL:
				$position = DealPosition::find($event->deal_position_id);
				if (!$position) {
					return response()->json(['status' => 'error', 'reason' => 'Позиция сделки не найдена']);
				}
				
				if (!$product = $position->product) {
					return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
				}
			break;
			case Event::EVENT_TYPE_SHIFT_ADMIN:
			case Event::EVENT_TYPE_SHIFT_PILOT:
				if (!$event->user) {
					return response()->json(['status' => 'error', 'reason' => 'Пользователь не найден']);
				}
				
				$startAt = Carbon::parse($this->request->start_at)->format('Y-m-d') . ' ' . Carbon::parse($event->start_at)->format('H:i');
				$stopAt = Carbon::parse($this->request->start_at)->format('Y-m-d') . ' ' . Carbon::parse($event->stop_at)->format('H:i');

				/*\Log::debug($this->request);
				\Log::debug($startAt);
				\Log::debug($stopAt);*/

				//\DB::connection()->enableQueryLog();
				$existingEvent = Event::where('event_type', $event->event_type)
					->where('start_at', '<', Carbon::parse($stopAt)->format('Y-m-d H:i'))
					->where('stop_at', '>', Carbon::parse($startAt)->format('Y-m-d H:i'))
					->where('id', '<>', $event->id)
					->first();
				//\Log::debug(\DB::getQueryLog());
				if ($existingEvent) {
					return response()->json(['status' => 'error', 'reason' => 'Пересечение со сменой ' . (($existingEvent->event_type == Event::EVENT_TYPE_SHIFT_ADMIN) ? 'администратора' : 'пилота') . ' ' . $existingEvent->user->fio()]);
				}
			break;
		}
		
		try {
			\DB::beginTransaction();
			
			switch ($event->event_type) {
				case Event::EVENT_TYPE_DEAL:
					if ($this->request->source == Event::EVENT_SOURCE_DEAL) {
						$startAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
						$stopAt = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
						
						if (!$product->validateFlightDate($startAt)) {
							return response()->json(['status' => 'error', 'reason' => 'Некорректная дата полета для выбранного продукта']);
						}
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
				break;
				case Event::EVENT_TYPE_SHIFT_ADMIN:
				case Event::EVENT_TYPE_SHIFT_PILOT:
					$event->start_at = Carbon::parse($this->request->start_at)->format('Y-m-d') . ' ' . Carbon::parse($event->start_at)->format('H:i');
					$event->stop_at = Carbon::parse($this->request->stop_at)->subDay()->format('Y-m-d') . ' ' . Carbon::parse($event->stop_at)->format('H:i');
					$event->save();
				break;
			}
			
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
	
	public function deleteComment($id, $commentId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		$comment = $event->comments->find($commentId);
		if (!$comment) return response()->json(['status' => 'error', 'reason' => 'Комментарий не найден']);
		
		if (!$comment->delete()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'msg' => 'Комментарий успешно удален']);
	}
}
