<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
