<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\Promo
 *
 * @property int $id
 * @property string $name наименование
 * @property int $discount_id скидка
 * @property string|null $preview_text анонс
 * @property string|null $detail_text описание
 * @property int $city_id город, к которому относится акция
 * @property bool $is_published для публикации
 * @property bool $is_active признак активности
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Discount|null $discount
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|Promo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo newQuery()
 * @method static \Illuminate\Database\Query\Builder|Promo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereDetailText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo wherePreviewText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Promo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Promo withoutTrashed()
 * @mixin \Eloquent
 */
class Promo extends Model
{
	use HasFactory, SoftDeletes, RevisionableTrait;
	
	const ATTRIBUTES = [
		'name' => 'Наименование',
		'discount_id' => 'Скидка',
		'preview_text' => 'Превью текст',
		'detail_text' => 'Детальное описание',
		'city_id' => 'Город',
		'is_active' => 'Активность',
		'is_published' => 'Для публикации',
		'created_at' => 'Создано',
		'updated_at' => 'Изменено',
		'deleted_at' => 'Удалено',
	];

	protected $revisionForceDeleteEnabled = true;
	protected $revisionCreationsEnabled = true;
	
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
		'is_published',
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
		'is_active' => 'boolean',
		'is_published' => 'boolean',
	];

	public function city()
	{
		return $this->hasOne('App\Models\City', 'id', 'city_id');
	}
	
	public function discount()
	{
		return $this->hasOne('App\Models\Discount', 'id', 'discount_id');
	}
	
	/**
	 * @return array
	 */
	public function format()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'preview_text' => $this->preview_text,
			'detail_text' => $this->detail_text,
		];
	}
}
