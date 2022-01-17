<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\LocationFlightSimulator
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LocationFlightSimulator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocationFlightSimulator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocationFlightSimulator query()
 * @mixin \Eloquent
 */
class LocationFlightSimulator extends Pivot
{
	use HasFactory;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'location_id',
		'flight_simulator_id',
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
		'data_json' => 'array',
	];
}
