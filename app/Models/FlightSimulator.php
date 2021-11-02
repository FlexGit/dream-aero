<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FlightSimulator
 *
 * @property int $id
 * @property string $name наименование авиатренажера
 * @property int $flight_simulator_type_id тип авиатренажера
 * @property int $location_id локация, в которой находится авиатренажер
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location $location
 * @property-read \App\Models\FlightSimulatorType|null $simulatorType
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator query()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereFlightSimulatorTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulator whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FlightSimulator extends Model {
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'flight_simulator_type_id',
		'location_id',
		'is_active',
	];

	public function simulatorType() {
		return $this->hasOne('App\Models\FlightSimulatorType', 'id', 'flight_simulator_type_id');
	}

	public function location() {
		return $this->belongsTo('App\Models\Location', 'id', 'location_id');
	}
}
