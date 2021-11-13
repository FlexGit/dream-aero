<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Promo
 *
 * @property-read \App\Models\City $city
 * @method static \Illuminate\Database\Eloquent\Builder|Promo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name наименование
 * @property string $preview_text анонс
 * @property string $detail_text описание
 * @property int $city_id город, к которому относится акция
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereDetailText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo wherePreviewText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereUpdatedAt($value)
 */
class Promo extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'preview_text',
		'detail_text',
		'city_id',
		'is_active',
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
	];

	public function city() {
		return $this->belongsTo('App\Models\City', 'city_id', 'id');
	}
}
