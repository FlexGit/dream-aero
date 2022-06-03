<?php

namespace App\Http\Controllers;

use App\Exports\AeroflotAccrualReportExport;
use App\Exports\AeroflotWriteOffReportExport;
use App\Exports\ContractorSelfMadePayedDealsReportExport;
use App\Exports\NpsReportExport;
use App\Models\Bill;
use App\Models\Certificate;
use App\Models\City;
use App\Models\Content;
use App\Models\Event;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Repositories\CityRepository;
use App\Repositories\PaymentRepository;
use App\Services\AeroflotBonusService;
use Carbon\Carbon;
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
	
	public function certificatesIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-certificates');
		
		$cities = $this->cityRepo->getList($this->request->user());
		
		return view('admin.report.certificates.index', [
			'page' => $page,
			'cities' => $cities,
		]);
	}
	
	public function certificatesGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$cityId = ($this->request->filter_city_id != 'all') ? $this->request->filter_city_id : null;
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
		}
		//\DB::connection()->enableQueryLog();
		$certificates = Certificate::where('created_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('created_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'));
		if (!is_null($cityId)) {
			$certificates = $certificates->where('city_id', $cityId);
		}
		$certificates = $certificates->has('product')
			->has('position')
			->get();
		//\Log::debug(\DB::getQueryLog());
		$certificateItems = [];
		/** @var Certificate[] $certificates */
		foreach ($certificates as $certificate) {
			$position = $certificate->position;
			$positionProduct = $position ? $position->product : null;
			$positionBill = $position->bill;
			$positionBillStatus = ($positionBill && $positionBill->status) ? $positionBill->status : null;
			$positionBillPaymentMethod = ($positionBill && $positionBill->paymentMethod) ? $positionBill->paymentMethod : null;
			$certificateProduct = $certificate->product;
			$certificateCity = $certificate->city;
			$certificateStatus = $certificate->status ?? null;
			
			$certificateItems[$certificate->id] = [
				'number' => $certificate->number,
				'created_at' => $certificate->created_at,
				'city_name' => $certificateCity ? $certificateCity->name : 'Действует в любом городе',
				'certificate_product_name' => $certificateProduct ? $certificateProduct->name : '',
				'position_product_name' => $positionProduct ? $positionProduct->name : '',
				'position_amount' => $position ? $position->amount : 0,
				'expire_at' => $certificate->expire_at ? Carbon::parse($certificate->expire_at)->format('Y-m-d') : 'бессрочно',
				'certificate_status_name' => $certificateStatus ? $certificateStatus->name : '',
				'bill_number' => $positionBill ? $positionBill->number : '',
				'bill_status_alias' => $positionBillStatus ? $positionBillStatus->alias : '',
				'bill_status_name' => $positionBillStatus ? $positionBillStatus->name : '',
				'bill_payment_method_name' => $positionBillPaymentMethod ? $positionBillPaymentMethod->name : '',
			];
		}
		
		//\Log::debug($certificateItems);
		
		$data = [
			'certificateItems' => $certificateItems,
		];
		
		/*$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-personal-selling-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new PersonalSellingReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}*/
		
		$VIEW = view('admin.report.certificates.list', $data);
		
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
				'bill_created_at' => $bill->created_at,
				'card_number' => $bill->aeroflot_card_number,
				'bill_amount' => $bill->amount,
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
				'transaction_order_id' => $bill->aeroflot_transaction_order_id,
				'city_name' => $cityName,
				'location_name' => $locationName,
				'transaction_created_at' => $bill->transaction_created_at ? $bill->transaction_created_at->format('Y-m-d H:i:s') : '',
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
	 * @param $fileName
	 * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function getExportFile($fileName)
	{
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		return Storage::disk('private')->download('report/' . $fileName);
	}
}