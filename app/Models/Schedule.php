<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
		self::DAY_OFF_PILOT_TYPE => '#ffffff',
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
