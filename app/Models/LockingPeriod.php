<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\LockingPeriod
 *
 * @property int $id
 * @property int $user_id пользователь
 * @property \Illuminate\Support\Carbon|null $start_at дата начала периода
 * @property \Illuminate\Support\Carbon|null $stop_at дата окончания периода
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod newQuery()
 * @method static \Illuminate\Database\Query\Builder|LockingPeriod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod query()
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereStopAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|LockingPeriod withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LockingPeriod withoutTrashed()
 * @mixin \Eloquent
 * @property int $location_id
 * @property-read \App\Models\Location|null $location
 * @method static \Illuminate\Database\Eloquent\Builder|LockingPeriod whereLocationId($value)
 */
class LockingPeriod extends Model
{
	use HasFactory, SoftDeletes;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'location_id',
		'user_id',
		'start_at',
		'stop_at',
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
		'start_at' => 'datetime:Y-m-d H:i:s',
		'stop_at' => 'datetime:Y-m-d H:i:s',
	];
	
	public function location()
	{
		return $this->hasOne(Location::class, 'id', 'location_id');
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
