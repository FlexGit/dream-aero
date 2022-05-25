<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformLog extends Model
{
	use HasFactory, SoftDeletes;

	const IN_AIR_ACTION_TYPE = 'in_air';
	const IN_UP_ACTION_TYPE = 'in_up';
	const IANM_ACTION_TYPE = 'ianm';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'platform_data_id',
		'action_type',
		'start_at',
		'stop_at',
		'duration',
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
	];
	
	public function platformData()
	{
		return $this->hasOne(PlatformData::class, 'id', 'platform_data_id');
	}
}
