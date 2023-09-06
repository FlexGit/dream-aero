<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Content
 *
 * @property int $id
 * @property string $title заголовок
 * @property string $alias алиас
 * @property string|null $preview_text аннотация
 * @property string|null $detail_text контент
 * @property int $parent_id родитель
 * @property string $version версия
 * @property string|null $meta_title meta Title
 * @property string|null $meta_description meta Description
 * @property bool $is_active признак активности
 * @property array|null $data_json дополнительная информация
 * @property \datetime|null $published_at дата публикации
 * @property \datetime|null $created_at
 * @property \datetime|null $updated_at
 * @property \datetime|null $deleted_at
 * @property-read Content|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|Content newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Content newQuery()
 * @method static \Illuminate\Database\Query\Builder|Content onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Content query()
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereDataJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereDetailText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePreviewText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|Content withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Content withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\City|null $city
 * @property int $city_id город
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereCityId($value)
 * @property float $rating_value
 * @property int $rating_count
 * @property string|null $rating_ips
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereRatingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereRatingIps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereRatingValue($value)
 * @property string|null $meta_title_en
 * @property string|null $meta_description_en
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaTitleEn($value)
 * @property \datetime|null $published_end_at
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePublishedEndAt($value)
 */
class Content extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;

	const NEWS_TYPE = 'news';
	const REVIEWS_TYPE = 'reviews';
	const GALLERY_TYPE = 'gallery';
	const GUESTS_TYPE = 'guests';
	const PAGES_TYPE = 'pages';
	const PROMOBOX_TYPE = 'promoboxes';
	const WIKI_TYPE = 'wiki';

	const VERSION_RU = 'ru';
	const VERSION_AERO = 'aero';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'title',
		'alias',
		'preview_text',
		'detail_text',
		'parent_id',
		'city_id',
		'rating_value',
		'rating_count',
		'rating_ips',
		'version',
		'meta_title',
		'meta_description',
		'meta_title_en',
		'meta_description_en',
		'is_active',
		'data_json',
		'published_at',
		'published_end_at',
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
		'published_at' => 'datetime:Y-m-d',
		'published_end_at' => 'datetime:Y-m-d',
		'data_json' => 'array',
		'rating_ips' => 'array',
		'is_active' => 'boolean',
	];

	public function parent()
	{
		return $this->hasOne(Content::class, 'id', 'parent_id');
	}

	public function city()
	{
		return $this->hasOne(City::class, 'id', 'city_id');
	}
}
