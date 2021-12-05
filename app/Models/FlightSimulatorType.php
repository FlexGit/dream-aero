<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

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
 * @property bool $is_active признак активности
 * @method static \Illuminate\Database\Eloquent\Builder|FlightSimulatorType whereIsActive($value)
 */
class FlightSimulatorType extends Model {
    use HasFactory, RevisionableTrait;
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'is_active' => 'boolean',
	];

	public function simulator() {
		return $this->belongsTo('App\Models\FlightSimulator', 'id', 'flight_simulator_type_id');
	}
}
