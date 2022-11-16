<?php

namespace App\Http\Controllers;

use App\Models\ExtraShift;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Repositories\CityRepository;
use Throwable;
use Validator;

class ScheduleController extends Controller
{
	private $request;
	private $cityRepo;

	const MONTHS = [
		'01' => 'Январь',
		'02' => 'Февраль',
		'03' => 'Март',
		'04' => 'Апрель',
		'05' => 'Май',
		'06' => 'Июнь',
		'07' => 'Июль',
		'08' => 'Август',
		'09' => 'Сентябрь',
		'10' => 'Октябрь',
		'11' => 'Ноябрь',
		'12' => 'Декабрь',
	];

	const WEEKDAYS = [
		0 => 'Вс',
		1 => 'Пн',
		2 => 'Вт',
		3 => 'Ср',
		4 => 'Чт',
		5 => 'Пт',
		6 => 'Сб',
	];
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request, CityRepository $cityRepo) {
		$this->request = $request;
		$this->cityRepo = $cityRepo;
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$user = \Auth::user();
		
		$cities = $this->cityRepo->getList($user);
		
		$years = [];
		for ($year = 2022; $year <= Carbon::now()->addYear()->format('Y'); $year++) {
			$years[] = $year;
		}
		
		return view('admin.schedule.index', [
			'cities' => $cities,
			'years' => $years,
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
		
		$rules = [
			'filter_location_id' => 'required|numeric|min:0|not_in:0',
			'filter_year' => 'required',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'filter_location_id' => 'Локация',
				'filter_year' => 'Год',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$filterLocationId = $this->request->filter_location_id ?? 0;
		$filterYear = $this->request->filter_year ?? Carbon::now()->format('Y');
		
		$filterDate = '';
		if ($filterYear) {
			$filterDate = Carbon::parse($filterYear);
		}
		
		$location = Location::find($filterLocationId);
		$city = $location->city;
		
		$schedules = Schedule::get();
		if ($filterDate) {
			$schedules = $schedules->whereBetween('scheduled_at', [$filterDate->startOfYear()->format('Y-m-d H:i:s'), $filterDate->endOfYear()->format('Y-m-d H:i:s')]);
		}
		if ($filterLocationId) {
			$schedules = $schedules->where('location_id', $filterLocationId);
		}
		$scheduleItems = [];
		foreach ($schedules as $schedule) {
			$scheduleItems[$schedule->location_id][$schedule->flight_simulator_id][$schedule->user_id][$schedule->scheduled_at->format('Y-m-d')] = [
				'id' => $schedule->id,
				'schedule_type' => $schedule->schedule_type,
				'letter' => Schedule::LETTER_TYPES[$schedule->schedule_type],
				'text' => trim(($schedule->start_at ? $schedule->start_at->format('H:i') : '') . ($schedule->stop_at ? '-' . $schedule->stop_at->format('H:i') : '') . ($schedule->comment ? ' ' . $schedule->comment : '')),
			];
		}
		
		$users = User::where('enable', true)
			->where('location_id', $filterLocationId)
			->orderBy('lastname')
			->orderBy('name')
			->get();
		$userItems = [];
		foreach ($users as $user) {
			$userItems[$user->role][$user->flight_simulator_id][] = [
				'id' => $user->id,
				'fio' => $user->fioFormatted(),
				'is_extra' => false,
			];
		}
		
		$extraShifts = ExtraShift::where('location_id', $filterLocationId)
			->whereHas('user', function ($query) {
				return $query->where('enable', true)
					->orderBy('lastname')
					->orderBy('name');
			})
			->get();
		$extraShiftItems = [];
		foreach ($extraShifts as $extraShift) {
			$extraShiftItems[$extraShift->period->format('Y-m')][$extraShift->user->role][$extraShift->flight_simulator_id][] = [
				'id' => $extraShift->user_id,
				'fio' => $extraShift->user->fioFormatted(),
				'is_extra' => true,
			];
		}
		
		$availableUserItems = [];
		if ($city->locations->count() > 1) {
			$availableUsers = User::where('enable', true)
				->where('city_id', $city->id)
				->where('location_id', '!=', $filterLocationId)
				->orderBy('lastname')
				->orderBy('name')
				->get();
			foreach ($availableUsers as $availableUser) {
				$availableUserItems[$availableUser->role][] = [
					'id' => $availableUser->id,
					'fio' => $availableUser->fioFormatted(),
				];
			}
		}
		
		$data = [
			'scheduleItems' => $scheduleItems,
			'userItems' => $userItems,
			'extraShiftItems' => $extraShiftItems,
			'availableUserItems' => $availableUserItems,
			'months' => self::MONTHS,
			'weekDays' => self::WEEKDAYS,
			'filterYear' => $filterYear,
			'location' => $location,
		];
		
		$VIEW = view('admin.schedule.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$userId = $this->request->user_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->simulator_id ?? 0;
		$scheduledAt = $this->request->scheduled_at ?? 0;

		$employee = User::find($userId);
		if (!$employee) return response()->json(['status' => 'error', 'reason' => 'Сотрудник не найден']);
		
		$types = Schedule::TYPES;
		switch ($employee->role) {
			case User::ROLE_PILOT:
				unset($types[Schedule::SHIFT_ADMIN_TYPE]);
				break;
			case User::ROLE_ADMIN:
				unset($types[Schedule::BASIC_PILOT_TYPE]);
				unset($types[Schedule::DUTY_PILOT_TYPE]);
				unset($types[Schedule::DAY_OFF_PILOT_TYPE]);
			break;
			default:
				$types = Schedule::TYPES;
		}
		
		$VIEW = view('admin.schedule.modal.add', [
			'types' => $types,
			'userId' => $userId,
			'locationId' => $locationId,
			'simulatorId' => $simulatorId,
			'scheduledAt' => $scheduledAt,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$schedule = Schedule::find($id);
		if (!$schedule) return response()->json(['status' => 'error', 'reason' => 'Запись не найдена']);
		
		$employee = $schedule->user;
		if (!$employee) return response()->json(['status' => 'error', 'reason' => 'Сотрудник не найден']);
		
		$types = Schedule::TYPES;
		switch ($employee->role) {
			case User::ROLE_PILOT:
				unset($types[Schedule::SHIFT_ADMIN_TYPE]);
			break;
			case User::ROLE_ADMIN:
				unset($types[Schedule::BASIC_PILOT_TYPE]);
				unset($types[Schedule::DUTY_PILOT_TYPE]);
				unset($types[Schedule::DAY_OFF_PILOT_TYPE]);
			break;
			default:
				$types = Schedule::TYPES;
		}
		
		$VIEW = view('admin.schedule.modal.edit', [
			'schedule' => $schedule,
			'types' => $types,
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
		
		$user = \Auth::user();
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$rules = [
			'type' => 'required',
			'start_at' => 'required_with:stop_at',
			'stop_at' => 'required_with:start_at',
		];
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'type' => 'Тип записи',
				'start_at' => 'Время начала',
				'stop_at' => 'Время окончания',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$id = $this->request->id ?? 0;
		$type = $this->request->type ?? null;
		$userId = $this->request->user_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->simulator_id ?? 0;
		$scheduledAt = $this->request->scheduled_at ?? 0;
		$startAt = $this->request->start_at ?? null;
		$stopAt = $this->request->stop_at ?? null;
		$comment = $this->request->comment ?? null;
		
		if ($startAt && $stopAt && Carbon::parse($stopAt)->lte($startAt)) {
			return response()->json(['status' => 'error', 'reason' => 'Время окончания события должно быть позже времени начала']);
		}
		
		if ($id) {
			$schedule = Schedule::find($id);
			if (!$schedule) return response()->json(['status' => 'error', 'reason' => 'Запись не найдена']);
			
			if ($type == 'reset') {
				if (!$schedule->delete()) {
					return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
				}
				
				return response()->json(['status' => 'success', 'message' => 'Запись успешно удалена', 'type' => Schedule::RESET_TYPE, 'color' => '#ffffff', 'text' => '', 'id' => $id]);
			}
		} else {
			if ($type == 'reset') return response()->json(['status' => 'error', 'reason' => 'Запись не найдена']);
			
			$schedule = new Schedule();
			$schedule->user_id = $userId;
			$schedule->location_id = $locationId;
			$schedule->flight_simulator_id = $simulatorId;
			$schedule->scheduled_at = Carbon::parse($scheduledAt)->format('Y-m-d');
			$schedule->start_at = $startAt ? Carbon::parse($startAt)->format('H:i') : null;
			$schedule->stop_at = $stopAt ? Carbon::parse($stopAt)->format('H:i') : null;
			$schedule->comment = $comment;
		}
		$schedule->schedule_type = $type;
		if (!$schedule->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		$text = trim(($schedule->start_at ? $schedule->start_at->format('H:i') : '') . ($schedule->stop_at ? '-' . $schedule->stop_at->format('H:i') : '') . ($schedule->comment ? ' ' . $schedule->comment : ''));
		
		return response()->json(['status' => 'success', 'message' => 'Запись успешно сохранена', 'type' => $type, 'color' => Schedule::COLOR_TYPES[$schedule->schedule_type], 'text' => $text, 'id' => $schedule->id, 'user_id' => $schedule->user_id, 'location_id' => $schedule->location_id, 'simulator_id' => $schedule->flight_simulator_id, 'scheduled_at' => $schedule->scheduled_at->format('Y-m-d')]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function storeExtraUser()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$rules = [
			'user_id' => 'required',
		];
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'user_id' => 'Сотрудник',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$userId = $this->request->user_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->simulator_id ?? 0;
		$period = $this->request->period ?? '';
		
		//DB::connection()->enableQueryLog();
		$extraShift = ExtraShift::where('user_id', $userId)
			->where('location_id', $locationId)
			->where('flight_simulator_id', $simulatorId)
			->whereDate('period', $period)
			->first();
		if ($extraShift) {
			return response()->json(['status' => 'error', 'reason' => 'Сотрудник уже существует']);
		}
		//\Log::debug(DB::getQueryLog());
		
		$extraShift = new ExtraShift();
		$extraShift->user_id = $userId;
		$extraShift->location_id = $locationId;
		$extraShift->flight_simulator_id = $simulatorId;
		$extraShift->period = $period ? Carbon::parse($period)->format('Y-m-d') : null;
		if (!$extraShift->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Сотрудник успешно добавлен', 'id' => $extraShift->id, 'user_id' => $extraShift->user_id, 'location_id' => $extraShift->location_id, 'simulator_id' => $extraShift->flight_simulator_id, 'period' => $extraShift->period->format('Y-m')]);
	}

	public function deleteExtraUser()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$userId = $this->request->user_id ?? 0;
		$locationId = $this->request->location_id ?? 0;
		$simulatorId = $this->request->simulator_id ?? 0;
		$period = $this->request->period ?? '';
		
		$extraShift = ExtraShift::where('user_id', $userId)
			->where('location_id', $locationId)
			->where('flight_simulator_id', $simulatorId)
			->where('period', $period)
			->first();
		if (!$extraShift) {
			return response()->json(['status' => 'success', 'message' => 'Дополнительная смена не найдена']);
		}
		
		try {
			DB::beginTransaction();

			Schedule::where('user_id', $userId)
				->where('location_id', $locationId)
				->where('flight_simulator_id', $simulatorId)
				->where('scheduled_at', '>=', Carbon::parse($period . '-01')->startOfMonth())
				->where('scheduled_at', '<=', Carbon::parse($period . '-01')->endOfMonth())
				->delete();
			
			$extraShift->delete();

			DB::commit();
		} catch (Throwable $e) {
			DB::rollback();
			
			\Log::debug('500 - Extra Shift Delete: ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Дополнительная смена успешно удалена']);
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
		
		$user = \Auth::user();
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$rules = [
			'type' => 'required',
			'start_at' => 'required_with:stop_at',
			'stop_at' => 'required_with:start_at',
		];
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'type' => 'Тип записи',
				'start_at' => 'Время начала',
				'stop_at' => 'Время окончания',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$type = $this->request->type ?? null;
		$startAt = $this->request->start_at ?? null;
		$stopAt = $this->request->stop_at ?? null;
		$comment = $this->request->comment ?? null;

		$schedule = Schedule::find($id);
		if (!$schedule) return response()->json(['status' => 'error', 'reason' => 'Запись не найдена']);
		
		if ($startAt && $stopAt && Carbon::parse($stopAt)->lte($startAt)) {
			return response()->json(['status' => 'error', 'reason' => 'Время окончания события должно быть позже времени начала']);
		}
		$schedule->schedule_type = $type;
		$schedule->start_at = $startAt ? Carbon::parse($startAt)->format('H:i') : null;
		$schedule->stop_at = $stopAt ? Carbon::parse($stopAt)->format('H:i') : null;
		$schedule->comment = $comment;
		if (!$schedule->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		$text = trim(($schedule->start_at ? $schedule->start_at->format('H:i') : '') . ($schedule->stop_at ? '-' . $schedule->stop_at->format('H:i') : '') . ($schedule->comment ? ' ' . $schedule->comment : ''));
		
		return response()->json(['status' => 'success', 'message' => 'Запись успешно сохранена', 'type' => $type, 'color' => Schedule::COLOR_TYPES[$schedule->schedule_type], 'text' => $text, 'id' => $schedule->id, 'user_id' => $schedule->user_id, 'location_id' => $schedule->location_id, 'simulator_id' => $schedule->flight_simulator_id, 'scheduled_at' => $schedule->scheduled_at->format('Y-m-d')]);
	}
}