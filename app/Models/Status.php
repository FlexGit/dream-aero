<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Status
 *
 * @property int $id
 * @property string $name наименование
 * @property string $alias алиас
 * @property string $type тип сущности: контрагент, заказ, сделка, счет, платеж, сертификат
 * @property int $flight_time время налета
 * @property int $sort сортировка
 * @property bool $is_active признак активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Query\Builder|Status onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereFlightTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Status withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Status withoutTrashed()
 * @mixin \Eloquent
 */
class Status extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
    const STATUS_TYPE_CONTRACTOR = 'contractor';
	/*const STATUS_TYPE_ORDER = 'order';*/
	const STATUS_TYPE_DEAL = 'deal';
	const STATUS_TYPE_CERTIFICATE = 'certificate';
	const STATUS_TYPE_BILL = 'bill';
	/*const STATUS_TYPE_PAYMENT = 'payment';*/
	const STATUS_TYPES = [
		Status::STATUS_TYPE_CONTRACTOR => 'Контрагент',
		/*Status::STATUS_TYPE_ORDER => 'Заказ',*/
		Status::STATUS_TYPE_DEAL => 'Сделка',
		Status::STATUS_TYPE_CERTIFICATE => 'Сертификат',
		Status::STATUS_TYPE_BILL => 'Счет',
		/*Status::STATUS_TYPE_PAYMENT => 'Платеж',*/
	];
	
	const ATTRIBUTES = [
		'name' => 'Наименование',
		'alias' => 'Алиас',
		'type' => 'Сущность',
		'sort' => 'Сортировка',
		'is_active' => 'Признак активности',
		'data_json' => 'Дополнительная информация',
		'flight_time' => 'Налет',
		'discount_id' => 'Скидка',
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
		'name',
		'alias',
		'type',
		'flight_time',
		'discount_id',
		'sort',
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
	];
}
