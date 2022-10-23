<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Content;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Notification;
use App\Models\ProductType;
use App\Models\Promocode;
use App\Models\Score;
use App\Models\Status;
use App\Models\Contractor;
use App\Models\City;
use App\Repositories\StatusRepository;
use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;
use Validator;

class ContractorController extends Controller
{
	private $request;
	private $statusRepo;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request, StatusRepository $statusRepo) {
		$this->request = $request;
		$this->statusRepo = $statusRepo;
	}
	
	/**
	 * @param null $contractorId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index($contractorId = null)
	{
		$user = \Auth::user();
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name')
			->get();

		if ($contractorId) {
			$contractor = Contractor::find($contractorId);
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'contractors');
		
		return view('admin.contractor.index', [
			'cities' => $cities,
			'contractor' => $contractor ?? null,
			'page' => $page,
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
		
		$user = \Auth::user();
		
		$id = $this->request->id ?? 0;
		
		$contractors = Contractor::/*whereRelation('city', 'version', '=', $user->version)
			->*/orderBy('created_at', 'desc');
		if ($this->request->filter_city_id) {
			$contractors = $contractors->where('city_id', $this->request->filter_city_id);
		/*} elseif ($this->request->user()->city) {
			$contractors = $contractors->whereIn('city_id', [$this->request->user()->city->id, 0]);*/
		}
		if ($this->request->search_contractor) {
			$contractors = $contractors->where(function ($query) {
				$query->where('name', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('lastname', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('uuid', $this->request->search_contractor);
			});
		}
		/*if (!$user->isSuperAdmin()) {
			$contractors = $contractors->whereIn('city_id', [$user->city_id, 0]);
		}*/
		if ($id) {
			$contractors = $contractors->where('id', '<', $id);
		}
		$contractors = $contractors->limit(10)->get();
		
		$statuses = Status::where('is_active', true)
			->get();

		$VIEW = view('admin.contractor.list', [
			'contractors' => $contractors,
			'statuses' => $statuses,
			'user' => $user,
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
		
		$user = \Auth::user();
		
		$contractor = Contractor::find($id);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);

		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name');
		/*if ($this->request->user()->city) {
			$cities = $cities->where('id', $this->request->user()->city->id);
		}*/
		$cities = $cities->get();
		
		$VIEW = view('admin.contractor.modal.edit', [
			'contractor' => $contractor,
			'cities' => $cities,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function unite($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$user = \Auth::user();

		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}

		$contractor = Contractor::find($id);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		
		$statuses = $this->statusRepo->getList(Status::STATUS_TYPE_CONTRACTOR);

		$VIEW = view('admin.contractor.modal.unite', [
			'contractor' => $contractor,
			'statuses' => $statuses,
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @throws Throwable
	 */
	public function storeUnite($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		if (!$user->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$slaveContractor = Contractor::find($id);
		if (!$slaveContractor) {
			return response()->json(['status' => 'error', 'reason' => 'Объединяемый контрагент не найден']);
		}

		$rules = [
			'contractor_id' => 'required|numeric|min:0|not_in:0',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'contractor_id' => 'Основной контрагент',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$masterContractor = Contractor::find($this->request->contractor_id);
		if (!$masterContractor) {
			return response()->json(['status' => 'error', 'reason' => 'Основной контрагент']);
		}
		
		if ($slaveContractor->id == $masterContractor->id) {
			return response()->json(['status' => 'error', 'reason' => 'Контрагент не может быть объединен с самим собой']);
		}
		
		/*\DB::connection()->enableQueryLog();*/
		try {
			\DB::beginTransaction();
			
			// события
			Event::where('contractor_id', $slaveContractor->id)->update(['contractor_id' => $masterContractor->id]);
			
			// счета
			Bill::where('contractor_id', $slaveContractor->id)->update(['contractor_id' => $masterContractor->id]);
			
			// сделки
			Deal::where('contractor_id', $slaveContractor->id)->update(['contractor_id' => $masterContractor->id]);
			
			// баллы
			Score::where('contractor_id', $slaveContractor->id)->update(['contractor_id' => $masterContractor->id]);
			
			// промокоды
			Promocode::where('contractor_id', $slaveContractor->id)->update(['contractor_id' => $masterContractor->id]);
			
			//ToDo: использованные промокоды
			
			// уведомления
			Notification::where('contractor_id', $slaveContractor->id)->update(['contractor_id' => $masterContractor->id]);
			
			$slaveContractor->delete();
			
			\DB::commit();
		} catch (Throwable $e) {
			\DB::rollback();
			
			\Log::debug('500 - Contractor Unite ' . $slaveContractor->id . ' - ' . $masterContractor->id . ', ' . $e->getMessage());
			
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		/*\Log::debug(\DB::getQueryLog());*/
		
		return response()->json(['status' => 'success', 'message' => 'Контрагенты успешно объединены']);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$user = \Auth::user();
		
		$cities = City::where('version', $user->version)
			->orderByRaw("FIELD(alias, 'msk') DESC")
			->orderByRaw("FIELD(alias, 'spb') DESC")
			->orderBy('name');
		/*if ($this->request->user()->city) {
			$cities = $cities->where('id', $this->request->user()->city->id);
		}*/
		$cities = $cities->get();

		$VIEW = view('admin.contractor.modal.add', [
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
			'name' => 'required',
			'email' => 'required|email|unique_email',
			/*'phone' => 'sometimes|required|valid_phone',*/
			'city_id' => 'required|numeric|min:0|not_in:0|valid_city',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				/*'phone' => 'Телефон',*/
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$birthdate = $this->request->birthdate ?? null;

		$data = [];
		
		$contractor = new Contractor();
		$contractor->name = $this->request->name;
		$contractor->lastname = $this->request->lastname;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone ?? null;
		$contractor->city_id = $this->request->city_id;
		$contractor->birthdate = $birthdate ? Carbon::parse($birthdate)->format('Y-m-d') : null;
		$contractor->source = Contractor::ADMIN_SOURCE;
		$contractor->is_active = (bool)$this->request->is_active;
		$contractor->is_subscribed = (bool)$this->request->is_subscribed;
		$contractor->user_id = $this->request->user()->id;
		$contractor->data_json = $data;
		if (!$contractor->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Контрагент успешно добавлен']);
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

		$contractor = Contractor::find($id);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);

		if ($contractor->email == Contractor::ANONYM_EMAIL) {
			return response()->json(['status' => 'error', 'reason' => 'Контрагент Аноним не может быть изменен']);
		}

		$rules = [
			'name' => 'required',
			'email' => 'required|email|unique_email',
			/*'phone' => 'sometimes|required|valid_phone',*/
			'city_id' => 'required|numeric|min:0|not_in:0|valid_city',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'name' => 'Имя',
				'email' => 'E-mail',
				/*'phone' => 'Телефон',*/
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$birthdate = $this->request->birthdate ?? null;

		$data = [];
		
		$contractor->name = $this->request->name;
		$contractor->lastname = $this->request->lastname;
		$contractor->email = $this->request->email;
		$contractor->phone = $this->request->phone ?? null;
		$contractor->city_id = $this->request->city_id;
		$contractor->birthdate = $birthdate ? Carbon::parse($birthdate)->format('Y-m-d') : null;
		$contractor->is_active = (bool)$this->request->is_active;
		$contractor->is_subscribed = (bool)$this->request->is_subscribed;
		$contractor->data_json = $data;
		if (!$contractor->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'message' => 'Контрагент успешно сохранен']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function search() {
		$q = $this->request->post('query');
		if (!$q) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$user = \Auth::user();
		$statuses = $this->statusRepo->getList(Status::STATUS_TYPE_CONTRACTOR);

		//\DB::connection()->enableQueryLog();
		$contractors = Contractor::where('is_active', true)
			->where(function($query) use ($q) {
				$query->where("name", "LIKE", "%{$q}%")
					->orWhere("lastname", "LIKE", "%{$q}%")
					->orWhere("email", "LIKE", "%{$q}%")
					->orWhere("phone", "LIKE", "%{$q}%");
			})
			/*->whereRelation('city', 'version', '=', $user->version)*/
			//->where("email", "LIKE", "%{$q}%")
			->orderBy('name')
			->orderBy('lastname');
		/*if ($this->request->user()->city) {
			$contractors = $contractors->whereIn('id', [$this->request->user()->city->id, 0]);
		}*/
		/*if (!$user->isSuperAdmin() && $user->city) {
			$contractors = $contractors->whereIn('city_id', [$user->city->id, 0]);
		}*/
		$contractors = $contractors->get();
		//\Log::debug(\DB::getQueryLog());
		
		$suggestions = [];
		foreach ($contractors as $contractor) {
			$flightTime = $contractor->getFlightTime();
			$status = $contractor->getStatus($statuses, $flightTime);
			
			$suggestions[] = [
				'value' => $contractor->name . ($contractor->lastname ? ' ' . $contractor->lastname : '') . ' [' . $contractor->email . ($contractor->phone ? ', ' . $contractor->phone : '') . ($contractor->city ? ', ' . $contractor->city->name : '') . (($status && $status->discount) ? ', скидка ПГ: ' . $status->discount->valueFormatted() : 'нет') . ']',
				'id' => $contractor->id,
				'data' => [
					'name' => $contractor->name,
					'lastname' => $contractor->lastname ?? '',
					'email' => $contractor->email ?? '',
					'phone' => $contractor->phone ?? '',
					'city_id' => $contractor->city ? $contractor->city->id : 0,
				],
			];
		}
		
		return response()->json(['suggestions' => $suggestions]);
	}

	/**
	 * @param $contractorId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function addScore($contractorId)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$contractor = Contractor::find($contractorId);
		if (!$contractor) return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		
		$productTypes = ProductType::where('is_active', true)
			->whereIn('alias', [ProductType::REGULAR_ALIAS, ProductType::ULTIMATE_ALIAS, ProductType::COURSES_ALIAS])
			->orderBy('name')
			->get();
		
		$scores = Score::where('contractor_id', $contractor->id)
			->latest()
			->get();

		$VIEW = view('admin.contractor.modal.add_score', [
			'productTypes' => $productTypes,
			'scores' => $scores,
			'contractor' => $contractor,
			'user' => $this->request->user(),
		]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function storeScore()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'contractor_id' => 'required|numeric|min:0|not_in:0',
		];

		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'contractor_id' => 'Контрагент',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}

		$contractor = Contractor::find($this->request->contractor_id);
		if (!$contractor) {
			return response()->json(['status' => 'error', 'reason' => 'Контрагент не найден']);
		}

		$scoreValue = $flightTime = 0;
		if ($this->request->operation_type == 'minus') {
			if ($this->request->is_minus_score) {
				$scoreValue = -1 * $this->request->scoreValue;
			}
			if ($this->request->is_minus_flight_time) {
				$flightTime = -1 * $this->request->duration;
			}
		} else {
			if ($this->request->is_minus_score) {
				$scoreValue = $this->request->scoreValue;
			}
			if ($this->request->is_minus_flight_time) {
				$flightTime = $this->request->duration;
			}
		}
		
		if (!$scoreValue && !$flightTime) {
			return response()->json(['status' => 'error', 'reason' => 'Необходимо выбрать баллы или время налета']);
		}

		$score = new Score();
		$score->contractor_id = $contractor->id;
		$score->score = $scoreValue ?? 0;
		$score->duration = $flightTime ?? 0;
		$score->user_id = $this->request->user()->id;
		$score->type = Score::SCORING_TYPE;
		if (!$score->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}

		return response()->json(['status' => 'success', 'message' => 'Время полета / баллы успешно сохранено']);
	}
	
	/**
	 * @param $uuid
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function unsubscribe($uuid)
	{
		$contractor = HelpFunctions::getEntityByUuid(Contractor::class, $uuid);
		if (!$contractor) {
			abort(404);
		}
		
		if ($contractor->is_subscribed) {
			$contractor->is_subscribed = 0;
			$contractor->save();
		}
		
		$page = HelpFunctions::getEntityByAlias(Content::class, 'unsubscribe');
		
		return view('unsubscribe', [
			'contractor' => $contractor,
			'page' => $page,
		]);
	}
}
