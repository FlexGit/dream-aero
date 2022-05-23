<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Repositories\CityRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\HelpFunctions;

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
		
		return view('admin.report.nps.index', [
		]);
	}
	
	public function npsGetListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$dateFromAt = $this->request->filter_date_from_at ?? '';
		$dateToAt = $this->request->filter_date_to_at ?? '';
		$role = $this->request->filter_role ?? '';
		
		if (!$dateFromAt && !$dateToAt) {
			$dateFromAt = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
			$dateToAt = Carbon::now()->format('Y-m-d H:i:s');
		}
		
		$events = Event::where('event_type', Event::EVENT_TYPE_DEAL);
		if ($dateFromAt) {
			$events = $events->where('stop_at', '>=', $dateFromAt);
		}
		if ($dateToAt) {
			$events = $events->where('stop_at', '<=', $dateToAt);
		}
		$events = $events->orderBy('start_at')
			->get();
		
		$userAssessments = [];
		foreach ($events as $event) {
			if ($event->pilot_id) {
				if (!isset($userAssessments[$event->pilot_id])) {
					$userAssessments[$event->pilot_id] = [];
					$userAssessments[$event->pilot_id] = [
						'good' => 0,
						'neutral' => 0,
						'bad' => 0,
					];
				}
				
				if ($event->pilot_assessment >= 9) {
					++$userAssessments[$event->pilot_id]['good'];
				}
				elseif ($event->pilot_assessment >= 7 && $event->pilot_assessment <= 8) {
					++$userAssessments[$event->pilot_id]['neutral'];
				}
				elseif ($event->pilot_assessment >= 1 && $event->pilot_assessment <= 6) {
					++$userAssessments[$event->pilot_id]['bad'];
				}
			}
			
			if ($event->user_id) {
				if (!isset($userAssessments[$event->user_id])) {
					$userAssessments[$event->user_id] = [];
					$userAssessments[$event->user_id] = [
						'good' => 0,
						'neutral' => 0,
						'bad' => 0,
					];
				}
				
				if ($event->admin_assessment >= 9) {
					++$userAssessments[$event->user_id]['good'];
				}
				else if ($event->admin_assessment >= 7 && $event->admin_assessment <= 8) {
					++$userAssessments[$event->user_id]['neutral'];
				}
				elseif ($event->admin_assessment >= 1 && $event->admin_assessment <= 6) {
					++$userAssessments[$event->user_id]['bad'];
				}
			}
		}
		
		//\Log::debug($userAssessments);
		
		$userNps = [];
		foreach ($userAssessments as $userId => $assessment) {
			$goodBadDiff = $assessment['good'] - $assessment['bad'];
			$goodNeutralBadSum = $assessment['good'] + $assessment['neutral'] + $assessment['bad'];
			if (!$goodNeutralBadSum) continue;
			
			$userNps[$userId] = round($goodBadDiff / $goodNeutralBadSum * 100, 1);
			//\Log::debug($userId . ' - ' . $goodBadDiff . ' - ' . $goodNeutralBadSum . ' = ' . $userNps[$userId]);
		}
		
		//\Log::debug($userNps);
		
		$users = User::where('enable', true)
			->orderBy('lastname')
			->orderBy('name')
			->orderBy('middlename');
		if ($role) {
			$users = $users->where('role', $role);
		}
		$users = $users->get();

		$cities = $this->cityRepo->getList($this->request->user());
		
		$VIEW = view('admin.report.nps.list', [
			'events' => $events,
			'users' => $users,
			'cities' => $cities,
			'userAssessments' => $userAssessments,
			'userNps' => $userNps
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
}