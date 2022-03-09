<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevisionController extends Controller
{
	private $request;

	const ENTITIES = [
		'Contractor' => 'Контрагент',
		'Deal' => 'Сделка',
		'Score' => 'Баллы',
		'Bill' => 'Счет',
		'Certificate' => 'Сертификат',
		'Event' => 'Событие',
		/*'City' => 'Город',
		'Role' => 'Роль',
		'FlightSimulator' => 'Авиатренажер',
		'LegalEntity' => 'Юр.лицо',
		'Location' => 'Локация',
		'Promo' => 'Акция',
		'Promocode' => 'Промокод',
		'Status' => 'Статус',
		'Product' => 'Продукт',
		'ProductType' => 'Тип продукта',
		'User' => 'Пользователь',
		'Discount' => 'Скидка',*/
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
			case 'deals':
			case 'certificates':
			case 'bills':
			/*case 'promocodes':*/
				$field = 'number';
			break;
			/*case 'discounts':
				$field = 'value';
			break;*/
			case 'scores':
				$field = 'score';
			break;
			case 'events':
				$field = 'start_at';
			break;
			case '':
				$field = 'title';
			break;
			default:
				$field = 'name';
		}

		//DB::connection()->enableQueryLog();
		$revisions = DB::table('revisions')
			->leftJoin('users as u', 'revisions.user_id', '=', 'u.id')
			->select('revisions.*', 'u.name as user')
			->orderBy('revisions.id', 'desc');
		if ($id) {
			$revisions = $revisions->where('revisions.id', '<', $id);
		}
		if ($table) {
			$revisions = $revisions->leftJoin($table, 'revisions.revisionable_id', '=', $table . '.id');
			$revisions = $revisions->where('revisions.revisionable_type', 'App\\Models\\' . $this->request->filter_entity_alias);
			$revisions = $revisions->where($table . '.' . $field, 'like', '%' . $this->request->search_object . '%');
		}
		$revisions = $revisions->limit(20)->get();
		//$queries = DB::getQueryLog();
		//\Log::debug($queries);

		$revisionData = [];
		foreach ($revisions as $revision) {
			$model = $revision->revisionable_type::find($revision->revisionable_id);
			//if (!$model) continue;
			
			$object = '';

			if ($model) {
				if ($model->number) {
					$object = $model->number;
				} else if ($model->value) {
					$object = $model->value;
				} else if ($model->score) {
					$object = $model->score;
					$linkedObject = $model->contractor ? $model->contractor->fio() : '';
				} else if ($model->title) {
					$object = $model->title;
				} else if ($model->name) {
					$object = $model->name;
				} else if ($model->start_at) {
					$object = Carbon::parse($model->start_at)->format('Y-m-d H:i');
				}
			}

			$oldValue = $newValue = '';
			if (substr($revision->key, strlen($revision->key) - 3) == '_id') {
				$tableName = substr($revision->key, 0, strlen($revision->key) - 3);
				$entity = 'App\Models\\' . \Str::studly(\Str::singular($tableName));
				if ($revision->old_value) {
					$model = $entity::withTrashed()->find($revision->old_value);
					$oldValue = $model->number ?: $model->name;
				}
				if ($revision->new_value) {
					$model = $entity::withTrashed()->find($revision->new_value);
					if ($model->number) {
						$newValue = $model->number;
					} elseif ($model->value) {
						$newValue = $model->value;
					} elseif ($model->score) {
						$newValue = $model->score;
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
				'linkedObject' => $linkedObject ?? '',
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
