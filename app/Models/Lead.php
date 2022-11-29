<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Lead
 *
 * @property int $id
 * @property string|null $type тип лида
 * @property string|null $name имя
 * @property string|null $phone телефон
 * @property string|null $email email
 * @property int $product_id продукт
 * @property int $city_id
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newQuery()
 * @method static \Illuminate\Database\Query\Builder|Lead onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Lead withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Lead withoutTrashed()
 * @mixin \Eloquent
 */
class Lead extends Model
{
	use HasFactory, SoftDeletes;

	const BLACK_FRIDAY_TYPE = 'black-friday';
	const BLACK_FRIDAY_START = '2022-11-24 22:00:00';
	const BLACK_FRIDAY_STOP = '2022-11-25 23:00:00';
	const TYPES = [
		self::BLACK_FRIDAY_TYPE => 'Чёрная пятница',
	];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'email',
		'phone',
		'product_id',
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
		'deleted_at' => 'datetime:Y-m-d H:i:s',
	];
	
	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id');
	}
	
	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
	}
}
