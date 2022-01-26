<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $event_type тип события
 * @property int $deal_id сделка
 * @property int $user_id пользователь
 * @property int $city_id город, в котором будет осуществлен полет
 * @property int $location_id локация, на которой будет осуществлен полет
 * @property int $flight_simulator_id авиатренажер, на котором будет осуществлен полет
 * @property \datetime|null $start_at дата и время начала события
 * @property \datetime|null $stop_at дата и время окончания события
 * @property int $extra_time дополнительное время
 * @property int $is_repeated_flight признак повторного полета
 * @property int $is_unexpected_flight признак спонтанного полета
 * @property int $is_test_flight признак повторного полета
 * @property string $notification_type способ оповещения контрагента о полете
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventComment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Deal $deal
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read \App\Models\FlightSimulator $simulator
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Query\Builder|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereExtraTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereFlightSimulatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsRepeatedFlight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsTestFlight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsUnexpectedFlight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStopAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Event withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Event withoutTrashed()
 * @mixin \Eloquent
 * @property int $deal_position_id
 * @property int $employee_id сотрудник
 * @property-read \App\Models\DealPosition|null $dealPosition
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDealPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEmployeeId($value)
 */
class Event extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'event_type' => 'Тип события',
		'deal_id' => 'Сделка',
		'deal_position_id' => 'Позиция сделки',
		'user_id' => 'Пользователь',
		'extra_time' => 'Дополнительное время',
		'city_id' => 'Город',
		'location_id' => 'Локация',
		'flight_simulator_id' => 'Авиатренажер',
		'start_at' => 'Начало события',
		'stop_at' => 'Окончание события',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;

	const EVENT_SOURCE_DEAL = 'deal';
	const EVENT_SOURCE_CALENDAR = 'calendar';

	const EVENT_TYPE_DEAL = 'deal';

	const EVENT_TYPE_DEAL_PAID = 'deal_paid';
	const EVENT_TYPE_DEAL_NOT_PAID = 'deal_notpaid';
	const EVENT_TYPE_NOTE = 'note';
	const EVENT_TYPE_SHIFT_ADMIN = 'shift_admin';
	const EVENT_TYPE_SHIFT_PILOT = 'shift_pilot';
	const EVENT_TYPES = [
		self::EVENT_TYPE_DEAL_PAID => 'Сделка оплачена',
		self::EVENT_TYPE_DEAL_NOT_PAID => 'Сделка не оплачена',
		self::EVENT_TYPE_NOTE => 'Уведомление',
		self::EVENT_TYPE_SHIFT_ADMIN => 'Смена администратора',
		self::EVENT_TYPE_SHIFT_PILOT => 'Смена пилота',
	];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'event_type',
		'deal_id',
		'deal_position_id',
		'user_id',
		'extra_time',
		'city_id',
		'location_id',
		'flight_simulator_id',
		'start_at',
		'stop_at',
		'data_json',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'start_at' => 'datetime:Y-m-d H:i:s',
		'stop_at' => 'datetime:Y-m-d H:i:s',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
	];

	public function city()
	{
		return $this->belongsTo(City::class, 'city_id', 'id');
	}
	
	public function location()
	{
		return $this->belongsTo(Location::class, 'location_id', 'id');
	}

	public function simulator()
	{
		return $this->belongsTo(FlightSimulator::class, 'flight_simulator_id', 'id');
	}
	
	public function deal()
	{
		return $this->belongsTo(Deal::class, 'deal_id', 'id');
	}

	public function dealPosition()
	{
		return $this->belongsTo(DealPosition::class, 'deal_position_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
	
	public function comments()
	{
		return $this->hasMany(EventComment::class, 'event_id', 'id')
			->orderByDesc('id');
	}
}
