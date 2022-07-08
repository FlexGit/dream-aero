<?php

namespace App\Models;

use App\Services\HelpFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PlatformData
 *
 * @property int $id
 * @property int $location_id локация
 * @property int $flight_simulator_id авиатренажер
 * @property string|null $data_at дата, на которую представлены данные
 * @property string|null $total_up данные платформы: общее время в поднятом и подвижном состоянии
 * @property string|null $user_total_up данные пользователя: общее время в поднятом и подвижном состоянии
 * @property string|null $in_air_no_motion данные платформы: общее время в поднятом и неподвижном состоянии
 * @property string|null $comment комментарий
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\FlightSimulator|null $simulator
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData newQuery()
 * @method static \Illuminate\Database\Query\Builder|PlatformData onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereDataAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereFlightSimulatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereInAirNoMotion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereTotalUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformData whereUserTotalUp($value)
 * @method static \Illuminate\Database\Query\Builder|PlatformData withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PlatformData withoutTrashed()
 * @mixin \Eloquent
 */
class PlatformData extends Model
{
	use HasFactory, SoftDeletes;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'location_id',
		'flight_simulator_id',
		'data_at',
		'total_up',
		'user_total_up',
		'in_air_no_motion',
		'comment',
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
	];
	
	public function location()
	{
		return $this->hasOne(Location::class, 'id', 'location_id');
	}
	
	public function simulator()
	{
		return $this->hasOne(FlightSimulator::class, 'id', 'flight_simulator_id');
	}
	
	public function logs()
	{
		return $this->hasMany(PlatformLog::class, 'platform_data_id', 'id');
	}
	
	/**
	 * Подсчет времени Motion without permit
	 *
	 * @param $events
	 * @return array
	 */
	public function mwp($events = [])
	{
		foreach ($events ?? [] as $index => $event) {
			if (!isset($event[$index - 1])) continue;
			
			if ($event['start_at'] == $events[$index - 1]['stop_at']) {
				$events[$index - 1]['stop_at'] = $event['stop_at'];
				unset($events[$index]);
			}
		}
		array_values($events);
		
		$items = [];
		foreach ($this->logs as $log) {
			if ($log->action_type != PlatformLog::IN_UP_ACTION_TYPE) continue;
			if (!Carbon::parse($log->start_at)->diffInMinutes($log->stop_at)) continue;
			
			$serverStartAt = Carbon::parse($this->data_at . ' ' . $log->start_at);
			$serverStartAtWithLag = Carbon::parse($this->data_at . ' ' . $log->start_at)->addMinutes(PlatformLog::MWP_MINUTE_LAG);
			$serverStopAt = Carbon::parse($this->data_at . ' ' . $log->stop_at);
			$serverStopAtWithLag = Carbon::parse($this->data_at . ' ' . $log->stop_at)->subMinutes(PlatformLog::MWP_MINUTE_LAG);
			
			foreach ($events as $event) {
				$eventStopAtWithExtraTime = Carbon::parse($event['stop_at'])->addMinutes($event['extra_time'])->format('Y-m-d H:i:s');
				
				// время подъема сервера попадает в интервал события,
				// и время опускания сервера попадает в интервал события
				if (($serverStartAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStartAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
					&& ($serverStopAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStopAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
				) {
					$items[Carbon::parse($log->start_at)->format('H')][$log->id] = 0;
					break;
				}
				
				// время подъема сервера попадает в интервал события,
				// и время опускания сервера позже времени окончания события
				if (($serverStartAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStartAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
					&& ($serverStopAt->gt($eventStopAtWithExtraTime) || $serverStopAtWithLag->gt($eventStopAtWithExtraTime))
					&& ($serverStopAt->diffInMinutes($eventStopAtWithExtraTime) < 30)
				) {
					$items[Carbon::parse($log->start_at)->format('H')][$log->id] = $serverStopAt->diffInMinutes($eventStopAtWithExtraTime);
					break;
				}
				
				// время опускания сервера попадает в интервал события,
				// и время подъема сервера раньше времени начала события
				if (($serverStopAt->isBetween($event['start_at'], $eventStopAtWithExtraTime) || $serverStopAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime))
					&& ($serverStartAt->lt($event['start_at']) || $serverStartAtWithLag->lt($event['start_at']))
					&& ($serverStartAt->diffInMinutes($event['start_at']) < 30)
				) {
					$items[Carbon::parse($log->start_at)->format('H')][$log->id] = $serverStartAt->diffInMinutes($event['start_at']);
					break;
				}
				
				// время подъема сервера раньше времени начала события,
				// и время опускания сервера позже времени окончания события
				if (($serverStartAt->lt($event['start_at']) || $serverStartAtWithLag->lt($event['start_at']))
					&& ($serverStopAt->gt($eventStopAtWithExtraTime) || $serverStopAtWithLag->gt($eventStopAtWithExtraTime))
				) {
					$items[Carbon::parse($log->start_at)->format('H')][$log->id] = $serverStartAt->diffInMinutes($event['start_at']) + $serverStopAt->diffInMinutes($eventStopAtWithExtraTime);
					break;
				}
			}
			
			// данный элемент сервера уже был ранее сопоставлен событию календаря
			if (isset($items[Carbon::parse($log->start_at)->format('H')][$log->id])) continue;
			
			foreach ($events as $event) {
				// время подъема сервера не попадает в интервал события,
				// и время опускания сервера не попадает в интервал события
				if (!$serverStartAt->isBetween($event['start_at'], $eventStopAtWithExtraTime)
					&& !$serverStartAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime)
					&& !$serverStopAt->isBetween($event['start_at'], $eventStopAtWithExtraTime)
					&& !$serverStopAtWithLag->isBetween($event['start_at'], $eventStopAtWithExtraTime)
				) {
					$items[Carbon::parse($log->start_at)->format('H')][$log->id] = Carbon::parse($log->stop_at)->diffInMinutes($log->start_at);
					break;
				}
			}
		}
		
		return $items;
	}
}
