<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
