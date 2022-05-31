<?php

namespace App\Http\Controllers;

use App\Exports\NpsReportExport;
use App\Models\Bill;
use App\Models\Content;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Status;
use App\Models\User;
use App\Repositories\CityRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\HelpFunctions;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportController extends Controller {
	private $request;
	private $cityRepo;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request, CityRepository $cityRepo) {
		$this->request = $request;
		$this->cityRepo = $cityRepo;
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
			'userNps' => $userNps
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
		
		$billItems = $dealIds = [];
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
			
			// кол-во счетов
			++$billItems[$bill->location_id][$bill->user_id]['bill_count'];
			// сумма счетов
			$billItems[$bill->location_id][$bill->user_id]['bill_sum'] += $bill->amount;
			if ($bill->status && $bill->status->alias == Bill::PAYED_STATUS) {
				// кол-во оплаченных счетов
				++$billItems[$bill->location_id][$bill->user_id]['payed_bill_count'];
				// сумма оплаченных счетов
				$billItems[$bill->location_id][$bill->user_id]['payed_bill_sum'] += $bill->amount;
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
		
		$userItems = [];
		$users = User::where('enable', true)
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
		
		$data = [
			'billItems' => $billItems,
			'userItems' => $userItems,
			'cities' => $cities,
		];
		
		//\Log::debug($billItems);
		
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