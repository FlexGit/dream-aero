<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Notification
 *
 * @property int $id
 * @property string $title заголовок
 * @property string $description описание
 * @property int $city_id город
 * @property int $contractor_id контрагент
 * @property bool $is_new новое уведомление
 * @property array|null $data_json дополнительная информация
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Deal|null $deal
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\Status|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Query\Builder|Notification onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Notification withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Notification withoutTrashed()
 * @mixin \Eloquent
 */
class Notification extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'title' => 'Заголовок',
		'description' => 'Описание',
		'city_id' => 'Город',
		'contractor_id' => 'Контрагент',
		'is_new' => 'Новое уведомление',
		'is_active' => 'Активность',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'title',
		'description',
		'city_id',
		'contractor_id',
		'is_new',
		'is_active',
		'data_json',
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
		'is_active' => 'boolean',
		'is_new' => 'boolean',
	];
	
	public function status()
	{
		return $this->hasOne('App\Models\Status', 'id', 'status_id');
	}
	
	public function deal()
	{
		return $this->hasOne('App\Models\Deal', 'id', 'deal_id');
	}
	
	/**
	 * @return array
	 */
	public function format()
	{
		$data = $this->data_json ?? [];
		
		return [
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'is_new' => (bool)$this->is_new,
			'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
			'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
		];
	}
}
