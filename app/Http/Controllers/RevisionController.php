<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevisionController extends Controller
{
	private $request;

	const ENTITIES = [
		'City' => 'Город',
		'Contractor' => 'Контрагент',
		'Deal' => 'Группа сделок',
		'Employee' => 'Сотрудник',
		'EmployeePosition' => 'Позиция сотрудника',
		'FlightSimulator' => 'Авиатренажер',
		'LegalEntity' => 'Юр. лицо',
		'Location' => 'Локация',
		'Order' => 'Заявка',
		'DealPosition' => 'Сделка',
		'Promo' => 'Акция',
		'Promocode' => 'Промокод',
		'Score' => 'Баллы',
		'Status' => 'Статус',
		'Product' => 'Продукт',
		'ProductType' => 'Тип продукта',
		'User' => 'Пользователь',
		'Bill' => 'Счет',
		'Payment' => 'Платеж',
		'Certificate' => 'Сертификат',
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
		if (!$this->request->ajax()) {
			abort(404);
		}
		if (!$this->request->user()->isSuperAdmin()) {
			return response()->json(['status' => 'error', 'reason' => 'Недостаточно прав доступа']);
		}
		
		$id = $this->request->id ?? 0;
		
		$table = $this->request->filter_entity_alias ? app('App\Models\\' . $this->request->filter_entity_alias)->getTable() : '';
		switch ($table) {
			case 'orders':
			case 'deals':
			case 'dealPositions':
			case 'certificates':
			case 'bills':
			case 'payments':
			case 'promocodes':
				$field = 'number';
			break;
			case 'discounts':
				$field = 'value';
			break;
			case '':
				$field = 'title';
			break;
			default:
				$field = 'name';
		}
		
		$revisions = DB::table('revisions')
			->leftJoin('users', 'revisions.user_id', '=', 'users.id')
			->select('revisions.*', 'users.name as user')
			->orderBy('revisions.id', 'desc');
		if ($id) {
			$revisions = $revisions->where('revisions.id', '<', $id);
		}
		if ($table) {
			$revisions = $revisions->leftJoin($table, 'revisions.revisionable_id', '=', $table . '.id');
			$revisions = $revisions->where('revisions.revisionable_type', 'App\\Models\\' . $this->request->filter_entity_alias);
			$revisions = $revisions->where($table . '.' . $field, 'like', '%' . $this->request->search_object . '%');
		}
		/*if ($this->request->search_object) {
			$revisions = $revisions->where('revisions.revisionable_id', $this->request->search_object);
		}*/
		$revisions = $revisions->limit(20)->get();
		
		$revisionData = [];
		foreach ($revisions as $revision) {
			$model = $revision->revisionable_type::find($revision->revisionable_id);
			$object = '';
			if ($model->number) {
				$object = $model->number;
			} elseif ($model->value) {
				$object = $model->value;
			} elseif ($model->title) {
				$object = $model->title;
			} elseif ($model->name) {
				$object = $model->name;
			}

			$oldValue = $newValue = '';
			if (substr($revision->key, strlen($revision->key) - 3) == '_id') {
				$tableName = substr($revision->key, 0, strlen($revision->key) - 3);
				$entity = 'App\Models\\' . \Str::studly(\Str::singular($tableName));
				if ($revision->old_value) {
					$model = $entity::find($revision->old_value);
					$oldValue = $model->number ?: $model->name;
				}
				if ($revision->new_value) {
					$model = $entity::find($revision->new_value);
					if ($model->number) {
						$newValue = $model->number;
					} elseif ($model->value) {
						$newValue = $model->value;
					} elseif ($model->title) {
						$newValue = $model->title;
					} else {
						$newValue = $model->name;
					}
				}
			}
			
			$revisionableType = mb_substr($revision->revisionable_type, 11);

			$revisionData[] = [
				'id' => $revision->id,
				'entity' => $revisionableType,
				'revisionable_type' => array_key_exists($revisionableType, self::ENTITIES) ? self::ENTITIES[$revisionableType] : $revisionableType,
				'revisionable_id' => $revision->revisionable_id,
				'object' => $object,
				'user' => $revision->user,
				'key' => $revision->key,
				'old_value' => $oldValue ?: $revision->old_value,
				'new_value' => $newValue ?: $revision->new_value,
				'created_at' => $revision->created_at,
				'updated_at' => $revision->updated_at,
			];
		}
		
		$VIEW = view('admin.revision.list', [
			'revisionData' => $revisionData,
			'entities' => self::ENTITIES,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
}
