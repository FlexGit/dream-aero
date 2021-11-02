<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FlightSimulatorType
 *
 * @property int $id
 * @property string $name наименование типа авиатренажера
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FlightSimulator $simulator
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FlightSimulatorType extends Model {
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
	];

	public function simulator() {
		return $this->belongsTo('App\Models\FlightSimulator', 'id', 'flight_simulator_type_id');
	}
}
