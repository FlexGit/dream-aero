<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
//use Illuminate\Database\Eloquent\SoftDeletes;
//use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\CityProduct
 *
 * @property-read \App\Models\Discount $discount
 * @method static \Illuminate\Database\Eloquent\Builder|CityProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CityProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CityProduct query()
 * @mixin \Eloquent
 * @property \datetime $created_at
 * @property \datetime $updated_at
 */
class CityProduct extends Pivot
{
	use HasFactory/*, SoftDeletes*//*, RevisionableTrait*/;
	
	/*const ATTRIBUTES = [
		'product_id' => 'Продукт',
		'city_id' => 'Город',
		'price' => 'Стоимость',
		'discount_id' => 'Скидка',
		'is_hit' => 'Хит',
		'is_active' => 'Активность',
		'data_json' => 'Дополнительная информация',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];
	
	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;*/
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'product_id',
		'city_id',
		'price',
		'discount_id',
		'is_hit',
		'score',
		'is_active',
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
		/*'deleted_at' => 'datetime:Y-m-d H:i:s',*/
		'is_hit' => 'boolean',
		'is_active' => 'boolean',
		'data_json' => 'array',
	];

	public function discount()
	{
		return $this->hasOne(Discount::class, 'id', 'discount_id');
	}
}
