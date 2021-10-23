<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'city_id',
		'data_json',
		'is_active',
		'legal_entity_id'
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'data_json' => 'array',
	];

	public function city() {
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}

	public function simulator() {
		return $this->hasMany('App\Models\FlightSimulator', 'location_id', 'id');
	}
}
