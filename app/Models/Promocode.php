<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Promocode
 *
 * @property int $id
 * @property string $number промокод
 * @property int $city_id город, в котором действует промокод
 * @property bool $is_active признак активности
 * @property \datetime|null $active_from_at дата начала активности
 * @property \datetime|null $active_to_at дата окончания активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property-read \App\Models\City $city
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereActiveFromAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereActiveToAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promocode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Promocode extends Model
{
    use HasFactory, RevisionableTrait;
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'number',
		'city_id',
		'is_active',
		'active_from_at',
		'active_to_at',
		'data_json',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'active_from_at' => 'datetime:Y-m-d H:i:s',
		'active_to_at' => 'datetime:Y-m-d H:i:s',
		'is_active' => 'boolean',
		'data_json' => 'array',
	];

	public function city() {
		return $this->belongsTo('App\Models\City', 'city_id', 'id');
	}
}
