<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Support\Facades\DB;

class RevisionController extends Controller
{
	private $request;

	const ENTITIES = [
		'City' => 'Города',
		'Contractor' => 'Контрагенты',
		'Deal' => 'Сделки',
		'Employee' => 'Сотрудники',
		'EmployeePosition' => 'Позиции сотрудников',
		'FlightSimulator' => 'Авиатренажеры',
		'FlightSimulatorType' => 'Типы авиатренажеров',
		'LegalEntity' => 'Юр. лица',
		'Location' => 'Локации',
		'Order' => 'Заявки',
		'Position' => 'Позиции заявки',
		'Product' => 'Продукты',
		'Promo' => 'Акции',
		'Promocode' => 'Промокоды',
		'Score' => 'Баллы',
		'Status' => 'Статусы',
		'Tariff' => 'Тарифы',
		'TariffType' => 'Типы тарифов',
		'User' => 'Пользователи',
	];
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @param null $entity
	 * @param null $objectId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index($entity = null, $objectId = null)
	{
		$entities = self::ENTITIES;
		asort($entities);
		
		return view('admin.revision.index', [
			'entities' => $entities,
			'entity' => $entity,
			'objectId' => $objectId,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		//DB::connection()->enableQueryLog();
		
		$revisions = DB::table('revisions')
			->leftJoin('users', 'revisions.user_id', '=', 'users.id')
			->select('revisions.*', 'users.name as user')
			->orderBy('revisions.id', 'asc');
		if ($this->request->filter_entity_alias) {
			$revisions = $revisions->where('revisions.revisionable_type', 'App\\Models\\' . $this->request->filter_entity_alias);
		}
		if ($this->request->search_object) {
			$revisions = $revisions->where('revisions.revisionable_id', $this->request->search_object);
		}
		$revisions = $revisions->get();
		//$queries = DB::getQueryLog();
		//Log::debug($queries);
		
		$VIEW = view('admin.revision.list', [
			'revisions' => $revisions,
			'entities' => self::ENTITIES,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
}
