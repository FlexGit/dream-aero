<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property mixed|null $scheduled_at дата записи
 * @property string|null $schedule_type тип записи
 * @property int $user_id пользователь
 * @property int $location_id локация
 * @property int $flight_simulator_id авиатренажер
 * @property mixed|null $start_at время начала события
 * @property mixed|null $stop_at время окончания события
 * @property string|null $comment комментарий
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\FlightSimulator|null $simulator
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Query\Builder|Schedule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereFlightSimulatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereScheduleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStopAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Schedule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Schedule withoutTrashed()
 * @mixin \Eloquent
 */
class Schedule extends Model
{
	use HasFactory, SoftDeletes;

	const BASIC_PILOT_TYPE = 'basic_pilot';
	const DUTY_PILOT_TYPE = 'duty_pilot';
	const DAY_OFF_PILOT_TYPE = 'day_off_pilot';
	const SHIFT_ADMIN_TYPE = 'shift_admin';
	const VACATION_TYPE = 'vacation';
	const LOCKING_TYPE = 'locking';
	const QUARANTINE_TYPE = 'quarantine';
	const RESET_TYPE = 'reset';
	
	const TYPES = [
		self::BASIC_PILOT_TYPE => 'Основной пилот',
		self::DUTY_PILOT_TYPE => 'Дежурный пилот',
		self::DAY_OFF_PILOT_TYPE => 'Выходной пилот',
		self::SHIFT_ADMIN_TYPE => 'Смена администратора',
		self::VACATION_TYPE => 'Отпуск',
		self::LOCKING_TYPE => 'Не менять',
		self::QUARANTINE_TYPE => 'Карантин',
		self::RESET_TYPE => '',
	];

	const COLOR_TYPES = [
		self::BASIC_PILOT_TYPE => '#00cc00',
		self::DUTY_PILOT_TYPE => '#cccccc',
		self::DAY_OFF_PILOT_TYPE => '#f8a36a',
		self::SHIFT_ADMIN_TYPE => '#ffff00',
		self::VACATION_TYPE => '#9999ff',
		self::LOCKING_TYPE => '#ea9999',
		self::QUARANTINE_TYPE => '#ff0000',
		self::RESET_TYPE => '#000000',
	];
	
	const LETTER_TYPES = [
		self::BASIC_PILOT_TYPE => 'О',
		self::DUTY_PILOT_TYPE => 'Д',
		self::DAY_OFF_PILOT_TYPE => 'В',
		self::SHIFT_ADMIN_TYPE => 'О',
		self::VACATION_TYPE => '',
		self::LOCKING_TYPE => '',
		self::QUARANTINE_TYPE => '',
		self::RESET_TYPE => '',
	];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'scheduled_at',
		'schedule_type',
		'location_id',
		'flight_simulator_id',
		'user_id',
		'start_at',
		'stop_at',
		'comment',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'scheduled_at' => 'date:Y-m-d',
		'start_at' => 'date:H:i',
		'stop_at' => 'date:H:i',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'deleted_at' => 'datetime:Y-m-d H:i:s',
	];
	
	public function location()
	{
		return $this->hasOne(Location::class, 'id', 'location_id');
	}
	
	public function simulator()
	{
		return $this->hasOne(FlightSimulator::class, 'id', 'flight_simulator_id');
	}
	
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
