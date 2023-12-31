<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\City;
use App\Models\Content;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\Location;
use App\Models\LockingPeriod;
use App\Models\ProductType;
use App\Models\Promo;
use App\Models\Status;
use App\Models\User;
use App\Repositories\StatusRepository;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Validator;

class EventController extends Controller
{
	private $request;
	private $statusRepo;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request, StatusRepository $statusRepo)
	{
		$this->request = $request;
		$this->statusRepo = $statusRepo;
	}

	/**
	 * Календарь
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$user = \Auth::user();
		
		$cities = $user->city
			? new Collection([$user->city])
			: City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$upcomingEvents = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->where('start_at', '>=', Carbon::now()->addDays(1)->startOfDay()->format('Y-m-d H:i:s'))
			->where('start_at', '<=', Carbon::now()->addDays(1)->endOfDay()->format('Y-m-d H:i:s'))
			->where('is_notified', false)
			->get();
		if (!$user->isSuperAdmin() && $user->city) {
			$upcomingEvents = $upcomingEvents->where('city_id', $user->city->id);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'calendar');
		
		return view('admin.event.index', [
			'cities' => $cities,
			'upcomingEvents' => $upcomingEvents,
			'user' => $user,
			'page' => $page,
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
		
		$eventData = [];
		/** @var Event[] $events */
		foreach ($events as $event) {
			$data = isset($locationData[$event->location_id][$event->flight_simulator_id]) ? $locationData[$event->location_id][$event->flight_simulator_id] : [];

			$color = '#ffffff';
			$allDay = false;
			
			switch ($event->event_type) {
				case Event::EVENT_TYPE_DEAL:
					$deal = $event->deal;
					$balance = $deal ? $deal->balance() : 0;
					$position = $event->dealPosition;
					$product = $position ? $position->product : null;
					$certificate = $position ? $position->certificate : null;
					
					// контактное лицо
					$title = $deal ? $deal->name . ' ' . HelpFunctions::formatPhone($deal->phone) : '';
					
					// тариф
					$title .= $product ? '. ' . $product->name : '';

					// доп. время
					if ($event->extra_time) {
						$title .= ' (+' . $event->extra_time . ')';
					}
					
					$amount = 0;
					$paymentMethodNames = $billStatusAliases = [];
					$promo = $promocode = null;
					
					// инфа о сертификате
					if ($certificate) {
						$certificateData = $certificate->data_json;
						if (isset($certificateData['amount'])) {
							$amount = $certificateData['amount'];
							if (isset($certificateData['payment_method'])) {
								$paymentMethodNames[] = $certificateData['payment_method'];
							}
						} else {
							$certificatePurchasePosition = DealPosition::where('is_certificate_purchase', true)
								->where('certificate_id', $certificate->id)
								->first();
							if ($certificatePurchasePosition) {
								$amount = $certificatePurchasePosition->amount;
								$bills = $certificatePurchasePosition->bills;
								foreach ($bills as $bill) {
									$billStatusAliases[] = $bill->status ? $bill->status->alias : Bill::NOT_PAYED_STATUS;
									$paymentMethod = $bill->paymentMethod;
									if ($paymentMethod) {
										$paymentMethodNames[] = $paymentMethod->name;
									}
								}
								$promo = $certificatePurchasePosition->promo;
								$promocode = $certificatePurchasePosition->promocode;
							}
						}
						$title .= '. Сертификат ' . $certificate->number . ' от ' . Carbon::parse($certificate->created_at)->format('d.m.Y') . ' за ' . ($amount ?? 0) . ' руб';
						// способ оплаты
						if ($paymentMethodNames) {
							$title .= '. ' . implode(', ', $paymentMethodNames);
						}
					} else {
						if ($position) {
							$bills = $position->bills;
							foreach ($bills as $bill) {
								$billStatusAliases[] = $bill->status ? $bill->status->alias : Bill::NOT_PAYED_STATUS;
								$paymentMethod = $bill->paymentMethod;
								if ($paymentMethod) {
									$paymentMethodNames[] = $paymentMethod->name;
								}
							}
							$promo = $position->promo;
							$promocode = $position->promocode;

							// стоимость позиции
							$title .= '. Стоимость ' . $position->amount() . ' руб';
							
							// сумма оплаченных счетов
							$title .= '. Оплачено ' . $position->billPayedAmount() . ' руб';

							// способы оплаты
							if ($paymentMethodNames) {
								$title .= '. ' . implode(', ', $paymentMethodNames);
							}
						}
					}
					// инфа об акции
					if (isset($promo)) {
						$title .= '. Акция ' . $promo->name . ' (' . ($promo->discount ? $promo->discount->valueFormatted() : '') . ')';
					}
					// инфа о промокоде
					if (isset($promocode)) {
						$title .= '. Промокод ' . $promocode->number . ' (' . ($promocode->discount ? $promocode->discount->valueFormatted() : '') . ')';
					}
					// время работы платформы от админа
					if ($event->simulator_up_at || $event->simulator_down_at) {
						$title .= '. Платформа ' . ($event->simulator_up_at ? Carbon::parse($event->simulator_up_at)->format('H:i') : '') . ' - ' . ($event->simulator_down_at ? Carbon::parse($event->simulator_down_at)->format('H:i') : '');
					}
					// спонтанный полет
					if ($event->is_unexpected_flight) {
						$title .= '. СП';
					}
					// повторный полет
					if ($event->is_repeated_flight) {
						$title .= '. ПП';
					}
					// описание
					if ($event->description) {
						$title .= '. ' . $event->description;
					}
					// инфа о прикрепленном к событию документе
					if (is_array($event->data_json) && array_key_exists('doc_file_path', $event->data_json) && $event->data_json['doc_file_path']) {
						$title .= '. Прикреплен документ';
					}
					
					$allDay = false;
					
					if ($data) {
						// если к позиции привязан счет, то он должен быть оплачен
						// иначе проверяем чтобы вся сделка была оплачена
						if ($billStatusAliases) {
							$color = (in_array(Bill::NOT_PAYED_STATUS, $billStatusAliases) || in_array(Bill::PAYED_PROCESSING_STATUS, $billStatusAliases)) ? $data['deal'] : $data['deal_paid'];
						} else {
							$color = ($balance >= 0) ? $data['deal_paid'] : $data['deal'];
						}
					}
				break;
				case Event::EVENT_TYPE_SHIFT_ADMIN:
					$title = $event->start_at->format('H:i') . '-' . $event->stop_at->format('H:i') . ' ' . ($event->user ? $event->user->fioFormatted() : '');
					$allDay = true;
					if ($data && isset($data['shift_admin'])) {
						$color = $data['shift_admin'];
					}
				break;
				case Event::EVENT_TYPE_SHIFT_PILOT:
					$title = $event->start_at->format('H:i') . '-' . $event->stop_at->format('H:i') . ' ' . ($event->user ? $event->user->fioFormatted() : '');
					$allDay = true;
					if ($data && isset($data['shift_pilot'])) {
						$color = $data['shift_pilot'];
					}
				break;
				case Event::EVENT_TYPE_CLEANING:
					$title = Event::EVENT_TYPES[Event::EVENT_TYPE_CLEANING];
					$allDay = false;
					if ($data && isset($data[Event::EVENT_TYPE_CLEANING])) {
						$color = $data[Event::EVENT_TYPE_CLEANING];
					}
				break;
				case Event::EVENT_TYPE_BREAK:
					$title = Event::EVENT_TYPES[Event::EVENT_TYPE_BREAK];
					$allDay = false;
					if ($data && isset($data[Event::EVENT_TYPE_BREAK])) {
						$color = $data[Event::EVENT_TYPE_BREAK];
					}
				break;
				case Event::EVENT_TYPE_TEST_FLIGHT:
					$title = Event::EVENT_TYPES[Event::EVENT_TYPE_TEST_FLIGHT];
					$title .= $event->testPilot ? ' ' . $event->testPilot->fioFormatted() : '';
					$allDay = false;
					if ($data && isset($data[Event::EVENT_TYPE_TEST_FLIGHT])) {
						$color = $data[Event::EVENT_TYPE_TEST_FLIGHT];
					}
				break;
				case Event::EVENT_TYPE_USER_FLIGHT:
					$title = Event::EVENT_TYPES[Event::EVENT_TYPE_USER_FLIGHT];
					$title .= $event->employee ? ' ' . $event->employee->fioFormatted() : '';
					$allDay = false;
					if ($data && isset($data[Event::EVENT_TYPE_USER_FLIGHT])) {
						$color = $data[Event::EVENT_TYPE_USER_FLIGHT];
					}
				break;
			}
			
			$commentData = [];
			foreach ($event->comments ?? [] as $comment) {
				$userName = '';
				if ($comment->updated_by) {
					$userName = $comment->updatedUser ? $comment->updatedUser->fio() : '';
				} elseif ($comment->created_by) {
					$userName = $comment->createdUser ? $comment->createdUser->fio() : '';
				}
				$commentData[] = [
					'name' => $comment->name,
					'user' => $userName,
					'date' => $comment->updated_at->format('d.m.Y H:i'),
					'wasUpdated' => ($comment->created_at != $comment->updated_at) ? 'изменено' : 'создано',
				];
			}
			
			$eventData[] = [
				'eventType' => $event->event_type,
				'className' => !in_array($event->event_type, [Event::EVENT_TYPE_CLEANING && Event::EVENT_TYPE_BREAK]) ? (($event->simulator_down_at && Carbon::parse($event->simulator_down_at)->lt(Carbon::now())) ? 'fc-event-past' : 'fc-event-not-past') : '',
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
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
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
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		$position = $event->dealPosition;
		$bills = $position ? $position->bills : [];
		$promo = $position ? $position->promo : null;
		$employee = $event->employee;
		
		$paidSum = 0;
		foreach ($bills as $bill) {
			if (!$bill->status || in_array($bill->status->alias, [Bill::CANCELED_STATUS, Bill::NOT_PAYED_STATUS])) continue;
			
			$paidSum += $bill->amount;
		}
		
		$pilotSum = $event->nominal_price;
		if ($event->event_type == Event::EVENT_TYPE_USER_FLIGHT) {
			if ($employee->isPilot()) {
				$pilotSum = 0;
			} else {
				$pilotSum = $pilotSum * 0.8;
			}
		} elseif ($event->event_type == Event::EVENT_TYPE_TEST_FLIGHT) {
			$pilotSum = 0;
		} else {
			if ($promo && $promo->alias == Promo::DIRECTOR_ALIAS) {
				$pilotSum = $pilotSum * 0.8;
			}
		}
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();
		
		$isShift = filter_var($isShift, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (!$isShift) {
			$commentData = [];
			foreach ($event->comments as $comment) {
				$userName = '';
				if ($comment->updated_by) {
					$userName = $comment->updatedUser ? $comment->updatedUser->fio() : '';
				} elseif ($comment->created_by) {
					$userName = $comment->createdUser ? $comment->createdUser->fio() : '';
				}
				$commentData[] = [
					'id' => $comment->id,
					'name' => $comment->name,
					'user' => $userName,
					'date' => $comment->updated_at->format('d.m.Y H:i'),
					'wasUpdated' => ($comment->created_at != $comment->updated_at) ? 'изменено' : 'создано',
				];
			}
			
			$productTypes = ProductType::orderBy('name')
				->whereNotIn('alias', ['services'])
				->get();
			
			$shifts = Event::where('event_type', Event::EVENT_TYPE_SHIFT_PILOT)
				->where('city_id', $event->city_id)
				->where('location_id', $event->location_id)
				->where('flight_simulator_id', $event->flight_simulator_id)
				->where('start_at', '>=', Carbon::parse($event->start_at)->format('Y-m-d'))
				->where('stop_at', '<=', Carbon::parse($event->start_at)->addDay()->format('Y-m-d'))
				->orderBy('start_at')
				->get();

			$pilots = User::where('role', User::ROLE_PILOT)
				->orderByRaw("FIELD(location_id, $event->location_id) DESC")
				->orderByRaw("FIELD(city_id, $event->city_id) DESC")
				->orderByDesc('location_id')
				->orderBy('lastname')
				->orderBy('name')
				->get();
			$pilotItems = [];
			foreach ($pilots as $pilot) {
				$cityName = $pilot->city ? $pilot->city->name : '- без города -';
				$locationName = $pilot->location ? $pilot->location->name : '- без локации -';
				$pilotItems[$cityName][$locationName][] = $pilot;
			}
			
			$employees = User::where('enable', true)
				->orderBy('lastname')
				->orderBy('name')
				->get();
			
			$statuses = $this->statusRepo->getList(Status::STATUS_TYPE_CONTRACTOR);
			
			$VIEW = view('admin.event.modal.edit', [
				'event' => $event,
				'comments' => $commentData,
				'productTypes' => $productTypes,
				'cities' => $cities,
				'pilotItems' => $pilotItems,
				'employees' => $employees,
				'shifts' => $shifts,
				'statuses' => $statuses,
				'user' => $user,
				'paidSum' => $paidSum,
				'pilotSum' => $pilotSum,
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
	 * @throws \Throwable
	 */
	public function store()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$eventType = $this->request->event_type ?? '';
		$commentText = $this->request->comment ?? '';
		
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
				
				$existingEvent = Event::where('event_type', $shiftUser)
					->where('city_id', $cityId)
					->where('location_id', $locationId)
					->where('flight_simulator_id', $simulatorId)
					->where('start_at', '<', Carbon::parse($stopAt)->format('Y-m-d H:i'))
					->where('stop_at', '>', Carbon::parse($startAt)->format('Y-m-d H:i'))
					->first();
				if ($existingEvent) {
					return response()->json(['status' => 'error', 'reason' => 'Пересечение со сменой ' . (($existingEvent->event_type == Event::EVENT_TYPE_SHIFT_ADMIN) ? 'администратора' : 'пилота') . ' ' . ($existingEvent->user ? $existingEvent->user->fio() : '')]);
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
				$stopAt = Carbon::parse(Carbon::parse($startAt)->addMinutes($product->duration ?? 0));
				
				if (Carbon::parse($startAt)->format('Y-m-d') != Carbon::parse($stopAt)->format('Y-m-d') || Carbon::parse($startAt)->gte($stopAt)) {
					return response()->json(['status' => 'error', 'reason' => 'Проверьте корректность даты начала и окончания полета']);
				}
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
					$data = [
						'pilot_assessment' => $this->request->pilot_assessment ?? '',
						'admin_assessment' => $this->request->admin_assessment ?? '',
					];
					
					$event = new Event();
					$event->event_type = Event::EVENT_TYPE_DEAL;
					$event->contractor_id = ($position->deal && $position->deal->contractor) ? $position->deal->contractor->id : 0;
					$event->deal_id = $position->deal ? $position->deal->id : 0;
					$event->deal_position_id = $position->id ?? 0;
					$event->city_id = $city->id ?? 0;
					$event->location_id = $location->id ?? 0;
					$event->flight_simulator_id = $simulator->id ?? 0;
					$event->start_at = Carbon::parse($startAt)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($stopAt)->format('Y-m-d H:i');
					$event->extra_time = (int)$this->request->extra_time;
					$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
					$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
					$event->pilot_id = $this->request->pilot ?? 0;
					$event->user_id = $this->request->user()->id ?? 0;
					$event->data_json = $data;
					$event->nominal_price = $event->nominalPrice();
					$event->actual_pilot_sum = (int)$this->request->actual_pilot_sum;
					$event->save();
					
					if ($commentText) {
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
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Throwable
	 */
	public function update($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$rules = [
			'start_at_date' => 'required_if:source,deal|date',
			'start_at_time' => 'required_if:source,deal',
			'stop_at_date' => 'required_if:source,deal|date',
			'stop_at_time' => 'required_if:source,deal',
			'doc_file' => 'sometimes|image|max:5120|mimes:jpg,jpeg,png,webp',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'start_at_date' => 'Дата начала полета',
				'start_at_time' => 'Время начала полета',
				'stop_at_date' => 'Дата окончания полета',
				'stop_at_time' => 'Время окончания полета',
				'doc_file' => 'Фото документа',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		$userId = $this->request->user_id ?? 0;
		$pilotId = $this->request->pilot_id ?? 0;
		$employeeId = $this->request->employee_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->flight_simulator_id ?? 0;
		$commentId = $this->request->comment_id ?? 0;
		$commentText = $this->request->comment ?? '';
		$position = null;

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
			case Event::EVENT_TYPE_TEST_FLIGHT:
				if (!$pilotId) {
					return response()->json(['status' => 'error', 'reason' => 'Пилот обязательно для заполнения']);
				}
			break;
			case Event::EVENT_TYPE_USER_FLIGHT:
				if (!$employeeId) {
					return response()->json(['status' => 'error', 'reason' => 'Сотрудник обязательно для заполнения']);
				}
			break;
			case Event::EVENT_TYPE_SHIFT_ADMIN:
			case Event::EVENT_TYPE_SHIFT_PILOT:
				$shiftUser = 'shift_' . $this->request->shift_user;
				$startAt = Carbon::parse($event->start_at)->format('Y-m-d') . ' ' . $this->request->start_at_time;
				$stopAt = Carbon::parse($event->stop_at)->format('Y-m-d') . ' ' . $this->request->stop_at_time;
				if (Carbon::parse($startAt)->gte(Carbon::parse($stopAt))) {
					return response()->json(['status' => 'error', 'reason' => 'Время окончания смены должно быть больше времени начала']);
				}
			
				$existingEvent = Event::where('event_type', $shiftUser)
					->where('start_at', '<', Carbon::parse($stopAt)->format('Y-m-d H:i'))
					->where('stop_at', '>', Carbon::parse($startAt)->format('Y-m-d H:i'))
					->where('location_id', $event->location_id)
					->where('flight_simulator_id', $event->flight_simulator_id)
					->where('id', '!=', $event->id)
					->first();
				if ($existingEvent) {
					return response()->json(['status' => 'error', 'reason' => 'Пересечение со сменой ' . (($existingEvent->event_type == Event::EVENT_TYPE_SHIFT_ADMIN) ? 'администратора' : 'пилота') . ' ' . ($existingEvent->user ? $existingEvent->user->fio() : '')]);
				}
			break;
		}
		
		if ($this->request->source == Event::EVENT_SOURCE_DEAL && $position) {
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
						$stopAt = Carbon::parse($this->request->stop_at_date . ' ' . $this->request->stop_at_time)->format('Y-m-d H:i');
						
						$event->city_id = $city ? $city->id : 0;
						$event->location_id = $locationId ?: ($location ? $location->id : 0);
						$event->flight_simulator_id = $simulatorId ?: ($simulator ? $simulator->id : 0);
						$event->extra_time = (int)$this->request->extra_time;
						$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
						$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
						$event->notification_type = $this->request->notification_type;
						$event->pilot_assessment = (int)$this->request->pilot_assessment;
						$event->admin_assessment = (int)$this->request->admin_assessment;
						$event->simulator_up_at = $this->request->simulator_up_at ? Carbon::parse($this->request->start_at_date . ' ' . $this->request->simulator_up_at)->format('Y-m-d H:i') : null;
						$event->simulator_down_at = $this->request->simulator_down_at ? Carbon::parse($this->request->start_at_date . ' ' . $this->request->simulator_down_at)->format('Y-m-d H:i') : null;
						$event->pilot_id = $pilotId;
						$event->description = $this->request->description ?? null;
					} else if ($this->request->source == Event::EVENT_SOURCE_CALENDAR) {
						$startAt = Carbon::parse($this->request->start_at)->format('Y-m-d H:i');
						$stopAt = Carbon::parse($this->request->stop_at)->subMinutes($event->extra_time ?? 0)->format('Y-m-d H:i');
					}
					// сброс отметки об уведомлении, если перенос на другой день
					if ($event->start_at->format('d') != Carbon::parse($startAt)->format('d')) {
						$event->notification_type = null;
					}
					
					if (Carbon::parse($startAt)->format('Y-m-d') != Carbon::parse($stopAt)->format('Y-m-d') || Carbon::parse($startAt)->gte($stopAt)) {
						return response()->json(['status' => 'error', 'reason' => 'Проверьте корректность даты начала и окончания полета']);
					}
					
					$event->start_at = $startAt;
					$event->stop_at = $stopAt;
					if (isset($city)) {
						$event->city_id = $city->id;
					}
					if (isset($locationId) && $locationId) {
						$event->location_id = $locationId;
					} elseif (isset($location)) {
						$event->location_id = $location->id;
					}
					if (isset($simulatorId) && $simulatorId) {
						$event->flight_simulator_id = $simulatorId;
					} elseif (isset($simulator)) {
						$event->flight_simulator_id = $simulator->id;
					}
					
					$data = $event->data_json ?? [];
					if($file = $this->request->file('doc_file')) {
						$isFileUploaded = $file->move(storage_path('app/private/contractor/doc'), $file->getClientOriginalName());
						$data['doc_file_path'] = $isFileUploaded ? 'contractor/doc/' . $file->getClientOriginalName() : '';
					}

					if (isset($data)) {
						$event->data_json = $data;
					}
					$event->nominal_price = $event->nominalPrice();
					$event->actual_pilot_sum = (int)$this->request->actual_pilot_sum;
					$event->save();
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
					$event->start_at = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($this->request->stop_at_date . ' ' . $this->request->stop_at_time)->format('Y-m-d H:i');
					$event->save();
				break;
				case Event::EVENT_TYPE_USER_FLIGHT:
					if (Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d') != Carbon::parse($this->request->stop_at_date . ' ' . $this->request->stop_at_time)->format('Y-m-d') || Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->gte($this->request->stop_at_date . ' ' . $this->request->stop_at_time)) {
						return response()->json(['status' => 'error', 'reason' => 'Проверьте корректность даты начала и окончания полета']);
					}
					
					$event->start_at = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($this->request->stop_at_date . ' ' . $this->request->stop_at_time)->format('Y-m-d H:i');
					$event->simulator_up_at = $this->request->simulator_up_at ? Carbon::parse($this->request->start_at_date . ' ' . $this->request->simulator_up_at)->format('Y-m-d H:i') : null;;
					$event->simulator_down_at = $this->request->simulator_down_at ? Carbon::parse($this->request->start_at_date . ' ' . $this->request->simulator_down_at)->format('Y-m-d H:i') : null;
					$event->employee_id = $employeeId;
					$event->nominal_price = $event->nominalPrice();
					$event->pilot_id = $pilotId;
					$event->actual_pilot_sum = (int)$this->request->actual_pilot_sum;
					$event->save();
				break;
				case Event::EVENT_TYPE_TEST_FLIGHT:
					if (Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d') != Carbon::parse($this->request->stop_at_date . ' ' . $this->request->stop_at_time)->format('Y-m-d') || Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->gte($this->request->stop_at_date . ' ' . $this->request->stop_at_time)) {
						return response()->json(['status' => 'error', 'reason' => 'Проверьте корректность даты начала и окончания полета']);
					}
					
					$event->start_at = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
					$event->stop_at = Carbon::parse($this->request->stop_at_date . ' ' . $this->request->stop_at_time)->format('Y-m-d H:i');
					$event->simulator_up_at = $this->request->simulator_up_at ? Carbon::parse($this->request->start_at_date . ' ' . $this->request->simulator_up_at)->format('Y-m-d H:i') : null;;
					$event->simulator_down_at = $this->request->simulator_down_at ? Carbon::parse($this->request->start_at_date . ' ' . $this->request->simulator_down_at)->format('Y-m-d H:i') : null;
					$event->test_pilot_id = $pilotId;
					$event->save();
				break;
				case Event::EVENT_TYPE_SHIFT_ADMIN:
				case Event::EVENT_TYPE_SHIFT_PILOT:
					$event->start_at = Carbon::parse($event->start_at)->format('Y-m-d') . ' ' . $this->request->start_at_time;
					$event->stop_at = Carbon::parse($event->stop_at)->format('Y-m-d') . ' ' . $this->request->stop_at_time;
					$event->user_id = $userId;
					$event->save();
				break;
			}
			
			if ($commentText) {
				if ($commentId) {
					$comment = $event->comments->find($commentId);
					if (!$comment) {
						return response()->json(['status' => 'error', 'reason' => 'Комментарий не найден']);
					}
					$comment->name = $commentText;
					$comment->updated_by = $user->id ?? 0;
					$comment->save();
				} elseif ($event) {
					$event->comments()->create([
						'name' => $commentText,
						'created_by' => $user->id ?? 0,
					]);
				}
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
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Throwable
	 */
	public function dragDrop($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
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

				$existingEvent = Event::where('event_type', $event->event_type)
					->where('start_at', '<', Carbon::parse($stopAt)->format('Y-m-d H:i'))
					->where('stop_at', '>', Carbon::parse($startAt)->format('Y-m-d H:i'))
					->where('location_id', $event->location_id)
					->where('flight_simulator_id', $event->flight_simulator_id)
					->where('id', '!=', $event->id)
					->first();
				if ($existingEvent) {
					return response()->json(['status' => 'error', 'reason' => 'Пересечение со сменой ' . (($existingEvent->event_type == Event::EVENT_TYPE_SHIFT_ADMIN) ? 'администратора' : 'пилота') . ' ' . ($existingEvent->user ? $existingEvent->user->fio() : '')]);
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
					} elseif ($this->request->source == Event::EVENT_SOURCE_CALENDAR) {
						$startAt = Carbon::parse($this->request->start_at)->format('Y-m-d H:i');
						$stopAt = Carbon::parse($this->request->stop_at)->subMinutes($event->extra_time ?? 0)->format('Y-m-d H:i');
					}
					// сброс отметки об уведомлении, если перенос на другой день
					if ($event->start_at->format('d') != Carbon::parse($startAt)->format('d')) {
						$event->notification_type = null;
					}
					if (Carbon::parse($startAt)->format('Y-m-d') != Carbon::parse($stopAt)->format('Y-m-d') || Carbon::parse($startAt)->gte($stopAt)) {
						return response()->json(['status' => 'error', 'reason' => 'Проверьте корректность даты начала и окончания полета']);
					}
					
					$event->start_at = $startAt;
					$event->stop_at = $stopAt;
					$event->nominal_price = $event->nominalPrice();
					$event->save();
				break;
				case Event::EVENT_TYPE_BREAK:
				case Event::EVENT_TYPE_CLEANING:
				case Event::EVENT_TYPE_TEST_FLIGHT:
				case Event::EVENT_TYPE_USER_FLIGHT:
					$startAt = Carbon::parse($this->request->start_at)->format('Y-m-d H:i');
					$stopAt = Carbon::parse($this->request->stop_at)->format('Y-m-d H:i');
				
					if (Carbon::parse($startAt)->format('Y-m-d') != Carbon::parse($stopAt)->format('Y-m-d') || Carbon::parse($startAt)->gte($stopAt)) {
						return response()->json(['status' => 'error', 'reason' => 'Проверьте корректность даты начала и окончания полета']);
					}
	
					$event->start_at = $startAt;
					$event->stop_at = $stopAt;
					$event->nominal_price = $event->nominalPrice();
					$event->save();
				break;
				case Event::EVENT_TYPE_SHIFT_ADMIN:
				case Event::EVENT_TYPE_SHIFT_PILOT:
					$event->start_at = $startAt;
					$event->stop_at = $stopAt;
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
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		try {
			\DB::beginTransaction();
			
			$flightInvitationFilePath = (is_array($event->data_json) && array_key_exists('flight_invitation_file_path', $event->data_json)) ? $event->data_json['flight_invitation_file_path'] : '';
		
			$childEvents = Event::where('parent_id', $event->id)
				->get();
			/** @var Event[] $childEvents */
			foreach ($childEvents as $childEvent) {
				$childPosition = $childEvent->dealPosition;
				if ($childPosition) {
					$childPosition->delete();
				}
				$childEvent->delete();
			}
			
			$event->delete();
			
			if ($flightInvitationFilePath) {
				Storage::disk('private')->delete($flightInvitationFilePath);
			}
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			\Log::debug('500 - Event Delete: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}
	
	/**
	 * @param $id
	 * @param $commentId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteComment($id, $commentId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
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
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteDocFile($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		$data = $event->data_json;
		if(is_array($data)
			&& array_key_exists('doc_file_path', $data)
			&& $data['doc_file_path']) {
			$data['doc_file_path'] = '';
			$event->data_json = $data;
			if (!$event->save()) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}

			return response()->json(['status' => 'success', 'msg' => 'Файл успешно удален']);
		}
		
		return response()->json(['status' => 'error', 'reason' => 'Файл не найден']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function notified()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$eventId = $this->request->event_id ?? 0;
		if (!$eventId) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректные параметры']);
		}
		
		$event = Event::find($eventId);
		if (!$event) {
			return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		}
		
		$event->is_notified = true;
		if (!$event->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'msg' => 'Уведомление по событию успешно сохранено']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sendFlightInvitation()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$rules = [
			'id' => 'required|numeric|min:0|not_in:0',
			'event_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'id' => 'Позиция',
				'event_id' => 'Событие',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$position = DealPosition::find($this->request->id);
		if (!$position) return response()->json(['status' => 'error', 'reason' => 'Позиция не найдена']);
		
		/** @var Deal $deal */
		$deal = $position->deal;
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		
		if (in_array($deal->status->alias, [Deal::CANCELED_STATUS, Deal::RETURNED_STATUS])) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка недоступна для редактирования']);
		}
		
		$balance = $position->balance();
		if ($balance < 0) return response()->json(['status' => 'error', 'reason' => 'Позиция должна быть оплачена']);
		
		$event = Event::find($this->request->event_id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);
		
		$job = new \App\Jobs\SendFlightInvitationEmail($event);
		$job->handle();
		
		return response()->json(['status' => 'success', 'message' => 'Задание на отправку Приглашения на полет принято']);
	}
	
	/**
	 * @param $uuid
	 * @return \never|\Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function getFlightInvitationFile($uuid)
	{
		$event = HelpFunctions::getEntityByUuid(Event::class, $uuid);
		if (!$event) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$event = $event->generateFile();
		if (!$event) {
			abort(404);
		}
		
		$flightInvitationFilePath = (is_array($event->data_json) && array_key_exists('flight_invitation_file_path', $event->data_json)) ? $event->data_json['flight_invitation_file_path'] : '';
		
		return Storage::disk('private')->download($flightInvitationFilePath);
	}
	
	/**
	 * @param $uuid
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function getDocFile($uuid)
	{
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$event = HelpFunctions::getEntityByUuid(Event::class, $uuid);
		if (!$event) {
			abort(404);
		}
		
		$docFilePath = (is_array($event->data_json) && array_key_exists('doc_file_path', $event->data_json)) ? $event->data_json['doc_file_path'] : '';

		return Storage::disk('private')->download($docFilePath);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function lockPeriod()
	{
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$locationId = $this->request->location_id ?? 0;
		$startAt = $this->request->start_at ?? '';
		$stopAt = $this->request->stop_at ?? '';
		
		$lockingPeriod = HelpFunctions::getLockingPeriod($user->id, $locationId, $startAt, $stopAt);
		if ($lockingPeriod) {
			return response()->json(['status' => 'error', 'reason' => 'Выбранный период пересекается с зарезервированным ранее периодом пользователя ' . ($lockingPeriod->user ? $lockingPeriod->user->fioFormatted() : '-')]);
		}

		$lockingPeriod = new LockingPeriod();
		$lockingPeriod->location_id = $locationId;
		$lockingPeriod->user_id = $user->id;
		$lockingPeriod->start_at = $startAt;
		$lockingPeriod->stop_at = $stopAt;
		if (!$lockingPeriod->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Период ' . $startAt . ' - ' . Carbon::parse($stopAt)->format('H:i') . ' зарезервирован на ' . Event::LOCKING_PERIOD . ' мин.']);
	}
}
