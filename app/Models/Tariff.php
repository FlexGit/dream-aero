<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tariff
 *
 * @property int $id
 * @property string $name наименование тарифа
 * @property int $tariff_type_id тип тарифа
 * @property int $city_id город, в котором действует продукт
 * @property int $duration длительность полёта, мин.
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property int $price базовая цена продукта
 * @property int $is_hit является ли продукт хитом продаж
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereIsHit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereTariffTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $employee_id пилот
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Employee|null $employee
 * @property-read \App\Models\TariffType|null $tarifType
 * @method static \Illuminate\Database\Eloquent\Builder|Tariff whereEmployeeId($value)
 */
class Tariff extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'tariff_id',
		'city_id',
		'duration',
		'data_json',
		'is_active',
		'price',
		'is_hit',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
		'is_active' => 'boolean',
		'is_hit' => 'boolean',
	];
	
	public function tarifType() {
		return $this->hasOne('App\Models\TariffType', 'id', 'tariff_type_id');
	}
	
	public function employee() {
		return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
	}

	public function city() {
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
}
