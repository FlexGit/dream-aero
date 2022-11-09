<?php

namespace App\Http\Controllers;

use App\Exports\AeroflotAccrualReportExport;
use App\Exports\AeroflotWriteOffReportExport;
use App\Exports\ContractorSelfMadePayedDealsReportExport;
use App\Exports\FlightLogMultipleSheetsReportExport;
use App\Exports\FlightLogReportExport;
use App\Exports\NpsReportExport;
use App\Exports\PersonalSellingReportExport;
use App\Exports\PlatformDataReportExport;
use App\Exports\SpontaneousRepeatedReportExport;
use App\Models\Bill;
use App\Models\Product;
use App\Models\Certificate;
use App\Models\City;
use App\Models\Content;
use App\Models\DealPosition;
use App\Models\Event;
use App\Models\FlightSimulator;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\PlatformData;
use App\Models\PlatformLog;
use App\Models\ProductType;
use App\Models\Promo;
use App\Models\Score;
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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Validator;

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
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
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
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
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
		
		if ($isExport) {
			$xls = new Spreadsheet();
			$sheetIndex = 0;
			foreach ($cities as $city) {
				$xls->createSheet($sheetIndex);
				$xls->setActiveSheetIndex($sheetIndex);
				$activeSheet = $xls->getActiveSheet();
				$activeSheet->setTitle($city->name);
				
				$i = 1;
				foreach($users as $user) {
					if ($user->city_id != $city->id || !isset($userNps[$user->id])) continue;
					
					$activeSheet->setCellValueByColumnAndRow($i, 1, $user->fioFormatted());
					$activeSheet->setCellValueByColumnAndRow($i, 2, $userNps[$user->id] . '%');
					$activeSheet->setCellValueByColumnAndRow($i, 3, $userAssessments[$user->id]['good']);
					$activeSheet->setCellValueByColumnAndRow($i, 4, $userAssessments[$user->id]['neutral']);
					$activeSheet->setCellValueByColumnAndRow($i, 5, $userAssessments[$user->id]['bad']);
					
					$j = 6;
					foreach ($eventItems[$user->id] ?? [] as $eventItem) {
						if (!$eventItem['assessment']) continue;
						
						$activeSheet->setCellValueByColumnAndRow($i, $j, $eventItem['assessment']);
						
						++$j;
					}
					
					for($col = 'A';$col !== 'Z';$col++) {
						$activeSheet->getColumnDimension($col)->setAutoSize(true);
					}
					$activeSheet->getStyle('A1:Z500')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					$activeSheet->getStyle('A2:Z2')->getFont()->setBold(true)->getColor()->setRGB('17a2b8');
					$activeSheet->getStyle('A3:Z3')->getFont()->setBold(true)->getColor()->setRGB('28a745');
					$activeSheet->getStyle('A4:Z4')->getFont()->setBold(true)->getColor()->setRGB('ffc107');
					$activeSheet->getStyle('A5:Z5')->getFont()->setBold(true)->getColor()->setRGB('dc3545');
					
					++$i;
				}
				
				++$sheetIndex;
			}
			
			$xls->setActiveSheetIndex(0);
			$writer = new Xlsx($xls);

			ob_start();
			$writer->save('php://output');
			$content = ob_get_contents();
			ob_end_clean();
			
			$reportFileName = 'report-nps-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			Storage::disk('private')->put('report/' . $reportFileName, $content);
			
			return response()->json(['status' => 'success', 'fileName' => $reportFileName]);
		}
		
		$data = [
			'eventItems' => $eventItems,
			'users' => $users,
			'cities' => $cities,
			'userAssessments' => $userAssessments,
			'userNps' => $userNps,
		];

		$VIEW = view('admin.report.nps.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function flightLogIndex()
	{
		$user = \Auth::user();
		
		if ($user->isAdmin()) {
			$cities = $this->cityRepo->getList($user);
		} else {
			$cities = $this->cityRepo->getList();
		}
		$pilot = $user->isPilot() ? User::find($user->id) : null;
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-flight-log');
		
		return view('admin.report.flight-log.index', [
			'cities' => $cities,
			'pilot' => $pilot,
			'user' => $user,
			'page' => $page,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function flightLogGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$locationId = $this->request->filter_location_id ?? 0;
		$simulatorId = $this->request->filter_simulator_id ?? 0;
		$pilotId = $this->request->filter_pilot_id ?? 0;
		$isExport = filter_var($this->request->is_export, FILTER_VALIDATE_BOOLEAN);
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
		}
		
		$location = $locationId ? Location::find($locationId) : null;
		$simulator = $simulatorId ? FlightSimulator::find($simulatorId) : null;
		$city = $location ? $location->city : null;
		$now = Carbon::now()->format('Y-m-d H:i:s');
		$period = CarbonPeriod::create($dateFromAt, $dateToAt);
		
		$shiftItems = [];
		//\DB::connection()->enableQueryLog();
		$shifts = Event::where('event_type', Event::EVENT_TYPE_SHIFT_PILOT)
			->where('start_at', '>=', Carbon::parse($dateFromAt)->startOfDay())
			->where('start_at', '<=', Carbon::parse($dateToAt)->endOfDay());
		if ($location && $simulator) {
			$shifts = $shifts->where('location_id', $location->id)
				->where('flight_simulator_id', $simulator->id);
		}
		/*if ($pilotId) {
			$shifts = $shifts->where('shift_pilot_id', $pilotId);
		}*/
		$shifts = $shifts->orderBy('start_at')
			->get();
		foreach ($shifts as $shift) {
			/** @var User $shiftPilot */
			$shiftPilot = $shift->user;
			$shiftPilotFio = $shiftPilot ? $shiftPilot->fioFormatted() : '';
			if ($shiftPilotFio) {
				$shiftItems[$shift->location_id][$shift->flight_simulator_id][Carbon::parse($shift->start_at)->format('d.m.Y')][] = $shiftPilotFio;
			}
		}
		/*if ($user->email == env('DEV_EMAIL')) {
			\Log::debug($shiftItems);
		}*/
		
		//\DB::connection()->enableQueryLog();
		$events = Event::whereIn('event_type', [Event::EVENT_TYPE_DEAL, Event::EVENT_TYPE_USER_FLIGHT])
			->where('start_at', '>=', Carbon::parse($dateFromAt)->startOfDay())
			->where('start_at', '<=', Carbon::parse($dateToAt)->endOfDay())
			->where('stop_at', '<', $now);
		if ($location && $simulator) {
			$events = $events->where('location_id', $location->id)
				->where('flight_simulator_id', $simulator->id);
		}
		if (!$user->isSuperAdmin() && $user->city) {
			$events = $events->where('city_id', $user->city->id);
		}
		$events = $events->orderBy('start_at')
			->get();
		//\Log::debug(\DB::getQueryLog());
		
		$items = [];
		foreach ($events as $event) {
			/** @var DealPosition $position */
			$position = $event->dealPosition;
			$bills = $position ? $position->bills : [];
			$promo = $position ? $position->promo : null;
			$promocode = $position ? $position->promocode : null;
			$product = $position ? $position->product : null;
			$productType = $product ? $product->productType : null;
			/** @var User $employee */
			$employee = $event->employee;
			
			$extendedText = '';
			
			if ($user->email == env('DEV_EMAIL')) {
				$extendedText .= $event->id;
				$extendedText .= $position ? $position->number : '';
			}
			
			/*if ($productType && in_array($productType->alias, [ProductType::COURSES_ALIAS, ProductType::VIP_ALIAS])) {*/
			if ($product) {
				$extendedText .= ', ' . $product->name;
			}
			/*}*/
			
			$pilotSum = $event->nominal_price;
			
			$eventTypeText = '';
			if ($event->event_type == Event::EVENT_TYPE_USER_FLIGHT) {
				if ($employee->isPilot()) {
					$pilotSum = 0;
					$eventTypeText .= 'Бесплатный полет пилота' . ($employee ? ' ' . $employee->fioFormatted() : '');
				} else {
					$pilotSum = $pilotSum * 0.8;
					$eventTypeText .= 'Бесплатный полет сотрудника' . ($employee ? ' ' . $employee->fioFormatted() : '');
				}
			} elseif ($event->event_type == Event::EVENT_TYPE_TEST_FLIGHT) {
				$eventTypeText .= 'Тестовый полет пилота' . ($employee ? ' ' . $employee->fioFormatted() : '');
				$pilotSum = 0;
			} else {
				if ($promo && $promo->alias == Promo::DIRECTOR_ALIAS) {
					$pilotSum = $pilotSum * 0.8;
				}
			}
			$promoText = $promo ? ($promo->name . ($promo->discount ? ' (' . $promo->discount->valueFormatted(). ')' : '')) : '';
			$promocodeText = $promocode ? ($promocode->number . ($promocode->discount ? ' (' . $promocode->discount->valueFormatted(). ')' : '')) : '';
			$score = $position ? $position->score()->where('type', Score::USED_TYPE)->sum('score') : null;
			$scoreText = $score ? $score . ' баллами' : '';
			
			$paidSum = 0;
			$paymentMethods = [];
			foreach ($bills as $bill) {
				if (!$bill->status || in_array($bill->status->alias, [Bill::CANCELED_STATUS, Bill::NOT_PAYED_STATUS])) continue;
				
				$paidSum += $bill->amount;
				if ($bill->paymentMethod) {
					$paymentMethods[] = $bill->paymentMethod->name;
				}
			}
			$paymentMethods = array_unique($paymentMethods);
			
			$certificateNumber = '';
			$isOldCertificate = false;
			if ($position) {
				$certificate = $position->certificate;
				if ($certificate) {
					$certificateNumber = $certificate->number;
					if ($certificate->product) {
						$certificatePosition = DealPosition::where('is_certificate_purchase', true)
							->where('certificate_id', $certificate->id)
							->first();
						$bills = $certificatePosition ? $certificatePosition->bills : [];
						$promo = $certificatePosition ? $certificatePosition->promo : null;
						$promocode = $certificatePosition ? $certificatePosition->promocode : null;
						
						$promoText = $promo ? ($promo->name . ($promo->discount ? ' (' . $promo->discount->valueFormatted(). ')' : '')) : '';
						$promocodeText = $promocode ? ($promocode->number . ($promocode->discount ? ' (' . $promocode->discount->valueFormatted(). ')' : '')) : '';
						$score = $certificatePosition ? $certificatePosition->score()->where('type', Score::USED_TYPE)->sum('score') : null;
						$scoreText = $score ? $score . ' баллами' : '';
						
						foreach ($bills as $bill) {
							if (!$bill->status || in_array($bill->status->alias, [Bill::CANCELED_STATUS, Bill::NOT_PAYED_STATUS])) continue;
							
							$paidSum += $bill->amount;
						}
					} else {
						// сертификаты из старой системы
						$certificateData = $certificate->data_json;
						$paidSum += isset($certificateData['amount']) ? (int) str_replace(' ', '', $certificateData['amount']) : 0;
						$isOldCertificate = true;
					}
				}
			}
			
			// фактический пилот
			$pilot = $event->pilot;
			
			if (!$pilot) {
				// смена пилота во время данного события
				$pilotShiftEvent = Event::where('event_type', Event::EVENT_TYPE_SHIFT_PILOT)
					->where('start_at', '<=', $event->start_at)
					->where('stop_at', '>=', $event->start_at);
				if ($event->location && $event->simulator) {
					$pilotShiftEvent = $pilotShiftEvent->where('location_id', $event->location->id)
						->where('flight_simulator_id', $event->simulator->id);
				}
				/*if ($user->email == env('DEV_EMAIL')) {
					\Log::debug(\DB::getQueryLog());
				}*/
				/*if ($pilotId) {
					$pilotShiftEvent = $pilotShiftEvent->where('shift_pilot_id', $pilotId);
				}*/
				$pilotShiftEvent = $pilotShiftEvent->first();
				$pilot = $pilotShiftEvent ? $pilotShiftEvent->user : null;
			}
			
			if (!$pilot) {
				// смена первого из пилотов в данный день
				$pilotShiftEvent = Event::where('event_type', Event::EVENT_TYPE_SHIFT_PILOT)
					->where('start_at', '>=', Carbon::parse($event->start_at)->startOfDay()->format('Y-m-d H:i:s'))
					->where('stop_at', '<=', Carbon::parse($event->start_at)->endOfDay()->format('Y-m-d H:i:s'));
				if ($event->location && $event->simulator) {
					$pilotShiftEvent = $pilotShiftEvent->where('location_id', $event->location->id)
						->where('flight_simulator_id', $event->simulator->id);
				}
				/*if ($pilotId) {
					$pilotShiftEvent = $pilotShiftEvent->where('shift_pilot_id', $pilotId);
				}*/
				$pilotShiftEvent = $pilotShiftEvent->orderBy('start_at')
					->first();
				$pilot = $pilotShiftEvent ? $pilotShiftEvent->user : null;
			}
			
			if ($pilotId && $pilot && $pilotId != $pilot->id) continue;
			
			$details = [
				$certificateNumber,
				$paymentMethods ? implode(', ', $paymentMethods) : ((!$certificateNumber && !$eventTypeText) ? 'счет не привязан к позиции' . ($position ? ' ' . $position->number : '') : ''),
				$promoText,
				$promocodeText,
				$scoreText,
				$eventTypeText,
				$extendedText,
			];
			
			$items[$event->location_id][$event->flight_simulator_id][Carbon::parse($event->start_at)->format('d.m.Y')][] = [
				'id' => $event->id,
				'start_at_date' => Carbon::parse($event->start_at)->format('d.m.Y'),
				'start_at_time' => Carbon::parse($event->start_at)->format('H:i'),
				'duration' => Carbon::parse($event->stop_at)->diffInMinutes(Carbon::parse($event->start_at)),
				'paid_sum' => $paidSum,
				'pilot_sum' => $pilotSum,
				'actual_pilot_sum' => $event->actual_pilot_sum,
				'details' => implode(', ', array_filter($details)),
				'pilot' => $pilot ? $pilot->fioFormatted() : '',
				'pilot_id' => $pilot ? $pilot->id : 0,
				'deal_id' => $event->deal_id,
				'is_old_certificate' => $isOldCertificate,
			];
		}
		
		if (!$user->isSuperAdmin()) {
			$cities = $this->cityRepo->getList($user);
		} else {
			$cities = $this->cityRepo->getList();
		}

		$data = [
			'items' => $items,
			'dates' => $period->toArray(),
			'shiftItems' => $shiftItems,
			'cities' => $cities,
			'cityId' => $city ? $city->id : 0,
			'locationId' => $location ? $location->id : 0,
			'simulatorId' => $simulator ? $simulator->id : 0,
			'pilotId' => $pilotId,
			'user' => $user,
		];
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-flight-log-' . (($location && $simulator) ? $location->alias . '-' . $simulator->alias . '-' : '') . $user->id . '-' . date('YmdHis') . '.xlsx';
			if ($location && $simulator) {
				$exportResult = Excel::store(new FlightLogReportExport($data, $location, $simulator), 'report/' . $reportFileName);
			} else {
				$exportResult = Excel::store(new FlightLogMultipleSheetsReportExport($data, $cities), 'report/' . $reportFileName);
			}
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		if ($location && $simulator) {
			$VIEW = view('admin.report.flight-log.location-list', $data);
		} else {
			$VIEW = view('admin.report.flight-log.list', $data);
		}
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function personalSellingIndex()
	{
		$user = \Auth::user();
		
		if (!$user->isAdminOrHigher()) {
			abort(404);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'report-personal-selling');
		
		return view('admin.report.personal-selling.index', [
			'page' => $page,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
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
			->orWhere('payed_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('created_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->orWhere('payed_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->orderBy('created_at')
			->orderBy('payed_at')
			->get();
		if ($user->isAdmin()) {
			$bills = $bills->where('user_id', $user->id);
		}
		
		$totalItems = $billItems = $paymentMethodSumItems = $dealIds = [];
		$totalSum = $i = 0;
		foreach ($bills as $bill) {
			if ($bill->payed_at && !Carbon::parse($bill->payed_at)->between(Carbon::parse($dateFromAt)->startOfDay(), Carbon::parse($dateToAt)->endOfDay())) {
				continue;
			}
			
			$deal = $bill->deal;
			
			$billItems[$bill->user_id][$i] = [
				'bill_number' => $bill->number,
				'bill_status' => $bill->status ? $bill->status->name : '-',
				'bill_amount' => $bill->amount,
				'bill_payed_at' => $bill->payed_at ? $bill->payed_at->format('Y-m-d H:i:s') : '-',
				'bill_location' => $bill->location ? $bill->location->name : '-',
				'deal_number' => $deal->number,
				'deal_status' => $deal->status ? $deal->status->name : '-',
			];
			
			if (!isset($totalItems[$bill->user_id])) {
				$totalItems[$bill->user_id] = [
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
			++$totalItems[$bill->user_id]['bill_count'];
			// сумма счетов
			$totalItems[$bill->user_id]['bill_sum'] += $bill->amount;
			if ($bill->status && $bill->status->alias == Bill::PAYED_STATUS) {
				// кол-во оплаченных счетов
				++$totalItems[$bill->user_id]['payed_bill_count'];
				// сумма оплаченных счетов
				$totalItems[$bill->user_id]['payed_bill_sum'] += $bill->amount;
				// сумма оплаченных счетов конкретного способа оплаты
				$paymentMethodSumItems[$bill->payment_method_id] += $bill->amount;
				$totalSum += $bill->amount;
			}
			$deal = $bill->deal;
			if ($deal && !in_array($bill->deal_id, $totalItems[$bill->user_id]['deal_ids'])) {
				$totalItems[$bill->user_id]['deal_ids'][] = $deal->id;
				// кол-во сделок
				++$totalItems[$bill->user_id]['deal_count'];
				// сумма сделок
				$totalItems[$bill->user_id]['deal_sum'] += $deal->amount();
			}
			
			++$i;
		}
		
		$shiftItems = [];
		$shifts = Event::where('event_type', Event::EVENT_TYPE_SHIFT_ADMIN)
			->where('start_at', '>=', Carbon::parse($dateFromAt)->startOfDay()->format('Y-m-d H:i:s'))
			->where('start_at', '<=', Carbon::parse($dateToAt)->endOfDay()->format('Y-m-d H:i:s'))
			->get();
		if ($user->isAdmin()) {
			$shifts = $shifts->where('user_id', $user->id);
		}
		foreach ($shifts as $shift) {
			if (!isset($shiftItems[$shift->user_id])) {
				$shiftItems[$shift->user_id] = 0;
			}
			++$shiftItems[$shift->user_id];
		}
		
		$userItems = [];
		$users = User::where('enable', true)
			->whereIn('role', [User::ROLE_ADMIN, User::ROLE_ADMIN_OB, User::ROLE_SUPERADMIN])
			->orderBy('lastname')
			->orderBy('name')
			->orderBy('middlename')
			->get();
		if ($user->isAdmin()) {
			$users = $users->where('id', $user->id);
		}
		foreach ($users as $user) {
			$userItems[] = [
				'id' => $user->id,
				'fio' => $user->fioFormatted(),
				'role' => User::ROLES[$user->role],
				'city_name' => $user->city ? $user->city->name : '',
			];
		}
		
		/*$cities = $this->cityRepo->getList($this->request->user());*/
		$paymentMethods = $this->paymentRepo->getPaymentMethodList(false);
		
		$data = [
			'billItems' => $billItems,
			'totalItems' => $totalItems,
			'paymentMethodSumItems' => $paymentMethodSumItems,
			'totalSum' => $totalSum,
			'shiftItems' => $shiftItems,
			'userItems' => $userItems,
			/*'cities' => $cities,*/
			'paymentMethods' => $paymentMethods,
		];
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-personal-selling-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new PersonalSellingReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		$VIEW = view('admin.report.personal-selling.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
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
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
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
		
		$reportFileName = '';
		if ($isExport) {
			$reportFileName = 'report-spontaneous-repeated-' . $user->id . '-' . date('YmdHis') . '.xlsx';
			$exportResult = Excel::store(new SpontaneousRepeatedReportExport($data), 'report/' . $reportFileName);
			if (!$exportResult) {
				return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
			}
		}
		
		$VIEW = view('admin.report.unexpected-repeated.list', $data);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW, 'fileName' => $reportFileName]);
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
				// для расчета MWP (Motion Without Permit)
				$calendarEvents = Event::where('location_id', $platformData->location_id)
					->where('flight_simulator_id', $platformData->flight_simulator_id)
					->whereIn('event_type', [Event::EVENT_TYPE_DEAL, Event::EVENT_TYPE_TEST_FLIGHT, Event::EVENT_TYPE_USER_FLIGHT])
					->where('start_at', '>=', Carbon::parse($platformData->data_at)->startOfDay()->format('Y-m-d H:i:s'))
					->where('start_at', '<=', Carbon::parse($platformData->data_at)->endOfDay()->format('Y-m-d H:i:s'))
					->orderBy('start_at')
					->get()
					->toArray();
				//\Log::debug($calendarEvents);
				
				// группировка событий календаря по времени
				$calendarEvents = $platformData->groupEvents($calendarEvents, 1);
				
				$mwpItems = $platformData->mwp($calendarEvents);
				/*if ($platformData->location_id == 18) {
					\Log::debug($mwpItems);
				}*/
				$mwp = 0;
				foreach ($mwpItems as $hour => $log) {
					foreach ($log as $mwpValue) {
						$mwp += $mwpValue;
					}
				}
				
				$items[$platformData->location_id][$platformData->flight_simulator_id][$platformData->data_at] = [
					'id' => $platformData->id,
					'platform_time' => $platformData->total_up ? HelpFunctions::mailGetTimeMinutes($platformData->total_up) : 0,
					'ianm_time' => $platformData->in_air_no_motion ? HelpFunctions::mailGetTimeMinutes($platformData->in_air_no_motion) : 0,
					'mwp_time' => ($mwp >= 10) ? $mwp : 0,
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
			'12' => 'Декабрь',
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
		$events = Event::where('location_id', $locationId)
			->where('flight_simulator_id', $simulatorId)
			->whereIn('event_type', [Event::EVENT_TYPE_DEAL, Event::EVENT_TYPE_TEST_FLIGHT, Event::EVENT_TYPE_USER_FLIGHT])
			->where('start_at', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'))
			->where('start_at', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'))
			->orderBy('start_at')
			->get()
			->toArray();
		foreach ($events ?? [] as $event) {
			$items['admin'][Carbon::parse($event['simulator_up_at'])->format('H')][] = [
				'start_at' => Carbon::parse($event['simulator_up_at'])->format('H:i'),
				'stop_at' => Carbon::parse($event['simulator_down_at'])->format('H:i'),
			];
			$items['calendar'][Carbon::parse($event['start_at'])->format('H')][] = [
				'start_at' => Carbon::parse($event['start_at'])->format('H:i'),
				'stop_at' => Carbon::parse($event['stop_at'])->addMinutes($event['extra_time'])->format('H:i'),
			];
		}
		
		$platformData = PlatformData::where('location_id', $locationId)
			->where('flight_simulator_id', $simulatorId)
			->where('data_at', $date)
			->first();
		
		if ($platformData) {
			// группировка событий календаря по времени
			$events = $platformData->groupEvents($events, 1);
			if ($user->email == env('DEV_EMAIL')) {
				\Log::debug($events);
			}
			
			// события платформы
			foreach ($platformData->logs as $log) {
				$items[$log->action_type][Carbon::parse($log->start_at)->format('H')][] = [
					'start_at' => Carbon::parse($log->start_at)->format('H:i'),
					'stop_at' => Carbon::parse($log->stop_at)->format('H:i'),
				];
				
				// расчет MWP (Motion Without Permit)
				if ($log->action_type != PlatformLog::IN_UP_ACTION_TYPE) continue;
				if (!Carbon::parse($log->start_at)->diffInMinutes($log->stop_at)) continue;
				
				$serverStartAt = Carbon::parse($platformData->data_at . ' ' . $log->start_at);
				$serverStartAtWithLag = Carbon::parse($platformData->data_at . ' ' . $log->start_at)->addMinutes(PlatformLog::MWP_MINUTE_LAG);
				$serverStopAt = Carbon::parse($platformData->data_at . ' ' . $log->stop_at);
				$serverStopAtWithLag = Carbon::parse($platformData->data_at . ' ' . $log->stop_at)->subMinutes(PlatformLog::MWP_MINUTE_LAG);
				
				foreach ($events ?? [] as $event) {
					$eventStopAtWithExtraTime = Carbon::parse($event['stop_at'])->addMinutes($event['extra_time'])->format('Y-m-d H:i:s');
					
					//\Log::debug('Server: ' . $log->start_at . ' - ' . $log->stop_at);
					//\Log::debug('Server: ' . $event['start_at'] . ' - ' . $eventStopAtWithExtraTime);
					
					// время подъема сервера попадает в интервал события,
					// и время опускания сервера попадает в интервал события
					if (($serverStartAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStartAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
						&& ($serverStopAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStopAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
					) {
						$items[PlatformLog::MWP_ACTION_TYPE][Carbon::parse($log->start_at)->format('H')][$log->id] = [
							'start_at' => Carbon::parse($log->start_at)->format('H:i'),
							'mwp_time' => 0,
							'case' => 5,
						];
						/*if ($user->email == env('DEV_EMAIL') && isset($items[PlatformLog::MWP_ACTION_TYPE])) {
							\Log::debug($items[PlatformLog::MWP_ACTION_TYPE]);
						}*/
						break;
					}
					
					// время подъема сервера попадает в интервал события,
					// и время опускания сервера позже времени окончания события
					if (($serverStartAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStartAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
						&& ($serverStopAt->gt($eventStopAtWithExtraTime) || $serverStopAtWithLag->gt($eventStopAtWithExtraTime))
						&& ($serverStopAt->diffInMinutes($eventStopAtWithExtraTime) < 30)
					) {
						$items[PlatformLog::MWP_ACTION_TYPE][Carbon::parse($log->start_at)->format('H')][$log->id] = [
							'start_at' => Carbon::parse($log->start_at)->format('H:i'),
							'mwp_time' => $serverStopAt->diffInMinutes($eventStopAtWithExtraTime),
							'case' => 1,
						];
						/*if ($user->email == env('DEV_EMAIL') && isset($items[PlatformLog::MWP_ACTION_TYPE])) {
							\Log::debug($items[PlatformLog::MWP_ACTION_TYPE]);
						}*/
						break;
					}
					
					// время опускания сервера попадает в интервал события,
					// и время подъема сервера раньше времени начала события
					if (($serverStopAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStopAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
						&& ($serverStartAt->lt($event['start_at']) || $serverStartAtWithLag->lt($event['start_at']))
						&& ($serverStartAt->diffInMinutes($event['start_at']) < 30)
					) {
						$items[PlatformLog::MWP_ACTION_TYPE][Carbon::parse($log->start_at)->format('H')][$log->id] = [
							'start_at' => Carbon::parse($log->start_at)->format('H:i'),
							'mwp_time' => $serverStartAt->diffInMinutes($event['start_at']),
							'case' => 2,
						];
						/*if ($user->email == env('DEV_EMAIL') && isset($items[PlatformLog::MWP_ACTION_TYPE])) {
							\Log::debug($items[PlatformLog::MWP_ACTION_TYPE]);
						}*/
						break;
					}
					
					// время подъема сервера раньше времени начала события,
					// и время опускания сервера позже времени окончания события
					if (($serverStartAt->lt($event['start_at']) || $serverStartAtWithLag->lt($event['start_at']))
						&& ($serverStopAt->gt($eventStopAtWithExtraTime) || $serverStopAtWithLag->gt($eventStopAtWithExtraTime))
					) {
						$items[PlatformLog::MWP_ACTION_TYPE][Carbon::parse($log->start_at)->format('H')][$log->id] = [
							'start_at' => Carbon::parse($log->start_at)->format('H:i'),
							'mwp_time' => $serverStartAt->diffInMinutes($event['start_at']) + $serverStopAt->diffInMinutes($eventStopAtWithExtraTime),
							'case' => 3,
						];
						/*if ($user->email == env('DEV_EMAIL') && isset($items[PlatformLog::MWP_ACTION_TYPE])) {
							\Log::debug($items[PlatformLog::MWP_ACTION_TYPE]);
						}*/
						break;
					}
				}
				
				// данный элемент сервера уже был ранее сопоставлен событию календаря
				if (isset($items[PlatformLog::MWP_ACTION_TYPE][Carbon::parse($log->start_at)->format('H')][$log->id])) continue;
				
				foreach ($events ?? [] as $event) {
					// время подъема сервера не попадает в интервал события,
					// и время опускания сервера не попадает в интервал события
					if (!$serverStartAt->isBetween($event['start_at'], $eventStopAtWithExtraTime)
						&& !$serverStartAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime)
						&& !$serverStopAt->isBetween($event['start_at'], $eventStopAtWithExtraTime)
						&& !$serverStopAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime)
					) {
						$items[PlatformLog::MWP_ACTION_TYPE][Carbon::parse($log->start_at)->format('H')][$log->id] = [
							'start_at' => Carbon::parse($log->start_at)->format('H:i'),
							'mwp_time' => Carbon::parse($log->stop_at)->diffInMinutes($log->start_at),
							'case' => 4,
						];
						if ($user->email == env('DEV_EMAIL') && isset($items[PlatformLog::MWP_ACTION_TYPE])) {
							\Log::debug($items[PlatformLog::MWP_ACTION_TYPE]);
							\Log::debug($serverStartAt->format('Y-m-d H:i:s') . ' - ' . $serverStartAtWithLag->format('Y-m-d H:i:s'));
							\Log::debug($serverStopAt->format('Y-m-d H:i:s') . ' - ' . $serverStopAtWithLag->format('Y-m-d H:i:s'));
							\Log::debug($event['start_at'] . ' - ' . $eventStopAtWithExtraTime);
						}
						break;
					}
				}
			}
		}
		
		$intervals = CarbonInterval::hour()->toPeriod(Carbon::parse($date . ' 08:00:00'), Carbon::parse($date . ' 23:59:59'));

		// собираем разные типы одного события в один интервал,
		// если из-за небольших расхождений по времени они попали в разные интервалы
		foreach ($intervals as $interval) {
			// сервер
			foreach ($items[PlatformLog::IN_UP_ACTION_TYPE][$interval->format('H')] ?? [] as $index => $item) {
				$calendarHourInterval = HelpFunctions::getHourInterval($item['start_at'], $interval->format('H'), $items['calendar']);
				if ($calendarHourInterval != $interval->format('H')) {
					$items[PlatformLog::IN_UP_ACTION_TYPE][$calendarHourInterval][] = $item;
					unset($items[PlatformLog::IN_UP_ACTION_TYPE][$interval->format('H')][$index]);
					sort($items[PlatformLog::IN_UP_ACTION_TYPE][$calendarHourInterval]);
				}
			}
			
			// mwp
			foreach ($items[PlatformLog::MWP_ACTION_TYPE][$interval->format('H')] ?? [] as $index => $item) {
				$calendarHourInterval = HelpFunctions::getHourInterval($item['start_at'], $interval->format('H'), $items['calendar']);
				if ($calendarHourInterval != $interval->format('H')) {
					$items[PlatformLog::MWP_ACTION_TYPE][$calendarHourInterval][] = $item;
					unset($items[PlatformLog::MWP_ACTION_TYPE][$interval->format('H')][$index]);
					sort($items[PlatformLog::MWP_ACTION_TYPE][$calendarHourInterval]);
				}
			}

			// админ
			foreach ($items['admin'][$interval->format('H')] ?? [] as $index => $item) {
				$calendarHourInterval = HelpFunctions::getHourInterval($item['start_at'], $interval->format('H'), $items['calendar']);
				if ($calendarHourInterval != $interval->format('H')) {
					$items['admin'][$calendarHourInterval][] = $item;
					unset($items['admin'][$interval->format('H')][$index]);
					sort($items['admin'][$calendarHourInterval]);
				}
			}
			
			// X-Plane
			foreach ($items[PlatformLog::IN_AIR_ACTION_TYPE][$interval->format('H')] ?? [] as $index => $item) {
				$calendarHourInterval = HelpFunctions::getHourInterval($item['start_at'], $interval->format('H'), $items['calendar']);
				if ($calendarHourInterval != $interval->format('H')) {
					$items[PlatformLog::IN_AIR_ACTION_TYPE][$calendarHourInterval][] = $item;
					unset($items[PlatformLog::IN_AIR_ACTION_TYPE][$interval->format('H')][$index]);
					sort($items[PlatformLog::IN_AIR_ACTION_TYPE][$calendarHourInterval]);
				}
			}
		}
		
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
		
		/*if (!$user->isAdminOrHigher()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}*/
		
		return Storage::disk('private')->download('report/' . $fileName);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function platformLoadData()
	{
		\Artisan::call('platform_data:load');
		$output = \Artisan::output();
		
		return response()->json(['status' => 'success', 'result' => $output]);
	}
}