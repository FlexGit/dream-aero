<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*use \Venturecraft\Revisionable\RevisionableTrait;*/

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
 */
class Content extends Model
{
	use HasFactory, SoftDeletes/*, RevisionableTrait*/;

	const NEWS_TYPE = 'news';
	const REVIEWS_TYPE = 'reviews';
	const GALLERY_TYPE = 'gallery';

	const VERSION_RU = 'ru';
	const VERSION_AERO = 'aero';
	
	/*const ATTRIBUTES = [
		'name' => 'Наименование',
		'comment' => 'Комментарий',
		'city_id' => 'Город',
		'active_to_at' => 'Окончание активности',
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
		'title',
		'alias',
		'preview_text',
		'detail_text',
		'parent_id',
		'city_id',
		'version',
		'meta_title',
		'meta_description',
		'is_active',
		'data_json',
		'published_at',
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
		'published_at' => 'datetime:Y-m-d H:i:s',
		'data_json' => 'array',
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
