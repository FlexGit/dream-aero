<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\CityPromocode
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CityPromocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CityPromocode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CityPromocode query()
 * @mixin \Eloquent
 */
class CityPromocode extends Pivot
{
	use HasFactory;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'promocode_id',
		'city_id',
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
	];
}
