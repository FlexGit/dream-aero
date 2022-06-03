<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PlatformLog
 *
 * @property int $id
 * @property int $platform_data_id
 * @property string|null $action_type тип действия
 * @property string|null $start_at время начала действия
 * @property string|null $stop_at время окончания действия
 * @property string|null $duration длительность
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\PlatformData|null $platformData
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|PlatformLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog wherePlatformDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereStopAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlatformLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PlatformLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PlatformLog withoutTrashed()
 * @mixin \Eloquent
 */
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
