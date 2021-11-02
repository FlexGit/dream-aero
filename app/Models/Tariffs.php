<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tariffs
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
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereIsHit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereTariffTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tariffs whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tariffs extends Model
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
		'data_json' => 'array',
	];
}
