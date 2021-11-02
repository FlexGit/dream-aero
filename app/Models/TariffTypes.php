<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TariffTypes
 *
 * @property int $id
 * @property string $name наименование тарифа
 * @property array $data_json дополнительная информация
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TariffTypes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TariffTypes extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
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
}
