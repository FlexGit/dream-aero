<?php

namespace App\Models;

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
}
