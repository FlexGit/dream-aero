<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Deal;
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
	 * @param $dealId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function add($dealId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$deal = Deal::find($dealId);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		$VIEW = view('admin.event.modal.add', [
			'deal' => $deal,
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
			->get();

		$cities = City::orderBy('version', 'desc')
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
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
			/*'deal_id' => 'required|numeric|min:0|not_in:0',*/
			'location_id' => 'required_if:source,deal|numeric|min:0|not_in:0',
			/*'flight_simulator_id' => 'required|numeric|min:0|not_in:0',*/
			'start_at_date' => 'required_if:source,deal|date',
			'start_at_time' => 'required_if:source,deal',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				/*'deal_id' => 'Сделка',*/
				'location_id' => 'Локация',
				/*'flight_simulator_id' => 'Авиатренажер',*/
				'start_at_date' => 'Дата начала полета',
				'start_at_time' => 'Время начала полета',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$deal = Deal::find($this->request->deal_id);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		}

		if (!$deal->product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if ($this->request->source == 'deal') {
			$location = Location::find($this->request->location_id);
			if (!$location) {
				return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
			}

			if (!$location->city) {
				return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
			}

			$simulator = FlightSimulator::find($this->request->flight_simulator_id);
			if (!$simulator) {
				return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
			}
		}

		$event = new Event();
		$event->event_type = Event::EVENT_TYPE_DEAL;
		$event->deal_id = $deal->id ?? 0;
		$event->city_id = $location->city->id ?? 0;
		$event->location_id = $location->id ?? 0;
		$event->flight_simulator_id = $simulator->id ?? 0;
		$event->start_at = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
		$event->stop_at = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->addMinutes($deal->product->duration ?? 0)->format('Y-m-d H:i');
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
			/*'deal_id' => 'required|numeric|min:0|not_in:0',*/
			'location_id' => 'required_if:source,deal|numeric|min:0|not_in:0',
			/*'flight_simulator_id' => 'required|numeric|min:0|not_in:0',*/
			'start_at_date' => 'required_if:source,deal|date',
			'start_at_time' => 'required_if:source,deal',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				/*'deal_id' => 'Сделка',*/
				'location_id' => 'Локация',
				/*'flight_simulator_id' => 'Авиатренажер',*/
				'start_at_date' => 'Дата начала полета',
				'start_at_time' => 'Время начала полета',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$event = Event::find($id);
		if (!$event) return response()->json(['status' => 'error', 'reason' => 'Событие не найдено']);

		$deal = Deal::find($event->deal_id);
		if (!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не найдена']);
		}

		if ($this->request->product_id) {
			$product = Product::find($this->request->product_id);
			if (!$product) {
				return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
			}
		} elseif (!$product = $deal->product) {
			return response()->json(['status' => 'error', 'reason' => 'Продукт не найден']);
		}

		if ($this->request->source == 'deal') {
			$location = Location::find($this->request->location_id);
			if (!$location) {
				return response()->json(['status' => 'error', 'reason' => 'Локация не найдена']);
			}

			if (!$location->city) {
				return response()->json(['status' => 'error', 'reason' => 'Город не найден']);
			}

			$simulator = FlightSimulator::find($this->request->flight_simulator_id);
			if (!$simulator) {
				return response()->json(['status' => 'error', 'reason' => 'Авиатренажер не найден']);
			}
		}

		try {
			\DB::beginTransaction();

			$deal = $event->deal;
			if ($deal && $this->request->product_id) {
				$deal->product_id = $this->request->product_id;
				$deal->save();
			}

			if ($this->request->source == 'deal') {
				$event->city_id = $location->city->id ?? 0;
				$event->location_id = $location->id ?? 0;
				$event->flight_simulator_id = $simulator->id ?? 0;
				$event->start_at = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->format('Y-m-d H:i');
				$event->stop_at = Carbon::parse($this->request->start_at_date . ' ' . $this->request->start_at_time)->addMinutes($product->duration ?? 0)->format('Y-m-d H:i');
				$event->extra_time = (int)$this->request->extra_time;
				$event->is_repeated_flight = (bool)$this->request->is_repeated_flight;
				$event->is_unexpected_flight = (bool)$this->request->is_unexpected_flight;
			} elseif ($this->request->source == 'calendar') {
				$event->start_at = Carbon::parse($this->request->start_at)->format('Y-m-d H:i');
				$event->stop_at = Carbon::parse($this->request->stop_at)->subMinutes($event->extra_time ?? 0)->format('Y-m-d H:i');
			}
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

	public function clear()
	{
		if (!$this->request->user()->isSuperAdmin()) {
			redirect(route('eventIndex'));
		}

		Deal::where('user_id', 0)
			->delete();
	}
}
