<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $name наименование локации
 * @property int $legal_entity_id юр.лицо, на которое оформлена локация
 * @property int $city_id город, в котором находится локация
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FlightSimulator[] $simulator
 * @property-read int|null $simulator_count
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\LegalEntity $legalEntity
 * @method static \Illuminate\Database\Eloquent\Builder|Location whereLegalEntityId($value)
 */
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
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'is_active' => 'boolean',
		'data_json' => 'array',
	];

	public function city() {
		return $this->belongsTo('App\Models\City', 'city_id', 'id');
	}
	
	public function legalEntity() {
		return $this->belongsTo('App\Models\LegalEntity', 'legal_entity_id', 'id');
	}

	public function simulator() {
		return $this->hasMany('App\Models\FlightSimulator', 'location_id', 'id');
	}
}
