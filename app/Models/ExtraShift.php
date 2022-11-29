<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ExtraShift
 *
 * @property int $id
 * @property int $user_id пользователь
 * @property int $location_id локация
 * @property int $flight_simulator_id авиатренажер
 * @property mixed|null $period период
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\FlightSimulator|null $simulator
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift newQuery()
 * @method static \Illuminate\Database\Query\Builder|ExtraShift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift whereFlightSimulatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraShift whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ExtraShift withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ExtraShift withoutTrashed()
 * @mixin \Eloquent
 */
class ExtraShift extends Model
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
		'user_id',
		'period',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'period' => 'date:Y-m-d',
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
