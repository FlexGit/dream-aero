<?php

namespace App\Http\Controllers;

use App\Exports\AeroflotAccrualReportExport;
use App\Exports\AeroflotWriteOffReportExport;
use App\Exports\ContractorSelfMadePayedDealsReportExport;
use App\Exports\NpsReportExport;
use App\Exports\PlatformDataReportExport;
use App\Models\Bill;
use App\Models\Certificate;
use App\Models\City;
use App\Models\Content;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\PlatformData;
use App\Models\PlatformLog;
use App\Models\User;
use App\Repositories\CityRepository;
use App\Repositories\PaymentRepository;
use App\Services\AeroflotBonusService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Services\HelpFunctions;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportController extends Controller {
	private $request;
	private $cityRepo;
	private $paymentRepo;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request, CityRepository $cityRepo, PaymentRepository $paymentRepo) {
		$this->request = $request;
		$this->cityRepo = $cityRepo;
		$this->paymentRepo = $paymentRepo;
	}
	
	public function npsIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-nps');
		
		return view('admin.report.nps.index', [
			'page' => $page,
		]);
	}
	
	public function npsGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$role = $this->request->filter_role ?? '';
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
		}
		
		$events = Event::where('event_type', Event::EVENT_TYPE_DEAL);
		if ($dateFromAt) {
			$events = $events->where('stop_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'));
		}
		if ($dateToAt) {
			$events = $events->where('stop_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'));
		}
		$events = $events->orderBy('start_at')
			->get();
		
		$userAssessments = $eventItems = [];
		foreach ($events as $event) {
			// находим админа, который был на смене во время полета
			$shiftAdminEvent = Event::where('event_type', Event::EVENT_TYPE_SHIFT_ADMIN)
				->where('user_id', '!=', 0)
				->where('city_id', $event->city_id)
				->where('location_id', $event->location_id)
				->where('flight_simulator_id', $event->flight_simulator_id)
				->where('start_at', '<=', Carbon::parse($event->start_at)->format('Y-m-d H:i:s'))
				->where('stop_at', '>=', Carbon::parse($event->stop_at)->format('Y-m-d H:i:s'))
				->first();
			
			$adminId = $shiftAdminEvent ? $shiftAdminEvent->user_id : 0;
			if ($adminId) {
				if (!isset($userAssessments[$adminId])) {
					$userAssessments[$adminId] = [];
					$userAssessments[$adminId] = [
						'good' => 0,
						'neutral' => 0,
						'bad' => 0,
					];
				}
				
				if ($event->admin_assessment >= 9) {
					++$userAssessments[$adminId]['good'];
				}
				else if ($event->admin_assessment >= 7 && $event->admin_assessment <= 8) {
					++$userAssessments[$adminId]['neutral'];
				}
				elseif ($event->admin_assessment >= 1 && $event->admin_assessment <= 6) {
					++$userAssessments[$adminId]['bad'];
				}
				
				$assessment = $event->getAssessment(User::ROLE_ADMIN);
				$eventItems[$adminId][] = [
					'uuid' => $event->uuid,
					'interval' => $event->getInterval(),
					'assessment' => $assessment,
					'assessment_state' => $event->getAssessmentState($assessment),
				];
			}

			// если был установлен вручную фактический пилот
			$pilotId = $event->pilot_id;
			
			if (!$pilotId) {
				// находим пилота, который был на смене во время полета
				$shiftPilotEvent = Event::where('event_type', Event::EVENT_TYPE_SHIFT_PILOT)
					->where('user_id', '!=', 0)
					->where('city_id', $event->city_id)
					->where('location_id', $event->location_id)
					->where('flight_simulator_id', $event->flight_simulator_id)
					->where('start_at', '<=', Carbon::parse($event->start_at)->format('Y-m-d H:i:s'))
					->where('stop_at', '>=', Carbon::parse($event->stop_at)->format('Y-m-d H:i:s'))
					->first();
				$pilotId = $shiftPilotEvent ? $shiftPilotEvent->user_id : 0;
			}
			
			if ($pilotId) {
				if (!isset($userAssessments[$pilotId])) {
					$userAssessments[$pilotId] = [];
					$userAssessments[$pilotId] = [
						'good' => 0,
						'neutral' => 0,
						'bad' => 0,
					];
				}
				
				if ($event->pilot_assessment >= 9) {
					++$userAssessments[$pilotId]['good'];
				}
				elseif ($event->pilot_assessment >= 7 && $event->pilot_assessment <= 8) {
					++$userAssessments[$pilotId]['neutral'];
				}
				elseif ($event->pilot_assessment >= 1 && $event->pilot_assessment <= 6) {
					++$userAssessments[$pilotId]['bad'];
				}

				$assessment = $event->getAssessment(User::ROLE_PILOT);
				$eventItems[$pilotId][] = [
					'uuid' => $event->uuid,
					'interval' => $event->getInterval(),
					'assessment' => $assessment,
					'assessment_state' => $event->getAssessmentState($assessment),
				];
			}
		}
		
		$userNps = [];
		foreach ($userAssessments as $userId => $assessment) {
			$goodBadDiff = $assessment['good'] - $assessment['bad'];
			$goodNeutralBadSum = $assessment['good'] + $assessment['neutral'] + $assessment['bad'];
			if (!$goodNeutralBadSum) continue;
			
			$userNps[$userId] = round($goodBadDiff / $goodNeutralBadSum * 100, 1);
			//\Log::debug($userId . ' - ' . $goodBadDiff . ' - ' . $goodNeutralBadSum . ' = ' . $userNps[$userId]);
		}
		
		$users = User::where('enable', true)
			->orderBy('lastname')
			->orderBy('name')
			->orderBy('middlename');
		if ($role) {
			$users = $users->where('role', $role);
		}
		$users = $users->get();

		$cities = $this->cityRepo->getList($this->request->user());
		
		$data = [
			'eventItems' => $eventItems,
			'users' => $users,
			'cities' => $cities,
			'userAssessments' => $userAssessments,
			'userNps' => $userNps,
		];
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-nps-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new NpsReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		$VIEW = view('admin.report.nps.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
	}
	
	public function personalSellingIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-personal-selling');
		
		return view('admin.report.personal-selling.index', [
			'page' => $page,
		]);
	}
	
	public function personalSellingGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
		}
		
		$bills = Bill::where('user_id', '!=', 0)
			->where('created_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('created_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			//->whereRelation('status', 'statuses.alias', '=', Bill::PAYED_STATUS)
			->get();
		
		$billItems = $paymentMethodSumItems = $dealIds = [];
		$totalSum = 0;
		foreach ($bills as $bill) {
			if (!isset($billItems[$bill->location_id])) {
				$billItems[$bill->location_id] = [];
			}
			if (!isset($billItems[$bill->location_id][$bill->user_id])) {
				$billItems[$bill->location_id][$bill->user_id] = [
					'bill_count' => 0,
					'bill_sum' => 0,
					'payed_bill_count' => 0,
					'payed_bill_sum' => 0,
					'deal_ids' => [],
					'deal_count' => 0,
					'deal_sum' => 0,
				];
			}
			if (!isset($paymentMethodSumItems[$bill->payment_method_id])) {
				$paymentMethodSumItems[$bill->payment_method_id] = 0;
			}
			
			// кол-во счетов
			++$billItems[$bill->location_id][$bill->user_id]['bill_count'];
			// сумма счетов
			$billItems[$bill->location_id][$bill->user_id]['bill_sum'] += $bill->amount;
			if ($bill->status && $bill->status->alias == Bill::PAYED_STATUS) {
				// кол-во оплаченных счетов
				++$billItems[$bill->location_id][$bill->user_id]['payed_bill_count'];
				// сумма оплаченных счетов
				$billItems[$bill->location_id][$bill->user_id]['payed_bill_sum'] += $bill->amount;
				// сумма оплаченных счетов конкретного способа оплаты
				$paymentMethodSumItems[$bill->payment_method_id] += $bill->amount;
				$totalSum += $bill->amount;
			}
			$deal = $bill->deal;
			if ($deal && !in_array($bill->deal_id, $billItems[$bill->location_id][$bill->user_id]['deal_ids'])) {
				$billItems[$bill->location_id][$bill->user_id]['deal_ids'][] = $deal->id;
				// кол-во сделок
				++$billItems[$bill->location_id][$bill->user_id]['deal_count'];
				// сумма сделок
				$billItems[$bill->location_id][$bill->user_id]['deal_sum'] += $deal->amount();
			}
		}
		
		$shiftItems = [];
		$shifts = Event::where('event_type', Event::EVENT_TYPE_SHIFT_ADMIN)
			->where('start_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('start_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->get();
		foreach ($shifts as $shift) {
			if (!isset($shiftItems[$shift->user_id])) {
				$shiftItems[$shift->user_id] = 0;
			}
			++$shiftItems[$shift->user_id];
		}
		
		$userItems = [];
		$users = User::where('enable', true)
			->where('role', User::ROLE_ADMIN)
			->orderBy('lastname')
			->orderBy('name')
			->orderBy('middlename')
			->get();
		foreach ($users as $user) {
			if (!$user->location_id) continue;
			
			$userItems[$user->location_id][] = [
				'id' => $user->id,
				'fio' => $user->fioFormatted(),
			];
		}
		
		$cities = $this->cityRepo->getList($this->request->user());
		$paymentMethods = $this->paymentRepo->getPaymentMethodList(false);
		
		$data = [
			'billItems' => $billItems,
			'paymentMethodSumItems' => $paymentMethodSumItems,
			'totalSum' => $totalSum,
			'shiftItems' => $shiftItems,
			'userItems' => $userItems,
			'cities' => $cities,
			'paymentMethods' => $paymentMethods,
		];
		
		/*$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-personal-selling-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new PersonalSellingReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}*/
		
		$VIEW = view('admin.report.personal-selling.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW/*, 'fileName' => $reportFileName*/]);
	}
	
	public function unexpectedRepeatedIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-unexpected-repeated');
		
		return view('admin.report.unexpected-repeated.index', [
			'page' => $page,
		]);
	}
	
	public function unexpectedRepeatedGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
		}
		
		$events = Event::where('event_type', Event::EVENT_TYPE_DEAL)
			->where('created_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('created_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			//->whereRelation('status', 'statuses.alias', '=', Bill::PAYED_STATUS)
			->get();
		
		$eventItems = [];
		foreach ($events as $event) {
			if (!isset($eventItems[$event->location_id])) {
				$eventItems[$event->location_id] = [];
			}
			if (!isset($eventItems[$event->location_id][$event->flight_simulator_id])) {
				$eventItems[$event->location_id][$event->flight_simulator_id] = [
					'is_unexpected_flight' => 0,
					'is_repeated_flight' => 0,
				];
			}
			
			if ($event->is_unexpected_flight) {
				++$eventItems[$event->location_id][$event->flight_simulator_id]['is_unexpected_flight'];
			}
			if ($event->is_repeated_flight) {
				++$eventItems[$event->location_id][$event->flight_simulator_id]['is_repeated_flight'];
			}
		}
		
		$cities = $this->cityRepo->getList($this->request->user());
		
		$data = [
			'eventItems' => $eventItems,
			'cities' => $cities,
		];
		
		/*$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-personal-selling-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new PersonalSellingReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}*/
		
		$VIEW = view('admin.report.unexpected-repeated.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW/*, 'fileName' => $reportFileName*/]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function aeroflotWriteOffIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-aeroflot-write-off');
		
		return view('admin.report.aeroflot.write-off.index', [
			'page' => $page,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function aeroflotWriteOffGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
		}
		//\DB::connection()->enableQueryLog();
		$bills = Bill::where('aeroflot_transaction_type', AeroflotBonusService::TRANSACTION_TYPE_REGISTER_ORDER)
			->where('aeroflot_state', AeroflotBonusService::PAYED_STATE)
			->where('created_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('created_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->get();
		//\Log::debug(\DB::getQueryLog());
		$items = [];
		foreach ($bills as $bill) {
			$billLocation = $bill->location;
			$billCity = $billLocation ? $billLocation->city : null;
			$deal = $bill->deal;
			$contractor = $deal ? $deal->contractor : null;
			$contractorCity = $contractor ? $contractor->city : null;
			
			if ($contractor && $contractor->email == env('DEV_EMAIL')) continue;
			
			$locationName = $billLocation ? $billLocation->name : '';
			$cityName = $billCity ? $billCity->name : ($contractorCity ? $contractorCity->name : '');
			
			$items[$bill->id] = [
				'partner_name' => AeroflotBonusService::PARTNER_NAME,
				'city_name' => $cityName,
				'location_name' => $locationName,
				'transaction_order_id' => $bill->aeroflot_transaction_order_id,
				'transaction_created_at' => $bill->aeroflot_transaction_created_at,
				'card_number' => $bill->aeroflot_card_number,
				'bill_amount' => $bill->amount + $bill->aeroflot_bonus_amount,
				'bonus_amount' => $bill->aeroflot_bonus_amount,
				'bonus_miles' => $bill->aeroflot_bonus_amount * 4,
				'product_type_name' => 'Полет на авиатренажере',
			];
		}
		
		$data = [
			'items' => $items,
		];
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-aeroflot-write-off-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new AeroflotWriteOffReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		$VIEW = view('admin.report.aeroflot.write-off.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function aeroflotAccrualIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-aeroflot-accrual');
		
		return view('admin.report.aeroflot.accrual.index', [
			'page' => $page,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function aeroflotAccrualGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
		}
		//\DB::connection()->enableQueryLog();
		$bills = Bill::where('aeroflot_transaction_type', AeroflotBonusService::TRANSACTION_TYPE_AUTH_POINTS)
			->where('aeroflot_status', 0)
			->where('aeroflot_transaction_created_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('aeroflot_transaction_created_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->get();
		//\Log::debug(\DB::getQueryLog());
		$items = [];
		foreach ($bills as $bill) {
			$billLocation = $bill->location;
			$billCity = $billLocation ? $billLocation->city : null;
			$deal = $bill->deal;
			$contractor = $deal ? $deal->contractor : null;
			$contractorCity = $contractor ? $contractor->city : null;
			
			if ($contractor && $contractor->email == env('DEV_EMAIL')) continue;
			
			$locationName = $billLocation ? $billLocation->name : '';
			$cityName = $billCity ? $billCity->name : ($contractorCity ? $contractorCity->name : '');
			
			$items[$bill->id] = [
				'transaction_order_id' => $bill->aeroflot_transaction_order_id,
				'city_name' => $cityName,
				'location_name' => $locationName,
				'transaction_created_at' => $bill->aeroflot_transaction_created_at ? $bill->aeroflot_transaction_created_at->format('Y-m-d H:i:s') : '',
				'bill_amount' => $bill->amount,
				'bonus_miles' => $bill->aeroflot_bonus_amount,
			];
		}
		
		$data = [
			'items' => $items,
		];
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-aeroflot-accrual-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new AeroflotAccrualReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		$VIEW = view('admin.report.aeroflot.accrual.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function contractorSelfMadePayedDealsIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-contractor-self-made-payed-deals');
		
		return view('admin.report.contractor-self-made-payed-deals.index', [
			'page' => $page,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function contractorSelfMadePayedDealsGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
		}
		//\DB::connection()->enableQueryLog();
		$bills = Bill::where('user_id', 0)
			->whereRelation('paymentMethod', 'payment_methods.alias', '=', PaymentMethod::ONLINE_ALIAS)
			->whereRelation('status', 'statuses.alias', '=', Bill::PAYED_STATUS)
			->where('payed_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('payed_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->get();
		//\Log::debug(\DB::getQueryLog());
		$items = [];
		foreach ($bills as $bill) {
			$deal = $bill->deal;
			if ($deal->bills->count() > 1) continue;
			
			$contractor = $bill->contractor;
			if ($contractor && $contractor->email == env('DEV_EMAIL')) continue;
			
			if (!isset($items[$bill->location_id])) {
				$items[$bill->location_id] = [
					'bill_count' => 0,
					'bill_amount_sum' => 0,
				];
			}
			++$items[$bill->location_id]['bill_count'];
			$items[$bill->location_id]['bill_amount_sum'] += $bill->amount;
		}

		$cities = City::where('version', $user->version)
			->get();
		
		$data = [
			'items' => $items,
			'cities' => $cities,
		];
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-contractor-self-made-payed-deals-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new ContractorSelfMadePayedDealsReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		$VIEW = view('admin.report.contractor-self-made-payed-deals.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function platformIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-platform');
		
		return view('admin.report.platform.index', [
			'page' => $page,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function platformGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		$items = $locationDurationData = $dayDurationData = $durationData = $userDurationData = [];
		
		//\DB::connection()->enableQueryLog();
		$events = Event::selectRaw('location_id, flight_simulator_id, SUBSTRING(start_at,1,10) as filght_date, SUM(TIMESTAMPDIFF(MINUTE, start_at, stop_at) + extra_time) as flight_duration, SUM(TIMESTAMPDIFF(MINUTE, simulator_up_at, simulator_down_at)) as user_flight_duration')
			->whereIn('event_type', [Event::EVENT_TYPE_DEAL, Event::EVENT_TYPE_TEST_FLIGHT, Event::EVENT_TYPE_USER_FLIGHT])
			->where('start_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('start_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->groupBy('location_id')
			->groupBy('flight_simulator_id')
			->groupBy('filght_date')
			->get();
		foreach ($events as $event) {
			$dateFormated = date('Y-m-d', strtotime($event->filght_date));
			$year = date('Y', strtotime($dateFormated));
			$month = date('m', strtotime($dateFormated));
			
			$durationData[$event->location_id][$event->flight_simulator_id][$dateFormated] = $event->flight_duration;
			$userDurationData[$event->location_id][$event->flight_simulator_id][$dateFormated] = $event->user_flight_duration;
			
			$locationDurationData[$year][$month][$event->location_id][$event->flight_simulator_id]['calendar_time'][] = $event->flight_duration;
			$dayDurationData[$dateFormated]['calendar_time'][] = $event->flight_duration;
			
			$locationDurationData[$year][$month][$event->location_id][$event->flight_simulator_id]['user_time'][] = $event->user_flight_duration;
			$dayDurationData[$dateFormated]['user_time'][] = $event->user_flight_duration;
		}
		
		$platformDatas = PlatformData::where('data_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d'))
			->where('data_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d'))
			->oldest()
			->get();
		//\Log::debug(\DB::getQueryLog());
		
		foreach ($platformDatas as $platformData) {
			if (!isset($items[$platformData->location_id])) {
				$items[$platformData->location_id] = [];
			}
			if (!isset($items[$platformData->location_id][$platformData->flight_simulator_id])) {
				$items[$platformData->location_id][$platformData->flight_simulator_id] = [];
			}
			if (!isset($items[$platformData->location_id][$platformData->flight_simulator_id][$platformData->data_at])) {
				$items[$platformData->location_id][$platformData->flight_simulator_id][$platformData->data_at] = [
					'id' => $platformData->id,
					'platform_time' => $platformData->total_up ? HelpFunctions::mailGetTimeMinutes($platformData->total_up) : 0,
					'ianm_time' => $platformData->in_air_no_motion ? HelpFunctions::mailGetTimeMinutes($platformData->in_air_no_motion) : 0,
					'comment' => $platformData->comment,
				];
				
				$dateFormated = date('Y-m-d', strtotime($platformData->data_at));
				$year = date('Y', strtotime($dateFormated));
				$month = date('m', strtotime($dateFormated));
				
				$locationDurationData[$year][$month][$platformData->location_id][$platformData->flight_simulator_id]['platform_time'][] = $platformData->total_up ? HelpFunctions::mailGetTimeMinutes($platformData->total_up) : 0;
				$locationDurationData[$year][$month][$platformData->location_id][$platformData->flight_simulator_id]['user_time'][] = $platformData->user_total_up ? HelpFunctions::mailGetTimeMinutes($platformData->user_total_up) : 0;
				
				$dayDurationData[$dateFormated]['platform_time'][] = $platformData->total_up ? HelpFunctions::mailGetTimeMinutes($platformData->total_up) : 0;
				$dayDurationData[$dateFormated]['user_time'][] = $platformData->user_total_up ? HelpFunctions::mailGetTimeMinutes($platformData->user_total_up) : 0;
			}
		}
		
		//\Log::debug($userDurationData);
		//\Log::debug($locationSum);
		
		$cities = City::where('version', $user->version)
			->get();
		
		$carbonDays = CarbonPeriod::create($dateFromAt, $dateToAt)->toArray();
		
		$days = $periods = [];
		foreach ($carbonDays as $carbonDay) {
			$days[] = $carbonDay->format('Y-m-d');
			$periods[] = $carbonDay->format('Y') . '-' . $carbonDay->format('m');
		}
		$periods = array_unique($periods);
		
		$months = [
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
			'12' => 'Декабрь'
		];
		
		$data = [
			'items' => $items,
			'locationDurationData' => $locationDurationData,
			'dayDurationData' => $dayDurationData,
			'durationData' => $durationData,
			'userDurationData' => $userDurationData,
			'cities' => $cities,
			'days' => $days,
			'months' => $months,
			'periods' => $periods,
		];
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-platform-data-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new PlatformDataReportExport($data, $periods), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		$VIEW = view('admin.report.platform.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
	}
	
	/**
	 * @param $locationId
	 * @param $simulatorId
	 * @param $date
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function platformModalEdit($locationId, $simulatorId, $date)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$location = Location::find($locationId);
		$simulator = FlightSimulator::find($simulatorId);
		
		$items = [];
		
		// события календаря и админа
		$events = Event::whereIn('event_type', [Event::EVENT_TYPE_DEAL, Event::EVENT_TYPE_TEST_FLIGHT, Event::EVENT_TYPE_USER_FLIGHT])
			->where('location_id', $locationId)
			->where('flight_simulator_id', $simulatorId)
			->where('start_at', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'))
			->where('start_at', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'))
			->orderBy('start_at')
			->get();
		foreach ($events as $event) {
			$items['admin'][Carbon::parse($event->simulator_up_at)->format('H')][] = [
				'start_at' => Carbon::parse($event->simulator_up_at )->format('H:i'),
				'stop_at' => Carbon::parse($event->simulator_down_at)->format('H:i'),
			];
			$items['calendar'][Carbon::parse($event->start_at)->format('H')][] = [
				'start_at' => Carbon::parse($event->start_at)->format('H:i'),
				'stop_at' => Carbon::parse($event->stop_at)->addMinutes($event->extra_time)->format('H:i'),
			];
		}
		
		// события платформы
		$platformData = PlatformData::where('location_id', $locationId)
			->where('flight_simulator_id', $simulatorId)
			->where('data_at', $date)
			->first();
		if ($platformData) {
			$platformLogs = PlatformLog::where('platform_data_id', $platformData->id)
				->orderBy('start_at')
				->get();
		}
		foreach ($platformLogs ?? [] as $platformLog) {
			$items[$platformLog->action_type][Carbon::parse($platformLog->start_at)->format('H')][] = [
				'start_at' => Carbon::parse($platformLog->start_at)->format('H:i'),
				'stop_at' => Carbon::parse($platformLog->stop_at)->format('H:i'),
			];
		}
		
		$intervals = CarbonInterval::hour()->toPeriod(Carbon::parse($date . ' 09:00:00'), Carbon::parse($date . ' 23:59:59'));

		// собираем разные типы одного события в один интервал,
		// если из-за небольших расхождений по времени они попали в разные интервалы
		foreach ($intervals as $interval) {
			// сервер
			foreach ($items[PlatformLog::IN_UP_ACTION_TYPE][$interval->format('H')] ?? [] as $index => $item) {
				$calendarHourInterval = HelpFunctions::getHourInterval($item['start_at'], $interval->format('H'), $items['calendar']);
				if ($calendarHourInterval != $interval->format('H')) {
					$items[PlatformLog::IN_UP_ACTION_TYPE][$calendarHourInterval][$index] = $item;
					unset($items[PlatformLog::IN_UP_ACTION_TYPE][$interval->format('H')][$index]);
				}
			}
			
			// админ
			foreach ($items['admin'][$interval->format('H')] ?? [] as $index => $item) {
				$calendarHourInterval = HelpFunctions::getHourInterval($item['start_at'], $interval->format('H'), $items['calendar']);
				if ($calendarHourInterval != $interval->format('H')) {
					$items['admin'][$calendarHourInterval][$index] = $item;
					unset($items['admin'][$interval->format('H')][$index]);
				}
			}
			
			// X-Plane
			foreach ($items[PlatformLog::IN_AIR_ACTION_TYPE][$interval->format('H')] ?? [] as $index => $item) {
				$calendarHourInterval = HelpFunctions::getHourInterval($item['start_at'], $interval->format('H'), $items['calendar']);
				if ($calendarHourInterval != $interval->format('H')) {
					$items[PlatformLog::IN_AIR_ACTION_TYPE][$calendarHourInterval][$index] = $item;
					unset($items[PlatformLog::IN_AIR_ACTION_TYPE][$interval->format('H')][$index]);
				}
			}
		}

		//\Log::debug($items);
		
		$data = [
			'location' => $location,
			'simulator' => $simulator,
			'date' => $date,
			'platformData' => $platformData,
			'items' => $items,
			'intervals' => $intervals,
		];
		
		$VIEW = view('admin.report.platform.modal.edit', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function platformModalUpdate()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$id = $this->request->id ?? 0;
		
		$platformData = PlatformData::find($id);
		if (!$platformData) return response()->json(['status' => 'error', 'reason' => 'Показания платформы не найдены']);
		
		$comment = $this->request->comment ?? '';
		
		$platformData->comment = $comment;
		if(!$platformData->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success']);
	}

	/**
	 * @param $fileName
	 * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function getExportFile($fileName)
	{
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		return Storage::disk('private')->download('report/' . $fileName);
	}
}