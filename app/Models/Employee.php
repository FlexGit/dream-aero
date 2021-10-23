<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'employee_position_id',
		'location_id',
		'data_json',
		'is_active',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'data_json' => 'array',
	];

	public function position() {
		return $this->hasOne('App\Models\EmployeePosition', 'id', 'employee_position_id');
	}

	public function location() {
		return $this->hasOne('App\Models\Location', 'id', 'location_id');
	}
}
